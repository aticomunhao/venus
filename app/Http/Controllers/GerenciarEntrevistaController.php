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
            ->leftJoin('pessoas as pessoa_representante', 'atendimentos.id_representante', '=', 'pessoa_representante.id')
            ->leftJoin('pessoas as pessoa_pessoa', 'atendimentos.id_assistido', '=', 'pessoa_pessoa.id')
            ->leftJoin('tipo_entrevista', 'encaminhamento.id_tipo_entrevista', '=', 'tipo_entrevista.id')
            ->leftJoin('tipo_encaminhamento', 'encaminhamento.id_tipo_encaminhamento', '=', 'tipo_encaminhamento.id')
            ->leftJoin('membro', 'entrevistas.id_entrevistador', '=', 'membro.id')
            ->leftJoin('associado', 'membro.id_associado', '=', 'associado.id')
            ->leftJoin('pessoas as pessoa_entrevistador', 'associado.id_pessoa', '=', 'pessoa_entrevistador.id')
            ->leftJoin('tipo_status_entrevista as tse', 'entrevistas.status', '=', 'tse.id')
            ->where('encaminhamento.id_tipo_encaminhamento', 1)
            ->where('encaminhamento.status_encaminhamento', '<>', 4)
            ->whereNotIn('tipo_entrevista.id', [8]) // Exclui o tipo de entrevista 8
            ->whereBetween('tipo_entrevista.id', [1, 7]) // Inclui os tipos de entrevista de 1 a 7
            ->select(
                'entrevistas.id_entrevistador',
                DB::raw("CASE
                        WHEN entrevistas.status IS NULL THEN 1
                        ELSE entrevistas.status
                    END as status"),
                'tse.descricao as d1',
                'entrevistas.data',
                'entrevistas.hora',
                'encaminhamento.id as ide',
                'tipo_encaminhamento.descricao',
                'encaminhamento.id_tipo_encaminhamento',
                'pessoa_pessoa.nome_completo as nome_pessoa',
                'pessoa_entrevistador.nome_completo as nome_entrevistador',
                'pessoa_representante.nome_completo as nome_representante',
                'atendimentos.id_representante as id_representante',
                'tipo_entrevista.descricao as entrevista_descricao',
                'tipo_entrevista.id as id_tipo_entrevista',
                'tipo_entrevista.sigla as entrevista_sigla',
                'tipo_encaminhamento.descricao as tipo_encaminhamento_descricao',
                's.nome as local',
                's.numero',
                'pessoa_entrevistador.nome_completo as nome_entrevistador'
            );

   
          

        $i = 0;
        $pesquisaNome = null;
        $pesquisaStatus = 0;
        $pesquisaValue = $request->status == null ? 'limpo' : $request->status;

        if (session()->get('usuario.setor') == 38) {
            $informacoes = $informacoes->where('encaminhamento.id_tipo_entrevista', 5);
        }
        if (session()->get('usuario.setor') == 7) {
            $informacoes = $informacoes->where('encaminhamento.id_tipo_entrevista', 3);
        }

        if ($request->nome_pesquisa) {

            $informacoes = $informacoes->where('pessoa_pessoa.nome_completo', 'ilike', "%$request->nome_pesquisa%");
            $pesquisaNome = $request->nome_pesquisa;
        }
        if ($request->status != 1 and $pesquisaValue != 'limpo') {


            $informacoes->where('entrevistas.status', $pesquisaValue);
        }


        $informacoes = $informacoes->orderBy('entrevistas.status')->orderBy('pessoa_pessoa.nome_completo')->get();

        // Mapear os status para a ordem desejada
        $statusOrder = [
            1 => 1,
            2 => 2,
            3 => 3,
            4 => 4,
            5 => 5,
            6 => 6
        ];

        // Ordenar os resultados de acordo com o mapeamento de status
        $informacoes = $informacoes->sortBy(function ($item) use ($statusOrder) {
            return $statusOrder[$item->status] ?? PHP_INT_MAX;
        });






        if ($request->status == 1) {
            $info = [];
            foreach ($informacoes as $dia) {
                $info[] = $dia;
            }
            foreach ($info as $check) {
                if ($check->status != 1) {
                    unset($info[$i]);
                }
                $i = $i + 1;
            }
            $informacoes = $info;
            $pesquisaStatus = "Aguardando agendamento";
            $pesquisaValue = 1;
        }




        $status = DB::table('tipo_status_entrevista')->orderBy('id', 'ASC')->get();


        return view('Entrevistas.gerenciar-entrevistas', compact('informacoes', 'pesquisaNome', 'pesquisaStatus', 'pesquisaValue', 'status'));
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


        return view('Entrevistas/criar-entrevista', compact('salas', 'entrevista', 'encaminhamento', 'informacoes', 'pessoas', 'tipo_tratamento', 'tipo_entrevista'));
    }





    public function store(Request $request, $id)
    {




        $request->validate([
            'id_sala' => 'required',
            'data' => 'required|date',
            'hora' => 'required',
        ]);


        DB::table('entrevistas')->insert([
            'id_encaminhamento' => $id,
            'id_sala' => $request->id_sala,
            'data' => $request->data,
            'hora' => $request->hora,
            'status' => 2,
        ]);




        return redirect()->route('gerenciamento')->with('success', 'Entrevista criada com sucesso!');
    }

    public function criar($id)
    {




        $associado = DB::table('membro')->select('membro.id',)
            ->leftJoin('associado', 'membro.id_associado', 'associado.id')->get();

        $entrevistas = DB::table('entrevistas AS entre')

            ->leftJoin('salas AS s', 'entre.id_sala', 's.id')
            ->leftJoin('pessoas as pessoa_entrevistador', 'entre.id_entrevistador', '=', 'pessoa_entrevistador.id')
            ->leftJoin('tipo_localizacao as tpl', 's.id_localizacao', 'tpl.id')
            ->leftJoin('encaminhamento AS enc', 'entre.id_encaminhamento', 'enc.id')
            ->leftJoin('atendimentos as atd', 'enc.id_atendimento', 'atd.id')
            ->leftJoin('pessoas AS pessoa_assitido', 'atd.id_assistido', 'pessoa_assitido.id')
            ->select(
                'pessoa_assitido.nome_completo',
                's.nome',
                's.numero',
                'tpl.nome as local',
                'enc.id',
                'entre.id',
                'entre.id_entrevistador',
                'entre.data',
                'entre.hora',
                'pessoa_entrevistador.nome_completo as nome_completo_pessoa_entrevistador'
            )
            ->where('entre.id_encaminhamento', $id)
            ->first();



        if (!$entrevistas) {
        }

        $salas = DB::table('salas')->get();
        $encaminhamento = DB::table('encaminhamento')->find($id);
        $pessoas = DB::table('pessoas')->get();
        $membros = DB::table('membro')
            ->join('associado', 'membro.id_associado', '=', 'associado.id')
            ->join('pessoas', 'associado.id_pessoa', '=', 'pessoas.id')
            ->select('membro.*', 'pessoas.nome_completo')
            ->distinct('membro.id_associado')
            ->get();


        $encaminhamento = DB::table('encaminhamento')->find($id);

        // Verificando se o tipo de entrevista é 3 (tipo_entrevista 3, afe)
        if ($encaminhamento && $encaminhamento->id_tipo_entrevista === 3) {
            // Obtendo informações dos atendentes (caso o tipo de entrevista seja afe)
            $membros = DB::table('membro')
                ->join('associado', 'membro.id_associado', '=', 'associado.id')
                ->join('pessoas', 'associado.id_pessoa', '=', 'pessoas.id')
                ->select('membro.*', 'pessoas.nome_completo')
                ->distinct('membro.id_associado')
                ->where('membro.id_funcao', 5)
                ->get();
        }
        return view('Entrevistas.agendar-entrevistador', compact('membros', 'entrevistas', 'encaminhamento', 'pessoas', 'salas'));
    }



    public function incluir(Request $request, string $id)
    {


        $a = DB::table('encaminhamento')->where('id', $id)->first();

     
        if($a->id_tipo_entrevista == 3){
            DB::table('entrevistas')->where('id_encaminhamento', $id)->update([
                'id_entrevistador' => $request->input('id_entrevistador'),
                'status' => 4,
            ]);
        }
       
        else{
            DB::table('entrevistas')->where('id_encaminhamento', $id)->update([
                'id_entrevistador' => $request->input('id_entrevistador'),
                'status' => 3,
            ]);
        }
       






        return redirect()->route('gerenciamento')->with('success', 'O cadastro foi realizado com sucesso!');
    }





    public function show($id)
    {
        $entrevistas = DB::table('entrevistas AS entre')
            ->leftJoin('salas AS s', 'entre.id_sala', 's.id')
            ->leftJoin('tipo_localizacao as tpl', 's.id_localizacao', 'tpl.id')
            ->leftJoin('encaminhamento AS enc', 'entre.id_encaminhamento', 'enc.id')
            ->leftJoin('atendimentos as atd', 'enc.id_atendimento', 'atd.id')
            ->leftJoin('pessoas AS p', 'atd.id_assistido', 'p.id')
            ->select('p.nome_completo', 's.nome', 's.numero', 'tpl.nome as local', 'enc.id', 'entre.id', 'entre.id_entrevistador', 'entre.data', 'entre.hora',)
            ->where('entre.id_encaminhamento', $id)
            ->first();

        if (!$entrevistas) {
        }

        $salas = DB::table('salas')->get();
        $encaminhamento = DB::table('encaminhamento')->find($id);
        $membros = DB::table('membro')
            ->join('associado', 'membro.id_associado', '=', 'associado.id')
            ->join('pessoas', 'associado.id_pessoa', '=', 'pessoas.id')
            ->select('membro.*', 'pessoas.nome_completo AS nome_entrevistador')
            ->where('membro.id', $entrevistas->id_entrevistador)
            ->first();


        return view('Entrevistas.visualizar-entrevista', compact('membros', 'entrevistas', 'encaminhamento',  'salas'));
    }





    public function edit($id)
    {

        $entrevistas = DB::table('entrevistas AS entre')
            ->leftJoin('salas AS s', 'entre.id_sala', 's.id')
            ->leftJoin('tipo_localizacao as tpl', 's.id_localizacao', 'tpl.id')
            ->leftJoin('encaminhamento AS enc', 'entre.id_encaminhamento', 'enc.id')
            ->leftJoin('atendimentos as atd', 'enc.id_atendimento', 'atd.id')
            ->leftJoin('pessoas AS p', 'atd.id_assistido', 'p.id')
            ->select(
                'p.nome_completo',
                's.nome',
                's.numero',
                's.id as sala_id',
                'tpl.nome as local',
                'enc.id',
                'entre.id',
                'entre.id_entrevistador',
                'entre.data',
                'entre.hora',
            
            )
            ->where('entre.id_encaminhamento', $id)
            ->first();


        if (!$entrevistas) {
        }
        $entrevistador = DB::table('pessoas')->get();
        $pessoas = DB::table('pessoas')->get();
        $encaminhamento = DB::table('encaminhamento')->find($id);
        $salas = DB::table('salas')
            ->join('tipo_localizacao', 'salas.id_localizacao', '=', 'tipo_localizacao.id')
            ->select('salas.*', 'tipo_localizacao.nome AS nome_localizacao')
            ->get();
        $membros = DB::table('membro')
            ->join('associado', 'membro.id_associado', '=', 'associado.id')
            ->join('pessoas', 'associado.id_pessoa', '=', 'pessoas.id')
            ->select('membro.*', 'pessoas.nome_completo AS nome_entrevistador')
            // ->where('membro.id' , $entrevistas->id_entrevistador)
            ->get();


        $encaminhamento = DB::table('encaminhamento')->find($id);

        // Verificando se o tipo de entrevista é 3 (tipo_entrevista 3, afe)
        if ($encaminhamento && $encaminhamento->id_tipo_entrevista === 3) {
            // Obtendo informações dos atendentes (caso o tipo de entrevista seja afe)
            $membros = DB::table('membro')
                ->join('associado', 'membro.id_associado', '=', 'associado.id')
                ->join('pessoas', 'associado.id_pessoa', '=', 'pessoas.id')
                ->select('membro.*', 'pessoas.nome_completo AS nome_entrevistador')
                ->distinct('membro.id_associado')
                ->where('membro.id_funcao', 5)
                ->get();
        }
           return view('Entrevistas.editar-entrevista', compact('membros', 'entrevistador', 'entrevistas', 'encaminhamento', 'pessoas', 'salas'));
    }




    public function update(Request $request, $id)
    {
        $entrevista = DB::table('entrevistas AS entre')
            ->leftJoin('salas AS s', 'entre.id_sala', 's.id')
            ->leftJoin('tipo_localizacao as tpl', 's.id_localizacao', 'tpl.id')
            ->leftJoin('encaminhamento AS enc', 'entre.id_encaminhamento', 'enc.id')
            ->leftJoin('atendimentos as atd', 'enc.id_atendimento', 'atd.id')
            ->leftJoin('pessoas AS p', 'atd.id_assistido', 'p.id')
            ->select('p.nome_completo', 's.nome', 's.numero', 'tpl.nome as local', 'enc.id', 'entre.id', 'entre.id_entrevistador', 'entre.data', 'entre.hora')
            ->where('entre.id_encaminhamento', $id)
            ->first();


        if (!$entrevista) {
            app('flasher')->addError("Entrevista não encontrada");
            return redirect('gerenciar-entrevistas');
        }





        DB::table('entrevistas')
            ->where('id_encaminhamento', $id)
            ->update([
                'id_entrevistador' => $request->input('entrevistador'),
                'data' => $request->input('data'),
                'hora' => $request->input('hora'),
                'id_sala' => $request->input('numero_sala'),
            ]);

        app('flasher')->addSuccess("Entrevista atualizada com sucesso");
        return redirect('gerenciar-entrevistas');
    }



    public function finalizar($id)
    {

        $novo_encaminhamento = Carbon::today();
        $id_usuario = session()->get('usuario.id_usuario');
        $encaminhamento = DB::table('encaminhamento')->where('id', $id)->first();

        // Obter informações sobre a entrevista
        $entrevista = DB::table('entrevistas')
            ->where('id_encaminhamento', $id)
            ->first();

        if (!$entrevista) {
            return redirect()->route('gerenciamento')->with('error', 'Entrevista não encontrada!');
        }


        $dateTime = DB::table('entrevistas as ent')->where('id_encaminhamento', $id)
            ->select('ent.data', 'ent.hora', 'enc.id_tipo_entrevista', 'enc.id', 'ent.id_sala', 'ent.id_entrevistador')
            ->leftJoin('encaminhamento as enc', 'ent.id_encaminhamento', 'enc.id')
            ->first();


        $dt = Carbon::createFromFormat('Y-m-d H:i:s', $dateTime->data . ' ' . $dateTime->hora);
        $atendimentos = DB::table('encaminhamento as enc')
            ->select('id_assistido')
            ->leftJoin('atendimentos as at', 'enc.id_atendimento', 'at.id')
            ->where('enc.id', $id)
            ->first();
        $id_entrevistador = DB::table('membro')->where('id', $dateTime->id_entrevistador)->select('id_associado')->first();

        if ($dateTime->id_tipo_entrevista == 3) {
            $data = date("Y-m-d H:i:s");
            DB::table('atendimentos')->insert([
                'dh_marcada' => $dt,
                'id_assistido' => $atendimentos->id_assistido,
                'id_atendente' => $id_entrevistador->id_associado,
                'id_usuario' => session()->get('usuario.id_usuario'),
                'id_sala' => $dateTime->id_sala,
                'status_atendimento' => 7,
                'afe' => true
            ]);
            DB::table('entrevistas')->where('id_encaminhamento', $id)->update(['status' => 3]);
            DB::table('historico_venus')->insert([

                'id_usuario' => session()->get('usuario.id_usuario'),
                'data' => $data,
                'fato' => 40,
                'obs' => $id

            ]);


            return redirect()->route('gerenciamento')->with('success', 'Entrevista finalizada com sucesso!');
        }

        else {  $nova = DB::table('encaminhamento')->insertGetId([
            // Defina os valores adequados para o novo registro na tabela de encaminhamento
            'dh_enc' => $novo_encaminhamento,
            'id_usuario' => $id_usuario,
            'id_tipo_encaminhamento' => 2,
            'id_atendimento' => $encaminhamento->id_atendimento,
            'status_encaminhamento' => 1,
        ]);

        if ($encaminhamento->id_tipo_entrevista == 4) {
            DB::table('encaminhamento')->where('id', $nova)->update(['id_tipo_tratamento' => 2,]);
        } else if ($encaminhamento->id_tipo_entrevista == 5) {

            DB::table('encaminhamento')->where('id', $nova)->update(['id_tipo_tratamento' => 6,]);
        } else if ($encaminhamento->id_tipo_entrevista == 3) {
        }

        // Atualizar o status da entrevista para 'Entrevistado' e remover o ID de encaminhamento
        DB::table('entrevistas')
            ->where('id_encaminhamento', $id)
            ->update(['status' => 5,]);


        DB::table('encaminhamento')->where('id', $id)->update(['status_encaminhamento' => 3]);;

        $data = date("Y-m-d H:i:s");

        DB::table('historico_venus')->insert([

            'id_usuario' => session()->get('usuario.id_usuario'),
            'data' => $data,
            'fato' => 40,
            'obs' => $id

        ]);

        return redirect()->route('gerenciamento')->with('success', 'Entrevista finalizada com sucesso!');}
        // Criar um novo registro na tabela de encaminhamento
    }


    public function fim($id)

    {


        DB::table('entrevistas')
            ->where('id_encaminhamento', $id)
            ->update(['status' => 6,]);


        DB::table('encaminhamento')->where('id', $id)->update(['status_encaminhamento' => 3]);;

        return redirect()->route('gerenciamento')->with('sucess', 'Entrevista cancelada!');
    }


    public function inativar($id, $tp)
    {


        $data = date("Y-m-d H:i:s");

        DB::table('historico_venus')->insert([

            'id_usuario' => session()->get('usuario.id_usuario'),
            'data' => $data,
            'fato' => 37,
            'obs' => $id

        ]);

        $entrevistas = DB::table('entrevistas')->where('id_encaminhamento', '=', $id)->first();


        if ($tp == 1) {

            DB::table('encaminhamento')
            ->where('id', $id)
            ->update(['status_encaminhamento' => 4]);

           
        }
     
        elseif($tp == 2){
            DB::table('entrevistas')
            ->where('id_encaminhamento', '=', $id)
            ->update(['status' =>6]);    

        }
        else{
            return redirect()->route('gerenciamento')->with('danger', 'Erro Inesperado!');
        }

        return redirect()->route('gerenciamento')->with('success', 'Entrevista Cancelada com Sucesso!');
    }
}
