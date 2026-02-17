<?php

namespace App\Notifications;

use App\Models\User;
use App\Mail\Onboarding;
use Illuminate\Notifications\Notification;

class UserOnboardingNotification extends Notification
{
    private string $userEmail;
    private User $user;
    private string $verificationCode;

    /**
     * Create a new notification instance.
     */
    public function __construct(User $user, string $verificationCode)
    {
        $this->userEmail = $user->email;  // Store email only
        $this->user = $user;
        $this->verificationCode = $verificationCode;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable)
    {
        return new Onboarding($this->user, $this->verificationCode);
    }
}