<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Carbon;
use function Laravel\Prompts\select;

class MediunidadePessoaController extends Controller
{



    public function index(Request $request)
    {
        $tipos = DB::table('mediunidade_pessoa')
            ->select('id_pessoa')->groupBy('id_pessoa')->get();

        $array = json_decode(json_encode($tipos), true);


        $mediunidade = DB::table('pessoas AS p')
            ->select('id as idp', 'nome_completo', 'cpf', 'status')
            ->whereIn('id', $array)
            ->orderBy('p.nome_completo', 'ASC');

        $nome = $request->nome_pesquisa;
        $cpf = $request->cpf_pesquisa;

        if ($nome || $cpf) {
            $mediunidade->where(function ($query) use ($nome, $cpf) {
                $query->where('p.nome_completo', 'ilike', "%$nome%")
                    ->orWhere('p.cpf', 'ilike', "%$nome%");

                if ($cpf) {
                    $query->orWhere('p.cpf', 'ilike', "%$cpf%");
                }
            });
        }

        $mediunidade = $mediunidade->get();



        return view('mediunidade.gerenciar-mediunidades', compact('nome', 'cpf',  'mediunidade'));
    }




    public function create()
    {
        $id_mediunidade = 1;
        $grupo = DB::select('select id, nome from grupo');
        $mediunidade = DB::select('select * from mediunidade_pessoa');
        $tipo_mediunidade = DB::select('select id, tipo from tipo_mediunidade');
        $pessoas = DB::select('select id as idp, nome_completo, motivo_status, status from pessoas');
        $tipo_funcao = DB::select('select id as idf, tipo_funcao, nome, sigla from tipo_funcao');
        $mediunidade_pessoa = DB::select('select id as idme, data_inicio from mediunidade_pessoa');
        $tipo_status_pessoa = DB::select('select id,tipo as tipos from tipo_status_pessoa');


        return view('mediunidade.criar-mediunidade', compact('tipo_status_pessoa', 'grupo', 'id_mediunidade', 'mediunidade', 'tipo_mediunidade', 'pessoas', 'tipo_funcao', 'mediunidade_pessoa'));
    }



    public function store(Request $request)
    {
        // Obter os dados do formulário
        $id_pessoa = $request->input('id_pessoa');
        $tipo_ids = $request->input('id_tp_mediunidade');

        // Inserir dados na tabela 'mediunidade_pessoa'
        foreach ($tipo_ids as $tipo_id) {
            $datas_inicio = $request->input("data_inicio.{$tipo_id}");

            foreach ($datas_inicio as $data_inicio) {
                DB::table('mediunidade_pessoa')->insert([
                    'id_pessoa' => $id_pessoa,
                    'id_mediunidade' => $tipo_id,
                    'data_inicio' => $data_inicio ? date('Y-m-d', strtotime($data_inicio)) : null,
                ]);
            }
        }

        // Mensagem de sucesso e redirecionamento
        app('flasher')->addSuccess("Cadastrado com Sucesso");
        return redirect('gerenciar-mediunidades');
    }




    public function edit($id)
    {

        $id_mediunidade = 1;
        $mediunidade = DB::table('mediunidade_pessoa AS m')
            ->leftJoin('pessoas AS p', 'm.id_pessoa', '=', 'p.id')
            ->leftJoin('tipo_status_pessoa as tsp', 'p.status', '=', 'tsp.id')
            ->select('p.nome_completo', 'm.id_pessoa', 'm.id_mediunidade', 'm.id AS idm', 'm.id_pessoa', 'p.status', 'p.motivo_status', 'm.data_inicio', 'tsp.tipo')
            ->where('m.id_pessoa', $id)
            ->first();

        $tipo_motivo_status_pessoa = DB::select('select id,motivo  from tipo_motivo_status_pessoa');
        $tipo_status_pessoa = DB::select('select id,tipo as tipos from tipo_status_pessoa');
        $pessoas = DB::table('pessoas')->get();
        $tipo_mediunidade = DB::table('tipo_mediunidade')->get();

        $mediunidadesIds = DB::table('mediunidade_pessoa')->where('id_pessoa', $id)->get();

        $arrayChecked = [];

        foreach ($mediunidadesIds as $ids) {
            $arrayChecked[] = $ids->id_mediunidade;
        }

        return view('mediunidade.editar-mediunidade', compact('mediunidadesIds', 'arrayChecked', 'id_mediunidade', 'mediunidade', 'tipo_motivo_status_pessoa', 'tipo_status_pessoa',  'tipo_mediunidade', 'pessoas'));
    }



    public function update(Request $request, string $id)
    {


        DB::table('mediunidade_pessoa')->where('id_pessoa', $id)->delete();

        

        // Obter os dados do formulário
        $id_pessoa = $request->input('id_pessoa');
        $tipo_ids = $request->input('id_tp_mediunidade');

        // Inserir dados na tabela 'mediunidade_medium'
        foreach ($tipo_ids as $tipo_id) {
            $datas_inicio = $request->input("data_inicio.{$tipo_id}");

            foreach ($datas_inicio as $data_inicio) {
                DB::table('mediunidade_pessoa')->insert([
                    'id_pessoa' => $id,
                    'id_mediunidade' => $tipo_id,
                    'data_inicio' => $data_inicio ? date('Y-m-d', strtotime($data_inicio)) : null,
                ]);
            }
        }

        return redirect('gerenciar-mediunidades');
    }





    public function show($id)
    {
        $id_mediunidade = 1;
        $mediunidade = DB::table('mediunidade_pessoa AS m')
            ->leftJoin('pessoas AS p', 'm.id_pessoa', '=', 'p.id')
            ->leftJoin('tipo_status_pessoa as tsp', 'p.status', '=', 'tsp.id')
            ->select('p.nome_completo', 'm.id_pessoa', 'm.id_mediunidade', 'm.id AS idm', 'm.id_pessoa', 'p.status', 'p.motivo_status', 'm.data_inicio', 'tsp.tipo')
            ->where('m.id_pessoa', $id)
            ->first();

        $tipo_motivo_status_pessoa = DB::select('select id,motivo  from tipo_motivo_status_pessoa');
        $tipo_status_pessoa = DB::select('select id,tipo as tipos from tipo_status_pessoa');
        $pessoas = DB::table('pessoas')->get();
        $tipo_mediunidade = DB::table('tipo_mediunidade')->get();

        $mediunidadesIds = DB::table('mediunidade_pessoa')->where('id_pessoa', $id)->get();

        $arrayChecked = [];

        foreach ($mediunidadesIds as $ids) {
            $arrayChecked[] = $ids->id_mediunidade;
        }
        return view('mediunidade.visualizar-mediunidade', compact('mediunidadesIds', 'arrayChecked', 'id_mediunidade', 'mediunidade', 'tipo_motivo_status_pessoa', 'tipo_status_pessoa',  'tipo_mediunidade', 'pessoas'));
    }



    public function destroy(string $id)
    {

        $data = date("Y-m-d H:i:s");

        DB::table('historico_venus')->insert([

            'id_usuario' => session()->get('pessoa.id_pessoa'),
            'data' => $data,
            'fato' => 0,
            'obs' => $id

        ]);

        $mediunidade = DB::table('mediunidade_pessoa')->where('id', $id)->first();


        if (!$mediunidade) {
            app('flasher')->addError('Pessoa não foi encontrada.');
            return redirect('/gerenciar-mediunidades');
        }


        DB::table('mediunidade_pessoa')->where('id', $id)->delete();


        app('flasher')->addError('Excluído com sucesso.');
        return redirect('/gerenciar-mediunidades');
    }
}
