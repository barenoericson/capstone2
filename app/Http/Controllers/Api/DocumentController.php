<?php

namespace App\Http\Controllers\Api;

use App\Events\DocumentSigned;
use App\Events\MessageSent;
use App\Events\NotificationSent;
use App\Models\Document;
use App\Models\DocumentSigning;
use App\Models\Message;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use setasign\Fpdi\Fpdi;

class DocumentController extends BaseController
{
    // ============================================================
    // AGENT: Upload a document and assign to a buyer
    // POST /api/agent/documents
    // ============================================================
    public function uploadDocument(Request $request)
    {
        $request->validate([
            'buyer_id'      => 'required|exists:users,id',
            'document_name' => 'required|string|max:255',
            'document_type' => 'required|in:contract,agreement,disclosure,other',
            'property_id'   => 'nullable|exists:properties,id',
            // Either an uploaded PDF file OR an external URL link (not both required)
            'file'          => 'nullable|file|mimes:pdf|max:10240',
            'document_link' => 'nullable|url|max:1000',
        ]);

        if (!$request->hasFile('file') && !$request->filled('document_link')) {
            return response()->json(['message' => 'Please upload a PDF file or provide a document link.'], 422);
        }

        $user  = $request->user();
        $agent = $user->agent;

        if (!$agent) {
            return response()->json(['message' => 'Agent profile not found.'], 403);
        }

        $path     = null;
        $url      = null;
        $fileSize = null;

        if ($request->hasFile('file')) {
            $file     = $request->file('file');
            $path     = $file->store('documents', 'public');
            $url      = Storage::url($path);
            $fileSize = $file->getSize();
        } else {
            // External link document
            $url = $request->document_link;
        }

        $document = Document::create([
            'agent_id'      => $agent->id,
            'buyer_id'      => $request->buyer_id,
            'property_id'   => $request->property_id,
            'document_name' => $request->document_name,
            'document_type' => $request->document_type,
            'document_path' => $path,
            'document_url'  => $url,
            'file_size'     => $fileSize,
            'status'        => Document::STATUS_PENDING,
        ]);

        $document->load(['buyer', 'agent.user']);

        return response()->json([
            'success'  => true,
            'message'  => 'Document sent to buyer successfully.',
            'document' => $this->formatDocument($document),
        ], 201);
    }

    // ============================================================
    // AGENT: Get documents I've sent
    // GET /api/agent/documents
    // ============================================================
    public function getAgentDocuments(Request $request)
    {
        $user  = $request->user();
        $agent = $user->agent;

        if (!$agent) {
            return response()->json(['message' => 'Agent profile not found.'], 403);
        }

        $documents = Document::where('agent_id', $agent->id)
            ->with(['buyer', 'signings.signer', 'property'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(fn ($d) => $this->formatDocument($d));

        return response()->json(['success' => true, 'documents' => $documents]);
    }

    // ============================================================
    // BUYER: Get documents sent to me
    // GET /api/buyer/documents
    // ============================================================
    public function getBuyerDocuments(Request $request)
    {
        $buyer = $request->user();

        $documents = Document::where('buyer_id', $buyer->id)
            ->with(['agent.user', 'signings.signer', 'property'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(fn ($d) => $this->formatDocument($d));

        return response()->json(['success' => true, 'documents' => $documents]);
    }

    // ============================================================
    // BUYER: Sign a document (embeds signature into PDF)
    // POST /api/buyer/documents/{id}/sign
    // ============================================================
    public function signDocument(Request $request, $id)
    {
        $request->validate([
            'signature_data'  => 'required|string',
            'signature_type'  => 'required|in:drawn,photo',
            'position_x'      => 'required|numeric',
            'position_y'      => 'required|numeric',
            'position_page'   => 'required|integer|min:1',
            'page_width'      => 'required|numeric|min:1',
            'page_height'     => 'required|numeric|min:1',
        ]);

        $buyer    = $request->user();
        $document = Document::where('buyer_id', $buyer->id)->findOrFail($id);

        // Check if buyer already signed
        $existing = DocumentSigning::where('document_id', $document->id)
            ->where('signer_id', $buyer->id)
            ->first();
        if ($existing) {
            return response()->json(['message' => 'You have already signed this document.'], 422);
        }

        // Decode and save the signature image
        $signatureData = $request->signature_data;
        if (str_contains($signatureData, ',')) {
            $signatureData = explode(',', $signatureData, 2)[1];
        }

        $imageData = base64_decode($signatureData);
        $sigPath   = 'signatures/' . uniqid('sig_', true) . '.png';
        Storage::disk('public')->put($sigPath, $imageData);
        $sigUrl = Storage::url($sigPath);

        // Embed signature into PDF if document is a PDF
        $isPdf = $document->document_path && str_ends_with(strtolower($document->document_path), '.pdf');
        if ($isPdf) {
            try {
                $newPdfPath = $this->embedSignatureIntoPdf(
                    $document->document_path,
                    $sigPath,
                    $request->position_x,
                    $request->position_y,
                    $request->position_page,
                    $request->page_width,
                    $request->page_height
                );
                // Update document to point to the signed PDF
                $document->update([
                    'document_path' => $newPdfPath,
                    'document_url'  => Storage::url($newPdfPath),
                ]);
            } catch (\Exception $e) {
                Log::error('PDF signature embedding failed: ' . $e->getMessage());
                // Continue without embedding — signature still saved separately
            }
        }

        // Create signing record
        DocumentSigning::create([
            'document_id'    => $document->id,
            'signer_id'      => $buyer->id,
            'signature_path' => $sigPath,
            'signature_url'  => $sigUrl,
            'signature_type' => $request->signature_type,
            'signed_at'      => now(),
        ]);

        // Mark document as signed
        $document->update([
            'status'    => Document::STATUS_SIGNED,
            'signed_at' => now(),
        ]);

        $document->load(['buyer', 'agent.user', 'signings.signer']);

        // Auto-send signed document to the agent via messaging
        $recipientId = $document->agent?->user_id;
        if ($recipientId) {
            $this->autoSendSignedDocument($document, $buyer->id, $recipientId, $buyer->name);
        }

        // Notify the agent
        try {
            broadcast(new DocumentSigned($document));
        } catch (\Exception $e) { /* Reverb not running */ }

        try {
            $notification = Notification::create([
                'user_id' => $document->agent->user_id,
                'notification_type' => Notification::TYPE_DOCUMENT,
                'title' => 'Document Signed',
                'message' => $buyer->name . ' signed "' . $document->document_name . '"',
                'related_model_type' => 'Document',
                'related_model_id' => $document->id,
            ]);
            broadcast(new NotificationSent($notification));
        } catch (\Exception $e) { /* silent */ }

        return response()->json([
            'success'       => true,
            'message'       => 'Document signed and sent to agent!',
            'signature_url' => $sigUrl,
            'document'      => $this->formatDocument($document),
        ]);
    }

    // ============================================================
    // AGENT: Sign a document they sent (embeds signature into PDF)
    // POST /api/agent/documents/{id}/sign
    // ============================================================
    public function signAgentDocument(Request $request, $id)
    {
        $request->validate([
            'signature_data'  => 'required|string',
            'signature_type'  => 'required|in:drawn,photo',
            'position_x'      => 'required|numeric',
            'position_y'      => 'required|numeric',
            'position_page'   => 'required|integer|min:1',
            'page_width'      => 'required|numeric|min:1',
            'page_height'     => 'required|numeric|min:1',
        ]);

        $user  = $request->user();
        $agent = $user->agent;

        if (!$agent) {
            return response()->json(['message' => 'Agent profile not found.'], 403);
        }

        $document = Document::where('agent_id', $agent->id)->findOrFail($id);

        // Prevent duplicate agent signature
        $existing = DocumentSigning::where('document_id', $document->id)
            ->where('signer_id', $user->id)
            ->first();

        if ($existing) {
            return response()->json(['message' => 'You have already signed this document.'], 422);
        }

        $signatureData = $request->signature_data;
        if (str_contains($signatureData, ',')) {
            $signatureData = explode(',', $signatureData, 2)[1];
        }

        $imageData = base64_decode($signatureData);
        $sigPath   = 'signatures/' . uniqid('agent_sig_', true) . '.png';
        Storage::disk('public')->put($sigPath, $imageData);
        $sigUrl = Storage::url($sigPath);

        // Embed signature into PDF if document is a PDF
        $isPdf = $document->document_path && str_ends_with(strtolower($document->document_path), '.pdf');
        if ($isPdf) {
            try {
                $newPdfPath = $this->embedSignatureIntoPdf(
                    $document->document_path,
                    $sigPath,
                    $request->position_x,
                    $request->position_y,
                    $request->position_page,
                    $request->page_width,
                    $request->page_height
                );
                $document->update([
                    'document_path' => $newPdfPath,
                    'document_url'  => Storage::url($newPdfPath),
                ]);
            } catch (\Exception $e) {
                Log::error('PDF agent signature embedding failed: ' . $e->getMessage());
            }
        }

        DocumentSigning::create([
            'document_id'    => $document->id,
            'signer_id'      => $user->id,
            'signature_path' => $sigPath,
            'signature_url'  => $sigUrl,
            'signature_type' => $request->signature_type,
            'signed_at'      => now(),
        ]);

        $document->load(['buyer', 'agent.user', 'signings.signer', 'property']);

        // Auto-send signed document to the buyer via messaging
        $recipientId = $document->buyer_id;
        if ($recipientId) {
            $this->autoSendSignedDocument($document, $user->id, $recipientId, $user->name);
        }

        return response()->json([
            'success'  => true,
            'message'  => 'Document signed and sent to buyer!',
            'document' => $this->formatDocument($document),
        ]);
    }

    // ============================================================
    // Download the original document
    // GET /api/documents/{id}/download
    // ============================================================
    public function downloadDocument($id)
    {
        $user     = request()->user();
        $document = Document::findOrFail($id);

        // Only the assigned buyer, the agent, or the uploader can download
        $isAgent    = $user->agent && $user->agent->id === $document->agent_id;
        $isBuyer    = $user->id === $document->buyer_id;
        $isUploader = $user->id === $document->uploaded_by;

        if (!$isAgent && !$isBuyer && !$isUploader) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        $path = Storage::disk('public')->path($document->document_path);

        if (!file_exists($path)) {
            return response()->json(['message' => 'File not found.'], 404);
        }

        return response()->download($path, $document->document_name . '.pdf');
    }

    // ============================================================
    // AGENT: Delete a document
    // DELETE /api/documents/{id}
    // ============================================================
    public function deleteDocument($id)
    {
        $user  = request()->user();
        $agent = $user->agent;

        if (!$agent) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        $document = Document::where('agent_id', $agent->id)->findOrFail($id);

        // Delete the file
        Storage::disk('public')->delete($document->document_path);

        $document->delete();

        return response()->json(['success' => true, 'message' => 'Document deleted.']);
    }

    // ============================================================
    // AGENT: Get buyers from conversations (for picker when sending docs)
    // GET /api/agent/conversation-buyers
    // ============================================================
    public function getConversationBuyers(Request $request)
    {
        $user  = $request->user();
        $agent = $user->agent;

        if (!$agent) {
            return response()->json(['message' => 'Agent profile not found.'], 403);
        }

        // Get unique buyers who have messaged this agent
        $buyers = Message::where('recipient_id', $user->id)
            ->with('sender')
            ->get()
            ->pluck('sender')
            ->unique('id')
            ->filter()
            ->values()
            ->map(fn ($b) => [
                'id'   => $b->id,
                'name' => $b->name,
                'email' => $b->email,
            ]);

        return response()->json(['success' => true, 'buyers' => $buyers]);
    }

    // ============================================================
    // Helper: format document for API responses
    // ============================================================
    private function formatDocument(Document $document): array
    {
        $makeUrl = fn($url) => $url
            ? (str_starts_with($url, 'http')
                ? $url
                : (rtrim(config('app.url'), '/') . $url))
            : null;

        // Separate agent vs buyer signings
        $agentUserId = $document->agent?->user_id;
        $buyerUserId = $document->buyer_id;

        $agentSigning = $document->signings?->first(fn($s) => $s->signer_id === $agentUserId);
        $buyerSigning = $document->signings?->first(fn($s) => $s->signer_id === $buyerUserId);

        return [
            'id'            => $document->id,
            'document_name' => $document->document_name,
            'document_type' => $document->document_type,
            'document_url'  => $makeUrl($document->document_url),
            'file_size'     => $document->file_size,
            'status'        => $document->status,
            'signed_at'     => $document->signed_at?->toISOString(),
            'created_at'    => $document->created_at?->toISOString(),

            // Buyer signature
            'signature_url'  => $makeUrl($buyerSigning?->signature_url),
            'signature_type' => $buyerSigning?->signature_type,

            // Agent signature
            'agent_signature_url'  => $makeUrl($agentSigning?->signature_url),
            'agent_signature_type' => $agentSigning?->signature_type,
            'agent_signed_at'      => $agentSigning?->signed_at?->toISOString(),

            // Convenience flat fields for frontend
            'agent_name'     => $document->agent?->user?->name ?? 'Agent',
            'property_title' => $document->property?->title,

            'property' => $document->property ? [
                'id'    => $document->property->id,
                'title' => $document->property->title,
            ] : null,
            'buyer' => $document->buyer ? [
                'id'   => $document->buyer->id,
                'name' => $document->buyer->name,
            ] : null,
            'agent' => $document->agent ? [
                'id'   => $document->agent->id,
                'name' => $document->agent->user?->name ?? 'Agent',
            ] : null,

            // Whether a physical file was uploaded (vs external link)
            'has_file' => !empty($document->document_path),

            // Personal upload fields
            'uploaded_by'  => $document->uploaded_by,
            'mime_type'    => $document->mime_type,
            'annotations'  => $document->annotations,
        ];
    }

    public function getUserDocuments(Request $request)
    {
        return $this->getBuyerDocuments($request);
    }

    // ============================================================
    // ANY USER: Upload a personal document
    // POST /api/documents/upload
    // ============================================================
    public function uploadPersonalDocument(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:20480|mimes:pdf,doc,docx,xls,xlsx,png,jpg,jpeg,gif,webp',
            'document_name' => 'nullable|string|max:255',
            'document_type' => 'nullable|in:contract,agreement,disclosure,other',
        ]);

        $user = $request->user();
        $file = $request->file('file');
        $path = $file->store('documents/' . $user->id, 'public');
        $url  = Storage::url($path);

        $document = Document::create([
            'uploaded_by'   => $user->id,
            'agent_id'      => $user->agent?->id,
            'buyer_id'      => $user->role === 'buyer' ? $user->id : null,
            'document_name' => $request->document_name ?: pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME),
            'document_type' => $request->document_type ?: 'other',
            'document_path' => $path,
            'document_url'  => $url,
            'file_size'     => $file->getSize(),
            'mime_type'     => $file->getMimeType(),
            'status'        => Document::STATUS_PENDING,
        ]);

        $document->load(['signings.signer', 'property']);

        return response()->json([
            'success'  => true,
            'message'  => 'Document uploaded successfully.',
            'document' => $this->formatDocument($document),
        ], 201);
    }

    // ============================================================
    // ANY USER: Get my uploaded documents
    // GET /api/documents/my
    // ============================================================
    public function getMyDocuments(Request $request)
    {
        $user = $request->user();

        $documents = Document::where('uploaded_by', $user->id)
            ->with(['signings.signer', 'property'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(fn ($d) => $this->formatDocument($d));

        return response()->json(['success' => true, 'documents' => $documents]);
    }

    // ============================================================
    // ANY USER: Save annotations for a document
    // PUT /api/documents/{id}/annotations
    // ============================================================
    public function saveAnnotations(Request $request, $id)
    {
        $request->validate([
            'annotations' => 'required|array',
        ]);

        $user = $request->user();
        $document = Document::where('uploaded_by', $user->id)->findOrFail($id);

        $document->update(['annotations' => $request->annotations]);

        return response()->json([
            'success' => true,
            'message' => 'Annotations saved.',
        ]);
    }

    // ============================================================
    // ANY USER: Save an exported/flattened document image
    // POST /api/documents/{id}/save-edited
    // ============================================================
    public function saveEditedDocument(Request $request, $id)
    {
        $request->validate([
            'edited_image' => 'required|string',
            'page_number'  => 'nullable|integer',
        ]);

        $user = $request->user();
        $document = Document::where('uploaded_by', $user->id)->findOrFail($id);

        $imageData = $request->edited_image;
        if (str_contains($imageData, ',')) {
            $imageData = explode(',', $imageData, 2)[1];
        }

        $decoded    = base64_decode($imageData);
        $editedPath = 'documents/' . $user->id . '/edited_' . uniqid() . '.png';
        Storage::disk('public')->put($editedPath, $decoded);

        $document->update([
            'document_path' => $editedPath,
            'document_url'  => Storage::url($editedPath),
            'mime_type'     => 'image/png',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Edited document saved.',
            'document' => $this->formatDocument($document->fresh(['signings.signer', 'property'])),
        ]);
    }

    // ============================================================
    // HELPER: Embed a signature image into a PDF using FPDI
    // Returns the storage path of the new signed PDF
    // ============================================================
    private function embedSignatureIntoPdf(
        string $pdfStoragePath,
        string $signatureStoragePath,
        float $posX,
        float $posY,
        int $page,
        float $canvasWidth,
        float $canvasHeight
    ): string {
        $pdfAbsPath = Storage::disk('public')->path($pdfStoragePath);
        $sigAbsPath = Storage::disk('public')->path($signatureStoragePath);

        $pdf = new Fpdi();
        $pageCount = $pdf->setSourceFile($pdfAbsPath);

        for ($i = 1; $i <= $pageCount; $i++) {
            $tplId = $pdf->importPage($i);
            $size  = $pdf->getTemplateSize($tplId);

            // Use the actual PDF page dimensions (in points)
            $pdfWidth  = $size['width'];
            $pdfHeight = $size['height'];

            $pdf->AddPage($size['orientation'], [$pdfWidth, $pdfHeight]);
            $pdf->useTemplate($tplId, 0, 0, $pdfWidth, $pdfHeight);

            // Place signature on the target page
            if ($i === $page) {
                // Scale canvas coordinates to PDF coordinates
                $scaleX = $pdfWidth / $canvasWidth;
                $scaleY = $pdfHeight / $canvasHeight;

                $sigPdfX = $posX * $scaleX;
                $sigPdfY = $posY * $scaleY;

                // Signature size: 150x60 points (~53mm x 21mm), scales with page
                $sigW = min(150, $pdfWidth * 0.25);
                $sigH = $sigW * 0.4;

                // Keep signature within page bounds
                $sigPdfX = min($sigPdfX, $pdfWidth - $sigW);
                $sigPdfY = min($sigPdfY, $pdfHeight - $sigH);
                $sigPdfX = max(0, $sigPdfX);
                $sigPdfY = max(0, $sigPdfY);

                $pdf->Image($sigAbsPath, $sigPdfX, $sigPdfY, $sigW, $sigH);
            }
        }

        // Save the new signed PDF
        $signedPath = 'documents/signed_' . uniqid('', true) . '.pdf';
        $signedAbsPath = Storage::disk('public')->path($signedPath);

        // Ensure directory exists
        $dir = dirname($signedAbsPath);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $pdf->Output('F', $signedAbsPath);

        return $signedPath;
    }

    // ============================================================
    // HELPER: Auto-send the signed document as a message
    // ============================================================
    private function autoSendSignedDocument(
        Document $document,
        int $senderId,
        int $recipientId,
        string $signerName
    ): void {
        try {
            // Copy the signed PDF to messages directory for attachment
            $attachmentPath = null;
            $attachmentName = $document->document_name . ' (Signed).pdf';
            $attachmentType = 'application/pdf';

            if ($document->document_path && Storage::disk('public')->exists($document->document_path)) {
                $msgPath = 'messages/' . $senderId . '/' . uniqid('signed_') . '.pdf';
                Storage::disk('public')->copy($document->document_path, $msgPath);
                $attachmentPath = Storage::url($msgPath);
            }

            $message = Message::create([
                'sender_id'       => $senderId,
                'recipient_id'    => $recipientId,
                'message_content' => 'I have signed the document: "' . $document->document_name . '"',
                'attachment_path' => $attachmentPath,
                'attachment_name' => $attachmentName,
                'attachment_type' => $attachmentType,
                'is_read'         => false,
            ]);

            try {
                broadcast(new MessageSent($message));
            } catch (\Exception $e) { /* Reverb not running */ }

            // Notification for the recipient
            Notification::create([
                'user_id'              => $recipientId,
                'notification_type'    => Notification::TYPE_MESSAGE,
                'title'                => 'Signed Document Received',
                'message'              => $signerName . ' signed "' . $document->document_name . '"',
                'related_model_type'   => 'Document',
                'related_model_id'     => $document->id,
            ]);
        } catch (\Exception $e) {
            Log::error('Auto-send signed document failed: ' . $e->getMessage());
        }
    }
}
