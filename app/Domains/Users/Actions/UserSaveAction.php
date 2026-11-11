<?php

namespace App\Domains\Users\Actions;

use App\Models\Billings\Subscriptions\SubscribedUserApp;
use App\Models\Users\User;

class UserSaveAction
{
	public function execute(array $validated, $CURRENT_USER, $TARGET_USER, $USER_UID, $USERNAME, $ROLE, $emailToChange, $emailChanged, $passwordToStore)
	{
			$savedUser = User::updateOrCreate(
				[
					'id' => $validated['id'] ?? null
				],
				[
					'created_by' => $TARGET_USER ? $TARGET_USER->created_by : $CURRENT_USER->id,
					'user_uid' => $USER_UID,
					'username' => $USERNAME,
					'name' => $validated['name'],
					'user_role' => $ROLE->role_key,
					'email' => $emailToChange,
					'email_verified_at' => $emailChanged ? now() : ($TARGET_USER ? $TARGET_USER->email_verified_at : now()),
					'phone' => $validated['phone'] ?? null,
					'password' => $passwordToStore,

					'guardian' => $validated['guardian'] ?? null,
					'position' => ($ROLE && strtolower($ROLE->role_key) === 'teacher') ? ($validated['position'] ?? null) : null,
					'address' => $validated['address'] ?? null,

					'avatar' => $validated['avatar']['path'] ?? null,
					'status' => $validated['status'] ?? 1,
				]
			);

		return $savedUser;
	}
}

/*
USE CASE:
	use App\Actions\Billings\SubscriptionTierSaveAction;
	public function store(TierValidationRequest $request, UserSaveAction $action)
	{
		... ... ...
		$userSaveAction = $action->execute($validated, $CURRENT_USER, $TARGET_USER, $USER_UID, $USERNAME, $ROLE, $emailToChange, $emailChanged, $passwordToStore);
	}
*/