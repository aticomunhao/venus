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


    public function verificarChaveEstrangeira($nomeTabela, $nomeColuna)
    {
        // Consulta para verificar se a coluna é usada como chave estrangeira em outras tabelas
        $resultado = DB::select("
            SELECT
                tc.table_name AS tabela_referenciada,
                ccu.column_name AS coluna_referenciada
            FROM
                information_schema.table_constraints AS tc
            JOIN information_schema.key_column_usage AS kcu
                ON tc.constraint_name = kcu.constraint_name
            JOIN information_schema.constraint_column_usage AS ccu
                ON ccu.constraint_name = tc.constraint_name
            WHERE
                tc.constraint_type = 'FOREIGN KEY'
                AND kcu.table_name = '$nomeTabela'
                AND kcu.column_name = '$nomeColuna';
        ");

        // Retorna o resultado da consulta
        return $resultado;
    }

    public function index(Request $request)
    {
    $informacoes = DB::table('encaminhamento')
    ->leftJoin('atendimentos', 'encaminhamento.id_atendimento', '=', 'atendimentos.id')
    ->leftJoin('entrevistas', 'encaminhamento.id', '=', 'entrevistas.id_encaminhamento')
    ->leftJoin('salas AS s', 'entrevistas.id_sala', 's.id')
    ->leftJoin('tipo_localizacao as tpl', 's.id_localizacao', 'tpl.id')
    ->leftJoin('pessoas as pessoa_entrevistas', 'entrevistas.id_entrevistador', '=', 'pessoa_entrevistas.id')
    ->leftJoin('pessoas as pessoa_representante', 'atendimentos.id_representante', '=', 'pessoa_representante.id')
    ->leftJoin('pessoas as pessoa_pessoa', 'atendimentos.id_assistido', '=', 'pessoa_pessoa.id')
    ->leftJoin('tipo_entrevista', 'encaminhamento.id_tipo_entrevista', '=', 'tipo_entrevista.id')
    ->leftJoin('tipo_encaminhamento', 'encaminhamento.id_tipo_encaminhamento', '=', 'tipo_encaminhamento.id')
    ->where('encaminhamento.id_tipo_encaminhamento', 1)
    ->where('encaminhamento.status_encaminhamento','<>',4 )
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
        'tipo_encaminhamento.descricao as tipo_encaminhamento_descricao',
        's.nome as local'
    );
$i = 0;
$pesquisaNome = null;
$pesquisaStatus = 0;
$pesquisaValue = 0;
    if($request->nome_pesquisa){

        $informacoes = $informacoes->where('pessoa_pessoa.nome_completo', 'ilike', "%$request->nome_pesquisa%");
        $pesquisaNome = $request->nome_pesquisa;
    }
    if($request->status != 1){

        if($request->status == 2){
            $informacoes->where('entrevistas.status', "ilike", "Agendado");
            $pesquisaValue = 2;
        }


    elseif($request->status == 3){
        $informacoes->where('entrevistas.status', "ilike", "Entrevistado");
    $pesquisaValue = 3;
}

elseif($request->status == 4){
    $informacoes->where('entrevistas.status', "ilike", "Aguardando entrevistador");
$pesquisaValue = 4;
}


    }

    $informacoes = $informacoes->orderBy('status', 'asc')->orderBy('pessoa_pessoa.nome_completo')->get();

    if($request->status == 1){
        $info = [];
        foreach ($informacoes as $dia) {
            $info[] = $dia;
        }
foreach($info as $check){
    if($check->status != "Aguardando agendamento"){
        unset($info[$i]);
    }
$i = $i +1;
}
$informacoes = $info;
$pesquisaStatus = "Aguardando agendamento";
$pesquisaValue = 1;
}





return view('entrevistas.gerenciar-entrevistas', compact('informacoes', 'pesquisaNome', 'pesquisaStatus', 'pesquisaValue'));
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
        'status' => 'Aguardando entrevistador',
    ]);



    return redirect()->route('gerenciamento')->with('success', 'Entrevista criada com sucesso!');
}

public function criar($id)
{


    $pessoas = DB::select('SELECT id, nome_completo FROM pessoas');
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




        return view('entrevistas.agendar-entrevistador', compact('entrevistas', 'encaminhamento', 'pessoas', 'salas'));
    }






public function incluir(Request $request,$id)
{

$request->validate([
    'id_entrevistador' => 'required',

]);


DB::table('entrevistas')->insert([
    'id_entrevistador' => $request->id_entrevistador,
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



        $entrevistador=DB::table('pessoas')->get();
        $encaminhamento = DB::table('encaminhamento')->find($id);
        $pessoas = DB::table('pessoas')->where('id', '=', $entrevistas->id_entrevistador)->get();
        $salas = DB::table('salas')
        ->join('tipo_localizacao', 'salas.id_localizacao', '=', 'tipo_localizacao.id')
        ->select('salas.*', 'tipo_localizacao.nome AS nome_localizacao')
        ->get();



    return view('entrevistas.editar-entrevista', compact('entrevistador','entrevistas', 'encaminhamento', 'pessoas', 'salas'));
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

        app('flasher')->addError("Entrevista não encontrada");
        return redirect('gerenciar-entrevistas');
    }

    DB::table('entrevistas')
    ->where('id_encaminhamento', $id)
    ->update([
        'id_entrevistador' => $request->input('id_entrevistador'),
        'data' => $request->input('data'),
        'hora' => $request->input('hora'),
        'id_sala' => $request->id_sala,
        'id_grupo' => $request->input('id_grupo'),
        'id_sala' => $request->input('id_sala'),
    ]);




    app('flasher')->addSuccess("Entrevista alterada com sucesso");


    return redirect('gerenciar-entrevistas');
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
