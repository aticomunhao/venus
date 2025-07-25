<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class GerenciarProamoController extends Controller
{
    /**

     */
    public function index(Request $request)
    {

        // Retorna o dia de hoje, para o modal de presença
        $now = Carbon::today()->format('Y-m-d');

        // Retorna todos os cronogramas de tratamento Integral
        $dirigentes = DB::table('membro as mem')
            ->select('ass.id_pessoa', 'gr.nome', 'cr.id', 'gr.status_grupo', 'd.nome as dia')
            ->leftJoin('associado as ass', 'mem.id_associado', 'ass.id')
            ->leftJoin('cronograma as cr', 'mem.id_cronograma', 'cr.id')
            ->leftJoin('grupo as gr', 'cr.id_grupo', 'gr.id')
            ->leftJoin('tipo_dia as d', 'cr.dia_semana', 'd.id')
            ->where('cr.id_tipo_tratamento', 4)
            ->distinct('gr.id');


        // Caso o usuário não seja Master Admin, retorna apenas os cronogramas no qual ele é dirigente ou subdirigente
        if (!in_array(36, session()->get('usuario.acesso'))) {
            $dirigentes =  $dirigentes->where('ass.id_pessoa', session()->get('usuario.id_pessoa'))
                ->where('id_funcao', '<', 3); // 1 => Dirigente, 2 => Sub-Dirigente
        }

        $dirigentes = $dirigentes->get();

        // Guarda os IDs dos cronogramas selecionados
        $grupos_autorizados = [];
        foreach ($dirigentes as $dir) {
            $grupos_autorizados[] = $dir->id;
        }

        // Retorna todos os tratamentos ativos em todas as reuniões
        $encaminhamentos = DB::table('tratamento as tr')
            ->select(
                'tr.id',
                'tr.dt_fim',
                'tr.id_reuniao',
                'atd.id as ida',
                'p.nome_completo',
                'cro.h_inicio',
                'cro.h_fim',
                'gr.nome',
                'tr.dt_inicio',
                'tse.nome as status',
                'tr.status as id_status',
                'tr.maca',
                'atd.id_assistido',
                'enc.id as ide',
                'tr.alta_ptd_proamo'
            )
            ->leftJoin('encaminhamento as enc', 'tr.id_encaminhamento', 'enc.id')
            ->leftJoin('cronograma as cro', 'tr.id_reuniao', 'cro.id')
            ->leftJoin('grupo as gr', 'cro.id_grupo', 'gr.id')
            ->leftJoin('atendimentos as atd', 'enc.id_atendimento', 'atd.id')
            ->leftJoin('pessoas as p', 'atd.id_assistido', 'p.id')
            ->leftJoin('tipo_status_tratamento as tse', 'tr.status', 'tse.id')
            ->where('enc.id_tipo_tratamento', 4)
            ->whereIn('tr.status', [1, 2])
            ->whereIn('tr.id_reuniao', $grupos_autorizados);


        $motivosAlta = DB::table('tipo_motivo')->where('vinculado', 2)->orderBy('tipo')->get();

        // Caso seja pesquisado um nome
        if ($request->nome_pesquisa) {
            $encaminhamentos = $encaminhamentos->where('p.nome_completo', 'ilike', "%$request->nome_pesquisa%");
        }

        // Pesquisa de Grupo
        $selected_grupo = $request->grupo;
        if ($request->grupo) { // Caso um cronograma seja pesquisado
            $encaminhamentos = $encaminhamentos->where('tr.id_reuniao', $request->grupo);
        } else { // Caso não seja pesquisado, traz um valor padrão
            $selected_grupo = current($grupos_autorizados);
            $encaminhamentos = $encaminhamentos->where('tr.id_reuniao', current($grupos_autorizados));
        }

        $hoje = Carbon::today();
        $encaminhamentos = $encaminhamentos->orderBy('tr.status')->orderBy('p.nome_completo')->get()->toArray();

        // Busca se existe um PTD ou PTI para este assistido e retorna dados para faltas
        $encaminhamentoPTD = DB::table('tratamento as tr')
            ->leftJoin('encaminhamento as enc', 'tr.id_encaminhamento', 'enc.id')
            ->leftJoin('atendimentos as at', 'enc.id_atendimento', 'at.id')
            ->whereIn('at.id_assistido', array_column($encaminhamentos, 'id_assistido'))
            ->whereIn('enc.id_tipo_tratamento', [1, 2]) // PTD e PTI
            ->where('enc.status_encaminhamento', '<', 3) // Finalizado
            ->pluck('at.id_assistido')
            ->toArray();

        $data = DB::table('presenca_cronograma as pc')
            ->leftJoin('dias_cronograma as dc', 'pc.id_dias_cronograma', 'dc.id')
            ->whereIn('id_tratamento', array_column($encaminhamentos, 'id'))
            ->orderBy('dc.data', 'DESC')
            ->select('dc.data', 'pc.id_tratamento')
            ->get()
            ->toArray();



        $presencas = DB::table('presenca_cronograma as pc')
            ->leftJoin('dias_cronograma as dc', 'pc.id_dias_cronograma', 'dc.id')
            ->whereIn('id_tratamento', array_column($encaminhamentos, 'id'))
            ->where('pc.presenca', true)
            ->select('pc.id_tratamento', DB::raw('COUNT(pc.id_tratamento) as conta'))
            ->groupBy('pc.id_tratamento')
            ->get()
            ->toArray();


        $tratamentos_faltas = DB::table('presenca_cronograma as pc')
            ->leftJoin('dias_cronograma as dc', 'pc.id_dias_cronograma', 'dc.id')
            ->whereIn('id_tratamento', array_column($encaminhamentos, 'id'))
            ->where('pc.presenca', false)
            ->get()
            ->toArray();

        $arrayTratamentosFaltas = array();
        foreach ($tratamentos_faltas as $element) {
            $arrayTratamentosFaltas[$element->id_tratamento][] = $element->data;
        }

        $array = array();
        foreach ($encaminhamentos as $encaminhamento) {
            $frequencias = DB::table('presenca_cronograma as pc')
                ->join('dias_cronograma as dc', 'pc.id_dias_cronograma', 'dc.id')
                ->where('pc.id_tratamento', $encaminhamento->id)
                ->orderBy('dc.data')
                ->select('dc.data', 'pc.presenca')
                ->get();

            $faltasConsecutivas = 0;

            foreach ($frequencias as $freq) {
                if ($freq->presenca) {
                    $faltasConsecutivas = 0; // presença: zera contagem
                } else {
                    $faltasConsecutivas++; // falta: aumenta contagem
                }
            }

            $encaminhamento->faltas = $faltasConsecutivas;
            $encaminhamento->ptd = in_array($encaminhamento->id_assistido, $encaminhamentoPTD);
            $encaminhamento->avaliacao = $hoje->diffInDays(Carbon::parse($encaminhamento->dt_inicio));
            $encaminhamento->data = collect($data)->firstWhere('id_tratamento', $encaminhamento->id)->data ?? null;
        }





        $totalAssistidos = count($encaminhamentos);
        return view('proamo.gerenciar-proamo', compact('encaminhamentos', 'dirigentes', 'selected_grupo', 'now', 'totalAssistidos', 'motivosAlta'));
    }


    public function create()
    {
        //
    }


    public function store(Request $request, String $id) {}


    public function show(string $id)
    {


        // Traz todos os dados do assistido e do Tratamento
        $result = DB::table('tratamento AS tr')
            ->select(
                'enc.id AS ide',
                'tr.dt_inicio',
                'tr.dt_fim',
                'tse.descricao AS tsenc',
                'at.id AS ida',
                'at.id_assistido',
                'p1.dt_nascimento',
                'p1.nome_completo AS nm_1',
                'p2.nome_completo as nm_2',
                'pa.nome',
                'tt.descricao AS desctrat',
                'tx.tipo',
                'p4.nome_completo AS nm_4',
                'at.dh_inicio',
                'at.dh_fim',
                'gr.nome AS nomeg',
                'rm.h_inicio AS rm_inicio',
                'tm.tipo AS tpmotivo',
                'sat.descricao AS statat',
                'sl.numero as sala',
                'tr.id_reuniao'
            )
            ->leftjoin('encaminhamento AS enc', 'tr.id_encaminhamento', 'enc.id')
            ->leftJoin('atendimentos AS at', 'enc.id_atendimento', 'at.id')
            ->leftjoin('pessoas AS p1', 'at.id_assistido', 'p1.id')
            ->leftjoin('pessoas AS p2', 'at.id_representante', 'p2.id')
            ->leftjoin('associado as ass', 'at.id_atendente', 'ass.id')
            ->leftjoin('pessoas AS p4', 'ass.id_pessoa', 'p4.id')
            ->leftJoin('tp_parentesco AS pa', 'at.parentesco', 'pa.id')
            ->leftJoin('tipo_status_encaminhamento AS tse', 'enc.status_encaminhamento', 'tse.id')
            ->leftJoin('tipo_status_atendimento AS sat', 'at.status_atendimento', 'sat.id')
            ->leftJoin('tipo_tratamento AS tt', 'enc.id_tipo_tratamento', 'tt.id')
            ->leftJoin('tp_sexo AS tx', 'p1.sexo', 'tx.id')
            ->leftjoin('cronograma AS rm', 'tr.id_reuniao', 'rm.id')
            ->leftjoin('grupo AS gr', 'rm.id_grupo', 'gr.id')
            ->leftJoin('tipo_motivo AS tm', 'enc.motivo', 'tm.id')
            ->leftJoin('salas as sl', 'rm.id_sala', 'sl.id')
            ->where('tr.id', $id)
            ->first();

        // Busca se existe um PTD para este assistido e retorna dados para faltas
        $encaminhamento = DB::table('tratamento as tr')
            ->leftJoin('encaminhamento as enc', 'tr.id_encaminhamento', 'enc.id')
            ->leftJoin('atendimentos AS at', 'enc.id_atendimento', 'at.id')
            ->where('at.id_assistido', $result->id_assistido)
            ->whereIn('enc.id_tipo_tratamento', [1, 2])
            ->where('status_encaminhamento', '<', 3) // Finalizado
            ->select('tr.id', 'enc.id_tipo_tratamento')
            ->first();

        // Traz todas as presenças do assistido nesse Tratamento
        $list = DB::table('presenca_cronograma AS dt')
            ->select(
                'dt.id AS idp',
                'dt.presenca',
                'dc.data',
                'gp.nome',
            )
            ->leftJoin('tratamento as tr', 'dt.id_tratamento', 'tr.id')
            ->leftjoin('encaminhamento AS enc', 'tr.id_encaminhamento', 'enc.id')
            ->leftjoin('cronograma AS rm', 'tr.id_reuniao', 'rm.id')
            ->leftJoin('dias_cronograma as dc', 'dt.id_dias_cronograma', 'dc.id')
            ->leftjoin('cronograma AS rm1', 'dc.id_cronograma', 'rm1.id')
            ->leftjoin('grupo AS gp', 'rm1.id_grupo', 'gp.id')
            ->where('tr.id', $id)
            ->get();

        // Conta a quantidade de faltas do assistido nesse Tratamento
        $faul = DB::table('tratamento AS tr')
            ->select(
                'dt.presenca'
            )
            ->leftjoin('encaminhamento AS enc', 'tr.id_encaminhamento', 'enc.id')
            ->leftjoin('cronograma AS rm', 'tr.id_reuniao', 'rm.id')
            ->leftJoin('presenca_cronograma AS dt', 'tr.id', 'dt.id_tratamento')
            ->where('tr.id', $id)
            ->where('dt.presenca', 0)
            ->count();

        // Inicializa as variáveis, logo que existe uma chance que elas não sejam inicializadas no IF
        $list2 = [];
        $faul2 = '';


        $emergencia = DB::table('presenca_cronograma as dt')
            ->select(
                'dt.id AS idp',
                'dt.presenca',
                'dc.data',
                'gp.nome',
            )
            ->leftJoin('tratamento as tr', 'dt.id_tratamento', 'tr.id')
            ->leftjoin('encaminhamento AS enc', 'tr.id_encaminhamento', 'enc.id')
            ->leftjoin('cronograma AS rm', 'tr.id_reuniao', 'rm.id')
            ->leftJoin('dias_cronograma as dc', 'dt.id_dias_cronograma', 'dc.id')
            ->leftjoin('cronograma AS rm1', 'dc.id_cronograma', 'rm1.id')
            ->leftjoin('grupo AS gp', 'rm1.id_grupo', 'gp.id')
            ->where('id_pessoa', $result->id_assistido)
            ->where('dc.data', '>=', $result->dt_inicio)
            ->whereNull('id_tratamento')
            ->get()
            ->toArray();



        if ($encaminhamento) { // Caso tenha um encaminhamento PTD
            // Retorna as faltas do PTD
            $list2 = DB::table('presenca_cronograma AS dt')
                ->select(
                    'dt.id AS idp',
                    'dt.presenca',
                    'dc.data',
                    'gp.nome'
                )
                ->leftJoin('tratamento AS tr', 'dt.id_tratamento', 'tr.id')
                ->leftjoin('encaminhamento AS enc', 'tr.id_encaminhamento', 'enc.id')
                ->leftjoin('cronograma AS rm', 'tr.id_reuniao', 'rm.id')
                ->leftJoin('dias_cronograma as dc', 'dt.id_dias_cronograma', 'dc.id')
                ->leftjoin('cronograma AS rm1', 'dc.id_cronograma', 'rm1.id')
                ->leftjoin('grupo AS gp', 'rm1.id_grupo', 'gp.id')
                ->where('tr.id', $encaminhamento->id)
                ->get()
                ->toArray();

            // Conta a quantidade de Faltas no PTD
            $faul2 = DB::table('tratamento AS tr')
                ->select(
                    'dt.presenca'
                )
                ->leftjoin('encaminhamento AS enc', 'tr.id_encaminhamento', 'enc.id')
                ->leftjoin('cronograma AS rm', 'tr.id_reuniao', 'rm.id')
                ->leftJoin('presenca_cronograma AS dt', 'tr.id', 'dt.id_tratamento')
                ->where('tr.id', $id)
                ->where('dt.presenca', 0)
                ->count();
        }

        return view('proamo.visualizar-proamo', compact('result', 'list', 'faul', 'list2', 'faul2', 'encaminhamento', 'emergencia'));
    }




    public function update(Request $request, string $ide)
    {
        try {
            $hoje = Carbon::today();

            $dt_hora = Carbon::now();
            $today = Carbon::today()->format('Y-m-d');

            $idAssistido = DB::table('encaminhamento')->where('encaminhamento.id', $ide)
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
                ->whereNot('enc.id', $ide) // Exclui o tratamento de agora
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
                if ($ptdAtivoInfinito) {

                    // Adiciona 8 semanas ao PTD
                    $novaDataFim = Carbon::parse($ptdAtivo->dt_fim)->addWeeks(8); // Soma 8 semanas à data de fim atual

                    // Atualiza o PTD com a nova data de fim
                    DB::table('tratamento')
                        ->where('id', $ptdAtivo->id)
                        ->update([
                            'dt_fim' => $novaDataFim,
                        ]);

                    // Insere no histórico a criação do atendimento
                    DB::table('log_atendimentos')->insert([
                        'id_referencia' => $ptdAtivo->id,
                        'id_usuario' => session()->get('usuario.id_usuario'),
                        'id_acao' => 10, // mudou de Status para
                        'id_origem' => 3, // Tratamento
                        'data_hora' => $dt_hora
                    ]);
                }
            }

            DB::table('encaminhamento AS enc') // Atualiza o encaminhamento para finalizado
                ->where('enc.id', $ide)
                ->update([
                    'status_encaminhamento' => 3,
                    'motivo' => $request->motivo
                ]);

            // Insere no histórico a criação do atendimento
            DB::table('log_atendimentos')->insert([
                'id_referencia' => $ide,
                'id_usuario' => session()->get('usuario.id_usuario'),
                'id_acao' => 1, // mudou de Status para
                'id_origem' => 2, // Encaminhamento
                'id_observacao' => 3, // Finalizado
                'data_hora' => $dt_hora
            ]);

            // Caso esse encaminhamento tenha um tratamento
            $tratamento = DB::table('tratamento')
                ->where('id_encaminhamento', $ide);


            if ($tratamento && $tratamento->exists()) {
                $firstTratamento = $tratamento->first();

                if ($firstTratamento) {
                    $idTratamento = $firstTratamento->id;

                    $tratamento->update([
                        'dt_fim' => $today,
                        'status' => 4, // tratamento Finalizado
                    ]);
                }



                // Insere no histórico a criação do atendimento
                DB::table('log_atendimentos')->insert([
                    'id_referencia' => $idTratamento,
                    'id_usuario' => session()->get('usuario.id_usuario'),
                    'id_acao' => 1, // mudou de Status para
                    'id_origem' => 3, // Tratamento
                    'id_observacao' => 4, // Tratamento finalizado
                    'data_hora' => $dt_hora
                ]);
            }




            app('flasher')->addSuccess('Alta declarada com Sucesso!');
            return redirect()->back();
        } catch (\Exception $e) {
            app('flasher')->addError("Houve um erro inesperado: #" . $e->getCode());
            DB::rollBack();
            return redirect()->back();
        }
    }

    public function altaPtd(String $id)
    {

        $dt_hora = Carbon::now();

        DB::table('tratamento')
            ->where('id', $id)
            ->update([
                'alta_ptd_proamo' => true
            ]);


        // Insere no histórico a criação do atendimento
        DB::table('log_atendimentos')->insert([
            'id_referencia' => $id,
            'id_usuario' => session()->get('usuario.id_usuario'),
            'id_acao' => 3, // foi Editado
            'id_origem' => 3, // Tratamento
            'data_hora' => $dt_hora
        ]);

        return redirect()->back();
    }
}
