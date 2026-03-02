<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupportTicketReply extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_id',
        'user_id',
        'is_from_support',
        'agent_name',
        'message',
        'attachments',
    ];

    protected $casts = [
        'is_from_support' => 'boolean',
        'attachments' => 'array',
    ];

    /**
     * The ticket this reply belongs to
     */
    public function ticket(): BelongsTo
    {
        return $this->belongsTo(SupportTicket::class, 'ticket_id');
    }

    /**
     * The user who made this reply (null if from support)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the author name
     */
    public function getAuthorNameAttribute(): string
    {
        if ($this->is_from_support) {
            return $this->agent_name ?? 'Support Team';
        }
        return $this->user?->name ?? 'User';
    }
}
