<?php

namespace App\Models\System\Settings;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class OptionSiteSetup extends Model
{
	protected $table = 'option_site_setups';

	protected $fillable = [
		'type',
		'name',
		'value',
		'log',
		'status',
	];
	
	protected function casts(): array
	{
		return [
			'status' => 'integer',
		];
	}

	// ----------------- QUERY SCOPING -----------------
	public function scopeType($query, $type)
	{
		return $query->where('type', $type);
	}
	
	public function scopeName($query, $name)
	{
		return $query->where('name', $name);
	}
	
	public function scopeValue($query, $value)
	{
		return $query->where('value', $value);
	}
	
	public function scopeStatus($query, $status)
	{
		return $query->where('status', $status);
	}
	// ----------------- QUERY SCOPING -----------------


	// ----------------- URL FRIENDLY IMAGE -----------------
	protected $appends = ['value_image_url'];

	public function getValueImageUrlAttribute()
	{
		if ($this->value) {
			return Storage::url($this->value);
		}
		return null;
	}
	// ----------------- URL FRIENDLY IMAGE -----------------
}
