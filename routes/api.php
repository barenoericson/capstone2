<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PropertyController;
use App\Http\Controllers\Api\AgentController;
use App\Http\Controllers\Api\ViewingController;
use App\Http\Controllers\Api\MessageController;
use App\Http\Controllers\Api\DocumentController;
use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\AgentReviewController;
use App\Http\Controllers\Api\AgentApplicationController;
use App\Http\Controllers\Api\ChatbotController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
| Register all API endpoints for the Real Estate Platform
*/

// ============================================
// PUBLIC ROUTES (No Authentication Required)
// ============================================

Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('/reset-password', [AuthController::class, 'resetPassword']);
    Route::post('/verify-email', [AuthController::class, 'verifyEmail']);
});

// ✅ PROPERTY ROUTES - PUBLIC
Route::prefix('properties')->group(function () {
    // Get all properties with pagination
    Route::get('/', [PropertyController::class, 'index']);
    
    // ✅ GET SINGLE PROPERTY (Used by PropertyDetailsPage and Edit)
    Route::get('/{id}', [PropertyController::class, 'show']);
    
    // ✅ INCREMENT VIEW COUNT
    Route::post('/{id}/views', [PropertyController::class, 'incrementViews']);

    // Blocked dates for a property (public - buyers need to see these before booking)
    Route::get('/{id}/blocked-dates', [ViewingController::class, 'getPropertyBlockedDates']);
});

Route::prefix('agents')->group(function () {
    Route::get('/{id}', [AgentController::class, 'show']);
    Route::get('/{id}/reviews', [AgentController::class, 'getReviews']);
});

// Public profile endpoints (by user_id)
Route::get('/users/{userId}/profile', [AgentReviewController::class, 'buyerProfile']);
Route::get('/users/{userId}/agent-profile', [AgentReviewController::class, 'agentProfile']);

Route::prefix('search')->group(function () {
    Route::get('/properties/suggestions', [PropertyController::class, 'suggestions']);
    Route::get('/locations/suggestions', [PropertyController::class, 'locationSuggestions']);
    Route::get('/agents', [AgentController::class, 'search']);
});

// ============================================
// PROTECTED ROUTES (Authentication Required)
// ============================================

Route::middleware('auth:sanctum')->group(function () {
    
    // ============================================
    // AUTH ROUTES
    // ============================================
    Route::prefix('auth')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'profile']);
        Route::post('/refresh-token', [AuthController::class, 'refreshToken']);
    });

    // ============================================
    // USER PROFILE ROUTES
    // ============================================
    Route::prefix('user')->group(function () {
        Route::get('/profile', [UserController::class, 'profile']);
        Route::put('/profile', [UserController::class, 'updateProfile']);
        Route::post('/change-password', [UserController::class, 'changePassword']);
        Route::post('/upload-profile-photo', [UserController::class, 'uploadProfilePhoto']);
        Route::post('/become-agent', [AgentApplicationController::class, 'apply']);
        Route::get('/agent-application-status', [AgentApplicationController::class, 'status']);
        Route::get('/preferences', [UserController::class, 'getPreferences']);
        Route::put('/preferences', [UserController::class, 'updatePreferences']);
        Route::post('/heartbeat', [MessageController::class, 'heartbeat']);
    });

    // ============================================
    // MESSAGING ROUTES
    // ============================================
    Route::prefix('messages')->group(function () {
        Route::get('/', [MessageController::class, 'getConversations']);
        Route::post('/', [MessageController::class, 'send']);
        Route::get('/unread-count', [MessageController::class, 'getUnreadCount']);
        Route::put('/{id}/read', [MessageController::class, 'markAsRead']);
    });

    Route::prefix('conversations')->group(function () {
        Route::get('/{userId}/messages', [MessageController::class, 'getConversation']);
        Route::post('/{userId}/mark-read', [MessageController::class, 'markConversationRead']);
    });

    // ============================================
    // NOTIFICATIONS ROUTES
    // ============================================
    Route::prefix('notifications')->group(function () {
        Route::get('/', [NotificationController::class, 'index']);
        Route::put('/{id}/read', [NotificationController::class, 'markAsRead']);
        Route::put('/mark-all-read', [NotificationController::class, 'markAllAsRead']);
        Route::delete('/{id}', [NotificationController::class, 'delete']);
        Route::get('/unread-count', [NotificationController::class, 'getUnreadCount']);
    });

    // ============================================
    // BUYER ROUTES
    // ============================================
    Route::prefix('buyer')->group(function () {
        // Saved properties
        Route::get('/saved-properties', [PropertyController::class, 'getSavedProperties']);
        Route::post('/properties/{id}/save', [PropertyController::class, 'saveProperty']);
        
        // Viewings
        Route::get('/viewings', [ViewingController::class, 'getBuyerViewings']);
        Route::post('/viewings', [ViewingController::class, 'requestViewing']);
        
        // Documents
        Route::get('/documents', [DocumentController::class, 'getBuyerDocuments']);
        Route::post('/documents/{id}/sign', [DocumentController::class, 'signDocument']);

        // Agent Reviews (buyer submits)
        Route::post('/agents/{agentId}/reviews', [AgentReviewController::class, 'store']);

        // Messaging
        Route::get('/conversations', [MessageController::class, 'getBuyerConversations']);
        Route::post('/messages', [MessageController::class, 'sendMessage']);
    });

    // ============================================
    // AGENT ROUTES
    // ============================================
    Route::prefix('agent')->group(function () {
        // Agent Profile
        Route::get('/profile', [AgentController::class, 'getMyProfile']);
        Route::put('/profile', [AgentController::class, 'updateProfile']);
        
        // ✅ PROPERTY ROUTES - Agent can CRUD properties
        Route::get('/properties', [PropertyController::class, 'agentProperties']);
        Route::post('/properties', [PropertyController::class, 'store']);
        Route::get('/properties/{id}', [PropertyController::class, 'show']);
        Route::put('/properties/{id}', [PropertyController::class, 'update']);
        Route::patch('/properties/{id}/status', [PropertyController::class, 'updateStatus']);
        Route::delete('/properties/{id}', [PropertyController::class, 'destroy']);
        
        // Property Photos
        Route::post('/properties/{id}/photos', [PropertyController::class, 'uploadPhotos']);
        Route::delete('/photos/{photoId}', [PropertyController::class, 'deletePhoto']);
        Route::put('/photos/{photoId}/cover', [PropertyController::class, 'setCoverPhoto']);
        
        // Viewings
        Route::get('/viewings', [ViewingController::class, 'getAgentViewings']);
        Route::put('/viewings/{id}/approve', [ViewingController::class, 'approveViewing']);
        Route::put('/viewings/{id}/reject', [ViewingController::class, 'rejectViewing']);
        Route::put('/viewings/{id}/completed', [ViewingController::class, 'markCompleted']);
        Route::post('/viewings/{id}/reminder', [ViewingController::class, 'sendReminder']);

        // Blocked Dates (calendar management)
        Route::get('/blocked-dates', [ViewingController::class, 'getBlockedDates']);
        Route::post('/blocked-dates', [ViewingController::class, 'blockDate']);
        Route::delete('/blocked-dates/{id}', [ViewingController::class, 'unblockDate']);

        // Documents
        Route::get('/documents', [DocumentController::class, 'getAgentDocuments']);
        Route::post('/documents', [DocumentController::class, 'uploadDocument']);
        Route::post('/documents/{id}/sign', [DocumentController::class, 'signAgentDocument']);
        Route::delete('/documents/{id}', [DocumentController::class, 'deleteDocument']);

        // Buyers from conversations (for document picker)
        Route::get('/conversation-buyers', [DocumentController::class, 'getConversationBuyers']);
        
        // Saved Properties (My Wallet)
        Route::get('/saved-properties', [PropertyController::class, 'getSavedProperties']);
        Route::post('/properties/{id}/save', [PropertyController::class, 'saveProperty']);

        // Messaging
        Route::get('/conversations', [MessageController::class, 'getAgentConversations']);
        Route::post('/messages', [MessageController::class, 'sendMessage']);

        // Availability
        Route::get('/availability', [AgentController::class, 'getAvailability']);
        Route::put('/availability', [AgentController::class, 'updateAvailability']);
        
        // Agent Applications
        Route::post('/apply', [AgentController::class, 'applyAsAgent']);
        Route::get('/application-status', [AgentController::class, 'getApplicationStatus']);
    });

    // ============================================
    // CHATBOT ROUTES (RealtyLinkPH Buddy)
    // ============================================
    Route::prefix('chatbot')->group(function () {
        Route::get('/history', [ChatbotController::class, 'history']);
        Route::post('/message', [ChatbotController::class, 'sendMessage']);
        Route::delete('/history', [ChatbotController::class, 'clearHistory']);
    });

    // ============================================
    // DOCUMENT ROUTES
    // ============================================
    Route::prefix('documents')->group(function () {
        Route::get('/{id}/download', [DocumentController::class, 'downloadDocument']);
        Route::delete('/{id}', [DocumentController::class, 'deleteDocument']);
    });

    // ============================================
    // ADMIN ROUTES (Admin only)
    // ============================================
    Route::prefix('admin')->middleware('can:isAdmin')->group(function () {
        // Dashboard
        Route::get('/dashboard/overview', [AdminController::class, 'getDashboardOverview']);
        
        // User Management
        Route::get('/users', [AdminController::class, 'getUsers']);
        Route::post('/users/{id}/suspend', [AdminController::class, 'suspendUser']);
        Route::post('/users/{id}/reactivate', [AdminController::class, 'reactivateUser']);
        
        // Agent Management
        Route::get('/agents/pending', [AdminController::class, 'getPendingAgents']);
        Route::post('/agents/{id}/verify', [AdminController::class, 'verifyAgent']);
        Route::post('/agents/{id}/reject', [AdminController::class, 'rejectAgent']);
        Route::get('/agents/verified', [AgentController::class, 'getVerifiedAgents']);
        Route::get('/agents/rejected', [AgentController::class, 'getRejectedApplications']);
        
        // Property Management
        Route::get('/properties', [AdminController::class, 'getProperties']);
        Route::delete('/properties/{id}', [AdminController::class, 'deleteProperty']);
        
        // Photo Moderation
        Route::get('/photos/pending', [AdminController::class, 'getPendingPhotos']);
        Route::post('/photos/{id}/approve', [AdminController::class, 'approvePhoto']);
        Route::post('/photos/{id}/reject', [AdminController::class, 'rejectPhoto']);
        
        // Audit Logs
        Route::get('/audit-logs', [AdminController::class, 'getAuditLogs']);
        Route::get('/notifications', [AdminController::class, 'getNotifications']);
    });
});

// ============================================
// CATCH-ALL FALLBACK (404 Handler)
// ============================================

Route::fallback(function () {
    return response()->json([
        'success' => false,
        'message' => 'Endpoint not found',
        'status' => 404
    ], 404);
});