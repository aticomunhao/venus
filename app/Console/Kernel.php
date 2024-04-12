<?php

namespace App\Console;

use App\Jobs\DiasCronograma;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Jobs\Faltas;
use App\Jobs\LimiteFalta;
use Illuminate\Support\Facades\DB;





class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    private $id;
    protected function schedule(Schedule $schedule): void
    {

          $schedule->job( new Faltas())->dailyAt('01:00');
          $schedule->job( new LimiteFalta())->dailyAt('01:01');
          $schedule->job( new DiasCronograma())->dailyAt('01:00');
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
