<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Commands\CancelPagueloFacilTransactions::class,
        Commands\SetMatrixLevel::class
    ];
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        //$schedule->command('cancel:paguelofacil:transactions')->everyFiveMinutes();
        //$schedule->command('futswap:canceled')->everyFiveMinutes();
        $schedule->command('matrix:set_level')->everySixHours();
        $schedule->command('mining:pay')->everySixHours();
        $schedule->command('mining:expire')->daily();
        $schedule->command('withdrawal:check')->weekends()->hourly();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
