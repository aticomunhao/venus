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
        ->leftJoin('pessoas as pessoa_pessoa', 'atendimentos.id_assistido', '=', 'pessoa_pessoa.id')
        ->leftJoin('tipo_tratamento', 'encaminhamento.id_tipo_tratamento', '=', 'tipo_tratamento.id')
        ->leftJoin('tipo_entrevista', 'encaminhamento.id_tipo_entrevista', '=', 'tipo_entrevista.id')
        ->leftJoin('entrevistas', 'encaminhamento.id', '=', 'entrevistas.id_encaminhamento')
        ->where('tipo_tratamento.sigla', '!=', 'PTD')
        ->select(
            'encaminhamento.id',
            'pessoa_pessoa.nome_completo as nome_pessoa',
            'tipo_tratamento.descricao as tratamento_descricao',
            'tipo_tratamento.sigla as tratamento_sigla',
            'tipo_entrevista.descricao as entrevista_descricao',
            'tipo_entrevista.sigla as entrevista_sigla',
            DB::raw("'Aguardando agendar' as status")
        )
        ->get();

    return view('entrevistas/gerenciar-entrevistas', compact('informacoes'));
}



public function create($id)
{
   // Consulta para obter dados necessários
   $pessoas = DB::select('SELECT id, nome_completo FROM pessoas');
   $tipo_tratamento = DB::select('SELECT id, descricao AS tratamento_descricao FROM tipo_tratamento');
   $tipo_entrevista = DB::select('SELECT id, descricao AS descricao_entrevista FROM tipo_entrevista');
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


   return view('entrevistas/criar-entrevista', compact('encaminhamento', 'informacoes', 'pessoas', 'tipo_tratamento', 'tipo_entrevista'));
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

        return redirect()->route('entrevistas.index');
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
