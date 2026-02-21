<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Notifications\VerifyEmailNotification;
use App\Http\Requests\LoginRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules\Password;
use Illuminate\Http\Response;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Events\Verified;
use App\Notifications\UserOnboardingNotification;
use App\Models\Group;
use Illuminate\Support\Facades\DB;
use Exception;

class UserController extends Controller
{
    /**
     * Register a new user.
     *
     * @param Request $request
     * @return JsonResponse
     */

    /**
     * @OA\Post(
     *     path="/api/register",
     *     tags={"Authentication"},
     *     summary="Register a new user",
     *     description="Creates a new user account",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","email","mobile","password","password_confirmation"},
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="email", type="string", format="email", example="user@example.com"),
     *             @OA\Property(property="mobile", type="string", example="+1234567890"),
     *             @OA\Property(property="password", type="string", format="password", example="password123"),
     *             @OA\Property(property="password_confirmation", type="string", format="password", example="password123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="User registered successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="User registration successful"),
     *             @OA\Property(property="user", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The given data was invalid."),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Registration failed"),
     *             @OA\Property(property="message", type="string")
     *         )
     *     )
     * )
     */
    public function store(Request $request): JsonResponse
    {
        try {
            // Validate user input
            $validatedData = $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
                'mobile' => ['required', 'string', 'max:255'],
                'password' => ['required', 'confirmed', Password::defaults()],
            ]);

            // Wrap user creation in a database transaction
            $user = DB::transaction(function () use ($validatedData) {
                // Create new user
                $user = User::create([
                    'name' => $validatedData['name'],
                    'email' => $validatedData['email'],
                    'mobile' => $validatedData['mobile'],
                    'password' => Hash::make($validatedData['password']),
                ]);

                return $user;
            });

            try {
                // Trigger registered event (outside transaction)
                event(new Registered($user));
            } catch (Exception $eventError) {
                Log::warning('Registered event error: ' . $eventError->getMessage());
                // Continue - event error shouldn't block registration
            }

            // Send onboarding notification (outside transaction)
            try {
                $user->notify(new UserOnboardingNotification());
            } catch (Exception $notifyError) {
                Log::warning('Notification error: ' . $notifyError->getMessage());
                // Continue - notification error shouldn't block registration
            }

            return response()->json([
                'message' => 'User registration successful',
                'user' => $user,
            ], 201);
        } catch (Exception $e) {
            Log::error('Registration error: ' . $e->getMessage());
            return response()->json([
                'error' => 'Registration failed',
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Verify user's email address using signed URL
     *
     * @param Request $request
     * @return JsonResponse
     */

    /**
     * @OA\Get(
     *     path="/verify",
     *     tags={"Authentication"},
     *     summary="Verify user's email address",
     *     description="Verifies a user's email address using signed URL with id and hash parameters",
     *     @OA\Parameter(
     *         name="id",
     *         in="query",
     *         required=true,
     *         description="User ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="hash",
     *         in="query",
     *         required=true,
     *         description="Email hash",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="signature",
     *         in="query",
     *         required=true,
     *         description="URL signature for verification",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Email successfully verified",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Email verified successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Invalid or expired verification link",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Invalid or expired verification link")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Invalid verification link")
     *         )
     *     )
     * )
     */
    public function verifyEmail(Request $request): JsonResponse
    {
        try {
            // Verify the signed URL signature
            if (!$request->hasValidSignature()) {
                return response()->json([
                    'message' => 'Invalid or expired verification link'
                ], 403);
            }

            $id = $request->query('id');
            $hash = $request->query('hash');

            $user = User::find($id);

            if (!$user) {
                return response()->json([
                    'message' => 'Invalid verification link'
                ], 404);
            }

            if (!hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
                return response()->json([
                    'message' => 'Invalid verification link'
                ], 403);
            }

            if ($user->hasVerifiedEmail()) {
                return response()->json([
                    'message' => 'Email already verified'
                ], 200);
            }

            $user->markEmailAsVerified();
            event(new Verified($user));

            Log::info('Email verified', [
                'user_id' => $user->id,
                'email' => $user->email,
            ]);

            return response()->json([
                'message' => 'Email verified successfully'
            ], 200);
        } catch (Exception $e) {
            Log::error('Email verification error: ' . $e->getMessage());
            return response()->json([
                'error' => 'Verification failed',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Authenticate user and generate token.
     *
     * @param LoginRequest $request
     * @return Response
     */

    /**
     * @OA\Post(
     *     path="/api/login",
     *     tags={"Authentication"},
     *     summary="Authenticate user and generate token",
     *     description="Logs in a user and returns an authentication token",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email","password"},
     *             @OA\Property(property="email", type="string", format="email", example="user@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="password123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Login successful",
     *         @OA\JsonContent(
     *             @OA\Property(property="token", type="string"),
     *             @OA\Property(property="user", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Invalid credentials",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Invalid credentials")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Email not verified",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Email not verified")
     *         )
     *     )
     * )
     */
    public function login(LoginRequest $request): Response
    {
        try {
            $credentials = $request->only('email', 'password');

            // Find user by email
            $user = User::where('email', $credentials['email'])->first();

            // Check if user exists and password is correct
            if (!$user || !Hash::check($credentials['password'], $user->password)) {
                return new Response([
                    'error' => 'Invalid credentials'
                ], 401);
            }

            // Check if email is verified
            if (!$user->hasVerifiedEmail()) {
                return new Response([
                    'error' => 'Email not verified'
                ], 403);
            }

            // Use JWT guard specifically
            if (!$token = Auth::guard('api')->attempt($credentials)) {
                return new Response([
                    'error' => 'Invalid credentials'
                ], 401);
            }

            return new Response([
                'token' => $token,
                'user' => $user,
                'expires_in' => config('jwt.ttl') * 60,
            ], 200);
        } catch (Exception $e) {
            Log::error('Login error: ' . $e->getMessage());
            return new Response([
                'error' => 'Login failed',
                'message' => $e->getMessage()
            ], 500);
        }
    }
    /**
     * Request password reset code.
     *
     * @param Request $request
     * @return JsonResponse
     */
    /**
     * @OA\Post(
     *     path="/api/forgot-password",
     *     tags={"Authentication"},
     *     summary="Request password reset code",
     *     description="Sends a password reset code to the user's email for mobile app",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email"},
     *             @OA\Property(property="email", type="string", format="email", example="user@example.com")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Reset code sent successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Password reset code sent to your email"),
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="data", type="object", @OA\Property(property="expires_in", type="integer", example=900))
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid email",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Validation error"),
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="No account found with this email"),
     *             @OA\Property(property="status", type="string", example="error")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Failed to send reset code"),
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string")
     *         )
     *     )
     * )
     */
    public function forgotPassword(Request $request): JsonResponse
    {
        try {
            $validatedData = $request->validate(
                ['email' => 'required|email'],
                [
                    'email.required' => 'Email address is required',
                    'email.email' => 'Please provide a valid email address',
                ]
            );

            $user = User::where('email', $validatedData['email'])->first();

            if (!$user) {
                return response()->json([
                    'status' => 'error',
                    'error' => 'No account found with this email',
                    'message' => 'Please check the email and try again, or sign up if you don\'t have an account',
                ], 404);
            }

            $resetCode = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            $expiresAt = now()->addMinutes(15);

            $user->update([
                'password_reset_code' => $resetCode,
                'password_reset_expires_at' => $expiresAt,
            ]);

            try {
                $user->notify(new \App\Notifications\PasswordResetNotification($user, $resetCode));
            } catch (Exception $e) {
                Log::warning('Password reset notification error: ' . $e->getMessage());
                // Don't fail the request, the code is still saved
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Password reset code sent to your email. Please check your inbox.',
                'data' => [
                    'expires_in' => 900, // 15 minutes in seconds
                    'email' => substr($user->email, 0, 3) . '***' . substr($user->email, -4), // masked email
                ]
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'error' => 'Validation error',
                'errors' => $e->errors(),
            ], 422);
        } catch (Exception $e) {
            Log::error('Forgot password error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'error' => 'Failed to send reset code',
                'message' => 'Please try again later',
            ], 500);
        }
    }

    /**
     * Reset user password.
     *
     * @param Request $request
     * @return JsonResponse
     */
    /**
     * @OA\Post(
     *     path="/api/reset-password",
     *     tags={"Authentication"},
     *     summary="Reset user password",
     *     description="Resets the user's password using a valid reset code. Optimized for mobile apps.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email","code","password","password_confirmation"},
     *             @OA\Property(property="email", type="string", format="email", example="user@example.com"),
     *             @OA\Property(property="code", type="string", example="123456"),
     *             @OA\Property(property="password", type="string", format="password", example="newPassword123"),
     *             @OA\Property(property="password_confirmation", type="string", format="password", example="newPassword123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Password reset successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Password has been reset successfully"),
     *             @OA\Property(property="data", type="object", @OA\Property(property="redirect", type="string", example="login"))
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid or expired code",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="error", type="string", example="Invalid or expired reset code"),
     *             @OA\Property(property="code", type="string", example="INVALID_CODE")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="error", type="string", example="User account not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="error", type="string", example="Validation failed"),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="error", type="string", example="Password reset failed")
     *         )
     *     )
     * )
     */
    public function resetPassword(Request $request): JsonResponse
    {
        try {
            $validatedData = $request->validate(
                [
                    'email' => 'required|email',
                    'code' => 'required|string|size:6',
                    'password' => ['required', 'confirmed', Password::defaults()],
                ],
                [
                    'email.required' => 'Email address is required',
                    'email.email' => 'Please provide a valid email address',
                    'code.required' => 'Reset code is required',
                    'code.size' => 'Reset code must be exactly 6 characters',
                    'password.required' => 'Password is required',
                    'password.confirmed' => 'Passwords do not match',
                    'password.regex' => 'Password must contain uppercase, lowercase, numbers, and special characters',
                ]
            );

            $user = User::where('email', $validatedData['email'])->first();

            if (!$user) {
                return response()->json([
                    'status' => 'error',
                    'error' => 'User account not found',
                ], 404);
            }

            // Check if reset code is valid and not expired
            if (
                !$user->password_reset_code ||
                $user->password_reset_code !== $validatedData['code'] ||
                $user->password_reset_expires_at === null ||
                $user->password_reset_expires_at < now()
            ) {
                $errorMessage = 'Invalid or expired reset code';
                $errorCode = 'INVALID_CODE';

                // Provide specific error messages
                if ($user->password_reset_expires_at && $user->password_reset_expires_at < now()) {
                    $errorMessage = 'Reset code has expired. Please request a new one.';
                    $errorCode = 'CODE_EXPIRED';
                }

                return response()->json([
                    'status' => 'error',
                    'error' => $errorMessage,
                    'code' => $errorCode,
                    'message' => 'Please request a new password reset code.',
                ], 400);
            }

            // Update password and clear reset code
            $user->update([
                'password' => Hash::make($validatedData['password']),
                'password_reset_code' => null,
                'password_reset_expires_at' => null,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Password has been reset successfully. Please log in with your new password.',
                'data' => [
                    'redirect' => 'login',
                    'email' => $user->email,
                ]
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'error' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (Exception $e) {
            Log::error('Reset password error: ' . $e->getMessage(), [
                'email' => $validatedData['email'] ?? null,
            ]);
            return response()->json([
                'status' => 'error',
                'error' => 'Password reset failed',
                'message' => 'Please try again later. If the problem persists, contact support.',
            ], 500);
        }
    }

    public function dashboard(Request $request)
    {
        // Check if the user is authenticated
        if (!Auth::check()) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        try {
            // Get the authenticated user
            $user = Auth::user();

            // Get user's groups (both owned and member)
            $userGroups = Group::with(['users' => function ($query) {
                    $query->select('users.id', 'users.name', 'users.email');
                }])
                ->withCount([
                    'users as total_members',
                    'users as active_members_count' => function ($query) {
                        $query->where('group_user.is_active', true);
                    }
                ])
                ->where(function($query) use ($user) {
                    $query->where('owner_id', $user->id)
                        ->orWhereHas('users', function($q) use ($user) {
                            $q->where('user_id', $user->id);
                        });
                })
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($group) use ($user) {
                    $userRole = $group->users->where('id', $user->id)->first()?->pivot?->role ?? 'member';
                    $isActive = $group->users->where('id', $user->id)->first()?->pivot?->is_active ?? false;
                    
                    return [
                        'id' => $group->id,
                        'title' => $group->title,
                        'target_amount' => $group->target_amount,
                        'payable_amount' => $group->payable_amount,
                        'expected_start_date' => $group->expected_start_date,
                        'expected_end_date' => $group->expected_end_date,
                        'payment_out_day' => $group->payment_out_day,
                        'status' => $group->status,
                        'total_members' => $group->total_members,
                        'active_members' => $group->active_members_count,
                        'user_role' => $userRole,
                        'is_active' => $isActive,
                        'is_owner' => $group->owner_id === $user->id,
                        'created_at' => $group->created_at,
                    ];
                });

            // Get suggested groups (random groups user is not part of)
            $suggestedGroups = Group::with(['users' => function ($query) {
                    $query->select('users.id', 'users.name');
                }])
                ->withCount([
                    'users as total_members',
                    'users as active_members_count' => function ($query) {
                        $query->where('group_user.is_active', true);
                    }
                ])
                ->whereDoesntHave('users', function ($query) use ($user) {
                    $query->where('user_id', $user->id);
                })
                ->where('status', 'active')
                ->inRandomOrder()
                ->limit(5)
                ->get()
                ->map(function ($group) {
                    return [
                        'id' => $group->id,
                        'title' => $group->title,
                        'target_amount' => $group->target_amount,
                        'payable_amount' => $group->payable_amount,
                        'total_members' => $group->total_members,
                        'active_members' => $group->active_members_count,
                        'expected_start_date' => $group->expected_start_date,
                        'payment_out_day' => $group->payment_out_day,
                        'owner' => $group->users->where('pivot.role', 'admin')->first()?->name,
                    ];
                });

            // Get pending invitations
            $pendingInvitations = Group::with(['users' => function ($query) {
                    $query->where('group_user.role', 'admin');
                }])
                ->whereHas('users', function ($query) use ($user) {
                    $query->where('user_id', $user->id)
                          ->where('group_user.is_active', false);
                })
                ->get()
                ->map(function ($group) {
                    return [
                        'id' => $group->id,
                        'title' => $group->title,
                        'target_amount' => $group->target_amount,
                        'payable_amount' => $group->payable_amount,
                        'invited_by' => $group->users->first()?->name,
                        'created_at' => $group->created_at,
                    ];
                });

            // Dashboard statistics
            $stats = [
                'total_groups' => $userGroups->count(),
                'owned_groups' => $userGroups->where('is_owner', true)->count(),
                'member_groups' => $userGroups->where('is_owner', false)->count(),
                'pending_invitations' => $pendingInvitations->count(),
                'active_groups' => $userGroups->where('status', 'active')->count(),
            ];

            return response()->json([
                'message' => 'Dashboard data retrieved successfully',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'mobile' => $user->mobile,
                    'created_at' => $user->created_at,
                ],
                'stats' => $stats,
                'user_groups' => $userGroups,
                'suggested_groups' => $suggestedGroups,
                'pending_invitations' => $pendingInvitations,
                'user_banks' => $user->userBank ?? [],
            ], 200);

        } catch (\Exception $e) {
            Log::error('Dashboard error', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'message' => 'Failed to load dashboard data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Logout user and invalidate session.
     *
     * @param Request $request
     * @return Response
     */
    /**
     * @OA\Post(
     *     path="/api/logout",
     *     tags={"Authentication"},
     *     summary="Logout user and invalidate session",
     *     description="Logs out the authenticated user and invalidates their session",
     *     security={{ "bearerAuth":{ }}},
     *     @OA\Response(
     *         response=204,
     *         description="Successfully logged out"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Logout failed",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Logout failed"),
     *             @OA\Property(property="message", type="string", example="Error message")
     *         )
     *     )
     * )
     */
    public function logout(Request $request): Response
    {
        try {
            // Logout user
            Auth::guard('api')->logout();

            return response()->noContent();
        } catch (Exception $e) {
            return new Response([
                'error' => 'Logout failed',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Resend email verification
     */
    public function resendEmailVerification(Request $request): JsonResponse
    {
        try {
            // Validate email input
            $validated = $request->validate([
                'email' => 'required|email|exists:users,email',
            ], [
                'email.required' => 'Email address is required',
                'email.email' => 'Please provide a valid email address',
                'email.exists' => 'No account found with this email address',
            ]);

            $user = User::where('email', $validated['email'])->first();

            if (!$user) {
                return response()->json([
                    'message' => 'No account found with this email address',
                ], 404);
            }

            if ($user->hasVerifiedEmail()) {
                return response()->json([
                    'message' => 'Email already verified',
                ], 200);
            }

            Log::info('Resend verification email requested', [
                'user_id' => $user->id,
                'email' => $user->email,
            ]);

            // Rate limiting - prevent spam
            $lastSentAt = $user->email_verification_sent_at ?? null;
            if ($lastSentAt && now()->diffInSeconds($lastSentAt) < 60) {
                return response()->json([
                    'message' => 'Please wait before requesting another verification email',
                    'retry_after' => 60 - now()->diffInSeconds($lastSentAt),
                ], 429);
            }

            $user->notify(new VerifyEmailNotification());
            $user->update(['email_verification_sent_at' => now()]);

            Log::info('Verification email resent', [
                'user_id' => $user->id,
                'email' => $user->email,
            ]);

            return response()->json([
                'message' => 'Verification email sent successfully',
                'data' => [
                    'email' => substr($user->email, 0, 3) . '***' . substr($user->email, -4),
                ]
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('Failed to resend verification email', [
                'email' => $validated['email'] ?? null,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'message' => 'Failed to send verification email',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
