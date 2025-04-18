<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class GerenciarIntegralController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {


        // try {

        // Retorna o dia de hoje, para o modal de presença
        $now = Carbon::today()->format('Y-m-d');

        // Retorna todos os cronogramas de tratamento Integral
        $dirigentes = DB::table('membro as mem')
            ->select('ass.id_pessoa', 'gr.nome', 'cr.id', 'gr.status_grupo', 'd.nome as dia')
            ->leftJoin('associado as ass', 'mem.id_associado', 'ass.id')
            ->leftJoin('cronograma as cr', 'mem.id_cronograma', 'cr.id')
            ->leftJoin('grupo as gr', 'cr.id_grupo', 'gr.id')
            ->leftJoin('tipo_dia as d', 'cr.dia_semana', 'd.id')
            ->where('cr.id_tipo_tratamento', 6)
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
                'atd.id as ida',
                'p.nome_completo',
                'cro.h_inicio',
                'cro.h_fim',
                'tr.dt_fim',
                'tr.dt_inicio',
                'tse.nome as status',
                'tr.status as id_status',
                'tr.maca',
                'atd.id_assistido'
            )
            ->leftJoin('encaminhamento as enc', 'tr.id_encaminhamento', 'enc.id')
            ->leftJoin('cronograma as cro', 'tr.id_reuniao', 'cro.id')
            ->leftJoin('atendimentos as atd', 'enc.id_atendimento', 'atd.id')
            ->leftJoin('pessoas as p', 'atd.id_assistido', 'p.id')
            ->leftJoin('tipo_status_tratamento as tse', 'tr.status', 'tse.id')
            ->where('enc.id_tipo_tratamento', 6)
            ->whereIn('tr.status', [1, 2])
            ->whereIn('tr.id_reuniao', $grupos_autorizados)
            ->orderBy('p.nome_completo');


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
        $encaminhamentos = $encaminhamentos->get()->toArray();

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

        foreach ($encaminhamentos as $key => $encaminhamento) {
            $presencasFaltas = DB::table('presenca_cronograma as pc')
                ->leftJoin('dias_cronograma as dc', 'pc.id_dias_cronograma', 'dc.id')
                ->where('pc.id_tratamento', $encaminhamento->id)
                ->orderBy('dc.data', 'asc')
                ->select('pc.presenca', 'dc.data')
                ->get()
                ->toArray();

            $consecutivas = 0;

            foreach ($presencasFaltas as $presenca) {
                if ($presenca->presenca == false) {
                    $consecutivas++;
                } else {
                    $consecutivas = 0; // Zera a contagem se teve uma presença
                }
            }

            $encaminhamento->faltas = $consecutivas;

            // Resto do código (ptd, data, presenca, contagem...)
            $ptdRegular = array_search($encaminhamento->id_assistido, $encaminhamentoPTD) !== false ? $encaminhamentoPTD[array_search($encaminhamento->id, $encaminhamentoPTD)] : null;
            $encaminhamento->ptd = $ptdRegular ? true : false;

        

            $encaminhamento->data = current(array_filter($data, function ($item) use ($encaminhamento) {
                return $item->id_tratamento == $encaminhamento->id;
            })) ? current(array_filter($data, function ($item) use ($encaminhamento) {
                return $item->id_tratamento == $encaminhamento->id;
            }))->data : null;

            $encaminhamento->presenca = array_search($encaminhamento->id, array_column($presencas, 'id_tratamento')) ? $presencas[array_search($encaminhamento->id, array_column($presencas, 'id_tratamento'))]->conta : null;

            if ($encaminhamento->dt_fim) {
                $encaminhamento->contagem = $hoje->diffInWeeks(Carbon::parse($encaminhamento->dt_inicio));
            } else {
                $encaminhamento->contagem = null;
            }
        }



        // Usado para Macas
        $vagas = DB::table('cronograma')->where('id', $selected_grupo)->pluck('max_atend')->toArray(); // Retorna o número máximo de assistidos de um cronograma
        $ocupadas = DB::table('tratamento')->whereNot('maca', null)->where('id_reuniao', $selected_grupo)->where('status', '<', 3)->pluck('maca')->toArray(); // Retorna um array com todas as macas ocuopadas do grupo
        $macasDisponiveis = array_diff(range(1, current($vagas)), $ocupadas); // Gera os números das macas e retira as ocupadas
        $totalAssistidos = count($encaminhamentos);
        return view('Integral.gerenciar-integral', compact('encaminhamentos', 'dirigentes', 'selected_grupo', 'macasDisponiveis', 'totalAssistidos', 'now'));
        // } catch (\Exception $e) {
        //     app('flasher')->addError("Você não tem autorização para acessar esta página");
        //     return redirect('/login/valida');
        // }

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, String $id)
    {

        //Salva a maca no tratamento
        DB::table('tratamento')->where('id', $id)->update([
            'maca' => $request->maca
        ]);

        return redirect('/gerenciar-integral');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //try {

        // Traz todos os dados do assistido e do Tratamento
        $result = DB::table('tratamento AS tr')
            ->select(
                'enc.id AS ide',
                'tr.dt_inicio',
                'tr.dt_fim',
                'tr.id_reuniao',

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
                't.cod_tca'

            )
            ->leftjoin('encaminhamento AS enc', 'tr.id_encaminhamento', 'enc.id')
            ->leftJoin('atendimentos AS at', 'enc.id_atendimento', 'at.id')
            ->leftJoin('registro_tema AS rt', 'at.id', 'rt.id_atendimento')
            ->leftJoin('tipo_temas AS t', 'rt.id_tematica', 't.id')
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
            ->get();

        // Busca se existe um PTD ou PTI para este assistido e retorna dados para faltas
        $encaminhamento = DB::table('tratamento as tr')
            ->leftJoin('encaminhamento as enc', 'tr.id_encaminhamento', 'enc.id')
            ->leftJoin('atendimentos as at', 'enc.id_atendimento', 'at.id')
            ->where('at.id_assistido', current(current($result))->id_assistido)
            ->whereIn('enc.id_tipo_tratamento', [1, 2]) // PTD e PTI
            ->where('enc.status_encaminhamento', '<', 3) // Finalizado
            ->select('enc.id_tipo_tratamento', 'tr.id')
            ->first();


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
            ->where('id_pessoa', current(current($result))->id_assistido)
            ->where('dc.data', '>=', current(current($result))->dt_inicio)
            ->whereNull('id_tratamento')
            ->get()
            ->toArray();

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
                ->get();

            // Conta a quantidade de Faltas no PTD
            $faul2 = DB::table('tratamento AS tr')
                ->select(
                    'dt.presenca'
                )
                ->leftjoin('encaminhamento AS enc', 'tr.id_encaminhamento', 'enc.id')
                ->leftjoin('cronograma AS rm', 'tr.id_reuniao', 'rm.id')
                ->leftJoin('presenca_cronograma AS dt', 'tr.id', 'dt.id_tratamento')
                ->where('dt.presenca', 0)
                ->where('tr.id', $encaminhamento->id)
                ->count();
        }

        return view('Integral.historico-integral', compact('result', 'list', 'faul', 'list2', 'faul2', 'encaminhamento', 'emergencia'));
        // } catch (\Exception $e) {

        //     app('flasher')->addError("Houve um erro inesperado: #" . $e->getCode());
        //     return redirect()->back();
        // }
    }
    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
       try {

            DB::table('tratamento')->where('id', $id)->update(['dt_fim' => null]);
            app('flasher')->addSuccess('Limite de semanas removido com Sucesso!');

            return redirect()->back();
        } catch (\Exception $e) {

            app('flasher')->addError("Houve um erro inesperado: #" . $e->getCode());
            DB::rollBack();
            return redirect()->back();
        }
    }

    public function alta(Request $request, string $id)
    {
        try {
            $hoje = Carbon::today();
            $id_encaminhamento = DB::table('tratamento')->where('id', $id)->first();
            DB::table('tratamento')->where('id', $id)->update(['status' => 4, 'dt_fim' => $hoje]);
            DB::table('encaminhamento')->where('id', $id_encaminhamento->id_encaminhamento)->update(['status_encaminhamento' => 3]);

            app('flasher')->addSuccess('Alta declarada com Sucesso!');

            return redirect()->back();
        } catch (\Exception $e) {

            app('flasher')->addError("Houve um erro inesperado: #" . $e->getCode());
            DB::rollBack();
            return redirect()->back();
        }
    }
    /**
     * Remove the specified resource from storage.
     */
}
