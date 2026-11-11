<?php

namespace App\Helpers\Ui;

use Illuminate\Support\Facades\Storage;

class GetUserAvatar
{
   public static function alignAvatar($USER, $fillWithDummy = false)
   {
      // $userAvatar = null;
      $userAvatar = $fillWithDummy ? asset('site_assets/dummies/dummy_man.webp') : null;

      if ($USER?->avatar) {
         $userAvatar = Storage::url($USER->avatar);
      }

      return $userAvatar;
   }
}

/*
USE CASE:
   $userAvatar = \App\Helpers\Profiles\GetUserAvatar::alignAvatar($user, false)
*/