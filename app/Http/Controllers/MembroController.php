<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Carbon;
use function Laravel\Prompts\select;

class MembroController extends Controller
{

    public function grupos(Request $request){
        try{
        $cronogramas = DB::table('membro')->select('id_cronograma')->get();
        $array_cro = [];

        if($request->nome_membro){
            $cronogramas = DB::table('membro AS m')
            ->leftJoin('associado', 'associado.id', '=', 'm.id_associado')
            ->join('pessoas AS p', 'associado.id_pessoa', '=', 'p.id')
            ->leftJoin('tipo_funcao AS tf', 'm.id_funcao', '=', 'tf.id')
            ->leftJoin('grupo AS g', 'm.id_cronograma', '=', 'g.id')
            ->where('id_associado', $request->nome_membro)
            ->select(
                'p.nome_completo',
                'm.id_cronograma',

            )->distinct()->get();}


        foreach($cronogramas as $cro){
            $array_cro[] = $cro->id_cronograma;
        }

       // dd($request->all());

        $membro_cronograma = DB::table('cronograma as cro')
        ->select('cro.id', 'gr.nome as nome_grupo', 'td.nome as dia', 'cro.h_inicio', 'cro.h_fim', 'sl.numero as sala', 'tpg.descricao')
        ->leftJoin('grupo as gr', 'cro.id_grupo', 'gr.id')
        ->leftJoin('tipo_dia as td', 'cro.dia_semana', 'td.id')
        ->leftJoin('salas as sl', 'cro.id_sala', 'sl.id')
        ->leftJoin('tipo_status_grupo as tpg', 'cro.modificador', 'tpg.id')
        ->whereIn('cro.id', $array_cro);




        $membro = DB::table('membro AS m')
            ->leftJoin('associado', 'associado.id', '=', 'm.id_associado')
            ->join('pessoas AS p', 'associado.id_pessoa', '=', 'p.id')
            ->leftJoin('tipo_funcao AS tf', 'm.id_funcao', '=', 'tf.id')
            ->leftJoin('grupo AS g', 'm.id_cronograma', '=', 'g.id')
            ->select(
                'p.nome_completo',
                'm.id_associado',

            )->distinct()->get();


          if($request->nome_grupo){
            $membro_cronograma = $membro_cronograma->where('cro.id', $request->nome_grupo);
          }

                $membro_cronograma = $membro_cronograma->orderBy('tpg.descricao')->orderBy('nome_grupo')->get();

            $nome = $request->nome_grupo;
            $membroPesquisa = $request->nome_membro;






        return view('membro.listar-grupos-membro', compact('membro_cronograma', 'nome', 'membro', 'membroPesquisa'));
    }


    catch(\Exception $e){

        $code = $e->getCode( );
        return view('listar-grupos erro.erro-inesperado', compact('code'));
            }
        }

    public function createGrupo(Request $request, String $id){
        
        try{
        $grupo = DB::table('cronograma as cro')
    ->select('cro.id', 'gr.nome', 'cro.h_inicio', 'cro.h_fim', 'sa.numero', 'td.nome as dia')
    ->leftJoin('salas as sa', 'cro.id_sala', 'sa.id')
    ->leftJoin('grupo as gr', 'cro.id_grupo', 'gr.id')
    ->leftJoin('tipo_dia as td', 'cro.dia_semana', 'td.id')
    ->get();


    $membro = DB::select('select * from membro');
    $pessoas = DB::select('select id , nome_completo, motivo_status, status from pessoas order by nome_completo asc');
    $tipo_funcao = DB::select('select id as idf, tipo_funcao, nome, sigla from tipo_funcao order by nome asc');
    $tipo_status_pessoa = DB::select('select id,tipo as tipos from tipo_status_pessoa');
    $associado = DB::table('associado')
        ->leftJoin('pessoas', 'pessoas.id', '=', 'associado.id_pessoa')
        ->select(
            'pessoas.nome_completo',
            'associado.nr_associado'
        )
        ->orderBy('pessoas.nome_completo', 'asc')
        ->get();

    return view('membro/criar-membro-grupo', compact('associado', 'tipo_status_pessoa', 'grupo', 'membro', 'pessoas', 'tipo_funcao', 'id'));

    }
    catch(\Exception $e){

        $code = $e->getCode( );
        return view('administrativo-erro.erro-inesperado', compact('code'));
            }
        }

    public function storeGrupo(Request $request, String $id){
        try{
        $data = date("Y-m-d H:i:s");
        DB::table('membro')->insert([
            'id_associado' => $request->input('id_associado'),
            'id_funcao' => $request->input('id_funcao'),
            'id_cronograma' => $id,
            'dt_inicio' => $data,


        ]);


        app('flasher')->addSuccess("Cadastrado com Sucesso");
        return redirect("gerenciar-membro/$id");
    }
    catch(\Exception $e){

        $code = $e->getCode( );
        return view('administrativo-erro.erro-inesperado', compact('code'));
            }
        }
    public function index(Request $request, String $id)
    {

        try{

        $grupo = DB::table('cronograma as cro')
    ->select('cro.id', 'gr.nome', 'cro.h_inicio', 'cro.h_fim', 'sa.numero', 'td.nome as dia', 'cro.modificador')
    ->leftJoin('salas as sa', 'cro.id_sala', 'sa.id')
    ->leftJoin('grupo as gr', 'cro.id_grupo', 'gr.id')
    ->leftJoin('tipo_dia as td', 'cro.dia_semana', 'td.id')
    ->where('cro.id', $id)
    ->first();

        $membroQuery = DB::table('membro AS m')
            ->leftJoin('associado', 'associado.id', '=', 'm.id_associado')
            ->join('pessoas AS p', 'associado.id_pessoa', '=', 'p.id')
            ->leftJoin('tipo_funcao AS tf', 'm.id_funcao', '=', 'tf.id')
            ->leftJoin('grupo AS g', 'm.id_cronograma', '=', 'g.id')
            ->where('m.id_cronograma', $id)
            ->select(
                'p.nome_completo',
                'm.id AS idm',
                'm.id_associado',
                'm.id_funcao',
                'p.cpf',
                'p.status',
                'p.motivo_status',
                'tf.nome as nome_funcao',
                'm.id_cronograma',
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

        return view('membro.gerenciar-membro', compact('membro', 'id', 'grupo'));
    }

    catch(\Exception $e){

        $code = $e->getCode( );
        return view('gerenciar-membro erro.erro-inesperado', compact('code'));
            }
        }


    public function create()
{
    try{
    $grupo = DB::table('cronograma as cro')
    ->select('cro.id', 'gr.nome', 'cro.h_inicio', 'cro.h_fim', 'sa.numero', 'td.nome as dia')
    ->leftJoin('salas as sa', 'cro.id_sala', 'sa.id')
    ->leftJoin('grupo as gr', 'cro.id_grupo', 'gr.id')
    ->leftJoin('tipo_dia as td', 'cro.dia_semana', 'td.id')
    ->get();


    $membro = DB::select('select * from membro');
    $pessoas = DB::select('select id , nome_completo, motivo_status, status from pessoas order by nome_completo asc');
    $tipo_funcao = DB::select('select id as idf, tipo_funcao, nome, sigla from tipo_funcao order by nome asc');
    $tipo_status_pessoa = DB::select('select id,tipo as tipos from tipo_status_pessoa');
    $associado = DB::table('associado')
        ->leftJoin('pessoas', 'pessoas.id', '=', 'associado.id_pessoa')
        ->select(
            'pessoas.nome_completo',
            'associado.nr_associado'
        )
        ->orderBy('pessoas.nome_completo', 'asc')
        ->get();

    return view('membro/criar-membro', compact('associado', 'tipo_status_pessoa', 'grupo', 'membro', 'pessoas', 'tipo_funcao'));
}

catch(\Exception $e){

    $code = $e->getCode( );
    return view('administrativo-erro.erro-inesperado', compact('code'));
        }
    }



    public function store(Request $request)
    {
        try{


        $data = date("Y-m-d H:i:s");
        DB::table('membro')->insert([
            'id_associado' => $request->input('id_associado'),
            'id_funcao' => $request->input('id_funcao'),
            'id_cronograma' => $request->input('id_reuniao'),
            'dt_inicio' => $data,


        ]);




        app('flasher')->addSuccess("Cadastrado com Sucesso");
        return redirect('gerenciar-grupos-membro');
    }

    catch(\Exception $e){

        $code = $e->getCode( );
        return view('administrativo-erro.erro-inesperado', compact('code'));
            }
        }

    public function edit(String $idcro, String $id)
{
        try{
    $grupo = DB::table('cronograma as cro')
    ->select('cro.id', 'gr.nome', 'cro.h_inicio', 'cro.h_fim', 'sa.numero', 'td.nome as dia')
    ->leftJoin('salas as sa', 'cro.id_sala', 'sa.id')
    ->leftJoin('grupo as gr', 'cro.id_grupo', 'gr.id')
    ->leftJoin('tipo_dia as td', 'cro.dia_semana', 'td.id')
    ->get();

    $membro = DB::table('membro AS m')
        ->leftJoin('associado AS a', 'a.id', '=', 'm.id_associado')
        ->leftJoin('pessoas AS p', 'a.id_pessoa', '=', 'p.id')
        ->leftJoin('tipo_funcao AS tf', 'm.id_funcao', '=', 'tf.id')
        ->leftJoin('grupo AS g', 'm.id_cronograma', '=', 'g.id')
        ->leftJoin('pessoas', 'p.id', '=', 'a.id_pessoa')
        ->select(
            'p.nome_completo',
            'm.id AS idm',
            'm.id_associado',
            'm.id_funcao',
            'p.cpf',
            'p.status',
            'p.motivo_status',
            'tf.nome as nome_funcao',
            'm.id_cronograma',
            'g.nome as nome_grupo'
        )
        ->where('m.id', $id)
        ->first();

    $tipo_status_pessoa = DB::table('tipo_status_pessoa')->select('id', 'tipo as tipos')->get();
    $tipo_motivo_status_pessoa = DB::table('tipo_motivo_status_pessoa')->select('id', 'motivo')->get();
    $pessoas = DB::table('pessoas')->get();
    $tipo_funcao = DB::table('tipo_funcao')->get();
    $associado = DB::table('associado')  ->leftJoin('pessoas', 'pessoas.id', '=', 'associado.id_pessoa')
    ->select(
        'associado.id',
        'pessoas.nome_completo',
        'associado.nr_associado'
    )
    ->orderBy('pessoas.nome_completo', 'asc')
    ->get();

    return view('membro.editar-membro', compact('associado','membro', 'tipo_status_pessoa', 'tipo_motivo_status_pessoa', 'grupo', 'pessoas', 'tipo_funcao', 'idcro'));
}


catch(\Exception $e){

    $code = $e->getCode( );
    return view('administrativo-erro.erro-inesperado', compact('code'));
        }
    }



    public function update(Request $request, string $idcro, String $id)
    {
        try{

        DB::table('membro')->where('id', $id)->update([
            'id_funcao' => $request->input('id_funcao'),
            'id_cronograma' => $idcro,
        ]);



        app('flasher')->addSuccess("Alterado com Sucesso");

        return redirect("gerenciar-membro/$idcro");
    }


    catch(\Exception $e){

        $code = $e->getCode( );
        return view('administrativo-erro.erro-inesperado', compact('code'));
            }
        }


    public function show(string $idcro, string $id)
{
    try{
    $grupo = DB::table('cronograma as cro')
    ->select('cro.id', 'gr.nome', 'cro.h_inicio', 'cro.h_fim', 'sa.numero', 'td.nome as dia')
    ->leftJoin('salas as sa', 'cro.id_sala', 'sa.id')
    ->leftJoin('grupo as gr', 'cro.id_grupo', 'gr.id')
    ->leftJoin('tipo_dia as td', 'cro.dia_semana', 'td.id')
    ->get();

    $membro= DB::table('membro AS m')
    ->leftJoin('associado', 'associado.id', '=', 'm.id_associado')
    ->join('pessoas AS p', 'associado.id_pessoa', '=', 'p.id')
    ->leftJoin('tipo_funcao AS tf', 'm.id_funcao', '=', 'tf.id')
    ->leftJoin('grupo AS g', 'm.id_cronograma', '=', 'g.id')
    ->select(
        'p.nome_completo',
        'm.id AS idm',
        'm.id_associado',
        'm.id_funcao',
        'p.cpf',
        'p.status',
        'p.motivo_status',
        'tf.nome as nome_funcao',
        'm.id_cronograma',
        'g.nome as nome_grupo'
    )
        ->where('m.id', $id)
        ->first();


    $tipo_status_pessoa = DB::table('tipo_status_pessoa')->select('id', 'tipo as tipos')->get();
    $tipo_motivo_status_pessoa = DB::table('tipo_motivo_status_pessoa')->select('id', 'motivo')->get();
    $pessoas = DB::table('pessoas')->get();
    $tipo_funcao = DB::table('tipo_funcao')->get();
    $associado = DB::table('associado')  ->leftJoin('pessoas', 'pessoas.id', '=', 'associado.id_pessoa')
    ->select(
        'associado.id',
        'pessoas.nome_completo',
        'associado.nr_associado'
    )
    ->orderBy('pessoas.nome_completo', 'asc')
    ->get();

    return view('membro.visualizar-membro', compact('associado', 'tipo_status_pessoa', 'tipo_motivo_status_pessoa', 'grupo',  'membro', 'pessoas', 'tipo_funcao', 'idcro'));
}

catch(\Exception $e){

    $code = $e->getCode( );
    return view('administrativo-erro.erro-inesperado', compact('code'));
        }
    }
    public function destroy(string $idcro, string $id)
    {
        try{
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
            return redirect("/gerenciar-membro/$idcro");
        }


        DB::table('membro')->where('id', $id)->delete();


        app('flasher')->addError('Excluído com sucesso.');
        return redirect("/gerenciar-membro/$idcro");
    }

    catch(\Exception $e){

        $code = $e->getCode( );
        return view('administrativo-erro.erro-inesperado', compact('code'));
            }
        }    
    public function ferias(string $id, string $tp){
        try{
        if($tp == 1){

            DB::table('cronograma')->where('id', $id)->update([
            'modificador' => 4
         ]);



        }
        else if($tp == 2){

            DB::table('cronograma')->where('id', $id)->update([
            'modificador' => null
         ]);


        }
        return redirect()->back();



    }
    catch(\Exception $e){

        $code = $e->getCode( );
        return view('administrativo-erro.erro-inesperado', compact('code'));
            }
        }
}
