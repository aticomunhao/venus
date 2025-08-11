<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;


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
            )
            ->get();


        return view('/estudos-externos/gerenciar-estudos-externos', compact('lista'));
    }
    public function create()
    {
        $setores = DB::table('setor')->select('id', 'nome', 'sigla')->whereNull('dt_fim')->get();
        $estudos = DB::table('tipo_tratamento')->select('id', 'id_semestre', 'sigla')->where('id_tipo_grupo', '2')->get();
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
                    'instituicao' => $instituicaoId[$index] ?? null,
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

            return redirect()->route('index.estExt')->with('success', 'Estudo externo adicionado com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();
            app('flasher')->addError("Erro ao salvar os estudos:" . $e->getMessage());
            return back()->withInput();
        }
    }
}
