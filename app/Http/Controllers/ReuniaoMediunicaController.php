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
                'cro.id_tipo_semestre',
                'cro.h_inicio',
                'td.nome AS nomed',
                'cro.h_fim',
                'cro.max_atend',
                'cro.max_trab',
                'cro.data_inicio',
                'cro.data_fim',
                'gr.status_grupo AS idst',
                'tst.descricao AS trnome',
                'tst.sigla AS trsigla',
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
            ->leftJoin('tipo_semestre as tse', 'tst.id_semestre', 'tse.id');



        // Obtém os valores de pesquisa da requisição
        $semana = $request->input('semana', null);
        $grupo = $request->input('grupo', null);
        $tipo_tratamento = $request->input('tipo_tratamento', null);
        $semestre = $request->input('semestre', null);
        $setor = $request->input('setor', null);
        $status = $request->input('status','');
        $modalidade = $request->input('modalidade', null);


        //dd($tipo_tratamento, $semestre );
        // Aplica filtro por semana
        if ($semana != '') {
            // Se o valor de semana não for vazio, aplica o filtro
            $reuniao->where('cro.dia_semana', '=', $semana);
        }

        if ($grupo) {
            $reuniao->where('cro.id_grupo', $grupo);
        }


        if ($request->filled('tipo_tratamento')) {
            $descricao = DB::table('tipo_tratamento')
                ->where('id', $request->input('tipo_tratamento'))
                ->value('descricao');

            $ids = DB::table('tipo_tratamento')
                ->where('descricao', $descricao)
                ->pluck('id');

            $reuniao->whereIn('cro.id_tipo_tratamento', $ids);
        }

        if ($semestre) {
            $reuniao->when($semestre, function ($query, $semestre) {
            return $query->where('id_tipo_semestre', $semestre);
            });
        }

        if ($setor) {
            $reuniao->where('cro.id_setor', $setor);
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

        $tipo_tratamento = DB::table('tipo_tratamento AS tt')
        ->select('tt.id AS idt','tt.descricao', 'tt.sigla AS tipo')
        ->orderBy('tt.sigla')
        ->distinct('tt.sigla')
        ->get();

         $tipo_semestre = DB::table('tipo_tratamento AS tt')
        ->leftJoin('tipo_semestre AS ts', 'tt.id_semestre', 'ts.id')
        ->whereNotNull('tt.id_semestre')
        ->select('ts.id AS ids', 'ts.sigla')
        ->orderBy('ts.id')
        ->get();

        $tipo_motivo = DB::table('tipo_mot_inat_gr_reu')->get();

        $tmodalidade = DB::table('tipo_modalidade')->get();

        $tpdia = DB::table('tipo_dia')
            ->select('id AS idtd', 'nome AS nomed')
            ->orderByRaw('CASE WHEN id = 0 THEN 1 ELSE 0 END, idtd ASC')
            ->get();

        // Carregar a lista de setores para o Select2
        $setores = DB::table('setor')->orderBy('nome', 'asc')->get();



            // Retorna a view com os dados
        return view('/reuniao-mediunica/gerenciar-reunioes', compact('tipo_semestre', 'tipo_motivo', 'reuniao', 'tpdia', 'situacao', 'status', 'contar', 'semana', 'grupos', 'setores', 'tmodalidade', 'modalidade', 'tipo_tratamento'));
    }


    public function create()
    {


        $grupo = DB::table('grupo AS gr')
            ->leftJoin('setor as s', 'gr.id_setor', 's.id')
            ->select('gr.id AS idg', 'gr.nome', 'gr.id_tipo_grupo', 's.sigla as nsigla')
            ->orderBy('gr.nome');

        $modalidade = DB::table('tipo_modalidade')->get();

        $tp_semana = DB::table('tipo_semana')->orderBy('id')->get();

        $grupo = $grupo->get();


        $tipo = DB::table('tipo_grupo AS tg')
            ->select('tg.id AS idtg', 'tg.nm_tipo_grupo')
            ->get();

        $tratamento = DB::table('tipo_tratamento AS tt')
            ->leftJoin('tipo_semestre AS ts', 'tt.id_semestre', 'ts.id')
            ->select('tt.id AS idt', 'tt.descricao', 'tt.sigla', 'ts.nome','tt.id_semestre', 'ts.sigla AS siglasem')
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

        return view('/reuniao-mediunica/criar-reuniao', compact('grupo', 'tipo', 'modalidade', 'tratamento',  'dia', 'salas', 'observacao', 'tp_semana'));
    }

    public function store(Request $request)
    {
        //  try {
        $usuario = session()->get('usuario.id_pessoa');
        $now = Carbon::now()->format('Y-m-d');
        $amanha = Carbon::now()->addDay()->format('Y-m-d');

        $modalidade = intval($request->modalidade);
        $observacao = $request->observacao;
        $tratamento = intval($request->tratamento);
        $sala = intval($request->id_sala);
        $grupo = intval($request->grupo);
        $numero = $sala;
        $h_inicio = Carbon::createFromFormat('H:i:s', $request->h_inicio);
        $h_fim = Carbon::createFromFormat('H:i:s', $request->h_fim);
        $dia = intval($request->dia);
        $repete = isset($request->repete) ? 1 : 0;
        $tipo_semanas = $request->tipo_semana ?? [0];

        $semestre = DB::table('tipo_tratamento')
            ->where('id', $tratamento)
            ->value('id_semestre');

        foreach ($tipo_semanas as $tipo_semana) {
            // Validação específica para modalidade presencial
            if ($modalidade == 1) {
                if ($sala === 0) {
                    app('flasher')->addError('Preencha um número na sala.');
                    return redirect()->back()->withInput();
                }

                // Exclusividade: tipo de semana 0 (todos) não pode coexistir com tipos 1 a 4 e vice-versa
                if ($tipo_semana == 0) {
                    $conflito = DB::table('cronograma')
                        ->where(function ($q) use ($amanha) {
                            $q->whereNull('data_fim')->orWhere('data_fim', '>=', $amanha);
                        })
                        ->where('id_sala', $numero)
                        ->where('dia_semana', $dia)
                        ->whereIn('id_tipo_semana', [1, 2, 3, 4])
                        ->where('id_tipo_modalidade', 1)
                        ->where(function ($q) use ($h_inicio, $h_fim) {
                            $q->where('h_inicio', '<', $h_fim)
                            ->where('h_fim', '>', $h_inicio);
                        })
                        ->exists();

                    if ($conflito) {
                        app('flasher')->addError('Tipo de semana 0 não pode coexistir com tipos 1 a 4 no mesmo horário.');
                        return redirect()->back()->withInput();
                    }
                } elseif (in_array($tipo_semana, [1, 2, 3, 4])) {
                    $conflito = DB::table('cronograma')
                        ->where(function ($q) use ($amanha) {
                            $q->whereNull('data_fim')->orWhere('data_fim', '>=', $amanha);
                        })
                        ->where('id_sala', $numero)
                        ->where('dia_semana', $dia)
                        ->where('id_tipo_semana', 0)
                        ->where('id_tipo_modalidade', 1)
                        ->where(function ($q) use ($h_inicio, $h_fim) {
                            $q->where('h_inicio', '<', $h_fim)
                            ->where('h_fim', '>', $h_inicio);
                        })
                        ->exists();

                    if ($conflito) {
                        app('flasher')->addError('Tipos de semana 1 a 4 não podem coexistir com o tipo 0 no mesmo horário.');
                        return redirect()->back()->withInput();
                    }
                }

                // Verifica conflito de horário e modalidade igual
                $conflitoHorario = DB::table('cronograma')
                    ->where(function ($q) use ($amanha) {
                        $q->whereNull('data_fim')->orWhere('data_fim', '>=', $amanha);
                    })
                    ->where('id_sala', $numero)
                    ->where('dia_semana', $dia)
                    ->where('id_tipo_semana', $tipo_semana)
                    ->where('id_tipo_modalidade', 1)
                    ->where(function ($q) use ($h_inicio, $h_fim) {
                        $q->where('h_inicio', '<', $h_fim)
                        ->where('h_fim', '>', $h_inicio);
                    })
                    ->exists();

                if ($conflitoHorario) {
                    app('flasher')->addError('Já existe um cronograma para este horário.');
                    return redirect()->back()->withInput();
                }
            }

                // Validação para evitar duplicidade com modalidade online (2) ou externa (3)
            if (in_array($modalidade, [2, 3])) {
                $duplicado = DB::table('cronograma')
                    ->whereNull('data_fim') // apenas cronogramas ativos
                    ->where('id_tipo_modalidade', $modalidade)
                    ->where('dia_semana', $dia)
                    ->where('h_inicio', $request->h_inicio)
                    ->where('h_fim', $request->h_fim)
                    ->where('id_grupo', $grupo)
                    ->where('id_tipo_tratamento', $tratamento)
                    ->whereDate('data_inicio', $request->dt_inicio)
                    ->exists();

                if ($duplicado) {
                    app('flasher')->addError('Já existe um cronograma ativo com os mesmos dados para esta modalidade.');
                    return redirect()->back()->withInput();
                }
            }

            // Insere o novo cronograma
            DB::table('cronograma')->insert([
                'id_grupo' => $grupo,
                'id_sala' => $request->input('id_sala') ?: null,
                'h_inicio' => $request->h_inicio,
                'h_fim' => $request->h_fim,
                'max_atend' => $request->max_atend,
                'max_trab' => $request->max_trab,
                'dia_semana' => $dia,
                'id_tipo_modalidade' => $modalidade,
                'id_tipo_semana' => $tipo_semana,
                'id_tipo_tratamento' => $tratamento,
                'id_tipo_semestre' => $semestre,
                'data_inicio' => $request->dt_inicio,
                'data_fim' => $request->dt_fim,
                'observacao' => $observacao
            ]);
        }

        $id = DB::getPdo()->lastInsertId();

        DB::table('historico_venus')->insert([
            'id_usuario' => $usuario,
            'data' => $now,
            'fato' => 16,
            'id_ref' => $id
        ]);

        app('flasher')->addSuccess('A reunião foi cadastrada com sucesso.');

        return $repete ? redirect()->back()->withInput() : redirect('/gerenciar-reunioes');
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

        // try {
            $cronograma = DB::table('cronograma')->where('id', $id)->value('id');

            $grupo = DB::table('grupo AS gr')
                ->leftJoin('setor as s', 'gr.id_setor', 's.id')
                ->select('gr.id AS idg', 'gr.nome', 'gr.id_tipo_grupo', 's.sigla as nsigla')
                ->orderBy('gr.nome');

            $modalidade = DB::table('tipo_modalidade')->get(); 

            $grupo = $grupo->get();

            $tipo = DB::table('tipo_grupo AS tg')
                ->select('tg.id AS idtg', 'tg.nm_tipo_grupo')
                ->get();

            $tratamento = DB::table('tipo_tratamento AS tt')
            ->leftJoin('tipo_semestre AS ts', 'tt.id_semestre', 'ts.id')
            ->select('tt.id AS idt', 'tt.descricao', 'tt.sigla', 'ts.nome', 'ts.sigla AS siglasem')
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

            return view('/reuniao-mediunica/editar-reuniao', compact('cronograma', 'observacao', 'info', 'salas', 'grupo', 'tipo',  'tratamento',  'dia', 'modalidade'));
        // } catch (\Exception $e) {

        //     $code = $e->getCode();
        //     return view('administrativo-erro.erro-inesperado', compact('code'));
        // }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
       // try {

    $usuario = session()->get('usuario.id_pessoa');
    $now = Carbon::now()->format('Y-m-d');
    $modalidade = intval($request->modalidade);
    $observacao = $request->observacao;
    $tratamento = intval($request->tratamento);
    $sala = intval($request->id_sala);
    $grupo = intval($request->grupo);
    $numero = $sala;
    $h_inicio = Carbon::parse($request->h_inicio);
    $h_fim = Carbon::parse($request->h_fim);
    $h_inicio_buffer = $h_inicio->copy()->subMinutes(30);
    $h_fim_buffer = $h_fim->copy()->addMinutes(30);
    $dia = intval($request->dia);
    $tipo_semanas = $request->tipo_semana ?? [0];
//dd( $h_inicio,  $h_fim);
    

    $semestre = DB::table('tipo_tratamento')
        ->where('id', $tratamento)
        ->value('id_semestre');

    // Verificação única para duplicidade em modalidades online ou externa (2 ou 3)
    if (in_array($modalidade, [2, 3])) {
        $duplicado = DB::table('cronograma')
            ->where('id', '<>', $id)
            ->whereNull('data_fim')
            ->where('id_tipo_modalidade', $modalidade)
            ->where('dia_semana', $dia)
            ->where('h_inicio', $request->h_inicio)
            ->where('h_fim', $request->h_fim)
            ->where('id_grupo', $grupo)
            ->where('id_tipo_tratamento', $tratamento)
            ->whereDate('data_inicio', $request->dt_inicio)
            ->exists();

        if ($duplicado) {
            app('flasher')->addError('Já existe um cronograma ativo com os mesmos dados para esta modalidade.');
            return redirect()->back()->withInput();
        }
    }

    foreach ($tipo_semanas as $tipo_semana) {
        $query = DB::table('cronograma AS c')
            ->where('c.id', '<>', $id)
            ->where(function ($q) use ($now) {
                $q->whereNull('c.data_fim')->orWhere('c.data_fim', '>=', $now);
            });

        if ($modalidade == 1) {
            if ($sala === 0) {
                app('flasher')->addError('Preencha um número na sala.');
                return redirect()->back()->withInput();
            }

            $query->where('c.id_sala', $numero)
                ->where('c.dia_semana', $dia)
                ->where(function ($q) use ($h_inicio_buffer, $h_fim_buffer) {
                    $q->whereBetween('c.h_inicio', [$h_inicio_buffer, $h_fim_buffer])
                        ->orWhereBetween('c.h_fim', [$h_inicio_buffer, $h_fim_buffer])
                        ->orWhere(function ($sub) use ($h_inicio_buffer, $h_fim_buffer) {
                            $sub->where('c.h_inicio', '<=', $h_inicio_buffer)
                                ->where('c.h_fim', '>=', $h_fim_buffer);
                        });
                });

            if ($tipo_semana == 0) {
                $conflito_tipo_semana = DB::table('cronograma')
                    ->where('id', '<>', $id)
                    ->where(function ($q) use ($now) {
                        $q->whereNull('data_fim')->orWhere('data_fim', '>=', $now);
                    })
                    ->where('id_sala', $numero)
                    ->where('dia_semana', $dia)
                    ->whereIn('id_tipo_semana', [1, 2, 3, 4])
                    ->where(function ($q) use ($h_inicio_buffer, $h_fim_buffer) {
                        $q->whereBetween('h_inicio', [$h_inicio_buffer, $h_fim_buffer])
                            ->orWhereBetween('h_fim', [$h_inicio_buffer, $h_fim_buffer])
                            ->orWhere(function ($sub) use ($h_inicio_buffer, $h_fim_buffer) {
                                $sub->where('h_inicio', '<=', $h_inicio_buffer)
                                    ->where('h_fim', '>=', $h_fim_buffer);
                            });
                    })
                    ->exists();

                if ($conflito_tipo_semana) {
                    app('flasher')->addError('Não é permitido tipo de semana 0 quando já existem tipos 1 a 4.');
                    return redirect()->back()->withInput();
                }

                $query->where('c.id_tipo_semana', 0);
            } else {
                $query->where('c.id_tipo_semana', $tipo_semana);
            }
        } else {
            $existe_conflito = DB::table('cronograma')
                ->where('id', '<>', $id)
                ->where(function ($q) use ($now) {
                    $q->whereNull('data_fim')->orWhere('data_fim', '>=', $now);
                })
                ->where('id_grupo', $grupo)
                ->where('id_tipo_tratamento', $tratamento)
                ->where('dia_semana', $dia)
                ->where('h_inicio', $request->h_inicio)
                ->where('h_fim', $request->h_fim)
                ->where('id_tipo_semana', $tipo_semana)
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
                ->where('c.id_tipo_modalidade', $modalidade)
                ->where('c.observacao', $observacao);
        }

        if ($query->exists()) {
            app('flasher')->addError('Já existe um cronograma para este horário.');
            return redirect()->back()->withInput();
        }

        // Atualização do cronograma
        DB::table('cronograma')->where('id', $id)->update([
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

    DB::table('historico_venus')->insert([
        'id_usuario' => $usuario,
        'data' => $now,
        'fato' => 16,
        'id_ref' => $id
    ]);

    app('flasher')->addSuccess('A reunião foi atualizada com sucesso.');
    return redirect('/gerenciar-reunioes');

        // } catch (\Exception $e) {

        //     $code = $e->getCode();
        //     return view('administrativo-erro.erro-inesperado', compact('code'));
        // }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function inativa(string $id)
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

     public function destroy(string $id)
    {

        $em_uso = DB::table('tratamento AS t')
                    ->where('t.id_reuniao', $id)->count();

        if ($em_uso > 0) {

            app('flasher')->addError('A reunião já esta ligada a um tratamento.');
            return redirect()->back();
            
        } else {

            DB::table('dias_cronograma as dc')
                ->where('dc.id_cronograma', $id)
                ->delete();
            
            DB::table('cronograma as cro')
                ->where('cro.id', $id)
                ->delete();
            

            app('flasher')->addSuccess('A reunião foi excluida com sucesso.');
            return redirect()->back();
           
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
