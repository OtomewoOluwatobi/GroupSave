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

class UserController extends Controller
{
    /**
     * Register a new user.
     */

    /**
     * @OA\Post(
     *     path="/api/register",
     *     summary="Register a new user",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","email","mobile","password","password_confirmation"},
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="email", type="string", format="email", example="john@example.com"),
     *             @OA\Property(property="mobile", type="string", example="1234567890"),
     *             @OA\Property(property="password", type="string", format="password", example="password123"),
     *             @OA\Property(property="password_confirmation", type="string", format="password", example="password123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="User registered successfully",
     *         @OA\JsonContent(
     *             type="string",
     *             example="user registration successful"
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'mobile' => ['required', 'string', 'max:255'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'mobile' => $request->mobile,
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));

        // Send email verification link
        $user->sendEmailVerificationNotification();

        return response()->json("user registration successfull", 201);
    }

    /**
     * Verify the user's email.
     */

    /**
     * @OA\Get(
     *     path="/api/verify-email/{code}",
     *     summary="Verify user email",
     *     tags={"Authentication"},
     *     @OA\Parameter(
     *         name="code",
     *         in="path",
     *         required=true,
     *         description="Email verification code",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Email verified successfully"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid verification code"
     *     )
     * )
     */
    /**
     * Verify the user's email address.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $code
     * @return \Illuminate\Http\JsonResponse
     */
    /**
     * @OA\Get(
     *     path="/api/verify-email/{code}",
     *     summary="Verify user email",
     *     tags={"Authentication"},
     *     @OA\Parameter(
     *         name="code",
     *         in="path",
     *         required=true,
     *         description="Email verification code",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Email verified successfully"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid verification code"
     *     )
     * )
     */
    /**
     * Verify the user's email address.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $code
     * @return \Illuminate\Http\JsonResponse
     */
    /**
     * @OA\Get(
     *     path="/api/verify-email/{code}",
     *     summary="Verify user email",
     *     tags={"Authentication"},
     *     @OA\Parameter(
     *         name="code",
     *         in="path",
     *         required=true,
     *         description="Email verification code",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Email verified successfully"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid verification code"
     *     )
     * )
     */

    public function verifyEmail(Request $request, $code)
    {
        $user = User::where('email_verification_code', $code)->first();
        if (!$user) {
            return response()->json(['message' => 'Invalid verification code'], 400);
        }

        $user->email_verified_at = now();
        $user->email_verification_code = null;
        $user->save();

        return response()->json(['message' => 'Email verified successfully'], 200);
    }

    /**
     * Authenticate a user.
     */
    public function login(LoginRequest $request): Response
    {
        $credentials = $request->only('email', 'password');

        $user = User::where('email', $credentials['email'])->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            return new Response(['error' => 'Unauthorized'], 401);
        }

        if ($user->email_verification_code != null) {
            return new Response(['error' => 'Email not verified'], 403);
        }

        if (!$token = Auth::guard('api')->attempt($credentials)) {
            return new Response(['error' => 'Unauthorized'], 401);
        }

        return new Response(['token' => $token], 200);
    }

    /**
     * Destroy an authenticated session.
     */
    public function logout(Request $request): Response
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return response()->noContent();
    }
}
