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
        $schedule->command('app:sync-penjualan-data')
            ->everyMinute()
            ->appendOutputTo(storage_path('logs/sync-penjualan.log'));

        $schedule->command('app:coba-data')
            ->everyMinute()
            ->appendOutputTo(storage_path('logs/coba-data.log'));
        
        $schedule->command('app:sync-hpp-data')
            ->everyMinute()
            ->appendOutputTo(storage_path('logs/sync-hpp.log'));

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
