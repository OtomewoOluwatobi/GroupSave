<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;

class PlanActivatedNotification extends BaseNotification
{
    private string $userName;
    private string $planName;
    private string $activatedAt;

    public function __construct(string $userName, string $planName)
    {
        parent::__construct();
        $this->userName = $userName;
        $this->planName = $planName;
        $this->activatedAt = now()->toDateTimeString();
    }

    public function via($notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->view('emails.plan-activated', [
                'userName' => $this->userName,
                'planName' => $this->planName,
                'activatedAt' => $this->activatedAt,
            ])
            ->subject('Your ' . $this->planName . ' Plan Is Now Active');
    }

    public function toDatabase($notifiable): array
    {
        return [
            'type'         => 'plan_activated',
            'message'      => "Your {$this->planName} plan is now active.",
            'plan_name'    => $this->planName,
            'activated_at' => $this->activatedAt,
        ];
    }
}
