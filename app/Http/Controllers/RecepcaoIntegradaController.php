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
                    ->select('at.id AS ida', 'p1.id AS idas', 'p1.ddd', 'p1.celular', 'at.dh_chegada', 'at.dh_inicio', 'at.dh_fim', 'at.id_assistido', 'p1.nome_completo AS nm_1', 'at.id_representante as idr', 'p2.nome_completo as nm_2', 'at.id_atendente_pref AS iap', 'p3.nome_completo as nm_3', 'at.id_atendente', 'p4.nome_completo as nm_4', 'at.pref_tipo_atendente AS pta', 'ts.descricao', 'tx.tipo', 'pa.nome', 'att.id as idatt','at.id_prioridade', 'pr.id AS prid', 'pr.descricao AS prdesc', 'pr.sigla AS prsigla' )
                    ->leftjoin('tipo_status_atendimento AS ts', 'at.status_atendimento', 'ts.id')
                    ->leftJoin('atendentes AS att', 'at.id_atendente', 'att.id')
                    ->leftJoin('pessoas AS p', 'att.id_pessoa', 'p.id')
                    ->leftjoin('pessoas AS p1', 'at.id_assistido', 'p1.id')
                    ->leftjoin('pessoas AS p2', 'at.id_representante', 'p2.id')
                    ->leftjoin('pessoas AS p3', 'at.id_atendente_pref', 'p3.id')
                    ->leftjoin('pessoas AS p4', 'at.id_atendente', 'p4.id')
                    ->leftjoin('tp_sexo AS tx', 'at.pref_tipo_atendente', 'tx.id')
                    ->leftJoin('tp_parentesco AS pa', 'at.parentesco', 'pa.id')
                    ->leftJoin('tipo_prioridade AS pr', 'at.id_prioridade', 'pr.id');

        $data_inicio = $request->dt_ini;

        $assistido = $request->assist;

        $situacao = $request->status;


        if ($request->dt_ini){
            $lista->where('at.dh_chegada', '>=', $request->dt_ini);
        }

        if ($request->assist){
            $lista->where('p1.nome_completo', 'like', "%$request->assist%");
        }

        if ($request->status){
            $lista->where('at.status_atendimento', $request->status);
        }


        $lista = $lista->orderby('status_atendimento', 'ASC')->orderBy( 'at.id_prioridade', 'ASC')->orderby('at.dh_chegada', 'ASC')->paginate(50);

        $contar = $lista->count('at.id');

        $status = DB::select("select
        s.id,
        s.descricao
        from tipo_status_atendimento s
        ");




        return view ('/recepcao-AFI/gerenciar-atendimentos', compact('lista', 'status', 'contar', 'atende', 'data_inicio', 'assistido', 'situacao'));


}
