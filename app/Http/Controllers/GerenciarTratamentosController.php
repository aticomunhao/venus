<?php

namespace App\Http\Controllers;

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
                    ->leftjoin('tipo_dia AS td', 'rm.dia','td.id')
                    ->leftjoin('grupo AS gr', 'rm.id_grupo', 'gr.id')
                    ->where('enc.id_tipo_encaminhamento', 2)
                    ->where('enc.id_tipo_tratamento', '<>', 3)
                    ->where('rm.id_tipo_tratamento', '<>', 2)
                    ;

        $data_enc = $request->dt_enc;

        $dia = $request->dia;

        $assistido = $request->assist;

        $situacao = $request->status;

        if ($request->dia){
            $lista->where('rm.dia', '=', $request->dia);
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



        return view ('/recepcao-integrada/gerenciar-tratamentos', compact('lista', 'stat', 'contar', 'data_enc', 'assistido', 'situacao', 'now', 'dia'));


    }


    public function presenca(Request $request, $idtr){

        $infoTrat = DB::table('tratamento')->where('id', $idtr)->first();


        $data_atual = Carbon::now();
        $dia_atual = $data_atual->weekday();

        $confere = DB::table('dias_tratamento AS ds')->where('ds.data', $data_atual)->where('ds.id_tratamento', $idtr)->count();

        $lista = DB::table('tratamento AS tr')
        ->leftjoin('cronograma AS rm', 'tr.id_reuniao', 'rm.id')
        ->where('tr.id', $idtr)
        ->first();



        if($confere > 0){

            app('flasher')->addError('Já foi registrada a presença para este dia.');

            return Redirect('/gerenciar-tratamentos');

        }
        else if($lista->dia != $dia_atual){

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
                        ->select('enc.id AS ide', 'enc.id_tipo_encaminhamento', 'enc.dh_enc', 'enc.status_encaminhamento AS tst', 'tr.id AS idtr', 'rm.h_inicio AS rm_inicio', 'dt.id AS idp', 'dt.data', 'dt.presenca' )
                        ->leftjoin('encaminhamento AS enc', 'tr.id_encaminhamento', 'enc.id' )
                        ->leftjoin('cronograma AS rm', 'tr.id_reuniao', 'rm.id')
                        ->leftJoin('dias_tratamento AS dt', 'tr.id', 'dt.id_tratamento')
                        ->where('tr.id', $idtr)
                        ->get();

        $faul = DB::table('tratamento AS tr')
                        ->select('enc.id AS ide', 'enc.id_tipo_encaminhamento', 'enc.dh_enc', 'enc.status_encaminhamento AS tst', 'tr.id AS idtr', 'rm.h_inicio AS rm_inicio', 'dt.id AS idp', 'dt.data', 'dt.presenca')
                        ->leftjoin('encaminhamento AS enc', 'tr.id_encaminhamento', 'enc.id' )
                        ->leftjoin('cronograma AS rm', 'tr.id_reuniao', 'rm.id')
                        ->leftJoin('dias_tratamento AS dt', 'tr.id', 'dt.id_tratamento')
                        ->where('tr.id', $idtr)
                        ->where('dt.presenca', 0)
                        ->count();


        return view('/recepcao-integrada/historico-tratamento', compact('result', 'list', 'faul'));

    }


}
