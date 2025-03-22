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
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')
        //          ->hourly();
        $db_sources = [
            '10.0.0.73' => 'ERPv13 - Live',
            '10.0.0.76' => 'ERPv15 - Live',
            '10.0.49.27' => 'Test'
        ];
        $db_source = isset($db_sources[env('DB_HOST')]) ? $db_sources[env('DB_HOST')] : env('DB_HOST');
        echo "NOTE: Currently connected to $db_source DB\n";
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
