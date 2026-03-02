<?php

namespace App\Services;

use App\Models\Referral;
use App\Models\User;
use App\Notifications\ReferralBonusNotification;
use App\Notifications\ReferralSignupNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ReferralService
{
    /**
     * Points awarded per successful referral
     */
    public const POINTS_PER_REFERRAL = 10;

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
     */
    public function processReferral(User $newUser, string $referralCode): ?Referral
    {
        $referrer = User::where('referral_code', $referralCode)->first();

        if (!$referrer || $referrer->id === $newUser->id) {
            Log::info('Invalid referral code or self-referral attempt', [
                'code' => $referralCode,
                'new_user_id' => $newUser->id,
            ]);
            return null;
        }

        return DB::transaction(function () use ($newUser, $referrer) {
            // Update the referred_by field
            $newUser->update(['referred_by' => $referrer->id]);

            // Create referral record (pending until user completes certain actions)
            $referral = Referral::create([
                'referrer_id' => $referrer->id,
                'referred_id' => $newUser->id,
                'points_awarded' => 0,
                'status' => Referral::STATUS_PENDING,
            ]);

            // Notify referrer about new signup
            NotificationService::send($referrer, new ReferralSignupNotification($newUser));

            Log::info('Referral processed successfully', [
                'referrer_id' => $referrer->id,
                'referred_id' => $newUser->id,
                'referral_id' => $referral->id,
            ]);

            return $referral;
        });
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

            // Award points to referrer
            $referrer = $referral->referrer;
            $referrer->addReferralPoints(self::POINTS_PER_REFERRAL);

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
        $points = $user->referral_points;

        foreach (self::MILESTONES as $threshold => $title) {
            // Check if user just crossed this milestone
            $previousPoints = $points - self::POINTS_PER_REFERRAL;

            if ($points >= $threshold && $previousPoints < $threshold) {
                // User just reached this milestone
                Log::info("User {$user->id} reached milestone: {$title}", [
                    'user_id' => $user->id,
                    'milestone' => $title,
                    'points' => $points,
                ]);

                // TODO: Add milestone notification
                // NotificationService::send($user, new MilestoneAchievedNotification($title, $threshold));
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

        // Calculate progress to next milestone
        $currentPoints = $user->referral_points;
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
                'total_points' => $currentPoints,
                'points_per_referral' => self::POINTS_PER_REFERRAL,
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
