<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\NotificationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/**
 * API Routes Configuration
 * 
 * All routes in this file are prefixed with 'api/'
 */

/**
 * Protected route to get authenticated user details
 * Requires valid authentication token
 */
Route::middleware(['auth:jwt'])->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/ping', function () {
    return response()->json(['message' => 'pong'], 200);
});

/**
 * Authentication Routes Group
 * Prefix: /auth
 */
    Route::prefix('auth')->group(function () {
        // User registration
        Route::post('/register', [UserController::class, 'store']);

        // Email verification
        Route::post('/resend-verification', [UserController::class, 'resendEmailVerification']);

        Route::get('/verify', [UserController::class, 'verifyEmail'])->name('verification.verify');

        // User login
        Route::post('/login', [UserController::class, 'login']);

        // Forgot password - request password reset code
        Route::post('/forgot-password', [UserController::class, 'forgotPassword']);

        // Reset password - reset password with code
        Route::post('/reset-password', [UserController::class, 'resetPassword']);

        // User logout (requires authentication)
        Route::get('/logout', [UserController::class, 'logout'])
            ->middleware('auth:api');
    });

/**
 * Notification Routes Group
 * Prefix: /notifications
 * Requires JWT authentication
 */
    Route::prefix('notifications')->middleware(['auth:jwt'])->group(function () {
        // Get all notifications
        Route::get('/', [NotificationController::class, 'index']);
        
        // Get unread notifications
        Route::get('/unread', [NotificationController::class, 'unread']);
        
        // Get unread count
        Route::get('/unread/count', [NotificationController::class, 'unreadCount']);
        
        // Get notifications by type
        Route::get('/type/{type}', [NotificationController::class, 'byType']);
        
        // Get specific notification
        Route::get('/{id}', [NotificationController::class, 'show']);
        
        // Mark notification as read
        Route::put('/{id}/read', [NotificationController::class, 'markAsRead']);
        
        // Mark notification as unread
        Route::put('/{id}/unread', [NotificationController::class, 'markAsUnread']);
        
        // Mark all as read
        Route::put('/mark-all/read', [NotificationController::class, 'markAllAsRead']);
        
        // Delete notification
        Route::delete('/{id}', [NotificationController::class, 'destroy']);
        
        // Delete all notifications
        Route::delete('/', [NotificationController::class, 'deleteAll']);
    });
    Route::get('/dashboard', [UserController::class, 'dashboard']);
    Route::prefix('group')->group(function () {
        Route::get('/', [GroupController::class, 'index']);
        Route::get('/{id}', [GroupController::class, 'show']);
        Route::post('/store', [GroupController::class, 'store']);
        Route::get('/accept-invitation/{id}', [GroupController::class, 'acceptInvitation']);
    });
