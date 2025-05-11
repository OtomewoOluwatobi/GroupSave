<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\GroupController;
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

/**
 * Authentication Routes Group
 * Prefix: /auth
 */
Route::prefix('auth')->group(function () {
    // User registration
    Route::post('/register', [UserController::class, 'store'])
        ->name('auth.register');

    // Email verification
    Route::get('/verify/{code}', [UserController::class, 'verifyEmail'])
        ->name('verification.verify');

    // User login
    Route::post('/login', [UserController::class, 'login'])
        ->name('auth.login');

    // User logout (requires authentication)
    Route::get('/logout', [UserController::class, 'logout'])
        ->middleware('auth:api')
        ->name('auth.logout');
});

Route::prefix('user')->group(function () {
    Route::get('/dashboard', [UserController::class, 'dashboard']);
    Route::prefix('group')->group(function () {
        Route::get('/', [GroupController::class, 'index']);
        Route::get('/{id}', [GroupController::class, 'show']);
        Route::post('/store', [GroupController::class, 'store']);
    });
});
