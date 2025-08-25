<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GerenciarTipoCriterioController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tipos_criterio = DB::table('tipos_criterios')->get();
        return view('tipo-criterio.index', compact('tipos_criterio'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $result = DB::select("SELECT unnest(enum_range(NULL::tipo_valor_enum)) AS valor");
        $tipo_valores = collect($result)->pluck('valor')->toArray();

        return view('tipo-criterio.create', compact('tipo_valores'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validação dos dados
        $request->validate([
            'nome' => 'required|string|max:255',
            'tipo_criterio' => 'required|in:numero,texto,data,boolean',
        ]);

        // Inserção usando Query Builder
        DB::table('tipos_criterios')->insert([
            'descricao' => $request->input('nome'),
            'tipo_valor' => $request->input('tipo_criterio'),
            'status' => true,
        ]);

        return redirect()->route('index.tipo_criterio_controller')->with('success', 'Critério criado com sucesso!');
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
