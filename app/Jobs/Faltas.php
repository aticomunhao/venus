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

use function PHPUnit\Framework\isEmpty;

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

        $data_atual = Carbon::yesterday();

        $dia_atual = $data_atual->weekday();

        $inseridos = DB::table('presenca_cronograma as pc')
        ->leftJoin('dias_cronograma as dc', 'pc.id_dias_cronograma', 'dc.id')
        ->where('id_tratamento', '<>', null)
        ->where('dc.data', '=', $data_atual)
        ->select('id_tratamento')
        ->get();

        $inseridos = json_decode(json_encode($inseridos), true);

        $lista = DB::table('tratamento AS tr')
        ->select('tr.id', 'tr.id_reuniao')
        ->leftjoin('cronograma AS rm', 'tr.id_reuniao', 'rm.id')
        ->where('tr.status', 2)
        ->where('rm.dia_semana', $dia_atual)
        ->where('rm.id_tipo_tratamento', '<>', 2)
        ->whereNotIn('tr.id', $inseridos )
        ->get();



        foreach ($lista as $item){
        $id_dia_cronograma =  DB::table('dias_cronograma')->where('data', $data_atual)->select('id')->where('id_cronograma', $item->id_reuniao)->first();

        DB::table('presenca_cronograma AS dt')
        ->leftJoin('tratamento AS tr', 'dt.id', 'dt.id_tratamento')
        ->whereNotIn('id_tratamento', $inseridos)
        ->insert([
            'id_dias_cronograma' => $id_dia_cronograma->id,
            'id_tratamento' => $item->id,
            'presenca' => false
        ]);}




        return;

}

}
