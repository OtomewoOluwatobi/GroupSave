<?php

namespace App\Notifications;

use App\Models\Group;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class GroupMemberRemovedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(private Group $group)
    {
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("You have been removed from group: {$this->group->title}")
            ->greeting("Hello {$notifiable->name},")
            ->line("You have been removed from the group '{$this->group->title}' because your invitation was not accepted within the required timeframe.")
            ->line("A new member has been added to replace you.")
            ->action('View Group', url("/groups/{$this->group->id}"))
            ->line('Thank you for your interest in GroupSave!');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'group_member_removed',
            'group_id' => $this->group->id,
            'group_title' => $this->group->title,
            'message' => "You have been removed from group '{$this->group->title}' due to not accepting the invitation.",
        ];
    }
}