<?php

namespace App\Models\Users;

use Illuminate\Database\Eloquent\Model;

class UserAccess extends Model
{
	protected $table = 'user_accesses';

	protected $fillable = [
		'user_id',
		'email',
		'user_role',

		'join_ban',
		'chat_ban',
		'gallery_ban',
		'store_ban',
		'view_ban',
		'account_hold',

		'admin_note',

		'ban_expiry',

		'status',
	];

	protected $casts = [
		'user_id' => 'integer',

		'join_ban' => 'boolean',
		'chat_ban' => 'boolean',
		'gallery_ban' => 'boolean',
		'store_ban' => 'boolean',
		'view_ban' => 'boolean',
		'account_hold' => 'boolean',

		'ban_expiry' => 'datetime',

		'status' => 'boolean',
	];

	// ----------------- RELATIONSHIPS -----------------
	public function userAccessRelationWith_User()
	{
		return $this->belongsTo(User::class, 'accused_id', 'id');
	}
	// ----------------- RELATIONSHIPS -----------------

	// ----------------- QUERY SCOPING -----------------
	public function scopeUserId($query, $user_id)
	{
		return $query->where('user_id', $user_id);
	}
	
	public function scopeChatBan($query, $chat_ban)
	{
		return $query->where('chat_ban', $chat_ban);
	}
	
	public function scopeExpiry($query, $ban_expiry)
	{
		return $query->where('ban_expiry', $ban_expiry);
	}

	public function scopeStatus($query, $status)
	{
		return $query->where('status', $status);
	}
	// ----------------- QUERY SCOPING -----------------
}
/*
	if ($user->userRelationWith_UserAccess?->isChatBanned()) {
		abort(403, 'Chat access temporarily disabled.');
	}
*/