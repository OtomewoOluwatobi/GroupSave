<?php

namespace App\Services;

use App\Models\Referral;
use App\Models\User;
use App\Notifications\ReferralBonusNotification;
use App\Notifications\ReferralSignupNotification;
use App\Services\PointsService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ReferralService
{
    /**
     * Points awarded per successful referral
     */
    public const POINTS_PER_REFERRAL = 50;

    /**
     * Milestone thresholds for achievements
     */
    public const MILESTONES = [
        50 => 'Bronze Referrer',
        100 => 'Silver Referrer',
        250 => 'Gold Referrer',
        500 => 'Platinum Referrer',
    ];

    /**
     * Process referral when a new user signs up with a referral code
     * Note: This method does NOT send notifications - caller must handle notifications after transaction commits
     * 
     * @param User $newUser The newly registered user
     * @param string $referralCode The referral code used
     * @param bool $sendNotification Whether to send notification (set false if inside a transaction)
     * @return Referral|null
     */
    public function processReferral(User $newUser, string $referralCode, bool $sendNotification = true): ?Referral
    {
        $referrer = User::where('referral_code', $referralCode)->first();

        if (!$referrer || $referrer->id === $newUser->id) {
            Log::info('Invalid referral code or self-referral attempt', [
                'code' => $referralCode,
                'new_user_id' => $newUser->id,
            ]);
            return null;
        }

        // Update the referred_by field
        $newUser->update(['referred_by' => $referrer->id]);

        // Create referral record (pending until user completes certain actions)
        $referral = Referral::create([
            'referrer_id' => $referrer->id,
            'referred_id' => $newUser->id,
            'points_awarded' => 0,
            'status' => Referral::STATUS_PENDING,
        ]);

        Log::info('Referral record created', [
            'referrer_id' => $referrer->id,
            'referred_id' => $newUser->id,
            'referral_id' => $referral->id,
        ]);

        // Only send notification if not inside a parent transaction
        if ($sendNotification) {
            NotificationService::send($referrer, new ReferralSignupNotification($newUser));
            Log::info('Referral signup notification sent', ['referrer_id' => $referrer->id]);
        }

        return $referral;
    }

    /**
     * Activate referral and award points (e.g., when referred user verifies email or joins a group)
     */
    public function activateReferral(User $referredUser): bool
    {
        $referral = Referral::where('referred_id', $referredUser->id)
            ->where('status', Referral::STATUS_PENDING)
            ->first();

        if (!$referral) {
            return false;
        }

        return DB::transaction(function () use ($referral) {
            // Activate the referral
            $referral->update([
                'status' => Referral::STATUS_ACTIVE,
                'points_awarded' => self::POINTS_PER_REFERRAL,
                'activated_at' => now(),
            ]);

            // Award points to referrer via PointsService (also writes to point_transactions)
            $referrer = $referral->referrer;
            PointsService::award(
                $referrer,
                PointsService::ACTION_REFERRAL,
                'Refer a friend \u2014 ' . $referral->referred->name,
                ['referred_user' => $referral->referred->name]
            );

            // Check for milestone achievements
            $this->checkMilestones($referrer);

            // Notify referrer about bonus points
            NotificationService::send($referrer, new ReferralBonusNotification(
                $referral->referred,
                self::POINTS_PER_REFERRAL
            ));

            Log::info('Referral activated', [
                'referral_id' => $referral->id,
                'referrer_id' => $referrer->id,
                'points_awarded' => self::POINTS_PER_REFERRAL,
            ]);

            return true;
        });
    }

    /**
     * Check and notify about milestone achievements
     */
    protected function checkMilestones(User $user): void
    {
        $points = $user->pointTransactions()
            ->where('action', PointsService::ACTION_REFERRAL)
            ->sum('points');

        $previousPoints = $points - PointsService::REFERRAL;

        foreach (self::MILESTONES as $threshold => $title) {
            if ($points >= $threshold && $previousPoints < $threshold) {
                Log::info("User {$user->id} reached milestone: {$title}", [
                    'user_id'   => $user->id,
                    'milestone' => $title,
                    'points'    => $points,
                ]);
                // TODO: Add milestone notification
                break;
            }
        }
    }

    /**
     * Get referral history for a user
     */
    public function getReferralHistory(User $user): array
    {
        $referrals = $user->referrals()
            ->with('referred:id,name,email,created_at')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($referral) {
                return [
                    'id' => $referral->id,
                    'referred_user' => [
                        'name' => $referral->referred->name,
                        'initials' => $this->getInitials($referral->referred->name),
                    ],
                    'points_awarded' => $referral->points_awarded,
                    'status' => $referral->status,
                    'date' => $referral->created_at->format('d M Y'),
                ];
            });

        return $referrals->toArray();
    }

    /**
     * Get user initials from name
     */
    protected function getInitials(string $name): string
    {
        $words = explode(' ', trim($name));
        $initials = '';

        foreach (array_slice($words, 0, 2) as $word) {
            $initials .= strtoupper(substr($word, 0, 1));
        }

        return $initials;
    }

    /**
     * Get complete referral dashboard data
     */
    public function getDashboardData(User $user): array
    {
        $stats = $user->getReferralStats();

        // Calculate referral-specific points from point_transactions
        $currentPoints = $user->pointTransactions()
            ->where('action', PointsService::ACTION_REFERRAL)
            ->sum('points');
        $nextMilestone = null;
        $progressPercentage = 0;

        foreach (self::MILESTONES as $threshold => $title) {
            if ($currentPoints < $threshold) {
                $nextMilestone = $threshold;
                $progressPercentage = ($currentPoints / $threshold) * 100;
                break;
            }
        }

        return [
            'referral_code' => $user->referral_code,
            'stats' => [
                'active' => $stats['active_referrals'],
                'pending' => $stats['pending_referrals'],
                'total_points' => $stats['total_points'],
            ],
            'earnings_overview' => [
                'total_points'       => $currentPoints,
                'points_per_referral' => PointsService::REFERRAL,
            ],
            'milestone' => [
                'next_target' => $nextMilestone,
                'current_points' => $currentPoints,
                'points_to_go' => $nextMilestone ? $nextMilestone - $currentPoints : 0,
                'progress_percentage' => round($progressPercentage, 1),
            ],
            'history' => $this->getReferralHistory($user),
        ];
    }

    /**
     * Validate referral code
     */
    public function validateReferralCode(string $code): ?User
    {
        return User::where('referral_code', $code)
            ->where('status', 'active')
            ->first();
    }

    /**
     * Expire old pending referrals (can be called via scheduled job)
     */
    public function expirePendingReferrals(int $daysOld = 30): int
    {
        $expiredCount = Referral::pending()
            ->where('created_at', '<', now()->subDays($daysOld))
            ->update([
                'status' => Referral::STATUS_EXPIRED,
            ]);

        Log::info("Expired {$expiredCount} pending referrals older than {$daysOld} days");

        return $expiredCount;
    }
}
