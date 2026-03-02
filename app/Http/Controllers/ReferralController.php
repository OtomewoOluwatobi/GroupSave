<?php

namespace App\Http\Controllers;

use App\Services\ReferralService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReferralController extends Controller
{
    protected ReferralService $referralService;

    public function __construct(ReferralService $referralService)
    {
        $this->referralService = $referralService;
    }

    /**
     * @OA\Get(
     *     path="/api/user/referral",
     *     tags={"Referral"},
     *     summary="Get referral dashboard data",
     *     description="Returns the authenticated user's referral statistics, code, milestone progress, and history",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Referral dashboard data retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="referral_code", type="string", example="GRP-XXXX"),
     *                 @OA\Property(property="stats", type="object",
     *                     @OA\Property(property="active", type="integer", example=2),
     *                     @OA\Property(property="pending", type="integer", example=1),
     *                     @OA\Property(property="total_points", type="integer", example=20)
     *                 ),
     *                 @OA\Property(property="earnings_overview", type="object",
     *                     @OA\Property(property="total_points", type="integer", example=20),
     *                     @OA\Property(property="points_per_referral", type="integer", example=10)
     *                 ),
     *                 @OA\Property(property="milestone", type="object",
     *                     @OA\Property(property="next_target", type="integer", example=50),
     *                     @OA\Property(property="current_points", type="integer", example=20),
     *                     @OA\Property(property="points_to_go", type="integer", example=30),
     *                     @OA\Property(property="progress_percentage", type="number", example=40.0)
     *                 ),
     *                 @OA\Property(property="history", type="array", @OA\Items(type="object"))
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     )
     * )
     */
    public function index(): JsonResponse
    {
        $user = Auth::user();
        $dashboardData = $this->referralService->getDashboardData($user);

        return response()->json([
            'success' => true,
            'data' => $dashboardData,
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/user/referral/history",
     *     tags={"Referral"},
     *     summary="Get referral history",
     *     description="Returns the list of users referred by the authenticated user",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Referral history retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="referred_user", type="object",
     *                         @OA\Property(property="name", type="string", example="James Adeyemi"),
     *                         @OA\Property(property="initials", type="string", example="JA")
     *                     ),
     *                     @OA\Property(property="points_awarded", type="integer", example=10),
     *                     @OA\Property(property="status", type="string", example="active"),
     *                     @OA\Property(property="date", type="string", example="10 Feb 2026")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     )
     * )
     */
    public function history(): JsonResponse
    {
        $user = Auth::user();
        $history = $this->referralService->getReferralHistory($user);

        return response()->json([
            'success' => true,
            'data' => $history,
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/referral/validate",
     *     tags={"Referral"},
     *     summary="Validate a referral code",
     *     description="Checks if a referral code is valid and returns the referrer's name",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"referral_code"},
     *             @OA\Property(property="referral_code", type="string", example="GRP-XXXX")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Valid referral code",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Valid referral code"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="referrer_name", type="string", example="John Doe")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Invalid referral code",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Invalid referral code")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
     */
    public function validateCode(Request $request): JsonResponse
    {
        $request->validate([
            'referral_code' => 'required|string|max:10',
        ]);

        $referrer = $this->referralService->validateReferralCode($request->referral_code);

        if (!$referrer) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid referral code',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Valid referral code',
            'data' => [
                'referrer_name' => $referrer->name,
            ],
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/user/referral/regenerate-code",
     *     tags={"Referral"},
     *     summary="Regenerate referral code",
     *     description="Generates a new unique referral code for the authenticated user",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Referral code regenerated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Referral code regenerated"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="referral_code", type="string", example="GRP-ABCD")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     )
     * )
     */
    public function regenerateCode(): JsonResponse
    {
        $user = Auth::user();
        $user->referral_code = $user::generateUniqueReferralCode();
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Referral code regenerated',
            'data' => [
                'referral_code' => $user->referral_code,
            ],
        ]);
    }
}
