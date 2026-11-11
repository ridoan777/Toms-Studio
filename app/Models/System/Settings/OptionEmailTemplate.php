<?php

namespace App\Models\System\Settings;

use Illuminate\Database\Eloquent\Model;

class OptionEmailTemplate extends Model
{
	protected $table = 'option_email_templates';
	protected $fillable = [
		'flag',

		'subject',
		'greeting',

		'body_message',
		'end_message',

		'support_message',
		'support_details',

		'status',
	];
}
