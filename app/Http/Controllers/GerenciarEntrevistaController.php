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
            ->where('encaminhamento.id_tipo_encaminhamento', 1) // Filtro para apenas id_tipo_encaminhamento igual a 1
            ->select(
                'entrevistas.id_entrevistador',
                'entrevistas.status',
                'entrevistas.data',
                'entrevistas.hora',
                'encaminhamento.id',
                'tipo_encaminhamento.descricao',
                'encaminhamento.id_tipo_encaminhamento',
                'pessoa_pessoa.nome_completo as nome_pessoa',
                'pessoa_representante.nome_completo as nome_representante',
                'atendimentos.id_representante as id_representante',
                'tipo_entrevista.descricao as entrevista_descricao',
                'tipo_entrevista.sigla as entrevista_sigla',
                'tipo_encaminhamento.descricao as tipo_encaminhamento_descricao',
                DB::raw("'Aguardando agendar' as status")
            )
            ->get();


        $entrevistas = DB::table('entrevistas')->get();


        return view('entrevistas.gerenciar-entrevistas', compact('entrevistas', 'informacoes'));
    }




    // public function index()
    // {
    //     $informacoes = DB::table('encaminhamento')
    //         ->leftJoin('atendimentos', 'encaminhamento.id_atendimento', '=', 'atendimentos.id')
    //         ->leftJoin('pessoas as pessoa_atendente', 'atendimentos.id_usuario', '=', 'pessoa_atendente.id')
    //         ->leftJoin('pessoas as pessoa_representante', 'atendimentos.id_representante', '=', 'pessoa_representante.id')
    //         ->leftJoin('pessoas as pessoa_pessoa', 'atendimentos.id_assistido', '=', 'pessoa_pessoa.id')
    //         ->leftJoin('tipo_entrevista', 'encaminhamento.id_tipo_entrevista', '=', 'tipo_entrevista.id')
    //         ->leftJoin('entrevistas', 'encaminhamento.id', '=', 'entrevistas.id_encaminhamento')
    //         ->leftJoin('tipo_encaminhamento', 'encaminhamento.id_tipo_encaminhamento', '=', 'tipo_encaminhamento.id')
    //         ->where('encaminhamento.id_tipo_encaminhamento', 1) // Filtro para apenas id_tipo_encaminhamento igual a 1
    //         ->select(
    //             'entrevistas.id_entrevistador',
    //             'entrevistas.status',
    //             'entrevistas.data',
    //             'entrevistas.hora',
    //             'encaminhamento.id',
    //             'tipo_encaminhamento.descricao',
    //             'encaminhamento.id_tipo_encaminhamento',
    //             'pessoa_pessoa.nome_completo as nome_pessoa',
    //             'pessoa_representante.nome_completo as nome_representante', // Adicionando o nome do representante
    //             'atendimentos.id_representante as id_representante', // Adicionando o ID do representante
    //             'tipo_entrevista.descricao as entrevista_descricao',
    //             'tipo_entrevista.sigla as entrevista_sigla',
    //             'tipo_encaminhamento.descricao as tipo_encaminhamento_descricao',
    //             DB::raw("'Aguardando agendar' as status")
    //         )
    //         ->get();

    //     return view('entrevistas/gerenciar-entrevistas', compact('informacoes'));
    // }




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
    // Consulta para obter dados necessários
    $pessoas = DB::table('pessoas')->select('id', 'nome_completo')->get();
    $tipo_tratamento = DB::table('tipo_tratamento')->select('id', 'descricao AS tratamento_descricao')->get();
    $tipo_entrevista = DB::table('tipo_entrevista')->select('id', 'descricao AS descricao_entrevista')->get();
    $encaminhamento = DB::table('encaminhamento')->where('id', $id)->first(); // Obter o encaminhamento pelo ID

    // Coletar as informações do encaminhamento
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
                'encaminhamento.id_tipo_tratamento', // Seleciona o ID do tipo de tratamento
                'tipo_tratamento.descricao AS tratamento_descricao',
                'tipo_tratamento.sigla AS tratamento_sigla',
                'tipo_entrevista.descricao AS entrevista_descricao',
                'tipo_entrevista.sigla AS entrevista_sigla'
            )
            ->where('encaminhamento.id', $encaminhamento->id)
            ->distinct()
            ->first(); // Use first() para obter apenas um registro

        // Adicionar as informações coletadas ao array
        if ($info) {
            $informacoes[] = $info;
        }
    }

    return view('entrevistas/agendar-entrevista', compact('encaminhamento', 'informacoes', 'pessoas', 'tipo_tratamento', 'tipo_entrevista'));
}


public function store(Request $request)
{

    $idEncaminhamento = $request->input('id_encaminhamento');
    $idTipoEntrevista = $request->input('id_tipo_entrevista');
    $data = $request->input('data');
    $hora = $request->input('hora');
    $id_sala = $request->input('id_sala');
    $entrevistadorId = $request->input('entrevistador');

    DB::table('entrevistas')->insert([
        'id_encaminhamento' => $idEncaminhamento,
        'id_tipo_entrevista' => $idTipoEntrevista,
        'data' => $data,
        'hora' => $hora,
        'id_sala' => $id_sala,
        'id_entrevistador' => $entrevistadorId
    ]);

    return redirect()->route('gerenciamento')->with('success', 'Entrevista criada com sucesso!');
}









    public function show($id)
    {
        $pessoas = DB::select('select id as id, nome_completo from pessoas');
        $tipo_tratamento = DB::select('select id as id, descricao as tratamento_descricao from tipo_tratamento');
        $tipo_entrevista = DB::select('select id as id, descricao as descricao_entrevista from tipo_entrevista');
        $encaminhamento = DB::table('encaminhamento')->where('id', $id)->first(); // Obter o encaminhamento pelo ID

        // Coletar as informações do encaminhamento
        $informacoes = [];
        if ($encaminhamento) {
            $info = DB::table('encaminhamento')
                ->leftJoin('atendimentos', 'encaminhamento.id_atendimento', '=', 'atendimentos.id')
                ->leftJoin('pessoas as pessoa_atendente', 'atendimentos.id_usuario', '=', 'pessoa_atendente.id')
                ->leftJoin('pessoas as pessoa_pessoa', 'atendimentos.id_assistido', '=', 'pessoa_pessoa.id')
                ->leftJoin('tipo_tratamento', 'encaminhamento.id_tipo_tratamento', '=', 'tipo_tratamento.id')
                ->leftJoin('tipo_entrevista', 'encaminhamento.id_tipo_entrevista', '=', 'tipo_entrevista.id')
                ->select(
                    'atendimentos.id_assistido as id_pessoa',
                    'pessoa_pessoa.nome_completo as nome_pessoa',
                    'encaminhamento.id_tipo_tratamento', // Seleciona o ID do tipo de tratamento
                    'tipo_tratamento.descricao as tratamento_descricao',
                    'tipo_tratamento.sigla as tratamento_sigla',
                    'tipo_entrevista.descricao as entrevista_descricao',
                    'tipo_entrevista.sigla as entrevista_sigla'
                )
                ->where('encaminhamento.id', $encaminhamento->id)
                ->distinct()
                ->first(); // Use first() para obter apenas um registro

            // Adicionar as informações coletadas ao array
            if ($info) {
                $informacoes[] = $info;
            }
        }

        return view('entrevistas/visualizar-entrevista', compact('encaminhamento', 'informacoes', 'pessoas', 'tipo_tratamento', 'tipo_entrevista'));
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

        return redirect()->route('entrevistas.index');
    }
}
