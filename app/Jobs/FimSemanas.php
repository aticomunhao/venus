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

class FimSemanas implements ShouldQueue
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
        $ontem = Carbon::yesterday();

        $array_pti = DB::table('encaminhamento as enc')
        ->leftJoin('atendimentos as at', 'enc.id_atendimento', 'at.id')
        ->where('enc.id_tipo_tratamento', 2)
        ->where('enc.status_encaminhamento', 1)
        ->select('at.id_assistido as id')
        ->get();

        $aguardando_pti = [];
        foreach($array_pti as $array){
            $aguardando_pti[] = $array->id;
        }


         DB::table('tratamento')
        ->select('id_encaminhamento')
        ->where('dt_fim', '<>', null)
        ->where('dt_fim', $ontem)
        ->whereNotIn('id', $aguardando_pti)
        ->update([
            'status' => 4
        ]);

        $semanas = DB::table('tratamento')
        ->select('id_encaminhamento')
        ->where('dt_fim', '<>', null)
        ->where('dt_fim', $ontem)
        ->whereNotIn('id', $aguardando_pti)
        ->get();

        $semanasId = [];
        foreach($semanas as $semana){
            $semanasId[] = $semana->id_encaminhamento;
        }


        DB::table('encaminhamento')
        ->whereIn('id', $semanasId)
        ->update([
            'status_encaminhamento' => 3
        ]);


//172 e 190





    }
}
