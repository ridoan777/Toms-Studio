<?php

namespace App\Helpers\Ui;

use Carbon\Carbon;

class ConvertDynamicTimezone
{
   public static function handle()
   {
      $tz = config('settings.display_timezone');
      $timezoneOffset = Carbon::now($tz)->format('P');
      return [
         'zone' => $tz ?? null,
         'offset' => $timezoneOffset ?? null,
      ];
   }
}

/*
USE CASE-1: DATATABLE
   $zones = \App\Helpers\Profiles\ConvertDynamicTimezone::handle()
   $zones['zone']

   $row->created_at_formatted = $row->created_at ? Carbon::createFromFormat('Y-m-d H:i:s', $row->getRawOriginal('created_at'), 'UTC')->timezone($zones['zone'])->format('d M Y, H:i') . ' (UTC' . $zones['offset'] . ')' : 'N/A';

USER CASE-2: FORM
   @php
      $zones = \App\Helpers\Ui\ConvertDynamicTimezone::handle();
   @endphp
   <input type="text" class="normalInput" value="{{ $history?->created_at ? $history->created_at->timezone($zones['zone'])->format('d-M-Y h:i:s A') . ' (UTC' . $zones['offset'] . ')' : null }}" readonly disabled>
*/