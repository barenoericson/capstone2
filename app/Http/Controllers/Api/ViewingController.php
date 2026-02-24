<?php

namespace App\Http\Controllers\Api;

use App\Events\NotificationSent;
use App\Events\ViewingRequested;
use App\Events\ViewingStatusChanged;
use App\Models\AgentBlockedDate;
use App\Models\Notification;
use App\Models\Property;
use App\Models\Viewing;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;

class ViewingController extends BaseController
{
    // ============================================================
    // BUYER: Request a viewing
    // POST /api/buyer/viewings
    // ============================================================
    public function requestViewing(Request $request)
    {
        $request->validate([
            'property_id'  => 'required|exists:properties,id',
            'viewing_date' => 'required|date|after_or_equal:today',
            'viewing_time' => 'required',
            'buyer_notes'  => 'nullable|string|max:500',
        ]);

        $buyer    = $request->user();
        $property = Property::with('agent')->findOrFail($request->property_id);
        $agent    = $property->agent;

        if (!$agent) {
            return response()->json(['message' => 'This property has no assigned agent.'], 422);
        }

        // Check if the date is blocked
        $isBlocked = AgentBlockedDate::where('agent_id', $agent->id)
            ->where('blocked_date', $request->viewing_date)
            ->exists();

        if ($isBlocked) {
            return response()->json(['message' => 'This date is not available. Please choose another date.'], 422);
        }

        // Prevent duplicate pending request for same property
        $duplicate = Viewing::where('buyer_id', $buyer->id)
            ->where('property_id', $request->property_id)
            ->where('status', Viewing::STATUS_REQUESTED)
            ->exists();

        if ($duplicate) {
            return response()->json(['message' => 'You already have a pending viewing request for this property.'], 422);
        }

        $viewing = Viewing::create([
            'property_id'  => $request->property_id,
            'agent_id'     => $agent->id,
            'buyer_id'     => $buyer->id,
            'viewing_date' => $request->viewing_date,
            'viewing_time' => $request->viewing_time,
            'status'       => Viewing::STATUS_REQUESTED,
            'buyer_notes'  => $request->buyer_notes,
        ]);

        $viewing->load(['property', 'buyer', 'agent.user']);

        try {
            broadcast(new ViewingRequested($viewing));
        } catch (\Exception $e) {
            // Reverb not running — continue silently
        }

        // Create notification for the agent
        try {
            $notification = Notification::create([
                'user_id' => $viewing->agent->user_id,
                'notification_type' => Notification::TYPE_VIEWING_REQUEST,
                'title' => 'New Viewing Request',
                'message' => $buyer->name . ' requested a viewing for ' . $viewing->property->title,
                'related_model_type' => 'Viewing',
                'related_model_id' => $viewing->id,
            ]);
            broadcast(new NotificationSent($notification));
        } catch (\Exception $e) { /* silent */ }

        return response()->json([
            'success' => true,
            'message' => 'Viewing request submitted!',
            'viewing' => $this->formatViewing($viewing),
        ], 201);
    }

    // ============================================================
    // BUYER: Get my viewings
    // GET /api/buyer/viewings
    // ============================================================
    public function getBuyerViewings(Request $request)
    {
        $buyer  = $request->user();
        $status = $request->query('status');

        $query = Viewing::where('buyer_id', $buyer->id)
            ->with(['property.photos', 'agent.user'])
            ->orderBy('viewing_date', 'desc')
            ->orderBy('created_at', 'desc');

        if ($status) {
            $query->where('status', $status);
        }

        return response()->json([
            'success'  => true,
            'viewings' => $query->get()->map(fn ($v) => $this->formatViewing($v)),
        ]);
    }

    // ============================================================
    // AGENT: Get viewing requests for my properties
    // GET /api/agent/viewings
    // ============================================================
    public function getAgentViewings(Request $request)
    {
        $user  = $request->user();
        $agent = $user->agent;

        if (!$agent) {
            return response()->json(['message' => 'Agent profile not found.'], 403);
        }

        $status = $request->query('status');

        $query = Viewing::where('agent_id', $agent->id)
            ->with(['property.photos', 'buyer'])
            ->orderBy('created_at', 'desc');

        if ($status) {
            $query->where('status', $status);
        }

        return response()->json([
            'success'  => true,
            'viewings' => $query->get()->map(fn ($v) => $this->formatViewing($v)),
        ]);
    }

    // ============================================================
    // AGENT: Approve a viewing
    // PUT /api/agent/viewings/{id}/approve
    // ============================================================
    public function approveViewing($id)
    {
        $user  = request()->user();
        $agent = $user->agent;

        if (!$agent) {
            return response()->json(['message' => 'Agent profile not found.'], 403);
        }

        $viewing = Viewing::where('agent_id', $agent->id)->findOrFail($id);

        if ($viewing->status !== Viewing::STATUS_REQUESTED) {
            return response()->json(['message' => 'Only pending viewings can be approved.'], 422);
        }

        $viewing->update(['status' => Viewing::STATUS_APPROVED]);
        $viewing->load(['property', 'buyer', 'agent.user']);

        try {
            broadcast(new ViewingStatusChanged($viewing));
        } catch (\Exception $e) {}

        // Notify the buyer
        try {
            $notification = Notification::create([
                'user_id' => $viewing->buyer_id,
                'notification_type' => Notification::TYPE_VIEWING_REQUEST,
                'title' => 'Viewing Approved',
                'message' => 'Your viewing for ' . $viewing->property->title . ' has been approved!',
                'related_model_type' => 'Viewing',
                'related_model_id' => $viewing->id,
            ]);
            broadcast(new NotificationSent($notification));
        } catch (\Exception $e) { /* silent */ }

        return response()->json([
            'success' => true,
            'message' => 'Viewing approved!',
            'viewing' => $this->formatViewing($viewing),
        ]);
    }

    // ============================================================
    // AGENT: Reject a viewing with a mandatory reason
    // PUT /api/agent/viewings/{id}/reject
    // ============================================================
    public function rejectViewing(Request $request, $id)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        $user  = $request->user();
        $agent = $user->agent;

        if (!$agent) {
            return response()->json(['message' => 'Agent profile not found.'], 403);
        }

        $viewing = Viewing::where('agent_id', $agent->id)->findOrFail($id);

        if ($viewing->status !== Viewing::STATUS_REQUESTED) {
            return response()->json(['message' => 'Only pending viewings can be rejected.'], 422);
        }

        $viewing->update([
            'status'           => Viewing::STATUS_REJECTED,
            'rejection_reason' => $request->rejection_reason,
        ]);

        $viewing->load(['property', 'buyer', 'agent.user']);

        try {
            broadcast(new ViewingStatusChanged($viewing));
        } catch (\Exception $e) {}

        // Notify the buyer
        try {
            $notification = Notification::create([
                'user_id' => $viewing->buyer_id,
                'notification_type' => Notification::TYPE_VIEWING_REQUEST,
                'title' => 'Viewing Rejected',
                'message' => 'Your viewing for ' . $viewing->property->title . ' was rejected: ' . $request->rejection_reason,
                'related_model_type' => 'Viewing',
                'related_model_id' => $viewing->id,
            ]);
            broadcast(new NotificationSent($notification));
        } catch (\Exception $e) { /* silent */ }

        return response()->json([
            'success' => true,
            'message' => 'Viewing rejected.',
            'viewing' => $this->formatViewing($viewing),
        ]);
    }

    // ============================================================
    // AGENT: Mark viewing as completed
    // PUT /api/agent/viewings/{id}/completed
    // ============================================================
    public function markCompleted($id)
    {
        $user  = request()->user();
        $agent = $user->agent;

        if (!$agent) {
            return response()->json(['message' => 'Agent profile not found.'], 403);
        }

        $viewing = Viewing::where('agent_id', $agent->id)->findOrFail($id);
        $viewing->update(['status' => Viewing::STATUS_COMPLETED]);

        return response()->json(['success' => true, 'message' => 'Viewing marked as completed.']);
    }

    // ============================================================
    // Reminder stub
    // ============================================================
    public function sendReminder($id)
    {
        return response()->json(['success' => true, 'message' => 'Reminder sent.']);
    }

    // ============================================================
    // AGENT: Get my blocked dates
    // GET /api/agent/blocked-dates
    // ============================================================
    public function getBlockedDates(Request $request)
    {
        $user  = $request->user();
        $agent = $user->agent;

        if (!$agent) {
            return response()->json(['message' => 'Agent profile not found.'], 403);
        }

        $dates = AgentBlockedDate::where('agent_id', $agent->id)
            ->orderBy('blocked_date')
            ->get()
            ->map(fn ($d) => [
                'id'           => $d->id,
                'blocked_date' => $d->blocked_date->format('Y-m-d'),
                'reason'       => $d->reason,
            ]);

        return response()->json(['success' => true, 'blocked_dates' => $dates]);
    }

    // ============================================================
    // PUBLIC: Get blocked dates for a property's agent (buyers call this)
    // GET /api/properties/{id}/blocked-dates
    // ============================================================
    public function getPropertyBlockedDates($propertyId)
    {
        $property = Property::with('agent')->findOrFail($propertyId);

        if (!$property->agent) {
            return response()->json(['success' => true, 'blocked_dates' => []]);
        }

        $dates = AgentBlockedDate::where('agent_id', $property->agent->id)
            ->where('blocked_date', '>=', now()->toDateString())
            ->orderBy('blocked_date')
            ->get()
            ->map(fn ($d) => $d->blocked_date->format('Y-m-d'));

        return response()->json(['success' => true, 'blocked_dates' => $dates]);
    }

    // ============================================================
    // AGENT: Block a date
    // POST /api/agent/blocked-dates
    // ============================================================
    public function blockDate(Request $request)
    {
        $request->validate([
            'blocked_date' => 'required|date|after_or_equal:today',
            'reason'       => 'nullable|string|max:255',
        ]);

        $user  = $request->user();
        $agent = $user->agent;

        if (!$agent) {
            return response()->json(['message' => 'Agent profile not found.'], 403);
        }

        $record = AgentBlockedDate::firstOrCreate(
            ['agent_id' => $agent->id, 'blocked_date' => $request->blocked_date],
            ['reason'   => $request->reason]
        );

        return response()->json([
            'success'      => true,
            'message'      => 'Date blocked.',
            'blocked_date' => [
                'id'           => $record->id,
                'blocked_date' => $record->blocked_date->format('Y-m-d'),
                'reason'       => $record->reason,
            ],
        ], 201);
    }

    // ============================================================
    // AGENT: Unblock a date
    // DELETE /api/agent/blocked-dates/{id}
    // ============================================================
    public function unblockDate($id)
    {
        $user  = request()->user();
        $agent = $user->agent;

        if (!$agent) {
            return response()->json(['message' => 'Agent profile not found.'], 403);
        }

        $record = AgentBlockedDate::where('agent_id', $agent->id)->findOrFail($id);
        $record->delete();

        return response()->json(['success' => true, 'message' => 'Date unblocked.']);
    }

    // ============================================================
    // Helper: format a viewing for API responses
    // ============================================================
    private function formatViewing(Viewing $viewing): array
    {
        $coverPhoto = null;
        if ($viewing->property && $viewing->property->photos) {
            $cover      = $viewing->property->photos->where('is_cover', true)->first();
            $coverPhoto = $cover
                ? $cover->photo_url
                : ($viewing->property->photos->first() ? $viewing->property->photos->first()->photo_url : null);
        }

        return [
            'id'               => $viewing->id,
            'status'           => $viewing->status,
            'viewing_date'     => $viewing->viewing_date ? $viewing->viewing_date->format('Y-m-d') : null,
            'viewing_time'     => $viewing->viewing_time
                ? (is_string($viewing->viewing_time) ? $viewing->viewing_time : $viewing->viewing_time->format('H:i'))
                : null,
            'buyer_notes'      => $viewing->buyer_notes,
            'rejection_reason' => $viewing->rejection_reason,
            'created_at'       => $viewing->created_at ? $viewing->created_at->toISOString() : null,
            'property'         => $viewing->property ? [
                'id'          => $viewing->property->id,
                'title'       => $viewing->property->title,
                'city'        => $viewing->property->city,
                'cover_photo' => $coverPhoto,
            ] : null,
            'buyer' => $viewing->buyer ? [
                'id'    => $viewing->buyer->id,
                'name'  => $viewing->buyer->name,
                'email' => $viewing->buyer->email,
            ] : null,
            'agent' => $viewing->agent ? [
                'id'      => $viewing->agent->id,
                'user_id' => $viewing->agent->user_id,
                'name'    => $viewing->agent->user ? $viewing->agent->user->name : 'Agent',
            ] : null,
        ];
    }
}
