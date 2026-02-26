<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;

class GroupInvitationAcceptedNotification extends DatabaseNotification
{
    /**
     * Store only scalar values to avoid serialization issues
     */
    protected int $groupId;
    protected string $groupName;
    protected int $userId;
    protected string $userName;
    protected string $userEmail;

    /**
     * Create a new notification instance.
     */
    public function __construct($group, $user)
    {
        $this->groupId = $group->id;
        $this->groupName = $group->title;
        $this->userId = $user->id;
        $this->userName = $user->name;
        $this->userEmail = $user->email;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable): array
    {
        return ['database']; //'mail',
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->view('emails.group-invitation-accepted', [
                'groupName' => $this->groupName,
                'userName' => $this->userName,
                'userEmail' => $this->userEmail,
                'adminName' => $notifiable->name,
            ])
            ->subject("{$this->userName} accepted your {$this->groupName} group invitation");
    }

    /**
     * Get the database representation of the notification (in-app activity).
     */
    public function toDatabase($notifiable): array
    {
        return [
            'type' => 'group_invitation_accepted',
            'group_id' => $this->groupId,
            'group_name' => $this->groupName,
            'user_id' => $this->userId,
            'user_name' => $this->userName,
            'user_email' => $this->userEmail,
            'message' => "{$this->userName} accepted your invitation to join {$this->groupName}",
            'action_url' => "/groups/{$this->groupId}",
        ];
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray($notifiable): array
    {
        return [
            'type' => 'group_invitation_accepted',
            'group_id' => $this->groupId,
            'group_name' => $this->groupName,
            'user_id' => $this->userId,
            'user_name' => $this->userName,
            'user_email' => $this->userEmail,
            'message' => "{$this->userName} accepted your invitation to join {$this->groupName}",
        ];
    }
}
