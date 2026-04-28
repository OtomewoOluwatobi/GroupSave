<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Trin4ik\LaravelExpoPush\Channels\ExpoPushChannel;
use Trin4ik\LaravelExpoPush\ExpoPush;

class LoginNotification extends Notification
{
    use Queueable;

    private string $ipAddress;
    private string $userAgent;
    private string $loginTime;
    private bool $isNewDevice;

    /**
     * Create a new notification instance.
     */
    public function __construct(string $ipAddress, string $userAgent, bool $isNewDevice = false)
    {
        $this->ipAddress = $ipAddress;
        $this->userAgent = $userAgent;
        $this->loginTime = now()->toDateTimeString();
        $this->isNewDevice = $isNewDevice;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable): array
    {
        $channels = ['database'];

        if ($this->isNewDevice) {
            $channels[] = 'mail';
        }

        if (!empty($notifiable->expo_push_token)) {
            $channels[] = ExpoPushChannel::class;
        }

        return $channels;
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->view('emails.login', [
                'userName' => $notifiable->name,
                'loginTime' => $this->loginTime,
                'ipAddress' => $this->ipAddress,
                'device' => $this->parseUserAgent($this->userAgent),
            ])
            ->subject('New Login to Your ' . config('app.name') . ' Account');
    }

    /**
     * Get the database representation of the notification.
     */
    public function toDatabase($notifiable): array
    {
        return [
            'type' => 'login',
            'message' => 'New login detected',
            'ip_address' => $this->ipAddress,
            'user_agent' => $this->userAgent,
            'device' => $this->parseUserAgent($this->userAgent),
            'login_time' => $this->loginTime,
            'is_new_device' => $this->isNewDevice,
        ];
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray($notifiable): array
    {
        return [
            'type' => 'login',
            'message' => 'Login successful',
            'login_time' => $this->loginTime,
        ];
    }

    /**
     * Get the Expo push notification representation.
     */
    public function toExpoPush($notifiable): ExpoPush
    {
        return ExpoPush::create()
            ->title('New Login Detected')
            ->body('A login was made to your account from ' . $this->parseUserAgent($this->userAgent) . '.')
            ->data([
                'type'       => 'login',
                'login_time' => $this->loginTime,
            ]);
    }

    /**
     * Parse user agent to get readable device info.
     */
    private function parseUserAgent(string $userAgent): string
    {
        // Simple UA parsing - compatible with PHP 7.x
        if (strpos($userAgent, 'iPhone') !== false) {
            return 'iPhone';
        } elseif (strpos($userAgent, 'Android') !== false) {
            return 'Android Device';
        } elseif (strpos($userAgent, 'Windows') !== false) {
            return 'Windows PC';
        } elseif (strpos($userAgent, 'Mac') !== false) {
            return 'Mac';
        } elseif (strpos($userAgent, 'Linux') !== false) {
            return 'Linux';
        }
        return 'Unknown Device';
    }
}
