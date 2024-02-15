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
    ->leftJoin('pessoas as pessoa_atendente', 'atendimentos.id_usuario', '=', 'pessoa_atendente.id')
    ->leftJoin('pessoas as pessoa_representante', 'atendimentos.id_representante', '=', 'pessoa_representante.id')
    ->leftJoin('pessoas as pessoa_pessoa', 'atendimentos.id_assistido', '=', 'pessoa_pessoa.id')
    ->leftJoin('tipo_entrevista', 'encaminhamento.id_tipo_entrevista', '=', 'tipo_entrevista.id')
    ->leftJoin('entrevistas', 'encaminhamento.id', '=', 'entrevistas.id_encaminhamento')
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
        'encaminhamento.id',
        'tipo_encaminhamento.descricao',
        'encaminhamento.id_tipo_encaminhamento',
        'pessoa_pessoa.nome_completo as nome_pessoa',
        'pessoa_atendente.nome_completo as nome_entrevistador',
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
        // Carregue as informações necessárias
        $pessoas = DB::select('SELECT id, nome_completo FROM pessoas');
        $tipo_tratamento = DB::select('SELECT id, descricao AS tratamento_descricao FROM tipo_tratamento');
        $tipo_entrevista = DB::select('SELECT id, descricao AS descricao_entrevista FROM tipo_entrevista');
        $encaminhamento = DB::table('encaminhamento')->where('id', $id)->first();
        $entrevista = DB::table('entrevistas')->where('id', $id)->first();
        $salas = DB::table('salas')
        ->join('tipo_localizacao', 'salas.id_localizacao', '=', 'tipo_localizacao.id')
        ->select('salas.*', 'tipo_localizacao.nome AS nome_localizacao')
        ->get();


        // Verifique se o formulário foi enviado
        if(request()->isMethod('post')) {
            // Salve o entrevistador na tabela de entrevistas
            $entrevistadorId = request()->input('entrevistador');
            DB::table('entrevistas')->where('id', $id)->update(['id_entrevistador' => $entrevistadorId]);

            // Redirecione para onde for necessário após a atualização do entrevistador
            return redirect()->route('sua.rota.aqui')->with('success', 'Entrevistador salvo com sucesso!');
        }

        // Carregue outras informações necessárias para exibição no formulário
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

        // Retorne a view do formulário de criação de entrevista
        return view('entrevistas/criar-entrevista', compact('salas','entrevista','encaminhamento', 'informacoes', 'pessoas', 'tipo_tratamento', 'tipo_entrevista'));
    }









public function agen($id)
{

    $pessoas = DB::table('pessoas')->select('id', 'nome_completo')->get();
    $tipo_tratamento = DB::table('tipo_tratamento')->select('id', 'descricao AS tratamento_descricao')->get();
    $tipo_entrevista = DB::table('tipo_entrevista')->select('id', 'descricao AS descricao_entrevista')->get();
    $encaminhamento = DB::table('encaminhamento')->where('id', $id)->first();


    $informacoes = [];
    if ($encaminhamento) {
        $info = DB::table('encaminhamento')
            ->leftJoin('atendimentos', 'encaminhamento.id_atendimento', '=', 'atendimentos.id')
            ->leftJoin('pessoas AS pessoa_atendente', 'atendimentos.id_usuario', '=', 'pessoa_atendente.id')
            ->leftJoin('pessoas AS pessoa_pessoa', 'atendimentos.id_assistido', '=', 'pessoa_pessoa.id')
            ->leftJoin('tipo_tratamento', 'encaminhamento.id_tipo_tratamento', '=', 'tipo_tratamento.id')
            ->leftJoin('tipo_entrevista', 'encaminhamento.id_tipo_entrevista', '=', 'tipo_entrevista.id')
            ->select(
                'atendimentos.id_assistido AS id_pessoa',
                'pessoa_pessoa.nome_completo AS nome_pessoa',
                'encaminhamento.id_tipo_tratamento',
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

    return view('entrevistas/agendar-entrevista', compact('encaminhamento', 'informacoes', 'pessoas', 'tipo_tratamento', 'tipo_entrevista'));
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
    // Obter informações relacionadas ao encaminhamento
    $encaminhamento = DB::table('encaminhamento')->where('id', $id)->first();

    // Obter informações dos atendimentos, pessoas, tipos de tratamento e tipos de entrevista
    $info = DB::table('encaminhamento')
        ->leftJoin('atendimentos', 'encaminhamento.id_atendimento', '=', 'atendimentos.id')
        ->leftJoin('pessoas as pessoa_atendente', 'atendimentos.id_usuario', '=', 'pessoa_atendente.id')
        ->leftJoin('pessoas as pessoa_pessoa', 'atendimentos.id_assistido', '=', 'pessoa_pessoa.id')
        ->leftJoin('tipo_tratamento', 'encaminhamento.id_tipo_tratamento', '=', 'tipo_tratamento.id')
        ->leftJoin('tipo_entrevista', 'encaminhamento.id_tipo_entrevista', '=', 'tipo_entrevista.id')
        ->select(
            'atendimentos.id_assistido as id_pessoa',
            'pessoa_pessoa.nome_completo as nome_pessoa',
            'encaminhamento.id_tipo_tratamento',
            'tipo_tratamento.descricao as tratamento_descricao',
            'tipo_tratamento.sigla as tratamento_sigla',
            'tipo_entrevista.descricao as entrevista_descricao',
            'tipo_entrevista.sigla as entrevista_sigla'
        )
        ->where('encaminhamento.id', $encaminhamento->id)
        ->distinct()
        ->first();

    // Obter todas as pessoas
    $pessoas = DB::table('pessoas')->select('id', 'nome_completo')->get();

    // Obter todos os tipos de tratamento
    $tipo_tratamento = DB::table('tipo_tratamento')->select('id', 'descricao as tratamento_descricao')->get();

    // Obter todos os tipos de entrevista
    $tipo_entrevista = DB::table('tipo_entrevista')->select('id', 'descricao as descricao_entrevista')->get();

    return view('entrevistas.visualizar-entrevista', compact('encaminhamento', 'info', 'pessoas', 'tipo_tratamento', 'tipo_entrevista'));
}








    public function edit($id)
    {


        return view('.edit', compact('entrevista'));
    }

    public function update(Request $request, $id)
    {

        return redirect()->route('entrevistas.show', $id);
    }

    public function destroy($id)
    {

        DB::table('entrevistas')->where('id', $id)->delete();


        return redirect()->route('entrevistas.index');
    }
}
