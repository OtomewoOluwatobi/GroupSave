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
            ->subject('Contribution Received - ' . $this->group->name)
            ->line($this->contributor->name . ' has made a contribution of £' . number_format($this->contribution->amount, 2))
            ->line('Group: ' . $this->group->name)
            ->action('View Group', url('/groups/' . $this->group->id))
            ->line('Thank you!');
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
