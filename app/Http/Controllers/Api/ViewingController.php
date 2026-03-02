<?php

namespace App\Http\Controllers\Api;

use App\Events\NotificationSent;
use App\Events\ViewingNegotiationProposed;
use App\Events\ViewingRequested;
use App\Events\ViewingStatusChanged;
use App\Mail\ViewingConfirmation;
use App\Mail\ViewingNegotiationMail;
use App\Models\AgentBlockedDate;
use App\Models\Notification;
use App\Models\Property;
use App\Models\Viewing;
use App\Models\ViewingNegotiation;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

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

        if (!in_array($viewing->status, [Viewing::STATUS_REQUESTED, Viewing::STATUS_NEGOTIATING])) {
            return response()->json(['message' => 'Only pending or negotiating viewings can be approved.'], 422);
        }

        $viewing->update([
            'status' => Viewing::STATUS_APPROVED,
            'approved_at' => now(),
            'approved_by_user_id' => $user->id,
        ]);
        $viewing->load(['property', 'buyer', 'agent.user']);

        // Mark any pending negotiations as accepted
        $viewing->negotiations()->where('status', ViewingNegotiation::STATUS_PENDING)->update([
            'status' => ViewingNegotiation::STATUS_ACCEPTED,
        ]);

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

        // Send confirmation emails
        $this->handleViewingApproved($viewing);

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

        if (!in_array($viewing->status, [Viewing::STATUS_REQUESTED, Viewing::STATUS_NEGOTIATING])) {
            return response()->json(['message' => 'Only pending or negotiating viewings can be rejected.'], 422);
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
    // PUBLIC: Get booked dates for a property (for buyer calendar)
    // GET /api/properties/{id}/booked-dates
    // ============================================================
    public function getPropertyBookedDates($propertyId)
    {
        $bookedDates = Viewing::where('property_id', $propertyId)
            ->where('status', Viewing::STATUS_APPROVED)
            ->where('viewing_date', '>=', today())
            ->pluck('viewing_date')
            ->map(fn ($d) => $d->format('Y-m-d'))
            ->unique()
            ->values();

        return response()->json(['success' => true, 'booked_dates' => $bookedDates]);
    }

    // ============================================================
    // AGENT: Counter-propose a new schedule
    // PUT /api/agent/viewings/{id}/counter-propose
    // ============================================================
    public function agentCounterPropose(Request $request, $id)
    {
        $request->validate([
            'proposed_date' => 'required|date|after_or_equal:today',
            'proposed_time' => 'required',
            'note'          => 'nullable|string|max:500',
        ]);

        $user  = $request->user();
        $agent = $user->agent;

        if (!$agent) {
            return response()->json(['message' => 'Agent profile not found.'], 403);
        }

        $viewing = Viewing::where('agent_id', $agent->id)->findOrFail($id);

        if (!in_array($viewing->status, [Viewing::STATUS_REQUESTED, Viewing::STATUS_NEGOTIATING])) {
            return response()->json(['message' => 'Cannot edit schedule for this viewing.'], 422);
        }

        // Supersede any existing pending proposals
        $viewing->negotiations()->where('status', ViewingNegotiation::STATUS_PENDING)
            ->update(['status' => ViewingNegotiation::STATUS_SUPERSEDED]);

        $negotiation = ViewingNegotiation::create([
            'viewing_id'          => $viewing->id,
            'proposed_by_user_id' => $user->id,
            'proposed_by_role'    => 'agent',
            'proposed_date'       => $request->proposed_date,
            'proposed_time'       => $request->proposed_time,
            'note'                => $request->note,
        ]);

        $viewing->update(['status' => Viewing::STATUS_NEGOTIATING]);
        $viewing->load(['property', 'buyer', 'agent.user']);

        // Broadcast & notify the buyer
        try {
            broadcast(new ViewingNegotiationProposed($viewing, $negotiation, $viewing->buyer_id));
        } catch (\Exception $e) {}

        try {
            $notification = Notification::create([
                'user_id' => $viewing->buyer_id,
                'notification_type' => Notification::TYPE_VIEWING_REQUEST,
                'title' => 'New Schedule Proposed',
                'message' => ($viewing->agent->user->name ?? 'Agent') . ' proposed a new schedule for ' . $viewing->property->title,
                'related_model_type' => 'Viewing',
                'related_model_id' => $viewing->id,
            ]);
            broadcast(new NotificationSent($notification));
        } catch (\Exception $e) { /* silent */ }

        // Send email
        try {
            Mail::to($viewing->buyer->email)->send(new ViewingNegotiationMail($viewing, $negotiation, 'buyer'));
        } catch (\Exception $e) {
            Log::warning('Negotiation email failed: ' . $e->getMessage());
        }

        return response()->json([
            'success' => true,
            'message' => 'New schedule proposed.',
            'viewing' => $this->formatViewing($viewing),
        ]);
    }

    // ============================================================
    // BUYER: Counter-propose a new schedule
    // PUT /api/buyer/viewings/{id}/counter-propose
    // ============================================================
    public function buyerCounterPropose(Request $request, $id)
    {
        $request->validate([
            'proposed_date' => 'required|date|after_or_equal:today',
            'proposed_time' => 'required',
            'note'          => 'nullable|string|max:500',
        ]);

        $user    = $request->user();
        $viewing = Viewing::where('buyer_id', $user->id)->findOrFail($id);

        if (!in_array($viewing->status, [Viewing::STATUS_REQUESTED, Viewing::STATUS_NEGOTIATING])) {
            return response()->json(['message' => 'Cannot edit schedule for this viewing.'], 422);
        }

        // Supersede any existing pending proposals
        $viewing->negotiations()->where('status', ViewingNegotiation::STATUS_PENDING)
            ->update(['status' => ViewingNegotiation::STATUS_SUPERSEDED]);

        $negotiation = ViewingNegotiation::create([
            'viewing_id'          => $viewing->id,
            'proposed_by_user_id' => $user->id,
            'proposed_by_role'    => 'buyer',
            'proposed_date'       => $request->proposed_date,
            'proposed_time'       => $request->proposed_time,
            'note'                => $request->note,
        ]);

        $viewing->update(['status' => Viewing::STATUS_NEGOTIATING]);
        $viewing->load(['property', 'buyer', 'agent.user']);

        $targetUserId = $viewing->agent->user_id;

        // Broadcast & notify the agent
        try {
            broadcast(new ViewingNegotiationProposed($viewing, $negotiation, $targetUserId));
        } catch (\Exception $e) {}

        try {
            $notification = Notification::create([
                'user_id' => $targetUserId,
                'notification_type' => Notification::TYPE_VIEWING_REQUEST,
                'title' => 'New Schedule Proposed',
                'message' => $user->name . ' proposed a new schedule for ' . $viewing->property->title,
                'related_model_type' => 'Viewing',
                'related_model_id' => $viewing->id,
            ]);
            broadcast(new NotificationSent($notification));
        } catch (\Exception $e) { /* silent */ }

        // Send email
        try {
            Mail::to($viewing->agent->user->email)->send(new ViewingNegotiationMail($viewing, $negotiation, 'agent'));
        } catch (\Exception $e) {
            Log::warning('Negotiation email failed: ' . $e->getMessage());
        }

        return response()->json([
            'success' => true,
            'message' => 'New schedule proposed.',
            'viewing' => $this->formatViewing($viewing),
        ]);
    }

    // ============================================================
    // AGENT: Accept the buyer's proposal
    // PUT /api/agent/viewings/{id}/accept-proposal
    // ============================================================
    public function agentAcceptProposal($id)
    {
        $user  = request()->user();
        $agent = $user->agent;

        if (!$agent) {
            return response()->json(['message' => 'Agent profile not found.'], 403);
        }

        $viewing = Viewing::where('agent_id', $agent->id)->findOrFail($id);

        if ($viewing->status !== Viewing::STATUS_NEGOTIATING) {
            return response()->json(['message' => 'No active negotiation to accept.'], 422);
        }

        $proposal = $viewing->latestProposal();
        if (!$proposal) {
            return response()->json(['message' => 'No pending proposal found.'], 422);
        }

        // Accept the proposal
        $proposal->update(['status' => ViewingNegotiation::STATUS_ACCEPTED]);

        // Update viewing with the accepted schedule
        $viewing->update([
            'viewing_date'       => $proposal->proposed_date,
            'viewing_time'       => $proposal->proposed_time,
            'status'             => Viewing::STATUS_APPROVED,
            'approved_at'        => now(),
            'approved_by_user_id' => $user->id,
        ]);

        $viewing->load(['property', 'buyer', 'agent.user']);

        try {
            broadcast(new ViewingStatusChanged($viewing));
        } catch (\Exception $e) {}

        try {
            $notification = Notification::create([
                'user_id' => $viewing->buyer_id,
                'notification_type' => Notification::TYPE_VIEWING_REQUEST,
                'title' => 'Viewing Approved',
                'message' => 'Your proposed schedule for ' . $viewing->property->title . ' has been approved!',
                'related_model_type' => 'Viewing',
                'related_model_id' => $viewing->id,
            ]);
            broadcast(new NotificationSent($notification));
        } catch (\Exception $e) { /* silent */ }

        $this->handleViewingApproved($viewing);

        return response()->json([
            'success' => true,
            'message' => 'Proposal accepted! Viewing confirmed.',
            'viewing' => $this->formatViewing($viewing),
        ]);
    }

    // ============================================================
    // BUYER: Accept the agent's proposal
    // PUT /api/buyer/viewings/{id}/accept-proposal
    // ============================================================
    public function buyerAcceptProposal($id)
    {
        $user    = request()->user();
        $viewing = Viewing::where('buyer_id', $user->id)->findOrFail($id);

        if ($viewing->status !== Viewing::STATUS_NEGOTIATING) {
            return response()->json(['message' => 'No active negotiation to accept.'], 422);
        }

        $proposal = $viewing->latestProposal();
        if (!$proposal) {
            return response()->json(['message' => 'No pending proposal found.'], 422);
        }

        // Accept the proposal
        $proposal->update(['status' => ViewingNegotiation::STATUS_ACCEPTED]);

        // Update viewing with the accepted schedule
        $viewing->update([
            'viewing_date'       => $proposal->proposed_date,
            'viewing_time'       => $proposal->proposed_time,
            'status'             => Viewing::STATUS_APPROVED,
            'approved_at'        => now(),
            'approved_by_user_id' => $user->id,
        ]);

        $viewing->load(['property', 'buyer', 'agent.user']);

        try {
            broadcast(new ViewingStatusChanged($viewing));
        } catch (\Exception $e) {}

        try {
            $notification = Notification::create([
                'user_id' => $viewing->agent->user_id,
                'notification_type' => Notification::TYPE_VIEWING_REQUEST,
                'title' => 'Viewing Approved',
                'message' => $user->name . ' accepted the proposed schedule for ' . $viewing->property->title,
                'related_model_type' => 'Viewing',
                'related_model_id' => $viewing->id,
            ]);
            broadcast(new NotificationSent($notification));
        } catch (\Exception $e) { /* silent */ }

        $this->handleViewingApproved($viewing);

        return response()->json([
            'success' => true,
            'message' => 'Proposal accepted! Viewing confirmed.',
            'viewing' => $this->formatViewing($viewing),
        ]);
    }

    // ============================================================
    // BUYER: Reject a negotiation / viewing from buyer side
    // PUT /api/buyer/viewings/{id}/reject
    // ============================================================
    public function buyerRejectViewing(Request $request, $id)
    {
        $user    = $request->user();
        $viewing = Viewing::where('buyer_id', $user->id)->findOrFail($id);

        if (!in_array($viewing->status, [Viewing::STATUS_REQUESTED, Viewing::STATUS_NEGOTIATING])) {
            return response()->json(['message' => 'Cannot reject this viewing.'], 422);
        }

        $viewing->negotiations()->where('status', ViewingNegotiation::STATUS_PENDING)
            ->update(['status' => ViewingNegotiation::STATUS_REJECTED]);

        $viewing->update([
            'status'           => Viewing::STATUS_REJECTED,
            'rejection_reason' => $request->input('rejection_reason', 'Rejected by buyer'),
        ]);

        $viewing->load(['property', 'buyer', 'agent.user']);

        try {
            broadcast(new ViewingStatusChanged($viewing));
        } catch (\Exception $e) {}

        try {
            $notification = Notification::create([
                'user_id' => $viewing->agent->user_id,
                'notification_type' => Notification::TYPE_VIEWING_REQUEST,
                'title' => 'Viewing Rejected',
                'message' => $user->name . ' rejected the viewing for ' . $viewing->property->title,
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
    // Get negotiation history for a viewing
    // GET /api/viewings/{id}/negotiations
    // ============================================================
    public function getViewingNegotiations($id)
    {
        $user    = request()->user();
        $viewing = Viewing::findOrFail($id);

        // Verify the user is either the buyer or the agent
        $isAgent = $user->agent && $viewing->agent_id === $user->agent->id;
        $isBuyer = $viewing->buyer_id === $user->id;

        if (!$isAgent && !$isBuyer) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        $negotiations = $viewing->negotiations()
            ->with('proposedBy:id,name')
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(fn ($n) => [
                'id'               => $n->id,
                'proposed_date'    => $n->proposed_date->format('Y-m-d'),
                'proposed_time'    => is_string($n->proposed_time) ? $n->proposed_time : $n->proposed_time->format('H:i'),
                'note'             => $n->note,
                'proposed_by_role' => $n->proposed_by_role,
                'proposed_by_name' => $n->proposedBy->name ?? 'Unknown',
                'status'           => $n->status,
                'created_at'       => $n->created_at->toISOString(),
            ]);

        return response()->json([
            'success'      => true,
            'negotiations' => $negotiations,
        ]);
    }

    // ============================================================
    // Private: Handle post-approval automation (emails + calendar)
    // ============================================================
    private function handleViewingApproved(Viewing $viewing): void
    {
        $viewing->loadMissing(['property', 'buyer', 'agent.user']);

        // Send confirmation emails
        try {
            Mail::to($viewing->buyer->email)->send(new ViewingConfirmation($viewing, 'buyer'));
        } catch (\Exception $e) {
            Log::warning('Viewing confirmation email to buyer failed: ' . $e->getMessage());
        }

        try {
            Mail::to($viewing->agent->user->email)->send(new ViewingConfirmation($viewing, 'agent'));
        } catch (\Exception $e) {
            Log::warning('Viewing confirmation email to agent failed: ' . $e->getMessage());
        }

        // Google Calendar integration (if connected)
        try {
            $googleService = app(\App\Services\GoogleCalendarService::class);

            Log::info('Google Calendar: Checking buyer (id:' . $viewing->buyer_id . ') token: ' . ($viewing->buyer->google_access_token ? 'YES' : 'NO'));
            Log::info('Google Calendar: Checking agent user (id:' . ($viewing->agent->user_id ?? 'N/A') . ') token: ' . (($viewing->agent->user->google_access_token ?? null) ? 'YES' : 'NO'));

            if ($viewing->buyer->google_access_token) {
                try {
                    $eventId = $googleService->createViewingEvent($viewing->buyer, $viewing);
                    Log::info('Google Calendar: Buyer event created! Event ID: ' . $eventId);
                } catch (\Exception $e) {
                    Log::warning('Google Calendar (buyer) failed: ' . $e->getMessage());
                }
            }

            $agentUser = $viewing->agent->user;
            if ($agentUser->google_access_token) {
                try {
                    $eventId = $googleService->createViewingEvent($agentUser, $viewing);
                    $viewing->update(['google_calendar_event_id' => $eventId]);
                    Log::info('Google Calendar: Agent event created! Event ID: ' . $eventId);
                } catch (\Exception $e) {
                    Log::warning('Google Calendar (agent) failed: ' . $e->getMessage());
                }
            }
        } catch (\Exception $e) {
            Log::error('Google Calendar service error: ' . $e->getMessage());
        }
    }

    // ============================================================
    // AGENT: Get calendar data for a month
    // GET /api/agent/calendar-data?month=2026-03&property_id=all
    // ============================================================
    public function getCalendarData(Request $request)
    {
        $request->validate([
            'month'       => 'required|date_format:Y-m',
            'property_id' => 'nullable|string',
        ]);

        $user  = $request->user();
        $agent = $user->agent;

        if (!$agent) {
            return response()->json(['message' => 'Agent profile not found.'], 403);
        }

        $startDate = Carbon::createFromFormat('Y-m', $request->month)->startOfMonth();
        $endDate   = $startDate->copy()->endOfMonth();

        $query = Viewing::where('agent_id', $agent->id)
            ->whereBetween('viewing_date', [$startDate, $endDate])
            ->with(['property.photos', 'buyer', 'agent.user']);

        $propertyId = $request->query('property_id', 'all');
        if ($propertyId !== 'all' && is_numeric($propertyId)) {
            $query->where('property_id', (int) $propertyId);
        }

        $viewings = $query->orderBy('viewing_date')
            ->orderBy('viewing_time')
            ->get()
            ->map(fn ($v) => $this->formatViewing($v));

        $viewingsByDate = [];
        foreach ($viewings as $v) {
            $date = $v['viewing_date'];
            if (!$date) continue;
            $viewingsByDate[$date][] = $v;
        }

        $blockedDates = AgentBlockedDate::where('agent_id', $agent->id)
            ->whereBetween('blocked_date', [$startDate, $endDate])
            ->orderBy('blocked_date')
            ->get()
            ->map(fn ($d) => [
                'id'           => $d->id,
                'blocked_date' => $d->blocked_date->format('Y-m-d'),
                'reason'       => $d->reason,
            ]);

        $properties = Property::where('agent_id', $agent->id)
            ->select('id', 'title', 'city', 'status')
            ->orderBy('title')
            ->get();

        return response()->json([
            'success'          => true,
            'viewings_by_date' => (object) $viewingsByDate,
            'blocked_dates'    => $blockedDates,
            'properties'       => $properties,
            'month'            => $request->month,
        ]);
    }

    // ============================================================
    // Helper: format a viewing for API responses
    // ============================================================
    private function formatLatestProposal(Viewing $viewing): ?array
    {
        $proposal = $viewing->negotiations()
            ->where('status', ViewingNegotiation::STATUS_PENDING)
            ->with('proposedBy:id,name')
            ->latest()
            ->first();

        if (!$proposal) {
            return null;
        }

        return [
            'id'               => $proposal->id,
            'proposed_date'    => $proposal->proposed_date->format('Y-m-d'),
            'proposed_time'    => is_string($proposal->proposed_time) ? $proposal->proposed_time : $proposal->proposed_time->format('H:i'),
            'note'             => $proposal->note,
            'proposed_by_role' => $proposal->proposed_by_role,
            'proposed_by_name' => $proposal->proposedBy->name ?? 'Unknown',
            'status'           => $proposal->status,
            'created_at'       => $proposal->created_at->toISOString(),
        ];
    }

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
            'agent_notes'      => $viewing->agent_notes,
            'rejection_reason' => $viewing->rejection_reason,
            'approved_at'      => $viewing->approved_at?->toISOString(),
            'latest_proposal'  => $this->formatLatestProposal($viewing),
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
