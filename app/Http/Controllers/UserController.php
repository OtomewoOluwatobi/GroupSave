<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\User;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\Response;
use Exception;
use Illuminate\Support\Facades\Log;

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

            // Create new user
            $user = User::create([
                'name' => $validatedData['name'],
                'email' => $validatedData['email'],
                'mobile' => $validatedData['mobile'],
                'password' => Hash::make($validatedData['password']),
                'email_verification_code' => $verificationCode, // Save the verification code
            ]);

            // Trigger registered event
            event(new Registered($user));

            // Send verification email
            $user->sendEmailVerificationNotification();

            return response()->json([
                'message' => 'User registration successful',
                'user' => $user
            ], 201);
        } catch (Exception $e) {
            Log::error('Registration error: ' . $e->getMessage());
            return response()->json([
                'error' => 'Registration failed',
                'message' => $e->getMessage()
            ], 500);
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

            // Attempt to generate JWT token
            $token = Auth::guard('api')->attempt($credentials);
            if (!$token) {
                return new Response([
                    'error' => 'Token generation failed'
                ], 401);
            }

            return new Response([
                'token' => $token,
                'user' => $user
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
     *     security={{"bearerAuth":{}}},
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
            Auth::guard('web')->logout();

            // Invalidate and regenerate session
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return response()->noContent();
        } catch (Exception $e) {
            Log::error('Logout error: ' . $e->getMessage());
            return new Response([
                'error' => 'Logout failed',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
