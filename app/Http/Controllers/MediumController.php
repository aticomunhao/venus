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
    ->select('p.nome_completo', 'm.id AS idm', 'm.id_pessoa', 'm.id_funcao', 'p.status', 'p.motivo_status', 'tf.nome', 'm.id_grupo')
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
            $medium = DB::select('select * from medium');
            $tipo_mediunidade = DB::select('select id, tipo from tipo_mediunidade');
            $pessoas = DB::select('select id, nome_completo, motivo_status, status from pessoas');
            $tipo_motivo_status_pessoa = DB::select('select motivo from tipo_motivo_status_pessoa');
            $tipo_status_pessoa = DB::select('select tipo from tipo_status_pessoa');
            $tipo_funcao = DB::select('select tipo_funcao,nome,sigla from tipo_funcao');


            return view('medium/criar-mediuns', compact('medium', 'tipo_mediunidade', 'pessoas', 'tipo_motivo_status_pessoa', 'tipo_status_pessoa','tipo_funcao'));
        }


    /**
     * Store a newly created resource in storage.
     */



    /**
     * Display the specified resource.
     */

     public function show($id)
     {
        $medium = DB::table('medium AS m')
        ->leftJoin('pessoas AS p', 'm.id_pessoa', '=', 'p.id')
        ->leftJoin('tipo_funcao AS tf', 'm.id_funcao', '=', 'tf.id')
        ->select('m.id', 'p.nome_completo', 'm.id_pessoa', 'm.id_tp_mediunidade', 'p.status', 'm.data_manifestou_mediunidade', 'm.id_funcao','tf.nome')
        ->where('m.id', $id)
        ->first();

    $pessoas = DB::table('pessoas')->get();
    $tipo_mediunidade = DB::table('tipo_mediunidade')->get();
    $tipo_motivo_status_pessoa = DB::table('tipo_motivo_status_pessoa')->pluck('motivo');
    $tipo_funcao = DB::table('tipo_funcao')->pluck('nome');

         return view('medium.visualizar-mediuns', compact('tipo_mediunidade', 'medium', 'pessoas', 'tipo_motivo_status_pessoa','tipo_funcao'));
     }










     public function edit($id)
     {
         $medium = DB::table('medium AS m')
             ->leftJoin('pessoas AS p', 'm.id_pessoa', '=', 'p.id')
             ->leftJoin('tipo_funcao AS tf', 'm.id_funcao', '=', 'tf.id')
             ->select('m.id', 'p.nome_completo', 'm.id_pessoa', 'm.id_tp_mediunidade', 'p.status', 'm.data_manifestou_mediunidade', 'm.id_funcao','tf.nome')
             ->where('m.id', $id)
             ->first();

         $pessoas = DB::table('pessoas')->get();
         $tipo_mediunidade = DB::table('tipo_mediunidade')->get();
         $tipo_motivo_status_pessoa = DB::table('tipo_motivo_status_pessoa')->pluck('motivo');
         $tipo_funcao = DB::table('tipo_funcao')->pluck('nome');

         return view('medium.editar-mediuns', compact('tipo_mediunidade', 'medium', 'pessoas', 'tipo_motivo_status_pessoa', 'tipo_funcao'));
     }





     public function store(Request $request)
     {
         // Valide os dados do formulário conforme necessário
         $request->validate([
             'id_pessoa' => 'required|integer',
             'id_tp_mediunidade' => 'required|array',
             'data_manifestou_mediunidade' => 'required|array',
             'id_funcao'=>'required|integer'
             // Adicione outras regras de validação conforme necessário
         ]);


         $id_pessoa = $request->input('id_pessoa');
         $id_tp_mediunidade = $request->input('id_tp_mediunidade');
         $data_manifestou_mediunidade = $request->input('data_manifestou_mediunidade');
         $id_grupo = $request->input('id_grupo');
         $id_funcao = $request->input('id_funcao');


         $count = count($id_tp_mediunidade);


         for ($i = 0; $i < $count; $i++) {
             $tipo_id = $id_tp_mediunidade[$i];


             $data_manifestou = isset($data_manifestou_mediunidade[$tipo_id]) ? $data_manifestou_mediunidade[$tipo_id] : null;


             DB::table('medium')->insert([
                 'id_pessoa' => $id_pessoa,
                 'id_tp_mediunidade' => $tipo_id,
                 'data_manifestou_mediunidade' => $data_manifestou ? date('Y-m-d', strtotime($data_manifestou)) : null,
                 'id_grupo' => $id_grupo,
                 'id_funcao'=>$id_funcao,
             ]);
         }


         session()->flash('success', 'Cadastrado com sucesso!');


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

        $ids=DB::table('medium')->select('id')->where('id', $id)->get();
        $teste=session()->get('usuario');

        $verifica=DB::table('historico_venus') -> where('fato',$id)->count('fato');


        $data = date("Y-m-d H:i:s");






        DB::table('historico_venus')->insert([

            'id_usuario' => session()->get('usuario.id_usuario'),
            'data' => $data,
            'fato' => 0,
            'obs'=>$ids
        ]);

        DB::table('medium')->where('id', $id)->delete();


        app('flasher')->addError('Excluido com sucesso.');
        return redirect('/gerenciar-mediuns');

        //
    }
}
