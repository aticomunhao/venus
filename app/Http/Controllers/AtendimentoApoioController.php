<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AtendimentoApoioController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {


        $atendente = DB::table('atendente_apoio AS at')
        ->select('at.dh_inicio', 'at.dh_fim', 'p.nome_completo', 'p.cpf', 'tp.tipo')
        ->leftJoin('pessoas AS p', 'at.id_pessoa', '=', 'p.id')
        ->leftJoin('tipo_status_pessoa AS tp', 'p.status', '=', 'tp.id')->get();

        return view('/atendentes-apoio/gerenciar_atendente_apoio', compact('atendente'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
       $nomes = DB::table('pessoas')->get();

        return view('/atendentes-apoio/incluir_atendente_apoio', compact('nomes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        DB::table('atendente_apoio')->insert([
            'id_pessoa' => $request->input('nome'),
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
