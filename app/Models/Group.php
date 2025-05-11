<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Group extends Model
{
    protected $guarded = [];

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
     * Get all users that are members of this group
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'group_user')
            ->using(GroupUser::class)
            ->withPivot(['role', 'is_active'])
            ->withTimestamps();
    }

    /**
     * Alias for users() relationship
     */
    public function members(): BelongsToMany
    {
        return $this->users();
    }
}
