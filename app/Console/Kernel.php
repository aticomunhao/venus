<?php

namespace App\Console;

use App\Jobs\DiasCronograma;
use App\Jobs\EntrevistaProamo;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Jobs\Faltas;
use App\Jobs\FaltasTrabalhador;
use App\Jobs\FilaEncaminhamentos;
use App\Jobs\FimSemanas;
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

          $schedule->job( new Faltas());
          $schedule->job( new FaltasTrabalhador());
          $schedule->job( new DiasCronograma());
          $schedule->job( new LimiteFalta());
          $schedule->job( new FimSemanas());
          $schedule->job( new FilaEncaminhamentos());
          $schedule->job( new EntrevistaProamo());
          
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
