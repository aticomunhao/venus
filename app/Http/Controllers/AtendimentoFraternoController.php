<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Carbon;
class AtendimentoFraternoController extends Controller
{
    public function index(Request $request){

        //session()->put('usuario', [
        //    'id_usuario'=> 7,
        //    'sexo' => 1
       // ]);
        
      // dd(session());
     
        $atendente = session()->get('usuario.id_pessoa');

        $pref_att = session()->get('usuario.sexo');

        $nome = session()->get('usuario.nome');


        //$grupo = DB::table('grupos AS g')->select('g.nome as nomeg', 'g.id_dia_semana', 'g.hr_inicio', 'g.hr_fim')
                                  //  ->leftJoin('tipo_dia AS td', 'g.id_dia_semana', 'td.id')
                               //     ->get();

        //dd($nome);

        $now = Carbon::now()->format('Y-m-d');


        $assistido = DB::select("select 
                        at.id  ida,
                        p1.id  idas,
                        p1.ddd,
                        p1.celular, 
                        at.dh_chegada,
                        at.dh_inicio,
                        at.dh_fim,
                        at.id_assistido,
                        p1.nome_completo nm_1,
                        at.id_representante,
                        p2.nome_completo nm_2,
                        at.id_atendente_pref,
                        p3.nome_completo nm_3,
                        at.id_atendente, 
                        p4.nome_completo nm_4, 
                        at.pref_tipo_atendente, 
                        ts.descricao, 
                        tx.tipo,
                        pa.nome
                        from atendimentos at                    
                        left join atendentes att on (at.id_atendente =  att.id_pessoa)
                        left join tipo_status_atendimento ts on (at.status_atendimento = ts.id)
                        left join pessoas p1 on (at.id_assistido = p1.id)
                        left join pessoas p2 on (at.id_representante = p2.id)
                        left join pessoas p3 on (at.id_atendente_pref = p3.id)
                        left join pessoas p4 on (at.id_atendente = p4.id)
                        left join tp_sexo tx on (at.pref_tipo_atendente = tx.id)
                        left join tp_parentesco pa on (at.parentesco = pa.id)                                             
                        where at.dh_chegada = (select MIN(at.dh_chegada) from atendimentos at where status_atendimento = 1)
                        and tx.id = $pref_att
                        or at.pref_tipo_atendente = $atendente                        
                        
                        group by  at.id, p1.id, p2.nome_completo, p3.nome_completo, p4.nome_completo, ts.descricao, tx.tipo, pa.nome                        
                        ");

    
                    
//dd($assistido);
        return view ('/atendimento-assistido/atendendo', compact('assistido', 'atendente', 'now', 'nome'));

    
    }
}
