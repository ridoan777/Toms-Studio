<?php

namespace App\Models\System\Notifications;

use App\Models\Users\User;
use Illuminate\Database\Eloquent\Model;

class InAppNotification extends Model
{
	protected $table = 'in_app_notifications';

	protected $fillable = [
		'user_id',
		'trigger_place',

		'type',	// login, info, order, system, message, payment
		'severity',	// normal, alert, warning, critical

		'title',
		'message',

		'focus_name',
		'focus_image',

		'action_url',
		'action_label',

		'status',
	];

	protected $casts = [
		'user_id' => 'integer',
		'status' => 'integer',
	];

	// ----------------- RELATIONSHIPS -----------------
	public function inAppNotifyRelatingBackTo_User()
	{
		return $this->belongsTo(User::class, 'user_id');
	}
	// ----------------- RELATIONSHIPS -----------------


	// ----------------- QUERY SCOPING -----------------
	public function scopeStatus($query, $status)
	{
		return $query->where('status', $status);
	}
	// ----------------- QUERY SCOPING -----------------
}
