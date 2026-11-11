<?php

use App\Console\Commands\DemoCommand;
use App\DemoClass;
use App\Models\Users\User;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\{Artisan, Log, Schedule, DB};

Schedule::command('filepond:clear')->daily();
Schedule::command('log:clear')->monthly();

// Schedule::command('payables:generate')->weeklyOn(0, '18:00');
// Schedule::command('payables:generate')->dailyAt('01:00');
Schedule::command('payables:generate')->everyMinute();   // GenerateWeeklyPayables
Schedule::command('histories:generate')->everyMinute();   // GenerateWeeklyHistories

/*
   RUN:
      either:
         php artisan payables:generate --week=2026-06-10
      or:
         php artisan payables:generate
      or:
         keep this on console.php
            Schedule::command('payables:generate')->dailyAt('01:01');
         & RUN
            php artisan schedule:work  // dev
            php artisan schedule:run  // production
      or:
   CRON:
   /usr/bin/php8.5 /home/thomas/htdocs/dashboard.tomsperformancestudio.cloud/artisan schedule:run >> /dev/null 2>&1

   CRON RESTART:
      sudo service cron restart or systemctl restart cron

   FRESH COMPOSER:
      composer install --no-dev --optimize-autoloader --no-interaction
*/