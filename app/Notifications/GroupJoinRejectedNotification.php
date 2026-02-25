<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;

class GroupJoinRejectedNotification extends BaseNotification
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
            ->subject('Join Request Declined - ' . $this->groupTitle)
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('Your request to join "' . $this->groupTitle . '" has been declined.')
            ->line('You can try joining other groups or contact the group admin for more information.');
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
