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

    public function __construct() {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {

        $data_atual = Carbon::yesterday();
        $dia_atual = $data_atual->weekday();

        // Retorna todos os tratamentos que já tomaram presença hoje
        $inseridos = DB::table('presenca_cronograma as pc')
            ->leftJoin('dias_cronograma as dc', 'pc.id_dias_cronograma', 'dc.id')
            ->whereNot('id_tratamento', null)
            ->where('dc.data', $data_atual)
            ->pluck('id_tratamento')
            ->toArray();

        // Traz todos os tratamentos que trocaram de grupo hoje, usado para proteger contra faltas desnecessárias
        $idsTrocaDeGrupo = DB::table('tratamento_grupos')
            ->leftJoin('cronograma', 'tratamento_grupos.id_cronograma', 'cronograma.id')
            ->where('tratamento_grupos.dt_inicio', $data_atual)
            ->where('dia_semana', $dia_atual)
            ->pluck('id_tratamento');

        // Retorna todos os tratamentos ativos, iniciados, do dia de hoje, ativas, que não estejam de férias, inseridos ou que trocaram de grupo hoje
        $lista = DB::table('tratamento AS tr')
            ->select('tr.id', 'tr.id_reuniao')
            ->leftjoin('cronograma AS rm', 'tr.id_reuniao', 'rm.id')
            ->where('tr.dt_inicio', '<=', $data_atual) // Iniciados
            ->where('rm.dia_semana', $dia_atual)
            ->where(function ($query) {
                $query->where('rm.modificador', NULL); // Sem modificador algum
                $query->orWhere('rm.modificador', '<>', 4); // Em férias
            })
            ->whereNotIn('tr.id', $inseridos) // Inseridos de hoje
            ->whereNotIn('tr.id', $idsTrocaDeGrupo) // Que trocaram de grupo hoje
            ->get();







        foreach ($lista as $item) {

            // Descobre o cronograma que se reune hoje correspondente
            $id_dia_cronograma =  DB::table('dias_cronograma')
                ->select('id')
                ->where('data', $data_atual)
                ->where('id_cronograma', $item->id_reuniao)
                ->first();

            // Insere a falta de acordo com os dados
            DB::table('presenca_cronograma AS dt')
                ->leftJoin('tratamento AS tr', 'dt.id', 'dt.id_tratamento')
                ->insert([
                    'id_dias_cronograma' => $id_dia_cronograma->id,
                    'id_tratamento' => $item->id,
                    'presenca' => false
                ]);
        }
    }
}
