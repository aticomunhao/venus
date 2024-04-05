<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Carbon;
use function Laravel\Prompts\select;

class membroController extends Controller
{



    public function index(Request $request)
    {
        $membroQuery = DB::table('membro AS m')
            ->leftJoin('pessoas AS p', 'm.id_pessoa', '=', 'p.id')
            ->leftJoin('tipo_funcao AS tf', 'm.id_funcao', '=', 'tf.id')
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
                'g.nome as nome_grupo'
            )
            ->orderBy('p.nome_completo', 'ASC');

        $nome = $request->nome_pesquisa;
        $cpf = $request->cpf_pesquisa;
        $grupoPesquisa = $request->grupo_pesquisa;
        
        $grupos = DB::table('grupo')->pluck('nome', 'id');
        

        if ($nome || $cpf || $grupoPesquisa) {
            $membroQuery->where(function ($query) use ($nome, $cpf, $grupoPesquisa) {
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

            });
        }

        $membro = $membroQuery->orderBy('p.status', 'asc')
            ->orderBy('p.nome_completo', 'asc')
            ->paginate(50);

        return view('membro.gerenciar-membro', compact('grupos', 'nome', 'cpf', 'grupoPesquisa','membro'));
    }




    public function create()
    {
        $id_membro = 1;
        $grupo = DB::select('select id, nome from grupo');
        $membro = DB::select('select * from membro');
        $tipo_mediunidade = DB::select('select id, tipo from tipo_mediunidade');
        $pessoas = DB::select('select id as idp, nome_completo, motivo_status, status from pessoas');
        $tipo_funcao = DB::select('select id as idf, tipo_funcao, nome, sigla from tipo_funcao');
        $mediunidade_membro = DB::select('select id as idme, data_inicio from mediunidade_membro');
        $tipo_status_pessoa = DB::select('select id,tipo as tipos from tipo_status_pessoa');


        return view('membro/criar-membro', compact('tipo_status_pessoa', 'grupo', 'id_membro', 'membro', 'tipo_mediunidade', 'pessoas', 'tipo_funcao', 'mediunidade_membro'));
    }



    public function store(Request $request)
    {

        $id_pessoa = $request->input('id_pessoa');
        $id_funcao = $request->input('id_funcao');
        $tipo_ids = $request->input('id_tp_mediunidade');
        $id_grupo = $request->input('id_grupo');

        $existingmembro = DB::table('membro')
            ->where('id_pessoa', $id_pessoa)
            ->first();


        $isValidPerson = DB::table('pessoas')->where('id', $id_pessoa)->exists();

        if ($existingmembro || !$isValidPerson) {

            if ($existingmembro) {
                app('flasher')->addError("membro já cadastrado");
            }
            if (!$isValidPerson) {
                app('flasher')->addError("Nome de pessoa inválido");
            }
            return redirect()->back();
        }


        $membroId = DB::table('membro')->insertGetId([
            'id_pessoa' => $id_pessoa,
            'id_funcao' => $id_funcao,
            'id_grupo' => $id_grupo,
        ]);


        foreach ($tipo_ids as $tipo_id) {
            $datas_inicio = $request->input("data_inicio.{$tipo_id}");

            foreach ($datas_inicio as $data_inicio) {
                DB::table('mediunidade_membro')->insert([
                    'id_membro' => $membroId,
                    'id_mediunidade' => $tipo_id,
                    'data_inicio' => $data_inicio ? date('Y-m-d', strtotime($data_inicio)) : null,
                ]);
            }
        }

        app('flasher')->addSuccess("Cadastrado com Sucesso");
        return redirect('gerenciar-membro');
    }






    public function edit($id)
    {
        $membro = DB::table('membro AS m')
            ->leftJoin('pessoas AS p', 'm.id_pessoa', '=', 'p.id')
            ->leftJoin('tipo_funcao AS tf', 'm.id_funcao', '=', 'tf.id')
            ->leftJoin('grupo AS g', 'm.id_grupo', '=', 'g.id')
            ->leftJoin('mediunidade_membro AS mm', 'm.id', '=', 'mm.id_membro')
            ->select('p.nome_completo', 'm.id AS idm', 'p.id AS id_pessoa', 'p.motivo_status', 'p.status', 'm.id_grupo', 'g.nome AS nome_grupo', 'tf.nome AS nome_funcao', 'm.id_funcao', 'mm.id_mediunidade', 'mm.data_inicio')
            ->where('m.id', $id)
            ->first();

        $tipo_status_pessoa = DB::table('tipo_status_pessoa')->select('id', 'tipo as tipos')->get();
        $tipo_motivo_status_pessoa = DB::table('tipo_motivo_status_pessoa')->select('id', 'motivo')->get();
        $grupo = DB::table('grupo')->get();
        $pessoas = DB::table('pessoas')->get();
        $tipo_mediunidade = DB::table('tipo_mediunidade')->get();
        $tipo_funcao = DB::table('tipo_funcao')->get();
        $mediunidade_membro = DB::table('mediunidade_membro')->select('id_mediunidade', 'data_inicio', 'id_membro as id_membro')->where('id_membro', $membro->idm)->get();


        $createdMediunidades = DB::table('mediunidade_membro')
            ->where('id_membro', $membro->idm)
            ->get();


        $createdMediunidadeIds = [];
        $createdMediunidadeData = [];


        foreach ($createdMediunidades as $createdMediunidade) {
            $createdMediunidadeIds[] = $createdMediunidade->id_mediunidade;
            $createdMediunidadeData[$createdMediunidade->id_mediunidade] = $createdMediunidade->data_inicio;
        }



        return view('membro.editar-membro', compact('tipo_status_pessoa', 'tipo_motivo_status_pessoa', 'grupo', 'createdMediunidadeData', 'createdMediunidadeIds', 'tipo_mediunidade', 'mediunidade_membro', 'membro', 'pessoas', 'tipo_funcao', 'tipo_mediunidade'));
    }



    public function update(Request $request, string $id)
    {
        $input = $request->all();

        $dataManifestacao = $input['datas_manifestou'] ?? [];
        $tiposMediunidade = $input['mediunidades'] ?? [];


        DB::table('mediunidade_membro')->where('id_membro', $id)->delete();


        foreach ($tiposMediunidade as $tipo) {

            DB::table('mediunidade_membro')->insert([
                'id_membro' => $id,
                'id_mediunidade' => $tipo,
                'data_inicio' => isset($dataManifestacao[$tipo]) ? date('Y-m-d', strtotime($dataManifestacao[$tipo])) : null,
            ]);
        }


        $dataToUpdate = [
            'id_pessoa' => $input['id_pessoa'],
            'id_funcao' => $input['id_funcao'],
            'id_grupo' => $input['id_grupo'],
        ];
        DB::table('membro')->where('id', $id)->update($dataToUpdate);


        $dataToUpdatePessoas = [
            'status' => $input['status'] ?? null,
            'motivo_status' => $input['motivo_status'] ?? null,
        ];

        DB::table('pessoas')->where('id', $input['id_pessoa'])->update($dataToUpdatePessoas);

        app('flasher')->addSuccess("Alterado com Sucesso");

        return redirect('gerenciar-membro');
    }





    public function show($id)
    {

        $membro = DB::table('membro AS m')
            ->leftJoin('pessoas AS p', 'm.id_pessoa', '=', 'p.id')
            ->leftJoin('tipo_funcao AS tf', 'm.id_funcao', '=', 'tf.id')
            ->leftJoin('grupo AS g', 'm.id_grupo', '=', 'g.id')
            ->leftJoin('mediunidade_membro AS mm', 'm.id', '=', 'mm.id_membro')
            ->select('p.nome_completo', 'm.id AS idm', 'm.id_pessoa', 'p.status', 'p.motivo_status', 'm.id_grupo', 'g.nome AS nome_grupo', 'tf.nome AS nome_funcao', 'm.id_funcao', 'mm.id_mediunidade', 'mm.data_inicio')
            ->where('m.id', $id)
            ->first();


        $tipo_motivo_status_pessoa = DB::select('select id,motivo  from tipo_motivo_status_pessoa');
        $tipo_status_pessoa = DB::select('select id,tipo as tipos from tipo_status_pessoa');
        $grupo = DB::table('grupo')->get();
        $pessoas = DB::table('pessoas')->get();
        $tipo_mediunidade = DB::table('tipo_mediunidade')->get();
        $tipo_funcao = DB::table('tipo_funcao')->get();
        $mediunidade_membro = DB::table('mediunidade_membro')->select('id_mediunidade', 'data_inicio', 'id_membro as id_membro')->where('id_membro', $id)->get();



        return view('membro.visualizar-membro', compact('tipo_motivo_status_pessoa', 'tipo_status_pessoa', 'grupo', 'tipo_mediunidade', 'membro', 'pessoas', 'tipo_funcao', 'mediunidade_membro'));
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

        $membro = DB::table('membro')->where('id', $id)->first();


        if (!$membro) {
            app('flasher')->addError('A membro não foi encontrada.');
            return redirect('/gerenciar-membro');
        }
        DB::table('mediunidade_membro')->where('id_membro', $id)->delete();

        DB::table('membro')->where('id', $id)->delete();


        app('flasher')->addError('Excluído com sucesso.');
        return redirect('/gerenciar-membro');
    }
}
