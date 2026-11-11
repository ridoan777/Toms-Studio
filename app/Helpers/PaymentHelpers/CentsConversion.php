<?php

namespace App\Helpers\PaymentHelpers;

use App\Models\Subscription\SubscribedUser;
use Auth;
use Carbon\Carbon;

class CentsConversion
{
   public static function convertCentsToFullAmount($session, $targetAmount)
   {
      $noCentCurrencies = [
         'BIF', // Burundian Franc  
         'CLP', // Chilean Peso  
         'DJF', // Djiboutian Franc  
         'GNF', // Guinean Franc  
         'JPY', // Japanese Yen  
         'KMF', // Comorian Franc  
         'KRW', // South Korean Won  
         'MGA', // Malagasy Ariary  
         'PYG', // Paraguayan Guaraní  
         'RWF', // Rwandan Franc  
         'UGX', // Ugandan Shilling  
         'VND', // Vietnamese Dong  
         'VUV', // Vanuatu Vatu  
         'XAF', // Central African CFA Franc  
         'XOF', // West African CFA Franc  
         'XPF', // CFP Franc  
      ];

      $currency = is_array($session) ? $session['currency'] : ($session->currency ?? null);
      $usersCurrency = strtoupper($currency);

      $isZeroDecimal = in_array($usersCurrency, $noCentCurrencies, true);

      if ($isZeroDecimal) {
         return round($targetAmount, 2);
      }
      return round($targetAmount / 100, 2);
   }
}