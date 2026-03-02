<?php

namespace App\Http\Controllers\Api;

use App\Events\MessageSent;
use App\Events\MessageRead;
use App\Events\NotificationSent;
use App\Models\Message;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MessageController extends \App\Http\Controllers\Controller
{
    /**
     * GET /api/messages
     * Get all conversations for current user (grouped by other user)
     */
    public function getConversations(Request $request)
    {
        try {
            $userId = Auth::id();
            
            // Get all messages where user is sender or recipient
            $conversations = Message::where('sender_id', $userId)
                ->orWhere('recipient_id', $userId)
                ->with(['sender', 'recipient', 'property'])
                ->get()
                ->groupBy(function ($message) use ($userId) {
                    // Group by the OTHER user
                    return $message->sender_id === $userId ? $message->recipient_id : $message->sender_id;
                });

            $result = [];
            foreach ($conversations as $otherId => $messages) {
                $lastMessage = $messages->sortByDesc('created_at')->first();
                $otherUser = User::find($otherId);
                $unreadCount = $messages->where('is_read', false)
                    ->where('recipient_id', $userId)
                    ->count();

                $result[] = [
                    'buyer' => $otherUser ? array_merge($otherUser->toArray(), [
                        'profile_photo_url' => $otherUser->profile_photo_path
                            ? url('/storage/' . $otherUser->profile_photo_path)
                            : null,
                        'agent_id' => $otherUser->agent?->id,
                        'last_seen_at' => $otherUser->last_seen_at?->toISOString(),
                    ]) : null,
                    'last_message' => $lastMessage,
                    'unread_count' => $unreadCount,
                    'created_at' => $lastMessage->created_at,
                ];
            }

            // Sort by most recent
            usort($result, function ($a, $b) {
                return $b['created_at']->timestamp <=> $a['created_at']->timestamp;
            });

            return response()->json([
                'success' => true,
                'data' => $result,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * GET /api/conversations/{userId}/messages
     * Get conversation between current user and specified user
     */
    public function getConversation($userId, Request $request)
    {
        try {
            $currentUserId = Auth::id();
            $propertyId = $request->query('property_id');

            // ✅ FIX: Build query to get messages between two users
            $query = Message::where(function ($q) use ($currentUserId, $userId) {
                $q->where('sender_id', $currentUserId)
                  ->where('recipient_id', $userId);
            })->orWhere(function ($q) use ($currentUserId, $userId) {
                $q->where('sender_id', $userId)
                  ->where('recipient_id', $currentUserId);
            });

            // Filter by property if provided
            if ($propertyId) {
                $query->where('property_id', $propertyId);
            }

            $messages = $query->with(['sender', 'recipient', 'property'])
                ->orderBy('created_at', 'ASC')
                ->get();

            // ✅ FIX: Get the OTHER user (not current user)
            $otherUser = User::find($userId);

            if (!$otherUser) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'messages' => $messages->map(function ($m) {
                        return array_merge($m->toArray(), [
                            'sender_photo_url' => $m->sender && $m->sender->profile_photo_path
                                ? url('/storage/' . $m->sender->profile_photo_path)
                                : null,
                        ]);
                    }),
                    'otherUser' => array_merge($otherUser->toArray(), [
                        'profile_photo_url' => $otherUser->profile_photo_path
                            ? url('/storage/' . $otherUser->profile_photo_path)
                            : null,
                        'agent_id' => $otherUser->agent?->id,
                        'last_seen_at' => $otherUser->last_seen_at?->toISOString(),
                    ]),
                    'currentUserId' => $currentUserId,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * POST /api/messages
     * Send a message
     */
    public function send(Request $request)
    {
        try {
            $validated = $request->validate([
                'recipient_id'   => 'required|exists:users,id',
                'message_content'=> 'nullable|string|max:5000',
                'property_id'    => 'nullable|integer|exists:properties,id',
                'attachment'     => 'nullable|file|max:20480',
            ]);

            $senderId = Auth::id();

            if ($validated['recipient_id'] == $senderId) {
                return response()->json(['success' => false, 'message' => 'Cannot send message to yourself'], 422);
            }

            if (empty($validated['message_content']) && !$request->hasFile('attachment')) {
                return response()->json(['success' => false, 'message' => 'Message or attachment is required'], 422);
            }

            $attachmentPath = null;
            $attachmentName = null;
            $attachmentType = null;

            if ($request->hasFile('attachment')) {
                $file = $request->file('attachment');
                $attachmentName = $file->getClientOriginalName();
                $attachmentType = $file->getMimeType();
                $attachmentPath = $file->store("messages/{$senderId}", 'public');
            }

            $message = Message::create([
                'sender_id'       => $senderId,
                'recipient_id'    => $validated['recipient_id'],
                'message_content' => $validated['message_content'] ?? '',
                'property_id'     => $validated['property_id'] ?? null,
                'attachment_path' => $attachmentPath,
                'attachment_name' => $attachmentName,
                'attachment_type' => $attachmentType,
                'is_read'         => false,
            ]);

            $message->load(['sender', 'recipient', 'property']);

            // Broadcast to the recipient in real-time
            try {
                broadcast(new MessageSent($message))->toOthers();
            } catch (\Exception $e) {
                // Reverb not running — continue silently
            }

            // Create notification for the recipient
            try {
                $senderName = Auth::user()->name;
                $preview = mb_substr($validated['message_content'] ?? 'Sent an attachment', 0, 60);
                $notification = Notification::create([
                    'user_id' => $validated['recipient_id'],
                    'notification_type' => Notification::TYPE_MESSAGE,
                    'title' => 'New Message from ' . $senderName,
                    'message' => $preview,
                    'related_model_type' => 'Message',
                    'related_model_id' => $message->id,
                ]);
                broadcast(new NotificationSent($notification));
            } catch (\Exception $e) { /* silent */ }

            return response()->json([
                'success' => true,
                'data' => $message,
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * POST /api/conversations/{userId}/mark-read
     * Mark all messages in a conversation as read
     */
    public function markConversationRead($userId)
    {
        try {
            $currentUserId = Auth::id();

            // Get IDs of unread messages from this sender before marking them
            $unreadMessageIds = Message::where('sender_id', $userId)
                ->where('recipient_id', $currentUserId)
                ->where('is_read', false)
                ->pluck('id');

            $updated = Message::whereIn('id', $unreadMessageIds)
                ->update(['is_read' => true, 'read_at' => now()]);

            // Also mark related notifications as read
            if ($unreadMessageIds->isNotEmpty()) {
                Notification::where('user_id', $currentUserId)
                    ->where('notification_type', 'message')
                    ->where('related_model_type', 'Message')
                    ->whereIn('related_model_id', $unreadMessageIds)
                    ->where('is_read', false)
                    ->update(['is_read' => true, 'read_at' => now()]);
            }

            // Notify the sender in real-time that their messages have been seen
            if ($updated > 0) {
                try {
                    broadcast(new MessageRead($currentUserId, (int) $userId))->toOthers();
                } catch (\Exception $e) {
                    // Reverb not running — continue silently
                }
            }

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * POST /api/user/heartbeat
     * Update user's last_seen_at timestamp
     */
    public function heartbeat()
    {
        try {
            $user = Auth::user();
            $user->update(['last_seen_at' => now()]);

            return response()->json(['success' => true, 'last_seen_at' => now()->toISOString()]);
        } catch (\Exception $e) {
            return response()->json(['success' => false], 500);
        }
    }

    /**
     * PUT /api/messages/{id}/read
     * Mark a message as read
     */
    public function markAsRead($messageId)
    {
        try {
            $message = Message::find($messageId);

            if (!$message) {
                return response()->json([
                    'success' => false,
                    'message' => 'Message not found',
                ], 404);
            }

            // Verify recipient
            if ($message->recipient_id !== Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized',
                ], 403);
            }

            $message->markAsRead();

            return response()->json([
                'success' => true,
                'data' => $message,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * GET /api/messages/unread-count
     * Get count of unread messages
     */
    public function getUnreadCount()
    {
        try {
            $unreadCount = Message::where('recipient_id', Auth::id())
                ->where('is_read', false)
                ->count();

            return response()->json([
                'success' => true,
                'unread_count' => $unreadCount,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * GET /api/agent/conversations
     * Get all conversations for an agent (grouped by buyer)
     */
    public function getAgentConversations(Request $request)
    {
        try {
            $user = Auth::user();

            // Check if user is agent
            if ($user->role !== 'agent') {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized: Only agents can access this endpoint',
                ], 403);
            }

            $agentId = $user->id;

            // Get all messages where agent is sender or recipient
            $conversations = Message::where('sender_id', $agentId)
                ->orWhere('recipient_id', $agentId)
                ->with(['sender', 'recipient', 'property'])
                ->get()
                ->groupBy(function ($message) use ($agentId) {
                    // Group by the OTHER user (buyer)
                    return $message->sender_id === $agentId ? $message->recipient_id : $message->sender_id;
                });

            $result = [];
            foreach ($conversations as $buyerId => $messages) {
                $lastMessage = $messages->sortByDesc('created_at')->first();
                $buyer = User::find($buyerId);
                $unreadCount = $messages->where('is_read', false)
                    ->where('recipient_id', $agentId)
                    ->count();

                $result[] = [
                    'buyer' => $buyer ? array_merge($buyer->toArray(), [
                        'profile_photo_url' => $buyer->profile_photo_path
                            ? url('/storage/' . $buyer->profile_photo_path)
                            : null,
                        'last_seen_at' => $buyer->last_seen_at?->toISOString(),
                    ]) : null,
                    'last_message' => $lastMessage,
                    'unread_count' => $unreadCount,
                    'created_at' => $lastMessage->created_at,
                ];
            }

            // Sort by most recent
            usort($result, function ($a, $b) {
                return $b['created_at']->timestamp <=> $a['created_at']->timestamp;
            });

            return response()->json([
                'success' => true,
                'data' => $result,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * GET /api/buyer/conversations
     * Get all conversations for a buyer (grouped by agent)
     */
    public function getBuyerConversations(Request $request)
    {
        try {
            $user = Auth::user();

            // Check if user is buyer
            if ($user->role !== 'buyer') {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized: Only buyers can access this endpoint',
                ], 403);
            }

            $buyerId = $user->id;

            // Get all messages where buyer is sender or recipient
            $conversations = Message::where('sender_id', $buyerId)
                ->orWhere('recipient_id', $buyerId)
                ->with(['sender', 'recipient', 'property'])
                ->get()
                ->groupBy(function ($message) use ($buyerId) {
                    // Group by the OTHER user (agent)
                    return $message->sender_id === $buyerId ? $message->recipient_id : $message->sender_id;
                });

            $result = [];
            foreach ($conversations as $agentId => $messages) {
                $lastMessage = $messages->sortByDesc('created_at')->first();
                $agent = User::find($agentId);
                $unreadCount = $messages->where('is_read', false)
                    ->where('recipient_id', $buyerId)
                    ->count();

                $result[] = [
                    'agent' => $agent ? array_merge($agent->toArray(), [
                        'profile_photo_url' => $agent->profile_photo_path
                            ? url('/storage/' . $agent->profile_photo_path)
                            : null,
                        'last_seen_at' => $agent->last_seen_at?->toISOString(),
                    ]) : null,
                    'last_message' => $lastMessage,
                    'unread_count' => $unreadCount,
                    'created_at' => $lastMessage->created_at,
                ];
            }

            // Sort by most recent
            usort($result, function ($a, $b) {
                return $b['created_at']->timestamp <=> $a['created_at']->timestamp;
            });

            return response()->json([
                'success' => true,
                'data' => $result,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * POST /api/buyer/messages or /api/agent/messages
     * Alias for send() method
     */
    public function sendMessage(Request $request)
    {
        return $this->send($request);
    }
}