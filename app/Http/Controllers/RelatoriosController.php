<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class RelatoriosController extends Controller
{
    /**
     * Display a listing of the resource.
     */  
    public function indexAFI(Request $request)
    {
        $afiSelecionado = $request->afi;
        $dt_inicio = $request->dt_inicio == null ? (Carbon::now()->subMonth()->firstOfMonth()->format('Y-m-d')) : $request->dt_inicio;
        $dt_fim =  $request->dt_fim == null ? Carbon::today()->format('Y-m-d') : $request->dt_fim;

        $atendentes = DB::table('membro as m')
        ->leftJoin('cronograma as cro','m.id_cronograma', 'cro.id')
        ->leftJoin('grupo as gr', 'cro.id_grupo', 'gr.id')
        ->leftJoin('associado as ass', 'm.id_associado', 'ass.id')
        ->leftJoin('pessoas as p', 'ass.id_pessoa', 'p.id')
        ->where(function ($query) {
            $query->where('id_funcao', 5);
            $query->orWhere('id_funcao', 6);
        })
        ->where('id_tipo_grupo', 3)
        ->distinct('p.nome_completo')
        ->orderBy('p.nome_completo')
        ->select('m.id_associado', 'p.nome_completo')
        ->get();

        foreach($atendentes as $key => $atendente){
$diasAtendente = DB::table('atendente_dia')
            ->where('id_associado', $atendente->id_associado)
            ->get();
        $dados = [];
        foreach ($diasAtendente as $mKey => $diaAtendente) {
            foreach ($diasAtendente as $diaAtendenteCompare) {
                if (Carbon::parse($diaAtendente->dh_inicio)->format('Y-m-d') == Carbon::parse($diaAtendenteCompare->dh_inicio)->format('Y-m-d') and $diaAtendente->id != $diaAtendenteCompare->id) {
                    unset($diasAtendente[$mKey]);
                }
            }
        }
    
    

    
        // Pega um array com todas as reuniões que o atendente participa
        $cronogramasParticipa = DB::table('membro')
            ->where('id_associado', $atendente->id_associado)
            ->where(function ($query) {
                $query->where('id_funcao', 5);
                $query->orWhere('id_funcao', 6);
            })
            ->pluck('id_cronograma');

        //Pega datas como id do cronograma, e data das reuniões que aconteceram durante o período selecionado
        $cronogramaAFI = DB::table('dias_cronograma as dc')
            ->leftJoin('cronograma as cro', 'dc.id_cronograma', 'cro.id')
            ->leftJoin('grupo as gr', 'cro.id_grupo', 'gr.id')
            ->leftJoin('tipo_dia as td', 'cro.dia_semana', 'td.id')
            ->where('id_tipo_grupo', 3)
            ->where('dc.data', '>' , $dt_inicio)
            ->where('dc.data', '<' , $dt_fim)
            ->whereIn('cro.id', $cronogramasParticipa)
            ->select('cro.id', 'dc.data', 'gr.nome', 'cro.h_inicio', 'td.nome as dia')
            ->orderBy('dc.data')->get();

        //Confere se a data de uma reunião está presente na lista de distinct criaada acima, gerando um array completo, com o dado de presenca   
        foreach ($cronogramaAFI as $datas) {
            $i = 0;
            foreach ($diasAtendente as $diaAtendente) {
                if ($datas->data == Carbon::parse($diaAtendente->dh_inicio)->format('Y-m-d') and $diaAtendente->id_grupo == $datas->id) {
                    array_push($dados, ['id' => $datas->id, 'data' => $datas->data, 'nome' => $datas->nome, 'h_inicio' => $datas->h_inicio, 'dia' => $datas->dia, 'presenca' => 1]);
                    break;
                } elseif (++$i === count($diasAtendente)) {
                    array_push($dados, ['id' => $datas->id, 'data' => $datas->data, 'nome' => $datas->nome, 'h_inicio' => $datas->h_inicio, 'dia' => $datas->dia, 'presenca' => 0]);
                }
            }
        }

        //Gera numa variável a contagem total de cada item de presença
        $contaFaltas = array_count_values(array_column($dados, 'presenca'));
        $atendentes[$key]->presenca = $contaFaltas;
        }
        return view('relatorios.gerenciar-relatorio-afi', compact('atendentes', 'afiSelecionado', 'dt_inicio', 'dt_fim'));
    }


    public function visualizarAFI(Request $request)
    {

        // Devolve a data selecioanda, se não tiver nenhuma coloca uma padrão
        $dt_inicio = $request->dt_inicio == null ? (Carbon::now()->subMonth()->firstOfMonth()->format('Y-m-d')) : $request->dt_inicio;
        $dt_fim =  $request->dt_fim == null ? Carbon::today()->format('Y-m-d') : $request->dt_fim;
      //  dd($request->dt_inicio);

        // Faz um distinct caso o atendente venha duas vezes em um mesmo dia
        $diasAtendente = DB::table('atendente_dia')
            ->where('id_associado', $request->afi)// Colocar trava de data
            ->get();
        $dados = [];
        foreach ($diasAtendente as $mKey => $diaAtendente) {
            foreach ($diasAtendente as $diaAtendenteCompare) {
                if (Carbon::parse($diaAtendente->dh_inicio)->format('Y-m-d') == Carbon::parse($diaAtendenteCompare->dh_inicio)->format('Y-m-d') and $diaAtendente->id != $diaAtendenteCompare->id) {
                    unset($diasAtendente[$mKey]);
                }
            }
        }

        // Pega um array com todas as reuniões que o atendente participa
        $cronogramasParticipa = DB::table('membro')
            ->where('id_associado', $request->afi)
            ->where(function ($query) {
                $query->where('id_funcao', 5);
                $query->orWhere('id_funcao', 6);
            })
            ->pluck('id_cronograma');

        //Pega datas como id do cronograma, e data das reuniões que aconteceram durante o período selecionado
        $cronogramaAFI = DB::table('dias_cronograma as dc')
            ->leftJoin('cronograma as cro', 'dc.id_cronograma', 'cro.id')
            ->leftJoin('grupo as gr', 'cro.id_grupo', 'gr.id')
            ->leftJoin('tipo_dia as td', 'cro.dia_semana', 'td.id')
            ->where('id_tipo_grupo', 3)
            ->where('dc.data', '>' , $dt_inicio)
            ->where('dc.data', '<' , $dt_fim)
            ->whereIn('cro.id', $cronogramasParticipa)
            ->select('cro.id', 'dc.data', 'gr.nome', 'cro.h_inicio', 'td.nome as dia')
            ->orderBy('dc.data')->get();

        //Confere se a data de uma reunião está presente na lista de distinct criaada acima, gerando um array completo, com o dado de presenca   
        foreach ($cronogramaAFI as $datas) {
            $i = 0;
            foreach ($diasAtendente as $diaAtendente) {
                if ($datas->data == Carbon::parse($diaAtendente->dh_inicio)->format('Y-m-d') and $diaAtendente->id_grupo == $datas->id) {
                    array_push($dados, ['id' => $datas->id, 'data' => $datas->data, 'nome' => $datas->nome, 'h_inicio' => $datas->h_inicio, 'dia' => $datas->dia, 'presenca' => 1]);
                    break;
                } elseif (++$i === count($diasAtendente)) {
                    array_push($dados, ['id' => $datas->id, 'data' => $datas->data, 'nome' => $datas->nome, 'h_inicio' => $datas->h_inicio, 'dia' => $datas->dia, 'presenca' => 0]);
                }
            }
        }

        //Gera numa variável a contagem total de cada item de presença
        $contaFaltas = array_count_values(array_column($dados, 'presenca'));
        // dd($diasAtendente, $cronogramaAFI, $dados, $cronogramasParticipa, $contaFaltas);

        // Devolve todos os atendentes membros de uma reunião
        $atendentes = DB::table('membro as m')
            ->leftJoin('cronograma as cro','m.id_cronograma', 'cro.id')
            ->leftJoin('grupo as gr', 'cro.id_grupo', 'gr.id')
            ->leftJoin('associado as ass', 'm.id_associado', 'ass.id')
            ->leftJoin('pessoas as p', 'ass.id_pessoa', 'p.id')
            ->where(function ($query) {
                $query->where('id_funcao', 5);
                $query->orWhere('id_funcao', 6);
            })
            ->where('id_tipo_grupo', 3)
            ->distinct('p.nome_completo')
            ->orderBy('p.nome_completo')
            ->select('m.id_associado', 'p.nome_completo')
            ->get();

            //Devolve dados como nome do atendente selecionado na pesquisa
            $afiSelecionado = DB::table('membro as m')
            ->leftJoin('associado as ass', 'm.id_associado', 'ass.id')
            ->leftJoin('pessoas as p', 'ass.id_pessoa', 'p.id')
            ->where('m.id_associado', $request->afi)
            ->select('m.id_associado', 'p.nome_completo')
            ->first();


        return view('relatorios.visualizar-presenca-afi', compact('contaFaltas', 'dados', 'atendentes', 'afiSelecionado', 'dt_inicio', 'dt_fim'));
    }

    /**
     * Show the form for creating a new resource.
     */
  

    /**
     * Store a newly created resource in storage.
     */
    public function tematicas(Request $request)
    {
    
        return view('relatorios.tematicas');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
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