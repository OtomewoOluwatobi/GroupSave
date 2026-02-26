<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\URL;

class VerifyEmailNotification extends BaseNotification
{
    public function via($notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $verificationUrl = $this->verificationUrl($notifiable);
        $expiresIn = Config::get('auth.verification.expire', 60);

        return (new MailMessage)
            ->view('emails.verify-email', [
                'userName' => $notifiable->name,
                'verificationUrl' => $verificationUrl,
                'expiresIn' => $expiresIn,
            ])
            ->subject('Verify Your Email Address - GroupSave');
    }

    /**
     * Get the database representation of the notification (in-app activity).
     */
    public function toDatabase($notifiable): array
    {
        return [
            'type' => 'email_verification',
            'message' => 'Please verify your email address to complete your account setup',
            'action_url' => '/email/verify',
        ];
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray($notifiable): array
    {
        return [
            'type' => 'email_verification',
            'message' => 'Verify your email address',
        ];
    }

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
