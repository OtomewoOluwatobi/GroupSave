<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

/**
 * Base notification class with best practices
 * All notifications should extend this class
 */
abstract class BaseNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Queue timeout - prevents hung notifications
     */
    public int $timeout = 120;

    /**
     * Number of retry attempts
     */
    public int $tries = 3;

    /**
     * Backoff strategy in seconds (progressive delays)
     */
    public array $backoff = [10, 60, 180];

    /**
     * Create a new notification instance.
     */
    public function __construct()
    {
        $this->onQueue('notifications');
    }

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
            'trace' => $exception->getTraceAsString(),
        ]);
    }
}
