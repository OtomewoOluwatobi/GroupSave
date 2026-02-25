<?php

use App\Http\Controllers\GroupController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\UserController;
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
Route::prefix('user')->middleware(['auth:api'])->group(function () {
    Route::get('/dashboard', [UserController::class, 'dashboard']);
    Route::put('/profile', [UserController::class, 'updateProfile']);
    Route::post('/change-password', [UserController::class, 'changePassword']);
    Route::prefix('notifications')->group(function () {
        // Get all notifications (supports filtering via query params)
        Route::get('/', [NotificationController::class, 'index']);
        // Get specific notification
        Route::get('/{id}', [NotificationController::class, 'show']);
        // Mark notification as read
        Route::put('/{id}/read', [NotificationController::class, 'markAsRead']);
        // Mark all as read
        Route::put('/mark-all-read', [NotificationController::class, 'markAllAsRead']);
        // Delete notification
        Route::delete('/{id}', [NotificationController::class, 'destroy']);
    });
    Route::prefix('group')->group(function () {
        Route::get('/', [GroupController::class, 'index']);
        Route::get('/{id}', [GroupController::class, 'show']);
        Route::post('/store', [GroupController::class, 'store']);
        Route::get('/{id}/accept-invitation', [GroupController::class, 'acceptInvitation']);

        // Join request routes
        Route::get('/{id}/send-join-request', [GroupController::class, 'sendJoinRequest']);
        Route::get('/{id}/join-requests', [GroupController::class, 'getPendingJoinRequests']);
        Route::put('/{groupId}/join-requests/{requestId}/approve', [GroupController::class, 'approveJoinRequest']);
        Route::put('/{groupId}/join-requests/{requestId}/reject', [GroupController::class, 'rejectJoinRequest']);
    });
});
