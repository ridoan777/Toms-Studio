<?php

namespace App\Helpers\System;

use App\Helpers\Errors\ExceptionHandling;
use App\Models\System\Settings\UserAppPreference;
use Throwable;

class UserPreferenceHelper
{
   public static function newRegistration($USER)
   {
      try {
         $createNew = UserAppPreference::updateOrCreate(
            [
               'user_id' => $USER->id,
            ],
            [
               'in_app_notification' => (int) 1,
               'email_notification' => (int) 1,
               'push_notification' => (int) 1,
               'activity_log' => (int) 1,
               'status' => (int) 1,
            ]
         );
         return $createNew;
      } catch (Throwable $e) {
         return ExceptionHandling::handle('saving user settings preference.', $e);
      }
   }
}
