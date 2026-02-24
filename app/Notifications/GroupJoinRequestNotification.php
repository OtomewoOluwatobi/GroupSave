<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class GroupJoinRequestNotification extends Notification
{
    use Queueable;

    public $group;
    public $user;

    public function __construct($group, $user)
    {
        $this->group = $group;
        $this->user = $user;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->line('New join request for group: ' . $this->group->name)
            ->action('View Request', url('/'))
            ->line('Thank you for using our application!');
    }
}
