<?php

namespace App\Console\Commands;

use App\Domains\Lessons\Services\HistoryGeneratorService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class GenerateWeeklyHistories extends Command
{
   protected $signature = 'histories:generate {--date= : YYYY-MM-DD reference date, defaults to today} {--weeks=2 : Number of fresh weeks ahead to generate}';
   protected $description = 'Generate upcoming lesson histories from the next fresh week and sync stale/upcoming history phases';

   public function handle(HistoryGeneratorService $service): void
   {
      $inputDate = $this->option('date');
      $weeks = max(1, (int) $this->option('weeks'));

      $referenceDate = $inputDate ? Carbon::parse($inputDate)->startOfDay() : Carbon::today();

      $rangeStart = $referenceDate->isMonday()
         ? $referenceDate->copy()->startOfDay()
         : $referenceDate->copy()->next('Monday')->startOfDay();

      $rangeEnd = $rangeStart->copy()->addDays(($weeks * 7) - 1)->endOfDay();

      $this->info("Generating lesson histories from {$rangeStart->toDateString()} to {$rangeEnd->toDateString()}");

      $stats = $service->generateForRange($referenceDate, $rangeStart, $rangeEnd);

      $this->info("Lessons created: {$stats['lessons']}");
      $this->info("Groups created: {$stats['groups']}");
      $this->info("Makeups created: {$stats['makeups']}");
      $this->info("Today's upcoming -> in_progress: {$stats['today_progressed']}");
      $this->info("Past upcoming -> belated: {$stats['past_belated']}");
      $this->info("Skipped: {$stats['skipped']}");
   }
}
