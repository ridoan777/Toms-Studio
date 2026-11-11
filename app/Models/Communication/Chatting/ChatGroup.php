<?php

namespace App\Models\Communication\Chatting;

use App\Models\Users\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ChatGroup extends Model
{
	protected $table = 'chat_groups';
	protected $fillable = [
		'chat_group_uid',
		'creator_id',
		'title',
		'description',
		'total_members',
		'image',
		'status',
	];

	protected function casts(): array
	{
		return [
			'creator_id' => 'integer',
			'total_members' => 'integer',
		];
	}

	// ----------------- RELATIONSHIPS -----------------
	public function chatGroupCreatorRelatingBackTo_User()
	{
		return $this->belongsTo(User::class, 'creator_id');
	}
	
	public function chatGroupRelationWith_Message()
	{
		return $this->hasMany(ChatMessage::class, 'chat_group_id');
	}
	// ----------------- RELATIONSHIPS -----------------


	// ----------------- QUERY SCOPING -----------------
	public function scopeGroupId($query, $id)
	{
		return $query->where('id', $id);
	}

	public function scopeCreator($query, $creator_id)
	{
		return $query->where('creator_id', $creator_id);
	}

	public function scopeStatus($query, $status)
	{
		return $query->where('status', $status);
	}
	// ----------------- QUERY SCOPING -----------------


	// ----------------- URL FRIENDLY IMAGE -----------------
	protected $appends = ['image_url'];

	public function getImageUrlAttribute()
	{
		if (!$this->image) {
			return asset('site_assets/dummies/dummy_no_file.jpg');
		}

		if (filter_var($this->image, FILTER_VALIDATE_URL)) {
			return $this->image;
		}

		return Storage::url($this->image);
	}
	// ----------------- URL FRIENDLY IMAGE -----------------
}
