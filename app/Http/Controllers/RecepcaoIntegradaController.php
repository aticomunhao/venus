<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Redirect;
use PhpParser\Node\Expr\AssignOp\Coalesce;
use PhpParser\Node\Expr\BinaryOp\Coalesce as BinaryOpCoalesce;


class RecepcaoIntegradaController extends Controller
{
    public function index(Request $request){

        $now =  Carbon::now()->format('Y-m-d');

       
        $lista = DB::table('encaminhamento AS enc')
                    ->select('enc.id AS ide', 'enc.id_tipo_encaminhamento', 'dh_enc', 'enc.id_atendimento', 'enc.status_encaminhamento', 'tse.descricao AS tsenc', 'enc.id_tipo_tratamento AS idtt', 'id_tipo_entrevista', 'at.id AS ida', 'at.id_assistido', 'p1.nome_completo AS nm_1', 'at.id_representante as idr', 'p2.nome_completo as nm_2',  'pa.nome', 'pr.id AS prid', 'pr.descricao AS prdesc', 'pr.sigla AS prsigla', 'tt.descricao AS desctrat' )
                    ->leftJoin('atendimentos AS at', 'enc.id_atendimento', 'at.id')
                    ->leftjoin('pessoas AS p1', 'at.id_assistido', 'p1.id')
                    ->leftjoin('pessoas AS p2', 'at.id_representante', 'p2.id')
                    ->leftjoin('pessoas AS p3', 'at.id_atendente_pref', 'p3.id')
                    ->leftjoin('pessoas AS p4', 'at.id_atendente', 'p4.id')                   
                    ->leftJoin('tp_parentesco AS pa', 'at.parentesco', 'pa.id')
                    ->leftJoin('tipo_prioridade AS pr', 'at.id_prioridade', 'pr.id')
                    ->leftJoin('tipo_status_encaminhamento AS tse', 'enc.status_encaminhamento', 'tse.id')
                    ->leftJoin('tipo_tratamento AS tt', 'enc.id_tipo_tratamento', 'tt.id')
                    ->where('enc.id_tipo_encaminhamento', 2)
                    ->where('enc.id_tipo_tratamento', '<>', 3);

        $data_enc = $request->dt_enc;

        $assistido = $request->assist;

        $situacao = $request->status;


        if ($request->dt_enc){
            $lista->where('enc.dh_enc', '>=', $request->dt_enc);
        }

        if ($request->assist){
            $lista->where('p1.nome_completo', 'ilike', "%$request->assist%");
        }

        if ($request->status){
            $lista->where('enc.status_encaminhamento', $request->status);
        }


        $lista = $lista->orderby('status_encaminhamento', 'ASC')->orderby('at.id_prioridade', 'ASC')->orderby('nm_1', 'ASC')->paginate(50);

        $contar = $lista->count('enc.id');


        $stat = DB::select("select
        ts.id,
        ts.descricao
        from tipo_status_encaminhamento ts
        ");




        return view ('/recepcao-integrada/gerenciar-recepcao', compact('lista', 'stat', 'contar', 'data_enc', 'assistido', 'situacao'));


    }

    public function agenda($ide, $idtt){

        $result = DB::table('encaminhamento AS enc')
                        ->select('enc.id AS ide', 'enc.id_tipo_encaminhamento', 'dh_enc', 'enc.id_atendimento', 'enc.status_encaminhamento', 'tse.descricao AS tsenc', 'enc.id_tipo_tratamento', 'id_tipo_entrevista', 'at.id AS ida', 'at.id_assistido', 'p1.nome_completo AS nm_1', 'at.id_representante as idr', 'p2.nome_completo as nm_2', 'pa.id AS pid',  'pa.nome', 'pr.id AS prid', 'pr.descricao AS prdesc', 'pr.sigla AS prsigla', 'tt.descricao AS desctrat' )
                        ->leftJoin('atendimentos AS at', 'enc.id_atendimento', 'at.id')
                        ->leftjoin('pessoas AS p1', 'at.id_assistido', 'p1.id')
                        ->leftjoin('pessoas AS p2', 'at.id_representante', 'p2.id')
                        ->leftjoin('pessoas AS p3', 'at.id_atendente_pref', 'p3.id')
                        ->leftjoin('pessoas AS p4', 'at.id_atendente', 'p4.id')                   
                        ->leftJoin('tp_parentesco AS pa', 'at.parentesco', 'pa.id')
                        ->leftJoin('tipo_prioridade AS pr', 'at.id_prioridade', 'pr.id')
                        ->leftJoin('tipo_status_encaminhamento AS tse', 'enc.status_encaminhamento', 'tse.id')
                        ->leftJoin('tipo_tratamento AS tt', 'enc.id_tipo_tratamento', 'tt.id')
                        ->where('enc.id', $ide)
                        ->get();

        $contgrseg = DB::table('reuniao_mediunica AS reu')
                        ->selectRaw('count(*) as ttreu, sum(max_atend) as maxat')
                        ->leftJoin('grupo AS gr', 'reu.id_grupo', 'gr.id')
                        ->where('gr.data_fim', null)
                        ->where('reu.id_tipo_tratamento', $idtt)
                        ->where('reu.dia', 1)        
                        ->get();

        $contgrter = DB::table('reuniao_mediunica AS reu')
                        ->selectRaw('count(*) as ttreu, sum(max_atend) as maxat')
                        ->leftJoin('grupo AS gr', 'reu.id_grupo', 'gr.id')
                        ->where('gr.data_fim', null)
                        ->where('reu.id_tipo_tratamento', $idtt)
                        ->where('reu.dia', 2)        
                        ->get();

        $contgrqua = DB::table('reuniao_mediunica AS reu')
                        ->selectRaw('count(*) as ttreu, sum(max_atend) as maxat')
                        ->leftJoin('grupo AS gr', 'reu.id_grupo', 'gr.id')
                        ->where('gr.data_fim', null)
                        ->where('reu.id_tipo_tratamento', $idtt)
                        ->where('reu.dia', 3)        
                        ->get();

        $contgrqui = DB::table('reuniao_mediunica AS reu')
                        ->selectRaw('count(*) as ttreu, sum(max_atend) as maxat')
                        ->leftJoin('grupo AS gr', 'reu.id_grupo', 'gr.id')
                        ->where('gr.data_fim', null)
                        ->where('reu.id_tipo_tratamento', $idtt)
                        ->where('reu.dia', 4)        
                        ->get();

        $contgrsex = DB::table('reuniao_mediunica AS reu')
                        ->selectRaw('count(*) as ttreu, sum(max_atend) as maxat')
                        ->leftJoin('grupo AS gr', 'reu.id_grupo', 'gr.id')
                        ->where('gr.data_fim', null)
                        ->where('reu.id_tipo_tratamento', $idtt)
                        ->where('reu.dia', 5)        
                        ->get();

        $contgrsab = DB::table('reuniao_mediunica AS reu')
                        ->selectRaw('count(*) as ttreu, sum(max_atend) as maxat')
                        ->leftJoin('grupo AS gr', 'reu.id_grupo', 'gr.id')
                        ->where('gr.data_fim', null)
                        ->where('reu.id_tipo_tratamento', $idtt)
                        ->where('reu.dia', 6)        
                        ->get();

        $contgrdom = DB::table('reuniao_mediunica AS reu')
                        ->selectRaw('count(*) as ttreu, sum(max_atend) as maxat')
                        ->leftJoin('grupo AS gr', 'reu.id_grupo', 'gr.id')
                        ->where('gr.data_fim', null)
                        ->where('reu.id_tipo_tratamento', $idtt)
                        ->where('reu.dia', 0)        
                        ->get();

        //dd($contgrter);

        $contcap = DB::table('reuniao_mediunica AS reu')
                        ->leftJoin('grupo AS gr', 'reu.id_grupo', 'gr.id')
                        ->where('gr.data_fim', null)
                        ->where('reu.id_tipo_tratamento', $idtt)
                        ->sum('reu.max_atend');
//dd($contcap);

        return view('/recepcao-integrada/agendar-dia', compact('result', 'contgrseg', 'contgrter', 'contgrqua', 'contgrqui', 'contgrsex', 'contgrsab', 'contgrdom', 'contcap'));

    }


    public function tratamento(Request $request, $ide){

        $dia = $request->dia;

        $tp_trat = DB::table('encaminhamento AS enc')
                        ->select('enc.id_tipo_tratamento')        
                        ->leftJoin('tipo_tratamento AS tt', 'enc.id_tipo_tratamento', 'tt.id')
                        ->where('enc.id', $ide)
                        ->value('enc.id_tipo_tratamento');
        //dd($tp_trat);

        $result = DB::table('encaminhamento AS enc')
                        ->select('enc.id AS ide', 'enc.id_tipo_encaminhamento', 'dh_enc', 'enc.id_atendimento', 'enc.status_encaminhamento', 'tse.descricao AS tsenc', 'enc.id_tipo_tratamento', 'id_tipo_entrevista', 'at.id AS ida', 'at.id_assistido', 'p1.nome_completo AS nm_1', 'at.id_representante as idr', 'p2.nome_completo as nm_2', 'pa.id AS pid',  'pa.nome', 'pr.id AS prid', 'pr.descricao AS prdesc', 'pr.sigla AS prsigla', 'tt.descricao AS desctrat' )
                        ->leftJoin('atendimentos AS at', 'enc.id_atendimento', 'at.id')
                        ->leftjoin('pessoas AS p1', 'at.id_assistido', 'p1.id')
                        ->leftjoin('pessoas AS p2', 'at.id_representante', 'p2.id')
                        ->leftjoin('pessoas AS p3', 'at.id_atendente_pref', 'p3.id')
                        ->leftjoin('pessoas AS p4', 'at.id_atendente', 'p4.id')                   
                        ->leftJoin('tp_parentesco AS pa', 'at.parentesco', 'pa.id')
                        ->leftJoin('tipo_prioridade AS pr', 'at.id_prioridade', 'pr.id')
                        ->leftJoin('tipo_status_encaminhamento AS tse', 'enc.status_encaminhamento', 'tse.id')
                        ->leftJoin('tipo_tratamento AS tt', 'enc.id_tipo_tratamento', 'tt.id')
                        ->where('enc.id', $ide)
                        ->get();


        $trata = DB::table('reuniao_mediunica AS reu')
                        ->select('reu.id AS idr', 'gr.nome AS nomeg', 'reu.dia AS idd', 'reu.dia', 'reu.id_sala', 'reu.id_tipo_tratamento', 'reu.id_tipo_tratamento', 'reu.h_inicio','td.nome AS nomed', 'reu.h_fim', 'reu.max_atend', 'gr.status_grupo AS idst', 'tsg.descricao as descst', 'tst.descricao AS tstd', 'sa.numero' )
                        ->leftJoin('tipo_tratamento AS tst', 'reu.id_tipo_tratamento', 'tst.id')
                        ->leftjoin('grupo AS gr', 'reu.id_grupo', 'gr.id')
                        ->leftjoin('tipo_status_grupo AS tsg', 'gr.status_grupo', 'tsg.id')
                        ->leftJoin('medium AS me', 'gr.id', 'me.id_grupo')
                        ->leftJoin('salas AS sa', 'reu.id_sala', 'sa.id')
                        ->leftJoin('tipo_dia AS td', 'reu.dia', 'td.id')
                        ->where('reu.id_tipo_tratamento', $tp_trat )
                        ->where('reu.dia', $dia)
                        ->get();
        
        return view('/recepcao-integrada/agendar-tratamento', compact('result', 'trata', 'dia'));


    }

    public function tratar(Request $request, $ide){

        $reu = intval($request->reuniao);

        $dia_semana = DB::table('reuniao_mediunica AS reu')->where('id', $reu)->value('dia');

        $data_atual = Carbon::now();
        
        $dia_atual = $data_atual->weekday();

        if ($dia_atual < $dia_semana){

            $prox = (date("Y-m-d", strtotime("$data_atual + $dia_semana day - $dia_atual day")));
        
        }elseif($dia_atual > $dia_semana){

            $prox = (date("Y-m-d", strtotime("$data_atual + $dia_semana day + 7 day - $dia_atual day"))); 
        }

        //dd($prox);

        // $primeiro_trat = date_diff();

        //dd($dia_semana);

        DB::table('tratamento AS tr')->insert([
                            'id_reuniao' => $reu,
                            'id_encaminhamento' => $ide,
                            'data' => $prox,
                            'status' => 1

        ]);


    }

}
