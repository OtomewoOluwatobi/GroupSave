<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;

class PasswordResetNotification extends BaseNotification
{
    private string $userEmail;
    private string $userName;
    private string $resetCode;
    private string $userId;

    /**
     * Create a new notification instance.
     */
    public function __construct(string $userName, string $userEmail, string $resetCode, int $userId)
    {
        parent::__construct();
        // Store only scalar values - never store User model
        $this->userName = $userName;
        $this->userEmail = $userEmail;
        $this->resetCode = $resetCode;
        $this->userId = $userId;
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
            ->view('emails.password-reset', [
                'name' => $this->userName,
                'email' => $this->userEmail,
                'resetCode' => $this->resetCode,
            ])
            ->subject('Password Reset Request - ' . config('app.name'));
    }

    /**
     * Get the database representation of the notification (in-app activity).
     */
    public function toDatabase($notifiable): array
    {
        return [
            'type' => 'password_reset',
            'message' => 'A password reset request was initiated for your account',
            'code' => $this->resetCode,
            'action_url' => '/auth/reset-password',
        ];
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray($notifiable): array
    {
        return [
            'type' => 'password_reset',
            'message' => 'Password reset request received',
            'code' => $this->resetCode,
        ];
    }
}
