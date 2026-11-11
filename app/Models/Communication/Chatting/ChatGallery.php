<?php

namespace App\Models\Communication\Chatting;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ChatGallery extends Model
{
	protected $table = 'chat_galleries';

	protected $fillable = [
		'chat_group_id',
		'message_id',
		'sender_id',
		'receiver_id',

		'file',
		'metadata',

		'status',
	];

	protected function casts(): array
	{
		return [
			'chat_group_id' => 'integer',
			'message_id' => 'integer',
			'sender_id' => 'integer',
			'receiver_id' => 'integer',
			'metadata' => 'array',
			'status' => 'integer',
		];
	}

	// ----------------- RELATIONSHIPS -----------------
	public function galleryRelatingBackTo_ChatMessage()
	{
		return $this->belongsTo(ChatMessage::class, 'message_id', 'id');
	}

	// ----------------- QUERY SCOPING -----------------
	public function scopeStatus($query, $status)
	{
		return $query->where('status', $status);
	}
	// ----------------- QUERY SCOPING -----------------

	// ----------------- URL FRIENDLY IMAGE -----------------
	protected $appends = ['chat_file_url'];

	public function getChatFileUrlAttribute()
	{
		if ($this->file) {
			return Storage::url($this->file);
		}

		return null;
	}
	// ----------------- URL FRIENDLY IMAGE -----------------
}
