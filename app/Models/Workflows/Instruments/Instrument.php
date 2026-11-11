<?php

namespace App\Models\Workflows\Instruments;

use Illuminate\Database\Eloquent\Attributes\{Table,Fillable};
use Illuminate\Database\Eloquent\Model;

#[Fillable(['name', 'note', 'status'])]
#[Table('instruments')]

class Instrument extends Model
{
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
}
