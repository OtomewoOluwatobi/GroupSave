<?php

namespace App\Services;

use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    /**
     * Send a notification safely with error handling
     *
     * @param mixed $notifiable The user or entity to notify
     * @param Notification $notification The notification instance
     * @param bool $defer Whether to defer the notification until after response
     * @return bool Whether the notification was sent/queued successfully
     */
    public static function send($notifiable, Notification $notification, bool $defer = true): bool
    {
        if (!$notifiable) {
            Log::warning('NotificationService: Attempted to send notification to null notifiable', [
                'notification' => get_class($notification)
            ]);
            return false;
        }

        $sendNotification = function () use ($notifiable, $notification) {
            try {
                $notifiable->notify($notification);
                
                Log::info('Notification sent successfully', [
                    'notification' => get_class($notification),
                    'notifiable_id' => $notifiable->id ?? null,
                    'notifiable_type' => get_class($notifiable)
                ]);
                
                return true;
            } catch (\Exception $e) {
                Log::error('NotificationService: Failed to send notification', [
                    'notification' => get_class($notification),
                    'notifiable_id' => $notifiable->id ?? null,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                
                return false;
            }
        };

        if ($defer) {
            defer($sendNotification);
            return true; // Deferred, we assume it will succeed
        }

        return $sendNotification();
    }

    /**
     * Send notification to multiple users safely
     *
     * @param iterable $notifiables Collection of users to notify
     * @param Notification $notification The notification instance
     * @param bool $defer Whether to defer notifications until after response
     * @return array Results for each notifiable
     */
    public static function sendToMany(iterable $notifiables, Notification $notification, bool $defer = true): array
    {
        $results = [];

        foreach ($notifiables as $notifiable) {
            $results[] = [
                'notifiable_id' => $notifiable->id ?? null,
                'success' => self::send($notifiable, $notification, $defer)
            ];
        }

        return $results;
    }

    /**
     * Send notification immediately without deferring (use sparingly)
     *
     * @param mixed $notifiable
     * @param Notification $notification
     * @return bool
     */
    public static function sendNow($notifiable, Notification $notification): bool
    {
        return self::send($notifiable, $notification, defer: false);
    }
}
