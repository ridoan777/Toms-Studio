<?php

namespace App\Notifications\InAppNotifications;

use App\Domains\Users\Enums\ApiAuthEnums;
use App\Helpers\Notifications\InAppNotificationHelper;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Throwable;

class ApiAuthInAppNotify
{
   use Queueable;

   /**
    * Create a new notification instance.
    */
   public $actionFlag = null;
   public function __construct(
      public $targetUser = null,
      public $actor = null,
      public $forcedShow = false,
      public $receiver = null,
   ) {
   }

   public function handle(?ApiAuthEnums $actionFlag = ApiAuthEnums::LOGIN)
   {
      $this->actionFlag = $actionFlag;
      
      switch ($actionFlag) {
         case ApiAuthEnums::LOGIN:
            return $this->login();
         case ApiAuthEnums::REGISTER:
            return $this->register();

         case ApiAuthEnums::PASSWORD_CHANGE_ATTEMPT:
         case ApiAuthEnums::EMAIL_CHANGE_ATTEMPT:
         case ApiAuthEnums::ACCOUNT_DELETE_ATTEMPT:
         case ApiAuthEnums::PASSWORD_CHANGED:
         case ApiAuthEnums::EMAIL_CHANGED:
            return $this->action();

         case ApiAuthEnums::ACCOUNT_DELETED:
            return $this->deleted();
      }
   }
   // ----------------------------------------------------

   private function login()
   {
      $title = "Login Attempt";
      $message = "Your account was logged-in at " . Carbon::now('UTC')->setTimezone($this->receiver?->timezone ?? 'UTC')->toDayDateTimeString();

      $this->callNotifyHelper($this->receiver, 'login', 'alert', $title, $message, 'backend_single_user');
   }
   // ----------------------------------------------------

   private function register()
   {
      $title = "New User";
      $message = "Hi {$this->receiver->name}! Welcome abord to " . config('app.name');

      $this->callNotifyHelper($this->receiver, 'login', 'alert', $title, $message, 'backend_single_user');
   }
   // ----------------------------------------------------

   private function action()
   {
      $action = $this->actionFlag?->value;
      $sanitzedAction = $action ? str_replace('_', ' ', $action) : "account modification attempt!";

      $title = ucfirst($sanitzedAction);

      $message = "A/An {$sanitzedAction} was made to your account.";

      $this->callNotifyHelper($this->receiver, 'system', 'alert', $title, $message, 'backend_single_user');
   }
   // ----------------------------------------------------

   private function deleted()
   {
      $title = "Account Deleted";
      $message = "Your account with username '{$this->targetUser?->username}' has been deleted at " . Carbon::now('UTC')->setTimezone($this->receiver?->timezone ?? 'UTC')->toDayDateTimeString();

      $this->callNotifyHelper($this->receiver, 'system', 'critical', $title, $message, 'backend_single_user');
   }
   // ----------------------------------------------------

   private function callNotifyHelper($recipient, $type, $severity, $title, $message, $route)
   { 
      return InAppNotificationHelper::createInAppNotify(
         $recipient,
         'api_auth',
         $type,
         $severity,
         $title,
         $message,
         $this?->actor?->name,
         $this?->actor?->avatar_url,
         $route ? route('backend_single_user', $this->receiver->id) : null,
         "visit",
         $this?->forcedShow,
      );
   }
}
