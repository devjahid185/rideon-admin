<?php

namespace App\Models;

use Laravel\Sanctum\PersonalAccessToken as SanctumPersonalAccessToken;

class PersonalAccessToken extends SanctumPersonalAccessToken
{
    protected $fillable = [
        'name',
        'token',
        'abilities',
        'expires_at',
        'called_ip',
    ];

    protected $casts = [
        'abilities' => 'array',
        'expires_at' => 'datetime',
    ];
}
