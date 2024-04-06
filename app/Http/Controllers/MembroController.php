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
            ->leftJoin('associado', 'associado.id', '=', 'm.id_associado')
            ->join('pessoas AS p', 'associado.id_pessoa', '=', 'p.id')
            ->leftJoin('tipo_funcao AS tf', 'm.id_funcao', '=', 'tf.id')
            ->leftJoin('grupo AS g', 'm.id_grupo', '=', 'g.id')
            ->select(
                'p.nome_completo',
                'm.id AS idm',
                'm.id_associado',
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

        return view('membro.gerenciar-membro', compact('membroQuery', 'grupos', 'nome', 'cpf', 'grupoPesquisa', 'membro'));
    }




    public function create()
    {
  
        $grupo = DB::select('select id, nome from grupo');
        $membro = DB::select('select * from membro');
        $pessoas = DB::select('select id , nome_completo, motivo_status, status from pessoas');
        $tipo_funcao = DB::select('select id as idf, tipo_funcao, nome, sigla from tipo_funcao');
        $tipo_status_pessoa = DB::select('select id,tipo as tipos from tipo_status_pessoa');
        $associado = DB::table('associado')
            ->leftJoin('pessoas', 'pessoas.id', '=', 'associado.id_pessoa')
            ->select(
                'pessoas.nome_completo',
                'associado.nr_associado'
            )
            ->get();
           




        return view('membro/criar-membro', compact('associado', 'tipo_status_pessoa', 'grupo', 'membro', 'pessoas', 'tipo_funcao'));
    }




    public function store(Request $request)
    {



        $data = date("Y-m-d H:i:s");
        DB::table('membro')->insert([
            'id_associado' => $request->input('id_associado'),
            'id_funcao' => $request->input('id_funcao'),
            'id_grupo' => $request->input('id_grupo'),
            'dt_inicio' => $data,


        ]);
        



        app('flasher')->addSuccess("Cadastrado com Sucesso");
        return redirect('gerenciar-membro');
    }




    public function edit($id)
{
    $membro= DB::table('membro AS m')
    ->leftJoin('associado', 'associado.id', '=', 'm.id_associado')
    ->join('pessoas AS p', 'associado.id_pessoa', '=', 'p.id')
    ->leftJoin('tipo_funcao AS tf', 'm.id_funcao', '=', 'tf.id')
    ->leftJoin('grupo AS g', 'm.id_grupo', '=', 'g.id')
    ->select(
        'p.nome_completo',
        'm.id AS idm',
        'm.id_associado',
        'm.id_funcao',
        'p.cpf',
        'p.status',
        'p.motivo_status',
        'tf.nome as nome_funcao',
        'm.id_grupo',
        'g.nome as nome_grupo'
    )
        ->where('m.id', $id)
        ->first();

      
    $tipo_status_pessoa = DB::table('tipo_status_pessoa')->select('id', 'tipo as tipos')->get();
    $tipo_motivo_status_pessoa = DB::table('tipo_motivo_status_pessoa')->select('id', 'motivo')->get();
    $grupo = DB::table('grupo')->get();
    $pessoas = DB::table('pessoas')->get();
    $tipo_funcao = DB::table('tipo_funcao')->get();
    $associado = DB::table('associado')->get();

    return view('membro.editar-membro', compact('associado', 'tipo_status_pessoa', 'tipo_motivo_status_pessoa', 'grupo',  'membro', 'pessoas', 'tipo_funcao'));
}




    public function update(Request $request, string $id)
    {


        DB::table('membro')->where('id', $id)->update([
            'id_pessoa' => $request->input('id_pessoa'),
            'id_funcao' => $request->input('id_funcao'),
            'id_grupo' => $request->input('id_grupo'),



        ]);


        app('flasher')->addSuccess("Alterado com Sucesso");

        return redirect('gerenciar-membro');
    }





    public function show($id)
{
    $membro= DB::table('membro AS m')
    ->leftJoin('associado', 'associado.id', '=', 'm.id_associado')
    ->join('pessoas AS p', 'associado.id_pessoa', '=', 'p.id')
    ->leftJoin('tipo_funcao AS tf', 'm.id_funcao', '=', 'tf.id')
    ->leftJoin('grupo AS g', 'm.id_grupo', '=', 'g.id')
    ->select(
        'p.nome_completo',
        'm.id AS idm',
        'm.id_associado',
        'm.id_funcao',
        'p.cpf',
        'p.status',
        'p.motivo_status',
        'tf.nome as nome_funcao',
        'm.id_grupo',
        'g.nome as nome_grupo'
    )
        ->where('m.id', $id)
        ->first();

      
    $tipo_status_pessoa = DB::table('tipo_status_pessoa')->select('id', 'tipo as tipos')->get();
    $tipo_motivo_status_pessoa = DB::table('tipo_motivo_status_pessoa')->select('id', 'motivo')->get();
    $grupo = DB::table('grupo')->get();
    $pessoas = DB::table('pessoas')->get();
    $tipo_funcao = DB::table('tipo_funcao')->get();
    $associado = DB::table('associado')->get();

    return view('membro.visualizar-membro', compact('associado', 'tipo_status_pessoa', 'tipo_motivo_status_pessoa', 'grupo',  'membro', 'pessoas', 'tipo_funcao'));
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


        DB::table('membro')->where('id', $id)->delete();


        app('flasher')->addError('Excluído com sucesso.');
        return redirect('/gerenciar-membro');
    }
}
