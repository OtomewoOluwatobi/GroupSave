<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ContributionReceivedNotification extends Notification
{
    use Queueable;

    public $group;
    public $contribution;
    public $contributor;

    public function __construct($group, $contribution, $contributor)
    {
        $this->group = $group;
        $this->contribution = $contribution;
        $this->contributor = $contributor;
    }

    public function via($notifiable)
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->view('emails.contribution-received', [
                'groupName' => $this->group->name,
                'groupId' => $this->group->id,
                'contributorName' => $this->contributor->name,
                'amount' => $this->contribution->amount,
            ])
            ->subject('Contribution Received - ' . $this->group->name);
    }

    public function toDatabase($notifiable)
    {
        return [
            'type' => 'contribution_received',
            'group_id' => $this->group->id,
            'group_name' => $this->group->name,
            'contributor_id' => $this->contributor->id,
            'contributor_name' => $this->contributor->name,
            'amount' => $this->contribution->amount,
            'message' => $this->contributor->name . ' contributed ₦' . number_format($this->contribution->amount, 2) . ' to ' . $this->group->name,
        ];
    }
}
