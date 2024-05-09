<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class GerenciarIntegralController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {


        try{

            $dirigentes = DB::table('membro as mem')
        ->select('ass.id_pessoa', 'gr.nome', 'cr.id', 'gr.status_grupo', 'd.nome as dia')
        ->leftJoin('associado as ass', 'mem.id_associado', 'ass.id')
        ->leftJoin('cronograma as cr', 'mem.id_cronograma', 'cr.id')
        ->leftJoin('grupo as gr', 'cr.id_grupo', 'gr.id')
        ->leftJoin('tipo_dia as d', 'cr.dia_semana', 'd.id')
        ->where('ass.id_pessoa', session()->get('usuario.id_pessoa'))
        ->where('id_funcao', '<', 3)
        ->where('cr.id_tipo_tratamento', 6)
        ->where('cr.status_reuniao', '<>', 2)
        ->distinct('gr.id')
        ->get();

        $grupos_autorizados = [];
        foreach($dirigentes as $dir){
            $grupos_autorizados[] = $dir->id;
        }




        $encaminhamentos = DB::table('tratamento as tr')
        ->select('tr.id','p.nome_completo', 'cro.h_inicio', 'cro.h_fim', 'gr.nome', 'tr.dt_fim')
        ->leftJoin('encaminhamento as enc', 'tr.id_encaminhamento', 'enc.id')
        ->leftJoin('cronograma as cro', 'tr.id_reuniao', 'cro.id')
        ->leftJoin('grupo as gr', 'cro.id_grupo', 'gr.id')
        ->leftJoin('atendimentos as atd', 'enc.id_atendimento', 'atd.id')
        ->leftJoin('pessoas as p','atd.id_assistido', 'p.id')
        ->where('enc.id_tipo_tratamento', 6)
        ->where('tr.status', 2)
        ->whereIn('tr.id_reuniao', $grupos_autorizados);


        if($request->nome_pesquisa){
            $encaminhamentos = $encaminhamentos->where('p.nome_completo', 'ilike', "%$request->nome_pesquisa%");
        }
        $selected_grupo = $request->grupo;
        if($request->grupo){
            $encaminhamentos = $encaminhamentos->where('tr.id_reuniao', $request->grupo);
        }
        if(!$request->grupo){
            $selected_grupo = $grupos_autorizados[0];
            $encaminhamentos = $encaminhamentos->where('tr.id_reuniao', $grupos_autorizados[0]);
        }

        $encaminhamentos = $encaminhamentos->get();

        }

        catch(\Exception $e){

                    app('flasher')->addError("Você não tem autorização para acessar esta página");
                    return redirect('/login/valida');

                }



        return view('integral.gerenciar-Integral', compact('encaminhamentos', 'dirigentes', 'selected_grupo'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $result = DB::table('tratamento AS tr')
                        ->select('enc.id AS ide', 'tr.id AS idtr', 'enc.id_tipo_encaminhamento', 'dh_enc', 'enc.id_atendimento', 'enc.status_encaminhamento', 'tse.descricao AS tsenc', 'enc.id_tipo_tratamento', 'id_tipo_entrevista', 'at.id AS ida', 'at.id_assistido','p1.dt_nascimento', 'p1.nome_completo AS nm_1', 'at.id_representante as idr', 'p2.nome_completo as nm_2', 'pa.id AS pid',  'pa.nome', 'pr.id AS prid', 'pr.descricao AS prdesc', 'pr.sigla AS prsigla', 'tt.descricao AS desctrat', 'tx.tipo', 'p4.nome_completo AS nm_4', 'at.dh_inicio', 'at.dh_fim', 'enc.status_encaminhamento AS tst', 'tr.id AS idtr', 'gr.nome AS nomeg', 'rm.h_inicio AS rm_inicio', 'tm.tipo AS tpmotivo', 'sat.descricao AS statat')
                        ->leftjoin('encaminhamento AS enc', 'tr.id_encaminhamento', 'enc.id' )
                        ->leftJoin('atendimentos AS at', 'enc.id_atendimento', 'at.id')
                        ->leftjoin('pessoas AS p1', 'at.id_assistido', 'p1.id')
                        ->leftjoin('pessoas AS p2', 'at.id_representante', 'p2.id')
                        ->leftjoin('pessoas AS p3', 'at.id_atendente_pref', 'p3.id')
                        ->leftjoin('pessoas AS p4', 'at.id_atendente', 'p4.id')
                        ->leftJoin('tp_parentesco AS pa', 'at.parentesco', 'pa.id')
                        ->leftJoin('tipo_prioridade AS pr', 'at.id_prioridade', 'pr.id')
                        ->leftJoin('tipo_status_encaminhamento AS tse', 'enc.status_encaminhamento', 'tse.id')
                        ->leftJoin('tipo_status_atendimento AS sat', 'at.status_atendimento', 'sat.id')
                        ->leftJoin('tipo_tratamento AS tt', 'enc.id_tipo_tratamento', 'tt.id')
                        ->leftJoin('tp_sexo AS tx', 'p1.sexo', 'tx.id')
                        ->leftjoin('cronograma AS rm', 'tr.id_reuniao', 'rm.id')
                        ->leftjoin('grupo AS gr', 'rm.id_grupo', 'gr.id')
                        ->leftJoin('tipo_motivo AS tm', 'enc.motivo', 'tm.id')
                        ->where('tr.id', $id)
                        ->get();

        $list = DB::table('tratamento AS tr')
                        ->select('enc.id AS ide', 'enc.id_tipo_encaminhamento', 'enc.dh_enc', 'enc.status_encaminhamento AS tst', 'tr.id AS idtr', 'rm.h_inicio AS rm_inicio', 'dt.id AS idp', 'dt.presenca', 'dc.data', 'gp.nome')
                        ->leftjoin('encaminhamento AS enc', 'tr.id_encaminhamento', 'enc.id' )
                        ->leftjoin('cronograma AS rm', 'tr.id_reuniao', 'rm.id')
                        ->leftJoin('presenca_cronograma AS dt', 'tr.id', 'dt.id_tratamento')
                        ->leftJoin('dias_cronograma as dc', 'dt.id_dias_cronograma', 'dc.id')
                        ->leftjoin('cronograma AS rm1', 'dc.id_cronograma', 'rm1.id')
                        ->leftjoin('grupo AS gp', 'rm1.id_grupo', 'gp.id')
                        ->where('tr.id', $id)
                        ->get();

        $faul = DB::table('tratamento AS tr')
                        ->select('enc.id AS ide', 'enc.id_tipo_encaminhamento', 'enc.dh_enc', 'enc.status_encaminhamento AS tst', 'tr.id AS idtr', 'rm.h_inicio AS rm_inicio', 'dt.id AS idp',  'dt.presenca')
                        ->leftjoin('encaminhamento AS enc', 'tr.id_encaminhamento', 'enc.id' )
                        ->leftjoin('cronograma AS rm', 'tr.id_reuniao', 'rm.id')
                        ->leftJoin('presenca_cronograma AS dt', 'tr.id', 'dt.id_tratamento')
                        ->where('tr.id', $id)
                        ->where('dt.presenca', 0)
                        ->count();

        return view('integral.historico-integral', compact('result', 'list', 'faul'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $hoje = Carbon::today();

        $tratamento = DB::table('tratamento')->where('id', $id)->first();

        if($tratamento->dt_fim != null){
            DB::table('tratamento')->where('id', $id)->update(['dt_fim' => null]);
        }
        elseif($tratamento->dt_fim == null){

        $id_encaminhamento = DB::table('tratamento')->where('id', $id)->first();
        DB::table('tratamento')->where('id', $id)->update(['status' => 4, 'dt_fim' => $hoje]);
        DB::table('encaminhamento')->where('id', $id_encaminhamento->id_encaminhamento)->update(['status_encaminhamento'=> 3]);

        }
        else{
            app('flasher')->addError('Houve um erro inesperado');
        }

        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     */

}
