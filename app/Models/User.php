<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Soft\SoftDeletes;
use Illuminate\Support\Str;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject, MustVerifyEmail
{
    use Notifiable;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'password',
        'mobile',
        'email_verified_at',
        'password_reset_code',
        'password_reset_expires_at',
        'email_verification_sent_at',
        'referral_code',
        'referred_by',
        'referral_points',
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'referral_points' => 'integer',
    ];

    /**
     * Boot method - generate referral code on user creation
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($user) {
            $user->referral_code = self::generateUniqueReferralCode($user);
        });
    }

    /**
     * Generate unique referral code
     */
    public static function generateUniqueReferralCode($user): string
    {
        do {
            $code = strtoupper(substr($user->name, 0, 3)) . '-' . strtoupper(Str::random(4));
        } while (self::where('referral_code', $code)->exists());

        return $code;
    }

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

    /**
     * User who referred this user
     */
    public function referrer()
    {
        return $this->belongsTo(User::class, 'referred_by');
    }

    /**
     * Users that this user has referred (referral records)
     */
    public function referrals()
    {
        return $this->hasMany(Referral::class, 'referrer_id');
    }

    /**
     * Active referrals only
     */
    public function activeReferrals()
    {
        return $this->referrals()->where('status', Referral::STATUS_ACTIVE);
    }

    /**
     * Pending referrals
     */
    public function pendingReferrals()
    {
        return $this->referrals()->where('status', Referral::STATUS_PENDING);
    }

    /**
     * Add referral points
     */
    public function addReferralPoints(int $points): void
    {
        $this->increment('referral_points', $points);
    }

    /**
     * Get referral statistics
     */
    public function getReferralStats(): array
    {
        return [
            'total_referrals' => $this->referrals()->count(),
            'active_referrals' => $this->activeReferrals()->count(),
            'pending_referrals' => $this->pendingReferrals()->count(),
            'total_points' => $this->referral_points,
        ];
    }
}
