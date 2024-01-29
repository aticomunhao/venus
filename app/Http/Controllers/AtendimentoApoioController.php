<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class AtendimentoApoioController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $pesquisaNome = $request->input('nome');
        $pesquisaCpf = $request->input('cpf');




            $atendente = DB::table('atendente_apoio AS at')
                ->select('at.id', 'atd.dh_inicio', 'atd.dh_fim', 'p.nome_completo', 'p.cpf', 'tp.tipo')
                ->leftJoin('pessoas AS p', 'at.id_pessoa', '=', 'p.id')
                ->leftJoin('tipo_status_pessoa AS tp', 'p.status', '=', 'tp.id')
                ->leftJoin('atendente_apoio_dia as atd', 'at.id', '=', 'atd.id_atendente')
                ->get();


        $conta = $atendente->count();

        return view('/atendentes-apoio/gerenciar-atendente-apoio', compact('atendente', 'conta'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $nomes = DB::table('pessoas')
            ->where('status', '=', '1')
            ->get();

        $dias = DB::table('tipo_dia')->get();

        return view('/atendentes-apoio/incluir-atendente-apoio', compact('nomes', 'dias'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $req = $request->all();
        $dataHoje = Carbon::today()->toDateString();

dd($req);
        $idAtendente = DB::table('atendente_apoio')->insertGetId([
            'id_pessoa' => $request->input('nome'),
        ]);

        foreach($req['checkbox'] as $checked){
            DB::table('atendente_apoio_dia')
            ->insert([
                'id_atendente' => $idAtendente,
                'id_dia' => $checked->id,

            ]);



            }







        DB::table('historico_atendente_apoio')->insert([
            'id_atendente_apoio' => $idAtendente,
            'dt_inicio' => $dataHoje,
            'dh_inicio' => $request->input('dhInicio'),
            'dh_fim' => $request->input('dhFinal'),
        ]);

        return redirect()->route('indexAtendenteApoio');
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
        return view('atendentes-apoio/editar-atendente-apoio');
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
