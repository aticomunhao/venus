<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class GerenciarDirigentesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $dirigentes = DB::table('membro as mem')
        ->select('ass.id_pessoa', 'gr.nome', 'gr.id')
        ->leftJoin('associado as ass', 'mem.id_associado', 'ass.id')
        ->leftJoin('grupo as gr', 'mem.id_grupo', 'gr.id')
        ->leftJoin('cronograma as cr', 'gr.id', 'cr.id_grupo')
        ->where('ass.id_pessoa', session()->get('usuario.id_pessoa'))
        ->where('id_funcao', 1)
        ->where('cr.id_tipo_tratamento', 2)
        ->where('cr.status_reuniao', '<>', 2)
        ->distinct('gr.id')
        ->get();

        $encaminhamentos = DB::table('tratamento as tr')
        ->select('tr.id','p.nome_completo', 'cro.h_inicio', 'cro.h_fim', 'gr.nome')
        ->leftJoin('encaminhamento as enc', 'tr.id_encaminhamento', 'enc.id')
        ->leftJoin('cronograma as cro', 'tr.id_reuniao', 'cro.id')
        ->leftJoin('grupo as gr', 'cro.id_grupo', 'gr.id')
        ->leftJoin('atendimentos as atd', 'enc.id_atendimento', 'atd.id')
        ->leftJoin('pessoas as p','atd.id_assistido', 'p.id')
        ->where('enc.id_tipo_tratamento', 2)
        ->where('tr.status', 2)
        ->get();

        return view('dirigentes.gerenciar-dirigente', compact('encaminhamentos'));
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
