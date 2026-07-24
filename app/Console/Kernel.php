<?php

namespace App\Console;

use App\Console\Commands\DeleteLaravelLog;
use App\Jobs\DistributeVendorCommissionJob;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');
        require base_path('routes/console.php');
    }

    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {

        $schedule->job(new DistributeVendorCommissionJob)->cron('* * * * *');
        $schedule->command('log:clean')->daily();
        $schedule->command('tokens:cleanup')->everyFiveMinutes();
    }

    /**
     * Register custom Artisan commands
     */
    protected $commands = [
        DeleteLaravelLog::class,
    ];
}
