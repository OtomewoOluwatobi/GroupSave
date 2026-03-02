<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReferralBonusNotification extends Notification
{
    use Queueable;

    public User $referredUser;
    public int $points;

    public function __construct(User $referredUser, int $points)
    {
        $this->referredUser = $referredUser;
        $this->points = $points;
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
            ->subject('You Earned Referral Points!')
            ->greeting('Congratulations ' . $notifiable->name . '!')
            ->line("You earned {$this->points} referral points!")
            ->line("{$this->referredUser->name} has verified their account.")
            ->line('Keep referring friends to earn more rewards.')
            ->action('View Your Points', url('/referrals'))
            ->salutation('Happy saving, GroupSave Team');
    }

    /**
     * Get the database representation of the notification.
     */
    public function toDatabase($notifiable): array
    {
        return [
            'type' => 'referral_bonus',
            'title' => 'Referral Points Earned!',
            'message' => "You earned {$this->points} points! {$this->referredUser->name} has verified their account.",
            'referred_user_id' => $this->referredUser->id,
            'referred_user_name' => $this->referredUser->name,
            'points' => $this->points,
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
