<?php

namespace App\Models\Users;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class UserVerification extends Model
{
    protected $fillable = ['user_id', 'type', 'otp', 'new_value', 'expiry_duration', 'expires_at'];

    protected $casts = [
        'expiry_duration' => 'integer',
        'expires_at' => 'datetime',
    ];

    public function isExpired()
    {
        return $this->expires_at->isPast();
    }

    public static function generateOTP($digit = 4)
    {
        $max = (10 ** $digit) - 1;
        return str_pad(random_int(0, $max), $digit, '0', STR_PAD_LEFT);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ----------------- QUERY SCOPING -----------------
    public function scopeUserId($query, int $user_id)
    {
        return $query->where('user_id', $user_id);
    }

    public function scopeType($query, $type)
    {
        return $query->where('type', $type);    // email_change
    }

    public function scopeExpiresAt($query, $expires_at)
    {
        return $query->where('expires_at', $expires_at);
    }
    // ----------------- QUERY SCOPING -----------------
}
