<?php

namespace App\Models\Workflows\Projects;

use App\Models\Users\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Project extends Model
{
	protected $table = 'projects';

	protected $fillable = [
		'project_uid',
		'project_manager_id',

		'title',

		'phase',		// on-track, completed, disputed, cancelled
		'progress',
		'location',

		'start_date',
		'target_date',

		'description',
		'image',
		'video',
		// 'video_gallery',

		'status',
	];

	protected function casts(): array
	{
		return [
			'video' => 'array',
			// 'video_gallery' => 'array',
		];
	}


	public function projectRelatingBackTo_User()
	{
		return $this->belongsTo(User::class, 'project_manager_id', 'id');
	}

	// ----------------- QUERY SCOPING -----------------
	public function scopeStatus($query, $status)
	{
		return $query->where('status', $status);
	}
	// ----------------- QUERY SCOPING -----------------


	// ----------------- URL FRIENDLY IMAGE -----------------
	protected $appends = ['project_image_url'];

	public function getProjectImageUrlAttribute()
	{
		if ($this->image) {
			return Storage::url($this->image);
		}

		return null;
	}
	// ----------------- URL FRIENDLY IMAGE -----------------
}
