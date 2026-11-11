<?php

namespace App\Models\System\Settings;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class OptionEnv extends Model
{
	protected $table = 'option_envs';

	protected $fillable = [
		'var_type',

		'name',
		'value',
		'needEncrypt',

		'status',
	];

	protected $casts = [
		'needEncrypt' => 'boolean',
		'status' => 'boolean',
	];

	public function setValueAttribute($value)
	{
		$needEncrypt = $this->attributes['needEncrypt'] ?? ($this->needEncrypt ?? false);
		if ($needEncrypt && !is_null($value) && $value !== '') {
			$this->attributes['value'] = Crypt::encryptString($value);
		} else {
			$this->attributes['value'] = $value;
		}
	}

	public function getValueAttribute($value)
	{
		if (!is_null($value) && $value !== '') {
			try {
				return Crypt::decryptString($value);
			} catch (Exception $e) {
				return $value;
			}
		}
		return $value;
	}

	// ----------------- QUERY SCOPING -----------------
	public function scopeVarType($query, $var_type)
	{
		return $query->where('var_type', $var_type);
	}
	public function scopeStatus($query, $status)
	{
		return $query->where('status', $status);
	}
	// ----------------- QUERY SCOPING -----------------

}
