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

        // Retorna todas as faltas de todos os tratamentos ativos
        $tratamentos_faltas = DB::table('presenca_cronograma as pc')
            ->select('pc.id_tratamento', 'dc.data')
            ->leftJoin('tratamento as tr', 'pc.id_tratamento', 'tr.id')
            ->leftJoin('encaminhamento as enc', 'tr.id_encaminhamento', 'enc.id')
            ->leftJoin('atendimentos as at', 'enc.id_atendimento', 'at.id')
            ->leftJoin('dias_cronograma as dc', 'pc.id_dias_cronograma', 'dc.id')
            ->whereNot('pc.id_tratamento', null) // Apenas tratamentos, sem Avulsos
            ->where('pc.presenca', false) // Falta
            ->where('enc.status_encaminhamento', 2)
            ->get()->toArray();

        // Organiza os dados por ID tratamento, para facilitar o foreach
        $arrayTratamentoFaltas = array();
        foreach ($tratamentos_faltas as $element) {
            $arrayTratamentoFaltas[$element->id_tratamento][] = $element->data;
        }


        // Para cada ID tratamento
        foreach ($arrayTratamentoFaltas as $key => $faltas) {
            
            foreach ($faltas as $falta) { // Para cada falta
                $consecutivo = 1; // Contagem de faltas consecutivas
                foreach ($faltas as $faltaCross) { // Para cada falta

                    // Confere se as faltas são consecutivas com as ultimas, aumentando a contagem
                    if (Carbon::parse($falta)->addWeek($consecutivo) == Carbon::parse($faltaCross)) {
                        $consecutivo += 1;
                    }
                }

                // Caso alcance a terceira falta consecutiva
                if ($consecutivo > 2) {

                    // Descobre o id_encaminhamento do tratamento atual
                    $id_encaminhamento = DB::table('tratamento')->select('id_encaminhamento')->where('id', $key)->first();

                    // Inativa o tratamento por faltas
                    DB::table('tratamento')
                        ->where('id', $key)
                        ->update([
                            'status' => 5 // Finalizado por faltas
                        ]);

                    // Inativa o encaminhamento
                    DB::table('encaminhamento')
                        ->where('id', $id_encaminhamento->id_encaminhamento)
                        ->update([
                            'status_encaminhamento' => 4 // Inativado
                        ]);
                }
            }
            
        }
    }
}
