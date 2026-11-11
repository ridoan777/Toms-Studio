<?php

namespace App\Models\Workflows\Lessons;

use App\Models\Users\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Attributes\{Table, Fillable};
use Illuminate\Database\Eloquent\Model;

#[Table('lesson_notes')]
#[Fillable(['note_uid', 'lesson_id', 'group_id', 'student_id', 'title', 'note', 'creator_id', 'last_modifier_id', 'status'])]

class LessonNote extends Model
{
	protected function casts(): array
	{
		return [
			'lesson_id' => 'integer',
			'group_id' => 'integer',
			'student_id' => 'integer',
			'creator_id' => 'integer',
			'last_modifier_id' => 'integer',
			'status' => 'boolean',
		];
	}

	// ----------------- RELATIONSHIPS -----------------

	public function noteRelatingBackTo_Lesson()
	{
		return $this->belongsTo(Lesson::class, 'lesson_id');
	}

	public function noteRelatingBackTo_Group()
	{
		return $this->belongsTo(LessonGroup::class, 'group_id');
	}

	public function noteRelatingBackTo_Student()
	{
		return $this->belongsTo(User::class, 'student_id');
	}

	public function noteRelationWith_Gallery()
	{
		return $this->hasMany(LessonNoteGallery::class, 'note_id');
	}

	public function noteRelatingBackTo_Creator()
	{
		return $this->belongsTo(User::class, 'creator_id');
	}

	public function noteRelatingBackTo_LastModifier()
	{
		return $this->belongsTo(User::class, 'last_modifier_id');
	}
	// ----------------- RELATIONSHIPS -----------------


	// ----------------- QUERY SCOPING -----------------
	public function scopeToday($query)
	{
		$todayDay = strtolower(Carbon::today()->format('l'));

		return $query->whereHas('noteRelatingBackTo_Lesson', function ($q) use ($todayDay) {
			$q->whereRaw('LOWER(schedule_day) = ?', [$todayDay]);
		})->orWhereHas('noteRelatingBackTo_Group', function ($q) use ($todayDay) {
			$q->whereRaw('LOWER(schedule_day) = ?', [$todayDay]);
		});
	}

	public function scopeLessonId($query, int $lesson_id)
	{
		return $query->where('lesson_id', $lesson_id);
	}

	public function scopeStudentId($query, int $student_id)
	{
		return $query->where('student_id', $student_id);
	}

	public function scopeStatus($query, $status)
	{
		return $query->where('status', $status);
	}
	// ----------------- QUERY SCOPING -----------------
}
/*
Error! Action error: App\Helpers\FileHelpers\GalleryManagement::handle(): Argument #8 ($uploadedPaths) must be of type array, null given, called in C:\Users\Ridoan\SoftVence-Projects\tech_wix\9-TomsPerformance\app\Http\Controllers\Workflows\Lessons\WebLessonNoteController.php on line 123

*/