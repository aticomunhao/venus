<?php

namespace App\Http\Controllers;

use App\Jobs\DiasCronograma;
use App\Jobs\DiasCronogramaOntem;
use App\Jobs\EntrevistaProamo;
use App\Jobs\Faltas;
use App\Jobs\FaltasTrabalhador;
use App\Jobs\FilaEncaminhamentos;
use App\Jobs\FimSemanas;
use App\Jobs\LimiteFalta;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\Rules\Exists;
use PhpParser\Node\Expr\AssignOp\Coalesce;
use PhpParser\Node\Expr\BinaryOp\Coalesce as BinaryOpCoalesce;

class GerenciarTratamentosController extends Controller
{
    public function index(Request $request)
    {
        try {

        $now =  Carbon::now()->format('Y-m-d');

        $selectGrupo = explode(' ', $request->grupo);
        $lista = DB::table('tratamento AS tr')
            ->select('tr.id AS idtr', 'tr.status', 'enc.id AS ide', 'enc.id_tipo_encaminhamento', 'dh_enc', 'enc.id_atendimento', 'enc.status_encaminhamento', 'tst.nome AS tst', 'enc.id_tipo_tratamento AS idtt', 'id_tipo_entrevista', 'at.id AS ida', 'at.id_assistido', 'p1.nome_completo AS nm_1', 'at.id_representante as idr', 'p2.nome_completo as nm_2', 'p1.cpf AS cpf_assistido', 'pa.nome', 'pr.id AS prid', 'pr.descricao AS prdesc', 'pr.sigla AS prsigla', 'tt.descricao AS desctrat', 'tt.sigla', 'tr.id AS idtr', 'gr.nome AS nomeg', 'td.nome AS nomed', 'rm.h_inicio', 'tr.dt_fim')
            ->leftJoin('encaminhamento AS enc',  'tr.id_encaminhamento', 'enc.id')
            ->leftJoin('atendimentos AS at', 'enc.id_atendimento', 'at.id')
            ->leftjoin('pessoas AS p1', 'at.id_assistido', 'p1.id')
            ->leftjoin('pessoas AS p2', 'at.id_representante', 'p2.id')
            ->leftjoin('pessoas AS p3', 'at.id_atendente_pref', 'p3.id')
            ->leftjoin('pessoas AS p4', 'at.id_atendente', 'p4.id')
            ->leftJoin('tp_parentesco AS pa', 'at.parentesco', 'pa.id')
            ->leftJoin('tipo_prioridade AS pr', 'at.id_prioridade', 'pr.id')
            ->leftJoin('tipo_status_tratamento AS tst', 'tr.status', 'tst.id')
            ->leftJoin('tipo_tratamento AS tt', 'enc.id_tipo_tratamento', 'tt.id')
            ->leftjoin('cronograma AS rm', 'tr.id_reuniao', 'rm.id')
            ->leftjoin('tipo_dia AS td', 'rm.dia_semana', 'td.id')
            ->leftjoin('grupo AS gr', 'rm.id_grupo', 'gr.id')
            ->where('enc.id_tipo_encaminhamento', 2)
            ->where('enc.id_tipo_tratamento', '<>', 3);

        $cronogramas = DB::table('cronograma as cro')
            ->select('cro.id', 'gr.nome', 'td.nome as dia', 'cro.h_inicio', 'cro.h_fim', 's.sigla as setor')
            ->leftJoin('grupo AS gr', 'cro.id_grupo', 'gr.id')
            ->leftJoin('setor as s', 'gr.id_setor', 's.id')
            ->leftJoin('salas AS sa', 'cro.id_sala', 'sa.id')
            ->leftJoin('tipo_dia AS td', 'cro.dia_semana', 'td.id')
            ->orderBy('gr.nome')
            ->get();

        $cronogramasDirigente = DB::table('membro')->where('id_associado', session()->get('usuario.id_associado'))->whereIn('id_funcao', [1, 2])->pluck('id_cronograma');


        //Setor DIVAP ou Master Admin



        //dd($cronogramasDirigente, $lista->get());
        // dd($cronogramas);
        $data_enc = $request->dt_enc;

        $diaP = $request->dia;
        $assistido = $request->assist;
        $situacao = $request->status;
        $cron = $request->grupo;


        $cpf = $request->cpf;

        $acesso = DB::table('usuario_acesso')->where('id_usuario', session()->get('usuario.id_usuario'))->where('id_acesso', session()->get('acessoAtual'))->where('id_setor', '51')->first();

        if (!$acesso and !in_array(36, session()->get('usuario.acesso'))) {
            $lista = $lista->whereIn('tr.id_reuniao', $cronogramasDirigente);
            $request->status ?? $situacao = 'all';
        }

        if ($request->dia != null) {
            $lista->where('rm.dia_semana', '=', $request->dia);
        }

        if ($request->dt_enc) {
            $lista->where('enc.dh_enc', '>=', $request->dt_enc);
        }

        if (current($selectGrupo) != '') {

            if (intval(current($selectGrupo)) != 0) {
                $lista->where('rm.id', current($selectGrupo));
            } else {

                $pesquisaNome = array();
                $pesquisaNome = explode(' ', current($selectGrupo));

                foreach ($pesquisaNome as $itemPesquisa) {
                    $lista->whereRaw("UNACCENT(LOWER(gr.nome)) ILIKE UNACCENT(LOWER(?))", ["%$itemPesquisa%"]);
                }
            }

            if ($situacao == 'all') {
                $lista->whereIn('tr.status', [1, 2]);
            }
        }

        if ($request->assist) {
            $pesquisaNome = array();
            $pesquisaNome = explode(' ', $request->assist);

            foreach ($pesquisaNome as $itemPesquisa) {
                $lista->whereRaw("UNACCENT(LOWER(p1.nome_completo)) ILIKE UNACCENT(LOWER(?))", ["%$itemPesquisa%"]);
            }
        }


        if ($request->cpf) {
            $lista->whereRaw("LOWER(p1.cpf) LIKE LOWER(?)", ["%{$request->cpf}%"]);
        } else {

            if ($request->status && $situacao != 'all') {
                $lista->where('tr.status', $request->status);
            } elseif ($situacao == 'all') {
            } elseif (current($selectGrupo) == '') {
                $lista->where('tr.status', 2);
            }
        }

        $contar = $lista->count('enc.id');
        $lista = $lista->orderby('tr.status', 'ASC')
            ->orderby('nm_1', 'ASC')
            ->orderby('at.id_prioridade', 'ASC')
            ->paginate(50)
            ->appends([
                'dt_enc' => $data_enc, // Caso troque de pagina, matém a pesquisa de dt_enc
                'dia' => $diaP,
                'grupo' => $cron,
                'assist' => $assistido, // Caso troque de pagina, mantém a pesquisa de Assisitido
                'tratamento' => $request->tratamento, // Caso troque de pagina, matém a pesquisa de tratamento
                'status' => $situacao, // Caso troque de pagina, matém a pesquisa de  status
            ]);

    

        $stat = DB::select("select
        ts.id,
        ts.nome
        from tipo_status_tratamento ts
        ");

        $dia = DB::select("select
        id,
        nome
        from tipo_dia
        ");

        $motivo = DB::table('tipo_mot_inat_at_enc')->get();



        return view('/recepcao-integrada/gerenciar-tratamentos', compact('cron', 'cronogramas', 'cpf', 'lista', 'stat', 'contar', 'data_enc', 'assistido', 'situacao', 'now', 'dia', 'diaP', 'motivo'));
        } catch (\Exception $e) {

            $code = $e->getCode();
            return view('tratamento-erro.erro-inesperado', compact('code'));
        }
    }

    public function destroy(Request $request, string $id)
    {

      //  try {
            $hoje = Carbon::today();
            $tratamento = DB::table('tratamento')->where('id', $id)->first();

            $today = Carbon::today()->format('Y-m-d');

            $idAssistido = DB::table('encaminhamento')->where('encaminhamento.id', $tratamento->id_encaminhamento)
                ->leftJoin('atendimentos', 'encaminhamento.id_atendimento', 'atendimentos.id')
                ->pluck('atendimentos.id_assistido')->toArray();

            // Retorna todos os IDs dos encaminhamentos de tratamento
            $countTratamentos = DB::table('encaminhamento as enc')
                ->select('id_tipo_tratamento', 't.dt_fim', 't.id')
                ->leftJoin('atendimentos as at', 'enc.id_atendimento', 'at.id')
                ->leftJoin('tratamento as t', 'enc.id', 't.id_encaminhamento')
                ->where('enc.id_tipo_encaminhamento', 2) // Encaminhamento de Tratamento
                ->where('at.id_assistido', $idAssistido)
                ->where('enc.status_encaminhamento', '<', 3) // 3 => Finalizado, Traz apenas os ativos (Para Agendar, Agendado)
                ->whereNot('enc.id', $tratamento->id_encaminhamento) // Exclui o tratamento de agora
                ->get()->toArray();

            // Retorna todos os IDs dos encaminhamentos de entrevista
            $countEntrevistas = DB::table('encaminhamento as enc')
                ->leftJoin('atendimentos as at', 'enc.id_atendimento', 'at.id')
                ->where('enc.id_tipo_encaminhamento', 1) // Encaminhamento de Entrevista
                ->where('at.id_assistido', $idAssistido)
                ->where('enc.status_encaminhamento', '<', 3) // 3 => Finalizado, Traz apenas os ativos (Para Agendar, Agendado)
                ->pluck('id_tipo_entrevista')->toArray();


            $tfiInfinito = array_search(6, array_column($countTratamentos, 'id_tipo_tratamento')); // Busca, caso exista, a array key dos dados de Integral
            $tfiInfinito = $tfiInfinito ? $countTratamentos[$tfiInfinito] : false; // Caso tenha encontrado, retorna os dados de Integral
            $tfiInfinito = $tfiInfinito ? ($tfiInfinito->dt_fim == null and $tfiInfinito->id != null and in_array(6, array_column($countTratamentos, 'id_tipo_tratamento'))) : false; // Confere se é um Integral Permanente caso os dados existam
            // Essa é a clausula para um PTD infinito que está sendo apoiado em outro tratamento
            //      Tratamento PTI                                                         Entrevista NUTRES (PTI)                Tratamento PROAMO                                             Entrevista DIAMO (PROAMO)   Tratamento Integral Permanente
            if (in_array(2, array_column($countTratamentos, 'id_tipo_tratamento')) or in_array(4, $countEntrevistas) or in_array(4, array_column($countTratamentos, 'id_tipo_tratamento')) or in_array(6, $countEntrevistas) or $tfiInfinito) {

                // Não executa nenhum comando especial, apenas o padrão do método

            } else {

                $ptdAtivo = DB::table('tratamento as t')
                    ->select('t.id', 'e.id as ide', 't.dt_fim', 'c.dia_semana')
                    ->leftJoin('encaminhamento as e', 't.id_encaminhamento', 'e.id')
                    ->leftJoin('atendimentos as a', 'e.id_atendimento', 'a.id')
                    ->leftJoin('cronograma as c', 't.id_reuniao', 'c.id')
                    ->where('a.id_assistido', $idAssistido)
                    ->where('t.status', '<', 3)
                    ->where('e.id_tipo_tratamento', 1)
                    ->first();

                // Caso aquela entrevista tenha um PTD marcado, e ele seja infinito, e o motivo do cancelamento foi alta da avaliação, tire de infinito
                $ptdAtivoInfinito = $ptdAtivo ? $ptdAtivo->dt_fim == null : false; //
                $dataFim = Carbon::today()->weekday($ptdAtivo->dia_semana);
                if ($ptdAtivoInfinito) {

                    // Inativa o PTD infinito
                  DB::table('tratamento')
                        ->where('id', $ptdAtivo->id)
                        ->update([
                            'dt_fim' => $dataFim,
                            'status' => 6, // Inativado
                        ]);

                    // Inativa o encaminhamento do PTD infinito
                    DB::table('encaminhamento')
                        ->where('id', $ptdAtivo->ide)
                        ->update([
                            'status_encaminhamento' => 4 // Inativado
                        ]);
                }
            }

            DB::table('encaminhamento AS enc') // Atualiza o encaminhamento para cancelado
                ->where('enc.id', $tratamento->id_encaminhamento)
                ->update([
                    'status_encaminhamento' => 4,
                    'motivo' => $request->input('motivo'), // Vem de um select na view, os dados vem da variável $motivo do metodo index()
                ]);

            // Caso esse encaminhamento tenha um tratamento
            DB::table('tratamento')
                ->where('id', $id)
                ->update([
                    'dt_fim' => $today,
                    'status' => 6, // Inativado
                    'motivo' => $request->input('motivo')
                ]);

            return redirect()->back();
        // } catch (\Exception $e) {

        //     $code = $e->getCode();
        //     return view('tratamento-erro.erro-inesperado', compact('code'));
        // }
    }


    public function presenca(Request $request, $idtr)
    {
        try {

            $infoTrat = DB::table('tratamento')->leftJoin('encaminhamento', 'tratamento.id_encaminhamento', 'encaminhamento.id')->where('tratamento.id', $idtr)->first();

            $data_atual = Carbon::now();
            $dia_atual = $data_atual->weekday();

            $confere = DB::table('presenca_cronograma AS ds')
                ->leftJoin('dias_cronograma as dc', 'ds.id_dias_cronograma', 'dc.id')
                ->where('dc.data', $data_atual)
                ->where('ds.id_tratamento', $idtr)
                ->count();

            $lista = DB::table('tratamento AS tr')
                ->leftjoin('cronograma AS rm', 'tr.id_reuniao', 'rm.id')
                ->leftJoin('encaminhamento as enc', 'tr.id_encaminhamento', 'enc.id')
                ->where('tr.id', $idtr)
                ->first();



            $dia_cronograma = DB::table('dias_cronograma')->where('id_cronograma', $lista->id_reuniao)->where('data', $data_atual)->first();

            $acompanhantes = DB::table('dias_cronograma')->where('id_cronograma', $request->reuniao)->where('data', $data_atual)->first();


            if ($confere > 0) {

                app('flasher')->addError('Já foi registrada a presença para este dia.');

                return Redirect()->back();
            } else if ($lista->dia_semana != $dia_atual) {

                app('flasher')->addError('Este assistido não corresponde ao dia de hoje.');

                return Redirect()->back();
            } else {

                if ($infoTrat->status == 1) {
                    DB::table('tratamento')->where('id', $idtr)->update([
                        'status' => 2
                    ]);
                }


                $presenca = isset($request->presenca) ? true : false;

                $acompanhantes = isset($acompanhantes->nr_acompanhantes)  ? $acompanhantes->nr_acompanhantes : 0;
                $nrAcomp = $acompanhantes + $request->acompanhantes;
                // Recupera o nome completo da pessoa associado ao id_usuario
                $nomePessoa = DB::table('pessoas')
                    ->where('id', session()->get('usuario.id_usuario'))
                    ->value('nome_completo');

                // Realiza a inserção na tabela 'historico_venus'
                DB::table('historico_venus')->insert([
                    'id_usuario' => session()->get('usuario.id_usuario'),
                    'data' => $data_atual,
                    'fato' => 25,
                    'obs' => 'Presença em tratamento',
                    'pessoa' => $nomePessoa,
                ]);

                DB::table('dias_cronograma')
                    ->where('id_cronograma', $lista->id_reuniao)
                    ->where('data', $data_atual)
                    ->update([
                        'nr_acompanhantes' => $nrAcomp
                    ]);

                DB::table('presenca_cronograma')
                    ->insert([
                        'id_tratamento' => $idtr,
                        'presenca' => true,
                        'id_dias_cronograma' => $dia_cronograma->id
                    ]);





                app('flasher')->addSuccess('Foi registrada a presença com sucesso.');

                return Redirect()->back();
            }

            app('flasher')->addError('Aconteceu um erro inesperado.');

            return Redirect()->back();
        } catch (\Exception $e) {

            $code = $e->getCode();
            return view('tratamento-erro.erro-inesperado', compact('code'));
        }
    }


    public function visualizar($idtr)
    {
        // try{

        $pessoa = DB::table('tratamento')
            ->leftJoin('encaminhamento', 'tratamento.id_encaminhamento', 'encaminhamento.id')
            ->leftJoin('atendimentos', 'encaminhamento.id_atendimento', 'atendimentos.id')
            ->where('tratamento.id', $idtr)
            ->first();



        $result = DB::table('tratamento AS tr')
            ->select(
                'enc.id AS ide',
                'tr.id AS idtr',
                'enc.id_tipo_encaminhamento',
                'tr.dt_inicio',
                'enc.id_atendimento',
                'enc.status_encaminhamento',
                'tse.descricao AS tsenc',
                'enc.id_tipo_tratamento',
                'id_tipo_entrevista',
                'at.id AS ida',
                'at.id_assistido',
                'p1.dt_nascimento',
                'p1.nome_completo AS nm_1',
                'at.id_representante as idr',
                'p2.nome_completo as nm_2',
                'pa.id AS pid',
                'pa.nome',
                'pr.id AS prid',
                'pr.descricao AS prdesc',
                'pr.sigla AS prsigla',
                'tt.descricao AS desctrat',
                'tx.tipo',
                'p4.nome_completo AS nm_4',
                'at.dh_inicio',
                'at.dh_fim',
                'enc.status_encaminhamento AS tst',
                'tr.id AS idtr',
                'gr.nome AS nomeg',
                'td.nome as nomedia',
                'rm.h_inicio AS rm_inicio',
                'tm.tipo AS tpmotivo',
                'sat.descricao AS statat',
                'sl.numero as sala',
                'tr.dt_fim as final'
            )
            ->leftjoin('encaminhamento AS enc', 'tr.id_encaminhamento', 'enc.id')
            ->leftJoin('atendimentos AS at', 'enc.id_atendimento', 'at.id')
            ->leftjoin('pessoas AS p1', 'at.id_assistido', 'p1.id')
            ->leftjoin('pessoas AS p2', 'at.id_representante', 'p2.id')
            ->leftjoin('pessoas AS p3', 'at.id_atendente_pref', 'p3.id')
            ->leftjoin('associado as ass', 'at.id_atendente', 'ass.id')
            ->leftjoin('pessoas AS p4', 'ass.id_pessoa', 'p4.id')
            ->leftJoin('tp_parentesco AS pa', 'at.parentesco', 'pa.id')
            ->leftJoin('tipo_prioridade AS pr', 'at.id_prioridade', 'pr.id')
            ->leftJoin('tipo_status_encaminhamento AS tse', 'enc.status_encaminhamento', 'tse.id')
            ->leftJoin('tipo_status_atendimento AS sat', 'at.status_atendimento', 'sat.id')
            ->leftJoin('tipo_tratamento AS tt', 'enc.id_tipo_tratamento', 'tt.id')
            ->leftJoin('tp_sexo AS tx', 'p1.sexo', 'tx.id')
            ->leftjoin('cronograma AS rm', 'tr.id_reuniao', 'rm.id')
            ->leftjoin('grupo AS gr', 'rm.id_grupo', 'gr.id')
            ->leftJoin('tipo_motivo AS tm', 'enc.motivo', 'tm.id')
            ->leftJoin('salas as sl', 'rm.id_sala', 'sl.id')
            ->leftJoin('tipo_dia as td', 'rm.dia_semana', 'td.id')
            ->where('at.id_assistido', $pessoa->id_assistido)
            ->where('enc.id_tipo_encaminhamento', 2);

        if ($pessoa->status_encaminhamento < 3) {
            $result = $result->where('enc.status_encaminhamento', '<', 3)
                ->get();
        } else {

            $result = $result->where('tr.id', $idtr)->get();
        }


        // dd($result, $pessoa);
        $list = DB::table('tratamento AS tr')
            ->select('enc.id AS ide', 'enc.id_tipo_encaminhamento', 'enc.dh_enc', 'enc.status_encaminhamento AS tst', 'tr.id AS idtr', 'rm.h_inicio AS rm_inicio', 'dt.id AS idp', 'dt.presenca', 'dc.data', 'gp.nome')
            ->leftjoin('encaminhamento AS enc', 'tr.id_encaminhamento', 'enc.id')
            ->leftjoin('cronograma AS rm', 'tr.id_reuniao', 'rm.id')
            ->leftJoin('presenca_cronograma AS dt', 'tr.id', 'dt.id_tratamento')
            ->leftJoin('dias_cronograma as dc', 'dt.id_dias_cronograma', 'dc.id')
            ->leftjoin('cronograma AS rm1', 'dc.id_cronograma', 'rm1.id')
            ->leftjoin('grupo AS gp', 'rm1.id_grupo', 'gp.id')
            ->where('tr.id', $idtr)
            ->get();



        $faul = DB::table('tratamento AS tr')
            ->select('enc.id AS ide', 'enc.id_tipo_encaminhamento', 'enc.dh_enc', 'enc.status_encaminhamento AS tst', 'tr.id AS idtr', 'rm.h_inicio AS rm_inicio', 'dt.id AS idp',  'dt.presenca')
            ->leftjoin('encaminhamento AS enc', 'tr.id_encaminhamento', 'enc.id')
            ->leftjoin('cronograma AS rm', 'tr.id_reuniao', 'rm.id')
            ->leftJoin('presenca_cronograma AS dt', 'tr.id', 'dt.id_tratamento')
            ->where('tr.id', $idtr)
            ->where('dt.presenca', 0)
            ->count();

        return view('/recepcao-integrada/historico-tratamento', compact('result', 'list', 'faul'));
    }
    // catch(\Exception $e){

    //    $code = $e->getCode( );
    //    return view('tratamento-erro.erro-inesperado', compact('code'));
    //  }
    //   }
    public function job()
    {

        DiasCronogramaOntem::dispatch();
        DiasCronograma::dispatch();
        LimiteFalta::dispatch();
        FimSemanas::dispatch();
        Faltas::dispatch();
        FaltasTrabalhador::dispatch();
        FilaEncaminhamentos::dispatch();
        EntrevistaProamo::dispatch();

        return redirect()->back();
    }


    public function createAvulso()
    {
        try {
            //dd($request->all());
            $hoje = Carbon::today();
            $dia = Carbon::today()->weekday();

            $assistidos = DB::table('pessoas')->select('id', 'nome_completo')->orderBy('nome_completo')->get();

            $reuniao = DB::table('cronograma as cro')
                ->leftJoin('grupo as gr', 'cro.id_grupo', 'gr.id')
                ->leftJoin('salas as sl', 'cro.id_sala', 'sl.id')
                ->leftJoin('tipo_dia as td', 'cro.dia_semana', 'td.id')
                ->where('cro.id_tipo_tratamento', 1)
                ->where('cro.dia_semana', $dia)
                ->where(function ($query) use ($hoje) {
                    $query->whereRaw("cro.data_fim < ?", [$hoje])
                        ->orWhereNull('cro.data_fim');
                })
                ->select('cro.id', 'cro.h_inicio', 'cro.h_fim', 'td.nome as nomedia', 'gr.nome', 'sl.numero as sala')
                ->get();

            // dd($reuniao);

            $motivo = DB::table('tipo_motivo_presenca')->get();


            return view('recepcao-integrada.incluir-avulso', compact('assistidos', 'reuniao', 'motivo'));
        } catch (\Exception $e) {

            $code = $e->getCode();
            return view('tratamento-erro.erro-inesperado', compact('code'));
        }
    }
    public function storeAvulso(Request $request)
    {
        $hoje = Carbon::today();
        $acompanhantes = DB::table('dias_cronograma')
            ->where('id_cronograma', $request->reuniao)
            ->where('data', $hoje)
            ->first();

        // Verifica se o registro existe
        if (!$acompanhantes) {
            // Se não existir, crie um novo registro
            $acompanhantesId = DB::table('dias_cronograma')->insertGetId([
                'id_cronograma' => $request->reuniao,
                'data' => $hoje,
                'nr_acompanhantes' => $request->acompanhantes,
            ]);
        } else {
            // Se existir, atualiza o número de acompanhantes
            $nrAcomp = $acompanhantes->nr_acompanhantes + $request->acompanhantes;

            DB::table('dias_cronograma')
                ->where('id_cronograma', $request->reuniao)
                ->where('data', $hoje)
                ->update([
                    'nr_acompanhantes' => $nrAcomp
                ]);

            $acompanhantesId = $acompanhantes->id; // Use o ID existente
        }

        // Insere a presença do assistido
        DB::table('presenca_cronograma')->insert([
            'presenca' => true,
            'id_pessoa' => $request->assistido,
            'id_dias_cronograma' => $acompanhantesId,
            'id_motivo' => $request->motivo
        ]);

        return redirect('/gerenciar-tratamentos');
    }
}
