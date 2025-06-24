<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LogAtendimentosController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        $origens = DB::table('tipo_log_origem')->get();
        $acoes = DB::table('tipo_log_acao')->get();

        return view('log-atendimentos.gerenciar-log', compact('origens', 'acoes'));
    }


    /**
     * Display the specified resource.
     */

    


    public function show(Request $request)
    {


        //sleep(3);

        $dadosTabela = DB::table('log_atendimentos as la')
            ->select(
                'la.id',
                'p.nome_completo as nome_completo_usuario',
                'to.descricao as origem',
                'ta.descricao as acao',
                'la.id_referencia',
                'la.data_hora',
                'la.id_origem',
                'la.id_acao',
                'la.id_observacao',
            )
            ->leftJoin('tipo_log_origem as to', 'la.id_origem', 'to.id')
            ->leftJoin('tipo_log_acao as ta', 'la.id_acao', 'ta.id')
            ->leftJoin('usuario as u', 'la.id_usuario', 'u.id')
            ->leftJoin('pessoas as p', 'u.id_pessoa', 'p.id')
            ->when($request->id_origem, function($query, $param){
                $query->where('la.id_origem', $param);
            })
            ->when($request->id_acao, function($query, $param){
                $query->where('la.id_acao', $param);
            })
            ->when($request->inputObs, function($query, $param){
                $query->where('la.id_observacao', $param);
            })
            ->when($request->dt_inicio, function($query, $param){
                $query->where('la.data_hora', '>=',$param);
            })
            ->when($request->dt_fim, function($query, $param){
                $query->where('la.data_hora', '<=',$param);
            })
            ->limit(2000)
            ->inRandomOrder()
            ->get()
            ->toArray();

        $idAtendimentos = array_filter(array_column($dadosTabela, 'id_referencia'), fn($k) => in_array($k, array_keys(array_column($dadosTabela, 'id_origem'), 1)), ARRAY_FILTER_USE_KEY);
        $idEncaminhamentos = array_filter(array_column($dadosTabela, 'id_referencia'), fn($k) => in_array($k, array_keys(array_column($dadosTabela, 'id_origem'), 2)), ARRAY_FILTER_USE_KEY);
        $idTratamentos = array_filter(array_column($dadosTabela, 'id_referencia'), fn($k) => in_array($k, array_keys(array_column($dadosTabela, 'id_origem'), 3)), ARRAY_FILTER_USE_KEY);
        $idEntrevistas = array_filter(array_column($dadosTabela, 'id_referencia'), fn($k) => in_array($k, array_keys(array_column($dadosTabela, 'id_origem'), 4)), ARRAY_FILTER_USE_KEY);
        $idPresencas = array_filter(array_column($dadosTabela, 'id_referencia'), fn($k) => in_array($k, array_keys(array_column($dadosTabela, 'id_origem'), 5)), ARRAY_FILTER_USE_KEY);


        $atendimentos = DB::table('atendimentos as at')
            ->select('at.id', 'p.nome_completo')
            ->leftJoin('pessoas as p', 'at.id_assistido', 'p.id')
            ->whereIn('at.id', $idAtendimentos)
            ->pluck('p.nome_completo', 'at.id')
            ->toArray();

        $encaminhamentos = DB::table('encaminhamento as enc')
            ->select('enc.id', 'p.nome_completo')
            ->leftJoin('atendimentos as at', 'enc.id_atendimento', 'at.id')
            ->leftJoin('pessoas as p', 'at.id_assistido', 'p.id')
            ->whereIn('enc.id', $idEncaminhamentos)
            ->pluck('p.nome_completo', 'enc.id')
            ->toArray();

        $tratamentos = DB::table('tratamento as tr')
            ->select('tr.id', 'p.nome_completo')
            ->leftJoin('encaminhamento as enc', 'tr.id_encaminhamento', 'enc.id')
            ->leftJoin('atendimentos as at', 'enc.id_atendimento', 'at.id')
            ->leftJoin('pessoas as p', 'at.id_assistido', 'p.id')
            ->whereIn('tr.id', $idTratamentos)
            ->pluck('p.nome_completo', 'tr.id')
            ->toArray();

        $entrevistas = DB::table('entrevistas as ent')
            ->select('ent.id', 'p.nome_completo')
            ->leftJoin('encaminhamento as enc', 'ent.id_encaminhamento', 'enc.id')
            ->leftJoin('atendimentos as at', 'enc.id_atendimento', 'at.id')
            ->leftJoin('pessoas as p', 'at.id_assistido', 'p.id')
            ->whereIn('ent.id', $idEntrevistas)
            ->pluck('p.nome_completo', 'ent.id')
            ->toArray();

        $presencasTratamento = DB::table('presenca_cronograma as pc')
            ->select('pc.id', 'p.nome_completo')
            ->leftJoin('tratamento as tr', 'pc.id_tratamento', 'tr.id')
            ->leftJoin('encaminhamento as enc', 'tr.id_encaminhamento', 'enc.id')
            ->leftJoin('atendimentos as at', 'enc.id_atendimento', 'at.id')
            ->leftJoin('pessoas as p', 'at.id_assistido', 'p.id')
            ->whereIn('pc.id', $idPresencas)
            ->whereNull('pc.id_pessoa')
            ->pluck('p.nome_completo', 'pc.id')
            ->toArray();

        $presencasEmergencia = DB::table('presenca_cronograma as pc')
            ->select('pc.id', 'p.nome_completo')
            ->leftJoin('pessoas as p', 'pc.id_pessoa', 'p.id')
            ->whereIn('pc.id', $idPresencas)
            ->whereNull('id_tratamento')
            ->pluck('p.nome_completo', 'pc.id')
            ->toArray();

        $presencas = $presencasTratamento + $presencasEmergencia;


        $status_atendimento = DB::table('tipo_status_atendimento')->pluck('descricao', 'id')->toArray();
        $status_encaminhamento = DB::table('tipo_status_encaminhamento')->pluck('descricao', 'id')->toArray();
        $status_tratamento = DB::table('tipo_status_tratamento')->pluck('nome', 'id')->toArray();
        $status_entrevista = DB::table('tipo_status_entrevista')->pluck('descricao', 'id')->toArray();

        $erro = array();
        foreach ($dadosTabela as $dado) {



            switch ($dado->id_origem) {
                case 1:
                    $dado->nome_completo = isset($atendimentos[$dado->id_referencia]) ?  $atendimentos[$dado->id_referencia] : array_push($erro, $dado->id);
                    if ($dado->id_observacao and !in_array($dado->id_acao, [5, 6, 7])) {
                        $dado->obs = $status_atendimento[$dado->id_observacao];
                    } elseif ($dado->id_observacao and in_array($dado->id_acao, [5, 6, 7])) {
                        $dado->obs = $dado->id_observacao;
                    } else {
                        $dado->obs = "";
                    }
                    break;
                case 2:
                    $dado->nome_completo = isset($encaminhamentos[$dado->id_referencia]) ?  $encaminhamentos[$dado->id_referencia] : array_push($erro, $dado->id);
                    $dado->obs = $dado->id_observacao ? $status_encaminhamento[$dado->id_observacao] : null;
                    break;
                case 3:
                    $dado->nome_completo = isset($tratamentos[$dado->id_referencia]) ?  $tratamentos[$dado->id_referencia] : array_push($erro, $dado->id);
                    $dado->obs = $dado->id_observacao ? $status_tratamento[$dado->id_observacao] : null;
                    break;
                case 4:
                    $dado->nome_completo = isset($entrevistas[$dado->id_referencia]) ?  $entrevistas[$dado->id_referencia] : array_push($erro, $dado->id);
                    $dado->obs = $dado->id_observacao ? $status_entrevista[$dado->id_observacao] : null;
                    break;
                case 5:
                    $dado->nome_completo = isset($presencas[$dado->id_referencia]) ?  $presencas[$dado->id_referencia] : array_push($erro, $dado->id);
                    $dado->obs = null;
                    break;
            }
        }


        $respostaTratada = $dadosTabela;

        return view('log-atendimentos.tabela', compact('respostaTratada', 'erro'));
    }

    public function placeholder()
    {
        return view('log-atendimentos.placeholder');
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
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
