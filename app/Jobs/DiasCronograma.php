<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class DiasCronograma implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $dia_hoje = Carbon::tomorrow();
        $dia_semana_hoje = $dia_hoje->weekday();


        $reunioes_hoje = DB::table('cronograma')->where('dia_semana', $dia_semana_hoje)->where('status_reuniao', '<>', 2)->get();

        foreach($reunioes_hoje as $reuniao){
            DB::table('dias_cronograma')->insert([
                'data' => $dia_hoje,
                'id_cronograma' => $reuniao->id,
            ]);
        }
    }
}
