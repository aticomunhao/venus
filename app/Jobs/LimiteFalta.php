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


        $tratamentos_faltas = DB::table('presenca_cronograma')->select('id_tratamento', DB::raw('count(*) as total'))->groupBy('id_tratamento')->where('id_tratamento', '<>', null)->get();



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
                    'status_encaminhamento' => 4
                ]);
            }
        }

    }
}
