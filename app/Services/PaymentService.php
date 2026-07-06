<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\Plan;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Laravel\Cashier\Exceptions\IncompletePayment;
use Stripe\Exception\ApiErrorException;
use Throwable;

class PaymentService
{
    /**
     * Create a subscription for a user on a plan
     *
     * @param User $user
     * @param Plan $plan
     * @param string $paymentMethodId
     * @return array
     * @throws Throwable
     */
    public function createSubscription(User $user, Plan $plan, string $paymentMethodId): array
    {
        try {
            // Validate plan is not free
            if ($plan->billing === 'free_forever') {
                return [
                    'success' => false,
                    'message' => 'Free plans should be joined via the plan endpoint.',
                    'code' => 422,
                ];
            }

            // Validate Stripe price ID exists
            if (!$plan->stripe_price_id) {
                return [
                    'success' => false,
                    'message' => 'This plan is not configured for Stripe payments.',
                    'code' => 400,
                ];
            }

            // Ensure user has Stripe customer ID
            if (!$user->hasStripeId()) {
                $user->createAsStripeCustomer();
            }

            // Update payment method
            $user->updateDefaultPaymentMethod($paymentMethodId);

            // Check for existing subscription
            $activeSubscription = $user->subscription('default');

            if ($activeSubscription && $activeSubscription->active()) {
                // Check if already on this plan
                $alreadyOnTargetPrice = $activeSubscription->items()
                    ->where('stripe_price', $plan->stripe_price_id)
                    ->exists();

                if ($alreadyOnTargetPrice) {
                    return [
                        'success' => true,
                        'message' => 'You are already subscribed to this plan.',
                        'subscription' => $activeSubscription,
                        'code' => 200,
                    ];
                }

                // Swap to new plan
                $activeSubscription->swap($plan->stripe_price_id);
                $subscription = $activeSubscription->fresh();
            } else {
                // Create new subscription
                $subscription = $user->newSubscription('default', $plan->stripe_price_id)
                    ->create($paymentMethodId);
            }

            // Record payment in database
            $this->recordPayment(
                user: $user,
                plan: $plan,
                status: 'succeeded',
                stripeResponse: $subscription,
                paymentType: 'subscription'
            );

            // Activate plan entitlements
            app(BillingEntitlementService::class)->activatePlan($user, $plan, $subscription->ends_at);

            return [
                'success' => true,
                'message' => 'Subscription is active.',
                'subscription' => $subscription,
                'code' => 200,
            ];
        } catch (IncompletePayment $e) {
            Log::warning('Incomplete payment for subscription', [
                'user_id' => $user->id,
                'plan_id' => $plan->id,
                'error' => $e->getMessage(),
            ]);

            // Record failed payment
            $this->recordPayment(
                user: $user,
                plan: $plan,
                status: 'failed',
                failureReason: 'Additional authentication required',
                stripeResponse: $e->payment->asStripePaymentIntent()
            );

            return [
                'success' => false,
                'message' => 'Additional authentication is required to complete payment.',
                'payment' => [
                    'payment_intent' => $e->payment->asStripePaymentIntent(),
                ],
                'code' => 402,
            ];
        } catch (ApiErrorException $e) {
            Log::error('Stripe API error during subscription', [
                'user_id' => $user->id,
                'plan_id' => $plan->id,
                'error' => $e->getMessage(),
                'error_code' => $e->getStripeCode(),
            ]);

            // Record failed payment
            $this->recordPayment(
                user: $user,
                plan: $plan,
                status: 'failed',
                failureReason: $e->getMessage()
            );

            return [
                'success' => false,
                'message' => 'Failed to create subscription: ' . $e->getMessage(),
                'code' => 400,
            ];
        } catch (Throwable $e) {
            Log::error('Unexpected error during subscription', [
                'user_id' => $user->id,
                'plan_id' => $plan->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Record failed payment
            $this->recordPayment(
                user: $user,
                plan: $plan,
                status: 'failed',
                failureReason: $e->getMessage()
            );

            return [
                'success' => false,
                'message' => 'Failed to create subscription. Please try again.',
                'code' => 500,
            ];
        }
    }

    /**
     * Record a payment in the database
     */
    private function recordPayment(
        User $user,
        Plan $plan,
        string $status,
        mixed $stripeResponse = null,
        string $paymentType = 'subscription',
        ?string $failureReason = null
    ): Payment {
        $paymentData = [
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'amount' => $plan->price,
            'currency' => $plan->currency ?? 'USD',
            'status' => $status,
            'payment_type' => $paymentType,
            'stripe_response' => $stripeResponse ? (array) $stripeResponse : null,
            'failure_reason' => $failureReason,
        ];

        // Add timestamps for status changes
        if ($status === 'succeeded') {
            $paymentData['succeeded_at'] = now();
        } elseif ($status === 'failed') {
            $paymentData['failed_at'] = now();
        }

        // Extract Stripe IDs if available
        if (is_object($stripeResponse)) {
            if (isset($stripeResponse->id)) {
                if (str_starts_with($stripeResponse->id, 'pi_')) {
                    $paymentData['stripe_payment_intent_id'] = $stripeResponse->id;
                }
                if (str_starts_with($stripeResponse->id, 'ch_')) {
                    $paymentData['stripe_charge_id'] = $stripeResponse->id;
                }
                if (str_starts_with($stripeResponse->id, 'in_')) {
                    $paymentData['stripe_invoice_id'] = $stripeResponse->id;
                }
            }
        }

        return Payment::create($paymentData);
    }

    /**
     * Handle Stripe webhook payment succeeded event
     */
    public function handlePaymentSucceeded(array $payload): void
    {
        try {
            $paymentIntentId = $payload['id'] ?? null;

            if (!$paymentIntentId) {
                Log::warning('Payment succeeded webhook missing payment intent ID', $payload);
                return;
            }

            $payment = Payment::where('stripe_payment_intent_id', $paymentIntentId)
                ->orWhere('stripe_charge_id', $paymentIntentId)
                ->first();

            if (!$payment) {
                Log::warning('Payment record not found for webhook', ['stripe_id' => $paymentIntentId]);
                return;
            }

            $payment->update([
                'status' => 'succeeded',
                'succeeded_at' => now(),
                'stripe_response' => $payload,
            ]);

            Log::info('Payment marked as succeeded', ['payment_id' => $payment->id]);
        } catch (Throwable $e) {
            Log::error('Error handling payment succeeded webhook', [
                'error' => $e->getMessage(),
                'payload' => $payload,
            ]);
        }
    }

    /**
     * Handle Stripe webhook payment failed event
     */
    public function handlePaymentFailed(array $payload): void
    {
        try {
            $paymentIntentId = $payload['id'] ?? null;

            if (!$paymentIntentId) {
                Log::warning('Payment failed webhook missing payment intent ID', $payload);
                return;
            }

            $payment = Payment::where('stripe_payment_intent_id', $paymentIntentId)
                ->orWhere('stripe_charge_id', $paymentIntentId)
                ->first();

            if (!$payment) {
                Log::warning('Payment record not found for webhook', ['stripe_id' => $paymentIntentId]);
                return;
            }

            $failureMessage = $payload['last_payment_error']['message'] ?? 'Unknown error';

            $payment->update([
                'status' => 'failed',
                'failed_at' => now(),
                'failure_reason' => $failureMessage,
                'stripe_response' => $payload,
            ]);

            Log::warning('Payment marked as failed', [
                'payment_id' => $payment->id,
                'reason' => $failureMessage,
            ]);
        } catch (Throwable $e) {
            Log::error('Error handling payment failed webhook', [
                'error' => $e->getMessage(),
                'payload' => $payload,
            ]);
        }
    }

    /**
     * Get payment history for a user
     */
    public function getUserPaymentHistory(User $user, int $limit = 50): array
    {
        $payments = Payment::where('user_id', $user->id)
            ->with('plan')
            ->orderByDesc('created_at')
            ->paginate($limit);

        return [
            'total' => $payments->total(),
            'per_page' => $payments->per_page(),
            'current_page' => $payments->current_page(),
            'last_page' => $payments->last_page(),
            'payments' => $payments->items(),
        ];
    }
}
