<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class GroupJoinRejectedNotification extends Notification
{
    use Queueable;

    public $group;

    public function __construct($group)
    {
        $this->group = $group;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Join Request Declined - ' . $this->group->name)
            ->line('Your request to join ' . $this->group->name . ' has been declined.')
            ->line('You can try joining other groups or contact the group admin for more information.');
    }

    public function toDatabase($notifiable)
    {
        return [
            'type' => 'group_join_rejected',
            'group_id' => $this->group->id,
            'group_name' => $this->group->name,
            'message' => 'Your request to join ' . $this->group->name . ' has been declined.',
        ];
    }
}
