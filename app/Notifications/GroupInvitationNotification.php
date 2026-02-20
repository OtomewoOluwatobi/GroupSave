<?php

namespace App\Notifications;

use App\Models\Group;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class GroupInvitationNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $group;
    protected $invitee;
    protected $generatedPassword;
    protected $inviter;

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
        $this->inviter = $group->owner;
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
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new \Illuminate\Notifications\Messages\MailMessage)
            ->view('emails.group-invitation', [
                'group' => $this->group,
                'user' => $this->invitee,
                'inviter' => $this->inviter,
                'generatedPassword' => $this->generatedPassword,
            ])
            ->subject("You've been invited to join {$this->group->title}");
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


