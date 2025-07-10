<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GerenciarQuestoesControler extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        
        return view('questoes.gerenciar-questoes');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {

        $tipoAtividade = DB::table('tipo_tratamento')
       // ->where('id_tipo_grupo', 2)
        ->get();

        return view('questoes.incluir-questoes', compact('tipoAtividade'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        dd($request->all());
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
