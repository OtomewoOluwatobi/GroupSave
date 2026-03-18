<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;

class GroupJoinApprovedNotification extends DatabaseNotification
{
    private string $groupId;
    private string $groupTitle;

    public function __construct(int $groupId, string $groupTitle)
    {
        $this->groupId = $groupId;
        $this->groupTitle = $groupTitle;
    }

    public function via($notifiable): array
    {
        return ['database']; //'mail',
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->view('emails.group-join-approved', [
                'userName' => $notifiable->name,
                'groupTitle' => $this->groupTitle,
                'groupId' => $this->groupId,
            ])
            ->subject('Join Request Approved - ' . $this->groupTitle);
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
