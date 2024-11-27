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
            ->select('pc.id_tratamento', 'dc.data')
            ->leftJoin('tratamento as tr', 'pc.id_tratamento', 'tr.id')
            ->leftJoin('encaminhamento as enc', 'tr.id_encaminhamento', 'enc.id')
            ->leftJoin('atendimentos as at', 'enc.id_atendimento', 'at.id')
            ->leftJoin('dias_cronograma as dc', 'pc.id_dias_cronograma', 'dc.id')
            ->where('pc.id_tratamento', '<>', null) // Retira os avulsos
            ->where('pc.presenca', false) // Faltas
            ->where('tr.status', 2) // Encaminhamentos Agendados
            ->orderBy('dc.data')
            ->get()->toArray();

        // Organiza os dados de uma maneira onde a KEY é o id Tratamento
        $arrayTratamentoFaltas = array(); 
        foreach ($tratamentos_faltas as $element) {
            $arrayTratamentoFaltas[$element->id_tratamento][] = $element->data;
        }

       

        foreach ($arrayTratamentoFaltas as $key => $faltas) {// Para cada ID tratamento
            foreach ($faltas as $falta) { // Para cada Falta de um ID tratamento
                $consecutivo = 1;
                foreach ($faltas as $faltaCross) { // Checa a quantidade de faltas consecutivas a partir daquela

                    if (Carbon::parse($falta)->addWeek($consecutivo) == Carbon::parse($faltaCross)) {
                        $consecutivo += 1;
                    }
                }
                if ($consecutivo > 3) { // Se a quantidade de faltas for 3 ou mais 
                    $id_encaminhamento = DB::table('tratamento')->select('id_encaminhamento')->where('id', $key)->first();
                    DB::table('tratamento')
                        ->where('id', $key)
                        ->update([
                            'status' => 5 // Finaliza por falta o tratamento
                        ]);

                    DB::table('encaminhamento')
                        ->where('id', $id_encaminhamento->id_encaminhamento)
                        ->update([
                            'status_encaminhamento' => 6 // Inativa o encaminhamento
                        ]);
                }
            }
            
        }
    }
}
