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





         DB::table('tratamento as tr')
        ->select('tr.id_encaminhamento')
        ->leftJoin('encaminhamento as enc', 'tr.id_encaminhamento', 'enc.id')
        ->where('tr.dt_fim', '<>', null)
        ->where('tr.dt_fim', $ontem)
        ->whereNot('enc.status_encaminhamento', 4)
        ->update([
            'tr.status' => 4
        ]);

        $semanas = DB::table('tratamento as tr')
        ->select('id_encaminhamento')
        ->leftJoin('encaminhamento as enc', 'tr.id_encaminhamento', 'enc.id')
        ->where('dt_fim', '<>', null)
        ->where('dt_fim', $ontem)
        ->whereNot('enc.status_encaminhamento', 4)
        ->get();


        $semanasId = [];
        foreach($semanas as $semana){
            $semanasId[] = $semana->id_encaminhamento;
        }


        DB::table('encaminhamento')
        ->whereIn('id', $semanasId)
        ->update([
            'status_encaminhamento' => 5
        ]);


//172 e 190





    }
}
