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
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('New Join Request for ' . $this->group->name)
            ->line('User ' . $this->user->name . ' has requested to join your group: ' . $this->group->name)
            ->action('Review Request', url('/groups/' . $this->group->id))
            ->line('Thank you!');
    }

    public function toDatabase($notifiable)
    {
        return [
            'type' => 'group_join_request',
            'group_id' => $this->group->id,
            'user_id' => $this->user->id,
            'user_name' => $this->user->name,
            'group_name' => $this->group->name,
            'message' => $this->user->name . ' has requested to join ' . $this->group->name,
        ];
    }
}
