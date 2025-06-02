<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param \Illuminate\Console\Scheduling\Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule): void
    {
        // $schedule->command('smth')->dailyAt('07:45');

        // tous les 2 jours
        // $schedule->command('smth')->cron('0 0 */2 * *')->at('08:00');


        $schedule->command('app:clear-temp-stock')->everyFifteenMinutes();
        $schedule->command('app:clear-expiring-carts')->everyFifteenMinutes();
        $schedule->command('app:send-preliminary-grant-list')->daily();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
