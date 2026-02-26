<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

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
        // Only send email for new device logins
        // Database first to ensure notification is saved before attempting email
        return $this->isNewDevice ? ['database', 'mail'] : ['database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New Login to Your GroupSave Account')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('We detected a new login to your GroupSave account.')
            ->line('**Time:** ' . $this->loginTime)
            ->line('**IP Address:** ' . $this->ipAddress)
            ->line('**Device:** ' . $this->parseUserAgent($this->userAgent))
            ->line('If this was you, no action is needed.')
            ->line('If you did not log in, please change your password immediately and contact support.')
            ->action('Secure Your Account', url('/auth/change-password'))
            ->salutation('Stay safe, GroupSave Team');
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
