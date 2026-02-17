<?php

namespace App\Notifications;

use App\Models\User;
use App\Mail\Onboarding;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class UserOnboardingNotification extends Notification
{
    use Queueable;

    protected $user;
    protected $cc;
    protected $bcc;

    /**
     * Create a new notification instance.
     *
     * @param User $user
     * @param string|null $cc
     * @param string|null $bcc
     */
    public function __construct(User $user, $cc = null, $bcc = null)
    {
        $this->user = $user;
        $this->cc = $cc;
        $this->bcc = $bcc;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     * @return \Illuminate\Mail\Mailable
     */
    public function toMail($notifiable)
    {
        return new Onboarding($this->user, $this->cc, $this->bcc);
    }

    /**
     * Get the array representation of the notification.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'user_id' => $this->user->id,
            'user_name' => $this->user->name,
            'message' => "Welcome {$this->user->name}! Your account has been created successfully.",
        ];
    }
}

