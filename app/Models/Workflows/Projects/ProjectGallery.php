<?php

namespace App\Models\Workflows\Projects;

use App\Models\Users\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ProjectGallery extends Model
{
	protected $table = 'project_galleries';

	protected $fillable = [
		'project_id',

		'filename',
		'file',
		'metadata',

		'status',
	];

	protected function casts(): array
	{
		return [
			'project_id' => 'integer',
			'metadata' => 'array',
			'status' => 'integer',
		];
	}

	public function galleryRelatingBackTo_Project()
	{
		return $this->belongsTo(Project::class, 'project_id', 'id');
	}

	// ----------------- QUERY SCOPING -----------------
	public function scopeProject($query, int $project_id)
	{
		return $query->where('project_id', $project_id);
	}
	
	public function scopeStatus($query, $status)
	{
		return $query->where('status', $status);
	}
	// ----------------- QUERY SCOPING -----------------


	// ----------------- URL FRIENDLY IMAGE -----------------
	protected $appends = ['file_url'];

	public function getFileUrlAttribute()
	{
		if ($this->file) {
			return Storage::url($this->file);
		}
		return null;
	}
	// ----------------- URL FRIENDLY IMAGE -----------------
}
/*
1
2	- 	2	id + remove = 1
3	-	3	id + file
		4	file
		5	file

*/