<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    /**
     * Get user profile
     */
    public function profile(Request $request)
    {
        try {
            $user = $request->user();

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'profile_photo_path' => $user->profile_photo_path,
                    'address' => $user->address ?? null,
                    'city' => $user->city ?? null,
                    'province' => $user->province ?? null,
                    'postal_code' => $user->postal_code ?? null,
                    'date_of_birth' => $user->date_of_birth ?? null,
                    'role' => $user->role,
                    'status' => $user->status,
                    'preferences' => $user->preferences ?? [],
                    'email_verified_at' => $user->email_verified_at,
                    'created_at' => $user->created_at,
                ],
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch profile',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update user profile
     */
    public function updateProfile(Request $request)
    {
        try {
            $user = $request->user();

            $validated = $request->validate([
                'name' => 'sometimes|string|max:255',
                'phone' => 'sometimes|string|max:20',
                'email' => 'sometimes|email|max:255|unique:users,email,' . $user->id,
                'address' => 'sometimes|string|max:255',
                'city' => 'sometimes|string|max:100',
                'province' => 'sometimes|string|max:100',
                'postal_code' => 'sometimes|string|max:20',
                'date_of_birth' => 'sometimes|date',
            ]);

            $user->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Profile updated successfully',
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
                'message' => 'Failed to update profile',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Change password
     */
    public function changePassword(Request $request)
    {
        try {
            $user = $request->user();

            $validated = $request->validate([
                'current_password' => 'required|string|min:8',
                'password' => 'required|string|min:8|confirmed',
            ]);

            // Verify current password
            if (!Hash::check($validated['current_password'], $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Current password is incorrect',
                ], 422);
            }

            // Update password
            $user->update([
                'password' => Hash::make($validated['password']),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Password changed successfully',
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
                'message' => 'Failed to change password',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Upload profile photo
     */
    public function uploadProfilePhoto(Request $request)
    {
        try {
            $user = $request->user();

            // Validate request
            $request->validate([
                'photo' => 'required|image|max:2048', // 2MB max
            ]);

            // Check if photo file exists
            if (!$request->hasFile('photo')) {
                return response()->json([
                    'success' => false,
                    'message' => 'No photo file provided',
                ], 400);
            }

            $photo = $request->file('photo');

            // Validate it's a real image
            if (!$photo->isValid()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid photo file',
                ], 400);
            }

            // Delete old photo if exists
            if ($user->profile_photo_path) {
                try {
                    if (Storage::disk('public')->exists($user->profile_photo_path)) {
                        Storage::disk('public')->delete($user->profile_photo_path);
                    }
                } catch (\Exception $e) {
                    // Continue even if delete fails
                    \Log::warning('Failed to delete old profile photo: ' . $e->getMessage());
                }
            }

            // Store new photo
            $path = $photo->store('profile-photos', 'public');
            
            if (!$path) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to store photo',
                ], 500);
            }

            // Update user
            $user->update([
                'profile_photo_path' => $path,
            ]);

            // Get full URL
            $url = Storage::disk('public')->url($path);

            return response()->json([
                'success' => true,
                'message' => 'Photo uploaded successfully',
                'data' => [
                    'photo_path' => $path,
                    'photo_url' => $url,
                ],
            ], 200);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Photo upload error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to upload photo',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update preferences
     */
    public function updatePreferences(Request $request)
    {
        try {
            $user = $request->user();

            $validated = $request->validate([
                'email_notifications' => 'sometimes|boolean',
                'sms_notifications' => 'sometimes|boolean',
                'language' => 'sometimes|string|in:en,fil,es',
                'currency' => 'sometimes|string|in:PHP,USD,EUR',
            ]);

            // Get existing preferences or create new array
            $preferences = $user->preferences ?? [];

            // Merge with new preferences
            $preferences = array_merge($preferences, $validated);

            $user->update(['preferences' => $preferences]);

            return response()->json([
                'success' => true,
                'message' => 'Preferences updated successfully',
                'data' => ['preferences' => $preferences],
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
                'message' => 'Failed to update preferences',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get user preferences
     */
    public function getPreferences(Request $request)
    {
        try {
            $user = $request->user();

            return response()->json([
                'success' => true,
                'data' => ['preferences' => $user->preferences ?? []],
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch preferences',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}