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
        return (new MailMessage)
            ->view('emails.member-removed', [
                'userName' => $notifiable->name,
                'groupName' => $this->group->name,
                'reason' => $this->reason,
            ])
            ->subject('Removed from Group - ' . $this->group->name);
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
