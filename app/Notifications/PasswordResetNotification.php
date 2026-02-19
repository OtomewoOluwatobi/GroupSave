<?php

namespace App\Notifications;

use App\Models\User;
use App\Mail\PasswordReset;
use Illuminate\Notifications\Notification;

class PasswordResetNotification extends Notification
{
    private string $userEmail;
    private string $userName;
    private string $resetCode;

    /**
     * Create a new notification instance.
     */
    public function __construct(User $user, string $resetCode)
    {
        // Store only scalar values - never store User model
        $this->userEmail = $user->email;
        $this->userName = $user->name;
        $this->resetCode = $resetCode;
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
        return new PasswordReset($this->userName, $this->userEmail, $this->resetCode);
    }
}
