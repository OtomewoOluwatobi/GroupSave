<?php

namespace App\Notifications;

use App\Models\User;

class ReferralBonusNotification
{
    public User $referredUser;
    public int $points;

    public function __construct(User $referredUser, int $points)
    {
        $this->referredUser = $referredUser;
        $this->points = $points;
    }

    /**
     * Get the notification's data array.
     */
    public function toArray(): array
    {
        return [
            'title' => 'Referral Points Earned!',
            'message' => "You earned {$this->points} points! {$this->referredUser->name} has verified their account.",
            'type' => 'referral_bonus',
            'data' => [
                'referred_user_id' => $this->referredUser->id,
                'referred_user_name' => $this->referredUser->name,
                'points' => $this->points,
            ],
        ];
    }
}
