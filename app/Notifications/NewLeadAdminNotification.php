<?php

namespace App\Notifications;

use App\Models\Lead;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewLeadAdminNotification extends Notification implements ShouldQueue
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
        return ['database', 'mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->view('emails.new-lead-admin', [
                'adminName' => $notifiable->name,
                'lead'      => $this->lead,
            ])
            ->subject('New Lead Submitted — ' . config('app.name'));
    }

    public function toDatabase($notifiable): array
    {
        return [
            'type'       => 'new_lead',
            'title'      => 'New Lead Submitted',
            'message'    => "{$this->lead->name} ({$this->lead->email}) submitted a lead response.",
            'lead_id'    => $this->lead->id,
            'lead_name'  => $this->lead->name,
            'lead_email' => $this->lead->email,
            'source'     => $this->lead->source,
        ];
    }

    public function toArray($notifiable): array
    {
        return $this->toDatabase($notifiable);
    }
}
