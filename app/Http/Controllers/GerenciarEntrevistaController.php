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

        $lista = DB::table('entrevistas AS entre')
            ->select(
                'enc.id AS id',
                'p.nome_completo',
                'tpe.descricao AS descricao1',
                'tpe.sigla AS sigla1',
                'tt.descricao AS descricao2',
                'tt.sigla AS sigla2',
                'g.nome AS nome_grupo'
            )
            ->join('encaminhamento AS enc', 'entre.id', '=', 'enc.id')
            ->join('pessoas AS p', 'p.id', '=', 'enc.id')
            ->join('tipo_encaminhamento AS tpe', 'tpe.id', '=', 'enc.id')
            ->join('tipo_tratamento AS tt', 'tt.id', '=', 'enc.id')
            ->join('grupo AS g', 'g.id', '=', 'entre.id')
            ->join('atendimentos AS at', 'at.id', '=', 'enc.id')
            ->where('tt.sigla', '<>', 'ptd')
            ->whereNotNull('enc.id_tipo_entrevista')
            ->get();



        return view('entrevistas/gerenciar-entrevistas', compact('lista'));
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
