<?php

namespace App\Notifications;

use App\Models\User;

class ReferralSignupNotification
{
    public User $referredUser;

    public function __construct(User $referredUser)
    {
        $this->referredUser = $referredUser;
    }

    /**
     * Get the notification's data array.
     */
    public function toArray(): array
    {
        return [
            'title' => 'New Referral Signup!',
            'message' => "{$this->referredUser->name} signed up using your referral code. Points will be awarded once they verify their account.",
            'type' => 'referral_signup',
            'data' => [
                'referred_user_id' => $this->referredUser->id,
                'referred_user_name' => $this->referredUser->name,
            ],
        ];
    }
}
