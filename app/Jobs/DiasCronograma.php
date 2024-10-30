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
        $dia_hoje = Carbon::today();
        $dia_semana_hoje = $dia_hoje->weekday();


        $incluidos = DB::table('dias_cronograma')->where('data', $dia_hoje)->select('id_cronograma')->get();
        $arrayIncluidos = [];
        foreach($incluidos as $incluido){
            $arrayIncluidos[] = $incluido->id_cronograma;
        }

        $reunioes_hoje = DB::table('cronograma')->where('dia_semana', $dia_semana_hoje)
       // ->whereNotIn('id', $arrayIncluidos)->where(function($query) use ($dia_hoje) {
       //     $query->whereRaw("data_fim < ?", [$dia_hoje])
       //           ->orWhereNull('data_fim');
        })->get();

        foreach($reunioes_hoje as $reuniao){
            DB::table('dias_cronograma')

            ->insert([
                'data' => $dia_hoje,
                'id_cronograma' => $reuniao->id,
            ]);
        }
    }
}
