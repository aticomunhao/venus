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

        $medium=DB::select('select * from medium');
        $tipo_mediunidade = DB::select('select id, tipo from tipo_mediunidade');
        $pessoas = DB::select('select id as idp, nome_completo, motivo_status, status from pessoas');
        $tipo_funcao = DB::select('select id as idf, tipo_funcao, nome, sigla from tipo_funcao');
        $setor = DB::select('select id as ids, nome from setor');
        $mediunidade_medium = DB::select('select id as idme, data_inicio from mediunidade_medium');


        return view('medium/criar-mediuns', compact('id_medium','medium','tipo_mediunidade', 'pessoas', 'tipo_funcao', 'setor', 'mediunidade_medium'));
    }



    public function show($id)

    {
        $medium = DB::table('medium AS m')
            ->leftJoin('pessoas AS p', 'm.id_pessoa', '=', 'p.id')
            ->leftJoin('tipo_funcao AS tf', 'm.id_funcao', '=', 'tf.id')
            ->leftJoin('grupo AS g', 'm.id_grupo', '=', 'g.id')
            ->leftJoin('setor AS s', 'm.id_setor', '=', 's.id')
            ->select('p.nome_completo', 'm.id AS idm', 'm.id_pessoa', 'm.status', 'm.id_setor', 'm.motivo_status', 'm.id_grupo', 'g.nome AS nome_grupo', 'tf.nome AS nome_funcao', 'm.id_funcao', 's.nome AS nome_setor')
            ->where('m.id', $id)
            ->first();


        $pessoas = DB::table('pessoas')->get();
        $tipo_mediunidade = DB::table('tipo_mediunidade')->get();
        $tipo_funcao = DB::table('tipo_funcao')->get();
        $setor = DB::table('setor')->get();

        return view('medium.visualizar-mediuns', compact('tipo_mediunidade', 'medium', 'pessoas', 'tipo_funcao', 'setor'));
    }





    public function edit($id)
    {
        $medium = DB::table('medium AS m')
            ->leftJoin('pessoas AS p', 'm.id_pessoa', '=', 'p.id')
            ->leftJoin('tipo_funcao AS tf', 'm.id_funcao', '=', 'tf.id')
            ->leftJoin('grupo AS g', 'm.id_grupo', '=', 'g.id')
            ->leftJoin('setor AS s', 'm.id_setor', '=', 's.id')
            ->select('p.nome_completo', 'm.id AS idm', 'm.id_pessoa', 'm.status', 'm.id_setor', 'm.motivo_status', 'm.id_grupo', 'g.nome AS nome_grupo', 'tf.nome AS nome_funcao', 'm.id_funcao', 's.nome AS nome_setor')
            ->where('m.id', $id)
            ->first();


        $pessoas = DB::table('pessoas')->get();
        $tipo_mediunidade = DB::table('tipo_mediunidade')->get();
        $tipo_funcao = DB::table('tipo_funcao')->get();
        $setor = DB::table('setor')->get();

        return view('medium.editar-mediuns', compact('tipo_mediunidade', 'medium', 'pessoas', 'tipo_funcao', 'setor'));
    }




    public function store(Request $request)
    {
        $id_medium = $request->input('id_medium');
        $tipo_ids = $request->input('id_tp_mediunidade');
        $id_mediunidade = $request->input('id_mediunidade');


        DB::table('medium')->insert([
            'id_pessoa' => $request->input('id_pessoa'),
            'id_setor' => $request->input('id_setor'),
            'id_funcao' => $request->input('id_funcao'),
            'status' => $request->input('status'),
            'id_tipo_mediunidade' => implode(',', $tipo_ids), // Convertendo array em string
            'id_mediunidade_medium' => $id_mediunidade,
        ]);

        foreach ($tipo_ids as $tipo_id) {
            $data_inicio = $request->input("data_inicio.{$tipo_id}");

            DB::table('mediunidade_medium')->insert([
                'id_medium' => $id_medium,
                'id_mediunidade' => $id_mediunidade,
                'data_inicio' => $data_inicio ? date('Y-m-d', strtotime($data_inicio)) : null,
            ]);
        }

        app('flasher')->addSuccess("Cadastrado com Sucesso");

        return redirect('gerenciar-mediuns');
    }












    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Obtenha as entradas do formulário
        $input = $request->all();

        // Crie um array para armazenar as datas de manifestação
        $dataManifestacao = $input['data_manifestou_mediunidade'];

        // Crie um array para armazenar os tipos de mediunidade selecionados
        $tiposMediunidade = $input['id_tp_mediunidade'];

        // Inicialize um array para armazenar os dados a serem atualizados no banco de dados
        $dataToUpdate = [
            'id_pessoa' => $input['id_pessoa'],
            'status' => $input['status'],
            'id_funcao' => $input['id_funcao'],
        ];

        // Itere sobre os tipos de mediunidade selecionados
        foreach ($tiposMediunidade as $tipo) {
            // Verifique se a data de manifestação para esse tipo está definida
            if (isset($dataManifestacao[$tipo])) {
                // Adicione a data de manifestação ao array de dados a serem atualizados
                $dataToUpdate["data_de_manifestacao_mediunidade_$tipo"] = $dataManifestacao[$tipo];
            }
        }

        // Atualize os dados no banco de dados
        DB::table('medium')->where('id', $id)->update($dataToUpdate);

        app('flasher')->addSuccess("Alterado com Sucesso");

        return redirect('gerenciar-mediuns');
    }

    //



    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {

        $ids = DB::table('medium')->select('id')->where('id', $id)->get();
        $teste = session()->get('usuario');

        $verifica = DB::table('historico_venus')->where('fato', $id)->count('fato');


        $data = date("Y-m-d H:i:s");






        DB::table('historico_venus')->insert([

            'id_usuario' => session()->get('usuario.id_usuario'),
            'data' => $data,
            'fato' => 0,
            'obs' => $ids
        ]);

        DB::table('medium')->where('id', $id)->delete();


        app('flasher')->addError('Excluido com sucesso.');
        return redirect('/gerenciar-mediuns');

        //
    }
}
