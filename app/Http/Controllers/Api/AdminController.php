<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Agent;
use App\Models\Property;
use App\Models\PropertyPhoto;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class AdminController extends Controller
{
    /**
     * ============================================================================
     * USER MANAGEMENT
     * ============================================================================
     */

    /**
     * Get all users with filtering
     */
    public function users(Request $request)
    {
        try {
            $query = User::query();

            // Filter by role
            if ($request->has('role') && $request->role) {
                $query->where('role', $request->role);
            }

            // Filter by status
            if ($request->has('status') && $request->status) {
                $query->where('status', $request->status);
            }

            // Search by name or email
            if ($request->has('search') && $request->search) {
                $query->where('name', 'like', "%{$request->search}%")
                      ->orWhere('email', 'like', "%{$request->search}%");
            }

            $users = $query->paginate($request->get('per_page', 15));

            return response()->json([
                'success' => true,
                'data' => $users,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch users',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get user detail
     */
    public function userDetail($id)
    {
        try {
            $user = User::with('agent')->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $user,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'User not found',
                'error' => $e->getMessage(),
            ], 404);
        }
    }

    /**
     * Update user
     */
    public function updateUser(Request $request, $id)
    {
        try {
            $user = User::findOrFail($id);

            $validated = $request->validate([
                'name' => 'sometimes|string|max:255',
                'email' => 'sometimes|email|unique:users,email,' . $id,
                'phone' => 'sometimes|string|max:20',
                'role' => 'sometimes|in:buyer,agent,admin',
                'status' => 'sometimes|in:active,suspended,inactive',
            ]);

            $user->update($validated);

            // Log action
            AuditLog::create([
                'user_id' => auth()->id(),
                'action' => 'user_updated',
                'action_type' => 'update',
                'model_type' => 'User',
                'model_id' => $user->id,
                'description' => "User {$user->name} updated by admin",
                'ip_address' => $request->ip(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'User updated successfully',
                'data' => $user,
            ], 200);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update user',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete user
     */
    public function deleteUser($id)
    {
        try {
            $user = User::findOrFail($id);

            // Can't delete self
            if ($user->id === auth()->id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete your own account',
                ], 403);
            }

            $user->delete();

            AuditLog::create([
                'user_id' => auth()->id(),
                'action' => 'user_deleted',
                'action_type' => 'delete',
                'model_type' => 'User',
                'model_id' => $user->id,
                'description' => "User {$user->name} deleted",
                'ip_address' => request()->ip(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'User deleted successfully',
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete user',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Suspend user account
     */
    public function suspendUser($id)
    {
        try {
            $user = User::findOrFail($id);

            // Can't suspend self
            if ($user->id === auth()->id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot suspend your own account',
                ], 403);
            }

            $user->suspend();

            AuditLog::create([
                'user_id' => auth()->id(),
                'action' => 'user_suspended',
                'action_type' => 'update',
                'model_type' => 'User',
                'model_id' => $user->id,
                'description' => "User {$user->name} suspended",
                'ip_address' => request()->ip(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'User suspended successfully',
                'data' => $user,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to suspend user',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Activate user account
     */
    public function activateUser($id)
    {
        try {
            $user = User::findOrFail($id);

            $user->activate();

            AuditLog::create([
                'user_id' => auth()->id(),
                'action' => 'user_activated',
                'action_type' => 'update',
                'model_type' => 'User',
                'model_id' => $user->id,
                'description' => "User {$user->name} activated",
                'ip_address' => request()->ip(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'User activated successfully',
                'data' => $user,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to activate user',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * ============================================================================
     * AGENT VERIFICATION
     * ============================================================================
     */

    /**
     * Get pending agents for verification
     */
    public function pendingAgents(Request $request)
    {
        try {
            $agents = Agent::where('verification_status', Agent::VERIFICATION_PENDING)
                ->with('user')
                ->paginate($request->get('per_page', 15));

            return response()->json([
                'success' => true,
                'data' => $agents,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch pending agents',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get agent detail
     */
    public function agentDetail($id)
    {
        try {
            $agent = Agent::with('user', 'properties', 'reviews')->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $agent,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Agent not found',
                'error' => $e->getMessage(),
            ], 404);
        }
    }

    /**
     * Verify agent
     */
    public function verifyAgent(Request $request, $id)
    {
        try {
            $agent = Agent::findOrFail($id);

            if ($agent->isVerified()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Agent is already verified',
                ], 400);
            }

            $agent->verify(auth()->id());

            return response()->json([
                'success' => true,
                'message' => 'Agent verified successfully',
                'data' => $agent,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to verify agent',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Reject agent verification
     */
    public function rejectAgent(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'rejection_reason' => 'required|string|max:500',
            ]);

            $agent = Agent::findOrFail($id);

            if ($agent->isRejected()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Agent verification is already rejected',
                ], 400);
            }

            $agent->reject(auth()->id(), $validated['rejection_reason']);

            return response()->json([
                'success' => true,
                'message' => 'Agent verification rejected',
                'data' => $agent,
            ], 200);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to reject agent',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get all agents
     */
    public function agents(Request $request)
    {
        try {
            $query = Agent::with('user');

            // Filter by verification status
            if ($request->has('verification_status')) {
                $query->where('verification_status', $request->verification_status);
            }

            $agents = $query->paginate($request->get('per_page', 15));

            return response()->json([
                'success' => true,
                'data' => $agents,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch agents',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * ============================================================================
     * PROPERTY MANAGEMENT
     * ============================================================================
     */

    /**
     * Get all properties for moderation
     */
    public function properties(Request $request)
    {
        try {
            $query = Property::with('agent', 'photos');

            // Filter by status
            if ($request->has('status')) {
                $query->where('status', $request->status);
            }

            // Filter by city
            if ($request->has('city')) {
                $query->where('city', $request->city);
            }

            $properties = $query->paginate($request->get('per_page', 15));

            return response()->json([
                'success' => true,
                'data' => $properties,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch properties',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get property detail
     */
    public function propertyDetail($id)
    {
        try {
            $property = Property::with('agent', 'photos', 'viewings')->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $property,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Property not found',
                'error' => $e->getMessage(),
            ], 404);
        }
    }

    /**
     * Delete property (admin moderation)
     */
    public function deleteProperty($id)
    {
        try {
            $property = Property::findOrFail($id);

            $propertyTitle = $property->title;
            $agentName = $property->agent->user->name;

            $property->delete();

            AuditLog::create([
                'user_id' => auth()->id(),
                'action' => 'property_deleted_by_admin',
                'action_type' => 'delete',
                'model_type' => 'Property',
                'model_id' => $property->id,
                'description' => "Property '{$propertyTitle}' by {$agentName} deleted by admin",
                'ip_address' => request()->ip(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Property deleted successfully',
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete property',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Feature property
     */
    public function featureProperty($id)
    {
        try {
            $property = Property::findOrFail($id);

            $property->update(['featured' => true]);

            AuditLog::create([
                'user_id' => auth()->id(),
                'action' => 'property_featured',
                'action_type' => 'update',
                'model_type' => 'Property',
                'model_id' => $property->id,
                'description' => "Property {$property->title} featured",
                'ip_address' => request()->ip(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Property featured successfully',
                'data' => $property,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to feature property',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Unfeature property
     */
    public function unfeatureProperty($id)
    {
        try {
            $property = Property::findOrFail($id);

            $property->update(['featured' => false]);

            AuditLog::create([
                'user_id' => auth()->id(),
                'action' => 'property_unfeatured',
                'action_type' => 'update',
                'model_type' => 'Property',
                'model_id' => $property->id,
                'description' => "Property {$property->title} unfeatured",
                'ip_address' => request()->ip(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Property unfeatured successfully',
                'data' => $property,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to unfeature property',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * ============================================================================
     * PHOTO QUALITY REVIEW
     * ============================================================================
     */

    /**
     * Get pending photos for approval
     */
    public function pendingPhotos(Request $request)
    {
        try {
            $photos = PropertyPhoto::where('approval_status', PropertyPhoto::STATUS_PENDING)
                ->with('property')
                ->paginate($request->get('per_page', 15));

            return response()->json([
                'success' => true,
                'data' => $photos,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch pending photos',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Approve photo
     */
    public function approvePhoto($id)
    {
        try {
            $photo = PropertyPhoto::findOrFail($id);

            $photo->approve();

            AuditLog::create([
                'user_id' => auth()->id(),
                'action' => 'photo_approved',
                'action_type' => 'approve',
                'model_type' => 'PropertyPhoto',
                'model_id' => $photo->id,
                'description' => "Photo for property {$photo->property->title} approved",
                'ip_address' => request()->ip(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Photo approved successfully',
                'data' => $photo,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to approve photo',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Reject photo
     */
    public function rejectPhoto($id)
    {
        try {
            $photo = PropertyPhoto::findOrFail($id);

            $photo->reject();

            AuditLog::create([
                'user_id' => auth()->id(),
                'action' => 'photo_rejected',
                'action_type' => 'reject',
                'model_type' => 'PropertyPhoto',
                'model_id' => $photo->id,
                'description' => "Photo for property {$photo->property->title} rejected",
                'ip_address' => request()->ip(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Photo rejected successfully',
                'data' => $photo,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to reject photo',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * ============================================================================
     * AUDIT & ANALYTICS
     * ============================================================================
     */

    /**
     * Get audit logs
     */
    public function auditLogs(Request $request)
    {
        try {
            $query = AuditLog::with('user');

            // Filter by user
            if ($request->has('user_id')) {
                $query->where('user_id', $request->user_id);
            }

            // Filter by action type
            if ($request->has('action_type')) {
                $query->where('action_type', $request->action_type);
            }

            // Filter by date range
            if ($request->has('start_date') && $request->has('end_date')) {
                $query->whereBetween('created_at', [
                    $request->start_date,
                    $request->end_date,
                ]);
            }

            $logs = $query->latest()->paginate($request->get('per_page', 20));

            return response()->json([
                'success' => true,
                'data' => $logs,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch audit logs',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get audit log detail
     */
    public function auditLogDetail($id)
    {
        try {
            $log = AuditLog::with('user')->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $log,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Audit log not found',
                'error' => $e->getMessage(),
            ], 404);
        }
    }

    /**
     * Get dashboard overview
     */
    public function dashboardOverview()
    {
        try {
            $totalUsers = User::count();
            $totalAgents = Agent::count();
            $verifiedAgents = Agent::where('verification_status', Agent::VERIFICATION_VERIFIED)->count();
            $totalProperties = Property::count();
            $activeProperties = Property::where('status', Property::STATUS_AVAILABLE)->count();
            $totalPendingAgents = Agent::where('verification_status', Agent::VERIFICATION_PENDING)->count();
            $totalPendingPhotos = PropertyPhoto::where('approval_status', PropertyPhoto::STATUS_PENDING)->count();

            return response()->json([
                'success' => true,
                'data' => [
                    'total_users' => $totalUsers,
                    'total_agents' => $totalAgents,
                    'verified_agents' => $verifiedAgents,
                    'pending_agents' => $totalPendingAgents,
                    'total_properties' => $totalProperties,
                    'active_properties' => $activeProperties,
                    'pending_photos' => $totalPendingPhotos,
                    'users_by_role' => [
                        'buyers' => User::where('role', 'buyer')->count(),
                        'agents' => User::where('role', 'agent')->count(),
                        'admins' => User::where('role', 'admin')->count(),
                    ],
                ],
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch dashboard data',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get dashboard stats
     */
    public function dashboardStats(Request $request)
    {
        try {
            $days = $request->get('days', 30);

            // Users registered in period
            $usersRegistered = User::where('created_at', '>=', now()->subDays($days))->count();

            // Properties added in period
            $propertiesAdded = Property::where('created_at', '>=', now()->subDays($days))->count();

            // Messages in period
            $messagesCount = \App\Models\Message::where('created_at', '>=', now()->subDays($days))->count();

            // Viewings requested in period
            $viewingsCount = \App\Models\Viewing::where('created_at', '>=', now()->subDays($days))->count();

            return response()->json([
                'success' => true,
                'data' => [
                    'period_days' => $days,
                    'users_registered' => $usersRegistered,
                    'properties_added' => $propertiesAdded,
                    'messages_sent' => $messagesCount,
                    'viewings_requested' => $viewingsCount,
                    'period_from' => now()->subDays($days)->toDateString(),
                    'period_to' => now()->toDateString(),
                ],
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch stats',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get recent activity
     */
    public function recentActivity(Request $request)
    {
        try {
            $limit = $request->get('limit', 10);

            $logs = AuditLog::with('user')
                ->latest()
                ->limit($limit)
                ->get();

            return response()->json([
                'success' => true,
                'data' => $logs,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch recent activity',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}