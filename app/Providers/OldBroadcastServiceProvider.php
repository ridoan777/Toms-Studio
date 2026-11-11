<?php
namespace App\Providers;

use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\ServiceProvider;

class OldBroadcastServiceProvider extends ServiceProvider
{
   public function boot(): void
   {
      Broadcast::routes(['middleware' => ['auth:sanctum']]);
      require base_path('routes/channels.php');
   }
}
/*
   This file used to be called at bootstrap\app.php as :
   ->withProviders([
        App\Providers\BroadcastServiceProvider::class,
    ])

   ... but in Laravel 13, adding this seems to duplicate the channel routes. So let's avoid this.
   Add this in routes\channels.php at the top:
      Broadcast::routes(['middleware' => ['auth:sanctum']]);
*/ 