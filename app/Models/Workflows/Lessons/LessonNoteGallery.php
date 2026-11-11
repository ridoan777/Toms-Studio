<?php

namespace App\Models\Workflows\Lessons;

use App\Models\Workflows\Lessons\LessonNote;
use Illuminate\Database\Eloquent\Attributes\{Appends, Table, Fillable};
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

#[Table('lesson_note_galleries')]
#[Fillable(['note_id', 'filename', 'filepath', 'metadata', 'creator_id', 'last_modifier_id', 'status'])]
#[Appends(['filepath_url'])]

class LessonNoteGallery extends Model
{
	protected function casts(): array
	{
		return [
			'note_id' => 'integer',
			'metadata' => 'array',
			'creator_id' => 'integer',
			'last_modifier_id' => 'integer',
			'status' => 'integer',
		];
	}

	public function galleryRelatingBackTo_Note()
	{
		return $this->belongsTo(LessonNote::class, 'note_id', 'id');
	}

	// ----------------- QUERY SCOPING -----------------
	public function scopeNote($query, int $note_id)
	{
		return $query->where('note_id', $note_id);
	}

	public function scopeStatus($query, $status)
	{
		return $query->where('status', $status);
	}
	// ----------------- QUERY SCOPING -----------------


	// ----------------- URL FRIENDLY IMAGE -----------------

	public function getFilepathUrlAttribute()
	{
		if ($this->filepath) {
			return Storage::url($this->filepath);
		}
		return null;
	}
	// ----------------- URL FRIENDLY IMAGE -----------------
}