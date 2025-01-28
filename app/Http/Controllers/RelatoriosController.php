<?php

namespace App\Http\Controllers;

use Carbon\CarbonPeriod;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\App;

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
        foreach ($nomes_temas as $tema) {
            $tematicasArray[$tema] = in_array($tema, array_column($tematicas, 'nm_tca')) ? $tematicas[array_search($tema, array_column($tematicas, 'nm_tca'))]['total'] : 0;
        }


        return view('relatorios.tematicas', compact('tematicasArray', 'dt_inicio', 'dt_fim', 'tematicas'));
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

        $setoresPesquisa = DB::table('setor')->get();

        $requestSala = $request->sala;
        if ($requestSala) {
            $cronogramas = $cronogramas->where('cro.id_sala', $requestSala);
        }
        $requestGrupo = $request->grupo;
        if ($request->grupo) {
            $cronogramas = $cronogramas->where('cro.id_grupo', $requestGrupo);
        }
        $requestSetor = $request->setor;
        if ($request->setor) {
            $cronogramas = $cronogramas->where('gr.id_setor', $requestSetor);
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
        return view('relatorios.relatorio-salas-cronograma', compact('requestSetor', 'setoresPesquisa', 'eventosCronogramas', 'salas', 'cronogramasPesquisa', 'requestSala', 'requestGrupo'));
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
        $setoresAutorizado = array();
        foreach (session()->get('acessoInterno') as $perfil) {

            $setoresAutorizado = array_merge($setoresAutorizado, array_column($perfil, 'id_setor'));
        }

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

    public function indexSetor(Request $request)
    {
        // Obter os parâmetros de busca
        $setorId = $request->input('setor');
        $grupoId = $request->input('grupo');
        $diaId = $request->input('dia');
        $nomeId = $request->input('nome');
        $funcaoId = $request->input('funcao');

        // Definir o número de itens por página
        $itemsPerPage = 50;
        $itemsPerPage = 50;
        $setoresAutorizado = array();
        foreach (session()->get('acessoInterno') as $perfil) {

            $setoresAutorizado = array_merge($setoresAutorizado, array_column($perfil, 'id_setor'));
        }

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
            ->orderBy('st.nome')
            ->orderBy('td.id')
            ->orderBy('gr.nome')
            ->orderBy('tf.id')
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
        $membros = $membrosQuery->paginate(50)
            ->appends([
                'setor' => $setorId,
                'grupo' => $grupoId,
                'dia' => $diaId,
                'funcao' => $funcaoId,
                'nome' => $nomeId,
            ]);

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



        //      dd($membros, $result);



        return view('relatorios.gerenciar-relatorio-setor-pessoas', compact('membros', 'grupo', 'setor', 'dias', 'atendentesParaSelect', 'funcao'));
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

            ->distinct('gr.nome');

        $reunioesPesquisa = DB::table('cronograma as cr')
            ->select(
                'cr.id',
                'cr.h_fim',
                'st.sigla',
                't.sigla as SiglaTratamento',
                'cr.modificador',
                'ts.descricao',
                'gr.nome',
                'cr.dia_semana as dia',
                'cr.h_inicio',
                'cr.h_fim',
                DB::raw("(CASE WHEN cr.data_fim IS NOT NULL THEN 'Inativo' ELSE 'Ativo' END) AS status") // Correção aqui
            )
            ->leftJoin('tipo_tratamento as t', 'cr.id_tipo_tratamento', 't.id')
            ->leftJoin('grupo as gr', 'cr.id_grupo', 'gr.id')
            ->leftJoin('setor as st', 'gr.id_setor', 'st.id')
            ->leftJoin('tipo_dia as d', 'cr.dia_semana', 'd.id')
            ->leftJoin('tipo_status_grupo AS ts', 'gr.status_grupo', 'ts.id')
            ->groupBy(
                'cr.id',
                'gr.nome',
                'd.nome',
                'cr.h_inicio',
                'cr.h_fim',
                'st.sigla',
                't.sigla',
                'ts.descricao'
            )
            ->orderBy('gr.nome', 'asc');

        $presencasCountAssistidos = DB::table('presenca_cronograma as pc')
            ->leftJoin('dias_cronograma as dc', 'pc.id_dias_cronograma', 'dc.id')
            ->where('dc.data', '>=', $dt_inicio)
            ->where('dc.data', '<', $dt_fim)
            ->groupBy('presenca')
            ->select('presenca', DB::raw("count(*) as total"));

        $acompanhantes = DB::table('dias_cronograma as dc')->leftJoin('cronograma as cr', 'dc.id_cronograma', 'cr.id')->whereNot('id_tipo_tratamento', 3);

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
            ->where('enc.dh_enc', '<=', $dt_fim)
            ->where('id_reuniao', $id)
            ->select('tr.id', 'p.nome_completo', 'tst.nome as status', 'dc.data', 'gr.nome as grupo', 'pc.presenca',)
            ->orderBy('p.nome_completo')
            ->get();

        $presencasMembros = DB::table('membro as m')
            ->leftJoin('associado as ass', 'm.id_associado', 'ass.id')
            ->leftJoin('pessoas as p', 'ass.id_pessoa', 'p.id')
            ->leftJoin('presenca_membros as pm', 'm.id', 'pm.id_membro')
            ->leftJoin('dias_cronograma as dc', 'pm.id_dias_cronograma', 'dc.id')
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

        return view('relatorios.visualizar-assistido-reuniao', compact('id', 'presencasAssistidosArray', 'presencasMembrosArray', 'presencasCountAssistidos', 'presencasCountMembros', 'dt_inicio', 'dt_fim', 'grupo'));
    }
    public function vagasGrupos(Request $request)
    {
        $now = Carbon::now()->format('Y-m-d');
        // Iniciar a consulta
        $grupos = DB::table('cronograma as cro')
            ->leftJoin('tipo_tratamento as t', 'cro.id_tipo_tratamento', 't.id')
            ->leftJoin('grupo as gr', 'cro.id_grupo', 'gr.id')
            ->leftJoin('tipo_dia as td', 'cro.dia_semana', 'td.id')
            ->leftJoin('setor as st', 'gr.id_setor', 'st.id')
            ->where(function ($query) use ($now) {
                $query->where('cro.data_fim', '>', $now);
                $query->orWhere('cro.data_fim', null);
            })
            ->select(
                DB::raw('
                (select count(*) from tratamento tr where tr.id_reuniao = cro.id and tr.status < 3) as trat'),
                't.id',
                't.descricao',
                'cro.id',
                'gr.nome as nome',
                'td.nome as dia',
                'cro.h_inicio',
                'cro.h_fim',
                'st.sigla as setor',
                'st.id as id_setor',
                'cro.max_atend'
            )
            ->orderBy('gr.nome');
        // Consultar setores para o filtro
        $setores = DB::table('setor')
            ->orderBy('nome');


        // Consultar grupos
        $grupo2 = DB::table('cronograma as cro')
            ->leftJoin('tipo_tratamento as t', 'cro.id_tipo_tratamento', 't.id')
            ->leftJoin('grupo as gr', 'cro.id_grupo', 'gr.id')
            ->leftJoin('tipo_dia as td', 'cro.dia_semana', 'td.id')
            ->leftJoin('setor as st', 'gr.id_setor', 'st.id')
            ->where(function ($query) use ($now) {
                $query->where('cro.data_fim', '>', $now);
                $query->orWhere('cro.data_fim', null);
            })
            ->select('t.id', 't.descricao', 'cro.id', 'gr.nome', 'cro.h_inicio', 'cro.h_fim', 'st.sigla as setor', 'td.nome as dia_semana');



        // Consultar tratamentos
        $tratamento = DB::table('tipo_tratamento')->get();

        // Filtros
        if ($request->grupo != null) {
            $grupos = $grupos->where('cro.id', $request->grupo);
        }

        if ($request->setor) {
            $grupos = $grupos->where('gr.id_setor', $request->setor);
        }

        if ($request->tratamento) {
            $grupos = $grupos->where('t.id', $request->tratamento);
            $grupo2 = $grupo2->where('t.id', $request->tratamento);
            $setores->where('id', $grupos->pluck('id_setor')->toArray());
        }
        $grupo2 = $grupo2->get();
        $setores = $setores->get();
        // Paginação dos grupos
        $grupos = $grupos->paginate(30)->appends([
            'grupo' => $request->grupo,
            'setor' => $request->setor,
            'tratamento' => $request->tratamento,
        ]);

        // Calcular a quantidade de vagas por tratamento (total)
        $quantidade_vagas_tipo_tratamento = 0;
        $tipo_de_tratamento = null;
        if ($request->tratamento) {
            // Somando as vagas de cada grupo conforme o tratamento selecionado
            foreach ($grupos as $grupo) {
                $quantidade_vagas_tipo_tratamento += $grupo->max_atend - $grupo->trat;
            }
            $tipo_de_tratamento = DB::table('tipo_tratamento')->where('id', '=', $request->input('tratamento'))->first();
            // dd($tipo_de_tratamento);
        }


        // Retornar a view com os dados
        return view('relatorios.vagas-grupos', compact('setores', 'grupos', 'grupo2', 'tratamento', 'quantidade_vagas_tipo_tratamento', 'tipo_de_tratamento'));
    }

    public function AtendimentosRel(Request $request)
    {

        $now = Carbon::now()->format('Y-m-d');
        $dt_inicio = $request->dt_inicio == null ? (Carbon::now()->subMonth()->firstOfMonth()->format('Y-m-d')) : $request->dt_inicio;
        $dt_fim =  $request->dt_fim == null ? Carbon::today()->format('Y-m-d') : $request->dt_fim;
        // Iniciar a consulta
        $grupos = DB::table('cronograma as cro')
            ->leftJoin('tratamento as tr', 'cro.id', 'tr.id')
            ->leftJoin('tipo_tratamento as t', 'cro.id_tipo_tratamento', 't.id')
            ->leftJoin('grupo as gr', 'cro.id_grupo', 'gr.id')
            ->leftJoin('tipo_dia as td', 'cro.dia_semana', 'td.id')
            ->leftJoin('setor as st', 'gr.id_setor', 'st.id')
            ->whereIn('t.id', [1, 2, 3, 4, 6])
            ->whereIn('tr.status', [2, 3, 4])
            ->select(
                'cro.id',
                'cro.h_inicio',
                'cro.h_fim',
                't.descricao',
                't.id as id_tp_tratamento',
                'tr.status',
                'cro.max_atend',
                'gr.nome as nome',
                'td.nome as dia',
                't.sigla',
                'st.sigla as setor',
                'st.id as id_setor',
            )
            ->orderBy('gr.nome');


        // Consultar setores para o filtro
        $setores = DB::table('setor')
            ->whereIn('id', [48, 50, 46, 72])
            ->orderBy('nome');

        // Consultar grupos
        $grupo2 =  DB::table('cronograma as cro')
            ->leftJoin('tratamento as tr', 'cro.id', 'tr.id')
            ->leftJoin('tipo_tratamento as t', 'cro.id_tipo_tratamento', 't.id')
            ->leftJoin('grupo as gr', 'cro.id_grupo', 'gr.id')
            ->leftJoin('tipo_dia as td', 'cro.dia_semana', 'td.id')
            ->leftJoin('setor as st', 'gr.id_setor', 'st.id')
            ->whereIn('t.id', [1, 2, 3, 4, 6])
            ->whereIn('tr.status', [2, 3, 4])
            ->select(
                'cro.id',
                'cro.h_inicio',
                'cro.h_fim',
                'gr.nome as nome',
                'td.nome as dia_semana',
                't.sigla',
                'st.sigla as setor',
                'st.id as id_setor',
            )
            ->orderBy('gr.nome');


        // Consultar tratamentos
        $tratamento = DB::table('tipo_tratamento')->whereIn('id', [1, 2, 3, 4, 6])->get();

        // Filtros
        if ($request->setor) {
            $grupos = $grupos->where('gr.id_setor', $request->setor);
        }

        if ($request->tratamento) {
            $grupos = $grupos->where('t.id', $request->tratamento);
            $grupo2 = $grupo2->where('t.id', $request->tratamento);
            $setores->where('id', $grupos->pluck('id_setor')->toArray());
        }
        $grupos = $grupos->get()->toArray();
        $grupo2 = $grupo2->get();
        $setores = $setores->get();

        // Insere os atendimentos
        foreach ($grupos as $key => $grupo) {

            $tratamentosAtivos = DB::table('tratamento as tra')
                ->leftJoin('encaminhamento as enc', 'tra.id', 'enc.id')
                ->leftJoin('atendimentos as at', 'enc.id_atendimento', 'at.id')
                ->where('id_reuniao', $grupo->id)
                ->where(function ($query) use ($dt_inicio, $dt_fim) {

                    // Data Inicio Tratamento
                    $query->where(function ($subQuery) use ($dt_inicio, $dt_fim) {
                        $subQuery->where(function ($innerQuery) use ($dt_inicio, $dt_fim) {
                            $innerQuery->where('tra.dt_inicio', '>', $dt_inicio)->where('tra.dt_inicio', '<', $dt_fim);
                        });
                        $subQuery->orWhere('tra.dt_inicio', '<', $dt_inicio);
                    });

                    // Data Fim Tratamento
                    $query->where(function ($subQuery) use ($dt_inicio, $dt_fim) {
                        $subQuery->where(function ($innerQuery) use ($dt_inicio, $dt_fim) {
                            $innerQuery->where('tra.dt_fim', '>', $dt_inicio)->where('tra.dt_inicio', '<', $dt_fim);
                        });
                        $subQuery->orWhere('tra.dt_fim', '>', $dt_fim);
                        $subQuery->orWhere('tra.dt_fim', NULL);
                    });
                })
                ->count();

            $passes = DB::table('dias_cronograma')
                ->where('id_cronograma', $grupo->id)
                ->where('data', '>=', $dt_inicio)
                ->where('data', '<', $dt_fim)
                ->sum('nr_acompanhantes');

            if ($grupo->id_tp_tratamento == 3) { // Caso seja um grupo de PTH, conta os assistidos
                $grupos[$key]->atendimentos =  $passes;
            } else {
                $grupos[$key]->atendimentos =  $tratamentosAtivos;
                $grupos[$key]->acompanhantes =  $passes;
            }
        }



        // Pesquisa de grupos
        if ($request->grupo != null) {

            $buffer = array();
            foreach ($grupos as $grupo) {
                if (in_array($grupo->id, $request->grupo)) {
                    $buffer[$grupo->id]['descricao'] = $grupo->descricao;
                    $buffer[$grupo->id]['nome'] = $grupo->nome;
                    $buffer[$grupo->id]['sigla'] =  $grupo->sigla;
                    $buffer[$grupo->id]['atendimentos'] = $grupo->atendimentos;
                    $buffer[$grupo->id]['dia_semana'] = $grupo->dia;
                    $buffer[$grupo->id]['h_inicio'] = $grupo->h_inicio;
                    $buffer[$grupo->id]['h_fim'] = $grupo->h_fim;
                    $buffer[$grupo->id]['id_tp_tratamento'] = $grupo->id_tp_tratamento;

                    isset($grupo->acompanhantes) ?  $buffer[$grupo->id]['acompanhantes'] = $grupo->acompanhantes : null;
                }
            }
            $grupos = $buffer;
        } else {

            $buffer = array();
            foreach ($grupos as $grupo) {
                $buffer[$grupo->id_tp_tratamento]['descricao'] = $grupo->descricao;
                $buffer[$grupo->id_tp_tratamento]['sigla'] =  $grupo->sigla;
                $buffer[$grupo->id_tp_tratamento]['id'] =  $grupo->id;

                array_key_exists("atendimentos", $buffer[$grupo->id_tp_tratamento]) ?
                    $buffer[$grupo->id_tp_tratamento]['atendimentos'] += $grupo->atendimentos :
                    $buffer[$grupo->id_tp_tratamento]['atendimentos'] = $grupo->atendimentos;

                if (isset($grupo->acompanhantes)) {
                    array_key_exists("acompanhantes", $buffer[$grupo->id_tp_tratamento]) ?
                        $buffer[$grupo->id_tp_tratamento]['acompanhantes'] += $grupo->acompanhantes :
                        $buffer[$grupo->id_tp_tratamento]['acompanhantes'] = $grupo->acompanhantes;
                }


                $grupos = $buffer;
            }

            // Retornar a view com os dados
        }
        return view('relatorios.gerenciar-relatorio-tratamento', compact('setores', 'grupos', 'grupo2', 'tratamento', 'dt_inicio', 'dt_fim'));
    }

    public function Atendimentos(Request $request)
    {
        $now = Carbon::now()->format('Y-m-d');
        $dt_inicio = $request->dt_inicio == null ? Carbon::now()->subMonth()->firstOfMonth()->format('Y-m-d') : $request->dt_inicio;
        $dt_fim = $request->dt_fim == null ? Carbon::today()->format('Y-m-d') : $request->dt_fim;



        $atendimentos = DB::table('atendimentos as at')
            ->leftJoin('pessoas as p', 'at.id_assistido', 'p.id')
            ->where('at.dh_chegada', '>=', $dt_inicio)
            ->where('at.dh_chegada', '<', $dt_fim);


        if ($request->tipo_visualizacao == 2) {
            Carbon::setlocale(config('app.locale'));
            $meses = CarbonPeriod::create($dt_inicio, $dt_fim)->month()->toArray();

            foreach ($meses as $mes) {

                if ($request->status_atendimento == 1) {
                    $nomeStatus = DB::table('tipo_status_atendimento')->where('id', $request->status_atendimento)->first();
                    $dadosChart[ucfirst($mes->locale('pt-br')->translatedFormat('F'))] = [
                        'Finalizados' => (clone $atendimentos)->where('at.status_atendimento', 6)->whereMonth('dh_chegada', $mes->month)->whereYear('dh_chegada', $mes->year)->count(),
                        'Cancelados' => (clone $atendimentos)->where('at.status_atendimento', 7)->whereMonth('dh_chegada', $mes->month)->whereYear('dh_chegada', $mes->year)->count(),
                        'Menores 18' => (clone $atendimentos)->where('at.menor_auto', true)->whereMonth('dh_chegada', $mes->month)->whereYear('dh_chegada', $mes->year)->count(),
                    ];
                } else if ($request->status_atendimento == 2) {
                    $dadosChart[ucfirst($mes->locale('pt-br')->translatedFormat('F'))] = [
                        'Homens' => (clone $atendimentos)->where('p.sexo', 1)->whereMonth('dh_chegada', $mes->month)->whereYear('dh_chegada', $mes->year)->count(),
                        'Mulheres' => (clone $atendimentos)->where('p.sexo', 2)->whereMonth('dh_chegada', $mes->month)->whereYear('dh_chegada', $mes->year)->count(),
                    ];
                } else if ($request->status_atendimento == 3) {
                    $dadosChart[ucfirst($mes->locale('pt-br')->translatedFormat('F'))] = [
                        'Domingo' => (clone $atendimentos)->whereRaw('EXTRACT(DOW FROM dh_chegada) = 0')->whereMonth('dh_chegada', $mes->month)->whereYear('dh_chegada', $mes->year)->count(),
                        'Segunda' => (clone $atendimentos)->whereRaw('EXTRACT(DOW FROM dh_chegada) = 1')->whereMonth('dh_chegada', $mes->month)->whereYear('dh_chegada', $mes->year)->count(),
                        'Terça' => (clone $atendimentos)->whereRaw('EXTRACT(DOW FROM dh_chegada) = 2')->whereMonth('dh_chegada', $mes->month)->whereYear('dh_chegada', $mes->year)->count(),
                        'Quarta' => (clone $atendimentos)->whereRaw('EXTRACT(DOW FROM dh_chegada) = 3')->whereMonth('dh_chegada', $mes->month)->whereYear('dh_chegada', $mes->year)->count(),
                        'Quinta' => (clone $atendimentos)->whereRaw('EXTRACT(DOW FROM dh_chegada) = 4')->whereMonth('dh_chegada', $mes->month)->whereYear('dh_chegada', $mes->year)->count(),
                        'Sexta' => (clone $atendimentos)->whereRaw('EXTRACT(DOW FROM dh_chegada) = 5')->whereMonth('dh_chegada', $mes->month)->whereYear('dh_chegada', $mes->year)->count(),
                        'Sábado' => (clone $atendimentos)->whereRaw('EXTRACT(DOW FROM dh_chegada) = 6')->whereMonth('dh_chegada', $mes->month)->whereYear('dh_chegada', $mes->year)->count(),

                    ];
                } else if ($request->status_atendimento == 4) {
                    $dadosChart[ucfirst($mes->locale('pt-br')->translatedFormat('F'))] = [
                        'Manhã' => (clone $atendimentos)->whereTime('dh_chegada', '>', '08:30:00')->whereTime('dh_chegada', '<', '10:30:00')->whereMonth('dh_chegada', $mes->month)->whereYear('dh_chegada', $mes->year)->count(),
                        'Tarde' => (clone $atendimentos)->whereTime('dh_chegada', '>', '15:30:00')->whereTime('dh_chegada', '<', '17:30:00')->whereMonth('dh_chegada', $mes->month)->whereYear('dh_chegada', $mes->year)->count(),
                        'Noite' => (clone $atendimentos)->whereTime('dh_chegada', '>', '17:30:00')->whereTime('dh_chegada', '<', '21:00:00')->whereMonth('dh_chegada', $mes->month)->whereYear('dh_chegada', $mes->year)->count(),

                    ];
                } else {
                    $dadosChart[ucfirst($mes->locale('pt-br')->translatedFormat('F'))] = [
                        'Finalizados' => (clone $atendimentos)->where('at.status_atendimento', 6)->whereMonth('dh_chegada', $mes->month)->whereYear('dh_chegada', $mes->year)->count(),
                        'Cancelados' => (clone $atendimentos)->where('at.status_atendimento', 7)->whereMonth('dh_chegada', $mes->month)->whereYear('dh_chegada', $mes->year)->count(),
                        'Menores 18' => (clone $atendimentos)->where('at.menor_auto', true)->whereMonth('dh_chegada', $mes->month)->whereYear('dh_chegada', $mes->year)->count(),
                    ];
                }
            }
        } else {

            if ($request->status_atendimento == 1) {
                $nomeStatus = DB::table('tipo_status_atendimento')->where('id', $request->status_atendimento)->first();
                $dadosChart = [
                    'Finalizados' => (clone $atendimentos)->where('at.status_atendimento', 6)->count(),
                    'Cancelados' => (clone $atendimentos)->where('at.status_atendimento', 7)->count(),
                    'Menores 18' => (clone $atendimentos)->where('at.menor_auto', true)->count(),
                ];
            } else if ($request->status_atendimento == 2) {
                $dadosChart = [
                    'Homens' => (clone $atendimentos)->where('p.sexo', 1)->count(),
                    'Mulheres' => (clone $atendimentos)->where('p.sexo', 2)->count(),
                ];
            } else if ($request->status_atendimento == 3) {
                $dadosChart = [
                    'Domingo' => (clone $atendimentos)->whereRaw('EXTRACT(DOW FROM dh_chegada) = 0')->count(),
                    'Segunda' => (clone $atendimentos)->whereRaw('EXTRACT(DOW FROM dh_chegada) = 1')->count(),
                    'Terça' => (clone $atendimentos)->whereRaw('EXTRACT(DOW FROM dh_chegada) = 2')->count(),
                    'Quarta' => (clone $atendimentos)->whereRaw('EXTRACT(DOW FROM dh_chegada) = 3')->count(),
                    'Quinta' => (clone $atendimentos)->whereRaw('EXTRACT(DOW FROM dh_chegada) = 4')->count(),
                    'Sexta' => (clone $atendimentos)->whereRaw('EXTRACT(DOW FROM dh_chegada) = 5')->count(),
                    'Sábado' => (clone $atendimentos)->whereRaw('EXTRACT(DOW FROM dh_chegada) = 6')->count(),

                ];
            } else if ($request->status_atendimento == 4) {
                $dadosChart = [
                    'Manhã' => (clone $atendimentos)->whereTime('dh_chegada', '>', '08:30:00')->whereTime('dh_chegada', '<', '10:30:00')->count(),
                    'Tarde' => (clone $atendimentos)->whereTime('dh_chegada', '>', '15:30:00')->whereTime('dh_chegada', '<', '17:30:00')->count(),
                    'Noite' => (clone $atendimentos)->whereTime('dh_chegada', '>', '17:30:00')->whereTime('dh_chegada', '<', '21:00:00')->count(),

                ];
            } else {
                $dadosChart = [
                    'Finalizados' => (clone $atendimentos)->where('at.status_atendimento', 6)->count(),
                    'Cancelados' => (clone $atendimentos)->where('at.status_atendimento', 7)->count(),
                    'Menores 18' => (clone $atendimentos)->where('at.menor_auto', true)->count(),
                ];
            }
        }
      //  dd($dadosChart);
        return view('relatorios.gerenciar-relatorio-atendimento', compact('dt_inicio', 'dt_fim', 'dadosChart'));
    }
}

    // public function teste()
    // {
    //     $pdf = \PDF::loadView('relatorios.teste');
    //     return $pdf->download('invoice.pdf');
    // }
