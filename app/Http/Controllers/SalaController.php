<?php

namespace App\Http\Controllers;

use App\Models\Grupo;
use App\Models\Sala;
use Doctrine\DBAL\Driver\Mysqli\Exception\InvalidOption;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Spatie\FlareClient\Http\Exceptions\InvalidData;
use Illuminate\Validation\Rule;


use function Psy\debug;

class SalaController extends Controller
{
    public function index(Request $request)
{
    $sala = DB::table('salas AS s')
        ->select(
            's.id AS ids',
            's.nome AS nome1',
            's.id_finalidade',
            's.numero',
            's.id_localizacao',
            's.tamanho_sala',
            's.nr_lugares',
            's.status_sala',
            'ts.descricao',
            'tl.nome AS nome2'
        )
        ->leftJoin('tipo_finalidade_sala AS ts', 's.id_finalidade', 'ts.id')
        ->leftJoin('tipo_localizacao AS tl', 's.id_localizacao', 'tl.id');

    $nome = $request->nome_pesquisa;

    if ($request->nome_pesquisa) {
        $sala->where('s.nome', 'like', "%$request->nome_pesquisa%");
    }

    $sala = $sala->orderBy('s.nome', 'ASC')->paginate(50);

    return view('salas.gerenciar-salas', compact('sala'));
}


    public function criar()
    {
        $salas = db::select('select * from salas');
        $tipo_finalidade_sala = db::select('select * from tipo_finalidade_sala');
        $tipo_motivo = db::select('select * from tipo_motivo');

        $tipo_localizacao = DB::table('tipo_localizacao as tl')
            ->select('tl.id AS ids', 'tl.nome', 'tl.sigla')->get();

            $numerosExistem = DB::table('salas')->pluck('numero')->toArray();






        //
        return view('salas/criar-salas', compact('salas', 'tipo_finalidade_sala', 'tipo_localizacao','numerosExistem','tipo_motivo'));
    }
    /**
     * Display the specified resource.
     */
    public function show($id)
    {

        $salaEditada = DB::table('salas')->where('id', $id)->select('*')->first();


        $sala = db::select('select * from salas');
        $tipo_finalidade_sala = db::select('select * from tipo_finalidade_sala');
        $tipo_localizacao = DB::table('tipo_localizacao as tl')
            ->leftJoin('salas AS s', 'tl.id', '=', 's.id_localizacao')->select('s.id AS ids', 'tl.nome', 'tl.sigla')->get();






        return view('salas/visualizar-salas', compact('sala', 'tipo_localizacao', 'tipo_finalidade_sala', 'salaEditada'));

        //
    }


    public function store(Request $request)
    {









        $ar_condicionado = isset($request->ar_condicionado) ? 1 : 0;


        $projetor = isset($request->projetor) ? 1 : 0;
        $quadro = isset($request->quadro) ? 1 : 0;
        $tela_projetor = isset($request->tela_projetor) ? 1 : 0;
        $ventilador = isset($request->ventilador) ? 1 : 0;
        $computador = isset($request->computador) ? 1 : 0;
        $controle = isset($request->controle) ? 1 : 0;
        $som = isset($request->som) ? 1 : 0;
        $luz_azul = isset($request->luz_azul) ? 1 : 0;
        $bebedouro = isset($request->bebedouro) ? 1 : 0;
        $armarios = isset($request->armarios) ? 1 : 0;




        DB::table('salas')->insert([

            'nome' => $request->input('nome'),
            'numero' => $request->input('numero'),
            'nr_lugares' => $request->input('nr_lugares'),
            'id_localizacao' => $request->input('id_localizacao'),
            'id_finalidade' => $request->input('tipo_sala'),
            'status_sala' => $request->input('status_sala'),
            'projetor' => $projetor,
            'quadro' => $quadro,
            'tela_projetor' => $tela_projetor,
            'ventilador' => $ventilador,
            'ar_condicionado' => $ar_condicionado,
            'computador' => $computador,
            'controle' => $controle,
            'som' => $som,
            'luz_azul' => $luz_azul,
            'bebedouro' => $bebedouro,
            'armarios' => $armarios,
            'tamanho_sala' => $request->input('tamanho_sala')

        ]);






        app('flasher')->addSuccess('O cadastro foi realizado com sucesso.');





        return redirect('gerenciar-salas');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $ativo = isset($request->status_sala) ? 1 : 0;

        $salaEditada = DB::table('salas')->where('id', $id)->select('*')->first();
        $salas = DB::table('salas AS s')
        ->leftJoin('tipo_finalidade_sala AS tf', 's.id_finalidade', 'tf.id')
        ->leftJoin('tipo_localizacao AS tl', 's.id_localizacao', 'tl.id')
        ->leftJoin('tipo_motivo AS tm', 's.id_motivo', 'tm.id')
        ->select(
            's.id',
            's.nome',
            's.numero',
            's.nr_lugares',
            's.projetor',
            's.quadro',
            's.tela_projetor',
            's.ventilador',
            's.ar_condicionado',
            's.computador',
            's.controle',
            's.som',
            's.luz_azul',
            's.bebedouro',
            's.armarios',
            's.tamanho_sala',
            's.status_sala',
            's.id_finalidade',
            's.id_localizacao',
            's.id_motivo',
            'tf.descricao',
            'tl.nome',
            'tm.tipo'
        )

        ->where('s.id', $id)
        ->get();

        $numerosExistem = DB::table('salas')->pluck('numero')->toArray();

    $tipo_finalidade_sala = DB::select('select * from tipo_finalidade_sala');
    $tipo_localizacao = DB::select('select * from tipo_localizacao');

    return view('salas/editar-salas', compact('salas', 'tipo_finalidade_sala', 'tipo_localizacao','salaEditada','numerosExistem'));


}


public function update(Request $request, string $id)
{



        $ativo = isset($request->checked) ? 1 : 0;




        $ar_condicionado = isset($request->ar_condicionado) ? 1 : 0;
        $projetor = isset($request->projetor) ? 1 : 0;
        $quadro = isset($request->quadro) ? 1 : 0;
        $tela_projetor = isset($request->tela_projetor) ? 1 : 0;
        $ventilador = isset($request->ventilador) ? 1 : 0;
        $computador = isset($request->computador) ? 1 : 0;
        $controle = isset($request->controle) ? 1 : 0;
        $som = isset($request->som) ? 1 : 0;
        $luz_azul = isset($request->luz_azul) ? 1 : 0;
        $bebedouro = isset($request->bebedouro) ? 1 : 0;
        $armarios = isset($request->armarios) ? 1 : 0;











        DB::table('salas')->where('id', $id)->UPDATE([
            'nome' => $request->input('nome'),
            'numero' => $request->input('numero'),
            'nr_lugares' => $request->input('nr_lugares'),
            'id_localizacao' => $request->input('id_localizacao'),
            'id_finalidade' => $request->input('id_finalidade'),
            'projetor' => $projetor,
            'quadro' => $quadro,
            'tela_projetor' => $tela_projetor,
            'ventilador' => $ventilador,
            'ar_condicionado' => $ar_condicionado,
            'computador' => $computador,
            'controle' => $controle,
            'som' => $som,
            'luz_azul' => $luz_azul,
            'bebedouro' => $bebedouro,
            'armarios' => $armarios,
            'status_sala' => $request->input('status_sala'),
            'tamanho_sala' => $request->input('tamanho_sala')
        ]);



        app('flasher')->addSuccess("Alterado com Sucesso");
        return redirect('gerenciar-salas');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $ids = DB::table('salas')->select('nome')->where('id', $id)->get();
        $teste = session()->get('usuario');

        $verifica = DB::table('historico_venus')->where('fato', $id)->count('fato');


        $data = date("Y-m-d H:i:s");






        DB::table('historico_venus')->insert([

            'id_usuario' => session()->get('usuario.id_usuario'),
            'data' => $data,
            'fato' => 0,
            'obs' => $ids

        ]);


        DB::table('salas')->where('id', $id)->delete();


        app('flasher')->addError('Excluido com sucesso.');
        return redirect('/gerenciar-salas');
    }
}
