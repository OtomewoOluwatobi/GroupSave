<?php

namespace App\Notifications;

use App\Models\SupportTicket;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TicketCreatedNotification extends Notification
{
    use Queueable;

    public SupportTicket $ticket;

    public function __construct(SupportTicket $ticket)
    {
        $this->ticket = $ticket;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable): array
    {
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable): MailMessage
    {
        $sla = SupportTicket::getPriorityInfo($this->ticket->priority)['sla'];

        return (new MailMessage)
            ->view('emails.ticket-created', [
                'userName' => $notifiable->name,
                'ticketId' => $this->ticket->ticket_id,
                'subject' => $this->ticket->subject,
                'category' => $this->ticket->category,
                'priority' => $this->ticket->priority,
                'sla' => $sla,
            ])
            ->subject('Support Ticket Created - ' . $this->ticket->ticket_id);
    }

    /**
     * Get the database representation of the notification.
     */
    public function toDatabase($notifiable): array
    {
        $sla = SupportTicket::getPriorityInfo($this->ticket->priority)['sla'];

        return [
            'type' => 'ticket_created',
            'title' => 'Support Ticket Created',
            'message' => "Your support ticket {$this->ticket->ticket_id} has been submitted. We'll respond within {$sla}.",
            'ticket_id' => $this->ticket->ticket_id,
            'subject' => $this->ticket->subject,
            'category' => $this->ticket->category,
            'priority' => $this->ticket->priority,
            'sla' => $sla,
        ];
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray($notifiable): array
    {
        return $this->toDatabase($notifiable);
    }
}
