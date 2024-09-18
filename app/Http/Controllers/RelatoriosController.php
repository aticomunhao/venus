<?php

namespace App\Http\Controllers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class RelatoriosController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function indexAFI(Request $request)
    {
        $afiSelecionado = $request->afi;
        $dt_inicio = $request->dt_inicio == null ? (Carbon::now()->subMonth()->firstOfMonth()->format('Y-m-d')) : $request->dt_inicio;
        $dt_fim =  $request->dt_fim == null ? Carbon::today()->format('Y-m-d') : $request->dt_fim;

        $atendentes = DB::table('membro as m')
            ->leftJoin('cronograma as cro', 'm.id_cronograma', 'cro.id')
            ->leftJoin('grupo as gr', 'cro.id_grupo', 'gr.id')
            ->leftJoin('associado as ass', 'm.id_associado', 'ass.id')
            ->leftJoin('pessoas as p', 'ass.id_pessoa', 'p.id')
            ->where(function ($query) {
                $query->where('id_funcao', 5);
                $query->orWhere('id_funcao', 6);
            })
            ->where('id_tipo_grupo', 3)
            ->distinct('p.nome_completo')
            ->orderBy('p.nome_completo')
            ->select('m.id_associado', 'p.nome_completo')
            ->get();

        foreach ($atendentes as $key => $atendente) {
            $diasAtendente = DB::table('atendente_dia')
                ->where('id_associado', $atendente->id_associado)
                ->get();
            $dados = [];
            foreach ($diasAtendente as $mKey => $diaAtendente) {
                foreach ($diasAtendente as $diaAtendenteCompare) {
                    if (Carbon::parse($diaAtendente->dh_inicio)->format('Y-m-d') == Carbon::parse($diaAtendenteCompare->dh_inicio)->format('Y-m-d') and $diaAtendente->id != $diaAtendenteCompare->id) {
                        unset($diasAtendente[$mKey]);
                    }
                }
            }




            // Pega um array com todas as reuniões que o atendente participa
            $cronogramasParticipa = DB::table('membro')
                ->where('id_associado', $atendente->id_associado)
                ->where(function ($query) {
                    $query->where('id_funcao', 5);
                    $query->orWhere('id_funcao', 6);
                })
                ->pluck('id_cronograma');

            //Pega datas como id do cronograma, e data das reuniões que aconteceram durante o período selecionado
            $cronogramaAFI = DB::table('dias_cronograma as dc')
                ->leftJoin('cronograma as cro', 'dc.id_cronograma', 'cro.id')
                ->leftJoin('grupo as gr', 'cro.id_grupo', 'gr.id')
                ->leftJoin('tipo_dia as td', 'cro.dia_semana', 'td.id')
                ->where('id_tipo_grupo', 3)
                ->where('dc.data', '>', $dt_inicio)
                ->where('dc.data', '<', $dt_fim)
                ->whereIn('cro.id', $cronogramasParticipa)
                ->select('cro.id', 'dc.data', 'gr.nome', 'cro.h_inicio', 'td.nome as dia')
                ->orderBy('dc.data')->get();

            //Confere se a data de uma reunião está presente na lista de distinct criada acima, gerando um array completo, com o dado de presenca   
            foreach ($cronogramaAFI as $datas) {
                $i = 0;
                foreach ($diasAtendente as $diaAtendente) {
                    if ($datas->data == Carbon::parse($diaAtendente->dh_inicio)->format('Y-m-d') and $diaAtendente->id_grupo == $datas->id) {
                        array_push($dados, ['id' => $datas->id, 'data' => $datas->data, 'nome' => $datas->nome, 'h_inicio' => $datas->h_inicio, 'dia' => $datas->dia, 'presenca' => 1]);
                        break;
                    } elseif (++$i === count($diasAtendente)) {
                        array_push($dados, ['id' => $datas->id, 'data' => $datas->data, 'nome' => $datas->nome, 'h_inicio' => $datas->h_inicio, 'dia' => $datas->dia, 'presenca' => 0]);
                    }
                }
            }

            //Gera numa variável a contagem total de cada item de presença
            $contaFaltas = array_count_values(array_column($dados, 'presenca'));
            $atendentes[$key]->presenca = $contaFaltas;
        }
        return view('relatorios.gerenciar-relatorio-afi', compact('atendentes', 'afiSelecionado', 'dt_inicio', 'dt_fim'));
    }


    public function visualizarAFI(Request $request)
    {

        // Devolve a data selecioanda, se não tiver nenhuma coloca uma padrão
        $dt_inicio = $request->dt_inicio == null ? (Carbon::now()->subMonth()->firstOfMonth()->format('Y-m-d')) : $request->dt_inicio;
        $dt_fim =  $request->dt_fim == null ? Carbon::today()->format('Y-m-d') : $request->dt_fim;
        //  dd($request->dt_inicio);

        // Faz um distinct caso o atendente venha duas vezes em um mesmo dia
        $diasAtendente = DB::table('atendente_dia')
            ->where('id_associado', $request->afi) // Colocar trava de data
            ->get();
        $dados = [];
        foreach ($diasAtendente as $mKey => $diaAtendente) {
            foreach ($diasAtendente as $diaAtendenteCompare) {
                if (Carbon::parse($diaAtendente->dh_inicio)->format('Y-m-d') == Carbon::parse($diaAtendenteCompare->dh_inicio)->format('Y-m-d') and $diaAtendente->id != $diaAtendenteCompare->id) {
                    unset($diasAtendente[$mKey]);
                }
            }
        }

        // Pega um array com todas as reuniões que o atendente participa
        $cronogramasParticipa = DB::table('membro')
            ->where('id_associado', $request->afi)
            ->where(function ($query) {
                $query->where('id_funcao', 5);
                $query->orWhere('id_funcao', 6);
            })
            ->pluck('id_cronograma');

        //Pega datas como id do cronograma, e data das reuniões que aconteceram durante o período selecionado
        $cronogramaAFI = DB::table('dias_cronograma as dc')
            ->leftJoin('cronograma as cro', 'dc.id_cronograma', 'cro.id')
            ->leftJoin('grupo as gr', 'cro.id_grupo', 'gr.id')
            ->leftJoin('tipo_dia as td', 'cro.dia_semana', 'td.id')
            ->where('id_tipo_grupo', 3)
            ->where('dc.data', '>', $dt_inicio)
            ->where('dc.data', '<', $dt_fim)
            ->whereIn('cro.id', $cronogramasParticipa)
            ->select('cro.id', 'dc.data', 'gr.nome', 'cro.h_inicio', 'td.nome as dia')
            ->orderBy('dc.data')->get();

        //Confere se a data de uma reunião está presente na lista de distinct criaada acima, gerando um array completo, com o dado de presenca   
        foreach ($cronogramaAFI as $datas) {
            $i = 0;
            foreach ($diasAtendente as $diaAtendente) {
                if ($datas->data == Carbon::parse($diaAtendente->dh_inicio)->format('Y-m-d') and $diaAtendente->id_grupo == $datas->id) {
                    array_push($dados, ['id' => $datas->id, 'data' => $datas->data, 'nome' => $datas->nome, 'h_inicio' => $datas->h_inicio, 'dia' => $datas->dia, 'presenca' => 1]);
                    break;
                } elseif (++$i === count($diasAtendente)) {
                    array_push($dados, ['id' => $datas->id, 'data' => $datas->data, 'nome' => $datas->nome, 'h_inicio' => $datas->h_inicio, 'dia' => $datas->dia, 'presenca' => 0]);
                }
            }
        }

        //Gera numa variável a contagem total de cada item de presença
        $contaFaltas = array_count_values(array_column($dados, 'presenca'));
        // dd($diasAtendente, $cronogramaAFI, $dados, $cronogramasParticipa, $contaFaltas);

        // Devolve todos os atendentes membros de uma reunião
        $atendentes = DB::table('membro as m')
            ->leftJoin('cronograma as cro', 'm.id_cronograma', 'cro.id')
            ->leftJoin('grupo as gr', 'cro.id_grupo', 'gr.id')
            ->leftJoin('associado as ass', 'm.id_associado', 'ass.id')
            ->leftJoin('pessoas as p', 'ass.id_pessoa', 'p.id')
            ->where(function ($query) {
                $query->where('id_funcao', 5);
                $query->orWhere('id_funcao', 6);
            })
            ->where('id_tipo_grupo', 3)
            ->distinct('p.nome_completo')
            ->orderBy('p.nome_completo')
            ->select('m.id_associado', 'p.nome_completo')
            ->get();

        //Devolve dados como nome do atendente selecionado na pesquisa
        $afiSelecionado = DB::table('membro as m')
            ->leftJoin('associado as ass', 'm.id_associado', 'ass.id')
            ->leftJoin('pessoas as p', 'ass.id_pessoa', 'p.id')
            ->where('m.id_associado', $request->afi)
            ->select('m.id_associado', 'p.nome_completo')
            ->first();


        return view('relatorios.visualizar-presenca-afi', compact('contaFaltas', 'dados', 'atendentes', 'afiSelecionado', 'dt_inicio', 'dt_fim'));
    }

    /**
     * Show the form for creating a new resource.
     */


    /**
     * Store a newly created resource in storage.
     */
    public function tematicas(Request $request)
    {
        $dt_inicio = $request->dt_inicio == null ? (Carbon::now()->subMonth()->firstOfMonth()->format('Y-m-d')) : $request->dt_inicio;
        $dt_fim =  $request->dt_fim == null ? Carbon::today()->format('Y-m-d') : $request->dt_fim;
        $tematicas = DB::table('registro_tema as rt')
        ->leftJoin('atendimentos as at', 'rt.id_atendimento', 'at.id')
        ->rightJoin('tipo_temas as tm', 'rt.id_tematica', 'tm.id')
        ->where('at.dh_chegada', '>=', $dt_inicio)
        ->where('at.dh_chegada', '<', $dt_fim)
        ->groupBy('nm_tca')
        ->select('nm_tca', DB::raw("count(*) as total"))
        ->get();

        $tematicas = json_decode(json_encode($tematicas), true);
        $nomes_temas = DB::table('tipo_temas')->pluck('nm_tca');
    
        $tematicasArray = array();
        foreach($nomes_temas as $tema){
            $tematicasArray[$tema] = in_array($tema,array_column($tematicas, 'nm_tca')) ? $tematicas[array_search($tema,array_column($tematicas, 'nm_tca'))]['total'] : 0;
        }


        return view('relatorios.tematicas', compact('tematicasArray', 'dt_inicio', 'dt_fim'));
    }

    /**
     * Display the specified resource.
     */
    public function cronograma(Request $request)
    {

        $cronogramas = DB::table('cronograma as cro')
            ->leftJoin('grupo as gr', 'cro.id_grupo', 'gr.id')
            ->leftJoin('salas as sl', 'cro.id_sala', 'sl.id')
            ->leftJoin('setor as st', 'gr.id_setor', 'st.id')
            ->leftJoin('tipo_dia as td', 'cro.dia_semana', 'td.id')
            ->select('cro.id', 'gr.nome', 'st.nome as setor', 'st.sigla', 'cro.h_inicio', 'cro.h_fim', 'cro.data_inicio', 'cro.data_fim', 'cro.dia_semana', 'td.nome as dia');




        $salas = DB::table('salas')->get();

        $cronogramasPesquisa = DB::table('cronograma as cro')
            ->leftJoin('grupo as gr', 'cro.id_grupo', 'gr.id')
            ->select('gr.id', 'gr.nome')
            ->distinct('gr.nome')
            ->orderBy('gr.nome')
            ->get();

        $requestSala = $request->sala;
        if ($requestSala) {
            $cronogramas = $cronogramas->where('cro.id_sala', $requestSala);
        }
        $requestGrupo = $request->grupo;
        if ($request->grupo) {
            $cronogramas = $cronogramas->where('cro.id_grupo', $requestGrupo);
        }
        // dd($cronogramas->get());
        $cronogramas = $cronogramas->get();

        $eventosCronogramas = [];
        $i = 0;
        foreach ($cronogramas as $cronograma) {

            $eventosCronogramas[$i]['id'] = $i;
            $eventosCronogramas[$i]['title'] = $cronograma->sigla;
            $eventosCronogramas[$i]['daysOfWeek'] = [$cronograma->dia_semana];
            $eventosCronogramas[$i]['startTime'] = $cronograma->h_inicio;
            $eventosCronogramas[$i]['endTime'] = $cronograma->h_fim;
            $cronograma->data_inicio == null ? '2024-09-02' : $eventosCronogramas[$i]['startRecur'] = $cronograma->data_inicio;
            $cronograma->data_fim == null ? null : $eventosCronogramas[$i]['endRecur'] = $cronograma->data_fim;
            $eventosCronogramas[$i]['extendedProps'] =
                [
                    'setor' => $cronograma->setor,
                    'dia' => $cronograma->dia,
                    'nome' => $cronograma->nome,
                    'h_inicio' => $cronograma->h_inicio,
                    'h_fim' => $cronograma->h_fim
                ];

            $i++;
        }






        json_encode($eventosCronogramas);

        //   dd($cronogramas, $eventosCronogramas);
        return view('relatorios.relatorio-salas-cronograma', compact('eventosCronogramas', 'salas', 'cronogramasPesquisa', 'requestSala', 'requestGrupo'));
    }

    public function indexmembro(Request $request)
    {
        // Obter os parâmetros de busca
        $setorId = $request->input('setor');
        $grupoId = $request->input('grupo');
        $diaId = $request->input('dia');
        $nomeId = $request->input('nome');
        $funcaoId = $request->input('funcao');

        // Definir o número de itens por página
        $itemsPerPage = 50;
        $setoresAutorizado = session()->get('usuario.setor');


        // Obter os atendentes para o select2
        $atendentesParaSelect = DB::table('membro AS m')
            ->select('m.id_associado AS ida', 'p.nome_completo AS nm_4')
            ->leftJoin('associado AS a', 'm.id_associado', 'a.id')
            ->leftJoin('pessoas AS p', 'a.id_pessoa', 'p.id')
            ->leftJoin('cronograma as cro', 'm.id_cronograma', 'cro.id')
            ->leftJoin('grupo as gr', 'cro.id_grupo', 'gr.id')
            ->where('p.status', 1)
            ->distinct()
            ->orderBy('p.nome_completo')
            ->get();


        $membrosQuery = DB::table('membro as m')
            ->leftJoin('cronograma as cro', 'm.id_cronograma', 'cro.id')
            ->leftJoin('grupo as gr', 'cro.id_grupo', 'gr.id')
            ->leftJoin('associado as ass', 'm.id_associado', 'ass.id')
            ->leftJoin('pessoas as p', 'ass.id_pessoa', 'p.id')
            ->leftJoin('setor as st', 'gr.id_setor', 'st.id')
            ->leftJoin('tipo_dia as td', 'cro.dia_semana', 'td.id')
            ->leftJoin('tipo_funcao as tf', 'm.id_funcao', 'tf.id')
            ->orderBy('p.nome_completo')
            ->whereIn('gr.id_setor', $setoresAutorizado)
            ->select('m.id', 'p.nome_completo', 'gr.nome as grupo_nome', 'st.nome as setor_nome', 'st.sigla as setor_sigla', 'td.nome as dia_nome', 'cro.h_inicio', 'cro.h_fim', 'tf.nome as nome_funcao', 'st.nome as sala') // Selecionando o nome da função
            ->when($setorId, function ($query, $setorId) {
                return $query->where('st.id', $setorId);
            })
            ->when($grupoId, function ($query, $grupoId) {

                return $query->where('gr.id', $grupoId);
            })
            ->when($diaId, function ($query, $diaId) {
                return $query->where('cro.dia_semana', $diaId);
            })
            ->when($funcaoId, function ($query, $funcaoId) {
                return $query->where('m.id_funcao', $funcaoId);
            })
            ->when($diaId == 0 && $diaId != null, function ($query) {
                return $query->where('cro.dia_semana', 0);
            })
            ->when($nomeId, function ($query, $nomeId) {
                return $query->where('m.id_associado', $nomeId);
            });


        // Paginar os resultados
        $membros = $membrosQuery->get();

        // Obter os grupos
        $grupo = DB::table('grupo')
            ->leftJoin('setor', 'grupo.id_setor', 'setor.id')
            ->select('grupo.id', 'grupo.nome as nome_grupo', 'setor.sigla')
            ->whereIn('id_setor', $setoresAutorizado)
            ->get();

        // Obter os setores
        $setor = DB::table('setor')
            ->select('id', 'nome', 'sigla')
            ->whereIn('id', $setoresAutorizado)
            ->get();

        // Obter os dias
        $dias = DB::table('tipo_dia')
            ->select('id', 'nome')
            ->get();

        $funcao = DB::table('tipo_funcao')->get();

        $result = array();
        foreach ($membros as $element) {
            $result[$element->nome_completo][$element->id] = $element;
        }

        //      dd($membros, $result);

        $result = $this->paginate($result, 50);
        $result->withPath('');
        return view('relatorios.gerenciar-relatorio-pessoas-grupo', compact('membros', 'grupo', 'setor', 'dias', 'atendentesParaSelect', 'result', 'funcao'));
    }

    public function paginate($items, $perPage = 5, $page = null)
    {
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
        $total = count($items);
        $currentpage = $page;
        $offset = ($currentpage * $perPage) - $perPage;
        $itemstoshow = array_slice($items, $offset, $perPage);
        return new LengthAwarePaginator($itemstoshow, $total, $perPage);
    }

    public function relatorioReuniao(Request $request)
    {
        $dt_inicio = $request->dt_inicio == null ? (Carbon::now()->subMonth()->firstOfMonth()->format('Y-m-d')) : $request->dt_inicio;
        $dt_fim =  $request->dt_fim == null ? Carbon::today()->format('Y-m-d') : $request->dt_fim;
        $idCronogramaPesquisa = $request->nome_grupo;

        //Traz todas as reuniões onde a pessoa logada é Dirigente ou Sub-dirigente
        $cronogramasAutorizados = DB::table('membro as m')
            ->leftJoin('associado as ass', 'm.id_associado', 'ass.id')
            ->where('ass.id_pessoa', session()->get('usuario.id_pessoa'))
            ->where('id_funcao', '<', 3)
            ->distinct('m.id_cronograma')
            ->pluck('m.id_cronograma');

        $reunioesDirigentes = DB::table('membro as mem')
            ->select('cr.id', 'gr.nome', 'cr.id', 'gr.status_grupo', 'd.nome as dia', 'cr.h_inicio', 'cr.h_fim')
            ->leftJoin('associado as ass', 'mem.id_associado', 'ass.id')
            ->leftJoin('cronograma as cr', 'mem.id_cronograma', 'cr.id')
            ->leftJoin('grupo as gr', 'cr.id_grupo', 'gr.id')
            ->leftJoin('tipo_dia as d', 'cr.dia_semana', 'd.id')
            ->when($idCronogramaPesquisa, function ($query, $idCronogramaPesquisa) {
                return $query->where('cr.id', $idCronogramaPesquisa);
            })
            ->orderBy('gr.nome')
            ->distinct('gr.nome');

        $reunioesPesquisa = DB::table('membro as mem')
            ->select('cr.id', 'gr.nome', 'd.nome as dia', 'cr.h_inicio', 'cr.h_fim')
            ->leftJoin('associado as ass', 'mem.id_associado', 'ass.id')
            ->leftJoin('cronograma as cr', 'mem.id_cronograma', 'cr.id')
            ->leftJoin('grupo as gr', 'cr.id_grupo', 'gr.id')
            ->leftJoin('tipo_dia as d', 'cr.dia_semana', 'd.id')
            ->orderBy('gr.nome')
            ->distinct('gr.nome');

        $presencasCountAssistidos = DB::table('presenca_cronograma as pc')
            ->leftJoin('dias_cronograma as dc', 'pc.id_dias_cronograma', 'dc.id')
            ->where('dc.data', '>=', $dt_inicio)
            ->where('dc.data', '<', $dt_fim)
            ->groupBy('presenca')
            ->select('presenca', DB::raw("count(*) as total"));

        $acompanhantes = DB::table('dias_cronograma');

        $presencasCountMembros = DB::table('presenca_membros as pc')
            ->leftJoin('dias_cronograma as dc', 'pc.id_dias_cronograma', 'dc.id')
            ->where('dc.data', '>=', $dt_inicio)
            ->where('dc.data', '<', $dt_fim)
            ->groupBy('presenca')
            ->select('presenca', DB::raw("count(*) as total"));

        if (!in_array(36, session()->get('usuario.acesso'))) {
            $reunioesDirigentes = $reunioesDirigentes->whereIn('mem.id_cronograma', $cronogramasAutorizados);
            $reunioesPesquisa = $reunioesPesquisa->whereIn('mem.id_cronograma', $cronogramasAutorizados);
            $presencasCountAssistidos = $presencasCountAssistidos->whereIn('dc.id_cronograma', $cronogramasAutorizados);
            $acompanhantes = $acompanhantes->whereIn('id_cronograma', $cronogramasAutorizados);
            $presencasCountMembros = $presencasCountMembros->whereIn('dc.id_cronograma', $cronogramasAutorizados);
        }


        $reunioesDirigentes = $reunioesDirigentes->get();
        $reunioesIds = json_decode(json_encode($reunioesDirigentes));

        $reunioesPesquisa = $reunioesPesquisa->get();

        $presencasCountAssistidos = $presencasCountAssistidos->get();
        $presencasCountAssistidos = json_decode(json_encode($presencasCountAssistidos));

        $acompanhantes = $acompanhantes->sum('nr_acompanhantes');


        $presencasCountMembros = $presencasCountMembros->get();
        $presencasCountMembros = json_decode(json_encode($presencasCountMembros));



        if ($presencasCountAssistidos == []) {
            $presencasCountAssistidos[0] = 0;
            $presencasCountAssistidos[1] = 0;
        } elseif (!in_array(false, array_values(array_column($presencasCountAssistidos, 'presenca')))) {
            $presencasCountAssistidos[1] = $presencasCountAssistidos[0]->total;
            $presencasCountAssistidos[0] = 0;
        } elseif (!in_array(true, array_values(array_column($presencasCountAssistidos, 'presenca')))) {
            $presencasCountAssistidos[0] = $presencasCountAssistidos[0]->total;
            $presencasCountAssistidos[1] = 0;
        } else {
            $presencasCountAssistidos[0] = $presencasCountAssistidos[0]->total;
            $presencasCountAssistidos[1] = $presencasCountAssistidos[1]->total;
        }
        $presencasCountAssistidos[2] =  $acompanhantes;

        if ($presencasCountMembros == []) {
            $presencasCountMembros[0] = 0;
            $presencasCountMembros[1] = 0;
        } elseif (!in_array(false, array_values(array_column($presencasCountMembros, 'presenca')))) {
            $presencasCountMembros[1] = $presencasCountMembros[0]->total;
            $presencasCountMembros[0] = 0;
        } elseif (!in_array(true, array_values(array_column($presencasCountMembros, 'presenca')))) {
            $presencasCountMembros[0] = $presencasCountMembros[0]->total;
            $presencasCountMembros[1] = 0;
        } else {
            $presencasCountMembros[0] = $presencasCountMembros[0]->total;
            $presencasCountMembros[1] = $presencasCountMembros[1]->total;
        }
        $presencasCountMembros[2] = 0;

        return view('relatorios.relatorio-assistido-reuniao', compact('reunioesDirigentes', 'presencasCountAssistidos', 'presencasCountMembros', 'reunioesPesquisa', 'dt_inicio', 'dt_fim', 'idCronogramaPesquisa'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function visualizarReuniao(Request $request, string $id)
    {

        $dt_inicio = $request->dt_inicio == null ? (Carbon::now()->subMonth()->firstOfMonth()->format('Y-m-d')) : $request->dt_inicio;
        $dt_fim =  $request->dt_fim == null ? Carbon::today()->format('Y-m-d') : $request->dt_fim;

        //Traz todas as reuniões onde a pessoa logada é Dirigente ou Sub-dirigente

        $grupo = DB::table('cronograma as cr')
            ->leftJoin('grupo as gr', 'cr.id_grupo', 'gr.id')
            ->leftJoin('tipo_dia as td', 'cr.dia_semana', 'td.id')
            ->select('gr.nome', 'td.nome as dia')
            ->where('cr.id', $id)
            ->first();

        $presencasCountAssistidos = DB::table('presenca_cronograma as pc')
            ->leftJoin('dias_cronograma as dc', 'pc.id_dias_cronograma', 'dc.id')
            ->where('dc.data', '>=', $dt_inicio)
            ->where('dc.data', '<', $dt_fim)
            ->where('id_cronograma', $id)
            ->groupBy('presenca')
            ->select('presenca', DB::raw("count(*) as total"));

        $acompanhantes = DB::table('dias_cronograma')->where('id_cronograma', $id);

        $presencasCountMembros = DB::table('presenca_membros as pc')
            ->leftJoin('dias_cronograma as dc', 'pc.id_dias_cronograma', 'dc.id')
            ->where('dc.data', '>=', $dt_inicio)
            ->where('dc.data', '<', $dt_fim)
            ->where('id_cronograma', $id)
            ->groupBy('presenca')
            ->select('presenca', DB::raw("count(*) as total"));







        $presencasAssistidos = DB::table('tratamento as tr')
            ->leftJoin('tipo_status_tratamento as tst', 'tr.status', 'tst.id')
            ->leftJoin('encaminhamento as enc', 'tr.id_encaminhamento', 'enc.id')
            ->leftJoin('presenca_cronograma as pc', 'tr.id', 'pc.id_tratamento')
            ->leftJoin('atendimentos as at', 'enc.id_atendimento', 'at.id')
            ->leftJoin('pessoas as p', 'at.id_assistido', 'p.id')
            ->leftJoin('dias_cronograma as dc', 'pc.id_dias_cronograma', 'dc.id')
            ->leftJoin('cronograma as cro', 'dc.id_cronograma', 'cro.id')
            ->leftJoin('grupo as gr', 'cro.id_grupo', 'gr.id')
            ->where('enc.dh_enc', '>=', $dt_inicio)
            ->where('enc.dh_enc', '<', $dt_fim)
            ->where('id_reuniao', $id)
            ->select('tr.id', 'p.nome_completo', 'tst.nome as status', 'dc.data', 'gr.nome as grupo', 'pc.presenca',)
            ->orderBy('p.nome_completo')
            ->get();

        $presencasMembros = DB::table('membro as m')
            ->leftJoin('associado as ass', 'm.id_associado', 'ass.id')
            ->leftJoin('pessoas as p', 'ass.id_pessoa', 'p.id')
            ->leftJoin('presenca_membros as pm', 'm.id', 'pm.id_membro')
            ->leftJoin('dias_cronograma as dc', 'pm.id_dias_cronograma', 'dc.id')
            ->where('m.dt_inicio', '>=', $dt_inicio)
            ->where(function ($query) use ($dt_fim) {
                $query->where('m.dt_fim', '<', $dt_fim);
                $query->orWhere('m.dt_fim', NULL);
            })
            ->where('m.id_cronograma', $id)
            ->select('m.id', 'p.nome_completo', 'dc.data', 'pm.presenca')
            ->get();

        $presencasMembrosArray = array();
        foreach ($presencasMembros as $element) {
            $presencasMembrosArray["$element->nome_completo"][] = $element;
        }

        $presencasCountAssistidos = $presencasCountAssistidos->get();
        $presencasCountAssistidos = json_decode(json_encode($presencasCountAssistidos));

        $acompanhantes = $acompanhantes->sum('nr_acompanhantes');


        $presencasCountMembros = $presencasCountMembros->get();
        $presencasCountMembros = json_decode(json_encode($presencasCountMembros));

        $presencasAssistidosArray = array();
        foreach ($presencasAssistidos as $element) {
            $presencasAssistidosArray["$element->nome_completo - $element->status"][] = $element;
        }


        if ($presencasCountAssistidos == []) {
            $presencasCountAssistidos[0] = 0;
            $presencasCountAssistidos[1] = 0;
        } elseif (!in_array(false, array_values(array_column($presencasCountAssistidos, 'presenca')))) {
            $presencasCountAssistidos[1] = $presencasCountAssistidos[0]->total;
            $presencasCountAssistidos[0] = 0;
        } elseif (!in_array(true, array_values(array_column($presencasCountAssistidos, 'presenca')))) {
            $presencasCountAssistidos[0] = $presencasCountAssistidos[0]->total;
            $presencasCountAssistidos[1] = 0;
        } else {
            $presencasCountAssistidos[0] = $presencasCountAssistidos[0]->total;
            $presencasCountAssistidos[1] = $presencasCountAssistidos[1]->total;
        }
        $presencasCountAssistidos[2] =  $acompanhantes;

        if ($presencasCountMembros == []) {
            $presencasCountMembros[0] = 0;
            $presencasCountMembros[1] = 0;
        } elseif (!in_array(false, array_values(array_column($presencasCountMembros, 'presenca')))) {
            $presencasCountMembros[1] = $presencasCountMembros[0]->total;
            $presencasCountMembros[0] = 0;
        } elseif (!in_array(true, array_values(array_column($presencasCountMembros, 'presenca')))) {
            $presencasCountMembros[0] = $presencasCountMembros[0]->total;
            $presencasCountMembros[1] = 0;
        } else {
            $presencasCountMembros[0] = $presencasCountMembros[0]->total;
            $presencasCountMembros[1] = $presencasCountMembros[1]->total;
        }
        $presencasCountMembros[2] = 0;

        return view('relatorios.visualizar-assistido-reuniao', compact('id', 'presencasAssistidosArray','presencasMembrosArray', 'presencasCountAssistidos', 'presencasCountMembros', 'dt_inicio', 'dt_fim', 'grupo'));
    }

public function teste(){
    
    $setor = DB::table('setor as st')
    ->leftJoin('setor as stf', 'st.id', 'stf.setor_pai')
    ->leftJoin('setor as stn', 'stf.id', 'stn.setor_pai')
    ->select('st.id as ids', 'stf.id as idf', 'stn.id as idn')
    ->whereIn('st.id', session()->get('usuario.setor'))
    ->get();

    Barryvdh
    
    $setor = json_decode(json_encode($setor), true);
    $setor = (array_unique(array_merge(array_column($setor, 'ids'), array_column($setor, 'idf'), array_column($setor, 'idn'))));
}
}
