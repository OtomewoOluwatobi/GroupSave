<?php

namespace App\Services;

use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    /**
     * Whether notifications are enabled
     * Set to false to completely disable all notifications (useful for debugging)
     * Can be controlled via NOTIFICATIONS_ENABLED env variable
     */
    private static ?bool $enabled = null;

    /**
     * Check if notifications are enabled
     */
    private static function isNotificationsEnabled(): bool
    {
        if (self::$enabled === null) {
            self::$enabled = env('NOTIFICATIONS_ENABLED', true);
        }
        return self::$enabled;
    }

    /**
     * Send a notification safely with error handling
     * Wraps in try-catch to prevent any notification failure from crashing the request
     *
     * @param mixed $notifiable The user or entity to notify
     * @param Notification $notification The notification instance
     * @return bool Whether the notification was sent successfully
     */
    public static function send($notifiable, Notification $notification): bool
    {
        // Quick bail-out if notifications are disabled
        if (!self::isNotificationsEnabled()) {
            Log::debug('NotificationService: Notifications disabled, skipping', [
                'notification' => get_class($notification)
            ]);
            return true;
        }

        if (!$notifiable) {
            Log::warning('NotificationService: Attempted to send notification to null notifiable', [
                'notification' => get_class($notification)
            ]);
            return false;
        }

        try {
            $notifiable->notify($notification);
            
            Log::info('Notification sent', [
                'notification' => get_class($notification),
                'notifiable_id' => $notifiable->id ?? null,
            ]);
            
            return true;
        } catch (\Throwable $e) {
            // Catch ALL errors including fatal errors
            Log::warning('NotificationService: Notification failed (non-blocking)', [
                'notification' => get_class($notification),
                'notifiable_id' => $notifiable->id ?? null,
                'error' => $e->getMessage(),
            ]);
            
            // Never throw - just log and continue
            return false;
        }
    }

    /**
     * Send notification to multiple users safely
     *
     * @param iterable $notifiables Collection of users to notify
     * @param Notification $notification The notification instance
     * @return array Results for each notifiable
     */
    public static function sendToMany(iterable $notifiables, Notification $notification): array
    {
        $results = [];

        foreach ($notifiables as $notifiable) {
            $results[] = [
                'notifiable_id' => $notifiable->id ?? null,
                'success' => self::send($notifiable, $notification)
            ];
        }

        return $results;
    }

    /**
     * Enable or disable notifications globally
     *
     * @param bool $enabled
     */
    public static function setEnabled(bool $enabled): void
    {
        self::$enabled = $enabled;
    }

    /**
     * Check if notifications are enabled
     *
     * @return bool
     */
    public static function isEnabled(): bool
    {
        return self::isNotificationsEnabled();
    }
}
