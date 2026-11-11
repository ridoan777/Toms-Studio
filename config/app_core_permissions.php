<?php

// Custom-made file to limit permission groups. Single source of truth for permissions.
// Super Admins can create new permissions; Admins can only assign and create new roles.

return [

	'role' => [
		['permission_name' => 'index', 'note' => "To view the roles page."],
		['permission_name' => 'show', 'note' => "To view a single role page."],
		['permission_name' => 'create', 'note' => "To create new roles."],
		['permission_name' => 'update', 'note' => "To update roles"],
		['permission_name' => 'delete', 'note' => "To delete roles"],
		['permission_name' => 'core_roles', 'note' => "⚠️Sensitive! Can modify or delete core roles. System might break in some cases. Assign only to super_admins."],
		['permission_name' => 'admin_assignment', 'note' => "⚠️Sensitive! Can assign admin roles to users."],
		['permission_name' => 'super_admin_assignment', 'note' => "⚠️Sensitive! Can assign super_admin roles to users."],
	],

	'admin' => [
		['permission_name' => 'index', 'note' => null],
		['permission_name' => 'show', 'note' => null],
		['permission_name' => 'create', 'note' => "⚠️Sensitive! Should not be assigned to anyone but super admins. This marks the users having superAccess."],
		['permission_name' => 'update', 'note' => "⚠️Sensitive! Should not be assigned to anyone but super admins. This marks the users having superAccess."],
		['permission_name' => 'delete', 'note' => "Can delete users with admin roles."],
		['permission_name' => 'email', 'note' => "Add this to admin, otherwise they can't even change their own email."],
		['permission_name' => 'password', 'note' => "⚠️Sensitive! Should not be assigned to anyone but super admins."],
	],

	'general' => [
		['permission_name' => 'index', 'note' => "To view general list."],
		['permission_name' => 'show', 'note' => "To view general details."],
		['permission_name' => 'create', 'note' => "To create new general item."],
		['permission_name' => 'update', 'note' => "To update general item."],
		['permission_name' => 'delete', 'note' => "To delete general item."],
	],

	'project' => [
		['permission_name' => 'index', 'note' => "To view project list."],
		['permission_name' => 'show', 'note' => "To view project details."],
		['permission_name' => 'create', 'note' => "To create new project."],
		['permission_name' => 'update', 'note' => "To update project."],
		['permission_name' => 'delete', 'note' => "To delete project."],
	],

	'user' => [
		['permission_name' => 'index', 'note' => "To view user list."],
		['permission_name' => 'show', 'note' => "To view user details."],
		['permission_name' => 'create', 'note' => "To create new user."],
		['permission_name' => 'update', 'note' => "To update user."],
		['permission_name' => 'delete', 'note' => "To delete user."],
		['permission_name' => 'admin_view', 'note' => "To view admins."],
	],

	'profile' => [
		['permission_name' => 'index', 'note' => "To view own user data, including sensitive ones."],
		['permission_name' => 'show', 'note' => "To view own user details."],
		['permission_name' => 'create', 'note' => "Generally non-functional! But can be useful to prevent certain user groups from adding exclusive fields like phone/bio to their profiles."],
		['permission_name' => 'update', 'note' => "To update own user data."],
		['permission_name' => 'delete', 'note' => "✅To delete own account. Must be checked for all to pass App Store verification."],
	],

	'subscription' => [
		['permission_name' => 'index', 'note' => "To view subscription list."],
		['permission_name' => 'show', 'note' => "To view subscription details."],
		['permission_name' => 'create', 'note' => "To create new subscription."],
		['permission_name' => 'update', 'note' => "To update old generic/stale subscription data. Doesn't update transactions."],
		['permission_name' => 'delete', 'note' => "To delete subscription data. Doesn't update transactions."],
		['permission_name' => 'cancel', 'note' => "To cancel subscription."],
	],

	'payment' => [
		['permission_name' => 'index', 'note' => "To view payment list."],
		['permission_name' => 'show', 'note' => "To view payment details."],
		['permission_name' => 'create', 'note' => "To create new payment."],
		['permission_name' => 'update', 'note' => "To update old generic/stale payment data. Doesn't update transactions."],
		['permission_name' => 'delete', 'note' => "To delete payment data. Doesn't update transactions."],
		['permission_name' => 'cancel', 'note' => "To cancel payment."],
	],

	'lesson' => [
		['permission_name' => 'index', 'note' => "To view lesson list."],
		['permission_name' => 'show', 'note' => "To view lesson details."],
		['permission_name' => 'create', 'note' => "To create new lesson."],
		['permission_name' => 'update', 'note' => "To update a lesson."],
		['permission_name' => 'delete', 'note' => "To delete a lesson."],
		['permission_name' => 'cancel', 'note' => "To cancel a lesson."],
		['permission_name' => 'schedule', 'note' => "To reschedule a lesson."],
	],

	'notes' => [
		['permission_name' => 'index', 'note' => "To view note list."],
		['permission_name' => 'show', 'note' => "To view note details."],
		['permission_name' => 'create', 'note' => "To create new note."],
		['permission_name' => 'update', 'note' => "To update note."],
		['permission_name' => 'delete', 'note' => "To delete note."],
		['permission_name' => 'file_edit', 'note' => "To upload a file while creating/updating a note."],
		['permission_name' => 'file_view', 'note' => "To view a note file."],
	],

];