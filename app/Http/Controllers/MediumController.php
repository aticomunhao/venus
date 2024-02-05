<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Carbon;
use function Laravel\Prompts\select;

class MediumController extends Controller
{



    public function index(Request $request)
    {
        $mediumQuery = DB::table('medium AS m')
            ->leftJoin('pessoas AS p', 'm.id_pessoa', '=', 'p.id')
            ->leftJoin('tipo_funcao AS tf', 'm.id_funcao', '=', 'tf.id')
            ->leftJoin('setor AS s', 'm.id_setor', '=', 's.id')
            ->leftJoin('grupo AS g', 'm.id_grupo', '=', 'g.id')
            ->select(
                'p.nome_completo',
                'm.id AS idm',
                'm.id_pessoa',
                'm.id_funcao',
                'p.cpf',
                'p.status',
                'p.motivo_status',
                'tf.nome as nome_funcao',
                'm.id_grupo',
                's.nome as nome_setor',
                'g.nome as nome_grupo'
            )
            ->orderBy('p.nome_completo', 'ASC');

        $nome = $request->nome_pesquisa;
        $cpf = $request->cpf_pesquisa;
        $grupoPesquisa = $request->grupo_pesquisa;
        $setorPesquisa = $request->setor_pesquisa;

        $grupos = DB::table('grupo')->pluck('nome', 'id');
        $setores = DB::table('setor')->pluck('nome', 'id');

        if ($nome || $cpf || $grupoPesquisa || $setorPesquisa) {
            $mediumQuery->where(function ($query) use ($nome, $cpf, $grupoPesquisa, $setorPesquisa) {
                if ($nome) {
                    $query->where('p.nome_completo', 'ilike', "%$nome%")
                        ->orWhere('p.cpf', 'ilike', "%$nome%");
                }

                if ($cpf) {
                    $query->orWhere('p.cpf', 'ilike', "%$cpf%");
                }

                if ($grupoPesquisa) {
                    $query->orWhere('g.id', '=', $grupoPesquisa);
                }

                if ($setorPesquisa) {
                    $query->orWhere('s.id', '=', $setorPesquisa);
                }
            });
        }

        $medium = $mediumQuery->orderBy('p.status', 'asc')
            ->orderBy('p.nome_completo', 'asc')
            ->paginate(50);

        return view('medium.gerenciar-mediuns', compact('grupos', 'setores', 'nome', 'cpf', 'grupoPesquisa', 'setorPesquisa', 'medium'));
    }




    public function create()
    {
        $id_medium = 1;
        $grupo = DB::select('select id, nome from grupo');
        $medium = DB::select('select * from medium');
        $tipo_mediunidade = DB::select('select id, tipo from tipo_mediunidade');
        $pessoas = DB::select('select id as idp, nome_completo, motivo_status, status from pessoas');
        $tipo_funcao = DB::select('select id as idf, tipo_funcao, nome, sigla from tipo_funcao');
        $setor = DB::select('select id as ids, nome from setor');
        $mediunidade_medium = DB::select('select id as idme, data_inicio from mediunidade_medium');
        $tipo_status_pessoa = DB::select('select id,tipo as tipos from tipo_status_pessoa');


        return view('medium/criar-mediuns', compact('tipo_status_pessoa', 'grupo', 'id_medium', 'medium', 'tipo_mediunidade', 'pessoas', 'tipo_funcao', 'setor', 'mediunidade_medium'));
    }



    public function store(Request $request)
    {

        $id_pessoa = $request->input('id_pessoa');
        $id_setor = $request->input('id_setor');
        $id_funcao = $request->input('id_funcao');
        $tipo_ids = $request->input('id_tp_mediunidade');
        $id_grupo = $request->input('id_grupo');

        $existingMedium = DB::table('medium')
            ->where('id_pessoa', $id_pessoa)
            ->first();


        $isValidPerson = DB::table('pessoas')->where('id', $id_pessoa)->exists();

        if ($existingMedium || !$isValidPerson) {

            if ($existingMedium) {
                app('flasher')->addError("Medium já cadastrado");
            }
            if (!$isValidPerson) {
                app('flasher')->addError("Nome de pessoa inválido");
            }
            return redirect()->back();
        }


        $mediumId = DB::table('medium')->insertGetId([
            'id_pessoa' => $id_pessoa,
            'id_setor' => $id_setor,
            'id_funcao' => $id_funcao,
            'id_grupo' => $id_grupo,
        ]);


        foreach ($tipo_ids as $tipo_id) {
            $datas_inicio = $request->input("data_inicio.{$tipo_id}");

            foreach ($datas_inicio as $data_inicio) {
                DB::table('mediunidade_medium')->insert([
                    'id_medium' => $mediumId,
                    'id_mediunidade' => $tipo_id,
                    'data_inicio' => $data_inicio ? date('Y-m-d', strtotime($data_inicio)) : null,
                ]);
            }
        }

        app('flasher')->addSuccess("Cadastrado com Sucesso");
        return redirect('gerenciar-mediuns');
    }






    public function edit($id)
    {
        $medium = DB::table('medium AS m')
            ->leftJoin('pessoas AS p', 'm.id_pessoa', '=', 'p.id')
            ->leftJoin('tipo_funcao AS tf', 'm.id_funcao', '=', 'tf.id')
            ->leftJoin('grupo AS g', 'm.id_grupo', '=', 'g.id')
            ->leftJoin('setor AS s', 'm.id_setor', '=', 's.id')
            ->leftJoin('mediunidade_medium AS mm', 'm.id', '=', 'mm.id_medium')
            ->select('p.nome_completo', 'm.id AS idm', 'p.id AS id_pessoa', 'p.motivo_status', 'p.status', 'm.id_setor', 'm.id_grupo', 'g.nome AS nome_grupo', 'tf.nome AS nome_funcao', 'm.id_funcao', 's.nome AS nome_setor', 'mm.id_mediunidade', 'mm.data_inicio')
            ->where('m.id', $id)
            ->first();

        $tipo_status_pessoa = DB::table('tipo_status_pessoa')->select('id', 'tipo as tipos')->get();
        $tipo_motivo_status_pessoa = DB::table('tipo_motivo_status_pessoa')->select('id', 'motivo')->get();
        $grupo = DB::table('grupo')->get();
        $pessoas = DB::table('pessoas')->get();
        $tipo_mediunidade = DB::table('tipo_mediunidade')->get();
        $tipo_funcao = DB::table('tipo_funcao')->get();
        $setor = DB::table('setor')->get();
        $mediunidade_medium = DB::table('mediunidade_medium')->select('id_mediunidade', 'data_inicio', 'id_medium as id_mediuns')->where('id_medium', $medium->idm)->get();


        $createdMediunidades = DB::table('mediunidade_medium')
            ->where('id_medium', $medium->idm)
            ->get();


        $createdMediunidadeIds = [];
        $createdMediunidadeData = [];


        foreach ($createdMediunidades as $createdMediunidade) {
            $createdMediunidadeIds[] = $createdMediunidade->id_mediunidade;
            $createdMediunidadeData[$createdMediunidade->id_mediunidade] = $createdMediunidade->data_inicio;
        }



        return view('medium.editar-mediuns', compact('tipo_status_pessoa', 'tipo_motivo_status_pessoa', 'grupo', 'createdMediunidadeData', 'createdMediunidadeIds', 'tipo_mediunidade', 'mediunidade_medium', 'medium', 'pessoas', 'tipo_funcao', 'setor', 'tipo_mediunidade'));
    }



    public function update(Request $request, string $id)
    {
        $input = $request->all();

        $dataManifestacao = $input['datas_manifestou'] ?? [];
        $tiposMediunidade = $input['mediunidades'] ?? [];


        DB::table('mediunidade_medium')->where('id_medium', $id)->delete();


        foreach ($tiposMediunidade as $tipo) {

            DB::table('mediunidade_medium')->insert([
                'id_medium' => $id,
                'id_mediunidade' => $tipo,
                'data_inicio' => isset($dataManifestacao[$tipo]) ? date('Y-m-d', strtotime($dataManifestacao[$tipo])) : null,
            ]);
        }


        $dataToUpdate = [
            'id_pessoa' => $input['id_pessoa'],
            'id_funcao' => $input['id_funcao'],
            'id_setor' => $input['setor'],
            'id_grupo' => $input['id_grupo'],
        ];
        DB::table('medium')->where('id', $id)->update($dataToUpdate);


        $dataToUpdatePessoas = [
            'status' => $input['status'] ?? null,
            'motivo_status' => $input['motivo_status'] ?? null,
        ];

        DB::table('pessoas')->where('id', $input['id_pessoa'])->update($dataToUpdatePessoas);

        app('flasher')->addSuccess("Alterado com Sucesso");

        return redirect('gerenciar-mediuns');
    }





    public function show($id)
    {

        $medium = DB::table('medium AS m')
            ->leftJoin('pessoas AS p', 'm.id_pessoa', '=', 'p.id')
            ->leftJoin('tipo_funcao AS tf', 'm.id_funcao', '=', 'tf.id')
            ->leftJoin('grupo AS g', 'm.id_grupo', '=', 'g.id')
            ->leftJoin('setor AS s', 'm.id_setor', '=', 's.id')
            ->leftJoin('mediunidade_medium AS mm', 'm.id', '=', 'mm.id_medium')
            ->select('p.nome_completo', 'm.id AS idm', 'm.id_pessoa', 'p.status', 'm.id_setor', 'p.motivo_status', 'm.id_grupo', 'g.nome AS nome_grupo', 'tf.nome AS nome_funcao', 'm.id_funcao', 's.nome AS nome_setor', 'mm.id_mediunidade', 'mm.data_inicio')
            ->where('m.id', $id)
            ->first();


        $tipo_motivo_status_pessoa = DB::select('select id,motivo  from tipo_motivo_status_pessoa');
        $tipo_status_pessoa = DB::select('select id,tipo as tipos from tipo_status_pessoa');
        $grupo = DB::table('grupo')->get();
        $pessoas = DB::table('pessoas')->get();
        $tipo_mediunidade = DB::table('tipo_mediunidade')->get();
        $tipo_funcao = DB::table('tipo_funcao')->get();
        $setor = DB::table('setor')->get();
        $mediunidade_medium = DB::table('mediunidade_medium')->select('id_mediunidade', 'data_inicio', 'id_medium as id_mediuns')->where('id_medium', $id)->get();



        return view('medium.visualizar-mediuns', compact('tipo_motivo_status_pessoa', 'tipo_status_pessoa', 'grupo', 'tipo_mediunidade', 'medium', 'pessoas', 'tipo_funcao', 'setor', 'mediunidade_medium'));
    }


    public function destroy(string $id)
    {

        $data = date("Y-m-d H:i:s");

        DB::table('historico_venus')->insert([

            'id_usuario' => session()->get('usuario.id_usuario'),
            'data' => $data,
            'fato' => 0,
            'obs' => $id

        ]);

        $medium = DB::table('medium')->where('id', $id)->first();


        if (!$medium) {
            app('flasher')->addError('A medium não foi encontrada.');
            return redirect('/gerenciar-mediuns');
        }
        DB::table('mediunidade_medium')->where('id_medium', $id)->delete();

        DB::table('medium')->where('id', $id)->delete();


        app('flasher')->addError('Excluído com sucesso.');
        return redirect('/gerenciar-mediuns');
    }
}
