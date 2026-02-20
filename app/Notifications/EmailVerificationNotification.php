<?php

namespace App\Notifications;

use App\Models\User;
use App\Mail\EmailVerification;
use Illuminate\Notifications\Notification;

class EmailVerificationNotification extends Notification
{
    private string $userEmail;
    private string $userName;
    private string $newEmail;
    private string $verificationCode;

    /**
     * Create a new notification instance.
     */
    public function __construct(User $user, string $verificationCode, string $newEmail = null)
    {
        // Store only scalar values - never store User model
        $this->userEmail = $newEmail ?? $user->email;
        $this->userName = $user->name;
        $this->newEmail = $newEmail ?? $user->email;
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
        return new EmailVerification($this->userName, $this->userEmail, $this->verificationCode);
    }
}
