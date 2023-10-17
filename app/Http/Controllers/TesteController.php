<?php

namespace App\Http\Controllers;

use App\Models\Atendente;
use App\Models\Pessoa;
use App\Models\Teste;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class TesteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $atendente = Atendente::find(1)->pessoa->nome_completo;
        $pessoa = Pessoa::find(6)->atendende; // null?

        return view('tester', compact('atendente', 'pessoa'));
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
    public function show(Teste $teste)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Teste $teste)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Teste $teste)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Teste $teste)
    {
        //
    }

    public function referenceCodes(){

        $Atendente = Pessoa::find(1)->atendente;

        // Metodo Query - Resgatando todas as Rows de "pessoas"
         $pessoas = DB::select("SELECT * from pessoas");

        // Metodo Query - Regatando dados tabela Atendente relacionamento Pessoa OneToOne

            $atendentes = DB::select("SELECT
            p.nome_completo,
            g.nome as nome_grupo,
            a.status_atendente

            FROM atendentes a
            LEFT JOIN pessoas p ON a.id_pessoa = p.id
            LEFT JOIN grupos g on a.id_grupo = g.id

            ");
    }
}
