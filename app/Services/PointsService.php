<?php

namespace App\Services;

use App\Models\PointTransaction;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PointsService
{
    // ── Point values ────────────────────────────────────────────────────────────
    const DAILY_LOGIN            = 5;
    const LOGIN_STREAK_BONUS     = 50;   // awarded at 7-day streak
    const LOGIN_STREAK_THRESHOLD = 7;
    const PAY_ON_TIME            = 20;
    const PAY_EARLY              = 10;
    const REFERRAL               = 50;
    const INVITE_ACCEPTED        = 15;
    const CYCLE_COMPLETED        = 100;
    const PROFILE_COMPLETED      = 25;
    const IDENTITY_VERIFIED      = 30;
    const TRUST_REVIEW           = 10;

    // ── Redemption costs ────────────────────────────────────────────────────────
    const REDEEM_EXTRA_GROUP_SLOT = 300;

    // ── Actions ─────────────────────────────────────────────────────────────────
    const ACTION_DAILY_LOGIN        = 'daily_login';
    const ACTION_STREAK_BONUS       = 'login_streak_bonus';
    const ACTION_PAY_ON_TIME        = 'pay_on_time';
    const ACTION_PAY_EARLY          = 'pay_early';
    const ACTION_REFERRAL           = 'referral';
    const ACTION_INVITE_ACCEPTED    = 'invite_accepted';
    const ACTION_CYCLE_COMPLETED    = 'cycle_completed';
    const ACTION_PROFILE_COMPLETED  = 'profile_completed';
    const ACTION_IDENTITY_VERIFIED  = 'identity_verified';
    const ACTION_TRUST_REVIEW       = 'trust_review';
    const ACTION_REDEEM_GROUP_SLOT  = 'redemption_extra_group_slot';

    /**
     * Award points to a user and log the transaction.
     */
    public static function award(User $user, string $action, string $description, array $metadata = []): void
    {
        $points = self::pointsFor($action);
        if ($points <= 0) {
            return;
        }

        DB::transaction(function () use ($user, $action, $description, $metadata, $points) {
            $user->increment('points', $points);

            PointTransaction::create([
                'user_id'     => $user->id,
                'action'      => $action,
                'points'      => $points,
                'description' => $description,
                'metadata'    => $metadata ?: null,
            ]);
        });

        Log::info('Points awarded', ['user_id' => $user->id, 'action' => $action, 'points' => $points]);
    }

    /**
     * Handle daily login: award points once per day and manage streak.
     */
    public static function recordLogin(User $user): void
    {
        $today     = now()->toDateString();
        $yesterday = now()->subDay()->toDateString();
        $lastLogin = $user->last_login_date?->toDateString();

        // Already logged in today — nothing to do
        if ($lastLogin === $today) {
            return;
        }

        DB::transaction(function () use ($user, $today, $yesterday, $lastLogin) {
            // Award daily login points
            $user->increment('points', self::DAILY_LOGIN);
            PointTransaction::create([
                'user_id'     => $user->id,
                'action'      => self::ACTION_DAILY_LOGIN,
                'points'      => self::DAILY_LOGIN,
                'description' => 'Daily login',
            ]);

            // Update streak
            if ($lastLogin === $yesterday) {
                $user->increment('login_streak');
            } else {
                $user->login_streak = 1;
            }

            $user->last_login_date = $today;
            $user->save();

            // Award streak bonus every LOGIN_STREAK_THRESHOLD days
            if ($user->login_streak % self::LOGIN_STREAK_THRESHOLD === 0) {
                $user->increment('points', self::LOGIN_STREAK_BONUS);
                PointTransaction::create([
                    'user_id'     => $user->id,
                    'action'      => self::ACTION_STREAK_BONUS,
                    'points'      => self::LOGIN_STREAK_BONUS,
                    'description' => "{$user->login_streak}-day login streak bonus",
                ]);
            }
        });
    }

    /**
     * Redeem points for a reward.
     * Returns true on success, false if insufficient balance.
     */
    public static function redeem(User $user, string $action): bool
    {
        $cost = self::redemptionCost($action);
        if ($cost <= 0) {
            return false;
        }

        if ($user->points < $cost) {
            return false;
        }

        DB::transaction(function () use ($user, $action, $cost) {
            $user->decrement('points', $cost);

            if ($action === self::ACTION_REDEEM_GROUP_SLOT) {
                $user->increment('extra_group_slots');
                $description = 'Redeemed — extra group slot';
            } else {
                $description = 'Redeemed points';
            }

            PointTransaction::create([
                'user_id'     => $user->id,
                'action'      => $action,
                'points'      => -$cost,
                'description' => $description,
            ]);
        });

        return true;
    }

    /**
     * Map action to its point value.
     */
    public static function pointsFor(string $action): int
    {
        return match ($action) {
            self::ACTION_DAILY_LOGIN       => self::DAILY_LOGIN,
            self::ACTION_STREAK_BONUS      => self::LOGIN_STREAK_BONUS,
            self::ACTION_PAY_ON_TIME       => self::PAY_ON_TIME,
            self::ACTION_PAY_EARLY         => self::PAY_EARLY,
            self::ACTION_REFERRAL          => self::REFERRAL,
            self::ACTION_INVITE_ACCEPTED   => self::INVITE_ACCEPTED,
            self::ACTION_CYCLE_COMPLETED   => self::CYCLE_COMPLETED,
            self::ACTION_PROFILE_COMPLETED => self::PROFILE_COMPLETED,
            self::ACTION_IDENTITY_VERIFIED => self::IDENTITY_VERIFIED,
            self::ACTION_TRUST_REVIEW      => self::TRUST_REVIEW,
            default                        => 0,
        };
    }

    /**
     * Map redemption action to its cost.
     */
    public static function redemptionCost(string $action): int
    {
        return match ($action) {
            self::ACTION_REDEEM_GROUP_SLOT => self::REDEEM_EXTRA_GROUP_SLOT,
            default                        => 0,
        };
    }

    /**
     * Returns a catalogue of all earnable actions with their point values.
     * Used by the frontend points screen.
     */
    public static function catalogue(): array
    {
        return [
            'daily_actions' => [
                ['action' => self::ACTION_DAILY_LOGIN,      'label' => 'Daily login',       'description' => 'Open the app each day',              'points' => self::DAILY_LOGIN],
                ['action' => self::ACTION_PAY_ON_TIME,      'label' => 'Pay on time',        'description' => 'Submit your contribution by the due date', 'points' => self::PAY_ON_TIME],
                ['action' => self::ACTION_PAY_EARLY,        'label' => 'Pay early',          'description' => 'Contribute before the due date',     'points' => self::PAY_EARLY],
            ],
            'streak' => [
                ['action' => self::ACTION_STREAK_BONUS,     'label' => 'Weekly login streak','description' => '7-day streak = 50 bonus pts',        'points' => self::LOGIN_STREAK_BONUS, 'threshold' => self::LOGIN_STREAK_THRESHOLD],
            ],
            'community_actions' => [
                ['action' => self::ACTION_REFERRAL,         'label' => 'Refer a friend',     'description' => 'They join and create their first group', 'points' => self::REFERRAL],
                ['action' => self::ACTION_INVITE_ACCEPTED,  'label' => 'Invite member to group', 'description' => 'Per accepted invitation',        'points' => self::INVITE_ACCEPTED],
                ['action' => self::ACTION_CYCLE_COMPLETED,  'label' => 'Complete a savings cycle', 'description' => 'Group reaches its target amount','points' => self::CYCLE_COMPLETED],
            ],
            'profile_trust' => [
                ['action' => self::ACTION_PROFILE_COMPLETED,'label' => 'Complete your profile', 'description' => 'Name, photo, bank account linked','points' => self::PROFILE_COMPLETED],
                ['action' => self::ACTION_IDENTITY_VERIFIED,'label' => 'Verify your identity', 'description' => 'Upload a valid ID document',       'points' => self::IDENTITY_VERIFIED],
                ['action' => self::ACTION_TRUST_REVIEW,     'label' => 'Leave a trust review', 'description' => 'Rate a fellow group member',        'points' => self::TRUST_REVIEW],
            ],
            'redemptions' => [
                ['action' => self::ACTION_REDEEM_GROUP_SLOT, 'label' => 'Extra group slot',  'description' => 'Unlock 1 additional group slot',     'cost' => self::REDEEM_EXTRA_GROUP_SLOT],
            ],
        ];
    }
}
