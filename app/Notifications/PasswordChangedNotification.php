<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;

class PasswordChangedNotification extends BaseNotification
{
    private string $userName;
    private string $changedAt;
    private string $method; // 'reset' or 'change'

    /**
     * Create a new notification instance.
     */
    public function __construct(string $userName, string $method = 'change')
    {
        parent::__construct();
        $this->userName = $userName;
        $this->changedAt = now()->toDateTimeString();
        $this->method = $method;
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
        $action = $this->method === 'reset' ? 'reset' : 'changed';
        
        return (new MailMessage)
            ->subject('Your GroupSave Password Has Been ' . ucfirst($action))
            ->greeting('Hello ' . $this->userName . ',')
            ->line('Your password was successfully ' . $action . ' on ' . $this->changedAt . '.')
            ->line('If you made this change, no further action is required.')
            ->line('**If you did NOT make this change**, your account may be compromised. Please:')
            ->line('1. Reset your password immediately')
            ->line('2. Contact our support team')
            ->action('Contact Support', url('/support'))
            ->salutation('Stay secure, GroupSave Team');
    }

    /**
     * Get the database representation of the notification.
     */
    public function toDatabase($notifiable): array
    {
        return [
            'type' => 'password_changed',
            'message' => 'Your password was ' . ($this->method === 'reset' ? 'reset' : 'changed') . ' successfully',
            'changed_at' => $this->changedAt,
            'method' => $this->method,
        ];
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray($notifiable): array
    {
        return [
            'type' => 'password_changed',
            'message' => 'Password ' . $this->method . ' successful',
            'changed_at' => $this->changedAt,
        ];
    }
}
