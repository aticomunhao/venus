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
            ->select('pc.id_tratamento', 'pc.presenca', 'enc.id_tipo_tratamento')
            ->leftJoin('tratamento as tr', 'pc.id_tratamento', 'tr.id')
            ->leftJoin('encaminhamento as enc', 'tr.id_encaminhamento', 'enc.id')
            ->leftJoin('atendimentos as at', 'enc.id_atendimento', 'at.id')
            ->leftJoin('dias_cronograma as dc', 'pc.id_dias_cronograma', 'dc.id')
            ->whereNot('pc.id_tratamento', null) // Apenas tratamentos, sem Avulsos
            ->where(function ($query) {
                $query->where('enc.status_encaminhamento', 2);
                $query->orWhere('tr.status', '<', 3);
            })
            ->orderBy('dc.data', 'ASC')
            ->get()->toArray();

        // Organiza os dados por ID tratamento, para facilitar o foreach
        $arrayTratamentoFaltas = array();
        foreach ($tratamentos_faltas as $element) {
            $arrayTratamentoFaltas[$element->id_tratamento . ',' . $element->id_tipo_tratamento][] = $element->presenca;
        }


        // Para cada ID tratamento
        foreach ($arrayTratamentoFaltas as $key => $faltas) {

            // Variável de contagem, usada para contar a quantidade de faltas consecutivas
            $current = 0;

            // Para cada marcação do tratamento
            foreach ($faltas as $item) {

                // Caso seja uma falta, incrementa o contador, caso não seja, reseta a contagem
                !$item ? $current++ : $current = 0;


                //  Caso seja um tratamento não PROAMO, com 3 consecutivas | Caso seja um PROAMO com 5 faltas consecutivas
                if(   (explode(',',$key)[1] != 4 and $current > 2) or (explode(',',$key)[1] == 4 and $current > 4)  ){


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
