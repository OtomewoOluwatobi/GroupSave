<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;

class GroupMemberRemovedNotification extends DatabaseNotification
{
    private int $groupId;
    private string $groupTitle;

    /**
     * Create a new notification instance.
     */
    public function __construct(int $groupId, string $groupTitle)
    {
        $this->groupId = $groupId;
        $this->groupTitle = $groupTitle;
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
            ->view('emails.member-removed', [
                'groupName' => $this->groupTitle,
                'reason' => 'Your invitation was not accepted within the required timeframe. A new member has been added to replace you.',
            ])
            ->subject("You have been removed from group: {$this->groupTitle}");
    }

    /**
     * Get the database representation of the notification.
     */
    public function toDatabase($notifiable): array
    {
        return [
            'type' => 'group_member_removed',
            'group_id' => $this->groupId,
            'group_title' => $this->groupTitle,
            'message' => "You have been removed from group '{$this->groupTitle}' due to not accepting the invitation.",
        ];
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray($notifiable): array
    {
        return [
            'type' => 'group_member_removed',
            'group_id' => $this->groupId,
            'message' => "Removed from group: {$this->groupTitle}",
        ];
    }
}