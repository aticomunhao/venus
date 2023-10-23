<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Carbon;

use function Laravel\Prompts\select;

class AtendimentoFraternoController extends Controller
{
    public function index(Request $request){

     
        $atendente = session()->get('usuario.id_pessoa');

        $pref_att = session()->get('usuario.sexo');

        $nome = session()->get('usuario.nome');

        $now = Carbon::now()->format('Y-m-d H:m:s');

        $assistido = DB::table('atendimentos AS at')
                    ->select('at.id AS ida', 'p1.id AS idas', 'p1.ddd', 'p1.celular', 'at.dh_chegada', 'at.dh_inicio', 'at.dh_fim', 'at.id_assistido', 'p1.nome_completo AS nm_1', 'at.id_representante', 'p2.nome_completo AS nm_2', 'at.id_atendente_pref', 'p3.nome_completo AS nm_3', 'at.id_atendente', 'p4.nome_completo AS nm_4', 'at.pref_tipo_atendente', 'ts.descricao', 'tx.tipo','pa.nome')
                    ->leftJoin('atendentes AS att', 'at.id_atendente', 'att.id_pessoa')
                    ->leftJoin('tipo_status_atendimento AS ts', 'at.status_atendimento', 'ts.id')
                    ->leftJoin('pessoas AS p1', 'at.id_assistido', 'p1.id')
                    ->leftJoin('pessoas AS p2', 'at.id_representante', 'p2.id')
                    ->leftJoin('pessoas AS p3', 'at.id_atendente_pref', 'p3.id')
                    ->leftJoin('pessoas AS p4', 'at.id_atendente', 'p4.id')
                    ->leftJoin('tp_sexo AS tx', 'at.pref_tipo_atendente', 'tx.id')
                    ->leftJoin('tp_parentesco AS pa', 'at.parentesco', 'pa.id')
                    ->where('at.dh_chegada', '=', DB::raw('select min(at.dh_chegada) from atendimentos at where status_atendimento = 1'))
                  //  ->where('tx.id', $pref_att)
                   // ->orWhere ('at.pref_tipo_atendente', $atendente)
                   // ->whereNull('at.pref_tipo_atendente')                                            
                    ->groupby('at.id', 'p1.id', 'p2.nome_completo', 'p3.nome_completo', 'p4.nome_completo', 'ts.descricao', 'tx.tipo', 'pa.nome')
                    ->get();        

                    //dd($now);
      

    
                    

        return view ('/atendimento-assistido/atendendo', compact('assistido', 'atendente', 'now', 'nome'));

    
    }
}

//   $assistido = DB::select("select 
//                         at.id  ida,
//                         p1.id  idas,
//                         p1.ddd,
//                         p1.celular, 
//                         at.dh_chegada,
//                         at.dh_inicio,
//                         at.dh_fim,
//                         at.id_assistido,
//                         p1.nome_completo nm_1,
//                         at.id_representante,
//                         p2.nome_completo nm_2,
//                         at.id_atendente_pref,
//                         p3.nome_completo nm_3,
//                         at.id_atendente, 
//                         p4.nome_completo nm_4, 
//                         at.pref_tipo_atendente, 
//                         ts.descricao, 
//                         tx.tipo,
//                         pa.nome
//                         from atendimentos at                    
//                         left join atendentes att on (at.id_atendente =  att.id_pessoa)
//                         left join tipo_status_atendimento ts on (at.status_atendimento = ts.id)
//                         left join pessoas p1 on (at.id_assistido = p1.id)
//                         left join pessoas p2 on (at.id_representante = p2.id)
//                         left join pessoas p3 on (at.id_atendente_pref = p3.id)
//                         left join pessoas p4 on (at.id_atendente = p4.id)
//                         left join tp_sexo tx on (at.pref_tipo_atendente = tx.id)
//                         left join tp_parentesco pa on (at.parentesco = pa.id)                                             
//                         where at.dh_chegada = (select MIN(at.dh_chegada) from atendimentos at where status_atendimento = 1)
//                         and tx.id = $pref_att
//                         or at.pref_tipo_atendente = $atendente
//                         null at.pref_tipo_atendente

                        
//                         group by  at.id, p1.id, p2.nome_completo, p3.nome_completo, p4.nome_completo, ts.descricao, tx.tipo, pa.nome                        
//                         ");