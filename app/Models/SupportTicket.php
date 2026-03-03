<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class SupportTicket extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'ticket_id',
        'user_id',
        'subject',
        'category',
        'priority',
        'status',
        'message',
        'assigned_to',
        'first_response_at',
        'resolved_at',
        'closed_at',
    ];

    protected $casts = [
        'first_response_at' => 'datetime',
        'resolved_at' => 'datetime',
        'closed_at' => 'datetime',
    ];

    // Status constants
    public const STATUS_OPEN = 'open';
    public const STATUS_IN_REVIEW = 'in_review';
    public const STATUS_AWAITING = 'awaiting';
    public const STATUS_ESCALATED = 'escalated';
    public const STATUS_RESOLVED = 'resolved';
    public const STATUS_CLOSED = 'closed';

    // Priority constants
    public const PRIORITY_LOW = 'low';
    public const PRIORITY_MEDIUM = 'medium';
    public const PRIORITY_HIGH = 'high';
    public const PRIORITY_CRITICAL = 'critical';

    // Category constants
    public const CATEGORY_ACCOUNT = 'account';
    public const CATEGORY_GROUPS = 'groups';
    public const CATEGORY_PAYMENTS = 'payments';
    public const CATEGORY_PAYOUTS = 'payouts';
    public const CATEGORY_NOTIFICATIONS = 'notifications';
    public const CATEGORY_BILLING = 'billing';
    public const CATEGORY_TECHNICAL = 'technical';
    public const CATEGORY_FRAUD = 'fraud';

    // SLA in hours by priority
    public const SLA_HOURS = [
        'low' => 72,
        'medium' => 48,
        'high' => 24,
        'critical' => 12,
    ];

    /**
     * Boot method - generate ticket ID on creation
     */
    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($ticket) {
            if (!$ticket->ticket_id) {
                $ticket->ticket_id = self::generateUniqueTicketId();
            }
        });
    }

    /**
     * Generate unique ticket ID (TK-XXX format)
     */
    public static function generateUniqueTicketId(): string
    {
        $lastTicket = self::orderBy('id', 'desc')->first();
        $nextNumber = $lastTicket ? (intval(substr($lastTicket->ticket_id, 3)) + 1) : 1;
        return 'TK-' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    }

    /**
     * User who submitted the ticket
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Replies to this ticket
     */
    public function replies(): HasMany
    {
        return $this->hasMany(SupportTicketReply::class, 'ticket_id');
    }

    /**
     * Scope for open tickets
     */
    public function scopeOpen($query)
    {
        return $query->whereIn('status', [self::STATUS_OPEN, self::STATUS_IN_REVIEW, self::STATUS_AWAITING, self::STATUS_ESCALATED]);
    }

    /**
     * Scope for resolved tickets
     */
    public function scopeResolved($query)
    {
        return $query->where('status', self::STATUS_RESOLVED);
    }

    /**
     * Scope for closed tickets
     */
    public function scopeClosed($query)
    {
        return $query->where('status', self::STATUS_CLOSED);
    }

    /**
     * Scope by priority
     */
    public function scopeByPriority($query, string $priority)
    {
        return $query->where('priority', $priority);
    }

    /**
     * Scope by category
     */
    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Check if ticket is still active (not resolved or closed)
     */
    public function isActive(): bool
    {
        return !in_array($this->status, [self::STATUS_RESOLVED, self::STATUS_CLOSED]);
    }

    /**
     * Get SLA deadline
     */
    public function getSlaDeadlineAttribute(): ?\Carbon\Carbon
    {
        $hours = self::SLA_HOURS[$this->priority] ?? 48;
        return $this->created_at->addHours($hours);
    }

    /**
     * Check if SLA is breached
     */
    public function isSlaBreached(): bool
    {
        if (!$this->isActive()) {
            return false;
        }
        return now()->gt($this->sla_deadline);
    }

    /**
     * Get category label
     */
    public static function getCategoryLabel(string $category): string
    {
        $labels = [
            'account' => 'Account & Security',
            'groups' => 'Savings Groups',
            'payments' => 'Payments & Contributions',
            'payouts' => 'Withdrawals & Payouts',
            'notifications' => 'Notifications',
            'billing' => 'Subscription & Billing',
            'technical' => 'Technical Issues',
            'fraud' => 'Fraud & Safety',
        ];
        return $labels[$category] ?? ucfirst($category);
    }

    /**
     * Get status label
     */
    public static function getStatusLabel(string $status): string
    {
        $labels = [
            'open' => 'Open',
            'in_review' => 'In Review',
            'awaiting' => 'Awaiting Response',
            'escalated' => 'Escalated',
            'resolved' => 'Resolved',
            'closed' => 'Closed',
        ];
        return $labels[$status] ?? ucfirst($status);
    }

    /**
     * Get priority label with SLA
     */
    public static function getPriorityInfo(string $priority): array
    {
        $info = [
            'low' => ['label' => 'Low', 'sla' => '72hr SLA'],
            'medium' => ['label' => 'Medium', 'sla' => '48hr SLA'],
            'high' => ['label' => 'High', 'sla' => '24hr SLA'],
            'critical' => ['label' => 'Critical', 'sla' => '6–12hr SLA'],
        ];
        return $info[$priority] ?? ['label' => ucfirst($priority), 'sla' => 'Standard SLA'];
    }
}
