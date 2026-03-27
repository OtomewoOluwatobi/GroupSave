<?php

namespace App\Notifications;

use App\Models\Lead;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LeadReceivedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public int $tries   = 3;
    public int $timeout = 120;
    public array $backoff = [10, 60, 180];

    public function __construct(public readonly Lead $lead)
    {
        $this->onQueue('notifications');
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->view('emails.lead-received', [
                'name' => $this->lead->name,
            ])
            ->subject('We received your response — ' . config('app.name'));
    }

    public function toArray($notifiable): array
    {
        return [];
    }
}
