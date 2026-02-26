<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

/**
 * Base class for database-only notifications
 * These run synchronously (no queue) so they save immediately
 */
abstract class DatabaseNotification extends Notification
{
    /**
     * Handle notification failures
     *
     * @param \Throwable $exception
     * @return void
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('Notification failed: ' . static::class, [
            'error' => $exception->getMessage(),
        ]);
    }
}
