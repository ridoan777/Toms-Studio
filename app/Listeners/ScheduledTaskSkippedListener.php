<?php

namespace App\Listeners;

use Illuminate\Console\Events\ScheduledTaskSkipped;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class ScheduledTaskSkippedListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(ScheduledTaskSkipped $event): void
	{
		Log::info('Task skipped: '.$event->task->command.' at '.now());
	}
}
