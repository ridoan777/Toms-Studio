<?php

namespace App\Domains\Billings\Enums;

enum CurrencyEnums: string
{
   case AUD = 'AUD';
   case USD = 'USD';
   case NZD = 'NZD';
   case GBP = 'GBP';
   case EUR = 'EUR';
   case CAD = 'CAD';
   case CHF = 'CHF';
   case SEK = 'SEK';
   case NOK = 'NOK';
   case DKK = 'DKK';

   // Friendly display names for your dropdown labels
   public function label(): string
   {
      return match ($this) {
         self::AUD => 'AUD - Australian Dollar',
         self::USD => 'USD - United States Dollar',
         self::NZD => 'NZD - New Zealand Dollar',
         self::GBP => 'GBP - Great Britain Pound',
         self::EUR => 'EUR - Euro',
         self::CAD => 'CAD - Canadian Dollar',
         self::CHF => 'CHF - Swiss Franc',
         self::SEK => 'SEK - Swedish Krona',
         self::NOK => 'NOK - Norwegian Krone',
         self::DKK => 'DKK - Danish Krone',
      };
   }
}