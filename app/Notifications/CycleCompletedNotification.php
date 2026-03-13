<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CycleCompletedNotification extends Notification
{
    use Queueable;

    public $group;
    public $cycle;

    public function __construct($group, $cycle)
    {
        $this->group = $group;
        $this->cycle = $cycle;
    }

    public function via($notifiable)
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->view('emails.cycle-completed', [
                'userName' => $notifiable->name,
                'groupName' => $this->group->name,
                'groupId' => $this->group->id,
                'cycleNumber' => $this->cycle->cycle_number,
            ])
            ->subject('Cycle Completed - ' . $this->group->name);
    }

    public function toDatabase($notifiable)
    {
        return [
            'type' => 'cycle_completed',
            'group_id' => $this->group->id,
            'group_name' => $this->group->name,
            'cycle_id' => $this->cycle->id,
            'cycle_number' => $this->cycle->cycle_number,
            'message' => 'Cycle ' . $this->cycle->cycle_number . ' has been completed for ' . $this->group->name,
        ];
    }
}
