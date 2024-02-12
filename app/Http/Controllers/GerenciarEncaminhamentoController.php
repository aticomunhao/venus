<?php

namespace App\Http\Controllers;

use Faker\Core\Number;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Redirect;
use PhpParser\Node\Expr\AssignOp\Coalesce;
use PhpParser\Node\Expr\BinaryOp\Coalesce as BinaryOpCoalesce;


class GerenciarEncaminhamentoController extends Controller
{
    public function index(Request $request){

        $now =  Carbon::now()->format('Y-m-d');


        $lista = DB::table('encaminhamento AS enc')
                    ->select('enc.id AS ide', 'enc.id_tipo_encaminhamento', 'dh_enc', 'enc.id_atendimento', 'enc.status_encaminhamento', 'tse.descricao AS tsenc', 'enc.id_tipo_tratamento AS idtt', 'id_tipo_entrevista', 'at.id AS ida', 'at.id_assistido', 'p1.nome_completo AS nm_1', 'at.id_representante as idr', 'p2.nome_completo as nm_2',  'pa.nome', 'pr.id AS prid', 'pr.descricao AS prdesc', 'pr.sigla AS prsigla', 'tt.descricao AS desctrat', 'tt.sigla', 'tr.id AS idtr', 'gr.nome AS nomeg' )
                    ->leftJoin('atendimentos AS at', 'enc.id_atendimento', 'at.id')
                    ->leftjoin('pessoas AS p1', 'at.id_assistido', 'p1.id')
                    ->leftjoin('pessoas AS p2', 'at.id_representante', 'p2.id')
                    ->leftjoin('pessoas AS p3', 'at.id_atendente_pref', 'p3.id')
                    ->leftjoin('pessoas AS p4', 'at.id_atendente', 'p4.id')
                    ->leftJoin('tp_parentesco AS pa', 'at.parentesco', 'pa.id')
                    ->leftJoin('tipo_prioridade AS pr', 'at.id_prioridade', 'pr.id')
                    ->leftJoin('tipo_status_encaminhamento AS tse', 'enc.status_encaminhamento', 'tse.id')
                    ->leftJoin('tipo_tratamento AS tt', 'enc.id_tipo_tratamento', 'tt.id')
                    ->leftjoin('tratamento AS tr', 'enc.id', 'tr.id_encaminhamento')
                    ->leftjoin('cronograma AS rm', 'tr.id_reuniao', 'rm.id')
                    ->leftjoin('grupo AS gr', 'rm.id_grupo', 'gr.id')
                    ->where('enc.id_tipo_encaminhamento', 2)
                    ->where('enc.id_tipo_tratamento', '<>', 3);

        $data_enc = $request->dt_enc;

        $assistido = $request->assist;

        $situacao = $request->status;   //


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
        //dd($lista)->get();

        $contar = $lista->count('enc.id');


        $stat = DB::select("select
        ts.id,
        ts.descricao
        from tipo_status_encaminhamento ts
        ");

        $motivo = DB::select("select
        tm.id,
        tm.tipo
        from tipo_motivo tm
        ");


        return view ('/recepcao-integrada/gerenciar-encaminhamentos', compact('lista', 'stat', 'contar', 'data_enc', 'assistido', 'situacao', 'now', 'motivo'));


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

        $contgrseg = DB::table('cronograma AS reu')
                        ->selectRaw('count(*) as ttreu, sum(max_atend) as maxat')
                        ->leftJoin('grupo AS gr', 'reu.id_grupo', 'gr.id')
                        ->where('gr.data_fim', null)
                        ->where('reu.id_tipo_tratamento', $idtt)
                        ->where('reu.dia', 1)
                        ->get();

        $seg = $contgrseg[0]->maxat;

        $conttratseg = DB::table('tratamento AS tr')
                        ->leftJoin('cronograma AS reu', 'tr.id_reuniao', 'reu.id' )
                        ->select(DB::raw("($seg - COUNT(CASE WHEN tr.status < 3 THEN tr.id END)) as trat"))
                        ->where('reu.id_tipo_tratamento', $idtt)
                        ->where('reu.dia', 1)
                        ->get();
        //dd($conttratseg, $seg);


        $contgrter = DB::table('cronograma AS reu')
                        ->selectRaw('count(*) as ttreu, sum(max_atend) as maxat')
                        ->leftJoin('grupo AS gr', 'reu.id_grupo', 'gr.id')
                        ->where('gr.data_fim', null)
                        ->where('reu.id_tipo_tratamento', $idtt)
                        ->where('reu.dia', 2)
                        ->get();

        $ter = $contgrter[0]->maxat;

        $conttratter = DB::table('tratamento AS tr')
                            ->leftJoin('cronograma AS reu', 'tr.id_reuniao', 'reu.id' )
                            ->select(DB::raw("($ter - COUNT(CASE WHEN tr.status < 3 THEN tr.id END)) as trat"))
                            ->where('reu.id_tipo_tratamento', $idtt)
                            ->where('reu.dia', 2)
                            ->get();

         //dd($conttratter);

        $contgrqua = DB::table('cronograma AS reu')
                            ->selectRaw('count(*) as ttreu, sum(max_atend) as maxat')
                            ->leftJoin('grupo AS gr', 'reu.id_grupo', 'gr.id')
                            ->where('gr.data_fim', null)
                            ->where('reu.id_tipo_tratamento', $idtt)
                            ->where('reu.dia', 3)
                            ->get();

        $qua = intval($contgrqua[0]->maxat);

        $conttratqua = DB::table('tratamento AS tr')
                        ->leftJoin('cronograma AS reu', 'tr.id_reuniao', 'reu.id' )
                        ->select(DB::raw("($qua - COUNT(CASE WHEN tr.status < 3 THEN tr.id END)) as trat"))
                        ->where('reu.id_tipo_tratamento', $idtt)
                        ->where('reu.dia', 3)
                        ->get();

        //dd($conttratqua);

        $contgrqui = DB::table('cronograma AS reu')
                        ->selectRaw('count(*) as ttreu, sum(max_atend) as maxat')
                        ->leftJoin('grupo AS gr', 'reu.id_grupo', 'gr.id')
                        ->where('gr.data_fim', null)
                        ->where('reu.id_tipo_tratamento', $idtt)
                        ->where('reu.dia', 4)
                        ->get();

        $qui = $contgrqui[0]->maxat;

        $conttratqui = DB::table('tratamento AS tr')
                        ->leftJoin('cronograma AS reu', 'tr.id_reuniao', 'reu.id' )
                        ->select(DB::raw("($qui - COUNT(CASE WHEN tr.status < 3 THEN tr.id END)) as trat"))
                        ->where('reu.id_tipo_tratamento', $idtt)
                        ->where('reu.dia', 4)
                        ->get();


        $contgrsex = DB::table('cronograma AS reu')
                        ->selectRaw('count(*) as ttreu, sum(max_atend) as maxat')
                        ->leftJoin('grupo AS gr', 'reu.id_grupo', 'gr.id')
                        ->where('gr.data_fim', null)
                        ->where('reu.id_tipo_tratamento', $idtt)
                        ->where('reu.dia', 5)
                        ->get();

        $sex = $contgrsex[0]->maxat;

        $conttratsex = DB::table('tratamento AS tr')
                        ->leftJoin('cronograma AS reu', 'tr.id_reuniao', 'reu.id' )
                        ->select(DB::raw("($sex - COUNT(CASE WHEN tr.status < 3 THEN tr.id END)) as trat"))
                        ->where('reu.id_tipo_tratamento', $idtt)
                        ->where('reu.dia', 5)
                        ->get();

        //dd($conttratsex);

        $contgrsab = DB::table('cronograma AS reu')
                        ->selectRaw('count(*) as ttreu, sum(max_atend) as maxat')
                        ->leftJoin('grupo AS gr', 'reu.id_grupo', 'gr.id')
                        ->where('gr.data_fim', null)
                        ->where('reu.id_tipo_tratamento', $idtt)
                        ->where('reu.dia', 6)
                        ->get();



        $sab = $contgrsab[0]->maxat;

        $conttratsab = DB::table('tratamento AS tr')
                        ->leftJoin('cronograma AS reu', 'tr.id_reuniao', 'reu.id' )
                        ->select(DB::raw("($sab - COUNT(CASE WHEN tr.status < 3 THEN tr.id END)) as trat"))
                        ->where('reu.id_tipo_tratamento', $idtt)
                        ->where('reu.dia', 6)
                        ->get();

        $contgrdom = DB::table('cronograma AS reu')
                        ->selectRaw('count(*) as ttreu, sum(max_atend) as maxat')
                        ->leftJoin('grupo AS gr', 'reu.id_grupo', 'gr.id')
                        ->where('gr.data_fim', null)
                        ->where('reu.id_tipo_tratamento', $idtt)
                        ->where('reu.dia', 0)
                        ->get();

        $dom = $contgrdom[0]->maxat;

        $conttratdom = DB::table('tratamento AS tr')
                        ->leftJoin('cronograma AS reu', 'tr.id_reuniao', 'reu.id' )
                        ->select(DB::raw("($dom - COUNT(CASE WHEN tr.status < 3 THEN tr.id END)) as trat"))
                        ->where('reu.id_tipo_tratamento', $idtt)
                        ->where('reu.dia', 0)
                        ->get();

        $contcap = DB::table('cronograma AS reu')
                        ->leftJoin('grupo AS gr', 'reu.id_grupo', 'gr.id')
                        ->where('gr.data_fim', null)
                        ->where('reu.id_tipo_tratamento', $idtt)
                        ->sum('reu.max_atend');



//dd($contcap);

        return view('/recepcao-integrada/agendar-dia', compact('result', 'contgrseg', 'contgrter', 'contgrqua', 'contgrqui', 'contgrsex', 'contgrsab', 'contgrdom', 'conttratseg', 'conttratter','conttratqua','conttratqui','conttratsex','conttratsab','conttratdom', 'contcap'));

    }


    public function tratamento(Request $request, $ide){




        $dia = intval($request->dia);

        $ide = intval($ide);

        $verifica = DB::table('cronograma AS rm')
                    ->select('rm.dia', 'rm.id AS idrm', 'enc.id_tipo_tratamento AS trenc', 'rm.id_tipo_tratamento AS trtr')
                    ->leftJoin('tratamento AS tr', 'tr.id_reuniao', 'rm.id')
                    ->leftJoin('encaminhamento AS enc', 'tr.id_encaminhamento', 'enc.id')
                    ->where('rm.dia', $dia)
                    ->distinct('rm.id')
                    ->whereRaw('enc.id_tipo_tratamento = rm.id_tipo_tratamento')
                    ->get();


        $tp_trat = DB::table('encaminhamento AS enc')
                        ->select('enc.id_tipo_tratamento')
                        ->leftJoin('tipo_tratamento AS tt', 'enc.id_tipo_tratamento', 'tt.id')
                        ->where('enc.id', $ide)
                        ->value('enc.id_tipo_tratamento');


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


        $trata = DB::table('cronograma AS reu')

                        ->select(DB::raw('(reu.max_atend - (select count(*) from tratamento tr where tr.id_reuniao = reu.id and tr.status < 3)) as trat'),'reu.id AS idr', 'gr.nome AS nomeg', 'reu.dia', 'reu.id_sala', 'reu.id_tipo_tratamento', 'reu.h_inicio', 'td.nome AS nomed', 'reu.h_fim', 'reu.max_atend', 'gr.status_grupo AS idst', 'tsg.descricao AS descst', 'tst.descricao AS tstd', 'sa.numero')
                        ->leftJoin('tratamento AS tr', 'reu.id', 'tr.id_reuniao')
                        ->leftJoin('tipo_tratamento AS tst', 'reu.id_tipo_tratamento', 'tst.id')
                        ->leftJoin('grupo AS gr', 'reu.id_grupo', 'gr.id')
                        ->leftJoin('tipo_status_grupo AS tsg', 'gr.status_grupo', 'tsg.id')
                        ->leftJoin('medium AS me', 'gr.id', 'me.id_grupo')
                        ->leftJoin('salas AS sa', 'reu.id_sala', 'sa.id')
                        ->leftJoin('tipo_dia AS td', 'reu.dia', 'td.id')
                        ->where('reu.dia', $dia)
                        ->where('reu.id_tipo_tratamento', $tp_trat)
                        ->orWhere('tr.status', null)
                        ->where('tr.status', '<', 3)
                        ->groupBy('reu.h_inicio', 'reu.max_atend', 'reu.id', 'gr.nome', 'td.nome', 'gr.status_grupo', 'tsg.descricao', 'tst.descricao', 'sa.numero')
                        ->orderBy('h_inicio')
                        ->get();

        return view('/recepcao-integrada/agendar-tratamento', compact('result', 'trata', 'dia'));


    }

    public function tratar(Request $request, $ide){

        $reu = intval($request->reuniao);

        $dia_semana = DB::table('cronograma AS reu')->where('id', $reu)->value('dia');

        $data_atual = Carbon::now();

        $dia_atual = $data_atual->weekday();

        //dd($dia_atual);
        $countVagas = DB::table('tratamento')->where('id_reuniao', '=', "$reu")->where('status', '<', '3' )->count();
        $maxAtend = DB::table('cronograma')->where('id', '=', "$reu")->get();
        $tratID = DB::table('encaminhamento')->where('id', '=', $ide)->get();


        if ($tratID[0]->id_tipo_tratamento == 2 and $countVagas >= $maxAtend[0]->max_atend){

            app('flasher')->addError('Número de vagas insuficientes');
            return redirect()->back();
        }




        if ($dia_atual < $dia_semana){

            $prox = (date("Y-m-d", strtotime("$data_atual + $dia_semana day - $dia_atual day")));

            $id_trata = DB::table('tratamento AS t')
            ->select(DB::raw('MAX(id) as max_id'))
            ->value('max_id');

            DB::table('tratamento AS tr')
                                ->insert([
                                'id_reuniao' => $reu,
                                'id_encaminhamento' => $ide,
                                'status' => 1

            ]);

            DB::table('dias_tratamento AS dt')
                                ->insert([
                                'id_tratamento' => $id_trata,
                                'data' => $prox

            ]);

            DB::table('encaminhamento AS enc')
                                        ->where('enc.id', $ide)
                                        ->update([
                                        'status_encaminhamento' => 2
            ]);

            app('flasher')->addSuccess('O tratamento foi agendo com sucesso.');

            return redirect('/gerenciar-encaminhamentos');


        }elseif($dia_atual > $dia_semana){

            $prox = (date("Y-m-d", strtotime("$data_atual + $dia_semana day + 7 day - $dia_atual day")));


        $id_trata = DB::table('tratamento AS t')
        ->select(DB::raw('MAX(id) as max_id'))
        ->value('max_id');

        DB::table('tratamento AS tr')
                            ->insert([
                            'id_reuniao' => $reu,
                            'id_encaminhamento' => $ide,
                            'status' => 1

        ]);

        DB::table('dias_tratamento AS dt')
                            ->insert([
                            'id_tratamento' => $id_trata,
                            'data' => $prox

        ]);

        DB::table('encaminhamento AS enc')
                                    ->where('enc.id', $ide)
                                    ->update([
                                    'status_encaminhamento' => 2
        ]);

        app('flasher')->addSuccess('O tratamento foi agendo com sucesso.');

        return redirect('/gerenciar-encaminhamentos');

        }

        app('flasher')->addError('Aconteceu um erro ao criar o tratamento contate a ATI.');

        return redirect('/gerenciar-encaminhamentos');

    }


    public function visualizar($ide){

        $result = DB::table('encaminhamento AS enc')
                        ->select('enc.id AS ide', 'enc.id_tipo_encaminhamento', 'dh_enc', 'enc.id_atendimento', 'enc.status_encaminhamento', 'tse.descricao AS tsenc', 'enc.id_tipo_tratamento', 'id_tipo_entrevista', 'at.id AS ida', 'at.id_assistido','p1.dt_nascimento', 'p1.nome_completo AS nm_1', 'at.id_representante as idr', 'p2.nome_completo as nm_2', 'pa.id AS pid',  'pa.nome', 'pr.id AS prid', 'pr.descricao AS prdesc', 'pr.sigla AS prsigla', 'tt.descricao AS desctrat', 'tx.tipo', 'p4.nome_completo AS nm_4', 'at.dh_inicio', 'at.dh_fim', 'enc.status_encaminhamento AS tst', 'tr.id AS idtr', 'gr.nome AS nomeg', 'rm.h_inicio AS rm_inicio', 'tm.tipo AS tpmotivo')
                        ->leftJoin('atendimentos AS at', 'enc.id_atendimento', 'at.id')
                        ->leftjoin('pessoas AS p1', 'at.id_assistido', 'p1.id')
                        ->leftjoin('pessoas AS p2', 'at.id_representante', 'p2.id')
                        ->leftjoin('pessoas AS p3', 'at.id_atendente_pref', 'p3.id')
                        ->leftjoin('pessoas AS p4', 'at.id_atendente', 'p4.id')
                        ->leftJoin('tp_parentesco AS pa', 'at.parentesco', 'pa.id')
                        ->leftJoin('tipo_prioridade AS pr', 'at.id_prioridade', 'pr.id')
                        ->leftJoin('tipo_status_encaminhamento AS tse', 'enc.status_encaminhamento', 'tse.id')
                        ->leftJoin('tipo_tratamento AS tt', 'enc.id_tipo_tratamento', 'tt.id')
                        ->leftJoin('tp_sexo AS tx', 'p1.sexo', 'tx.id')
                        ->leftjoin('tratamento AS tr', 'enc.id', 'tr.id_encaminhamento')
                        ->leftjoin('cronograma AS rm', 'tr.id_reuniao', 'rm.id')
                        ->leftjoin('grupo AS gr', 'rm.id_grupo', 'gr.id')
                        ->leftJoin('tipo_motivo AS tm', 'enc.motivo', 'tm.id')
                        ->where('enc.id', $ide)
                        ->get();

        $list = DB::table('encaminhamento AS enc')
                        ->select('enc.id AS ide', 'enc.id_tipo_encaminhamento', 'enc.dh_enc', 'enc.status_encaminhamento AS tst', 'tr.id AS idtr', 'rm.h_inicio AS rm_inicio', 'dt.id AS idp', 'dt.data', 'dt.presenca' )
                        ->leftjoin('tratamento AS tr', 'enc.id', 'tr.id_encaminhamento')
                        ->leftjoin('cronograma AS rm', 'tr.id_reuniao', 'rm.id')
                        ->leftJoin('dias_tratamento AS dt', 'tr.id', 'dt.id_tratamento')
                        ->where('enc.id', $ide)
                        ->get();

        $faul = DB::table('encaminhamento AS enc')
                        ->select('enc.id AS ide', 'enc.id_tipo_encaminhamento', 'enc.dh_enc', 'enc.status_encaminhamento AS tst', 'tr.id AS idtr', 'rm.h_inicio AS rm_inicio', 'dt.id AS idp', 'dt.data', 'dt.presenca')
                        ->leftjoin('tratamento AS tr', 'enc.id', 'tr.id_encaminhamento')
                        ->leftjoin('cronograma AS rm', 'tr.id_reuniao', 'rm.id')
                        ->leftJoin('dias_tratamento AS dt', 'tr.id', 'dt.id_tratamento')
                        ->where('enc.id', $ide)
                        ->where('presenca', 0)
                        ->count();


        return view('/recepcao-integrada/historico-encaminhamento', compact('result', 'list', 'faul'));

    }


    public function inative(Request $request, $ide){

        $today = Carbon::today()->format('Y-m-d');


        $inative =  DB::table('encaminhamento AS enc')
                        ->where('enc.id', $ide)
                        ->update([
                            'status_encaminhamento' => 4,
                            'motivo' => $request->input('motivo')

                        ]);



                    DB::table('historico_venus')->insert([
                        'id_usuario' => session()->get('usuario.id_usuario'),
                        'data' => $today,
                        'fato' => 36,
                        'id_ref' => $ide

        ]);

        app('flasher')->addSuccess('O encaminhamento foi inativado.');


        return redirect('/gerenciar-encaminhamentos');

    }


}
