<?php

namespace App\Models\Communication\Chatting;

use App\Models\Users\User;
use Illuminate\Database\Eloquent\Model;

class ChatMessage extends Model
{
	protected $table = 'chat_messages';
	protected $fillable = [
		'chat_group_id',
		'sender_id',
		'receiver_id',
		'text',
		'status',
	];

	protected function casts(): array
	{
		return [
			'chat_group_id' => 'integer',
			'receiver_id' => 'integer',
			'sender_id' => 'integer',
			'status' => 'integer',
		];
	}

	// ----------------- RELATIONSHIPS -----------------
	public function chatMessageRelatingBackTo_Group()
	{
		return $this->belongsTo(ChatGroup::class, 'chat_group_id');
	}

	public function chatSenderRelationWith_user()
	{
		return $this->belongsTo(User::class, 'sender_id');
	}

	public function chatReceiverRelationWith_user()
	{
		return $this->belongsTo(User::class, 'receiver_id');
	}

	public function chatMessageRelationWith_Gallery()
	{
		return $this->hasMany(ChatGallery::class, 'message_id');
	}
	// ----------------- RELATIONSHIPS -----------------

	// ----------------- QUERY SCOPING -----------------
	public function scopeStatus($query, $status)
	{
		return $query->where('status', $status);
	}
	// ----------------- QUERY SCOPING -----------------
}
