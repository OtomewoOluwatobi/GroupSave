<?php

namespace App\Notifications;

use App\Models\User;
use App\Mail\Onboarding;
use Illuminate\Notifications\Notification;

class UserOnboardingNotification extends Notification
{
    private string $userEmail;
    private string $userName;
    private string $verificationCode;

    /**
     * Create a new notification instance.
     */
    public function __construct(User $user, string $verificationCode)
    {
        // Store only scalar values - never store User model
        $this->userEmail = $user->email;
        $this->userName = $user->name;
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
        return new Onboarding($this->userName, $this->userEmail, $this->verificationCode);
    }
}