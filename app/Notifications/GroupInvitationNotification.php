<?php

namespace App\Notifications;

use App\Models\Group;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

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
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $mail = new MailMessage();
        
        $mail->to($notifiable->email)
            ->view('emails.group-invitation', [
                'group' => $this->group,
                'user' => $this->invitee,
                'inviter' => auth()->guard()->user(),
                'generatedPassword' => $this->generatedPassword,
            ])
            ->subject("You've been invited to join {$this->group->title}");

        // Add CC if provided
        if ($this->cc) {
            $mail->cc($this->cc);
        }

        // Add BCC if provided
        if ($this->bcc) {
            $mail->bcc($this->bcc);
        }

        return $mail;
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
