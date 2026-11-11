<?php

namespace App\Helpers\Auth;

use Illuminate\Support\Facades\Http;

class SocialAuthVerify
{
   public static function verifyProviderToken(string $provider, string $token): array
   {
      return match ($provider) {
         'google' => self::verifyGoogle($token),
         // 'apple' => $this->verifyApple($token),
         // 'facebook' => $this->verifyFacebook($token),
         // 'github' => $this->verifyGithub($token),
         // 'x' => $this->verifyX($token),
         // 'linkedin' => $this->verifyLinkedIn($token),
         default => throw new \Exception('Invalid provider'),
      };
   }

   private static function verifyGoogle(string $idToken): array
   {
      $response = Http::get(
         'https://oauth2.googleapis.com/tokeninfo',['id_token' => $idToken]
         // 'https://oauth2.googleapis.com/oauth2/v3/tokeninfo',['id_token' => $idToken]
      );
      
      if (!$response->ok()) {
         throw new \Exception('Invalid Google token');
      }

      $data = $response->json();

      if ($data['aud'] !== config('services.google.client_id')) {
         throw new \Exception('Invalid Google audience');
      }

      return [
         'id' => $data['sub'],
         'email' => $data['email'] ?? null,
         'name' => $data['name'] ?? null,
         'avatar' => $data['picture'] ?? null,
      ];
   }


}