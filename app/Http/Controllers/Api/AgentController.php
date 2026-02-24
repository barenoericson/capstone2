<?php

namespace App\Http\Controllers\Api;

use App\Models\Agent;
use App\Models\AgentApplication;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Validator;

class AgentController extends BaseController
{
    /**
     * Get agent profile by ID
     */
    public function show($id)
    {
        try {
            $agent = Agent::find($id);
            return response()->json(['agent' => $agent], $agent ? 200 : 404);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Get current authenticated agent's profile
     */
    public function getMyProfile(Request $request)
    {
        try {
            $user = $request->user();
            if (!$user || $user->role !== 'agent') {
                return response()->json(['message' => 'Not an agent'], 403);
            }

            $agent = Agent::where('user_id', $user->id)->first();
            return response()->json(['profile' => $agent], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Update agent profile
     */
    public function updateProfile(Request $request)
    {
        return response()->json(['message' => 'Not implemented'], 501);
    }

    /**
     * Apply to become an agent
     */
    public function applyAsAgent(Request $request)
    {
        try {
            $user = $request->user();

            if (!$user) {
                return response()->json(['message' => 'User not authenticated'], 401);
            }

            // Validate input
            $validator = Validator::make($request->all(), [
                'license_number' => 'required|string|unique:agent_applications,license_number',
                'prc_id' => 'required|string|unique:agent_applications,prc_id',
                'company_name' => 'sometimes|string|max:255',
                'bio' => 'sometimes|string|max:1000',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Validation failed',
                    'errors' => $validator->errors(),
                ], 422);
            }

            // Check if user already applied
            $existing = AgentApplication::where('user_id', $user->id)
                ->where('status', '!=', 'rejected')
                ->first();

            if ($existing) {
                return response()->json([
                    'message' => 'You already have a pending application',
                ], 409);
            }

            // Create application
            $application = AgentApplication::create([
                'user_id' => $user->id,
                'license_number' => $request->license_number,
                'prc_id' => $request->prc_id,
                'company_name' => $request->company_name,
                'bio' => $request->bio,
                'status' => 'pending',
                'applied_at' => now(),
            ]);

            return response()->json([
                'message' => 'Application submitted successfully',
                'application' => $application,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to submit application',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get agent's application status
     */
    public function getApplicationStatus(Request $request)
    {
        try {
            $user = $request->user();

            if (!$user) {
                return response()->json(['message' => 'User not authenticated'], 401);
            }

            $application = AgentApplication::where('user_id', $user->id)->first();

            if (!$application) {
                return response()->json([
                    'message' => 'No application found',
                    'hasApplication' => false,
                ], 404);
            }

            return response()->json([
                'message' => 'Application found',
                'application' => $application,
                'hasApplication' => true,
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // ==================== ADMIN METHODS ====================

    /**
     * ADMIN: Get all pending applications
     */
    public function getPendingApplications(Request $request)
    {
        try {
            // Check if admin
            if ($request->user()->role !== 'admin') {
                return response()->json(['message' => 'Unauthorized'], 403);
            }

            $applications = AgentApplication::pending()
                ->with('user')
                ->orderBy('applied_at', 'desc')
                ->get();

            return response()->json([
                'message' => 'Pending applications retrieved',
                'applications' => $applications,
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * ADMIN: Approve agent application
     */
    public function approveApplication(Request $request, $applicationId)
    {
        try {
            // Check if admin
            if ($request->user()->role !== 'admin') {
                return response()->json(['message' => 'Unauthorized'], 403);
            }

            $application = AgentApplication::find($applicationId);

            if (!$application) {
                return response()->json(['message' => 'Application not found'], 404);
            }

            // Approve the application
            $application->approve();

            return response()->json([
                'message' => 'Agent approved successfully',
                'application' => $application,
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * ADMIN: Reject agent application
     */
    public function rejectApplication(Request $request, $applicationId)
    {
        try {
            // Check if admin
            if ($request->user()->role !== 'admin') {
                return response()->json(['message' => 'Unauthorized'], 403);
            }

            $validator = Validator::make($request->all(), [
                'reason' => 'required|string|min:10',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Validation failed',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $application = AgentApplication::find($applicationId);

            if (!$application) {
                return response()->json(['message' => 'Application not found'], 404);
            }

            // Reject the application
            $application->reject($request->reason);

            return response()->json([
                'message' => 'Application rejected',
                'application' => $application,
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * ADMIN: Get all verified agents
     */
    public function getVerifiedAgents(Request $request)
    {
        try {
            if ($request->user()->role !== 'admin') {
                return response()->json(['message' => 'Unauthorized'], 403);
            }

            $agents = AgentApplication::verified()
                ->with('user')
                ->orderBy('verified_at', 'desc')
                ->get();

            return response()->json([
                'message' => 'Verified agents retrieved',
                'agents' => $agents,
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * ADMIN: Get all rejected applications
     */
    public function getRejectedApplications(Request $request)
    {
        try {
            if ($request->user()->role !== 'admin') {
                return response()->json(['message' => 'Unauthorized'], 403);
            }

            $applications = AgentApplication::rejected()
                ->with('user')
                ->orderBy('rejected_at', 'desc')
                ->get();

            return response()->json([
                'message' => 'Rejected applications retrieved',
                'applications' => $applications,
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * ADMIN: Get all users (for user management)
     */
    public function getAllUsers(Request $request)
    {
        try {
            if ($request->user()->role !== 'admin') {
                return response()->json(['message' => 'Unauthorized'], 403);
            }

            $users = User::select('id', 'name', 'email', 'role', 'status', 'created_at')
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'message' => 'Users retrieved',
                'users' => $users,
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * ADMIN: Suspend user
     */
    public function suspendUser(Request $request, $userId)
    {
        try {
            if ($request->user()->role !== 'admin') {
                return response()->json(['message' => 'Unauthorized'], 403);
            }

            $user = User::find($userId);

            if (!$user) {
                return response()->json(['message' => 'User not found'], 404);
            }

            $user->update(['status' => 'suspended']);

            return response()->json([
                'message' => 'User suspended',
                'user' => $user,
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * ADMIN: Reactivate user
     */
    public function reactivateUser(Request $request, $userId)
    {
        try {
            if ($request->user()->role !== 'admin') {
                return response()->json(['message' => 'Unauthorized'], 403);
            }

            $user = User::find($userId);

            if (!$user) {
                return response()->json(['message' => 'User not found'], 404);
            }

            $user->update(['status' => 'active']);

            return response()->json([
                'message' => 'User reactivated',
                'user' => $user,
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function getReviews($id)
    {
        return response()->json(['reviews' => []], 200);
    }

    public function submitReview(Request $request, $id)
    {
        return response()->json(['message' => 'Not implemented'], 501);
    }

    public function getAvailability(Request $request)
    {
        return response()->json(['availability' => []], 200);
    }

    public function updateAvailability(Request $request)
    {
        return response()->json(['message' => 'Not implemented'], 501);
    }

    public function search(Request $request)
    {
        return response()->json(['agents' => []], 200);
    }
}