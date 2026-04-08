<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contribution extends Model
{
    use HasFactory, SoftDeletes, HasUuids;

    protected $fillable = [
        'group_id',
        'user_id',
        'cycle_number',
        'amount',
        'proof_path',
        'note',
        'due_date',
        'status',
        'submitted_at',
    ];

    protected $casts = [
        'amount'       => 'decimal:2',
        'due_date'     => 'date',
        'submitted_at' => 'datetime',
    ];

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
