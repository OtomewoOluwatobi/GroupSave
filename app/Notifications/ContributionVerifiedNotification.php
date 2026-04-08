<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ContributionVerifiedNotification extends Notification
{
    use Queueable;

    public $group;
    public $contribution;

    public function __construct($group, $contribution)
    {
        $this->group = $group;
        $this->contribution = $contribution;
    }

    public function via($notifiable)
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Contribution Verified - ' . $this->group->title)
            ->line('Your contribution of £' . number_format($this->contribution->amount, 2) . ' to ' . $this->group->title . ' has been verified.')
            ->line('Cycle: ' . $this->contribution->cycle_number);
    }

    public function toDatabase($notifiable)
    {
        return [
            'type'         => 'contribution_verified',
            'group_id'     => $this->group->id,
            'group_name'   => $this->group->title,
            'contribution_id' => $this->contribution->id,
            'amount'       => $this->contribution->amount,
            'cycle_number' => $this->contribution->cycle_number,
            'message'      => 'Your contribution of £' . number_format($this->contribution->amount, 2) . ' to ' . $this->group->title . ' has been verified.',
        ];
    }
}
