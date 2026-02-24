<?php

namespace App\Http\Controllers\Api;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends \App\Http\Controllers\Controller
{
    /**
     * GET /api/notifications
     * Get all notifications for the current user
     */
    public function index(Request $request)
    {
        try {
            $userId = Auth::id();

            $notifications = Notification::where('user_id', $userId)
                ->orderBy('created_at', 'desc')
                ->limit(50)
                ->get()
                ->map(function ($n) {
                    return [
                        'id' => $n->id,
                        'notification_type' => $n->notification_type,
                        'title' => $n->title,
                        'message' => $n->message,
                        'related_model_type' => $n->related_model_type,
                        'related_model_id' => $n->related_model_id,
                        'is_read' => $n->is_read,
                        'read_at' => $n->read_at?->toISOString(),
                        'created_at' => $n->created_at->toISOString(),
                        'icon' => $n->icon,
                        'type_display' => $n->type_display,
                        'relative_time' => $n->relative_time,
                    ];
                });

            return response()->json([
                'success' => true,
                'notifications' => $notifications,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * PUT /api/notifications/{id}/read
     * Mark a single notification as read
     */
    public function markAsRead($id)
    {
        try {
            $notification = Notification::where('id', $id)
                ->where('user_id', Auth::id())
                ->firstOrFail();

            $notification->markAsRead();

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 404);
        }
    }

    /**
     * PUT /api/notifications/mark-all-read
     * Mark all notifications as read
     */
    public function markAllAsRead(Request $request)
    {
        try {
            Notification::where('user_id', Auth::id())
                ->where('is_read', false)
                ->update(['is_read' => true, 'read_at' => now()]);

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * DELETE /api/notifications/{id}
     * Delete a notification
     */
    public function delete($id)
    {
        try {
            $notification = Notification::where('id', $id)
                ->where('user_id', Auth::id())
                ->firstOrFail();

            $notification->delete();

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 404);
        }
    }

    /**
     * GET /api/notifications/unread-count
     * Get count of unread notifications
     */
    public function getUnreadCount(Request $request)
    {
        try {
            $count = Notification::where('user_id', Auth::id())
                ->where('is_read', false)
                ->count();

            return response()->json([
                'success' => true,
                'unread_count' => $count,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
