<?php

namespace App\Notifications;

use App\Models\SupportTicket;
use App\Models\SupportTicketReply;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TicketReplyNotification extends Notification
{
    use Queueable;

    public SupportTicket $ticket;
    public SupportTicketReply $reply;

    public function __construct(SupportTicket $ticket, SupportTicketReply $reply)
    {
        $this->ticket = $ticket;
        $this->reply = $reply;
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
        $author = $this->reply->is_from_support
            ? ($this->reply->agent_name ?? 'Support Team')
            : 'You';

        return (new MailMessage)
            ->view('emails.ticket-reply', [
                'userName' => $notifiable->name,
                'ticketId' => $this->ticket->ticket_id,
                'subject' => $this->ticket->subject,
                'author' => $author,
                'messagePreview' => substr($this->reply->message, 0, 200) . (strlen($this->reply->message) > 200 ? '...' : ''),
            ])
            ->subject('New Reply on Ticket ' . $this->ticket->ticket_id);
    }

    /**
     * Get the database representation of the notification.
     */
    public function toDatabase($notifiable): array
    {
        $author = $this->reply->is_from_support
            ? ($this->reply->agent_name ?? 'Support Team')
            : 'You';

        return [
            'type' => 'ticket_reply',
            'title' => 'New Reply on Your Ticket',
            'message' => "{$author} replied to ticket {$this->ticket->ticket_id}: \"{$this->ticket->subject}\"",
            'ticket_id' => $this->ticket->ticket_id,
            'subject' => $this->ticket->subject,
            'reply_id' => $this->reply->id,
            'is_from_support' => $this->reply->is_from_support,
            'author' => $author,
            'preview' => substr($this->reply->message, 0, 100) . (strlen($this->reply->message) > 100 ? '...' : ''),
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
