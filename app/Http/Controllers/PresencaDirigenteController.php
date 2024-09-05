<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Tipo_fato;
use Illuminate\Database\DBAL\TimestampType;
use Illuminate\Support\Carbon;


class PresencaDirigenteController extends Controller
{
    // Exibe a tela de dar presença
    public function index(Request $request)
    {
  

        $reunioesDirigentes = DB::table('membro as mem')
        ->select('ass.id_pessoa', 'gr.nome', 'cr.id', 'gr.status_grupo', 'd.nome as dia')
        ->leftJoin('associado as ass', 'mem.id_associado', 'ass.id')
        ->leftJoin('cronograma as cr', 'mem.id_cronograma', 'cr.id')
        ->leftJoin('grupo as gr', 'cr.id_grupo', 'gr.id')
        ->leftJoin('tipo_dia as d', 'cr.dia_semana', 'd.id')
        ->where('ass.id_pessoa', session()->get('usuario.id_pessoa'))
        ->where('id_funcao', '<', 3)
        ->orderBy('gr.nome')
        ->distinct('gr.nome');
        $reunioes = $reunioesDirigentes->get();
        if($request->grupo == null){
            $reunioesDirigentes = $reunioesDirigentes->pluck('id');
        }else{
            $reunioesDirigentes = $reunioesDirigentes->where('cr.id', $request->grupo)->pluck('id');
        }
 
        $membros = DB::table('membro as m')
        ->select('m.id','m.id_cronograma', 'p.nome_completo', 'tf.nome')
        ->leftJoin('associado as ass', 'm.id_associado', 'ass.id')
        ->leftJoin('pessoas as p', 'ass.id_pessoa', 'p.id')
        ->leftJoin('tipo_funcao as tf', 'm.id_funcao', 'tf.id')
        ->where('m.dt_fim', null)
        ->where('m.id_cronograma', $reunioesDirigentes[0])
        ->get();
        
   
    

        return view('presenca-dirigente.gerenciar-presenca-dirigente', compact('reunioes', 'reunioesDirigentes', 'membros'));
    }

  

   
        // Método para marcar a presença
        public function marcarPresenca(Request $request)
        {
            $membroId = $request->input('membro_id');
            $grupoId = $request->input('grupo_id'); 
            $reuniaoId = $request->input('reuniao_id');
        
            // Criar registro de presença
            DB::table('presenca')->insert([
                'id_membro' => $membroId,
                'id_grupo' => $grupoId,
                'id_reuniao' => $reuniaoId,
                'dh_presenca' => now(),
            ]);
        
            return response()->json(['success' => true]);
        }
    
        // Método para cancelar a presença
        public function cancelarPresenca(Request $request)
        {
            $membroId = $request->input('membro_id');
    
            // Deletar o registro de presença
            $presenca = DB::table('presenca')->where('membro_id', $membroId)->first();
    
            if ($presenca) {
                DB::table('presenca')->where('membro_id', $membroId)->delete();
    
                return redirect()->back()->with('success', 'Presença cancelada com sucesso.');
            }
    
            return redirect()->back()->with('error', 'Este membro não tem presença marcada.');
        }
    }
    

