<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject, MustVerifyEmail
{
    use Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'mobile',
        'email_verified_at',
        'password_reset_code',
        'password_reset_expires_at',
        'email_verification_sent_at',
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    /**
     * Check if email is verified
     */
    public function isEmailVerified(): bool
    {
        return !is_null($this->email_verified_at);
    }

    /**
     * Get user's unread notifications
     */
    public function unreadNotifications()
    {
        return $this->notifications()->unread();
    }

    /**
     * Get the number of unread notifications
     */
    public function unreadNotificationsCount(): int
    {
        return $this->unreadNotifications()->count();
    }

    /**
     * Get user's notifications
     */
    public function notifications()
    {
        return $this->morphMany(Notification::class, 'notifiable')
            ->orderBy('created_at', 'desc');
    }
}
