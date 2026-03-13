<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\URL;

class UserOnboardingNotification extends BaseNotification
{
    private string $userName;

    /**
     * Create a new notification instance.
     */
    public function __construct()
    {
        parent::__construct();
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
        $verificationUrl = $this->verificationUrl($notifiable);
        $expiresIn = Config::get('auth.verification.expire', 60);

        return (new MailMessage)
            ->view('emails.onboarding', [
                'userName' => $notifiable->name,
                'verificationUrl' => $verificationUrl,
                'expiresIn' => $expiresIn,
            ])
            ->subject('Welcome to ' . config('app.name') . '!');
    }

    /**
     * Get the database representation of the notification (in-app activity).
     */
    public function toDatabase($notifiable): array
    {
        return [
            'type' => 'user_onboarding',
            'message' => 'Welcome to GroupSave! Please verify your email address',
            'action_url' => '/email/verify',
        ];
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray($notifiable): array
    {
        return [
            'type' => 'user_onboarding',
            'message' => 'Welcome to GroupSave!',
        ];
    }

    /**
     * Generate a signed verification URL.
     */
    protected function verificationUrl($notifiable)
    {
        return URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addMinutes(Config::get('auth.verification.expire', 60)),
            [
                'id' => $notifiable->getKey(),
                'hash' => sha1($notifiable->getEmailForVerification()),
            ]
        );
    }
}