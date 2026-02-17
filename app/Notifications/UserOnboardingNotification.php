<?php

namespace App\Notifications;

use App\Models\User;
use App\Mail\Onboarding;
use Illuminate\Notifications\Notification;

class UserOnboardingNotification extends Notification
{
    private User $user;

    /**
     * Create a new notification instance.
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable)
    {
        return new Onboarding($this->user);
    }


