<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Tymon\JWTAuth\Contracts\JWTSubject;

/**
 * User Model
 * 
 * This class represents the User entity in the system.
 * It handles authentication, permissions, and user-related relationships.
 * 
 * @property string $name
 * @property string $email
 * @property string $mobile
 * @property string $password
 * @property \DateTime|null $email_verified_at
 */
class User extends Authenticatable implements JWTSubject
{
    /** 
     * Traits implementation
     * HasFactory - For model factory support
     * Notifiable - For notification handling
     * HasRoles - For user roles and permissions
     * HasApiTokens - For API token management
     */
    use HasFactory, Notifiable, HasRoles, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $guarded = [];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Define attribute casting for type consistency.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the identifier that will be stored in the JWT token.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Add custom claims to the JWT token.
     *
     * @return array<string, mixed>
     */
    public function getJWTCustomClaims(): array
    {
        return [];
    }

    /**
     * Define relationship with UserBank model.
     * One user can have multiple bank accounts.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function userBank()
    {
        return $this->hasMany(UserBank::class);
    }

    /**
     * Get groups owned by the user
     */
    public function ownedGroups(): HasMany
    {
        return $this->hasMany(Group::class, 'owner_id');
    }

    /**
     * Get groups user is a member of
     */
    public function groups(): BelongsToMany
    {
        return $this->belongsToMany(Group::class)
            ->using(GroupUser::class)
            ->withPivot(['role', 'is_active'])
            ->withTimestamps();
    }

    /**
     * Get group memberships
     */
    public function groupMemberships(): HasMany
    {
        return $this->hasMany(GroupUser::class);
    }
}
