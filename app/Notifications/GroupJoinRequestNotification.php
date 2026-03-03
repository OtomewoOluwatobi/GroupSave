<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;

class GroupJoinRequestNotification extends DatabaseNotification
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
            ->view('emails.group-join-request', [
                'adminName' => $notifiable->name,
                'requesterName' => $this->userName,
                'requesterEmail' => '',
                'groupTitle' => $this->groupTitle,
                'groupId' => $this->groupId,
            ])
            ->subject('New Join Request for ' . $this->groupTitle);
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
