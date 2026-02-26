<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;

class GroupJoinRequestNotification extends BaseNotification
{
    private int $groupId;
    private string $groupTitle;
    private int $userId;
    private string $userName;

    public function __construct(int $groupId, string $groupTitle, int $userId, string $userName)
    {
        $this->groupId = $groupId;
        $this->groupTitle = $groupTitle;
        $this->userId = $userId;
        $this->userName = $userName;
    }

    public function via($notifiable): array
    {
        return ['database']; //'mail',
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New Join Request for ' . $this->groupTitle)
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line($this->userName . ' has requested to join your group: ' . $this->groupTitle)
            ->action('Review Request', url('/groups/' . $this->groupId))
            ->line('Thank you for using GroupSave!');
    }

    public function toDatabase($notifiable): array
    {
        return [
            'type' => 'group_join_request',
            'group_id' => $this->groupId,
            'user_id' => $this->userId,
            'user_name' => $this->userName,
            'group_title' => $this->groupTitle,
            'message' => $this->userName . ' has requested to join ' . $this->groupTitle,
        ];
    }

    public function toArray($notifiable): array
    {
        return [
            'type' => 'group_join_request',
            'group_id' => $this->groupId,
            'user_id' => $this->userId,
            'message' => $this->userName . ' requested to join ' . $this->groupTitle,
        ];
    }
}
