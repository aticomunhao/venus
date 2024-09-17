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

        $contar = $mediunidade->distinct()->count('p.id');
        $nome = $request->nome_pesquisa;
        $cpf = $request->cpf_pesquisa;
   

        if($nome){
            $mediunidade = $mediunidade->where('nome_completo', 'ilike', "%$nome%");
        }
        if($cpf){
            $mediunidade = $mediunidade->where('cpf', 'ilike', "%$cpf%");
        }

        $contarQuery = clone $mediunidade;
        $contar = $contarQuery->distinct('p.id')->count('p.id');

      
        $mediunidade = $mediunidade->paginate(50);



        return view('mediunidade.gerenciar-mediunidades', compact('nome', 'cpf',  'mediunidade','contar'));
    }


  
        

    public function create()
    {
        try{
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
    catch(\Exception $e){

        $code = $e->getCode( );
        return view('administrativo-erro.erro-inesperado', compact('code'));
            }
        }


    public function store(Request $request)
    {
        try{
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

    catch(\Exception $e){

        $code = $e->getCode( );
        return view('administrativo-erro.erro-inesperado', compact('code'));
            }
        }


    public function edit($id)
    {
        try{
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
    catch(\Exception $e){

        $code = $e->getCode( );
        return view('administrativo-erro.erro-inesperado', compact('code'));
            }
        }

        public function update(Request $request, string $id)
        {
            try {
                // Inicia a transação
                DB::beginTransaction();
        
                // Excluir registros anteriores na tabela 'mediunidade_pessoa' para o mesmo id_pessoa
                DB::table('mediunidade_pessoa')->where('id_pessoa', $id)->delete();
        
                // Obter os dados do formulário
                $id_pessoa = $request->input('id_pessoa');
                $tipo_ids = $request->input('id_tp_mediunidade');
        
                // Certifique-se de que tipo_ids é um array
                if (is_array($tipo_ids)) {
                    // Inserir dados na tabela 'mediunidade_pessoa'
                    foreach ($tipo_ids as $tipo_id) {
                        $datas_inicio = $request->input("data_inicio.{$tipo_id}");
        
                        // Certifique-se de que datas_inicio é um array
                        if (is_array($datas_inicio)) {
                            foreach ($datas_inicio as $data_inicio) {
                                DB::table('mediunidade_pessoa')->insert([
                                    'id_pessoa' => $id,
                                    'id_mediunidade' => $tipo_id,
                                    'data_inicio' => $data_inicio ? date('Y-m-d', strtotime($data_inicio)) : null,
                                ]);
                            }
                        }
                    }
                }
        
                // Atualizar o status e motivo na tabela 'pessoas'
                $status = $request->input('tipo_status_pessoa');
                $motivo = $request->input('motivo_status');
                DB::table('pessoas')->where('id', $id)->update(['status' => $status, 'motivo_status' => $motivo]);
        
                // Gravar no histórico
                $ida = session()->get('usuario.id_pessoa');
                $data = Carbon::today();
                DB::table('historico_venus')->insert([
                    'id_usuario' => $ida,
                    'data' => $data,
                    'fato' => 18,
                    'pessoa' => $id,
                ]);
        
                // Commit da transação
                DB::commit();
        
                return redirect('gerenciar-mediunidades');
            } catch (\Exception $e) {
                // Rollback em caso de erro
                DB::rollBack();
        
                $code = $e->getCode();
                $message = $e->getMessage();
                return view('administrativo-erro.erro-inesperado', compact('code', 'message'));
            }
        }
        


    public function show($id)
    {
        try{
        $id_mediunidade = 1;
        $mediunidade = DB::table('mediunidade_pessoa AS m')
            ->leftJoin('pessoas AS p', 'm.id_pessoa', '=', 'p.id')
            ->leftJoin('tipo_status_pessoa as tsp', 'p.status', '=', 'tsp.id')
            ->select('p.nome_completo', 'm.id_pessoa', 'm.id_mediunidade', 'm.id AS idm', 'm.id_pessoa', 'p.status', 'p.motivo_status', 'tsp.tipo')
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

    catch(\Exception $e){

        $code = $e->getCode( );
        return view('administrativo-erro.erro-inesperado', compact('code'));
            }
        }

        
    public function destroy(string $id)
    {
      
        $data = date("Y-m-d H:i:s");

        DB::table('historico_venus')->insert([

            'id_usuario' => session()->get('usuario.id_pessoa'),
            'data' => $data,
            'fato' => 9,
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
