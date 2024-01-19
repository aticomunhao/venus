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

        $medium = DB::table('medium AS m')
            ->leftJoin('pessoas AS p', 'm.id_pessoa', '=', 'p.id')
            ->leftJoin('tipo_funcao AS tf', 'm.id_funcao', '=', 'tf.id')
            ->leftJoin('setor AS s', 'm.id_setor', '=', 's.id')
            ->leftJoin('grupo AS g', 'm.id_grupo', '=', 'g.id')
            ->select('p.nome_completo', 'm.id AS idm', 'm.id_pessoa', 'm.id_funcao', 'p.cpf', 'p.status', 'p.motivo_status', 'tf.nome as nome_funcao', 'm.id_grupo', 's.nome as nome_setor', 'g.nome as nome_grupo')
            ->orderBy('p.nome_completo', 'ASC');



        $nome = $request->nome_pesquisa;
        if ($nome) {
            $medium->where('p.nome_completo', 'ilike', "%$nome%")
                ->orWhere('p.cpf', 'like', "%$nome%");
        }

        $medium = $medium->orderBy('p.status', 'asc')
            ->orderBy('p.nome_completo', 'asc')
            ->paginate(50);





        return view('medium.gerenciar-mediuns', compact('medium'));
    }




    public function create()
    {
        $id_medium = 1;
        // $grupo=DB::select('select id, nome from grupo');
        $medium = DB::select('select * from medium');
        $tipo_mediunidade = DB::select('select id, tipo from tipo_mediunidade');
        $pessoas = DB::select('select id as idp, nome_completo, motivo_status, status from pessoas');
        $tipo_funcao = DB::select('select id as idf, tipo_funcao, nome, sigla from tipo_funcao');
        $setor = DB::select('select id as ids, nome from setor');
        $mediunidade_medium = DB::select('select id as idme, data_inicio from mediunidade_medium');


        return view('medium/criar-mediuns', compact('id_medium', 'medium', 'tipo_mediunidade', 'pessoas', 'tipo_funcao', 'setor', 'mediunidade_medium'));
    }

    public function store(Request $request)
    {
        // Obter os dados do formulário
        $id_pessoa = $request->input('id_pessoa');
        $id_setor = $request->input('id_setor');
        $id_funcao = $request->input('id_funcao');
        $status = $request->input('status');
        $tipo_ids = $request->input('id_tp_mediunidade');




        // Inserir dados na tabela 'medium'
        $mediumId = DB::table('medium')->insertGetId([
            'id_pessoa' => $id_pessoa,
            'id_setor' => $id_setor,
            'id_funcao' => $id_funcao,
            'status' => $status,

        ]);

        // Inserir dados na tabela 'mediunidade_medium'
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

        // Mensagem de sucesso e redirecionamento
        app('flasher')->addSuccess("Cadastrado com Sucesso");
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
            ->select('p.nome_completo', 'm.id AS idm', 'm.id_pessoa', 'm.status', 'm.id_setor', 'm.motivo_status', 'm.id_grupo', 'g.nome AS nome_grupo', 'tf.nome AS nome_funcao', 'm.id_funcao', 's.nome AS nome_setor', 'mm.id_mediunidade', 'mm.data_inicio')
            ->where('m.id', $id)
            ->first();



        $pessoas = DB::table('pessoas')->get();
        $tipo_mediunidade = DB::table('tipo_mediunidade')->get();
        $tipo_funcao = DB::table('tipo_funcao')->get();
        $setor = DB::table('setor')->get();
        $mediunidade_medium = DB::table('mediunidade_medium')->select('id_mediunidade', 'data_inicio', 'id_medium as id_mediuns')->where('id_medium', $id)->get();



        return view('medium.visualizar-mediuns', compact('tipo_mediunidade', 'medium', 'pessoas', 'tipo_funcao', 'setor', 'mediunidade_medium'));
    }


    public function edit($id)
    {
        $medium = DB::table('medium AS m')
            ->leftJoin('pessoas AS p', 'm.id_pessoa', '=', 'p.id')
            ->leftJoin('tipo_funcao AS tf', 'm.id_funcao', '=', 'tf.id')
            ->leftJoin('grupo AS g', 'm.id_grupo', '=', 'g.id')
            ->leftJoin('setor AS s', 'm.id_setor', '=', 's.id')
            ->leftJoin('mediunidade_medium AS mm', 'm.id', '=', 'mm.id_medium')
            ->select('p.nome_completo', 'm.id AS idm', 'm.id_pessoa', 'm.status', 'm.id_setor', 'm.motivo_status', 'm.id_grupo', 'g.nome AS nome_grupo', 'tf.nome AS nome_funcao', 'm.id_funcao', 's.nome AS nome_setor', 'mm.id_mediunidade', 'mm.data_inicio')
            ->where('m.id', $id)
            ->first();





        $pessoas = DB::table('pessoas')->get();
        $tipo_mediunidade = DB::table('tipo_mediunidade')->get();
        $tipo_funcao = DB::table('tipo_funcao')->get();
        $setor = DB::table('setor')->get();
        $mediunidade_medium = DB::table('mediunidade_medium')->select('id_mediunidade', 'data_inicio', 'id_medium as id_mediuns')->where('id_medium',  $medium->idm)->get();
        $tipo_mediunidade = DB::table('tipo_mediunidade')->get();




        return view('medium.editar-mediuns', compact('tipo_mediunidade', 'medium', 'pessoas', 'tipo_funcao', 'setor', 'mediunidade_medium', 'tipo_mediunidade'));
    }



    public function update(Request $request, string $id)
    {
        $input = $request->all();


        $dataManifestacao = isset($input['data_inicio']) ? $input['data_inicio'] : [];


        $tiposMediunidade = isset($input['id_mediunidade']) ? $input['id_mediunidade'] : [];



        $dataToUpdate = [
            'id_pessoa' => $input['id_pessoa'],
            'status' => $input['status'],
            'id_funcao' => $input['id_funcao'],
            'id_setor' => $input['setor'],
            //  'motivo_status' => $input['motivo_status'],
        ];


        foreach ($tiposMediunidade as $tipo) {

            if (isset($dataManifestacao[$tipo])) {

                $dataToUpdate["data_de_manifestacao_mediunidade_$tipo"] = $dataManifestacao[$tipo];


                DB::table('mediunidade_medium')
                    ->where('id_medium', $id)
                    ->where('id_mediunidade', $tipo)
                    ->update([
                        'data_inicio' => $dataManifestacao[$tipo] ? date('Y-m-d', strtotime($dataManifestacao[$tipo])) : null,
                    ]);
            }
        }

        DB::table('medium')->where('id', $id)->update($dataToUpdate);

        app('flasher')->addSuccess("Alterado com Sucesso");

        return redirect('gerenciar-mediuns');
    }



    public function destroy(string $id)
    {





        DB::table('medium')->where('id', $id)->delete();


        app('flasher')->addError('Excluido com sucesso.');
        return redirect('/gerenciar-mediuns');
    }
}
