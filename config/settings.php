<?php

return [

   /*
   |--------------------------------------------------------------------------
   | This is a custom made settings file and by-default will be loaded with Larvel bootstrap. It was not manually booted anywhere else.
   |--------------------------------------------------------------------------
   */

   'display_timezone' => 'UTC',
   'otp_digit_length' => env('OTP_DIGIT_LENGTH') ? (int)env('OTP_DIGIT_LENGTH') : 5,
];
