<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;



class GerenciarEstudosExternosController extends Controller
{
    public function index(Request $request)
    {
        $lista = DB::table('cursos_externos as ce')
            ->leftJoin('pessoas as p', 'ce.id_pessoa', 'p.id')
            ->leftJoin('tipo_tratamento as tt', 'ce.id_tipo_atividade', 'tt.id')
            ->leftJoin('instituicao as i', 'ce.instituicao', 'i.id')
            ->leftJoin('setor as s', 'ce.setor', 's.id')
            ->select(
                'p.nome_completo as nome_completo',
                's.sigla as setor_sigla',
                'i.nome_fantasia as instituicao_nome',
                'tt.sigla',
                'tt.descricao',
                'p.nome_completo',
                'id_tipo_atividade',
                'instituicao',
                'data_inicio',
                'data_fim',
                'ce.status',
                'documento_comprovante',
                'ce.setor',
                'ce.id',
                'tt.id_semestre',
            )
            ->get();


        return view('/estudos-externos/gerenciar-estudos-externos', compact('lista'));
    }
    public function create()
    {
        $setores = DB::table('setor')->select('id', 'nome', 'sigla')->whereNull('dt_fim')->get();
        $estudos = DB::table('tipo_tratamento')
            ->select('id', 'id_semestre', 'sigla')
            ->where('id_tipo_grupo', '2')
            ->get();
        $pessoas = DB::table('pessoas')->select('id', 'nome_completo')->orderBy('nome_completo')->get();
        $instituicoes = DB::table('instituicao')->select('id', 'nome_fantasia', 'razao_social')->get();

        return view('/estudos-externos/incluir-estudos-externos', compact('setores', 'estudos', 'pessoas', 'instituicoes'));
    }
    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            $setores = $request->input('setor');
            $pessoas = $request->input('pessoa');
            $instituicoes = $request->input('instituicao');
            $estudos = $request->input('estudo');
            $dataIncial = $request->input('dt_inicial');
            $datasFinais = $request->input('dt_final');
            $arquivos = $request->file('arquivo');

            // Validação básica
            if (!$setores || !$pessoas || !$instituicoes) {
                return back()->with('error', 'Dados obrigatórios ausentes.');
            }

            // Percorrer cada estudo enviado
            foreach ($instituicoes as $index => $instituicaoId) {
                DB::table('cursos_externos')->insert([
                    'setor' => $setores,
                    'id_pessoa' => $pessoas,
                    'instituicao' => $instituicoes[$index] ?? null,
                    'id_tipo_atividade' => $estudos[$index] ?? null,
                    'data_inicio' => $dataIncial[$index] ?? null,
                    'data_fim' => $datasFinais[$index] ?? null,
                    'documento_comprovante' => isset($arquivos[$index])
                        ? $arquivos[$index]->store('anexos_estudos', 'public')
                        : null,
                    'status' => 'Pendente'
                ]);
            }

            DB::commit();

            app('flasher')->addSuccess('Estudo externo adicionado com sucesso!');
            return redirect()->route('index.estExt');
        } catch (\Exception $e) {
            DB::rollBack();
            app('flasher')->addError("Erro ao salvar os estudos:" . $e->getMessage());
            return back()->withInput();
        }
    }
    public function edit($id)
    {
        $lista = DB::table('cursos_externos')->where('id', $id)->first();
        if (!$lista) {
            app('flasher')->addError("Estudo externo não encontrado.");
            return redirect()->route('index.estExt');
        }

        $setores = DB::table('setor')->select('id', 'nome', 'sigla')->whereNull('dt_fim')->get();
        $estudos = DB::table('tipo_tratamento')
            ->select('id', 'id_semestre', 'sigla')
            ->where('id_tipo_grupo', '2')
            ->get();
        $pessoas = DB::table('pessoas')->select('id', 'nome_completo')->orderBy('nome_completo')->get();
        $instituicoes = DB::table('instituicao')->select('id', 'nome_fantasia', 'razao_social')->get();

        return view('/estudos-externos/editar-estudos-externos', compact(
            'setores',
            'estudos',
            'pessoas',
            'instituicoes',
            'lista'
        ));
    }
    public function update(Request $request, $id)
    {
        DB::beginTransaction();

        try {
            // Validação básica
            if (!$request->setor || !$request->pessoa || !$request->instituicao) {
                return back()->with('error', 'Dados obrigatórios ausentes.');
            }

            // Buscar o registro existente
            $registro = DB::table('cursos_externos')->where('id', $id)->first();
            if (!$registro) {
                app('flasher')->addError("Estudo externo não encontrado.");
                return redirect()->route('index.estExt');
            }

            // Monta os dados para atualizar
            $dados = [
                'setor' => $request->setor,
                'id_pessoa' => $request->pessoa,
                'instituicao' => $request->instituicao,
                'id_tipo_atividade' => $request->estudo,
                'data_inicio' => $request->dt_inicial,
                'data_fim' => $request->dt_final,
                'status' => 'Pendente'
            ];

            // Upload do arquivo (se houver)
            if ($request->hasFile('arquivo')) {
                // Apaga o arquivo antigo, se existir
                if (!empty($registro->documento_comprovante) && Storage::disk('public')->exists($registro->documento_comprovante)) {
                    Storage::disk('public')->delete($registro->documento_comprovante);
                }

                // Salva o novo arquivo
                $dados['documento_comprovante'] = $request->file('arquivo')->store('anexos_estudos', 'public');
            }

            // Atualiza o registro
            DB::table('cursos_externos')->where('id', $id)->update($dados);

            DB::commit();

            app('flasher')->addSuccess('Estudo externo atualizado com sucesso!');
            return redirect()->route('index.estExt');
        } catch (\Exception $e) {
            DB::rollBack();
            app('flasher')->addError("Erro ao atualizar o estudo: " . $e->getMessage());
            return back()->withInput();
        }
    }
    public function destroy($id)
    {
        try {
            // Verifica se o estudo existe
            $estudo = DB::table('cursos_externos')->where('id', $id)->first();
            if (!$estudo) {
                app('flasher')->addError("Estudo externo não encontrado.");
                return redirect()->route('index.estExt');
            }

            // Deleta o estudo
            DB::table('cursos_externos')->where('id', $id)->delete();

            app('flasher')->addSuccess("Estudo externo excluído com sucesso!");
            return redirect()->route('index.estExt');
        } catch (\Exception $e) {
            return redirect()->route('index.estExt')->with('error', 'Erro ao excluir o estudo externo: ' . $e->getMessage());
        }
    }
}
