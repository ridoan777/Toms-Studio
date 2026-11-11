<?php

namespace App\Models\Communication\Chatting;

use App\Models\Users\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ChatGroupMember extends Model
{
	protected $table = 'chat_group_members';
	protected $fillable = [
		'chat_group_id',
		'member_id',
		'adder_id',
		'member_role',	// member, moderator, room_admin
		'username_in_room',
		'avatar_in_room',
		'status',
	];

	protected function casts(): array
	{
		return [
			'chat_group_id' => 'integer',
			'member_id' => 'integer',
			'adder_id' => 'integer',
			'status' => 'integer',
		];
	}

	// ----------------- RELATIONSHIPS -----------------
	public function chatMemberRelatingBackTo_Group()
	{
		return $this->belongsTo(ChatGroup::class, 'chat_group_id');
	}
	
	public function chatGroupMemberRelatingBackTo_User()
	{
		return $this->belongsTo(User::class, 'member_id');
	}
	// ----------------- RELATIONSHIPS -----------------


	// ----------------- QUERY SCOPING -----------------
	public function scopeGroupId($query, $chat_group_id)
	{
		return $query->where('chat_group_id', $chat_group_id);
	}

	public function scopeMember($query, $member_id)
	{
		return $query->where('member_id', $member_id);
	}

	public function scopeMemberRole($query, $member_role)
	{
		return $query->where('member_role', $member_role);
	}

	public function scopeStatus($query, $status)
	{
		return $query->where('status', $status);
	}
	// ----------------- QUERY SCOPING -----------------
}
