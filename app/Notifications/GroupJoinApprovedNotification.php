<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;

class GroupJoinApprovedNotification extends BaseNotification
{
    private int $groupId;
    private string $groupTitle;

    public function __construct(int $groupId, string $groupTitle)
    {
        $this->groupId = $groupId;
        $this->groupTitle = $groupTitle;
    }

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Join Request Approved - ' . $this->groupTitle)
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('Your request to join "' . $this->groupTitle . '" has been approved!')
            ->action('View Group', url('/groups/' . $this->groupId))
            ->line('Welcome to the group!');
    }

    public function toDatabase($notifiable): array
    {
        return [
            'type' => 'group_join_approved',
            'group_id' => $this->groupId,
            'group_title' => $this->groupTitle,
            'message' => 'Your request to join "' . $this->groupTitle . '" has been approved!',
        ];
    }

    public function toArray($notifiable): array
    {
        return [
            'type' => 'group_join_approved',
            'group_id' => $this->groupId,
            'message' => 'Join request approved for ' . $this->groupTitle,
        ];
    }
}
