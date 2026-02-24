<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Agent;
use App\Models\AgentApplication;
use App\Services\GeminiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class AgentApplicationController extends Controller
{
    /**
     * Handle the become-agent application with AI verification.
     *
     * 3 possible AI outcomes:
     * - approved: Valid PRC license, info matches → instant agent
     * - unclear: Might be PRC but can't fully verify → admin review (pending)
     * - rejected: Clearly NOT a PRC license → rejected, can reapply after 12 hours
     */
    public function apply(Request $request)
    {
        try {
            $user = $request->user();

            if (!$user || $user->role !== 'buyer') {
                return response()->json([
                    'success' => false,
                    'message' => 'Only buyers can apply to become agents.',
                ], 403);
            }

            // Check for existing pending or approved application
            $existing = AgentApplication::where('user_id', $user->id)
                ->whereIn('status', ['pending', 'approved'])
                ->first();

            if ($existing) {
                return response()->json([
                    'success' => false,
                    'message' => $existing->status === 'approved'
                        ? 'You are already an approved agent.'
                        : 'You already have a pending application. Please wait for admin review.',
                    'application' => $existing,
                ], 409);
            }

            // Check 12-hour cooldown for rejected applications
            $recentRejected = AgentApplication::where('user_id', $user->id)
                ->where('status', 'rejected')
                ->where('rejected_at', '>=', now()->subHours(12))
                ->first();

            if ($recentRejected) {
                $canReapplyAt = \Carbon\Carbon::parse($recentRejected->rejected_at)->addHours(12);
                $hoursLeft = now()->diffInHours($canReapplyAt, false);
                $minutesLeft = now()->diffInMinutes($canReapplyAt, false) % 60;

                return response()->json([
                    'success' => false,
                    'message' => "Your previous application was rejected. You can reapply in {$hoursLeft}h {$minutesLeft}m.",
                    'can_reapply_at' => $canReapplyAt->toIso8601String(),
                    'application' => $recentRejected,
                ], 429);
            }

            // Validate input
            $validator = Validator::make($request->all(), [
                'applicant_name'      => 'required|string|max:255',
                'license_number'      => 'required|string|max:50',
                'prc_id'              => 'required|string|max:50',
                'company_name'        => 'required|string|max:255',
                'company_address'     => 'required|string|max:500',
                'accreditation'       => 'required|in:REIN,PAMI,OTHER',
                'bio'                 => 'nullable|string|max:1000',
                'license_expiry_date' => 'required|date|after:today',
                'prc_license_photo'   => 'required|image|mimes:jpg,jpeg,png|max:5120',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed.',
                    'errors'  => $validator->errors(),
                ], 422);
            }

            // Store the PRC license photo
            $photoPath = $request->file('prc_license_photo')->store('prc-licenses', 'public');

            // Prepare base64 for Gemini AI
            $imageContents = file_get_contents($request->file('prc_license_photo')->getRealPath());
            $base64Image   = base64_encode($imageContents);
            $mimeType      = $request->file('prc_license_photo')->getMimeType();

            // Call Gemini AI verification
            $gemini = new GeminiService();
            $verificationResult = $gemini->verifyPrcLicense($base64Image, $mimeType, [
                'name'            => $request->applicant_name,
                'account_name'    => $user->name,
                'account_email'   => $user->email,
                'license_number'  => $request->license_number,
                'prc_id'          => $request->prc_id,
                'expiry_date'     => $request->license_expiry_date,
            ]);

            $aiDecision = $verificationResult['decision']; // 'approved', 'unclear', or 'rejected'

            // Use DB transaction for data integrity
            $application = DB::transaction(function () use ($user, $request, $photoPath, $verificationResult, $aiDecision) {

                // Map AI decision to application status
                $statusMap = [
                    'approved' => 'approved',
                    'unclear'  => 'pending',    // admin will review
                    'rejected' => 'rejected',   // AI clearly rejected
                ];

                $status = $statusMap[$aiDecision] ?? 'pending';

                $application = AgentApplication::create([
                    'user_id'                => $user->id,
                    'applicant_name'         => $request->applicant_name,
                    'license_number'         => $request->license_number,
                    'prc_id'                 => $request->prc_id,
                    'prc_license_photo'      => $photoPath,
                    'license_expiry_date'    => $request->license_expiry_date,
                    'company_name'           => $request->company_name,
                    'company_address'        => $request->company_address,
                    'accreditation'          => $request->accreditation,
                    'bio'                    => $request->bio,
                    'ai_verification_result' => json_encode($verificationResult['extracted']),
                    'ai_decision'            => $aiDecision,
                    'status'                 => $status,
                    'applied_at'             => now(),
                    'verified_at'            => $aiDecision === 'approved' ? now() : null,
                    'rejected_at'            => $aiDecision === 'rejected' ? now() : null,
                    'rejection_reason'       => $aiDecision === 'rejected'
                        ? 'RealtyLinkPH Buddy determined the uploaded document is not a valid PRC license. ' . ($verificationResult['reasoning'] ?? '')
                        : null,
                ]);

                if ($aiDecision === 'approved') {
                    // Create the Agent record
                    Agent::create([
                        'user_id'                   => $user->id,
                        'real_estate_license_number' => $request->license_number,
                        'prc_id_number'             => $request->prc_id,
                        'accreditation'             => $request->accreditation,
                        'company_name'              => $request->company_name,
                        'company_address'           => $request->company_address,
                        'bio'                       => $request->bio,
                        'verification_status'       => 'verified',
                        'verified_at'               => now(),
                        'license_expiry_date'       => $request->license_expiry_date,
                    ]);

                    // Update user role to agent
                    $user->update(['role' => 'agent']);
                }

                return $application;
            });

            // Return appropriate response based on AI decision
            if ($aiDecision === 'approved') {
                return response()->json([
                    'success'      => true,
                    'decision'     => 'approved',
                    'approved'     => true,
                    'message'      => 'Congratulations! RealtyLinkPH Buddy has verified your PRC license. You are now a registered agent!',
                    'ai_reasoning' => $verificationResult['reasoning'],
                    'application'  => $application,
                ]);
            } elseif ($aiDecision === 'rejected') {
                $documentType = $verificationResult['extracted']['document_type'] ?? 'non-PRC document';
                return response()->json([
                    'success'       => true,
                    'decision'      => 'rejected',
                    'approved'      => false,
                    'message'       => "RealtyLinkPH Buddy detected that the uploaded document is not a valid PRC license. It appears to be a \"{$documentType}\". Please upload a genuine PRC license card. You may reapply after 12 hours.",
                    'ai_reasoning'  => $verificationResult['reasoning'],
                    'document_type' => $documentType,
                    'application'   => $application,
                ]);
            } else {
                // unclear → pending for admin review
                return response()->json([
                    'success'      => true,
                    'decision'     => 'unclear',
                    'approved'     => false,
                    'message'      => 'RealtyLinkPH Buddy was unable to fully verify your PRC license. Your application has been submitted for admin review. Please allow up to 12 hours.',
                    'ai_reasoning' => $verificationResult['reasoning'],
                    'application'  => $application,
                ]);
            }

        } catch (\Exception $e) {
            Log::error('Become agent error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while processing your application. Please try again.',
            ], 500);
        }
    }

    /**
     * Check the current user's application status.
     */
    public function status(Request $request)
    {
        $user = $request->user();

        $application = AgentApplication::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->first();

        if (!$application) {
            return response()->json([
                'success'        => true,
                'hasApplication' => false,
                'application'    => null,
            ]);
        }

        // If rejected, include reapply info
        $canReapply = false;
        $canReapplyAt = null;

        if ($application->status === 'rejected' && $application->rejected_at) {
            $reapplyTime = \Carbon\Carbon::parse($application->rejected_at)->addHours(12);
            $canReapply = now()->gte($reapplyTime);
            $canReapplyAt = $reapplyTime->toIso8601String();
        }

        return response()->json([
            'success'        => true,
            'hasApplication' => true,
            'application'    => $application,
            'canReapply'     => $canReapply,
            'canReapplyAt'   => $canReapplyAt,
        ]);
    }
}
