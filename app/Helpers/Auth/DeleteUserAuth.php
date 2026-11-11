<?php

namespace App\Helpers\Auth;

use App\Helpers\Errors\ExceptionHandling;
use App\Models\Users\User;

class DeleteUserAuth
{
   // --------------------------------------
   public static function canIDelete(User $TARGET_USER, User $CURRENT_USER)
   {
      $statusCode = 403;
      $canDelete = true;
      $message = '';
      $isSameUser = $TARGET_USER ? ((int) $CURRENT_USER->id === (int) $TARGET_USER->id) : null;

      $numberOfAdmins = User::whereHas('roles', function ($fn) {
         $fn->where('role_key', 'admin');
      })->count();

      $numberOfSuperAdmins = User::whereHas('roles', function ($fn) {
         $fn->where('role_key', 'super_admin');
      })->count();


      // ---------------- SELF DELETE ----------------
      if ($isSameUser) {
         $canDelete = false;
         $message = "You can't delete yourself!";
      }
      // ---------------- SELF DELETE ----------------

      // ---------------- ADMIN ----------------
      if ($TARGET_USER->hasRoleKey('admin')) {
         if ($numberOfAdmins <= 1) {
            return [
               'statusCode' => 409,
               'canDelete' => false,
               'message' => "You cannot delete the only admin!",
            ];
         }
         if (!$CURRENT_USER->can('admin_delete')) {
            return [
               'statusCode' => 403,
               'canDelete' => false,
               'message' => "Aborting! You don't have permission(s) to delete this admin!",
            ];
         }
      }
      // ---------------- ADMIN ----------------

      // ---------------- SUPER ADMIN ----------------
      if ($TARGET_USER->hasRoleKey('super_admin')) {
         if ($numberOfSuperAdmins <= 1) {
            return [
               'statusCode' => 409,
               'canDelete' => false,
               'message' => "You cannot delete the only super admin!",
            ];
         }
         if (!$CURRENT_USER->hasRoleKey('super_admin')) {
            return [
               'statusCode' => 403,
               'canDelete' => false,
               'message' => "Aborting! You don't have permission(s) to delete this super admin!",
            ];
         }
      }
      // ---------------- SUPER ADMIN ----------------

      return [
         'statusCode' => $statusCode,
         'canDelete' => $canDelete,
         'message' => $message,
      ];
   }

   // --------------------------------------

}
/*
 Just call it as follows. No if-else is needed.

   // ------------------- USER ACCESS CHECKING -------------------
   UserAceesChecking::chatBan($SENDER->id);
*/