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


class GerenciarEntrevistaevangelhoController extends Controller
{


    public function index(Request $request)
{
    $informacoes = DB::table('encaminhamento')
        ->leftJoin('atendimentos', 'encaminhamento.id_atendimento', '=', 'atendimentos.id')
        ->leftJoin('evangelho', 'encaminhamento.id', '=', 'evangelho.id_encaminhamento')
        ->leftJoin('pessoas as pessoa_pessoa', 'atendimentos.id_assistido', '=', 'pessoa_pessoa.id')
        ->leftJoin('tipo_encaminhamento', 'encaminhamento.id_tipo_encaminhamento', '=', 'tipo_encaminhamento.id')
        ->where('encaminhamento.id_tipo_encaminhamento', 1)
        ->where('encaminhamento.status_encaminhamento', '<>', 4)
        ->select(
            'evangelho.data',
            'evangelho.hora',
            'evangelho.status',
            'evangelho.qtd_adultos',
            'evangelho.qtd_criancas',
            'encaminhamento.id as ide',
            'tipo_encaminhamento.descricao as tipo_encaminhamento_descricao',
            'encaminhamento.id_tipo_encaminhamento',
            'pessoa_pessoa.nome_completo as nome_pessoa',
            'atendimentos.id_representante as id_representante'
        )
        ->get();


    foreach ($informacoes as $info) {
        $info->status = 'Aguardando agendamento';
    }

    return view('Evangelho.gerenciar-evangelho', compact('informacoes'));
}


public function create(Request $request, $id)
{
    $pessoas = DB::select('SELECT id, nome_completo FROM pessoas');
    $encaminhamento = DB::table('encaminhamento')->where('id', $id)->first();
    $evangelho = DB::table('evangelho')->where('id', $id)->first();

    $informacoes_banco = [];
    if ($encaminhamento) {
        $info = DB::table('encaminhamento')
            ->leftJoin('atendimentos', 'encaminhamento.id_atendimento', '=', 'atendimentos.id')
            ->leftJoin('pessoas AS pessoa_atendente', 'atendimentos.id_usuario', '=', 'pessoa_atendente.id')
            ->leftJoin('pessoas AS pessoa_pessoa', 'atendimentos.id_assistido', '=', 'pessoa_pessoa.id')
            ->select(
                'atendimentos.id_assistido AS id_pessoa',
                'pessoa_pessoa.nome_completo AS nome_pessoa',
                'atendimentos.id_representante as id_representante'
            )
            ->where('encaminhamento.id', $encaminhamento->id)
            ->distinct()
            ->first();

        if ($info) {
            $informacoes_banco[] = $info;
        } else {

        }
    }

    $informacoes_request = [
        'data' => $request->input('data'),
        'hora' => $request->input('hora'),
        'status' => $request->input('status'),
        'qtd_adultos' => $request->input('qtd_adultos'),
        'qtd_criancas' => $request->input('qtd_criancas')
    ];


    $informacoes = array_merge($informacoes_banco, $informacoes_request);

    return view('Evangelho/criar-evangelho', compact('encaminhamento', 'evangelho', 'informacoes', 'pessoas'));
}








public function store(Request $request,$id)
{

    $request->validate([
        'data' => 'required|date',
        'hora' => 'required',
    ]);


    DB::table('evangelho')->insert([
        'id_encaminhamento' => $id,
        'id_grupo' => $request->id_grupo,
        'qtd_adultos' => $request->qtd_adultos,
        'qtd_criancas' => $request->qtd_criancas,
        'data' => $request->data,
        'hora' => $request->hora,
        'status' => 'Agendado',
    ]);



    return redirect()->route('start')->with('success', 'Entrevista evangelho criada com sucesso!');
}





public function show($id)
{
    $evangelho = DB::table('envangelho AS evan')
        ->leftJoin('encaminhamento AS enc', 'entre.id_encaminhamento', 'enc.id')
        ->leftJoin('atendimentos as atd', 'enc.id_atendimento', 'atd.id')
        ->leftJoin('pessoas AS p', 'atd.id_assistido', 'p.id')
        ->select('p.nome_completo','enc.id','evan.id','evan.id_grupo','evan.data','evan.hora','evan.qtd_adultos','evan.qtd_criancas')
        ->where('evan.id_encaminhamento', $id)
        ->first();

        if (!$evangelho) {

        }


        $encaminhamento = DB::table('encaminhamento')->find($id);






    return view('evangelho/visualizar-evangelho', compact('evangelho', 'encaminhamento', 'pessoas'));
}







    public function edit($id)
    {

        $evangelho = DB::table('evangelho AS evan')
        ->leftJoin('encaminhamento AS enc', 'evan.id_encaminhamento', 'evan.id')
        ->leftJoin('atendimentos as atd', 'enc.id_atendimento', 'atd.id')
        ->leftJoin('pessoas AS p', 'atd.id_assistido', 'p.id')
        ->select('p.nome_completo','evan.staus','evan.qtd_adultos','evan.qtd.criancas','enc.id','evan.data','evan.hora')
        ->where('entre.id_encaminhamento', $id)
        ->first();


        if (!$evangelho) {

        }




        $encaminhamento = DB::table('encaminhamento')->find($id);





    return view('evangelho/editar-evangelho', compact('evangelho', 'encaminhamento', 'pessoas'));
}





public function update(Request $request, $id)
{

    $evangelho = DB::table('evangelho AS evan')
        ->leftJoin('encaminhamento AS enc', 'evan.id_encaminhamento', 'evan.id')
        ->leftJoin('atendimentos as atd', 'enc.id_atendimento', 'atd.id')
        ->leftJoin('pessoas AS p', 'atd.id_assistido', 'p.id')
        ->select('p.nome_completo','evan.staus','evan.qtd_adultos','evan.qtd.criancas','enc.id','evan.data','evan.hora')
        ->where('entre.id_encaminhamento', $id)
        ->first();



    if (!$evangelho) {

        app('flasher')->addError("Entrevista não encontrada");
        return redirect('gerenciar-evangelho');
    }

    DB::table('evangelho')
    ->where('id_encaminhamento', $id)
    ->update([
        'data' => $request->input('data'),
        'hora' => $request->input('hora'),
        'id_grupo' => $request->input('id_grupo'),
        'qtd_adultos' => $request->input('qtd_adultos'),
        'qtd_criancas' => $request->input('qtd_criancas'),

    ]);




    app('flasher')->addSuccess("Entrevista alterada com sucesso");


    return redirect('gerenciar-evangelho');
}




public function finalizar($id)
{


    DB::table('entrevistas')
        ->where('id_encaminhamento', $id)
        ->update(['status' => 'Entrevistado']);

    return redirect()->route('gerenciamento')->with('success', 'Entrevista finalizada com sucesso!');
}

public function inativar($id){


    $data = date("Y-m-d H:i:s");

    DB::table('historico_venus')->insert([

        'id_usuario' => session()->get('usuario.id_usuario'),
        'data' => $data,
        'fato' => 37,
        'obs' => $id

    ]);

   $entrevistas= DB::table('entrevistas')->where('id_encaminhamento','=', $id)->first();


if(!is_null($entrevistas) and $entrevistas->status =='Agendado'){

    DB::table('entrevistas')
    ->where('id_encaminhamento','=', $id)
    ->delete();
}

    DB::table('encaminhamento')
        ->where('id', $id)
        ->update(['status_encaminhamento' => 4]);


    return redirect()->route('gerenciamento')->with('danger', 'Entrevista inativada!');
}

}
