<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class EntrevistaProamo implements ShouldQueue
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

        // Retorna os ID_pessoa dos assistidos aguardando uma entrevista PROAMO
        $assistidos = DB::table('encaminhamento as enc')
            ->leftJoin('atendimentos as at', 'enc.id_atendimento', 'at.id')
            ->where('status_encaminhamento', 7)
            ->pluck('id_assistido', 'enc.id')
            ->toArray();

        foreach ($assistidos as $key => $assistido) {

            // Retorna o ID do PTD ativo desse assistido
            $idPTD = DB::table('tratamento as tr')
                ->select('tr.id')
                ->leftJoin('encaminhamento as enc', 'tr.id_encaminhamento', 'enc.id')
                ->leftJoin('atendimentos as at', 'enc.id_atendimento', 'at.id')
                ->where('id_assistido', $assistido)
                ->where('id_tipo_tratamento', 1)
                ->where('status_encaminhamento', 2)
                ->first();

            // Conta a quantidade de presenças PTD do assistido
            $presencas = DB::table('presenca_cronograma')
                ->where('id_tratamento', $idPTD->id)
                ->where('presenca', true)
                ->count();

            // Caso o número de presenças exceda 7, disponibilize a entrevista PROAMO
            if ($presencas > 7) {

                // Atualiza o encaminhamento de Entrevista Proamo para Aguardando Agendamento
                DB::table('encaminhamento')
                    ->where('id', $key)
                    ->update([
                        'status_encaminhamento' => 1
                    ]);
            }
        }
    }
}