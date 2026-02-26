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
            ->subject('Cycle Completed - ' . $this->group->name)
            ->line('Cycle ' . $this->cycle->cycle_number . ' has been completed for ' . $this->group->name)
            ->action('View Group', url('/groups/' . $this->group->id))
            ->line('Thank you for participating!');
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
