<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;

class GerenciarIntegralController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function paginate($items, $perPage = 5, $page = null)
    {
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
        $total = count($items);
        $currentpage = $page;
        $offset = ($currentpage * $perPage) - $perPage;
        $itemstoshow = array_slice($items, $offset, $perPage);
        return new LengthAwarePaginator($itemstoshow, $total, $perPage);
    }

    public function index(Request $request)
    {


        try {

            $dirigentes = DB::table('membro as mem')
                ->select('ass.id_pessoa', 'gr.nome', 'cr.id', 'gr.status_grupo', 'd.nome as dia')
                ->leftJoin('associado as ass', 'mem.id_associado', 'ass.id')
                ->leftJoin('cronograma as cr', 'mem.id_cronograma', 'cr.id')
                ->leftJoin('grupo as gr', 'cr.id_grupo', 'gr.id')
                ->leftJoin('tipo_dia as d', 'cr.dia_semana', 'd.id')
                ->where('cr.id_tipo_tratamento', 6)
                // ->where('cr.status_reuniao', '<>', 2)
                ->distinct('gr.id');

            if (!in_array(36, session()->get('usuario.acesso'))) {
                $dirigentes =  $dirigentes->where('ass.id_pessoa', session()->get('usuario.id_pessoa'))
                    ->where('id_funcao', '<', 3);
            }

            $dirigentes = $dirigentes->get();
            $grupos_autorizados = [];
            foreach ($dirigentes as $dir) {
                $grupos_autorizados[] = $dir->id;
            }




            $encaminhamentos = DB::table('tratamento as tr')
                ->select('tr.id', 'p.nome_completo', 'cro.h_inicio', 'cro.h_fim', 'gr.nome', 'tr.dt_fim', 'tse.nome as status', 'tr.status as id_status', 'tr.maca')
                ->leftJoin('encaminhamento as enc', 'tr.id_encaminhamento', 'enc.id')
                ->leftJoin('cronograma as cro', 'tr.id_reuniao', 'cro.id')
                ->leftJoin('grupo as gr', 'cro.id_grupo', 'gr.id')
                ->leftJoin('atendimentos as atd', 'enc.id_atendimento', 'atd.id')
                ->leftJoin('pessoas as p', 'atd.id_assistido', 'p.id')
                ->leftJoin('tipo_status_tratamento as tse', 'tr.status', 'tse.id')
                ->where('enc.id_tipo_tratamento', 6)
                ->whereIn('tr.status', [1, 2])
                ->whereIn('tr.id_reuniao', $grupos_autorizados);


            if ($request->nome_pesquisa) {
                $encaminhamentos = $encaminhamentos->where('p.nome_completo', 'ilike', "%$request->nome_pesquisa%");
            }
            $selected_grupo = $request->grupo;
            if ($request->grupo) {
                $encaminhamentos = $encaminhamentos->where('tr.id_reuniao', $request->grupo);
            }
            if (!$request->grupo) {
                $selected_grupo = $grupos_autorizados[0];
                $encaminhamentos = $encaminhamentos->where('tr.id_reuniao', $grupos_autorizados[0]);
            }

            $vagas = DB::table('cronograma')->where('id', $selected_grupo)->pluck('max_atend')->toArray();
            $ocupadas = DB::table('tratamento')->whereNot('maca', null)->where('id_reuniao', $selected_grupo)->where('status', 2)->pluck('maca')->toArray();

            $macasDisponiveis = array_diff(range(1, current($vagas)), $ocupadas);
            // Usando paginate para obter os resultados com paginação
            $totalAssistidos = $encaminhamentos->count();
            $encaminhamentos = $encaminhamentos->orderBy('tr.status', 'DESC')
                ->orderBy('p.nome_completo')
                ->paginate(50);// Paginação com 5 itens por página


        } catch (\Exception $e) {

            app('flasher')->addError("Você não tem autorização para acessar esta página");
            return redirect('/login/valida');
        }


        return view('Integral.gerenciar-integral', compact('encaminhamentos', 'dirigentes', 'selected_grupo', 'macasDisponiveis', 'totalAssistidos'));
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
        try {
            $result = DB::table('tratamento AS tr')
                ->select('enc.id AS ide', 'tr.id AS idtr', 'enc.id_tipo_encaminhamento', 'dh_enc', 'enc.id_atendimento', 'enc.status_encaminhamento', 'tse.descricao AS tsenc', 'enc.id_tipo_tratamento', 'id_tipo_entrevista', 'at.id AS ida', 'at.id_assistido', 'p1.dt_nascimento', 'p1.nome_completo AS nm_1', 'at.id_representante as idr', 'p2.nome_completo as nm_2', 'pa.id AS pid',  'pa.nome', 'pr.id AS prid', 'pr.descricao AS prdesc', 'pr.sigla AS prsigla', 'tt.descricao AS desctrat', 'tx.tipo', 'p4.nome_completo AS nm_4', 'at.dh_inicio', 'at.dh_fim', 'enc.status_encaminhamento AS tst', 'tr.id AS idtr', 'gr.nome AS nomeg', 'rm.h_inicio AS rm_inicio', 'tm.tipo AS tpmotivo', 'sat.descricao AS statat', 'sl.numero as sala', 't.cod_tca')
                ->leftjoin('encaminhamento AS enc', 'tr.id_encaminhamento', 'enc.id')
                ->leftJoin('atendimentos AS at', 'enc.id_atendimento', 'at.id')
                ->leftJoin('registro_tema AS rt', 'at.id', 'rt.id_atendimento')
                ->leftJoin('tipo_temas AS t', 'rt.id_tematica', 't.id')
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
                ->where('tr.id', $id)
                ->get();


            $encaminhamento = DB::table('tratamento as tr')
                ->leftJoin('encaminhamento as enc', 'tr.id_encaminhamento', 'enc.id')
                ->where('enc.id_atendimento', $result[0]->ida)
                ->where('enc.id_tipo_tratamento', 1)
                ->select('tr.id')
                ->first();


            $list = DB::table('tratamento AS tr')
                ->select('enc.id AS ide', 'enc.id_tipo_encaminhamento', 'enc.dh_enc', 'enc.status_encaminhamento AS tst', 'tr.id AS idtr', 'rm.h_inicio AS rm_inicio', 'dt.id AS idp', 'dt.presenca', 'dc.data', 'gp.nome')
                ->leftjoin('encaminhamento AS enc', 'tr.id_encaminhamento', 'enc.id')
                ->leftjoin('cronograma AS rm', 'tr.id_reuniao', 'rm.id')
                ->leftJoin('presenca_cronograma AS dt', 'tr.id', 'dt.id_tratamento')
                ->leftJoin('dias_cronograma as dc', 'dt.id_dias_cronograma', 'dc.id')
                ->leftjoin('cronograma AS rm1', 'dc.id_cronograma', 'rm1.id')
                ->leftjoin('grupo AS gp', 'rm1.id_grupo', 'gp.id')
                ->where('tr.id', $id)
                ->get();


            $faul = DB::table('tratamento AS tr')
                ->select('enc.id AS ide', 'enc.id_tipo_encaminhamento', 'enc.dh_enc', 'enc.status_encaminhamento AS tst', 'tr.id AS idtr', 'rm.h_inicio AS rm_inicio', 'dt.id AS idp',  'dt.presenca')
                ->leftjoin('encaminhamento AS enc', 'tr.id_encaminhamento', 'enc.id')
                ->leftjoin('cronograma AS rm', 'tr.id_reuniao', 'rm.id')
                ->leftJoin('presenca_cronograma AS dt', 'tr.id', 'dt.id_tratamento')
                ->where('tr.id', $id)
                ->where('dt.presenca', 0)
                ->count();

            $list2 = [];
            $faul2 = '';

            if ($encaminhamento) {
                $list2 = DB::table('tratamento AS tr')
                    ->select('enc.id AS ide', 'enc.id_tipo_encaminhamento', 'enc.dh_enc', 'enc.status_encaminhamento AS tst', 'tr.id AS idtr', 'rm.h_inicio AS rm_inicio', 'dt.id AS idp', 'dt.presenca', 'dc.data', 'gp.nome')
                    ->leftjoin('encaminhamento AS enc', 'tr.id_encaminhamento', 'enc.id')
                    ->leftjoin('cronograma AS rm', 'tr.id_reuniao', 'rm.id')
                    ->leftJoin('presenca_cronograma AS dt', 'tr.id', 'dt.id_tratamento')
                    ->leftJoin('dias_cronograma as dc', 'dt.id_dias_cronograma', 'dc.id')
                    ->leftjoin('cronograma AS rm1', 'dc.id_cronograma', 'rm1.id')
                    ->leftjoin('grupo AS gp', 'rm1.id_grupo', 'gp.id')
                    ->where('tr.id', $encaminhamento->id)
                    ->get();

                $faul2 = DB::table('tratamento AS tr')
                    ->select('enc.id AS ide', 'enc.id_tipo_encaminhamento', 'enc.dh_enc', 'enc.status_encaminhamento AS tst', 'tr.id AS idtr', 'rm.h_inicio AS rm_inicio', 'dt.id AS idp',  'dt.presenca')
                    ->leftjoin('encaminhamento AS enc', 'tr.id_encaminhamento', 'enc.id')
                    ->leftjoin('cronograma AS rm', 'tr.id_reuniao', 'rm.id')
                    ->leftJoin('presenca_cronograma AS dt', 'tr.id', 'dt.id_tratamento')
                    ->where('dt.presenca', 0)
                    ->count();
            }

            return view('Integral.historico-integral', compact('result', 'list', 'faul', 'list2', 'faul2'));
        } catch (\Exception $e) {

            app('flasher')->addError("Houve um erro inesperado: #" . $e->getCode());
            return redirect()->back();
        }
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
            $hoje = Carbon::today();

            $tratamento = DB::table('tratamento')->where('id', $id)->first();

            if ($tratamento->dt_fim != null) {
                DB::table('tratamento')->where('id', $id)->update(['dt_fim' => null]);
            } elseif ($tratamento->dt_fim == null) {

                $id_encaminhamento = DB::table('tratamento')->where('id', $id)->first();
                DB::table('tratamento')->where('id', $id)->update(['status' => 4, 'dt_fim' => $hoje]);
                DB::table('encaminhamento')->where('id', $id_encaminhamento->id_encaminhamento)->update(['status_encaminhamento' => 3]);
            } else {
                app('flasher')->addError('Houve um erro inesperado');
            }

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
