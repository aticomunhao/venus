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
    public function presencaAFI(Request $request)
    {

        $idAssociado = $request->afi;
        $dt_inicio = $request->dt_inicio == null ? (Carbon::now()->subMonth()->firstOfMonth()->format('Y-m-d')) : $request->dt_inicio;
        $dt_fim =  $request->dt_fim == null ? Carbon::today()->format('Y-m-d') : $request->dt_fim;
      //  dd($request->dt_inicio);


        $diasAtendente = DB::table('atendente_dia')
            ->where('id_associado', $request->afi)
            ->get();
        $dados = [];
        foreach ($diasAtendente as $mKey => $diaAtendente) {
            foreach ($diasAtendente as $diaAtendenteCompare) {
                if (Carbon::parse($diaAtendente->dh_inicio)->format('Y-m-d') == Carbon::parse($diaAtendenteCompare->dh_inicio)->format('Y-m-d') and $diaAtendente->id != $diaAtendenteCompare->id) {
                    unset($diasAtendente[$mKey]);
                }
            }
        }

        $cronogramasParticipa = DB::table('membro')
            ->where('id_associado', $request->afi)
            ->where(function ($query) {
                $query->where('id_funcao', 5);
                $query->orWhere('id_funcao', 6);
            })
            ->pluck('id_cronograma');

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

        $contaFaltas = array_count_values(array_column($dados, 'presenca'));
        // dd($diasAtendente, $cronogramaAFI, $dados, $cronogramasParticipa, $contaFaltas);

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

            $afiSelecionado = DB::table('membro as m')
            ->leftJoin('associado as ass', 'm.id_associado', 'ass.id')
            ->leftJoin('pessoas as p', 'ass.id_pessoa', 'p.id')
            ->where('m.id_associado', $request->afi)
            ->select('m.id_associado', 'p.nome_completo')
            ->first();


        return view('relatorios.relatorio', compact('contaFaltas', 'dados', 'atendentes', 'afiSelecionado', 'idAssociado', 'dt_inicio', 'dt_fim'));
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
