<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class FilaEncaminhamentos implements ShouldQueue
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
        $data = Carbon::today()->subDay(15);

        // FIX PROAMO que ficam muito tempo caem quando trocam de status
        // Retorna todas as entrevistas com mais de 30 dias de criação
        $a = DB::table('encaminhamento as ent')
        ->leftJoin('atendimentos as at', 'ent.id_atendimento', 'at.id')
        ->where('status_encaminhamento', 1) // Aguardando Agendamento
        ->where('at.dh_chegada', '<', $data) // Mais que 15 dias sem marcar
        ->where(function($query){
            $query->where('ent.id_tipo_tratamento', 1); // Tratamento PTD
          //  $query->orWhere('ent.id_tipo_encaminhamento', 1); // Todos os tipos de Entrevista // Inativado devido ao bug acima
        })
        ->update([
            'status_encaminhamento' => 4, // Inativado
            'motivo' => 10
        ]);

    }
}
