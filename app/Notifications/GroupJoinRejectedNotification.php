<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;

class GroupJoinRejectedNotification extends DatabaseNotification
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
        return ['database']; //'mail',
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->view('emails.group-join-rejected', [
                'userName' => $notifiable->name,
                'groupTitle' => $this->groupTitle,
            ])
            ->subject('Join Request Declined - ' . $this->groupTitle);
    }

    public function toDatabase($notifiable): array
    {
        return [
            'type' => 'group_join_rejected',
            'group_id' => $this->groupId,
            'group_title' => $this->groupTitle,
            'message' => 'Your request to join "' . $this->groupTitle . '" has been declined.',
        ];
    }

    public function toArray($notifiable): array
    {
        return [
            'type' => 'group_join_rejected',
            'group_id' => $this->groupId,
            'message' => 'Join request declined for ' . $this->groupTitle,
        ];
    }
}
