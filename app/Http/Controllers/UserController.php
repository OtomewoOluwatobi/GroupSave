<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\User;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\Group;
use Illuminate\Http\Response;
use Exception;
use Illuminate\Support\Facades\Log;
use App\Notifications\UserOnboardingNotification;

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

            // Generate a unique verification code
            $verificationCode = bin2hex(random_bytes(16));

            // Wrap user creation in a database transaction
            $user = DB::transaction(function () use ($validatedData, $verificationCode) {
                // Create new user
                $user = User::create([
                    'name' => $validatedData['name'],
                    'email' => $validatedData['email'],
                    'mobile' => $validatedData['mobile'],
                    'password' => Hash::make($validatedData['password']),
                    'email_verification_code' => $verificationCode, // Save the verification code
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
                $user->notify(new UserOnboardingNotification($user));
            } catch (Exception $notifyError) {
                Log::warning('Notification error: ' . $notifyError->getMessage());
                // Continue - notification error shouldn't block registration
            }

            $user->append('verifyLink', 'https://phplaravel-1549794-6203025.cloudwaysapps.com/api/auth/verify/' . $verificationCode); // Include code in response

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
     * Verify user's email address.
     *
     * @param string $code
     * @return JsonResponse
     */

    /**
     * @OA\Get(
     *     path="/verify/{token}",
     *     tags={"Authentication"},
     *     summary="Verify user's email address",
     *     description="Verifies a user's email address using the provided token",
     *     @OA\Parameter(
     *         name="token",
     *         in="path",
     *         required=true,
     *         description="Email verification token",
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
     *         response=400,
     *         description="Invalid or expired token",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Invalid or expired verification token")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="User not found")
     *         )
     *     )
     * )
     */
    public function verifyEmail(string $code): JsonResponse
    {
        try {
            // Find user by verification code
            $user = User::where('email_verification_code', $code)->first();

            if (!$user) {
                return response()->json([
                    'message' => 'Invalid verification code'
                ], 400);
            }

            // Update user verification status
            $user->email_verified_at = now();
            $user->email_verification_code = null;
            $user->save();

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
            if ($user->email_verification_code !== null) {
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
}
