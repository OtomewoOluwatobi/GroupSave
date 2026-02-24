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
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Payout Received - ' . $this->group->name)
            ->line('Congratulations! You have received a payout of £' . number_format($this->payout->amount, 2))
            ->line('Group: ' . $this->group->name)
            ->action('View Details', url('/groups/' . $this->group->id))
            ->line('Thank you for being part of the group!');
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
