<?php

namespace Database\Seeders;

use App\Models\System\Settings\OptionEnv;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MailerSeeder extends Seeder
{
   /**
    * Run the database seeds.
    */
   public function run(): void
   {
      $settings = [
         'MAIL_MAILER' => 'smtp',
         'MAIL_HOST' => 'smtp.ethereal.email',
         'MAIL_PORT' => '587',
         'MAIL_USERNAME' => 'hiram.hoeger15@ethereal.email',
         'MAIL_PASSWORD' => 'Cu5aWEHP2JrQXnFbQX',
         'MAIL_ENCRYPTION' => 'tls',
         'MAIL_SCHEME' => null,
         'MAIL_FROM_NAME' => 'Alpha Albert',
         'MAIL_FROM_ADDRESS' => 'support@wokpulse.com',
      ];

      foreach ($settings as $name => $value) {
         OptionEnv::updateOrCreate(
            [
               'var_type' => 'mail',
               'name' => $name,
            ],
            [
               'value' => $value,
               'status' => 1,
               'updated_at' => now(),
               'created_at' => now(),
            ]
         );
      }
   }
}
