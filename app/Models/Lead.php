<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'use_case',
        'pain',
        'trust',
        'likelihood',
        'ip_address',
    ];
}
