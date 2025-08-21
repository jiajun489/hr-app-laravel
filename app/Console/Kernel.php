<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Run employee pattern analysis every Sunday at 1:00 AM
        $schedule->command('employees:analyze-patterns --notify-hr')
                ->weekly()
                ->sundays()
                ->at('01:00')
                ->withoutOverlapping();
                
        // Calculate work-life balance metrics every day at 2:00 AM
        $schedule->command('employees:calculate-work-life-metrics')
                ->dailyAt('02:00')
                ->withoutOverlapping();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
