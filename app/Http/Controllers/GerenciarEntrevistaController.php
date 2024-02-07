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
            ->leftjoin('pessoas as pessoa_atendente', 'atendimentos.id_usuario', '=', 'pessoa_atendente.id')
            ->leftjoin('pessoas as pessoa_pessoa', 'atendimentos.id_assistido', '=', 'pessoa_pessoa.id')
            ->leftjoin('tipo_tratamento', 'encaminhamento.id_tipo_tratamento', '=', 'tipo_tratamento.id')
            ->leftjoin('tipo_entrevista', 'encaminhamento.id_tipo_entrevista', '=', 'tipo_entrevista.id')
            ->select(

                        'encaminhamento.id',
            'pessoa_pessoa.nome_completo as nome_pessoa',
            'tipo_tratamento.descricao as tratamento_descricao',
            'tipo_tratamento.sigla as tratamento_sigla',
            'tipo_entrevista.descricao as entrevista_descricao',
            'tipo_entrevista.sigla as entrevista_sigla'
        )
                        // 'pessoa_atendente.nome_completo as nome_atendente',
                // 'pessoa_pessoa.nome_completo as nome_pessoa',
                // 'tipo_tratamento.descricao as tratamento_descricao',
                // 'tipo_tratamento.sigla as tratamento_sigla',
                // 'tipo_entrevista.descricao as entrevista_descricao',
                // 'tipo_entrevista.sigla as entrevista_sigla'


            ->get();

            return view('entrevistas/gerenciar-entrevistas', compact('informacoes'));
            }






    public function create()
    {
        return view('entrevistas.create');
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
