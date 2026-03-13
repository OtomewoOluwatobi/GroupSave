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
            ->view('emails.password-changed', [
                'userName' => $this->userName,
                'changedAt' => $this->changedAt,
                'action' => $action,
            ])
            ->subject('Your ' . config('app.name') . ' Password Has Been ' . ucfirst($action));
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
