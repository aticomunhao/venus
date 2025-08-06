<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GerenciarTipoCriterioController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $tipos_criterio = DB::table('tipos_criterios')->get();
        $result = DB::select("SELECT unnest(enum_range(NULL::tipo_valor_enum)) AS valor");
        $tipo_valores = collect($result)->pluck('valor')->toArray();

        $pesquisa_search = $request->input('search');
        if ($pesquisa_search) {
            $tipos_criterio = DB::table('tipos_criterios')
                ->where('descricao', 'ILIKE', '%' . $pesquisa_search . '%')
                ->get();
        }
        $pesquisa_tipo_criterio = $request->input('tipo_criterio');
        if ($pesquisa_tipo_criterio) {
            $tipos_criterio = DB::table('tipos_criterios')
                ->where('tipo_valor', $pesquisa_tipo_criterio)
                ->get();
        }

        // Ordenar os resultados por descrição
        $tipos_criterio = $tipos_criterio->sortBy('descricao');

        return view('tipo-criterio.index', compact('tipos_criterio', 'tipo_valores', 'pesquisa_search', 'pesquisa_tipo_criterio'));
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
        $tipos_criterio = DB::table('tipos_criterios')->where('id', $id)->first();
        $result = DB::select("SELECT unnest(enum_range(NULL::tipo_valor_enum)) AS valor");
        $tipo_valores = collect($result)->pluck('valor')->toArray();

        return view('tipo-criterio.edit', compact('tipos_criterio', 'tipo_valores'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'tipo_criterio' => 'required|string'
        ]);
        DB::table('tipos_criterios')
            ->where('id', $id)
            ->update([
                'descricao' => $request->nome,
                'tipo_valor' => $request->tipo_criterio,
                'status' => true
            ]);

        return redirect()->route('index.tipo_criterio_controller')
            ->with('success', 'Critério atualizado com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        DB::table('tipos_criterios')->where('id', $id)->update(['status' => false]);
        return redirect()->route('index.tipo_criterio_controller')
            ->with('warning', 'Critério inativado com sucesso!');
    }
}
