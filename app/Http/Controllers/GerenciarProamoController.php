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
        $now = Carbon::today();

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
                'atd.id_assistido'
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

        $encaminhamentos = $encaminhamentos->get()->toArray();
        $hoje = Carbon::today();
        foreach ($encaminhamentos as $key => $encaminhamento) {

            // Busca se existe um PTD ou PTI para este assistido e retorna dados para faltas
            $encaminhamentoPTD = DB::table('tratamento as tr')
                ->leftJoin('encaminhamento as enc', 'tr.id_encaminhamento', 'enc.id')
                ->leftJoin('atendimentos as at', 'enc.id_atendimento', 'at.id')
                ->where('at.id_assistido', $encaminhamento->id_assistido)
                ->whereIn('enc.id_tipo_tratamento', [1, 2]) // PTD e PTI
                ->where('status_encaminhamento', '<', 3) // Finalizado
                ->select('tr.id')
                ->first();

            $encaminhamentoPTD ? $encaminhamento->ptd = true : $encaminhamento->ptd = false;


            $encaminhamento->contagem = $hoje->diffInDays(Carbon::parse($encaminhamento->dt_inicio));
        }


        return view('proamo.gerenciar-proamo', compact('encaminhamentos', 'dirigentes', 'selected_grupo', 'now'));
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
            ->whereIn('enc.id_tipo_tratamento', [1,2])
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

        $list2 = array_merge($emergencia, $list2);
        array_multisort(array_column($list2, 'data'), SORT_DESC ,$list2);

        return view('proamo.visualizar-proamo', compact('result', 'list', 'faul', 'list2', 'faul2', 'encaminhamento'));
    }




    public function update(Request $request, string $id)
    {
        $hoje = Carbon::today();
        $id_encaminhamento = DB::table('tratamento')->where('id', $id)->first();
        DB::table('tratamento')->where('id', $id)->update(['status' => 4, 'dt_fim' => $hoje]);
        DB::table('encaminhamento')->where('id', $id_encaminhamento->id_encaminhamento)->update(['status_encaminhamento' => 3]);
        return redirect()->back();
    }
}

