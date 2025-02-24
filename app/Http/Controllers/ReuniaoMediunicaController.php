<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Carbon;
use Mockery\Undefined;

use function Laravel\Prompts\select;

class ReuniaoMediunicaController extends Controller
{

    public function index(Request $request)
    {
        // Obtém a data atual formatada
        $now = Carbon::now()->format('Y-m-d');

        // Inicializa a consulta
        $reuniao = DB::table('cronograma AS cro')
            ->select(
                'cro.id AS idr',
                'gr.nome AS nomeg',
                'cro.dia_semana AS idd',
                'cro.id_sala',
                'cro.id_tipo_tratamento',
                'cro.h_inicio',
                'td.nome AS nomed',
                'cro.h_fim',
                'cro.max_atend',
                'gr.status_grupo AS idst',
                'tst.descricao AS tstd',
                's.sigla as nsigla',
                'sa.numero',
                DB::raw("(CASE WHEN cro.data_fim is not null THEN 'Inativo' ELSE 'Ativo' END) as status")
            )
            ->leftJoin('tipo_tratamento AS tst', 'cro.id_tipo_tratamento', 'tst.id')
            ->leftJoin('grupo AS gr', 'cro.id_grupo', 'gr.id')
            ->leftJoin('setor as s', 'gr.id_setor', 's.id')
            ->leftJoin('membro AS me', 'gr.id', 'me.id_cronograma')
            ->leftJoin('salas AS sa', 'cro.id_sala', 'sa.id')
            ->leftJoin('tipo_dia AS td', 'cro.dia_semana', 'td.id');


        // Obtém os valores de pesquisa da requisição
        $semana = $request->input('semana', null);
        $grupo = $request->input('grupo', null);
        $setor = $request->input('setor', null);
        $status = $request->input('status', null);

        // Aplica filtro por semana
        if ($semana && $semana !== 'todos') {
            $reuniao->where('cro.dia_semana', '=', $semana);
        }

        // Aplica filtro por nome de grupo com insensibilidade a maiúsculas/minúsculas e acentos
        if ($grupo) {
            $reuniao->where('gr.id', $grupo);
        }

        // Aplica filtro por setor
        if ($setor) {
            $reuniao->where('s.id', $setor);
        }
        // Aplica filtro por status com base na expressão CASE WHEN
        $statusCaseWhen = DB::raw("CASE WHEN cro.data_fim is not null THEN 'Inativo' ELSE 'Ativo' END");

        if ($status) {
            switch ($status) {
                case 1:
                    $reuniao->where($statusCaseWhen, 'Ativo');
                    break;
                case 2:
                    $reuniao->where($statusCaseWhen, 'Inativo');
                    break;
                case 3:
                    $reuniao->where($statusCaseWhen, 'Experimental');
                    break;
                case 4:
                    $reuniao->where($statusCaseWhen, 'Em ferias');
                    break;
            }
        }

        // Conta o número de registros
        $contar = $reuniao->distinct()->count('cro.id');

        // Aplica a paginação e mantém os parâmetros de busca na URL
        $reuniao = $reuniao
            ->orderBy('status', 'ASC')
            ->orderBy('cro.id_tipo_tratamento', 'ASC')
            ->orderBy('nomeg', 'ASC')
            ->groupBy('idr', 'gr.nome', 'td.nome', 'gr.status_grupo', 'tst.descricao', 's.sigla', 'sa.numero')
            ->paginate(50)
            ->appends([
                'status' => $status,
                'semana' => $semana,
                'grupo' => $grupo,
                'setor' => $setor
            ]);

        // Obtém os dados para os filtros
        $situacao = DB::table('tipo_status_grupo')->select('id AS ids', 'descricao AS descs')->get();

        $tipo_motivo = DB::table('tipo_mot_inat_gr_reu')->get();

        $tpdia = DB::table('tipo_dia')
            ->select('id AS idtd', 'nome AS nomed')
            ->orderByRaw('CASE WHEN id = 0 THEN 1 ELSE 0 END, idtd ASC')
            ->get();

        // Carregar a lista de setores para o Select2
        $setores = DB::table('setor')->orderBy('nome', 'asc')->get();

        // Carregar a lista de grupos para o Select2
        $grupos = DB::table('grupo AS g')->leftJoin('setor AS s', 'g.id_setor', 's.id')->select('g.id AS idg', 'g.nome AS nomeg', 's.sigla')->orderBy('g.nome', 'asc')->get();


        // Retorna a view com os dados
        return view('/reuniao-mediunica/gerenciar-reunioes', compact('tipo_motivo', 'reuniao', 'tpdia', 'situacao', 'status', 'contar', 'semana', 'grupos', 'setores'));
    }


    public function create()
    {


        $grupo = DB::table('grupo AS gr')
            ->leftJoin('setor as s', 'gr.id_setor', 's.id')
            ->select('gr.id AS idg', 'gr.nome', 'gr.id_tipo_grupo', 's.sigla as nsigla')
            ->orderBy('gr.nome');



        $grupo = $grupo->get();


        $tipo = DB::table('tipo_grupo AS tg')
            ->select('tg.id AS idtg', 'tg.nm_tipo_grupo')
            ->get();

        $tratamento = DB::table('tipo_tratamento AS tt')
            ->select('tt.id AS idt', 'tt.descricao', 'tt.sigla')
            ->orderBy('tt.descricao')
            ->get();

        $dia = DB::table('tipo_dia AS td')
            ->select('td.id AS idd', 'td.nome', 'td.sigla')
            ->get();

        $salas = DB::table('salas')
            ->join('tipo_localizacao', 'salas.id_localizacao', '=', 'tipo_localizacao.id')
            // ->where('id_finalidade', 6)
            ->select('salas.*', 'tipo_localizacao.nome AS nome_localizacao')
            ->get();

        $observacao = DB::table('tipo_observacao_reuniao')->get();

        return view('/reuniao-mediunica/criar-reuniao', compact('grupo', 'tipo',  'tratamento',  'dia', 'salas', 'observacao'));
    }

    public function store(Request $request)
    {
        //  try {

        $usuario = session()->get('usuario.id_pessoa');
        $now =  Carbon::now()->format('Y-m-d');
        $data_inicio = $request->dt_inicio ? $request->dt_inicio : $now;

        $grupo = intval($request->grupo);
        $numero = intval($request->id_sala);
        $h_inicio = Carbon::createFromFormat('G:i', $request->h_inicio)->subMinutes(30);
        $h_fim = Carbon::createFromFormat('G:i', $request->h_fim)->addMinutes(30);
        $dia = intval($request->dia);

        // Conta cronogramas que ocupam a mesma sala no mesmo horário, no mesmo dia da semana
        $repeat = DB::table('cronograma AS rm')
            ->leftJoin('grupo AS g', 'rm.id_grupo', 'g.id')
            ->leftJoin('salas AS s', 'rm.id_sala', 's.id')
            ->where('rm.dia_semana', $dia) // Mesmo dia da semana
            ->where('rm.id_sala', $numero) // Mesmo ID_sala
            ->where(function ($query) use ($now) { // Apenas cronogramas Ativos
                $query->where('rm.data_fim', '>=', $now);
                $query->orWhere('rm.data_fim', null);
            })
            ->where(function ($query) use ($h_inicio, $h_fim) { // Função de reconhecimento de horários

                $query->where(function ($hour) use ($h_inicio, $h_fim) {  // A reunião criada inicia antes que outra, mas termina durante ou depois (  <----|---->  |  ou <----|-----|----> )
                    $hour->where('rm.h_inicio', '>=', $h_inicio);
                    $hour->where('rm.h_inicio', '<=', $h_fim);
                });
                $query->orWhere(function ($hour) use ($h_inicio, $h_fim) { // A reunião foi criada com a H_inicio interna de outra reunião (  | <-----|----> ou <----|------|---->  )
                   $hour->where('rm.h_fim', '>=', $h_inicio);
                    $hour->where('rm.h_fim', '<=', $h_fim);
                });
                $query->orWhere(function ($hour) use ($h_fim, $h_inicio) { // A reunião está completamente interna a outra (  | <---------> |  )
                   $hour->where('rm.h_inicio', '<=', $h_inicio);
                    $hour->where('rm.h_fim', '>=', $h_fim);
                });

            })
            ->count();

        if ($repeat > 0) {

            app('flasher')->addError('Existe uma outra reunião nesse horário.');

            return redirect()->back();
        } else {
        }


        DB::table('cronograma AS rm')->insert([
            'id_grupo' => $request->input('grupo'),
            'id_sala' => $request->input('id_sala'),
            'h_inicio' => $request->input('h_inicio'),
            'h_fim' => $request->input('h_fim'),
            'max_atend' => $request->input('max_atend'),
            'dia_semana' => $request->input('dia'),
            'id_tipo_tratamento' => $request->input('tratamento'),
            'data_inicio' => $data_inicio,
            'data_fim' => $request->dt_fim,
            'observacao' => $request->observacao
        ]);

        $result = DB::table('cronograma')->max('id');

        DB::table('historico_venus')->insert([
            'id_usuario' => $usuario,
            'data' => $now,
            'fato' => 16,
            'id_ref' => $result
        ]);


        app('flasher')->addSuccess('A reunião foi cadastrada com sucesso.');

        return redirect('/gerenciar-reunioes');
        // } catch (\Exception $e) {

        //     $code = $e->getCode();
        //     return view('administrativo-erro.erro-inesperado', compact('code'));
        // }
    }

    public function show(string $id)
    {
        // try {

        $grupo = DB::table('grupo AS gr')
            ->leftJoin('setor as s', 'gr.id_setor', 's.id')
            ->select('gr.id AS idg', 'gr.nome', 'gr.id_tipo_grupo', 's.sigla as nsigla')
            ->orderBy('gr.nome')
            ->get();

        $tipo = DB::table('tipo_grupo AS tg')
            ->select('tg.id AS idtg', 'tg.nm_tipo_grupo')
            ->get();

        $tratamento = DB::table('tipo_tratamento AS tt')
            ->select('tt.id AS idt', 'tt.descricao', 'tt.sigla')
            ->get();

        $salas = DB::table('salas')
            ->join('tipo_localizacao', 'salas.id_localizacao', '=', 'tipo_localizacao.id')
            ->select('salas.*', 'tipo_localizacao.nome AS nome_localizacao')
            ->orderBy('numero', 'asc')
            ->get();

        $dia = DB::table('tipo_dia AS td')
            ->select('td.id AS idd', 'td.nome', 'td.sigla')
            ->get();

        $info = DB::table('cronograma as crn')
            ->select('tor.descricao as obs', 'crn.id', 'gr.id as id_grupo', 'tpd.nome as dia', 'tpt.descricao', 'crn.max_atend', 'sl.numero', 'sl.nome as sala', 'crn.h_inicio', 'crn.h_fim', 'crn.id_sala', 'sl.id_localizacao as nome_localizacao', 'crn.data_inicio', 'crn.data_fim')
            ->leftJoin('grupo as gr', 'crn.id_grupo', 'gr.id')
            ->leftJoin('tipo_dia as tpd', 'crn.dia_semana', 'tpd.id')
            ->leftJoin('tipo_tratamento as tpt', 'crn.id_tipo_tratamento', 'tpt.id')
            ->leftJoin('salas as sl', 'crn.id_sala', 'sl.id')
            ->leftJoin('tipo_observacao_reuniao as tor', 'crn.observacao', 'tor.id')
            ->where('crn.id', "$id")
            ->first();



        return view('/reuniao-mediunica/visualizar-reuniao', compact('info', 'salas', 'grupo', 'tipo',  'tratamento',  'dia'));
        // } catch (\Exception $e) {

        //     $code = $e->getCode();
        //     return view('administrativo-erro.erro-inesperado', compact('code'));
        // }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {

        try {

            $grupo = DB::table('grupo AS gr')
                ->leftJoin('setor as s', 'gr.id_setor', 's.id')
                ->select('gr.id AS idg', 'gr.nome', 'gr.id_tipo_grupo', 's.sigla as nsigla')
                ->orderBy('gr.nome');



            $grupo = $grupo->get();

            $tipo = DB::table('tipo_grupo AS tg')
                ->select('tg.id AS idtg', 'tg.nm_tipo_grupo')
                ->get();

            $tratamento = DB::table('tipo_tratamento AS tt')
                ->select('tt.id AS idt', 'tt.descricao', 'tt.sigla')
                ->orderBy('tt.descricao')
                ->get();

            $dia = DB::table('tipo_dia AS td')
                ->select('td.id AS idd', 'td.nome', 'td.sigla')
                ->get();

            $salas = DB::table('salas')
                ->join('tipo_localizacao', 'salas.id_localizacao', '=', 'tipo_localizacao.id')
                ->select('salas.*', 'tipo_localizacao.nome AS nome_localizacao')
                ->orderBy('numero', 'asc')
                ->get();

            $info = DB::table('cronograma as crn')
                ->select('crn.observacao as obs', 'crn.id', 'gr.id as id_grupo', 'tpd.nome as dia', 'tpt.descricao', 'crn.max_atend', 'sl.numero', 'sl.nome as sala', 'crn.h_inicio', 'crn.h_fim', 'crn.id_sala', 'sl.id_localizacao as nome_localizacao', 'crn.data_inicio', 'crn.data_fim')
                ->leftJoin('grupo as gr', 'crn.id_grupo', 'gr.id')
                ->leftJoin('tipo_dia as tpd', 'crn.dia_semana', 'tpd.id')
                ->leftJoin('tipo_tratamento as tpt', 'crn.id_tipo_tratamento', 'tpt.id')
                ->leftJoin('salas as sl', 'crn.id_sala', 'sl.id')
                ->where('crn.id', "$id")
                ->first();

            $observacao = DB::table('tipo_observacao_reuniao')->get();

            return view('/reuniao-mediunica/editar-reuniao', compact('observacao', 'info', 'salas', 'grupo', 'tipo',  'tratamento',  'dia'));
        } catch (\Exception $e) {

            $code = $e->getCode();
            return view('administrativo-erro.erro-inesperado', compact('code'));
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {

            $usuario = session()->get('usuario.id_pessoa');
            $now =  Carbon::now()->format('Y-m-d');
            $data_inicio = $request->dt_inicio ? $request->dt_inicio : $now;

            $grupo = intval($request->grupo);
            $numero = intval($request->id_sala);
            $h_inicio = Carbon::createFromDate($request->h_inicio)->subMinutes(30);
            $h_fim = Carbon::createFromDate($request->h_fim)->addMinutes(30);
            $dia = intval($request->dia);
            $repeat = DB::table('cronograma AS rm')
                ->leftJoin('grupo AS g', 'rm.id_grupo', 'g.id')
                ->leftJoin('salas AS s', 'rm.id_sala', 's.id')
                ->where('rm.dia_semana', $dia)
                ->whereNot('rm.data_fim', '<', $now)
                ->where('rm.id_sala', $numero)
                ->where(function ($query) use ($h_inicio, $h_fim) {
                    $query->where(function ($hour) use ($h_inicio) {
                        $hour->where('rm.h_inicio', '<=', $h_inicio);
                        $hour->where('rm.h_fim', '>=', $h_inicio);
                    });
                    $query->orWhere(function ($hour) use ($h_fim) {
                        $hour->where('rm.h_inicio', '<=', $h_fim);
                        $hour->where('rm.h_fim', '>=', $h_fim);
                    });
                })
                ->count();

            if ($repeat > 0) {

                app('flasher')->addError('Existe uma outra reunião nesse horário.');

                return redirect('/gerenciar-reunioes');
            } else {
            }


            DB::table('cronograma AS rm')->where('id', $id)->update([
                'id_grupo' => $request->input('grupo'),
                'id_sala' => $request->input('id_sala'),
                'h_inicio' => $request->input('h_inicio'),
                'h_fim' => $request->input('h_fim'),
                'max_atend' => $request->input('max_atend'),
                'dia_semana' => $request->input('dia'),
                'id_tipo_tratamento' => $request->input('tratamento'),
                'data_inicio' => $data_inicio,
                'data_fim' => $request->dt_fim,
                'observacao' => $request->observacao
            ]);

            $result = DB::table('cronograma')->max('id');

            DB::table('historico_venus')->insert([
                'id_usuario' => $usuario,
                'data' => $now,
                'fato' => 16,
                'id_ref' => $result
            ]);


            app('flasher')->addSuccess('A reunião foi atualizada com sucesso.');

            return redirect('/gerenciar-reunioes');
        } catch (\Exception $e) {

            $code = $e->getCode();
            return view('administrativo-erro.erro-inesperado', compact('code'));
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Verifica se o cronograma existe antes de deletar
        $cronograma = DB::table('cronograma')->where('id', $id)->first();

        if (!$cronograma) {
            app('flasher')->addError('A reunião não existe.');
            return redirect()->back();
        }

        // Obtém todos os IDs de dias_cronograma relacionados a esse cronograma
        $diasCronogramaIds = DB::table('dias_cronograma')
            ->where('id_cronograma', $id)
            ->pluck('id'); // Obtém todos os IDs relacionados

        if ($diasCronogramaIds->isNotEmpty()) {
            // Deleta os registros relacionados na tabela 'presenca_membros' primeiro
            DB::table('presenca_membros')->whereIn('id_dias_cronograma', $diasCronogramaIds)->delete();

            // Agora pode deletar os registros da tabela 'dias_cronograma'
            DB::table('dias_cronograma')->whereIn('id', $diasCronogramaIds)->delete();
        }

        // Deleta os registros na tabela 'membro' que referenciam esse cronograma (ou pode atualizar o campo)
        DB::table('membro')->where('id_cronograma', $id)->delete(); // Se quiser apenas desvincular, use `update(['id_cronograma' => null])`

        // Finalmente, deleta o cronograma
        DB::table('cronograma')->where('id', $id)->delete();

        // Verifica se há algum registro com o fato específico na tabela 'historico_venus'
        $verifica = DB::table('historico_venus')
            ->where('fato', $id)
            ->count();

        // Se não houver nenhum registro, insere um novo registro
        if ($verifica == 0) {
            $data = Carbon::now()->format('Y-m-d');

            DB::table('historico_venus')->insert([
                'id_usuario' => session()->get('usuario.id_usuario'),
                'data' => $data,
                'fato' => 11, // Ajuste o valor conforme necessário
            ]);
        }

        app('flasher')->addSuccess('A reunião foi deletada com sucesso.');

        // Redireciona para a página de gerenciamento de reuniões
        return redirect('/gerenciar-reunioes');
    }

}
