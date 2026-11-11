<?php

namespace App\Models\Communication\Chatting;

use App\Models\Users\User;
use Illuminate\Database\Eloquent\Model;

class ChatBlocklist extends Model
{
	protected $table = 'chat_blocklists';

	protected $fillable = [
		'victim_id',
		'blocked_id',
		'composite_a',
		'composite_b',
		'reason',

		'is_blocked',
		'status',
	];

	protected function casts(): array
	{
		return [
			'victim_id' => 'integer',
			'blocked_id' => 'integer',
			'is_blocked' => 'integer',
			'status' => 'integer',
		];
	}

	public function victimRelationWith_User()
	{
		return $this->belongsTo(User::class, 'victim_id', 'id');
	}

	public function blockedRelationWith_User()
	{
		return $this->belongsTo(User::class, 'blocked_id', 'id');
	}
	
	// ----------------- QUERY SCOPING -----------------
	public function scopeVictim($query, $victim_id)
	{
		return $query->where('victim_id', $victim_id);
	}
	
	public function scopeBlocked($query, $blocked_id)
	{
		return $query->where('blocked_id', $blocked_id);
	}

	public function scopeStatus($query, $status)
	{
		return $query->where('status', $status);
	}
	// ----------------- QUERY SCOPING -----------------
}
