<?php

/* this is a custom-made file to define specific role, permission, access instead of hard-coding. Let's say, I decide admins cannot edit other admins. I put:

		'ADMIN_CAN_EDIT_ADMINS => false'. 

	Then in controller, I do:
		$CURRENT_USER_CAN_EDIT_ADMIN = config('app.allow_admin_edit') && $CURRENT_USER?->canAny(['admin_create','admin_update']);
*/

return [
	'permissions' => [
		// 'admin_allowed_for_admin_email' => env('ALLOW_ADMIN_EMAIL_CHANGE', true),
		// 'admin_allowed_for_admin_password' => env('ALLOW_ADMIN_PASSWORD_CHANGE', true),
		'allowed_changing_own_email' => env('CAN_CHANGE_OWN_EMAIL', true),
		'allowed_changing_own_password' => env('CAN_CHANGE_OWN_PASSWORD', true),
	]
];