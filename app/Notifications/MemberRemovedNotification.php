<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MemberRemovedNotification extends Notification
{
    use Queueable;

    public $group;
    public $reason;

    public function __construct($group, $reason = null)
    {
        $this->group = $group;
        $this->reason = $reason;
    }

    public function via($notifiable)
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable)
    {
        $mail = (new MailMessage)
            ->subject('Removed from Group - ' . $this->group->name)
            ->line('You have been removed from the group: ' . $this->group->name);

        if ($this->reason) {
            $mail->line('Reason: ' . $this->reason);
        }

        return $mail->line('If you believe this is an error, please contact the group admin.');
    }

    public function toDatabase($notifiable)
    {
        return [
            'type' => 'member_removed',
            'group_id' => $this->group->id,
            'group_name' => $this->group->name,
            'reason' => $this->reason,
            'message' => 'You have been removed from ' . $this->group->name,
        ];
    }
}
