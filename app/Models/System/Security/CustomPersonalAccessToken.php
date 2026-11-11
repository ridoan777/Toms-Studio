<?php

namespace App\Models\System\Security;

use Laravel\Sanctum\PersonalAccessToken as SanctumPersonalAccessToken;

class CustomPersonalAccessToken extends SanctumPersonalAccessToken
{
    protected $table = 'personal_access_tokens';
    protected $fillable = [
        'name',
        'token',
        'abilities',
        'expires_at',
        'device',
    ];
}
