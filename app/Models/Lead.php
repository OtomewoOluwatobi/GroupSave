<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Lead extends Model
{
    use HasUuids, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'source',
        'location',
        'cooking_habit',
        'grocery_frequency',
        'pain_points',
        'hassle_score',
        'likelihood_score',
        'fee_pref',
        'delivery_pref',
    ];

    protected $casts = [
        'hassle_score'     => 'integer',
        'likelihood_score' => 'integer',
    ];
}
