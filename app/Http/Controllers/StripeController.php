<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Plan;
use App\Services\BillingEntitlementService;
use App\Services\PaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Throwable;

class StripeController extends Controller
{
    public function __construct(
        protected BillingEntitlementService $entitlements,
        protected PaymentService $paymentService
    ) {}

    /**
     * Create a SetupIntent so the frontend can safely collect / save a card.
     */
    public function createSetupIntent(Request $request): JsonResponse
    {
        $user = $request->user();

        try {
            if (! $user->hasStripeId()) {
                $user->createAsStripeCustomer();
            }

            return response()->json([
                'status' => 'success',
                'data' => [
                    'setup_intent' => $user->createSetupIntent(),
                ],
            ]);
        } catch (Throwable $e) {
            Log::error('Stripe setup intent error', [
                'user_id' => $user?->id,
                'error' => $e->getMessage(),
                'exception' => get_class($e),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Unable to create setup intent.',
                'debug' => env('APP_DEBUG') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * Create a new subscription for the authenticated user
     */
    public function createSubscription(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'payment_method_id' => 'required',
            'plan_id' => 'required|exists:plans,id',
        ]);

        $user = $request->user();
        $plan = Plan::findOrFail($validated['plan_id']);

        $result = $this->paymentService->createSubscription(
            $user,
            $plan,
            $validated['payment_method_id']
        );

        return response()->json([
            'status' => $result['success'] ? 'success' : 'error',
            'message' => $result['message'],
            'subscription' => $result['subscription'] ?? null,
            'payment' => $result['payment'] ?? null,
        ], $result['code']);
    }

    /**
     * Return the current subscription and active local entitlement.
     */
    public function subscriptionStatus(Request $request): JsonResponse
    {
        $user = $request->user();

        $activePlan = $user->userPlans()
            ->with('plan')
            ->where('status', 'active')
            ->latest()
            ->first();

        return response()->json([
            'status' => 'success',
            'data' => [
                'subscription' => $user->subscription('default'),
                'active_plan' => $activePlan,
            ],
        ]);
    }

    /**
     * Cancel the active Stripe subscription and local entitlement.
     */
    public function cancelSubscription(Request $request): JsonResponse
    {
        $user = $request->user();
        $subscription = $user->subscription('default');

        if (! $subscription || ! $subscription->active()) {
            return response()->json([
                'status' => 'error',
                'message' => 'No active subscription found.',
            ], 404);
        }

        try {
            $subscription->cancel();

            $this->entitlements->deactivateCurrentPlan($user, 'cancelled', $subscription->ends_at ?? now());

            return response()->json([
                'status' => 'success',
                'message' => 'Subscription has been cancelled.',
            ]);
        } catch (Throwable $e) {
            Log::error('Stripe cancel subscription error', [
                'user_id' => $user?->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to cancel subscription. Please try again.',
            ], 400);
        }
    }

    /**
     * Get payment history for the authenticated user
     */
    public function paymentHistory(Request $request): JsonResponse
    {
        $user = $request->user();
        $limit = $request->query('limit', 50);

        $history = $this->paymentService->getUserPaymentHistory($user, $limit);

        return response()->json([
            'status' => 'success',
            'data' => $history,
        ]);
    }
}