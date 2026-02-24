<?php

namespace App\Http\Controllers\Api;

use App\Events\DocumentSigned;
use App\Events\NotificationSent;
use App\Models\Document;
use App\Models\DocumentSigning;
use App\Models\Message;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Storage;

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
    // BUYER: Sign a document (accepts base64 signature image)
    // POST /api/buyer/documents/{id}/sign
    // ============================================================
    public function signDocument(Request $request, $id)
    {
        $request->validate([
            'signature_data' => 'required|string',    // base64 PNG data URL
            'signature_type' => 'required|in:drawn,photo',
        ]);

        $buyer    = $request->user();
        $document = Document::where('buyer_id', $buyer->id)->findOrFail($id);

        if ($document->status === Document::STATUS_SIGNED) {
            return response()->json(['message' => 'This document has already been signed.'], 422);
        }

        // Decode and save the signature image
        $signatureData = $request->signature_data;

        // Remove data URL prefix (data:image/png;base64,)
        if (str_contains($signatureData, ',')) {
            $signatureData = explode(',', $signatureData, 2)[1];
        }

        $imageData     = base64_decode($signatureData);
        $sigPath       = 'signatures/' . uniqid('sig_', true) . '.png';
        Storage::disk('public')->put($sigPath, $imageData);
        $sigUrl = Storage::url($sigPath);

        // Create signing record
        $signing = DocumentSigning::create([
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

        // Notify the agent
        try {
            broadcast(new DocumentSigned($document));
        } catch (\Exception $e) {
            // Reverb not running — continue silently
        }

        // Create notification for the agent
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
            'message'       => 'Document signed successfully!',
            'signature_url' => $sigUrl,
            'document'      => $this->formatDocument($document),
        ]);
    }

    // ============================================================
    // AGENT: Sign a document they sent
    // POST /api/agent/documents/{id}/sign
    // ============================================================
    public function signAgentDocument(Request $request, $id)
    {
        $request->validate([
            'signature_data' => 'required|string',
            'signature_type' => 'required|in:drawn,photo',
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

        DocumentSigning::create([
            'document_id'    => $document->id,
            'signer_id'      => $user->id,
            'signature_path' => $sigPath,
            'signature_url'  => $sigUrl,
            'signature_type' => $request->signature_type,
            'signed_at'      => now(),
        ]);

        $document->load(['buyer', 'agent.user', 'signings.signer', 'property']);

        return response()->json([
            'success'  => true,
            'message'  => 'Document signed successfully!',
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

        // Only the assigned buyer or the agent can download
        $isAgent = $user->agent && $user->agent->id === $document->agent_id;
        $isBuyer = $user->id === $document->buyer_id;

        if (!$isAgent && !$isBuyer) {
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
        ];
    }

    public function getUserDocuments(Request $request)
    {
        return $this->getBuyerDocuments($request);
    }
}
