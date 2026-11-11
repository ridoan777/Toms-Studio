<?php

namespace App\Helpers\System;

use App\Helpers\FileHelpers\FileManagement;
use App\Models\Communication\Chatting\ChatMessage;

class ReportDeleteBlock
{
   public static function deleteChatMessage(ChatMessage $message): void
   {
      $DISK_FOLDER = config('filesystems.default');

      foreach ($message->chatMessageRelationWith_Gallery as $gallery) {
         if ($gallery->file) {
            FileManagement::deleteFile($gallery->file, $DISK_FOLDER);
         }
         $gallery->delete();
      }
      $message->delete();
   }
}
