<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

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
            ->leftJoin('tipo_semestre', 'tipo_tratamento.id_semestre', 'tipo_semestre.id')
            ->select('tipo_tratamento.id as id', 'id_semestre', 'tipo_tratamento.sigla as sigla', 'tipo_semestre.sigla as semestre_sigla', 'tipo_tratamento.descricao as descricao')
            ->where('id_tipo_grupo', '2')
            ->orderBy('descricao', 'asc')
            ->orderBy('id_semestre', 'asc')
            ->get();

        return view('questoes.incluir-questoes', compact('tipoAtividade'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $perguntas = $request->input('perguntas');

        DB::beginTransaction();
        try {
            foreach ($perguntas as $pergunta) {
                // Salva a pergunta
                $perguntaId = DB::table('perguntas')->insertGetId([
                    'enunciado' => $pergunta['enunciado'] ?? '',
                ]);

                // Salva as respostas
                foreach ($pergunta['respostas'] as $index => $resposta) {
                    DB::table('respostas')->insert([
                        'pergunta_id' => $perguntaId,
                        'resposta' => $resposta,
                        'correta' => (isset($pergunta['correta']) && $pergunta['correta'] == $index) ? 1 : 0,
                    ]);
                }
            }

            DB::commit();
            return redirect()->back()->with('success', 'Perguntas e respostas salvas com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Erro ao salvar: ' . $e->getMessage());
        }
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
