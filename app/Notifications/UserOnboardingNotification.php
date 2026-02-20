<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class UserOnboardingNotification extends Notification implements ShouldQueue
{
    use Queueable;

    private string $userEmail;
    private string $userName;
    private string $verifyLink;

    /**
     * Create a new notification instance.
     */
    public function __construct(string $userName, string $userEmail, string $verificationCode)
    {
        // Store only scalar values - never store User model
        $this->userName = $userName;
        $this->userEmail = $userEmail;
        $this->verifyLink = config('app.frontend_url') . '/verify-email?code=' . $verificationCode;
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
            ->view('emails.onboarding', [
                'name' => $this->userName,
                'verifyLink' => $this->verifyLink,
            ])
            ->subject('Welcome to GroupSave!');
    }
}