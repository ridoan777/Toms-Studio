<?php

namespace App\Helpers\Errors;
use Illuminate\Support\Facades\Log;

class LoggerAccess
{
   public static function showLog(array $ENVIRONMENTS, string $type, string $message, $content = null)
   {  
      if (!app()->environment($ENVIRONMENTS)){
         return;
      }

      $validTypes = ['info', 'error', 'alert', 'notice', 'debug', 'warning', 'critical', 'emergency'];
      $type = in_array($type, $validTypes) ? $type : 'debug';
      
      Log::{$type}($message, $content ? ['data' => $content] : []);
   }
}
/*
USE CASE:
   LoggerAccess::showLog(['local', 'staging'], 'info', "This_is_a_log_message", $request);
*/