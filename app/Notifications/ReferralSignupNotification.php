<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReferralSignupNotification extends Notification
{
    use Queueable;

    public User $referredUser;

    public function __construct(User $referredUser)
    {
        $this->referredUser = $referredUser;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable): array
    {
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->view('emails.referral-signup', [
                'userName' => $notifiable->name,
                'referredUserName' => $this->referredUser->name,
            ])
            ->subject('Someone Used Your Referral Code!');
    }

    /**
     * Get the database representation of the notification.
     */
    public function toDatabase($notifiable): array
    {
        return [
            'type' => 'referral_signup',
            'title' => 'New Referral Signup!',
            'message' => "{$this->referredUser->name} signed up using your referral code. Points will be awarded once they verify their account.",
            'referred_user_id' => $this->referredUser->id,
            'referred_user_name' => $this->referredUser->name,
        ];
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray($notifiable): array
    {
        return $this->toDatabase($notifiable);
    }
}
