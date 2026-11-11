<?php

namespace App\Models\System\Settings;

use App\Models\Users\User;
use Illuminate\Database\Eloquent\Model;

class UserAppPreference extends Model
{
	protected $table = 'user_app_preferences';

	protected $fillable = [
		'user_id',
		'in_app_notification',
		'email_notification',
		'push_notification',
		'activity_log',

		'status',
	];

	protected $casts = [
		'user_id' => 'integer',
		'in_app_notification' => 'integer',
		'email_notification' => 'integer',
		'push_notification' => 'integer',
		'activity_log' => 'integer',
		'status' => 'integer',
	];

	// ----------------- RELATIONSHIPS -----------------
	public function appPreferenceRelatingBackTo_user()
	{
		return $this->belongsTo(User::class, 'user_id');
	}
	// ----------------- RELATIONSHIPS -----------------

	// ----------------- QUERY SCOPING -----------------
	public function scopeInAppNotification($query, int $value = 1)
	{
		return $query->where('in_app_notification', $value);
	}

	public function scopeEmailNotification($query, int $value = 1)
	{
		return $query->where('email_notification', $value);
	}

	public function scopePushNotification($query, int $value = 1)
	{
		return $query->where('push_notification', $value);
	}

	public function scopeActivityLog($query, int $value = 1)
	{
		return $query->where('activity_log', $value);
	}

	public function scopeStatus($query, $status)
	{
		return $query->where('status', $status);
	}
	// ----------------- QUERY SCOPING -----------------
}
