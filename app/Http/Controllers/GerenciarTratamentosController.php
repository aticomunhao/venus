<?php

namespace App\Http\Controllers;

use App\Jobs\DiasCronograma;
use App\Jobs\FaLtas;
use App\Jobs\LimiteFalta;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Redirect;
use PhpParser\Node\Expr\AssignOp\Coalesce;
use PhpParser\Node\Expr\BinaryOp\Coalesce as BinaryOpCoalesce;

class GerenciarTratamentosController extends Controller
{
    public function index(Request $request){

        $now =  Carbon::now()->format('Y-m-d');


        $lista = DB::table('tratamento AS tr')
                    ->select('tr.id AS idtr', 'enc.id AS ide', 'enc.id_tipo_encaminhamento', 'dh_enc', 'enc.id_atendimento', 'enc.status_encaminhamento', 'tst.nome AS tst', 'enc.id_tipo_tratamento AS idtt', 'id_tipo_entrevista', 'at.id AS ida', 'at.id_assistido', 'p1.nome_completo AS nm_1', 'at.id_representante as idr', 'p2.nome_completo as nm_2',  'pa.nome', 'pr.id AS prid', 'pr.descricao AS prdesc', 'pr.sigla AS prsigla', 'tt.descricao AS desctrat', 'tt.sigla', 'tr.id AS idtr', 'gr.nome AS nomeg', 'td.nome AS nomed', 'rm.h_inicio' )
                    ->leftJoin('encaminhamento AS enc',  'tr.id_encaminhamento', 'enc.id')
                    ->leftJoin('atendimentos AS at', 'enc.id_atendimento', 'at.id')
                    ->leftjoin('pessoas AS p1', 'at.id_assistido', 'p1.id')
                    ->leftjoin('pessoas AS p2', 'at.id_representante', 'p2.id')
                    ->leftjoin('pessoas AS p3', 'at.id_atendente_pref', 'p3.id')
                    ->leftjoin('pessoas AS p4', 'at.id_atendente', 'p4.id')
                    ->leftJoin('tp_parentesco AS pa', 'at.parentesco', 'pa.id')
                    ->leftJoin('tipo_prioridade AS pr', 'at.id_prioridade', 'pr.id')
                    ->leftJoin('tipo_status_tratamento AS tst', 'tr.status', 'tst.id')
                    ->leftJoin('tipo_tratamento AS tt', 'enc.id_tipo_tratamento', 'tt.id')
                    ->leftjoin('cronograma AS rm', 'tr.id_reuniao', 'rm.id')
                    ->leftjoin('tipo_dia AS td', 'rm.dia_semana','td.id')
                    ->leftjoin('grupo AS gr', 'rm.id_grupo', 'gr.id')
                    ->where('enc.id_tipo_encaminhamento', 2)
                    ->where('enc.id_tipo_tratamento', '<>', 3);

        $data_enc = $request->dt_enc;

        $diaP = $request->dia;

        $assistido = $request->assist;

        $situacao = $request->status;

        if ($request->dia != null){
            $lista->where('rm.dia_semana', '=', $request->dia);
        }

        if ($request->dt_enc){
            $lista->where('enc.dh_enc', '>=', $request->dt_enc);
        }

        if ($request->assist){
            $lista->where('p1.nome_completo', 'ilike', "%$request->assist%");
        }

        if ($request->status){
            $lista->where('tr.status', $request->status);
        }

        $lista = $lista->orderby('tr.status', 'ASC')->orderby('at.id_prioridade', 'ASC')->orderby('nm_1', 'ASC')->paginate(50);
        //dd($lista)->get();

        $contar = $lista->count('enc.id');


        $stat = DB::select("select
        ts.id,
        ts.nome
        from tipo_status_tratamento ts
        ");

        $dia = DB::select("select
        id,
        nome
        from tipo_dia
        ");



        return view ('/recepcao-integrada/gerenciar-tratamentos', compact('lista', 'stat', 'contar', 'data_enc', 'assistido', 'situacao', 'now', 'dia', 'diaP'));


    }


    public function presenca(Request $request, $idtr){
        dd($request->all());

        $infoTrat = DB::table('tratamento')->where('id', $idtr)->first();


        $data_atual = Carbon::now();
        $dia_atual = $data_atual->weekday();

        $confere = DB::table('presenca_cronograma AS ds')
        ->leftJoin('dias_cronograma as dc', 'ds.id_dias_cronograma', 'dc.id')
        ->where('dc.data', $data_atual)
        ->where('ds.id_tratamento', $idtr)
        ->count();

        $lista = DB::table('tratamento AS tr')
        ->leftjoin('cronograma AS rm', 'tr.id_reuniao', 'rm.id')
        ->where('tr.id', $idtr)
        ->first();

        $acompanhantes = DB::table('dias_cronograma')->where('id_cronograma', $request->reuniao)->where('data', $data_atual)->first();



        if($confere > 0){

            app('flasher')->addError('Já foi registrada a presença para este dia.');

            return Redirect('/gerenciar-tratamentos');

        }
        else if($lista->dia_semana != $dia_atual){

            app('flasher')->addError('Este assistido não corresponde ao dia de hoje.');

            return Redirect('/gerenciar-tratamentos');

        }else{

            if($infoTrat->status == 1){
                DB::table('tratamento')->where('id', $idtr)->update([
                    'status' => 2
                ]);
            }


        $presenca = isset($request->presenca) ? true : false;

        DB::table('dias_tratamento AS dt')
        ->insert([
            'data' =>  $data_atual,
            'id_tratamento' => $idtr,
            'presenca' =>$presenca
        ]);

        $nrAcomp = $acompanhantes->nr_acompanhantes + $request->acompanhantes;


        DB::table('dias_cronograma')
        ->where('id_cronograma', $request->reuniao)
        ->where('data', $data_atual)
        ->update([
            'nr_acompanhantes' => $nrAcomp
        ]);



        app('flasher')->addSuccess('Foi registrada a presença com sucesso.');

        return Redirect('/gerenciar-tratamentos');
        }

        app('flasher')->addError('Aconteceu um erro inesperado.');

        return Redirect('/gerenciar-tratamentos');
    }



    public function visualizar($idtr){

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
                        ->where('tr.id', $idtr)
                        ->get();

        $list = DB::table('tratamento AS tr')
                        ->select('enc.id AS ide', 'enc.id_tipo_encaminhamento', 'enc.dh_enc', 'enc.status_encaminhamento AS tst', 'tr.id AS idtr', 'rm.h_inicio AS rm_inicio', 'dt.id AS idp', 'dt.presenca', 'dc.data', 'gp.nome')
                        ->leftjoin('encaminhamento AS enc', 'tr.id_encaminhamento', 'enc.id' )
                        ->leftjoin('cronograma AS rm', 'tr.id_reuniao', 'rm.id')
                        ->leftJoin('presenca_cronograma AS dt', 'tr.id', 'dt.id_tratamento')
                        ->leftJoin('dias_cronograma as dc', 'dt.id_dias_cronograma', 'dc.id')
                        ->leftjoin('cronograma AS rm1', 'dc.id_cronograma', 'rm1.id')
                        ->leftjoin('grupo AS gp', 'rm1.id_grupo', 'gp.id')
                        ->where('tr.id', $idtr)
                        ->get();

        $faul = DB::table('tratamento AS tr')
                        ->select('enc.id AS ide', 'enc.id_tipo_encaminhamento', 'enc.dh_enc', 'enc.status_encaminhamento AS tst', 'tr.id AS idtr', 'rm.h_inicio AS rm_inicio', 'dt.id AS idp',  'dt.presenca')
                        ->leftjoin('encaminhamento AS enc', 'tr.id_encaminhamento', 'enc.id' )
                        ->leftjoin('cronograma AS rm', 'tr.id_reuniao', 'rm.id')
                        ->leftJoin('presenca_cronograma AS dt', 'tr.id', 'dt.id_tratamento')
                        ->where('tr.id', $idtr)
                        ->where('dt.presenca', 0)
                        ->count();


        return view('/recepcao-integrada/historico-tratamento', compact('result', 'list', 'faul'));

    }

    public function job() {
        //Faltas::dispatch();
        //LimiteFalta::dispatch();
        DiasCronograma::dispatch();
        return redirect()->back();
    }


    public function escolherGrupo($id){

        $ide = DB::table('tratamento')->where('id', $id)->first();
        $idtt = DB::table('tratamento as tr')->where('tr.id', $id)
        ->leftJoin('encaminhamento as enc', 'tr.id_encaminhamento', 'enc.id')->select('id_tipo_tratamento')->first();

        $idtt = $idtt->id_tipo_tratamento;

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
                        ->where('enc.id', $ide->id_encaminhamento)
                        ->get();

        $contgrseg = DB::table('cronograma AS reu')
                        ->selectRaw('count(*) as ttreu, sum(max_atend) as maxat')
                        ->leftJoin('grupo AS gr', 'reu.id_grupo', 'gr.id')
                        ->where('reu.data_fim', null)
                        ->where('reu.id_tipo_tratamento', $idtt)
                        ->where('reu.dia_semana', 1)
                        ->get();

        $seg = $contgrseg[0]->maxat;

        $conttratseg = DB::table('tratamento AS tr')
                        ->leftJoin('cronograma AS reu', 'tr.id_reuniao', 'reu.id' )
                        ->select(DB::raw("($seg - COUNT(CASE WHEN tr.status < 3 THEN tr.id END)) as trat"))
                        ->where('reu.id_tipo_tratamento', $idtt)
                        ->where('reu.dia_semana', 1)
                        ->get();
        //dd($conttratseg, $seg);


        $contgrter = DB::table('cronograma AS reu')
                        ->selectRaw('count(*) as ttreu, sum(max_atend) as maxat')
                        ->leftJoin('grupo AS gr', 'reu.id_grupo', 'gr.id')
                        ->where('reu.data_fim', null)
                        ->where('reu.id_tipo_tratamento', $idtt)
                        ->where('reu.dia_semana', 2)
                        ->get();

        $ter = $contgrter[0]->maxat;

        $conttratter = DB::table('tratamento AS tr')
                            ->leftJoin('cronograma AS reu', 'tr.id_reuniao', 'reu.id' )
                            ->select(DB::raw("($ter - COUNT(CASE WHEN tr.status < 3 THEN tr.id END)) as trat"))
                            ->where('reu.id_tipo_tratamento', $idtt)
                            ->where('reu.dia_semana', 2)
                            ->get();

         //dd($conttratter);

        $contgrqua = DB::table('cronograma AS reu')
                            ->selectRaw('count(*) as ttreu, sum(max_atend) as maxat')
                            ->leftJoin('grupo AS gr', 'reu.id_grupo', 'gr.id')
                            ->where('reu.data_fim', null)
                            ->where('reu.id_tipo_tratamento', $idtt)
                            ->where('reu.dia_semana', 3)
                            ->get();

        $qua = intval($contgrqua[0]->maxat);

        $conttratqua = DB::table('tratamento AS tr')
                        ->leftJoin('cronograma AS reu', 'tr.id_reuniao', 'reu.id' )
                        ->select(DB::raw("($qua - COUNT(CASE WHEN tr.status < 3 THEN tr.id END)) as trat"))
                        ->where('reu.id_tipo_tratamento', $idtt)
                        ->where('reu.dia_semana', 3)
                        ->get();

        //dd($conttratqua);

        $contgrqui = DB::table('cronograma AS reu')
                        ->selectRaw('count(*) as ttreu, sum(max_atend) as maxat')
                        ->leftJoin('grupo AS gr', 'reu.id_grupo', 'gr.id')
                        ->where('reu.data_fim', null)
                        ->where('reu.id_tipo_tratamento', $idtt)
                        ->where('reu.dia_semana', 4)
                        ->get();

        $qui = $contgrqui[0]->maxat;

        $conttratqui = DB::table('tratamento AS tr')
                        ->leftJoin('cronograma AS reu', 'tr.id_reuniao', 'reu.id' )
                        ->select(DB::raw("($qui - COUNT(CASE WHEN tr.status < 3 THEN tr.id END)) as trat"))
                        ->where('reu.id_tipo_tratamento', $idtt)
                        ->where('reu.dia_semana', 4)
                        ->get();


        $contgrsex = DB::table('cronograma AS reu')
                        ->selectRaw('count(*) as ttreu, sum(max_atend) as maxat')
                        ->leftJoin('grupo AS gr', 'reu.id_grupo', 'gr.id')
                        ->where('reu.data_fim', null)
                        ->where('reu.id_tipo_tratamento', $idtt)
                        ->where('reu.dia_semana', 5)
                        ->get();

        $sex = $contgrsex[0]->maxat;

        $conttratsex = DB::table('tratamento AS tr')
                        ->leftJoin('cronograma AS reu', 'tr.id_reuniao', 'reu.id' )
                        ->select(DB::raw("($sex - COUNT(CASE WHEN tr.status < 3 THEN tr.id END)) as trat"))
                        ->where('reu.id_tipo_tratamento', $idtt)
                        ->where('reu.dia_semana', 5)
                        ->get();

        //dd($conttratsex);

        $contgrsab = DB::table('cronograma AS reu')
                        ->selectRaw('count(*) as ttreu, sum(max_atend) as maxat')
                        ->leftJoin('grupo AS gr', 'reu.id_grupo', 'gr.id')
                        ->where('reu.data_fim', null)
                        ->where('reu.id_tipo_tratamento', $idtt)
                        ->where('reu.dia_semana', 6)
                        ->get();



        $sab = $contgrsab[0]->maxat;

        $conttratsab = DB::table('tratamento AS tr')
                        ->leftJoin('cronograma AS reu', 'tr.id_reuniao', 'reu.id' )
                        ->select(DB::raw("($sab - COUNT(CASE WHEN tr.status < 3 THEN tr.id END)) as trat"))
                        ->where('reu.id_tipo_tratamento', $idtt)
                        ->where('reu.dia_semana', 6)
                        ->get();

        $contgrdom = DB::table('cronograma AS reu')
                        ->selectRaw('count(*) as ttreu, sum(max_atend) as maxat')
                        ->leftJoin('grupo AS gr', 'reu.id_grupo', 'gr.id')
                        ->where('reu.data_fim', null)
                        ->where('reu.id_tipo_tratamento', $idtt)
                        ->where('reu.dia_semana', 0)
                        ->get();

        $dom = $contgrdom[0]->maxat;

        $conttratdom = DB::table('tratamento AS tr')
                        ->leftJoin('cronograma AS reu', 'tr.id_reuniao', 'reu.id' )
                        ->select(DB::raw("($dom - COUNT(CASE WHEN tr.status < 3 THEN tr.id END)) as trat"))
                        ->where('reu.id_tipo_tratamento', $idtt)
                        ->where('reu.dia_semana', 0)
                        ->get();

        $contcap = DB::table('cronograma AS reu')
                        ->leftJoin('grupo AS gr', 'reu.id_grupo', 'gr.id')
                        ->where('reu.data_fim', null)
                        ->where('reu.id_tipo_tratamento', $idtt)
                        ->sum('reu.max_atend');



//dd($contcap);

        return view('recepcao-integrada/agendar-grupo-tratamento', compact('result', 'contgrseg', 'contgrter', 'contgrqua', 'contgrqui', 'contgrsex', 'contgrsab', 'contgrdom', 'conttratseg', 'conttratter','conttratqua','conttratqui','conttratsex','conttratsab','conttratdom', 'contcap'));

    }


    public function escolherHorario(Request $request, $ide){




        $dia = intval($request->dia);

        $ide = intval($ide);

        $verifica = DB::table('cronograma AS rm')
                    ->select('rm.dia_semana', 'rm.id AS idrm', 'enc.id_tipo_tratamento AS trenc', 'rm.id_tipo_tratamento AS trtr')
                    ->leftJoin('tratamento AS tr', 'tr.id_reuniao', 'rm.id')
                    ->leftJoin('encaminhamento AS enc', 'tr.id_encaminhamento', 'enc.id')
                    ->where('rm.dia_semana', $dia)
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

                        ->select(DB::raw('(reu.max_atend - (select count(*) from tratamento tr where tr.id_reuniao = reu.id and tr.status < 3)) as trat'),'reu.id AS idr', 'gr.nome AS nomeg', 'reu.dia_semana', 'reu.id_sala', 'reu.id_tipo_tratamento', 'reu.h_inicio', 'td.nome AS nomed', 'reu.h_fim', 'reu.max_atend', 'gr.status_grupo AS idst', 'tsg.descricao AS descst', 'tst.descricao AS tstd', 'sa.numero')
                        ->leftJoin('tratamento AS tr', 'reu.id', 'tr.id_reuniao')
                        ->leftJoin('tipo_tratamento AS tst', 'reu.id_tipo_tratamento', 'tst.id')
                        ->leftJoin('grupo AS gr', 'reu.id_grupo', 'gr.id')
                        ->leftJoin('tipo_status_grupo AS tsg', 'gr.status_grupo', 'tsg.id')
                        ->leftJoin('membro AS me', 'gr.id', 'me.id_grupo')
                        ->leftJoin('salas AS sa', 'reu.id_sala', 'sa.id')
                        ->leftJoin('tipo_dia AS td', 'reu.dia_semana', 'td.id')
                        ->where('reu.dia_semana', $dia)
                        ->where('reu.id_tipo_tratamento', $tp_trat)
                        ->orWhere('tr.status', null)
                        ->where('tr.status', '<', 3)
                        ->groupBy('reu.h_inicio', 'reu.max_atend', 'reu.id', 'gr.nome', 'td.nome', 'gr.status_grupo', 'tsg.descricao', 'tst.descricao', 'sa.numero')
                        ->orderBy('h_inicio')
                        ->get();

        return view('/recepcao-integrada/agendar-horario-tratamento', compact('result', 'trata', 'dia'));


    }

    public function trocarGrupo(Request $request, $ide){

        $reu = intval($request->reuniao);

        //dd($dia_atual);
        $countVagas = DB::table('tratamento')->where('id_reuniao', '=', "$reu")->where('status', '<', '3' )->count();
        $maxAtend = DB::table('cronograma')->where('id', '=', "$reu")->get();
        $tratID = DB::table('encaminhamento')->where('id', '=', $ide)->get();
        $idt = DB::table('tratamento')->where('id_encaminhamento', $ide)->first();
        $data_ontem = Carbon::yesterday();

        if ($tratID[0]->id_tipo_tratamento == 2 and $countVagas >= $maxAtend[0]->max_atend){

            app('flasher')->addError('Número de vagas insuficientes');
            return redirect()->back();
        }

        $data = date("Y-m-d H:i:s");

        DB::table('historico_venus')->insert([

            'id_usuario' => session()->get('usuario.id_usuario'),
            'data' => $data,
            'fato' => 39,
            'obs' => $ide

        ]);

        $data = date("Y-m-d H:i:s");


        DB::table('tratamento_grupos')
        ->where('dt_fim', null)
        ->where('id_tratamento', $idt->id)
        ->update([
            'dt_fim' => $data_ontem,
        ]);


        DB::table('tratamento_grupos')
        ->insert([
            'id_cronograma' => $reu,
            'id_tratamento' => $idt->id,
            'dt_inicio' => $data,
        ]);




     DB::table('tratamento')->where('id_encaminhamento', $ide)->update(['id_reuniao'=> $reu]);




        return redirect('/gerenciar-tratamentos');

    }

    public function createAvulso(){

        //dd($request->all());
        $dia = Carbon::today()->weekday();

       $assistidos = DB::table('pessoas')->select('id', 'nome_completo')->orderBy('nome_completo')->get();

       $reuniao = DB::table('cronograma as cro')
       ->leftJoin('grupo as gr', 'cro.id_grupo', 'gr.id')
       ->leftJoin('salas as sl', 'cro.id_sala', 'sl.id')
       ->where('cro.dia_semana', $dia)
       ->where('cro.status_reuniao', '<>', 2)
       ->select('cro.id', 'cro.h_inicio', 'cro.h_fim', 'gr.nome', 'sl.numero as sala')
       ->get();

   // dd($reuniao);



        return view('recepcao-integrada.incluir-avulso', compact('assistidos', 'reuniao'));
    }

    public function storeAvulso(Request $request){

        $hoje = Carbon::today();
        $acompanhantes = DB::table('dias_cronograma')->where('id_cronograma', $request->reuniao)->where('data', $hoje)->first();
        $nrAcomp = $acompanhantes->nr_acompanhantes + $request->acompanhantes;


        DB::table('dias_cronograma')
        ->where('id_cronograma', $request->reuniao)
        ->where('data', $hoje)
        ->update([
            'nr_acompanhantes' => $nrAcomp
        ]);

        DB::table('presenca_cronograma')
        ->insert([
            'presenca' => true,
            'id_pessoa' => $request->assistido,
            'id_dias_cronograma' => $acompanhantes->id
        ]);
        return redirect('/gerenciar-tratamentos');
    }

}
