<?php

namespace App\Console\Commands\Log;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Throwable;

#[Signature('log:clear')]
#[Description('Command description')]
class ClearLogFile extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $logPath = storage_path('logs/laravel.log');

        if (!file_exists($logPath)) {
            $this->warn('Log file not found');
            return Command::SUCCESS;
        }

        try {
            file_put_contents($logPath, '');
            $this->info('Log file cleared successfully');
            return Command::SUCCESS;
        } catch (Throwable $e) {
            $this->error('Failed to clear log: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
