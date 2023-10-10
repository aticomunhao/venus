<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Carbon;

class AtendimentoFraternoController extends Controller
{
    public function index(Request $request){

        session()->put('usuario', [
            'id_usuario'=> 12,
            'sexo' => 1
        ]);
        
       // dd(session());

        $atendente = session()->get('usuario.id_usuario', 'usuario.sexo' );

        $grupo = DB::table('grupos AS g')->select('g.nome as nomeg', 'g.id_dia_semana', 'g.hr_inicio', 'g.hr_fim')
                                    ->leftJoin('tipo_dia AS td', 'g.id_dia_semana', 'td.id')
                                    ->get();

        //dd($atendente);

        $now = Carbon::now()->format('Y-m-d');


        $lista = DB::table('atendimentos AS at')
                    ->select('at.id AS ida', 'p1.id AS idas', 'p1.ddd', 'p1.celular', 'at.dh_chegada', 'at.dh_inicio', 'at.dh_fim', 'at.id_assistido', 'p1.nome_completo AS nm_1', 'at.id_representante', 'p2.nome_completo as nm_2', 'at.id_atendente_pref', 'p3.nome_completo as nm_3', 'at.id_atendente', 'p4.nome_completo as nm_4', 'at.pref_tipo_atendente', 'ts.descricao', 'tx.tipo', 'pa.nome' )
                    //->selectRaw('max(dh_chegada) from atendimentos')
                    ->leftJoin('atendentes AS att', 'at.id_atendente', 'att.id_pessoa')
                    ->leftjoin('tipo_status_atendimento AS ts', 'at.status_atendimento', 'ts.id')
                    ->leftjoin('pessoas AS p1', 'at.id_assistido', 'p1.id')
                    ->leftjoin('pessoas AS p2', 'at.id_representante', 'p2.id')
                    ->leftjoin('pessoas AS p3', 'at.id_atendente_pref', 'p3.id')
                    ->leftjoin('pessoas AS p4', 'at.id_atendente', 'p4.id')
                    ->leftjoin('tp_sexo AS tx', 'at.pref_tipo_atendente', 'tx.id')
                    ->leftJoin('tp_parentesco AS pa', 'at.parentesco', 'pa.id')
                    
                    ->where('status_atendimento', 1)
                    ->orWhere('at.pref_tipo_atendente', )
                    ->get();
                    //->orwhere('at.id_atendente_preferido', $atendente)

        return view ('/atendimento-assistido/atendendo', compact('lista', 'grupo'));

    
    }
}
