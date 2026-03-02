<?php

namespace App\Notifications;

use App\Models\SupportTicket;

class TicketUpdatedNotification
{
    public SupportTicket $ticket;
    public string $updateType;
    public ?string $message;

    public function __construct(SupportTicket $ticket, string $updateType, ?string $message = null)
    {
        $this->ticket = $ticket;
        $this->updateType = $updateType;
        $this->message = $message;
    }

    /**
     * Get the notification's data array.
     */
    public function toArray(): array
    {
        $titles = [
            'status_change' => 'Ticket Status Updated',
            'assigned' => 'Ticket Assigned',
            'escalated' => 'Ticket Escalated',
            'resolved' => 'Ticket Resolved',
            'closed' => 'Ticket Closed',
        ];

        $messages = [
            'status_change' => "Your ticket {$this->ticket->ticket_id} status changed to {$this->ticket->status_label}.",
            'assigned' => "Your ticket {$this->ticket->ticket_id} has been assigned to a support agent.",
            'escalated' => "Your ticket {$this->ticket->ticket_id} has been escalated to senior support.",
            'resolved' => "Your ticket {$this->ticket->ticket_id} has been resolved. Please provide feedback.",
            'closed' => "Your ticket {$this->ticket->ticket_id} has been closed.",
        ];

        return [
            'title' => $titles[$this->updateType] ?? 'Ticket Update',
            'message' => $this->message ?? ($messages[$this->updateType] ?? "Your ticket {$this->ticket->ticket_id} has been updated."),
            'type' => 'ticket_updated',
            'data' => [
                'ticket_id' => $this->ticket->ticket_id,
                'subject' => $this->ticket->subject,
                'status' => $this->ticket->status,
                'update_type' => $this->updateType,
            ],
        ];
    }
}
