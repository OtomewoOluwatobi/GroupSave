<?php

namespace App\Notifications;

use App\Models\Group;
use App\Models\User;
use App\Mail\GroupInvitation;
use Illuminate\Notifications\Notification;

class GroupInvitationNotification extends Notification
{
    protected $group;
    protected $invitee;
    protected $generatedPassword;

    /**
     * Create a new notification instance.
     *
     * @param Group $group
     * @param User $invitee
     * @param string $generatedPassword
     */
    public function __construct(Group $group, User $invitee, $generatedPassword)
    {
        $this->group = $group;
        $this->invitee = $invitee;
        $this->generatedPassword = $generatedPassword;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     * @return \Illuminate\Mail\Mailable
     */
    public function toMail($notifiable)
    {
        return new GroupInvitation($this->group, $this->invitee, $this->generatedPassword);
    }

    /**
     * Get the array representation of the notification.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'group_id' => $this->group->id,
            'group_name' => $this->group->title,
            'inviter_id' => auth()->guard()->user()->id,
            'message' => "You've been invited to join {$this->group->title}",
        ];
    }
}


