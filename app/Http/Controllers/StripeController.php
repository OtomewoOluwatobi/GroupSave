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
    public function handleCheckoutSuccess(Request $request)
    {
        $sessionId = $request->query('session_id');

        if (!$sessionId) {
            return $this->renderPaymentStatus('error', 'Payment Error', 'Missing session ID. Please contact support.');
        }

        try {
            // Retrieve session from Stripe to confirm payment
            $session = \Stripe\Checkout\Session::retrieve($sessionId);

            Log::info('Checkout success - session retrieved', [
                'session_id' => $sessionId,
                'payment_status' => $session->payment_status,
            ]);

            return $this->renderPaymentStatus(
                'success',
                'Payment Successful',
                'Your payment was successful! Your plan is being activated. You can close this window and return to the app.',
                $sessionId
            );
        } catch (Throwable $e) {
            Log::error('Checkout success handler error', [
                'session_id' => $sessionId,
                'error' => $e->getMessage(),
            ]);

            return $this->renderPaymentStatus('error', 'Payment Error', 'Failed to retrieve payment status. Please try again or contact support.');
        }
    }

    /**
     * Render payment status as HTML
     */
    private function renderPaymentStatus($status, $title, $message, $sessionId = null)
    {
        $html = <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>$title</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', 'Oxygen', 'Ubuntu', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            max-width: 400px;
            width: 100%;
            padding: 40px;
            text-align: center;
        }
        .icon {
            font-size: 60px;
            margin-bottom: 20px;
        }
        .success .icon::before { content: '✓'; color: #10b981; }
        .error .icon::before { content: '✕'; color: #ef4444; }
        .cancelled .icon::before { content: '○'; color: #f59e0b; }
        h1 {
            font-size: 24px;
            margin-bottom: 10px;
            color: #1f2937;
        }
        .status-message {
            font-size: 14px;
            color: #6b7280;
            margin-bottom: 30px;
            line-height: 1.6;
        }
        .status-badge {
            display: inline-block;
            padding: 8px 16px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
            margin-bottom: 20px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .success .status-badge { background: #d1fae5; color: #059669; }
        .error .status-badge { background: #fee2e2; color: #dc2626; }
        .cancelled .status-badge { background: #fef3c7; color: #b45309; }
        .session-info {
            background: #f3f4f6;
            border-radius: 6px;
            padding: 12px;
            margin-bottom: 20px;
            font-size: 12px;
            color: #4b5563;
            word-break: break-all;
        }
        .session-label { font-weight: 600; color: #1f2937; display: block; margin-bottom: 4px; }
        .instructions {
            background: #eff6ff;
            border-left: 4px solid #3b82f6;
            padding: 12px;
            text-align: left;
            border-radius: 4px;
            font-size: 13px;
            color: #1e40af;
            margin-bottom: 20px;
            line-height: 1.5;
        }
        .success .instructions { background: #ecfdf5; border-left-color: #10b981; color: #065f46; }
        .error .instructions { background: #fef2f2; border-left-color: #ef4444; color: #7f1d1d; }
        .button { padding: 12px 24px; border-radius: 6px; border: none; font-size: 14px; font-weight: 600; cursor: pointer; background: #667eea; color: white; transition: all 0.3s ease; }
        .button:hover { background: #5a67d8; }
    </style>
</head>
<body>
    <div class="container $status">
        <div class="icon"></div>
        <h1>$title</h1>
        <div class="status-badge">{$status}</div>
        <div class="status-message">$message</div>
HTML;

        if ($status === 'success') {
            $html .= <<<HTML
        <div class="instructions">
            ✓ Your payment was processed successfully<br>
            ✓ Your new plan is being activated<br>
            ✓ You can close this window now
        </div>
HTML;
        } elseif ($status === 'cancelled') {
            $html .= <<<HTML
        <div class="instructions">
            ○ Payment was cancelled<br>
            ○ No charge was made<br>
            ○ You can try again from the app
        </div>
HTML;
        } else {
            $html .= <<<HTML
        <div class="instructions">
            ✕ Payment could not be completed<br>
            ✕ Please verify your payment details<br>
            ✕ Contact support if issue persists
        </div>
HTML;
        }

        if ($sessionId) {
            $html .= <<<HTML
        <div class="session-info">
            <span class="session-label">Session ID:</span>
            $sessionId
        </div>
HTML;
        }

        $html .= <<<HTML
        <button class="button" onclick="window.close()">Close Window</button>
    </div>
    <script>
        if ('{$status}' !== 'error') {
            setTimeout(() => { window.close(); }, 5000);
        }
    </script>
</body>
</html>
HTML;

        return response($html)->header('Content-Type', 'text/html; charset=UTF-8');
    }

    /**
     * Handle cancelled Stripe checkout
     * Stripe redirects to this URL if user cancels payment
     * Returns HTML page (browser is open, not mobile app)
     */
    public function handleCheckoutCancel(Request $request)
    {
        $sessionId = $request->query('session_id');

        Log::info('Checkout cancelled', [
            'session_id' => $sessionId,
        ]);

        return $this->renderPaymentStatus(
            'cancelled',
            'Payment Cancelled',
            'Payment was cancelled. You can close this window and return to the app to try again.',
            $sessionId
        );
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