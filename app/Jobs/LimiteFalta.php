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

class LimiteFalta implements ShouldQueue
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




        $tratamentos_faltas = DB::table('presenca_cronograma as pc')
        ->select('pc.id_tratamento', DB::raw('count(*) as total'))
        ->leftJoin('tratamento as tr', 'pc.id_tratamento', 'tr.id')
        ->leftJoin('encaminhamento as enc', 'tr.id_encaminhamento', 'enc.id')
        ->leftJoin('atendimentos as at', 'enc.id_atendimento', 'at.id')
        ->groupBy('pc.id_tratamento')
        ->where('pc.id_tratamento', '<>', null)
        ->where('pc.presenca', false)
        ->where('enc.id_tipo_tratamento', 1)
        ->whereNot('enc.status_encaminhamento', 4)
        ->get();



        foreach($tratamentos_faltas as $faltas){
            if($faltas->total >= 3){

                $id_encaminhamento = DB::table('tratamento')->select('id_encaminhamento')->where('id', $faltas->id_tratamento)->first();


                DB::table('tratamento')
                ->where('id', $faltas->id_tratamento)
                ->update([
                    'status' => 5
                ]);

                DB::table('encaminhamento')
                ->where('id', $id_encaminhamento->id_encaminhamento)
                ->update([
                    'status_encaminhamento' => 6
                ]);
            }
        }

    }
}
