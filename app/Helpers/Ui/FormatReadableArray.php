<?php
namespace App\Helpers\Ui;

class FormatReadableArray
{
   public static function formatArray($value)
   {
      if (is_array($value)) {
         return collect($value)->map(fn($v) => ucfirst(str_replace('_', ' ', $v)))->implode(', '); // -----
      }

      return $value ? ucfirst(str_replace('_', ' ', $value)) : 'N/A';
   }
}