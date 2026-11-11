<?php

namespace App\Models\System\Settings;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class OptionStaticPage extends Model
{
	protected $fillable = [
		'type',
		'title',
		'body',
		'feature_image',

		'status',
	];

	protected function casts(): array
	{
		return [
			'status' => 'integer',
		];
	}

	// ----------------- QUERY SCOPING -----------------
	public function scopeStatus($query, $status)
	{
		return $query->where('status', $status);
	}
	// ----------------- QUERY SCOPING -----------------

	// ----------------- URL FRIENDLY IMAGE -----------------
	protected $appends = ['feature_image_url'];

	public function getFeatureImageUrlAttribute()
	{
		if ($this->feature_image) {
			return Storage::url($this->feature_image);
		}

		return null;
	}
	// ----------------- URL FRIENDLY IMAGE -----------------
}
