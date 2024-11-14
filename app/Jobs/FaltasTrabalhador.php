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

class FaltasTrabalhador implements ShouldQueue
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

        // Tratamentos, integral, pti ou ptd, cujas reunioes não estejam de férias

        $data_atual = Carbon::yesterday();

        $dia_atual = $data_atual->weekday();

        $inseridos = DB::table('presenca_membros as pm')
        ->leftJoin('dias_cronograma as dc', 'pm.id_dias_cronograma', 'dc.id')
        ->where('dc.data', '=', $data_atual)
        ->select('id_membro')
        ->get();

        $inseridos = json_decode(json_encode($inseridos), true);

        $lista = DB::table('membro as m')
        ->leftJoin('cronograma as cro', 'm.id_cronograma', 'cro.id')
        ->where('cro.dia_semana', $dia_atual)
        ->where('dt_fim', null)
        ->whereNotIn('m.id', $inseridos)
        ->select('m.id', 'm.id_cronograma')
        ->get();


        foreach ($lista as $item){
        $id_dia_cronograma =  DB::table('dias_cronograma')->where('data', $data_atual)->select('id')->where('id_cronograma', $item->id_cronograma)->first();

        DB::table('presenca_membros')
        ->insert([
            'id_dias_cronograma' => $id_dia_cronograma->id,
            'id_membro' => $item->id,
            'presenca' => false
        ]);}

        return;

}
}
