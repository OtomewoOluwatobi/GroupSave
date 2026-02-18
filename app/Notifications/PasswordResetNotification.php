<?php

namespace App\Notifications;

use App\Models\User;
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
        return (new \Illuminate\Mail\Message)
            ->subject('Password Reset Request')
            ->greeting('Hello ' . $this->userName . ',')
            ->line('You requested a password reset. Use the code below to reset your password:')
            ->line('')
            ->line('Reset Code: ' . $this->resetCode)
            ->line('')
            ->line('This code will expire in 15 minutes.')
            ->line('If you did not request a password reset, please ignore this email.')
            ->line('')
            ->line('Enter this code in the GroupSave mobile app to reset your password.')
            ->action('Reset Password', url('/password-reset?code=' . $this->resetCode))
            ->from(config('mail.from.address'));
    }
}
