<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ContributionRejectedNotification extends Notification
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
            ->subject('Contribution Rejected - ' . $this->group->title)
            ->line('Your contribution of £' . number_format($this->contribution->amount, 2) . ' to ' . $this->group->title . ' has been rejected.')
            ->line('Please resubmit with a valid proof of payment.');
    }

    public function toDatabase($notifiable)
    {
        return [
            'type'            => 'contribution_rejected',
            'group_id'        => $this->group->id,
            'group_name'      => $this->group->title,
            'contribution_id' => $this->contribution->id,
            'amount'          => $this->contribution->amount,
            'cycle_number'    => $this->contribution->cycle_number,
            'message'         => 'Your contribution to ' . $this->group->title . ' was rejected. Please resubmit.',
        ];
    }
}
