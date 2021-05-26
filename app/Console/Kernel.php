<?php

namespace App\Console;

use App\Console\Commands\PressesParser;
use App\Console\Commands\RegularsParser;
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
        RegularsParser::class,
        PressesParser::class
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
//        $schedule->command(PressesParser::class, ['schedule'])
//            ->hourly()
//            ->between('4:00', '22:00');
//
//        $schedule->command(PressesParser::class, ['cup'])
//            ->hourly()
//            ->between('4:00', '22:00');
//
//        $schedule->command(RegularsParser::class, ['schedule'])
//            ->dailyAt('2:00');
//
//        $schedule->command(RegularsParser::class, ['cup'])
//            ->dailyAt('3:00');
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
