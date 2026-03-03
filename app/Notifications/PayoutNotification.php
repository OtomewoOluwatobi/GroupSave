<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PayoutNotification extends Notification
{
    use Queueable;

    public $group;
    public $payout;

    public function __construct($group, $payout)
    {
        $this->group = $group;
        $this->payout = $payout;
    }

    public function via($notifiable)
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->view('emails.payout', [
                'groupName' => $this->group->name,
                'groupId' => $this->group->id,
                'amount' => $this->payout->amount,
            ])
            ->subject('Payout Received - ' . $this->group->name);
    }

    public function toDatabase($notifiable)
    {
        return [
            'type' => 'payout_received',
            'group_id' => $this->group->id,
            'group_name' => $this->group->name,
            'payout_id' => $this->payout->id,
            'amount' => $this->payout->amount,
            'message' => 'You received a payout of ₦' . number_format($this->payout->amount, 2) . ' from ' . $this->group->name,
        ];
    }
}
