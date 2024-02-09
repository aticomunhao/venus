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





    public function create()
    {

        $pessoas = DB::select('select id as id, nome_completo from pessoas');
        $tipo_tratamento = DB::select('select id as id, descricao as tratamento_descricao from tipo_tratamento');
        $tipo_entrevista = DB::select('select id as id, descricao as descricao_entrevista from tipo_entrevista');



        $informacoes = DB::table('encaminhamento')
            ->leftJoin('atendimentos', 'encaminhamento.id_atendimento', '=', 'atendimentos.id')
            ->leftJoin('pessoas as pessoa_atendente', 'atendimentos.id_usuario', '=', 'pessoa_atendente.id')
            ->leftJoin('pessoas as pessoa_pessoa', 'atendimentos.id_assistido', '=', 'pessoa_pessoa.id')
            ->leftJoin('tipo_tratamento', 'encaminhamento.id_tipo_tratamento', '=', 'tipo_tratamento.id')
            ->leftJoin('tipo_entrevista', 'encaminhamento.id_tipo_entrevista', '=', 'tipo_entrevista.id')
            ->select(
                'pessoa_pessoa.nome_completo as nome_pessoa',
                'tipo_tratamento.descricao as tratamento_descricao',
                'tipo_tratamento.sigla as tratamento_sigla',
                'tipo_entrevista.descricao as entrevista_descricao',
                'tipo_entrevista.sigla as entrevista_sigla'
            )
            ->distinct()
            ->get();

        return view('entrevistas/criar-entrevista', compact('informacoes','pessoas','tipo_tratamento','tipo_entrevista'));
    }







    public function store(Request $request)
    {

        return redirect()->route('entrevistas.index');
    }

    public function show($id)
    {


        return view('entrevistas.show', compact('entrevista'));
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
