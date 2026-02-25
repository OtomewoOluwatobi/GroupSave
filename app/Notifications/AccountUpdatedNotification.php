<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;

class AccountUpdatedNotification extends BaseNotification
{
    private string $userName;
    private array $updatedFields;
    private string $updatedAt;
    private bool $emailChanged;

    /**
     * Create a new notification instance.
     */
    public function __construct(string $userName, array $updatedFields, bool $emailChanged = false)
    {
        $this->userName = $userName;
        $this->updatedFields = $updatedFields;
        $this->updatedAt = now()->toDateTimeString();
        $this->emailChanged = $emailChanged;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable): array
    {
        // Send email only for sensitive changes (email change)
        return $this->emailChanged ? ['mail', 'database'] : ['database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable): MailMessage
    {
        $fieldsList = implode(', ', array_keys($this->updatedFields));
        
        return (new MailMessage)
            ->subject('Your GroupSave Account Was Updated')
            ->greeting('Hello ' . $this->userName . ',')
            ->line('Your account information was updated on ' . $this->updatedAt . '.')
            ->line('**Updated fields:** ' . $fieldsList)
            ->line('If you made these changes, no action is needed.')
            ->line('If you did NOT make these changes, please secure your account immediately.')
            ->action('Review Account', url('/profile'))
            ->salutation('Best regards, GroupSave Team');
    }

    /**
     * Get the database representation of the notification.
     */
    public function toDatabase($notifiable): array
    {
        return [
            'type' => 'account_updated',
            'message' => 'Your account information was updated',
            'updated_fields' => array_keys($this->updatedFields),
            'updated_at' => $this->updatedAt,
        ];
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray($notifiable): array
    {
        return [
            'type' => 'account_updated',
            'message' => 'Account updated',
            'updated_fields' => array_keys($this->updatedFields),
        ];
    }
}
