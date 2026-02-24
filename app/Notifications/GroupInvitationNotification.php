<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;

class GroupInvitationNotification extends BaseNotification
{
    /**
     * Store only scalar values to avoid serialization issues
     */
    protected int $groupId;
    protected string $groupName;
    protected int $inviteeId;
    protected string $inviteeEmail;
    protected string $inviteeFirstName;
    protected int $inviterId;
    protected string $inviterName;
    protected string $generatedPassword;

    /**
     * Create a new notification instance.
     */
    public function __construct($group, $invitee, $generatedPassword)
    {
        $this->groupId = $group->id;
        $this->groupName = $group->title;
        $this->inviteeId = $invitee->id;
        $this->inviteeEmail = $invitee->email;
        $this->inviteeFirstName = $invitee->name;
        $this->inviterId = $group->owner->id;
        $this->inviterName = $group->owner->name;
        $this->generatedPassword = $generatedPassword;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->view('emails.group-invitation', [
                'groupName' => $this->groupName,
                'userName' => $this->inviteeFirstName,
                'inviterName' => $this->inviterName,
                'generatedPassword' => $this->generatedPassword,
            ])
            ->subject("You've been invited to join {$this->groupName}");
    }

    /**
     * Get the database representation of the notification (in-app activity).
     */
    public function toDatabase($notifiable): array
    {
        return [
            'type' => 'group_invitation',
            'group_id' => $this->groupId,
            'group_name' => $this->groupName,
            'inviter_id' => $this->inviterId,
            'inviter_name' => $this->inviterName,
            'message' => "{$this->inviterName} invited you to join {$this->groupName}",
            'action_url' => "/groups/{$this->groupId}",
        ];
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray($notifiable): array
    {
        return [
            'group_id' => $this->groupId,
            'group_name' => $this->groupName,
            'inviter_id' => $this->inviterId,
            'inviter_name' => $this->inviterName,
            'message' => "You've been invited to join {$this->groupName}",
        ];
    }
}


