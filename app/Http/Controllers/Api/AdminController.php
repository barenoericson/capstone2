<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Agent;
use App\Models\AgentApplication;
use App\Models\Property;
use App\Models\PropertyPhoto;
use App\Models\PropertyFlag;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
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
     * Get pending agent applications for verification
     */
    public function pendingAgents(Request $request)
    {
        try {
            $applications = AgentApplication::pending()
                ->with('user')
                ->orderBy('applied_at', 'desc')
                ->paginate($request->get('per_page', 15));

            return response()->json([
                'success' => true,
                'data' => $applications,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch pending applications',
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
     * Approve agent application (creates Agent record + updates user role)
     */
    public function verifyAgent(Request $request, $id)
    {
        try {
            $application = AgentApplication::with('user')->findOrFail($id);

            if ($application->status === 'approved') {
                return response()->json([
                    'success' => false,
                    'message' => 'Application is already approved',
                ], 400);
            }

            // Approve the application
            $application->approve();

            // Create Agent record if not exists
            $existingAgent = Agent::where('user_id', $application->user_id)->first();
            if (!$existingAgent) {
                Agent::create([
                    'user_id'                    => $application->user_id,
                    'real_estate_license_number'  => $application->license_number,
                    'prc_id_number'              => $application->prc_id,
                    'accreditation'              => $application->accreditation,
                    'company_name'               => $application->company_name,
                    'company_address'            => $application->company_address,
                    'bio'                        => $application->bio,
                    'verification_status'        => 'verified',
                    'verified_at'                => now(),
                    'verified_by_admin_id'       => auth()->id(),
                    'license_expiry_date'        => $application->license_expiry_date,
                ]);
            }

            AuditLog::create([
                'user_id'     => auth()->id(),
                'action'      => 'agent_application_approved',
                'action_type' => 'verify',
                'model_type'  => 'AgentApplication',
                'model_id'    => $application->id,
                'description' => "Agent application by {$application->user->name} approved by admin",
                'ip_address'  => $request->ip(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Agent application approved successfully',
                'data' => $application,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to approve application',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Reject agent application
     */
    public function rejectAgent(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'rejection_reason' => 'required|string|max:500',
            ]);

            $application = AgentApplication::with('user')->findOrFail($id);

            if ($application->status === 'rejected') {
                return response()->json([
                    'success' => false,
                    'message' => 'Application is already rejected',
                ], 400);
            }

            $application->reject($validated['rejection_reason']);

            AuditLog::create([
                'user_id'     => auth()->id(),
                'action'      => 'agent_application_rejected',
                'action_type' => 'reject',
                'model_type'  => 'AgentApplication',
                'model_id'    => $application->id,
                'description' => "Agent application by {$application->user->name} rejected by admin",
                'ip_address'  => $request->ip(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Agent application rejected',
                'data' => $application,
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
                'message' => 'Failed to reject application',
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

            $flaggedProperties = PropertyFlag::where('status', PropertyFlag::STATUS_PENDING)->count();

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
                    'flagged_properties' => $flaggedProperties,
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

    /**
     * ============================================================================
     * CREATE ADMIN
     * ============================================================================
     */

    /**
     * Create a new admin user account
     */
    public function createAdmin(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|string|min:8|confirmed',
                'phone' => 'nullable|string|max:20',
            ]);

            $admin = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'phone' => $validated['phone'] ?? null,
                'role' => 'admin',
                'status' => 'active',
            ]);

            AuditLog::create([
                'user_id' => auth()->id(),
                'action' => 'admin_created',
                'action_type' => 'create',
                'model_type' => 'User',
                'model_id' => $admin->id,
                'description' => "New admin account created: {$admin->name} ({$admin->email})",
                'ip_address' => $request->ip(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Admin account created successfully',
                'data' => $admin,
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create admin',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * ============================================================================
     * FLAGGED PROPERTIES
     * ============================================================================
     */

    /**
     * Get flagged properties
     */
    public function getFlaggedProperties(Request $request)
    {
        try {
            $query = PropertyFlag::with(['property.agent.user', 'property.photos', 'reviewer'])
                ->orderBy('flagged_at', 'desc');

            if ($request->has('status') && $request->status) {
                $query->where('status', $request->status);
            }

            $flags = $query->paginate($request->get('per_page', 15));

            return response()->json(['success' => true, 'data' => $flags], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch flagged properties',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Review a flagged property (approve = clear flag, dismiss = take action)
     */
    public function reviewFlaggedProperty(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'action' => 'required|in:approve,dismiss',
                'admin_notes' => 'nullable|string|max:1000',
                'suspend_agent' => 'nullable|boolean',
                'delete_property' => 'nullable|boolean',
            ]);

            $flag = PropertyFlag::with('property.agent.user')->findOrFail($id);

            $flag->update([
                'status' => $validated['action'] === 'approve'
                    ? PropertyFlag::STATUS_APPROVED
                    : PropertyFlag::STATUS_DISMISSED,
                'reviewed_at' => now(),
                'reviewed_by' => auth()->id(),
                'admin_notes' => $validated['admin_notes'] ?? null,
            ]);

            // Optional: suspend agent
            if ($validated['action'] === 'dismiss' && !empty($validated['suspend_agent'])) {
                $agentUser = $flag->property->agent->user ?? null;
                if ($agentUser) {
                    $agentUser->suspend();
                    AuditLog::create([
                        'user_id' => auth()->id(),
                        'action' => 'user_suspended_via_flag',
                        'action_type' => 'update',
                        'model_type' => 'User',
                        'model_id' => $agentUser->id,
                        'description' => "Agent {$agentUser->name} suspended due to flagged property",
                        'ip_address' => $request->ip(),
                    ]);
                }
            }

            // Optional: delete property
            if ($validated['action'] === 'dismiss' && !empty($validated['delete_property'])) {
                $property = $flag->property;
                if ($property) {
                    AuditLog::create([
                        'user_id' => auth()->id(),
                        'action' => 'property_deleted_via_flag',
                        'action_type' => 'delete',
                        'model_type' => 'Property',
                        'model_id' => $property->id,
                        'description' => "Property '{$property->title}' deleted due to AI flag",
                        'ip_address' => $request->ip(),
                    ]);
                    $property->delete();
                }
            }

            AuditLog::create([
                'user_id' => auth()->id(),
                'action' => 'flag_reviewed',
                'action_type' => $validated['action'] === 'approve' ? 'approve' : 'reject',
                'model_type' => 'PropertyFlag',
                'model_id' => $flag->id,
                'description' => "Property flag #{$flag->id} " . ($validated['action'] === 'approve' ? 'cleared' : 'actioned'),
                'ip_address' => $request->ip(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Flag reviewed successfully',
                'data' => $flag,
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
                'message' => 'Failed to review flag',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * ============================================================================
     * ANALYTICS
     * ============================================================================
     */

    /**
     * Get analytics data with trends
     */
    public function analyticsData(Request $request)
    {
        try {
            $period = (int) $request->get('period', 30);
            if (!in_array($period, [7, 30, 90])) {
                $period = 30;
            }

            // User registration trend
            $userTrend = User::selectRaw('DATE(created_at) as date, COUNT(*) as count')
                ->where('created_at', '>=', now()->subDays($period))
                ->groupByRaw('DATE(created_at)')
                ->orderBy('date')
                ->get()
                ->keyBy('date');

            // Property listing trend
            $propertyTrend = Property::selectRaw('DATE(created_at) as date, COUNT(*) as count')
                ->where('created_at', '>=', now()->subDays($period))
                ->groupByRaw('DATE(created_at)')
                ->orderBy('date')
                ->get()
                ->keyBy('date');

            // Top 5 cities by property count
            $topCities = Property::selectRaw('city, COUNT(*) as count')
                ->whereNotNull('city')
                ->where('city', '!=', '')
                ->groupBy('city')
                ->orderByDesc('count')
                ->limit(5)
                ->get();

            // Top 5 agents by listing count
            $topAgents = Agent::with('user')
                ->withCount('properties')
                ->orderByDesc('properties_count')
                ->limit(5)
                ->get()
                ->map(fn($a) => [
                    'name' => $a->user->name ?? 'Unknown',
                    'properties_count' => $a->properties_count,
                    'average_rating' => $a->average_rating ?? null,
                ]);

            // Build date-labeled arrays
            $dates = collect(range(0, $period - 1))
                ->map(fn($i) => now()->subDays($period - 1 - $i)->toDateString());

            $userTrendArray = $dates->map(fn($d) => [
                'date' => $d,
                'count' => (int) ($userTrend[$d]->count ?? 0),
            ])->values();

            $propertyTrendArray = $dates->map(fn($d) => [
                'date' => $d,
                'count' => (int) ($propertyTrend[$d]->count ?? 0),
            ])->values();

            return response()->json([
                'success' => true,
                'data' => [
                    'period' => $period,
                    'user_trend' => $userTrendArray,
                    'property_trend' => $propertyTrendArray,
                    'top_cities' => $topCities,
                    'top_agents' => $topAgents,
                ],
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch analytics',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}