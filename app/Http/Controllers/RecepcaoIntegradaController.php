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
            $lista->where('p1.nome_completo', 'like', "%$request->assist%");
        }

        if ($request->status){
            $lista->where('enc.status_encaminhamento', $request->status);
        }


        $lista = $lista->orderby('status_encaminhamento', 'ASC')->orderby('at.id_prioridade', 'ASC')->orderby('dh_enc', 'ASC')->paginate(50);

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
                ->where('reu.dia', 0)        
                ->get();

        $contgrter = DB::table('reuniao_mediunica AS reu')
                ->selectRaw('count(*) as ttreu, sum(max_atend) as maxat')
                ->leftJoin('grupo AS gr', 'reu.id_grupo', 'gr.id')
                ->where('gr.data_fim', null)
                ->where('reu.id_tipo_tratamento', $idtt)
                ->where('reu.dia', 1)        
                ->get();

        $contgrqua = DB::table('reuniao_mediunica AS reu')
                ->selectRaw('count(*) as ttreu, sum(max_atend) as maxat')
                ->leftJoin('grupo AS gr', 'reu.id_grupo', 'gr.id')
                ->where('gr.data_fim', null)
                ->where('reu.id_tipo_tratamento', $idtt)
                ->where('reu.dia', 2)        
                ->get();

        $contgrqui = DB::table('reuniao_mediunica AS reu')
                ->selectRaw('count(*) as ttreu, sum(max_atend) as maxat')
                ->leftJoin('grupo AS gr', 'reu.id_grupo', 'gr.id')
                ->where('gr.data_fim', null)
                ->where('reu.id_tipo_tratamento', $idtt)
                ->where('reu.dia', 3)        
                ->get();

        $contgrsex = DB::table('reuniao_mediunica AS reu')
                ->selectRaw('count(*) as ttreu, sum(max_atend) as maxat')
                ->leftJoin('grupo AS gr', 'reu.id_grupo', 'gr.id')
                ->where('gr.data_fim', null)
                ->where('reu.id_tipo_tratamento', $idtt)
                ->where('reu.dia', 4)        
                ->get();

        $contgrsab = DB::table('reuniao_mediunica AS reu')
                ->selectRaw('count(*) as ttreu, sum(max_atend) as maxat')
                ->leftJoin('grupo AS gr', 'reu.id_grupo', 'gr.id')
                ->where('gr.data_fim', null)
                ->where('reu.id_tipo_tratamento', $idtt)
                ->where('reu.dia', 5)        
                ->get();

        $contgrdom = DB::table('reuniao_mediunica AS reu')
                ->selectRaw('count(*) as ttreu, sum(max_atend) as maxat')
                ->leftJoin('grupo AS gr', 'reu.id_grupo', 'gr.id')
                ->where('gr.data_fim', null)
                ->where('reu.id_tipo_tratamento', $idtt)
                ->where('reu.dia', 6)        
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

}
