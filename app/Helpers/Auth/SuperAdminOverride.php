<?php

namespace App\Helpers\Auth;

use App\Models\Users\User;

class SuperAdminOverride
{
	public static function prevention($request, ?User $CURRENT_USER, ?User $TARGET_USER = null)
	{
		$restrictedRoles = ['admin'];
		$restrictedSuperRoles = ['super_admin'];

		$TARGET_ROLE = $request->user_role ? strtolower($request->user_role) : null;
		$TARGET_USER_ROLE = $TARGET_USER ? strtolower($TARGET_USER?->roles()->value('role_key')) : null;

		$lockdownTrigger = false;
		$message = "";

		$isSameUser = $TARGET_USER ? ((int)$CURRENT_USER->id === (int)$TARGET_USER->id) : null;

		// ------------------ Who can change ------------------
		$hasAdminAccess = $CURRENT_USER?->hasRoleKey('super_admin') || $CURRENT_USER->can('role_admin_assignment');
		$hasSuperAccess = $CURRENT_USER?->hasRoleKey('super_admin') || $CURRENT_USER->can('role_super_admin_assignment');
		// ------------------ Who can change ------------------

		$isRestrictedTargetRole = $TARGET_USER && in_array($TARGET_ROLE, $restrictedRoles);
		$isTargetUserRoleRestricted = $TARGET_USER ? in_array($TARGET_USER_ROLE, $restrictedRoles) : null;

		$isSuperRestrictedTargetRole = $TARGET_USER && in_array($TARGET_ROLE, $restrictedSuperRoles);
		$isTargetUserRoleSuperRestricted = $TARGET_USER ? in_array($TARGET_USER_ROLE, $restrictedSuperRoles) : null;

		// ------------------ Super Admin Assignment ------------------
		if (!$hasSuperAccess) {
			if($TARGET_USER === null && in_array($TARGET_ROLE, $restrictedSuperRoles)){
				$lockdownTrigger = true;
				$message = "Only Super Admin or Admin with 'role_super_admin_assignment' permission can assign Super-Admin role.";
			}

			if ($isSuperRestrictedTargetRole && ($TARGET_ROLE !== $TARGET_USER_ROLE)) {
				$lockdownTrigger = true;
				$message = "Only Super Admin or Admin with 'role_super_admin_assignment' permission can assign Super-Admin role.";
			}
			if ($isTargetUserRoleSuperRestricted && ($TARGET_ROLE !== $TARGET_USER_ROLE)) {
				$lockdownTrigger = true;
				$message = "Only Super Admin or Admin with 'role_super_admin_assignment' permission can upgrade or downgrade Super-Admin roles.";
			}
		}
		// ------------------ Super Admin Assignment ------------------

		// ------------------ Admin Assignment ------------------
		if (!$hasAdminAccess) {
			if($TARGET_USER === null && in_array($TARGET_ROLE, $restrictedRoles)){
				$lockdownTrigger = true;
				$message = "Only Super Admin or Admin with 'role_admin_assignment' permission can assign Admin";
			}

			if ($isRestrictedTargetRole && ($TARGET_ROLE !== $TARGET_USER_ROLE)) {
				$lockdownTrigger = true;
				$message = "Only Super Admin or Admin with 'role_admin_assignment' permission can assign Admin.";
			}
			if ($isTargetUserRoleRestricted && ($TARGET_ROLE !== $TARGET_USER_ROLE)) {
				$lockdownTrigger = true;
				$message = "Only Super Admin or Admin with 'role_admin_assignment' permission can upgrade or downgrade Admin.";
			}
		}
		// ------------------ Admin Assignment ------------------

		// ------------------ SuperAdmin Lockdown Prevention ------------------
		$superLockdown = null;
		$adminLockdown = null;
		if($isSameUser && ($TARGET_ROLE !== $CURRENT_USER->roles()->value('role_key')) && !$hasSuperAccess){
			$lockdownTrigger = true;
			$message = "You cannot modify your own role.";
		} else{
			$superLockdown = ($TARGET_USER_ROLE === 'super_admin') ? self::preventSuperLockdown($hasSuperAccess, $TARGET_ROLE) : null;
			$adminLockdown = ($TARGET_USER_ROLE === 'admin') ? self::preventAdminLockdown($hasAdminAccess, $TARGET_ROLE) : null;
		}
		// ------------------ SuperAdmin Lockdown Prevention ------------------

		$final = (isset($superLockdown['lockdownTrigger']) && $superLockdown['lockdownTrigger']) 
		? $superLockdown 
		: ((isset($adminLockdown['lockdownTrigger']) && $adminLockdown['lockdownTrigger']) 
			? $adminLockdown 
			: ['message' => $message, 'lockdownTrigger' => $lockdownTrigger]);

		return $final;
	}
	// --------------------------------------

	public static function preventSuperLockdown($hasSuperAccess, $TARGET_ROLE)
	{
		if ($hasSuperAccess) {
			$numberOfSuperAdmins = User::whereHas('roles', function ($fn) {
				$fn->where('role_key', 'super_admin');
			})->count();

			if ($TARGET_ROLE && ($TARGET_ROLE !== 'super_admin') && ($numberOfSuperAdmins <= 1)) {
				return [
					'lockdownTrigger' => true,
					'message' => "Lockdown prevented! You are attempting to change the only super-admin.",
				];
			}
		}
		return [
			'lockdownTrigger' => false,
			'message' => null
		];
	}
	// --------------------------------------

	public static function preventAdminLockdown($hasAdminAccess, $TARGET_ROLE)
	{
		if ($hasAdminAccess) {
			$numberOfAdmins = User::whereHas('roles', function ($fn) {
				$fn->where('role_key', 'admin');
			})->count();

			if ($TARGET_ROLE && ($TARGET_ROLE !== 'admin') && ($numberOfAdmins <= 1)) {
				return [
					'lockdownTrigger' => true,
					'message' => "Lockdown prevented! You are attempting to change the only admin.",
				];
			}
		}
		return [
			'lockdownTrigger' => false,
			'message' => null
		];
	}
}
/*
	This block was called from WebUserController::store(), by:
		$superAdminOverride = SuperAdminOverride::prevention($request, $CURRENT_USER, $TARGET_USER);
	
	to check if the current user is eligible to change the role of target user. Also to prevent lockdown when the last super_admin/admin tries to change the role.
*/
