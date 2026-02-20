<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class PasswordResetNotification extends Notification implements ShouldQueue
{
    use Queueable;

    private string $userEmail;
    private string $userName;
    private string $resetCode;

    /**
     * Create a new notification instance.
     */
    public function __construct(string $userName, string $userEmail, string $resetCode)
    {
        // Store only scalar values - never store User model
        $this->userName = $userName;
        $this->userEmail = $userEmail;
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
        return (new \Illuminate\Notifications\Messages\MailMessage)
            ->view('emails.password-reset', [
                'name' => $this->userName,
                'email' => $this->userEmail,
                'resetCode' => $this->resetCode,
            ])
            ->subject('Password Reset Request - GroupSave');
    }
}
