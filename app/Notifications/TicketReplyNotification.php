<?php

namespace App\Notifications;

use App\Models\SupportTicket;
use App\Models\SupportTicketReply;

class TicketReplyNotification
{
    public SupportTicket $ticket;
    public SupportTicketReply $reply;

    public function __construct(SupportTicket $ticket, SupportTicketReply $reply)
    {
        $this->ticket = $ticket;
        $this->reply = $reply;
    }

    /**
     * Get the notification's data array.
     */
    public function toArray(): array
    {
        $author = $this->reply->is_from_support
            ? ($this->reply->agent_name ?? 'Support Team')
            : 'You';

        return [
            'title' => 'New Reply on Your Ticket',
            'message' => "{$author} replied to ticket {$this->ticket->ticket_id}: \"{$this->ticket->subject}\"",
            'type' => 'ticket_reply',
            'data' => [
                'ticket_id' => $this->ticket->ticket_id,
                'subject' => $this->ticket->subject,
                'reply_id' => $this->reply->id,
                'is_from_support' => $this->reply->is_from_support,
                'author' => $author,
                'preview' => substr($this->reply->message, 0, 100) . (strlen($this->reply->message) > 100 ? '...' : ''),
            ],
        ];
    }
}
