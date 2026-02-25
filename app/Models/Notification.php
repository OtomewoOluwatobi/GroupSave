<?php

namespace App\Models;

use Illuminate\Notifications\DatabaseNotification;

class Notification extends DatabaseNotification
{
    /**
     * Mapping of data keys to their related models.
     */
    protected static array $relatedModelMap = [
        'group_id' => Group::class,
        'inviter_id' => User::class,
        'contributor_id' => User::class,
        'user_id' => User::class,
        'member_id' => User::class,
    ];

    /**
     * Load related data based on IDs stored in the notification's data column.
     *
     * @return array
     */
    public function loadRelatedData(): array
    {
        $relatedData = [];
        $data = $this->data ?? [];

        foreach (self::$relatedModelMap as $key => $modelClass) {
            if (isset($data[$key]) && !empty($data[$key])) {
                $relationName = $this->getRelationNameFromKey($key);
                $model = $modelClass::find($data[$key]);
                
                if ($model) {
                    $relatedData[$relationName] = $model;
                }
            }
        }

        return $relatedData;
    }

    /**
     * Convert a data key to a relation name.
     *
     * @param string $key
     * @return string
     */
    protected function getRelationNameFromKey(string $key): string
    {
        // Remove '_id' suffix and convert to a readable name
        return str_replace('_id', '', $key);
    }

    /**
     * Get the notification with its related data.
     *
     * @return array
     */
    public function withRelatedData(): array
    {
        return [
            'notification' => $this,
            'related' => $this->loadRelatedData(),
        ];
    }

    /**
     * Get the notifiable entity that the notification belongs to.
     */
    public function notifiable()
    {
        return $this->morphTo();
    }

    /**
     * Mark notification as read.
     */
    public function markAsRead(): void
    {
        if (is_null($this->read_at)) {
            $this->update(['read_at' => now()]);
        }
    }

    /**
     * Mark notification as unread.
     */
    public function markAsUnread(): void
    {
        $this->update(['read_at' => null]);
    }

    /**
     * Scope to get unread notifications.
     */
    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }

    /**
     * Scope to get read notifications.
     */
    public function scopeRead($query)
    {
        return $query->whereNotNull('read_at');
    }

    /**
     * Scope to get notifications by type.
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Check if notification is read.
     */
    public function isRead(): bool
    {
        return !is_null($this->read_at);
    }

    /**
     * Check if notification is unread.
     */
    public function isUnread(): bool
    {
        return is_null($this->read_at);
    }
}
