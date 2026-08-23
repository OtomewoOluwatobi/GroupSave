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

    /**
     * Create a Stripe Checkout Session for subscription
     * User redirects to returned URL, completes payment, and returns to success_url
     * Webhook will automatically create subscription in your DB
     */
    public function createCheckoutSession(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'plan_id' => 'required|exists:plans,id',
            'success_url' => 'required|url',
            'cancel_url' => 'required|url',
        ]);

        $user = $request->user();
        $plan = Plan::findOrFail($validated['plan_id']);

        try {
            // Validate plan is not free
            if ($plan->billing === 'free_forever') {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Free plans should be joined via the plan endpoint.',
                    'code' => 422,
                ], 422);
            }

            // Validate Stripe price ID exists
            if (!$plan->stripe_price_id) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'This plan is not configured for Stripe payments.',
                ], 400);
            }

            // Ensure user has Stripe customer ID
            if (!$user->hasStripeId()) {
                $user->createAsStripeCustomer();
            }

            // Create checkout session - Stripe handles payment + subscription creation
            $session = $user->checkout(
                [
                    [
                        'price' => $plan->stripe_price_id,  // Must be a price ID, not product
                        'quantity' => 1,
                    ]
                ],
                [
                    'success_url' => $validated['success_url'],
                    'cancel_url' => $validated['cancel_url'],
                    'mode' => 'subscription',
                    'metadata' => [
                        'user_id' => $user->id,
                        'plan_id' => $plan->id,
                    ],
                ]
            );

            Log::info('Stripe checkout session created', [
                'user_id' => $user->id,
                'plan_id' => $plan->id,
                'session_id' => $session->id,
            ]);

            return response()->json([
                'status' => 'success',
                'data' => [
                    'checkout_url' => $session->url,
                    'session_id' => $session->id,
                ],
            ]);
        } catch (Throwable $e) {
            Log::error('Stripe checkout session error', [
                'user_id' => $user?->id,
                'plan_id' => $plan?->id,
                'error' => $e->getMessage(),
                'exception' => get_class($e),
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create checkout session.',
                'debug' => env('APP_DEBUG') ? $e->getMessage() : null,
            ], 400);
        }
    }

    /**
     * Handle successful Stripe checkout
     * Stripe redirects to this URL after successful payment
     */
    public function handleCheckoutSuccess(Request $request): JsonResponse
    {
        $sessionId = $request->query('session_id');

        if (!$sessionId) {
            return response()->json([
                'status' => 'error',
                'message' => 'Missing session ID',
            ], 400);
        }

        try {
            // Retrieve session from Stripe to confirm payment
            $session = \Stripe\Checkout\Session::retrieve($sessionId);

            // Payment successful - Stripe webhook will handle subscription activation
            Log::info('Checkout success - session retrieved', [
                'session_id' => $sessionId,
                'payment_status' => $session->payment_status,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Payment successful! Your plan has been activated.',
                'session_id' => $sessionId,
            ]);
        } catch (Throwable $e) {
            Log::error('Checkout success handler error', [
                'session_id' => $sessionId,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve payment status.',
            ], 400);
        }
    }

    /**
     * Handle cancelled Stripe checkout
     * Stripe redirects to this URL if user cancels payment
     */
    public function handleCheckoutCancel(Request $request): JsonResponse
    {
        $sessionId = $request->query('session_id');

        Log::info('Checkout cancelled', [
            'session_id' => $sessionId,
        ]);

        return response()->json([
            'status' => 'cancelled',
            'message' => 'Payment was cancelled. You can try again anytime.',
            'session_id' => $sessionId,
        ], 200);
    }

    /**
     * Verify checkout session payment status
     * Mobile app calls this after returning from Stripe checkout
     * Does NOT activate the plan - webhook does that
     */
    public function verifyCheckoutSession(Request $request): JsonResponse
    {
        $request->validate([
            'session_id' => 'required|string',
        ]);

        try {
            $session = \Stripe\Checkout\Session::retrieve($request->session_id);

            Log::info('Checkout session verified', [
                'user_id' => $request->user()?->id,
                'session_id' => $request->session_id,
                'payment_status' => $session->payment_status,
            ]);

            return response()->json([
                'status' => 'success',
                'data' => [
                    'payment_status' => $session->payment_status,    // "paid", "unpaid", "no_payment_required"
                    'session_id' => $session->id,
                    'subscription_id' => $session->subscription,      // null until paid
                    'customer_id' => $session->customer,
                ],
            ]);
        } catch (Throwable $e) {
            Log::error('Verify checkout session error', [
                'user_id' => $request->user()?->id,
                'session_id' => $request->session_id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'status' => 'error',
                'error_code' => 'SESSION_VERIFICATION_FAILED',
                'message' => 'Failed to verify session.',
            ], 400);
        }
    }
}