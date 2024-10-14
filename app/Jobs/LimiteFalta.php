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
            ->where('pc.id_tratamento', '<>', null)
            ->where('pc.presenca', false)
            ->whereIn('enc.status_encaminhamento', [3, 4])
            ->get()->toArray();

        $arrayTratamentoFaltas = array();
        foreach ($tratamentos_faltas as $element) {
            $arrayTratamentoFaltas[$element->id_tratamento][] = $element->data;
        }

         dd($tratamentos_faltas, $arrayTratamentoFaltas);

        foreach ($arrayTratamentoFaltas as $key => $faltas) {
            $consecutivo = 1;
            foreach ($faltas as $falta) {
                foreach ($faltas as $faltaCross) {

                    if (Carbon::parse($falta)->addWeek($consecutivo) == Carbon::parse($faltaCross)) {
                        $consecutivo += 1;
                    }
                }
                if ($consecutivo > 2) {
                    $id_encaminhamento = DB::table('tratamento')->select('id_encaminhamento')->where('id', $key)->first();
                    DB::table('tratamento')
                        ->where('id', $key)
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
}
