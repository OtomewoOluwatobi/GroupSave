<?php

namespace App\Notifications;

use App\Models\Group;
use App\Models\User;
use App\Mail\GroupInvitation;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class GroupInvitationNotification extends Notification
{
    use Queueable;

    protected $group;
    protected $invitee;
    protected $generatedPassword;
    protected $cc;
    protected $bcc;

    /**
     * Create a new notification instance.
     *
     * @param Group $group
     * @param User $invitee
     * @param string $generatedPassword
     * @param string|null $cc
     * @param string|null $bcc
     */
    public function __construct(Group $group, User $invitee, $generatedPassword, $cc = null, $bcc = null)
    {
        $this->group = $group;
        $this->invitee = $invitee;
        $this->generatedPassword = $generatedPassword;
        $this->cc = $cc;
        $this->bcc = $bcc;
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
        return new GroupInvitation($this->group, $this->invitee, $this->generatedPassword, $this->cc, $this->bcc);
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

