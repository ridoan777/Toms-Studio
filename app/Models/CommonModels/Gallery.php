<?php

namespace App\Models\CommonModels;

use Illuminate\Database\Eloquent\Model;

class Gallery extends Model
{
	protected $fillable = [
		'section',
		'content_id',
		'content_type',
		'user_id',

		'title',
		'description',
		'path',
		
		'status',
	];

	public function morphContents()
	{
		return $this->morphTo();
	}
}
