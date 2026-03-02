<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Services\GoogleCalendarService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Log;

class GoogleCalendarController extends BaseController
{
    /**
     * GET /api/google/redirect
     * Returns the Google OAuth URL for the frontend to redirect to.
     * Encodes the authenticated user's ID in the state parameter.
     */
    public function redirect(Request $request)
    {
        try {
            $service = new GoogleCalendarService();
            $userId = $request->user()->id;

            return response()->json([
                'success'  => true,
                'auth_url' => $service->getAuthUrl($userId),
            ]);
        } catch (\Exception $e) {
            Log::error('Google redirect error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Google Calendar is not configured.',
            ], 500);
        }
    }

    /**
     * GET /google/callback (web route, not API)
     * Handles the OAuth callback from Google.
     * Retrieves the user from the encrypted state parameter.
     */
    public function callback(Request $request)
    {
        try {
            if (!$request->has('code')) {
                return redirect(config('app.url') . '/settings?google=error');
            }

            // Decrypt the user ID from the state parameter
            $userId = decrypt($request->input('state'));
            $user = User::findOrFail($userId);

            $service = new GoogleCalendarService();
            $token = $service->handleCallback($request->code);

            if (isset($token['error'])) {
                Log::error('Google token error: ' . ($token['error_description'] ?? $token['error']));
                return redirect(config('app.url') . '/settings?google=error');
            }

            $user->update([
                'google_access_token'     => json_encode($token),
                'google_refresh_token'    => $token['refresh_token'] ?? $user->google_refresh_token,
                'google_token_expires_at' => now()->addSeconds($token['expires_in'] ?? 3600),
            ]);

            return redirect(config('app.url') . '/settings?google=connected');
        } catch (\Exception $e) {
            Log::error('Google callback error: ' . $e->getMessage());

            return redirect(config('app.url') . '/settings?google=error');
        }
    }

    /**
     * DELETE /api/google/disconnect
     * Removes Google Calendar connection for the current user.
     */
    public function disconnect(Request $request)
    {
        $request->user()->update([
            'google_access_token'     => null,
            'google_refresh_token'    => null,
            'google_token_expires_at' => null,
            'google_calendar_id'      => null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Google Calendar disconnected.',
        ]);
    }

    /**
     * GET /api/google/status
     * Check if the current user has Google Calendar connected.
     */
    public function status(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'success'   => true,
            'connected' => !empty($user->google_access_token),
            'expires_at' => $user->google_token_expires_at,
        ]);
    }
}
