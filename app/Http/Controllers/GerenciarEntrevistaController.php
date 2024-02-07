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
        $encaminhamentos = DB::table('encaminhamento')->get();


        return view('entrevistas.gerenciar-entrevistas', ['encaminhamentos' => $encaminhamentos]);
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
