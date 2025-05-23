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


        $statusCaseWhen = DB::raw("
    CASE
        WHEN cro.modificador = 3 THEN 'Experimental'
        WHEN cro.modificador = 4 THEN 'Em Férias'
        WHEN cro.data_fim < '$now' THEN 'Inativo'
        ELSE 'Ativo'
    END as status
");
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
                'cro.max_trab',
                'gr.status_grupo AS idst',
                'tst.descricao AS trsigla',
                's.sigla as stsigla',
                'tse.sigla as sesigla',
                'sa.numero',
                't.descricao',
                'tm.nome as nmodal',
                'ts.nome as nsemana',
                'tst.descricao as tipo',
                'tst.id as idt',
                DB::raw("(CASE WHEN cro.data_fim is not null THEN 'Inativo' ELSE 'Ativo' END) as status")
            )
            ->leftJoin('tipo_tratamento AS tst', 'cro.id_tipo_tratamento', 'tst.id')
            ->leftJoin('tipo_observacao_reuniao AS t', 'cro.observacao', 't.id')
            ->leftJoin('grupo AS gr', 'cro.id_grupo', 'gr.id')
            ->leftJoin('setor as s', 'gr.id_setor', 's.id')
            ->leftJoin('membro AS me', 'gr.id', 'me.id_cronograma')
            ->leftJoin('salas AS sa', 'cro.id_sala', 'sa.id')
            ->leftJoin('tipo_dia AS td', 'cro.dia_semana', 'td.id')
            ->leftJoin('tipo_modalidade AS tm', 'cro.id_tipo_modalidade', 'tm.id')
            ->leftJoin('tipo_semana AS ts', 'cro.id_tipo_semana', 'ts.id')
            ->leftJoin('tipo_semestre as tse', 'cro.id_tipo_semestre', 'tse.id');



        // Obtém os valores de pesquisa da requisição
        $semana = $request->input('semana', null);
        $grupo = $request->input('grupo', null);
        $tipo_tratamento = $request->input('tipo_tratamento', null);
        $setor = $request->input('setor', null);
        $status = $request->input('status','');
        $modalidade = $request->input('modalidade', null);

        // Aplica filtro por semana
        if ($semana != '') {
            // Se o valor de semana não for vazio, aplica o filtro
            $reuniao->where('cro.dia_semana', '=', $semana);
        }


        // Aplica filtro por nome de grupo com insensibilidade a maiúsculas/minúsculas e acentos
        if ($grupo) {
            $reuniao->where('gr.id', $grupo);
        }

        if ($tipo_tratamento) {

            $reuniao->where('tst.id', $tipo_tratamento);
        }
        // Aplica filtro por setor
        if ($setor) {
            $reuniao->where('s.id', $setor);
        }
        // Aplica filtro por status com base na expressão CASE WHEN
        $statusCaseWhen = DB::raw("CASE WHEN cro.data_fim is not null THEN 'Inativo' ELSE 'Ativo' END");
        // dd($reuniao->get());
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

        // Aplica filtro por setor
        if ($modalidade) {
            $reuniao->where('tm.id', $modalidade);
        }

        // Conta o número de registros
        $contar = $reuniao->distinct()->count('cro.id');

          // Carregar a lista de grupos para o Select2
          $grupos = DB::table('cronograma as c')
          ->leftJoin('grupo AS g', 'c.id_grupo', 'g.id')
          ->leftJoin('setor AS s', 'g.id_setor', 's.id')
          ->select(
              'g.id AS idg',
              'g.nome AS nomeg',
              's.sigla'
          )
          ->orderBy('g.nome', 'asc')
          ->get()
          ->unique('idg') // aqui garantimos que o ID do grupo seja único
          ->values();     // reindexa os itens do array



        // Aplica a paginação e mantém os parâmetros de busca na URL
        $reuniao = $reuniao
            ->orderBy('status', 'ASC')
            ->orderBy('cro.id_tipo_tratamento', 'ASC')
            ->orderBy('nomeg', 'ASC')
            ->groupBy('idt', 'idr', 'gr.nome', 'td.nome', 'tse.sigla', 't.descricao', 'gr.status_grupo', 'tst.descricao', 's.sigla', 'sa.numero', 'tm.nome', 'ts.nome')
            ->paginate(50)
            ->appends([
                'status' => $status,
                'semana' => $semana,
                'grupo' => $grupo,
                'setor' => $setor,
                'tipo_tratamento' => $tipo_tratamento,
                'modalidade' => $modalidade
            ]);

        // Obtém os dados para os filtros
        $situacao = DB::table('tipo_status_grupo')->select('id AS ids', 'descricao AS descs')->get();

        $tipo_tratamento = DB::table('tipo_tratamento')->select('id AS idt', 'sigla AS tipo')->get();

        $tipo_motivo = DB::table('tipo_mot_inat_gr_reu')->get();

        $tmodalidade = DB::table('tipo_modalidade')->get();

        $tpdia = DB::table('tipo_dia')
            ->select('id AS idtd', 'nome AS nomed')
            ->orderByRaw('CASE WHEN id = 0 THEN 1 ELSE 0 END, idtd ASC')
            ->get();

        // Carregar a lista de setores para o Select2
        $setores = DB::table('setor')->orderBy('nome', 'asc')->get();



            // Retorna a view com os dados
        return view('/reuniao-mediunica/gerenciar-reunioes', compact('tipo_motivo', 'reuniao', 'tpdia', 'situacao', 'status', 'contar', 'semana', 'grupos', 'setores', 'tmodalidade', 'modalidade', 'tipo_tratamento'));
    }


    public function create()
    {


        $grupo = DB::table('grupo AS gr')
            ->leftJoin('setor as s', 'gr.id_setor', 's.id')
            ->select('gr.id AS idg', 'gr.nome', 'gr.id_tipo_grupo', 's.sigla as nsigla')
            ->orderBy('gr.nome');

        $modalidade = DB::table('tipo_modalidade')->get();

        $semestre = DB::table('tipo_semestre')->get();

        $tp_semana = DB::table('tipo_semana')->orderBy('id')->get();

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

        return view('/reuniao-mediunica/criar-reuniao', compact('grupo', 'tipo', 'modalidade', 'semestre',  'tratamento',  'dia', 'salas', 'observacao', 'tp_semana'));
    }

    public function store(Request $request)
    {
        //  try {

        $usuario = session()->get('usuario.id_pessoa');
        $now = Carbon::now()->format('Y-m-d');

        $modalidade = intval($request->modalidade);
        $observacao = $request->observacao;
        $tratamento = intval($request->tratamento);
        $sala = intval($request->id_sala);
        $grupo = intval($request->grupo);
        $numero = intval($request->id_sala);
        $h_inicio = Carbon::createFromFormat('G:i', $request->h_inicio)->subMinutes(30);
        $h_fim = Carbon::createFromFormat('G:i', $request->h_fim)->addMinutes(30);
        $dia = intval($request->dia);
        $semestre = intval($request->semestre);
        $repete = isset($request->repete) ? 1 : 0;
        $tipo_semanas = $request->tipo_semana ?? [0]; // Se não houver seleção, assume 0

        // Validação de sala para modalidade presencial
        if ($modalidade == 1 && $sala === null) {
            app('flasher')->addError('Preencha um número na sala.');
            return redirect()->back()->withInput();
        }

        foreach ($tipo_semanas as $tipo_semana) {
            $query = DB::table('cronograma AS c')
                ->where(function ($query) use ($now) {
                    $query->where('c.data_fim', '>=', $now)
                        ->orWhereNull('c.data_fim'); // Apenas cronogramas ativos
                });

            if ($modalidade == 1) { // Modalidade Presencial
                $query->where('c.id_sala', $numero)
                    ->where('c.dia_semana', $dia)
                    ->where(function ($q) use ($h_inicio, $h_fim) {
                        $q->whereBetween('c.h_inicio', [$h_inicio, $h_fim])
                            ->orWhereBetween('c.h_fim', [$h_inicio, $h_fim])
                            ->orWhere(function ($sub) use ($h_inicio, $h_fim) {
                                $sub->where('c.h_inicio', '<=', $h_inicio)
                                    ->where('c.h_fim', '>=', $h_fim);
                            });
                    });

                // Impede tipo_semana 0 se já houver tipo_semana 1, 2, 3 ou 4
                $tipo_semana_conflito = DB::table('cronograma')
                    ->where('id_sala', $numero)
                    ->where('dia_semana', $dia)
                    ->whereIn('id_tipo_semana', [1, 2, 3, 4])
                    ->where(function ($q) use ($h_inicio, $h_fim) {
                        $q->whereBetween('h_inicio', [$h_inicio, $h_fim])
                            ->orWhereBetween('h_fim', [$h_inicio, $h_fim])
                            ->orWhere(function ($sub) use ($h_inicio, $h_fim) {
                                $sub->where('h_inicio', '<=', $h_inicio)
                                    ->where('h_fim', '>=', $h_fim);
                            });
                    })
                    ->exists();

                if ($tipo_semana == 0 && $tipo_semana_conflito) {
                    app('flasher')->addError('Não é permitido adicionar um cronograma com tipo de semana 0 quando já existem cronogramas com tipo de semana 1, 2, 3 ou 4.');
                    return redirect()->back()->withInput();
                }

                // Aplicação da regra do tipo_semana
                if ($tipo_semana == 0) {
                    $query->where('c.id_tipo_semana', 0);
                } else {
                    $query->where('c.id_tipo_semana', $tipo_semana);
                }
            } elseif ($modalidade > 1) { // Modalidade Remota (ou outra maior que 1)
                // Verifica se já existe um cronograma com todos os dados iguais
                $existe_conflito = DB::table('cronograma')
                    ->where('id_grupo', $grupo)
                    ->where('id_tipo_tratamento', $tratamento)
                    ->where('dia_semana', $dia)
                    ->where('h_inicio', $request->h_inicio) // Comparação exata
                    ->where('h_fim', $request->h_fim) // Comparação exata
                    ->where('id_tipo_semana', $tipo_semana)
                    ->where('id_tipo_semestre', $semestre)
                    ->where('id_tipo_modalidade', $modalidade)
                    ->where('observacao', $observacao)
                    ->exists();

                if ($existe_conflito) {
                    app('flasher')->addError('Já existe um cronograma para esta modalidade com os mesmos parâmetros.');
                    return redirect()->back()->withInput();
                }

                $query->where('c.id_grupo', $grupo)
                    ->where('c.id_tipo_tratamento', $tratamento)
                    ->where('c.dia_semana', $dia)
                    ->where('c.h_inicio', $h_inicio)
                    ->where('c.h_fim', $h_fim)
                    ->where('c.id_tipo_semana', $tipo_semana)
                    ->where('c.id_tipo_semestre', $semestre)
                    ->where('c.id_tipo_modalidade', $modalidade)
                    ->where('c.observacao', $observacao);
            }

            // Verifica duplicação
            $repeat = $query->count();

            if ($repeat > 0) {
                app('flasher')->addError('Já existe um cronograma para este horário.');
                return redirect()->back()->withInput();
            }

            // Inserção no banco de dados se não houver duplicação
            DB::table('cronograma')->insert([
                'id_grupo' => $grupo,
                'id_sala' => $numero,
                'h_inicio' => $request->h_inicio,
                'h_fim' => $request->h_fim,
                'max_atend' => $request->max_atend,
                'max_trab' => $request->max_trab,
                'dia_semana' => $dia,
                'id_tipo_modalidade' => $modalidade,
                'id_tipo_semana' => $tipo_semana,
                'id_tipo_semestre' => $semestre,
                'id_tipo_tratamento' => $tratamento,
                'data_inicio' => $request->dt_inicio,
                'data_fim' => $request->dt_fim,
                'observacao' => $observacao
            ]);
        }

        // Registra o histórico da ação do usuário
        $result = DB::table('cronograma')->max('id');
        DB::table('historico_venus')->insert([
            'id_usuario' => $usuario,
            'data' => $now,
            'fato' => 16,
            'id_ref' => $result
        ]);

        app('flasher')->addSuccess('A reunião foi cadastrada com sucesso.');

        // Recuperar os valores preenchidos se "repete" for selecionado
        if ($repete) {
            return redirect()->back()->withInput();
        }

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
        // Obtém a data atual formatada
        $now = Carbon::now()->format('Y-m-d');

        // Atualiza a tabela 'cronograma' com a data de término
        if (DB::table('cronograma as cro')->where('cro.id', $id)->whereNull('data_fim')->count() == true) {
            DB::table('cronograma as cro')
                ->where('cro.id', $id)
                ->update([
                    'cro.data_fim' => $now
                ]);

            app('flasher')->addSuccess('A reunião foi inativada com sucesso.');
        } else {

            return redirect()->back();

            app('flasher')->addError('A reunião já está inativa.');
        }

        // Verifica se há algum registro com o fato específico na tabela 'historico_venus'
        $verifica = DB::table('historico_venus')
            ->where('fato', $id)
            ->count('fato');

        // Se não houver nenhum registro, insere um novo registro
        if ($verifica == 0) {
            // Obtém a data atual para inserção na tabela 'historico_venus'
            $data = Carbon::now()->format('Y-m-d');

            DB::table('historico_venus')->insert([
                'id_usuario' => session()->get('usuario.id_usuario'),
                'data' => $data,
                'fato' => 11, // Ajuste o valor conforme necessário
            ]);
        }

        // Redireciona para a página de gerenciamento de reuniões
        return redirect('/gerenciar-reunioes');
    }
}
