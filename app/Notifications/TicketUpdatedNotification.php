<?php

namespace App\Notifications;

use App\Models\SupportTicket;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TicketUpdatedNotification extends Notification
{
    use Queueable;

    public SupportTicket $ticket;
    public string $updateType;
    public ?string $customMessage;

    public function __construct(SupportTicket $ticket, string $updateType, ?string $customMessage = null)
    {
        $this->ticket = $ticket;
        $this->updateType = $updateType;
        $this->customMessage = $customMessage;
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
        $data = $this->getData();

        return (new MailMessage)
            ->subject($data['title'] . ' - ' . $this->ticket->ticket_id)
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line($data['message'])
            ->line('**Ticket ID:** ' . $this->ticket->ticket_id)
            ->line('**Subject:** ' . $this->ticket->subject)
            ->action('View Ticket', url('/support/tickets/' . $this->ticket->ticket_id))
            ->salutation('Best regards, GroupSave Support');
    }

    /**
     * Get the database representation of the notification.
     */
    public function toDatabase($notifiable): array
    {
        $data = $this->getData();

        return [
            'type' => 'ticket_updated',
            'title' => $data['title'],
            'message' => $data['message'],
            'ticket_id' => $this->ticket->ticket_id,
            'subject' => $this->ticket->subject,
            'status' => $this->ticket->status,
            'update_type' => $this->updateType,
        ];
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray($notifiable): array
    {
        return $this->toDatabase($notifiable);
    }

    /**
     * Get notification data based on update type.
     */
    protected function getData(): array
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
            'message' => $this->customMessage ?? ($messages[$this->updateType] ?? "Your ticket {$this->ticket->ticket_id} has been updated."),
        ];
    }
}
