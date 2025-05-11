<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GroupUser extends Pivot
{
    protected $table = 'group_user';

    protected $fillable = [
        'user_id',
        'group_id',
        'role',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    /**
     * Get the user that belongs to this membership
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the group that belongs to this membership
     */
    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }
}