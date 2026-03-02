<?php

namespace App\Notifications;

use App\Models\SupportTicket;

class TicketCreatedNotification
{
    public SupportTicket $ticket;

    public function __construct(SupportTicket $ticket)
    {
        $this->ticket = $ticket;
    }

    /**
     * Get the notification's data array.
     */
    public function toArray(): array
    {
        $sla = SupportTicket::getPriorityInfo($this->ticket->priority)['sla'];

        return [
            'title' => 'Support Ticket Created',
            'message' => "Your support ticket {$this->ticket->ticket_id} has been submitted. We'll respond within {$sla}.",
            'type' => 'ticket_created',
            'data' => [
                'ticket_id' => $this->ticket->ticket_id,
                'subject' => $this->ticket->subject,
                'category' => $this->ticket->category,
                'priority' => $this->ticket->priority,
                'sla' => $sla,
            ],
        ];
    }
}
