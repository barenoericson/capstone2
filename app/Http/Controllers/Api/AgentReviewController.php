<?php

namespace App\Http\Controllers\Api;

use App\Events\NotificationSent;
use App\Models\Agent;
use App\Models\AgentReview;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Storage;

class AgentReviewController extends BaseController
{
    // ============================================================
    // PUBLIC: Get agent public profile + reviews
    // GET /api/agents/{userId}/profile
    // (userId = the agent's user_id, easier to look up from chat)
    // ============================================================
    public function agentProfile($userId)
    {
        $user = User::with(['agent.reviews.buyer'])->findOrFail($userId);

        if (!$user->agent) {
            return response()->json(['message' => 'Agent not found.'], 404);
        }

        $agent = $user->agent;

        $reviews = $agent->reviews()
            ->with('buyer')
            ->latest()
            ->get()
            ->map(fn ($r) => $this->formatReview($r));

        return response()->json([
            'success' => true,
            'agent' => [
                'id'                       => $agent->id,
                'user_id'                  => $user->id,
                'name'                     => $user->name,
                'email'                    => $user->email,
                'profile_photo_url'        => $this->photoUrl($user->profile_photo_path),
                'bio'                      => $agent->bio,
                'company_name'             => $agent->company_name,
                'company_address'          => $agent->company_address,
                'accreditation'            => $agent->accreditation,
                'verification_status'      => $agent->verification_status,
                'average_rating'           => round((float) $agent->average_rating, 1),
                'total_reviews'            => (int) $agent->total_reviews,
                'license_number'           => $agent->real_estate_license_number,
                'created_at'               => $user->created_at?->toISOString(),
            ],
            'reviews' => $reviews,
        ]);
    }

    // ============================================================
    // PUBLIC: Get buyer public profile (for agents to view)
    // GET /api/users/{userId}/profile
    // ============================================================
    public function buyerProfile($userId)
    {
        $user = User::findOrFail($userId);

        return response()->json([
            'success' => true,
            'user' => [
                'id'                => $user->id,
                'name'              => $user->name,
                'email'             => $user->email,
                'profile_photo_url' => $this->photoUrl($user->profile_photo_path),
                'city'              => $user->city,
                'province'          => $user->province,
                'role'              => $user->role,
                'created_at'        => $user->created_at?->toISOString(),
            ],
        ]);
    }

    // ============================================================
    // BUYER: Submit a review for an agent
    // POST /api/buyer/agents/{agentId}/reviews
    // ============================================================
    public function store(Request $request, $agentId)
    {
        $request->validate([
            'rating'        => 'required|integer|min:1|max:5',
            'review_title'  => 'nullable|string|max:100',
            'review_content'=> 'nullable|string|max:1000',
        ]);

        $buyer = $request->user();
        $agent = Agent::findOrFail($agentId);

        // Prevent duplicate review from same buyer for same agent
        $existing = AgentReview::where('agent_id', $agent->id)
            ->where('buyer_id', $buyer->id)
            ->first();

        if ($existing) {
            // Update existing review instead of blocking
            $existing->update([
                'rating'         => $request->rating,
                'review_title'   => $request->review_title,
                'review_content' => $request->review_content,
            ]);
            $agent->updateAverageRating();
            return response()->json([
                'success' => true,
                'message' => 'Review updated.',
                'review'  => $this->formatReview($existing->fresh(['buyer'])),
                'average_rating' => round((float) $agent->fresh()->average_rating, 1),
                'total_reviews'  => (int) $agent->fresh()->total_reviews,
            ]);
        }

        $review = AgentReview::create([
            'agent_id'       => $agent->id,
            'buyer_id'       => $buyer->id,
            'rating'         => $request->rating,
            'review_title'   => $request->review_title,
            'review_content' => $request->review_content,
        ]);

        $agent->updateAverageRating();

        // Notify the agent about the new review
        try {
            $stars = str_repeat('*', $request->rating);
            $notification = Notification::create([
                'user_id' => $agent->user_id,
                'notification_type' => Notification::TYPE_SYSTEM,
                'title' => 'New Review Received',
                'message' => $buyer->name . ' rated your performance ' . $request->rating . '/5',
                'related_model_type' => 'AgentReview',
                'related_model_id' => $review->id,
            ]);
            broadcast(new NotificationSent($notification));
        } catch (\Exception $e) { /* silent */ }

        return response()->json([
            'success' => true,
            'message' => 'Review submitted. Thank you!',
            'review'  => $this->formatReview($review->load('buyer')),
            'average_rating' => round((float) $agent->fresh()->average_rating, 1),
            'total_reviews'  => (int) $agent->fresh()->total_reviews,
        ], 201);
    }

    // ============================================================
    // Helper
    // ============================================================
    private function photoUrl(?string $path): ?string
    {
        if (!$path) return null;
        return url('/storage/' . $path);
    }

    private function formatReview(AgentReview $r): array
    {
        return [
            'id'             => $r->id,
            'rating'         => $r->rating,
            'review_title'   => $r->review_title,
            'review_content' => $r->review_content,
            'created_at'     => $r->created_at?->toISOString(),
            'buyer' => $r->buyer ? [
                'id'                => $r->buyer->id,
                'name'              => $r->buyer->name,
                'profile_photo_url' => $this->photoUrl($r->buyer->profile_photo_path),
            ] : null,
        ];
    }
}
