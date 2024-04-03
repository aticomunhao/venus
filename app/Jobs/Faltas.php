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


class Faltas implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    private $info;
    /**
     * Create a new job instance.
     */

    public function __construct()
    {

    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {

        $data_atual = Carbon::now();

        $dia_atual = $data_atual->weekday() - 1;

        $inseridos = DB::table('dias_tratamento')->select('id_tratamento')->get();

        $lista = DB::table('tratamento AS tr')
        ->select('tr.id')
        ->leftjoin('dias_tratamento AS dt', 'tr.id', 'dt.id_tratamento')
        ->leftjoin('cronograma AS rm', 'tr.id_reuniao', 'rm.id')
        ->where('tr.status', 2)
        ->where('rm.dia', $dia_atual)
        ->where('rm.id_tipo_tratamento', '<>', 2)
        ->whereNotIn('tr.id', $inseridos )
        ->get();


        $confere = DB::table('tratamento AS tr')
        ->leftjoin('dias_tratamento AS dt', 'tr.id', 'dt.id_tratamento')
        ->where('tr.status', 2)
        ->where('dt.data', $data_atual)
        ->count('dt.id_tratamento');



        if($confere < 0){

            return;

        }
        else if($lista->isEmpty()) {
            return;
        }
        else{

            foreach ($lista as $item){
            DB::table('dias_tratamento AS dt')
            ->leftJoin('tratamento AS tr', 'dt.id', 'dt.id_tratamento')
            ->whereNotIn('id_tratamento', $inseridos)
            ->insert([
                'data' =>  $data_atual,
                'id_tratamento' => $item->id,
                'presenca' => false
            ]);}

        }

        return;

}

}
