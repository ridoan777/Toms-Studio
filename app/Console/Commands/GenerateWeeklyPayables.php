<?php

namespace App\Console\Commands;

use App\Domains\Billings\Services\PayableGeneratorService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class GenerateWeeklyPayables extends Command
{
   protected $signature = 'payables:generate {--date= : YYYY-MM-DD generation start date, defaults to today} {--days=14 : Number of days ahead to generate}';
   protected $description = 'Generate student payables from the first scheduled weekday only, then every 7 days';

   public function handle(PayableGeneratorService $service): void
   {
      $inputDate = $this->option('date');
      $days = max(1, (int)$this->option('days'));

      $rangeStart = $inputDate ? Carbon::parse($inputDate)->startOfDay() : Carbon::today();
      // $rangeStart =  Carbon::parse("2026-07-11 00:00:00")->startOfDay();

      $rangeEnd = $rangeStart->copy()->addDays($days - 1)->endOfDay();

      // logger("reached GenerateWeeklyPayables", [$rangeStart, $rangeEnd]);

      $this->info("Generating payables from {$rangeStart->toDateString()} to {$rangeEnd->toDateString()}");

      $stats = $service->generateForRange($rangeStart, $rangeEnd);

      $this->info("Lessons created: {$stats['lessons']}");
      $this->info("Groups created: {$stats['groups']}");
      $this->info("Skipped: {$stats['skipped']}");
   }
}
/*
   RUN:
      either:
         php artisan payables:generate --date=2026-07-10 --days=14
      or:
         php artisan payables:generate
      example:
         schedule_day = [sun, tues, wed]
         only sun is treated as the recurring due-day anchor
      or:
         keep this on console.php
            Schedule::command('payables:generate')->dailyAt('01:01');
         & RUN
            php artisan schedule:work
      or:
*/
