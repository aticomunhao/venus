<?php

namespace App\Http\Controllers;

use Illuminate\Database\Query\JoinClause;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Redirect;
use PhpParser\Node\Expr\AssignOp\Coalesce;
use PhpParser\Node\Expr\BinaryOp\Coalesce as BinaryOpCoalesce;

class GerenciarEntrevistaController extends Controller
{

    public function index()
    {
    $informacoes = DB::table('encaminhamento')
    ->leftJoin('atendimentos', 'encaminhamento.id_atendimento', '=', 'atendimentos.id')
    ->leftJoin('entrevistas', 'encaminhamento.id', '=', 'entrevistas.id_encaminhamento')
    ->leftJoin('pessoas as pessoa_entrevistas', 'entrevistas.id_entrevistador', '=', 'pessoa_entrevistas.id')
    ->leftJoin('pessoas as pessoa_representante', 'atendimentos.id_representante', '=', 'pessoa_representante.id')
    ->leftJoin('pessoas as pessoa_pessoa', 'atendimentos.id_assistido', '=', 'pessoa_pessoa.id')
    ->leftJoin('tipo_entrevista', 'encaminhamento.id_tipo_entrevista', '=', 'tipo_entrevista.id')
    ->leftJoin('tipo_encaminhamento', 'encaminhamento.id_tipo_encaminhamento', '=', 'tipo_encaminhamento.id')
    ->where('encaminhamento.id_tipo_encaminhamento', 1)
    ->select(
        'entrevistas.id_entrevistador',
        DB::raw("CASE
                    WHEN entrevistas.status IS NULL THEN 'Aguardando agendamento'
                    ELSE entrevistas.status
                END as status"),
        'entrevistas.data',
        'entrevistas.hora',
        'encaminhamento.id as ide',
        'tipo_encaminhamento.descricao',
        'encaminhamento.id_tipo_encaminhamento',
        'pessoa_pessoa.nome_completo as nome_pessoa',
        'pessoa_entrevistas.nome_completo as nome_entrevistador',
        'pessoa_representante.nome_completo as nome_representante',
        'atendimentos.id_representante as id_representante',
        'tipo_entrevista.descricao as entrevista_descricao',
        'tipo_entrevista.sigla as entrevista_sigla',
        'tipo_encaminhamento.descricao as tipo_encaminhamento_descricao'
    )
    ->get();

return view('entrevistas.gerenciar-entrevistas', compact('informacoes'));
    }





    public function create($id)
    {

        $pessoas = DB::select('SELECT id, nome_completo FROM pessoas');
        $tipo_tratamento = DB::select('SELECT id, descricao AS tratamento_descricao FROM tipo_tratamento');
        $tipo_entrevista = DB::select('SELECT id, descricao AS descricao_entrevista FROM tipo_entrevista');
        $encaminhamento = DB::table('encaminhamento')->where('id', $id)->first();
        $entrevista = DB::table('entrevistas')->where('id', $id)->first();
        $salas = DB::table('salas')
        ->join('tipo_localizacao', 'salas.id_localizacao', '=', 'tipo_localizacao.id')
        ->select('salas.*', 'tipo_localizacao.nome AS nome_localizacao')
        ->get();



        if(request()->isMethod('post')) {

            $entrevistadorId = request()->input('entrevistador');
            DB::table('entrevistas')->where('id', $id)->update(['id_entrevistador' => $entrevistadorId]);


            return redirect()->route('sua.rota.aqui')->with('success', 'Entrevistador salvo com sucesso!');
        }


        $informacoes = [];
        if ($encaminhamento) {
            $info = DB::table('encaminhamento')
                ->leftJoin('atendimentos', 'encaminhamento.id_atendimento', '=', 'atendimentos.id')
                ->leftJoin('pessoas AS pessoa_atendente', 'atendimentos.id_usuario', '=', 'pessoa_atendente.id')
                ->leftJoin('pessoas as pessoa_representante', 'atendimentos.id_representante', '=', 'pessoa_representante.id')
                ->leftJoin('pessoas AS pessoa_pessoa', 'atendimentos.id_assistido', '=', 'pessoa_pessoa.id')
                ->leftJoin('tipo_tratamento', 'encaminhamento.id_tipo_tratamento', '=', 'tipo_tratamento.id')
                ->leftJoin('tipo_entrevista', 'encaminhamento.id_tipo_entrevista', '=', 'tipo_entrevista.id')
                ->select(
                    'atendimentos.id_assistido AS id_pessoa',
                    'pessoa_pessoa.nome_completo AS nome_pessoa',
                    'encaminhamento.id_tipo_tratamento',
                    'pessoa_representante.nome_completo as nome_representante',
                    'atendimentos.id_representante as id_representante',
                    'tipo_tratamento.descricao AS tratamento_descricao',
                    'tipo_tratamento.sigla AS tratamento_sigla',
                    'tipo_entrevista.descricao AS entrevista_descricao',
                    'tipo_entrevista.sigla AS entrevista_sigla'
                )
                ->where('encaminhamento.id', $encaminhamento->id)
                ->distinct()
                ->first();

            if ($info) {
                $informacoes[] = $info;
            }
        }


        return view('entrevistas/criar-entrevista', compact('salas','entrevista','encaminhamento', 'informacoes', 'pessoas', 'tipo_tratamento', 'tipo_entrevista'));
    }






public function store(Request $request,$id)
{

    $request->validate([
        'id_entrevistador' => 'required',
        'id_sala' => 'required',
        'data' => 'required|date',
        'hora' => 'required',
    ]);


    DB::table('entrevistas')->insert([
        'id_encaminhamento' => $id,
        'id_entrevistador' => $request->id_entrevistador,
        'id_sala' => $request->id_sala,
        'data' => $request->data,
        'hora' => $request->hora,
        'status' => 'Agendado',
    ]);



    return redirect()->route('gerenciamento')->with('success', 'Entrevista criada com sucesso!');
}



public function show($id)
{
    $entrevistas = DB::table('entrevistas AS entre')
        ->leftJoin('salas AS s', 'entre.id_sala', 's.id')
        ->leftJoin('tipo_localizacao as tpl', 's.id_localizacao', 'tpl.id')
        ->leftJoin('encaminhamento AS enc', 'entre.id_encaminhamento', 'enc.id')
        ->leftJoin('atendimentos as atd', 'enc.id_atendimento', 'atd.id')
        ->leftJoin('pessoas AS p', 'atd.id_assistido', 'p.id')
        ->select('p.nome_completo', 's.nome', 's.numero', 'tpl.nome as local','enc.id','entre.id','entre.id_entrevistador','entre.data','entre.hora')
        ->where('entre.id_encaminhamento', $id)
        ->first();

        if (!$entrevistas) {

        }


        $salas = DB::table('salas')->get();
        $encaminhamento = DB::table('encaminhamento')->find($id);
        $pessoas = DB::table('pessoas')->where('id', '=', $entrevistas->id_entrevistador)->get();





    return view('entrevistas.visualizar-entrevista', compact('entrevistas', 'encaminhamento', 'pessoas', 'salas'));
}








    public function edit($id)
    {

        $entrevistas = DB::table('entrevistas AS entre')
        ->leftJoin('salas AS s', 'entre.id_sala', 's.id')
        ->leftJoin('tipo_localizacao as tpl', 's.id_localizacao', 'tpl.id')
        ->leftJoin('encaminhamento AS enc', 'entre.id_encaminhamento', 'enc.id')
        ->leftJoin('atendimentos as atd', 'enc.id_atendimento', 'atd.id')
        ->leftJoin('pessoas AS p', 'atd.id_assistido', 'p.id')
        ->select('p.nome_completo', 's.nome', 's.numero', 'tpl.nome as local','enc.id','entre.id','entre.id_entrevistador','entre.data','entre.hora')
        ->where('entre.id_encaminhamento', $id)
        ->first();


        if (!$entrevistas) {

        }


      
        $encaminhamento = DB::table('encaminhamento')->find($id);
        $pessoas = DB::table('pessoas')->where('id', '=', $entrevistas->id_entrevistador)->get();
        $salas = DB::table('salas')
        ->join('tipo_localizacao', 'salas.id_localizacao', '=', 'tipo_localizacao.id')
        ->select('salas.*', 'tipo_localizacao.nome AS nome_localizacao')
        ->get();




    return view('entrevistas.editar-entrevista', compact('entrevistas', 'encaminhamento', 'pessoas', 'salas'));
}





    public function update(Request $request, $id)
    {


    $entrevista = DB::table('entrevistas AS entre')
        ->leftJoin('salas AS s', 'entre.id_sala', 's.id')
        ->leftJoin('tipo_localizacao as tpl', 's.id_localizacao', 'tpl.id')
        ->leftJoin('encaminhamento AS enc', 'entre.id_encaminhamento', 'enc.id')
        ->leftJoin('atendimentos as atd', 'enc.id_atendimento', 'atd.id')
        ->leftJoin('pessoas AS p', 'atd.id_assistido', 'p.id')
        ->select('p.nome_completo', 's.nome', 's.numero', 'tpl.nome as local','enc.id','entre.id','entre.id_entrevistador','entre.data','entre.hora')
        ->where('entre.id_encaminhamento', $id)
        ->first();


    if (!$entrevista) {



        DB::table('entrevistas')->where('id', $id)->update([
            'id_entrevistador' => $request->input('id_entrevistador'),
            'data' => $request->input('data'),
            'hora' => $request->input('hora'),
            'id_grupo' => $request->input('id_grupo'),
            'id_sala' => $request->input('id_sala'),



        ]);

        app('flasher')->addSuccess("Alterado com Sucesso");

        return redirect('gerenciar-entrevistas');
    }



        return redirect()->route('gerenciar-entrevistas', $id);
    }

    public function destroy($id)
    {

        DB::table('entrevistas')->where('id', $id)->delete();


        return redirect()->route('entrevistas.index');
    }
}
