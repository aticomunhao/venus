<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;


class GerenciarEncaminhamentoController extends Controller
{
    public function index(Request $request)
    {
        // Lista de Dados que aparece na view
        $lista = DB::table('encaminhamento AS enc')
            ->select('enc.id AS ide', 'enc.id_tipo_encaminhamento', 'dh_enc', 'enc.id_atendimento', 'enc.status_encaminhamento', 'tse.descricao AS tsenc', 'enc.id_tipo_tratamento AS idtt', 'id_tipo_entrevista', 'at.id AS ida', 'at.id_assistido', 'p1.nome_completo AS nm_1', 'at.id_representante as idr', 'p2.nome_completo as nm_2', 'p1.cpf AS cpf_assistido', 'pr.id AS prid', DB::raw("(CASE WHEN at.emergencia = true THEN 'Emergência' ELSE 'Normal' END) as prdesc"), 'pr.sigla AS prsigla', 'tt.descricao AS desctrat', 'tt.sigla')
            ->leftJoin('atendimentos AS at', 'enc.id_atendimento', 'at.id')
            ->leftjoin('pessoas AS p1', 'at.id_assistido', 'p1.id')
            ->leftjoin('pessoas AS p2', 'at.id_representante', 'p2.id')
            ->leftjoin('pessoas AS p3', 'at.id_atendente_pref', 'p3.id')
            ->leftjoin('pessoas AS p4', 'at.id_atendente', 'p4.id')
            ->leftJoin('tipo_prioridade AS pr', 'at.id_prioridade', 'pr.id')
            ->leftJoin('tipo_status_encaminhamento AS tse', 'enc.status_encaminhamento', 'tse.id')
            ->leftJoin('tipo_tratamento AS tt', 'enc.id_tipo_tratamento', 'tt.id')
            ->where('enc.id_tipo_encaminhamento', 2); // Tipo de encaminhamento Tratamento

        $tratamentos_permitidos = array(); // Armazenas os tipos de tratamento permitidos pela sessão
        in_array(16, session()->get('usuario.acesso')) ? array_push($tratamentos_permitidos, 1) : 0; // Se tiver permissão PTD, adiciona o ID de PTD
        in_array(22, session()->get('usuario.acesso')) ? array_push($tratamentos_permitidos, 2) : 0; // Se tiver permissão PTI, adiciona o ID de PTI
        in_array(23, session()->get('usuario.acesso')) ? array_push($tratamentos_permitidos, 6) : 0; // Se tiver permissão Integral, adiciona o ID de Integral

        $lista = $lista->whereIn('enc.id_tipo_tratamento', $tratamentos_permitidos); // Busca apenas os IDs Permitidos na tela


        $data_enc = $request->dt_enc; // Armazena a pesquisa de Data de Encaminhamento
        $assistido = $request->assist; // Armazena a pesquisa de Nome de Assistido
        $cpf = $request->cpf; // Armazena a pesquisa de CPF
        $situacao = $request->status; // ArX3E-JZJ-VN3Bmazena a pesquisa por Status (Select)

        if ($request->dt_enc) {
            $lista->where('enc.dh_enc', '>=', $request->dt_enc); // Pesquisa qualquer data que seja maior ou igual a pesquisada
        }
        if ($request->assist) {

            $pesquisaNome = array(); // Inicia um array
            $pesquisaNome = explode(' ', $request->assist); // Popula esse array com cada palavra digitada no input
            $margemErro = 0; // Inicializa em 0 uma variável de contagem de erros, usada para validação
            foreach ($pesquisaNome as $itemPesquisa) { // Para cada palavra na pesquisa

                $bufferPessoa = (clone $lista); // Salva o estado anterior antes de pesquisar
                $lista =  $lista->whereRaw("UNACCENT(LOWER(p1.nome_completo)) ILIKE UNACCENT(LOWER(?))", ["%$itemPesquisa%"]); // Pesquisa sem acento e sem case sensitive
                if (count($lista->get()->toArray()) < 1) { // Caso durante o select, o banco não retorne nada
                    $pessoaVazia = (clone $lista); // Guarda em uma variável o que é um estado vazio, para popular a tabela
                    $lista = (clone $bufferPessoa); // Devolve a variável para o estado antes dessa pesquisa
                    $margemErro += 1; // Adiciona 1 na varíavel de contagem para validação

                }
            }
            if ($margemErro == 0) { // Caso não tenha sofrido nenhum erro, passa direto
            } else if ($margemErro < (count($pesquisaNome) / 2)) { // Caso o número de erros seja inferior a 50% dos dados indicados
                app('flasher')->addWarning('Nenhum Item Encontrado. Mostrando Pesquisa Aproximada');
            } else {
                //Transforma a variavel em algo vazio
                $pessoa = $pessoaVazia;
                app('flasher')->addError('Nenhum Item Encontrado!');
            }
        }
        if ($request->cpf) {

            $lista->where('p1.cpf', $request->cpf); // Pesquisa CPF
        }
        if ($request->status) {
            $lista->where('enc.status_encaminhamento', $request->status); // Pesquisa Status, é um select na view
        }
        if ($request->tratamento) {
            $lista->where('enc.id_tipo_tratamento', $request->tratamento); // Pesquisa os tratamentos, é um select na view
        }

        $contar = (clone $lista)->get()->count('enc.id');
        $lista = $lista
            ->orderby('status_encaminhamento', 'ASC') // Status tem prioridade, Primeiro Agendar, por último cancelado
            ->orderby('at.emergencia', 'DESC') // Emergências ao topo
            ->orderBy('enc.id_tipo_tratamento', 'DESC') // Organizados pelo tipo de tratamento
            ->orderBy('at.dh_inicio') // Por ordem de chegada no atendimento
            ->paginate(50) // Paginate com 50 itens por página
            ->appends([
                'assist' => $assistido, // Caso troque de pagina, mantém a pesquisa de Assisitido
                'cpf' => $cpf, // Caso troque de pagina, mantém a pesquisa de CPF
            ]);

        $stat = DB::select("select
        ts.id,
        ts.descricao
        from tipo_status_encaminhamento ts
        ");

        $motivo = DB::select("select
        tm.id,
        tm.tipo
        from tipo_motivo tm
        ");

        return view('/recepcao-integrada/gerenciar-encaminhamentos', compact('cpf', 'lista', 'stat', 'contar', 'data_enc', 'assistido', 'situacao', 'motivo'));
    }

    // Função retornada ao cliclar em Agendar, mostra os cards com os dias e grupos
    public function agenda($ide, $idtt)
    {
        //    try {
        $hoje = Carbon::now()->format('Y-m-d'); // Retorna o dia de Hoje no formato de banco de dados
        // Traz todos os dados da pessoa que foi encaminhada, usado na view para visualizar apenas
        $result = DB::table('encaminhamento AS enc')
            ->select('enc.id AS ide', 'enc.id_tipo_encaminhamento', 'dh_enc', 'enc.id_atendimento', 'enc.status_encaminhamento', 'tse.descricao AS tsenc', 'enc.id_tipo_tratamento', 'id_tipo_entrevista', 'at.id AS ida', 'at.id_assistido', 'p1.nome_completo AS nm_1', 'at.id_representante as idr', 'p2.nome_completo as nm_2', 'pa.id AS pid', 'pa.nome', 'pr.id AS prid', 'pr.descricao AS prdesc', 'pr.sigla AS prsigla', 'tt.descricao AS desctrat')
            ->leftJoin('atendimentos AS at', 'enc.id_atendimento', 'at.id')
            ->leftjoin('pessoas AS p1', 'at.id_assistido', 'p1.id')
            ->leftjoin('pessoas AS p2', 'at.id_representante', 'p2.id')
            ->leftjoin('pessoas AS p3', 'at.id_atendente_pref', 'p3.id')
            ->leftjoin('pessoas AS p4', 'at.id_atendente', 'p4.id')
            ->leftJoin('tp_parentesco AS pa', 'at.parentesco', 'pa.id')
            ->leftJoin('tipo_prioridade AS pr', 'at.id_prioridade', 'pr.id')
            ->leftJoin('tipo_status_encaminhamento AS tse', 'enc.status_encaminhamento', 'tse.id')
            ->leftJoin('tipo_tratamento AS tt', 'enc.id_tipo_tratamento', 'tt.id')
            ->where('enc.id', $ide)
            ->get();

        // Traz todos os dados de todos os dias de tratamento
        $dadosDias = DB::table('cronograma AS reu')
            ->select(DB::raw('count(*) as numeroCronograma, sum(max_atend) as maximoVagas'), 'reu.dia_semana as dia', 'td.nome as dia_semana') // Numero de grupos, Numero total de vagas, e dia da semana 
            ->leftJoin('grupo AS gr', 'reu.id_grupo', 'gr.id')
            ->leftJoin('tipo_dia as td', 'reu.dia_semana', 'td.id')
            ->where(function ($query) use ($hoje) {
                $query->whereRaw('reu.data_fim > ?', [$hoje])->orWhereNull('reu.data_fim'); // Traz apenas grupos ativos
            })
            ->where(function ($query) {
                $query->where('reu.modificador', NULL); // Sem modificador nenhum
                $query->orWhere('reu.modificador', '<>', 4); // Grupo que não esteja Em Férias
            })
            ->where('reu.id_tipo_tratamento', $idtt)
            ->groupBy('dia', 'td.nome')
            ->orderBy('dia') // Ordena para que os cards saiam certos
            ->get();

        foreach ($dadosDias  as $key => $dadoDia) {

            // Calcula quantos tratamentos ativos tem naquele dia e conta
            $bufferVagas = DB::table('tratamento AS tr')
                ->leftJoin('cronograma AS reu', 'tr.id_reuniao', 'reu.id')
                ->select(DB::raw("($dadoDia->maximovagas - COUNT(CASE WHEN tr.status < 3 THEN tr.id END)) as trat"))
                ->where('reu.id_tipo_tratamento', $idtt)
                ->where('reu.dia_semana', $dadoDia->dia)
                ->pluck('trat')
                ->toArray();

            $dadosDias[$key]->vagas = current($bufferVagas); // Adiciona o valor do buffer para a variavel principal
        }

        return view('recepcao-integrada.agendar-dia', compact('result', 'dadosDias'));
    }

    public function tratamento(Request $request, $ide) //
    {
        $hoje = Carbon::today(); // Data de Hoje
        $dia = intval($request->dia); // Pega o dia do request
        $ide = intval($ide); // Pega o id do encaminhamento passado por método GET

        // Descobre o tipo de tratamento do encaminhamento atual
        $tp_trat = DB::table('encaminhamento AS enc')
            ->select('enc.id_tipo_tratamento')
            ->leftJoin('tipo_tratamento AS tt', 'enc.id_tipo_tratamento', 'tt.id')
            ->where('enc.id', $ide)->value('enc.id_tipo_tratamento');

        // Retorna todos os dados do encaminhamento, para o header com informaçoes para confirmação visual
        $result = DB::table('encaminhamento AS enc')
            ->select('enc.id AS ide', 'enc.id_tipo_encaminhamento', 'dh_enc', 'enc.id_atendimento', 'enc.status_encaminhamento', 'tse.descricao AS tsenc', 'enc.id_tipo_tratamento', 'id_tipo_entrevista', 'at.id AS ida', 'at.id_assistido', 'p1.nome_completo AS nm_1', 'at.id_representante as idr', 'p2.nome_completo as nm_2', 'pa.id AS pid', 'pa.nome', 'pr.id AS prid', 'pr.descricao AS prdesc', 'pr.sigla AS prsigla', 'tt.descricao AS desctrat')
            ->leftJoin('atendimentos AS at', 'enc.id_atendimento', 'at.id')
            ->leftjoin('pessoas AS p1', 'at.id_assistido', 'p1.id')
            ->leftjoin('pessoas AS p2', 'at.id_representante', 'p2.id')
            ->leftjoin('pessoas AS p3', 'at.id_atendente_pref', 'p3.id')
            ->leftjoin('pessoas AS p4', 'at.id_atendente', 'p4.id')
            ->leftJoin('tp_parentesco AS pa', 'at.parentesco', 'pa.id')
            ->leftJoin('tipo_prioridade AS pr', 'at.id_prioridade', 'pr.id')
            ->leftJoin('tipo_status_encaminhamento AS tse', 'enc.status_encaminhamento', 'tse.id')
            ->leftJoin('tipo_tratamento AS tt', 'enc.id_tipo_tratamento', 'tt.id')
            ->where('enc.id', $ide)
            ->first();

        // Devolve todos os dados dos grupos que podem ser selecionados
        $trata = DB::table('cronograma AS reu')
            ->select(DB::raw('(reu.max_atend - (select count(*) from tratamento tr where tr.id_reuniao = reu.id and tr.status < 3)) as trat'), 'p.nome_completo', 'reu.id AS idr', 'gr.nome AS nomeg', 'reu.dia_semana', 'reu.id_sala', 'reu.id_tipo_tratamento', 'reu.h_inicio', 'td.nome AS nomed', 'reu.h_fim', 'reu.max_atend', 'gr.status_grupo AS idst', 'tsg.descricao AS descst', 'tst.descricao AS tstd', 'sa.numero')
            ->leftJoin('tratamento AS tr', 'reu.id', 'tr.id_reuniao')
            ->leftJoin('tipo_tratamento AS tst', 'reu.id_tipo_tratamento', 'tst.id')
            ->leftJoin('salas AS sa', 'reu.id_sala', 'sa.id')
            ->leftJoin('tipo_dia AS td', 'reu.dia_semana', 'td.id')
            ->leftJoin('grupo AS gr', 'reu.id_grupo', 'gr.id')
            ->leftJoin('tipo_status_grupo AS tsg', 'gr.status_grupo', 'tsg.id')
            ->leftJoin('membro AS me', 'reu.id', 'me.id_cronograma')
            ->leftJoin('associado as ass', 'me.id_associado', 'ass.id')
            ->leftJoin('pessoas as p', 'ass.id_pessoa', 'p.id')
            ->where(function ($query) use ($hoje) {
                $query->whereRaw('reu.data_fim > ?', [$hoje])->orWhereNull('reu.data_fim');
            })
            ->where(function ($query) {
                $query->where('reu.modificador', NULL);
                $query->orWhere('reu.modificador', '<>', 4);
            })
            ->where('me.id_funcao', 1) // Busca apenas dirigentes, gera um bug de duplicação caso um grupo tenha mais de um dirigente
            ->where('reu.dia_semana', $dia)
            ->where('reu.id_tipo_tratamento', $tp_trat)
            ->orWhere('tr.status', null)
            ->where('tr.status', '<', 3)
            ->groupBy('p.nome_completo', 'reu.h_inicio', 'reu.max_atend', 'reu.id', 'gr.nome', 'td.nome', 'gr.status_grupo', 'tsg.descricao', 'tst.descricao', 'sa.numero')
            ->orderBy('h_inicio')
            ->get();

        // Validação de erro para dias sem grupo
        if (sizeof($trata) == 0) {
            app('flasher')->addWarning('Não existem grupos para este dia');
            return redirect()->back();
        }

        return view('recepcao-integrada.agendar-tratamento', compact('result', 'trata'));
    }

    public function tratar(Request $request, $ide)
    {
        try {
            $reu = intval($request->reuniao); // Guarda nuuma varia
            $data_atual = Carbon::now(); // Dia de hoje, com dia e horário
            $dia_atual = $data_atual->weekday(); // ID do dia de hoje (Ex.: 0 => Domingo, 3 => Quarta-Feira)

            // Busca o ID dia da semana do cronograma escolhido
            $dia_semana = DB::table('cronograma AS reu')->where('id', $reu)->value('dia_semana');
            $data_fim_antes = Carbon::today()->weekday($dia_semana)->addWeek(8); // Pega a data de daqui a 8 semanas
            $data_fim_depois = Carbon::today()->weekday($dia_semana)->addWeek(7); // Pega a data de daqui a 7 semanas

            // Conta a quantidade de tratamentos ativos para a reunião escolhida
            $countVagas = DB::table('tratamento')
                ->where('id_reuniao', $reu)
                ->where('status', '<', '3')
                ->count();

            // Traz o cronograma/reunião escolhida, com todos os seus dados
            $maxAtend = DB::table('cronograma')
                ->where('id', $reu)
                ->first();

            // Traz todos os dados do encaminhamento atual    
            $tratID = DB::table('encaminhamento')->where('id', $ide)->first();

            // O encaminhamento não seja PTD (Cujo é permitido exder as vagas) e o número de Vagas seja menor ou igual ao de tratamentos
            if ($tratID->id_tipo_tratamento != 2 and $countVagas >= $maxAtend->max_atend) {
                app('flasher')->addError('Número de vagas insuficientes');
                return redirect()->back();
            }

            // Caso o dia seja superior ao dia de hoje, ou hoje
            if ($dia_atual < $dia_semana) { // TODO Colocar tudo que deve acontecer na marcação

                DB::table('tratamento AS tr')->insert([
                    'id_reuniao' => $reu,
                    'id_encaminhamento' => $ide,
                    'status' => 1,
                    'dt_fim' => $data_fim_depois,
                ]);

                DB::table('encaminhamento AS enc')
                    ->where('enc.id', $ide)
                    ->update([
                        'status_encaminhamento' => 3,
                    ]);
            } elseif ($dia_atual > $dia_semana or $dia_atual == $dia_semana) { // TODO Colocar tudo que deve acontecer na marcação

                DB::table('tratamento AS tr')->insert([
                    'id_reuniao' => $reu,
                    'id_encaminhamento' => $ide,
                    'status' => 1,
                    'dt_fim' => $data_fim_antes,
                ]);

                if ($tratID[0]->status_encaminhamento == 2) {
                    DB::table('encaminhamento AS enc')
                        ->where('enc.id', $ide)
                        ->update([
                            'status_encaminhamento' => 4,
                        ]);
                } else {
                    DB::table('encaminhamento AS enc')
                        ->where('enc.id', $ide)
                        ->update([
                            'status_encaminhamento' => 3,
                        ]);
                }
            } else {
                app('flasher')->addDanger('Ocorreu um erro inesperado!');
            }

            app('flasher')->addSuccess('O tratamento foi agendo com sucesso.');
            return redirect('/gerenciar-encaminhamentos');
        } catch (\Exception $e) {

            $code = $e->getCode();
            return view('tratamento-erro.erro-inesperado', compact('code'));
        }
    }

    public function visualizar($ide)
    {
        try {

            // Devolve o ID pessoa daquele encaminhamento, para buscar outros encaminhamentos, mesmo que não conectados
            $pessoa = DB::table('encaminhamento')
                ->leftJoin('atendimentos', 'encaminhamento.id_atendimento', 'atendimentos.id')
                ->where('encaminhamento.id', $ide)
                ->first('id_assistido');

            // Traz todas as informações da view exceto o header com nome, e o footer com as faltas
            $result = DB::table('encaminhamento AS enc')
                ->select(
                    'enc.id AS ide',
                    'td.nome as nomedia', // Utilizado em Dados Encaminhamento para o Dia do Grupo
                    'tr.dt_inicio',
                    'tse.descricao AS tsenc', // Status do encaminhamento, em String
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
                    'tse.descricao AS tst',
                    'tr.id AS idtr',
                    'gr.nome AS nomeg',
                    'rm.h_inicio AS rm_inicio',
                    'tm.tipo AS tpmotivo'
                )
                ->leftJoin('atendimentos AS at', 'enc.id_atendimento', 'at.id')
                ->leftjoin('pessoas AS p1', 'at.id_assistido', 'p1.id')
                ->leftjoin('pessoas AS p2', 'at.id_representante', 'p2.id') 
                ->leftjoin('associado AS ass', 'at.id_atendente', 'ass.id')
                ->leftjoin('pessoas AS p4', 'ass.id_pessoa', 'p4.id')
                ->leftJoin('tp_parentesco AS pa', 'at.parentesco', 'pa.id')
                ->leftJoin('tipo_prioridade AS pr', 'at.id_prioridade', 'pr.id')
                ->leftJoin('tipo_status_encaminhamento AS tse', 'enc.status_encaminhamento', 'tse.id')
                ->leftJoin('tipo_tratamento AS tt', 'enc.id_tipo_tratamento', 'tt.id')
                ->leftJoin('tp_sexo AS tx', 'p1.sexo', 'tx.id')
                ->leftjoin('tratamento AS tr', 'enc.id', 'tr.id_encaminhamento')
                ->leftjoin('cronograma AS rm', 'tr.id_reuniao', 'rm.id')
                ->leftjoin('grupo AS gr', 'rm.id_grupo', 'gr.id')
                ->leftJoin('tipo_motivo AS tm', 'enc.motivo', 'tm.id')
                ->leftJoin('tipo_dia as td', 'rm.dia_semana', 'td.id')
                ->where('at.id_assistido', $pessoa->id_assistido)
                ->where('enc.id_tipo_encaminhamento', 2)
                ->where('enc.status_encaminhamento', '<', 5)
                ->get();

            $list = DB::table('presenca_cronograma as pc')->select('pc.id as idp', 'dc.data', 'pc.presenca')->leftJoin('dias_cronograma as dc', 'id_dias_cronograma', 'dc.id')->leftJoin('cronograma as cr', 'dc.id_cronograma', 'cr.id')->leftJoin('tratamento as tr', 'pc.id_tratamento', 'tr.id')->where('id_encaminhamento', $ide)->get();

            $faul = DB::table('presenca_cronograma as pc')->leftJoin('dias_cronograma as dc', 'id_dias_cronograma', 'dc.id')->leftJoin('cronograma as cr', 'dc.id_cronograma', 'cr.id')->leftJoin('tratamento as tr', 'pc.id_tratamento', 'tr.id')->where('id_encaminhamento', $ide)->where('pc.presenca', 0)->count();

            return view('recepcao-integrada.historico-encaminhamento', compact('result', 'list', 'faul'));
        } catch (\Exception $e) {
            $code = $e->getCode();
            return view('tratamento-erro.erro-inesperado', compact('code'));
        }
    }
    public function inative(Request $request, $ide)
    {
        try {
            $today = Carbon::today()->format('Y-m-d');

            $inative = DB::table('encaminhamento AS enc')
                ->where('enc.id', $ide)
                ->update([
                    'status_encaminhamento' => 6,
                    'motivo' => $request->input('motivo'),
                ]);

            DB::table('historico_venus')->insert([
                'id_usuario' => session()->get('usuario.id_usuario'),
                'data' => $today,
                'fato' => 13,
                'id_ref' => $ide,
            ]);

            app('flasher')->addSuccess('O encaminhamento foi inativado.');

            return redirect('/gerenciar-encaminhamentos');
        } catch (\Exception $e) {
            $code = $e->getCode();
            return view('tratamento-erro.erro-inesperado', compact('code'));
        }
    }

    public function escolherGrupo($id)
    {


        try {


            $idtt = DB::table('encaminhamento')->where('id', $id)
                ->select('id_tipo_tratamento')->first();

            $idtt = $idtt->id_tipo_tratamento;


            $hoje = Carbon::now()->format('Y-m-d');
            $result = DB::table('encaminhamento AS enc')
                ->select('enc.id AS ide', 'enc.id_tipo_encaminhamento', 'dh_enc', 'enc.id_atendimento', 'enc.status_encaminhamento', 'tse.descricao AS tsenc', 'enc.id_tipo_tratamento', 'id_tipo_entrevista', 'at.id AS ida', 'at.id_assistido', 'p1.nome_completo AS nm_1', 'at.id_representante as idr', 'p2.nome_completo as nm_2', 'pa.id AS pid', 'pa.nome', 'pr.id AS prid', 'pr.descricao AS prdesc', 'pr.sigla AS prsigla', 'tt.descricao AS desctrat')
                ->leftJoin('atendimentos AS at', 'enc.id_atendimento', 'at.id')
                ->leftjoin('pessoas AS p1', 'at.id_assistido', 'p1.id')
                ->leftjoin('pessoas AS p2', 'at.id_representante', 'p2.id')
                ->leftjoin('pessoas AS p3', 'at.id_atendente_pref', 'p3.id')
                ->leftjoin('pessoas AS p4', 'at.id_atendente', 'p4.id')
                ->leftJoin('tp_parentesco AS pa', 'at.parentesco', 'pa.id')
                ->leftJoin('tipo_prioridade AS pr', 'at.id_prioridade', 'pr.id')
                ->leftJoin('tipo_status_encaminhamento AS tse', 'enc.status_encaminhamento', 'tse.id')
                ->leftJoin('tipo_tratamento AS tt', 'enc.id_tipo_tratamento', 'tt.id')
                ->where('enc.id', $id)
                ->get();

            $contgrseg = DB::table('cronograma AS reu')
                ->selectRaw('count(*) as ttreu, sum(max_atend) as maxat')
                ->leftJoin('grupo AS gr', 'reu.id_grupo', 'gr.id')
                ->where(function ($query) use ($hoje) {
                    $query->whereRaw('reu.data_fim > ?', [$hoje])->orWhereNull('reu.data_fim');
                })
                ->where(function ($query) {
                    $query->where('reu.modificador', NULL);
                    $query->orWhere('reu.modificador', '<>', 4);
                })
                ->where('reu.id_tipo_tratamento', $idtt)
                ->where('reu.dia_semana', 1)
                ->get();

            $seg = intval($contgrseg[0]->maxat);

            $conttratseg = DB::table('tratamento AS tr')
                ->leftJoin('cronograma AS reu', 'tr.id_reuniao', 'reu.id')
                ->select(DB::raw("($seg - COUNT(CASE WHEN tr.status < 3 THEN tr.id END)) as trat"))
                ->where('reu.id_tipo_tratamento', $idtt)
                ->where('reu.dia_semana', 1)
                ->get();
            //dd($conttratseg, $seg);

            $contgrter = DB::table('cronograma AS reu')
                ->selectRaw('count(*) as ttreu, sum(max_atend) as maxat')
                ->leftJoin('grupo AS gr', 'reu.id_grupo', 'gr.id')
                ->where(function ($query) use ($hoje) {
                    $query->whereRaw('reu.data_fim > ?', [$hoje])->orWhereNull('reu.data_fim');
                })
                ->where(function ($query) {
                    $query->where('reu.modificador', NULL);
                    $query->orWhere('reu.modificador', '<>', 4);
                })
                ->where('reu.id_tipo_tratamento', $idtt)
                ->where('reu.dia_semana', 2)
                ->get();

            $ter = intval($contgrter[0]->maxat);

            $conttratter = DB::table('tratamento AS tr')
                ->leftJoin('cronograma AS reu', 'tr.id_reuniao', 'reu.id')
                ->select(DB::raw("($ter - COUNT(CASE WHEN tr.status < 3 THEN tr.id END)) as trat"))
                ->where('reu.id_tipo_tratamento', $idtt)
                ->where('reu.dia_semana', 2)
                ->get();

            //dd($conttratter);

            $contgrqua = DB::table('cronograma AS reu')
                ->selectRaw('count(*) as ttreu, sum(max_atend) as maxat')
                ->leftJoin('grupo AS gr', 'reu.id_grupo', 'gr.id')
                ->where(function ($query) use ($hoje) {
                    $query->whereRaw('reu.data_fim > ?', [$hoje])->orWhereNull('reu.data_fim');
                })
                ->where(function ($query) {
                    $query->where('reu.modificador', NULL);
                    $query->orWhere('reu.modificador', '<>', 4);
                })
                ->where('reu.id_tipo_tratamento', $idtt)
                ->where('reu.dia_semana', 3)
                ->get();

            $qua = intval($contgrqua[0]->maxat);

            $conttratqua = DB::table('tratamento AS tr')
                ->leftJoin('cronograma AS reu', 'tr.id_reuniao', 'reu.id')
                ->select(DB::raw("($qua - COUNT(CASE WHEN tr.status < 3 THEN tr.id END)) as trat"))
                ->where('reu.id_tipo_tratamento', $idtt)
                ->where('reu.dia_semana', 3)
                ->get();

            //dd($conttratqua);

            $contgrqui = DB::table('cronograma AS reu')
                ->selectRaw('count(*) as ttreu, sum(max_atend) as maxat')
                ->leftJoin('grupo AS gr', 'reu.id_grupo', 'gr.id')
                ->where(function ($query) use ($hoje) {
                    $query->whereRaw('reu.data_fim > ?', [$hoje])->orWhereNull('reu.data_fim');
                })
                ->where(function ($query) {
                    $query->where('reu.modificador', NULL);
                    $query->orWhere('reu.modificador', '<>', 4);
                })
                ->where('reu.id_tipo_tratamento', $idtt)
                ->where('reu.dia_semana', 4)
                ->get();

            $qui = intval($contgrqui[0]->maxat);

            $conttratqui = DB::table('tratamento AS tr')
                ->leftJoin('cronograma AS reu', 'tr.id_reuniao', 'reu.id')
                ->select(DB::raw("($qui - COUNT(CASE WHEN tr.status < 3  THEN tr.id END)) as trat"))
                ->where('reu.id_tipo_tratamento', $idtt)
                ->where('reu.dia_semana', 4)
                ->get();

            $contgrsex = DB::table('cronograma AS reu')
                ->selectRaw('count(*) as ttreu, sum(max_atend) as maxat')
                ->leftJoin('grupo AS gr', 'reu.id_grupo', '=', 'gr.id')
                ->where(function ($query) use ($hoje) {
                    $query->whereRaw('reu.data_fim > ?', [$hoje])->orWhereNull('reu.data_fim');
                })
                ->where(function ($query) {
                    $query->where('reu.modificador', NULL);
                    $query->orWhere('reu.modificador', '<>', 4);
                })
                ->where('reu.id_tipo_tratamento', $idtt)
                ->where('reu.dia_semana', 5)
                ->get();

            $sex = intval($contgrsex[0]->maxat);

            $conttratsex = DB::table('tratamento AS tr')
                ->leftJoin('cronograma AS reu', 'tr.id_reuniao', 'reu.id')
                ->select(DB::raw("($sex - COUNT(CASE WHEN tr.status < 3 THEN tr.id END)) as trat"))
                ->where('reu.id_tipo_tratamento', $idtt)
                ->where('reu.dia_semana', 5)
                ->get();

            //dd($conttratsex);

            $contgrsab = DB::table('cronograma AS reu')
                ->selectRaw('count(*) as ttreu, sum(max_atend) as maxat')
                ->leftJoin('grupo AS gr', 'reu.id_grupo', 'gr.id')
                ->where(function ($query) use ($hoje) {
                    $query->whereRaw('reu.data_fim > ?', [$hoje])->orWhereNull('reu.data_fim');
                })
                ->where(function ($query) {
                    $query->where('reu.modificador', NULL);
                    $query->orWhere('reu.modificador', '<>', 4);
                })
                ->where('reu.id_tipo_tratamento', $idtt)
                ->where('reu.dia_semana', 6)
                ->get();

            $sab = intval($contgrsab[0]->maxat);

            $conttratsab = DB::table('tratamento AS tr')
                ->leftJoin('cronograma AS reu', 'tr.id_reuniao', 'reu.id')
                ->select(DB::raw("($sab - COUNT(CASE WHEN tr.status < 3  THEN tr.id END)) as trat"))
                ->where('reu.id_tipo_tratamento', $idtt)
                ->where('reu.dia_semana', 6)
                ->get();

            $contgrdom = DB::table('cronograma AS reu')
                ->selectRaw('count(*) as ttreu, sum(max_atend) as maxat')
                ->leftJoin('grupo AS gr', 'reu.id_grupo', 'gr.id')
                ->where(function ($query) use ($hoje) {
                    $query->whereRaw('reu.data_fim > ?', [$hoje])->orWhereNull('reu.data_fim');
                })
                ->where(function ($query) {
                    $query->where('reu.modificador', NULL);
                    $query->orWhere('reu.modificador', '<>', 4);
                })
                ->where('reu.id_tipo_tratamento', $idtt)
                ->where('reu.dia_semana', 0)
                ->get();


            $dom = intval($contgrdom[0]->maxat);

            $conttratdom = DB::table('tratamento AS tr')
                ->leftJoin('cronograma AS reu', 'tr.id_reuniao', 'reu.id')
                ->select(DB::raw("$dom - (COUNT(CASE WHEN tr.status < 3  THEN tr.id END)) as trat"))
                ->where('reu.id_tipo_tratamento', $idtt)
                ->where('reu.dia_semana', 0)
                ->get();

            $contcap = DB::table('cronograma AS reu')
                ->leftJoin('grupo AS gr', 'reu.id_grupo', 'gr.id')
                ->where(function ($query) use ($hoje) {
                    $query->whereRaw('reu.data_fim > ?', [$hoje])->orWhereNull('reu.data_fim');
                })
                ->where(function ($query) {
                    $query->where('reu.modificador', NULL);
                    $query->orWhere('reu.modificador', '<>', 4);
                })
                ->where('reu.id_tipo_tratamento', $idtt)
                ->sum('reu.max_atend');

            $dia_hoje = Carbon::today()->weekday();


            return view('recepcao-integrada/agendar-grupo-tratamento', compact('dia_hoje', 'result', 'contgrseg', 'contgrter', 'contgrqua', 'contgrqui', 'contgrsex', 'contgrsab', 'contgrdom', 'conttratseg', 'conttratter', 'conttratqua', 'conttratqui', 'conttratsex', 'conttratsab', 'conttratdom', 'contcap'));
        } catch (\Exception $e) {

            $code = $e->getCode();
            return view('tratamento-erro.erro-inesperado', compact('code'));
        }
    }


    public function escolherHorario(Request $request, $ide)
    {


        try {

            $dia = intval($request->dia);

            $ide = intval($ide);

            $verifica = DB::table('cronograma AS rm')
                ->select('rm.dia_semana', 'rm.id AS idrm', 'enc.id_tipo_tratamento AS trenc', 'rm.id_tipo_tratamento AS trtr')
                ->leftJoin('tratamento AS tr', 'tr.id_reuniao', 'rm.id')
                ->leftJoin('encaminhamento AS enc', 'tr.id_encaminhamento', 'enc.id')
                ->where('rm.dia_semana', $dia)
                ->distinct('rm.id')
                ->whereRaw('enc.id_tipo_tratamento = rm.id_tipo_tratamento')
                ->get();


            $tp_trat = DB::table('encaminhamento AS enc')
                ->select('enc.id_tipo_tratamento')
                ->leftJoin('tipo_tratamento AS tt', 'enc.id_tipo_tratamento', 'tt.id')
                ->where('enc.id', $ide)
                ->value('enc.id_tipo_tratamento');


            $result = DB::table('encaminhamento AS enc')
                ->select('enc.id AS ide', 'enc.id_tipo_encaminhamento', 'dh_enc', 'enc.id_atendimento', 'enc.status_encaminhamento', 'tse.descricao AS tsenc', 'enc.id_tipo_tratamento', 'id_tipo_entrevista', 'at.id AS ida', 'at.id_assistido', 'p1.nome_completo AS nm_1', 'at.id_representante as idr', 'p2.nome_completo as nm_2', 'pa.id AS pid',  'pa.nome', 'pr.id AS prid', 'pr.descricao AS prdesc', 'pr.sigla AS prsigla', 'tt.descricao AS desctrat')
                ->leftJoin('atendimentos AS at', 'enc.id_atendimento', 'at.id')
                ->leftjoin('pessoas AS p1', 'at.id_assistido', 'p1.id')
                ->leftjoin('pessoas AS p2', 'at.id_representante', 'p2.id')
                ->leftjoin('pessoas AS p3', 'at.id_atendente_pref', 'p3.id')
                ->leftjoin('pessoas AS p4', 'at.id_atendente', 'p4.id')
                ->leftJoin('tp_parentesco AS pa', 'at.parentesco', 'pa.id')
                ->leftJoin('tipo_prioridade AS pr', 'at.id_prioridade', 'pr.id')
                ->leftJoin('tipo_status_encaminhamento AS tse', 'enc.status_encaminhamento', 'tse.id')
                ->leftJoin('tipo_tratamento AS tt', 'enc.id_tipo_tratamento', 'tt.id')
                ->where('enc.id', $ide)
                ->get();


            $trata = DB::table('cronograma AS reu')

                ->select(DB::raw('(reu.max_atend - (select count(*) from tratamento tr where tr.id_reuniao = reu.id and tr.status < 3)) as trat'), 'p.nome_completo', 'reu.id AS idr', 'gr.nome AS nomeg', 'reu.dia_semana', 'reu.id_sala', 'reu.id_tipo_tratamento', 'reu.h_inicio', 'td.nome AS nomed', 'reu.h_fim', 'reu.max_atend', 'gr.status_grupo AS idst', 'tsg.descricao AS descst', 'tst.descricao AS tstd', 'sa.numero')
                ->leftJoin('tratamento AS tr', 'reu.id', 'tr.id_reuniao')
                ->leftJoin('tipo_tratamento AS tst', 'reu.id_tipo_tratamento', 'tst.id')
                ->leftJoin('grupo AS gr', 'reu.id_grupo', 'gr.id')
                ->leftJoin('tipo_status_grupo AS tsg', 'gr.status_grupo', 'tsg.id')
                ->leftJoin('cronograma as cro', 'gr.id', 'cro.id_grupo')
                ->leftJoin('membro AS me', 'cro.id', 'me.id_cronograma')
                ->leftJoin('associado as ass', 'me.id_associado', 'ass.id')
                ->leftJoin('pessoas as p', 'ass.id_pessoa', 'p.id')
                ->leftJoin('salas AS sa', 'reu.id_sala', 'sa.id')
                ->leftJoin('tipo_dia AS td', 'reu.dia_semana', 'td.id')
                ->where('reu.dia_semana', $dia)
                ->where('me.id_funcao', 1)
                ->where('reu.id_tipo_tratamento', $tp_trat)
                ->orWhere('tr.status', null)
                ->where('tr.status', '<', 3)
                ->groupBy('p.nome_completo', 'reu.h_inicio', 'reu.max_atend', 'reu.id', 'gr.nome', 'td.nome', 'gr.status_grupo', 'tsg.descricao', 'tst.descricao', 'sa.numero')
                ->orderBy('h_inicio')
                ->get();

            return view('/recepcao-integrada/agendar-horario-tratamento', compact('result', 'trata', 'dia'));
        } catch (\Exception $e) {

            $code = $e->getCode();
            return view('tratamento-erro.erro-inesperado', compact('code'));
        }
    }
    public function trocarGrupo(Request $request, $ide)
    {

        //     try{

        $reu = intval($request->reuniao);


        $countVagas = DB::table('tratamento')->where('id_reuniao', '=', "$reu")->where('status', '<', '3')->count();
        $maxAtend = DB::table('cronograma')->where('id', '=', "$reu")->first();
        $tratID = DB::table('encaminhamento')->where('id', '=', $ide)->get();
        $idt = DB::table('tratamento')->where('id_encaminhamento', $ide)->first();
        $data_ontem = Carbon::yesterday();
        $data_hoje = Carbon::today();

        if ($idt->dt_fim) {
            $dia_fim = Carbon::createFromFormat('Y-m-d G:i:s', "$idt->dt_fim 00:00:00");
            $dia_fim->weekday($maxAtend->dia_semana);


            if ($tratID[0]->id_tipo_tratamento == 2 and $countVagas >= $maxAtend->max_atend) {

                app('flasher')->addError('Número de vagas insuficientes');
                return redirect()->back();
            }

            if ($data_hoje->weekOfYear == $dia_fim->weekOfYear and $data_hoje->diffInDays($dia_fim, false) < 0) {

                app('flasher')->addError('Operação Impossível! Esta é a semana final do assistido');
                return redirect()->back();
            } elseif ($data_hoje->weekOfYear == ($dia_fim->weekOfYear + 1) and $data_hoje->diffInDays($dia_fim, false) < 0 and $maxAtend->dia_semana == 0) {
                app('flasher')->addError('Operação Impossível! Esta é a semana final do assistido');
                return redirect()->back();
            }
        }

        $data = date("Y-m-d H:i:s");

        DB::table('historico_venus')->insert([

            'id_usuario' => session()->get('usuario.id_usuario'),
            'data' => $data,
            'fato' => 19,
            'obs' => $ide

        ]);

        $data = date("Y-m-d H:i:s");


        DB::table('tratamento_grupos')
            ->where('dt_fim', null)
            ->where('id_tratamento', $idt->id)
            ->update([
                'dt_fim' => $data_ontem,
            ]);


        DB::table('tratamento_grupos')
            ->insert([
                'id_cronograma' => $reu,
                'id_tratamento' => $idt->id,
                'dt_inicio' => $data,
            ]);



        if ($idt->dt_fim) {
            DB::table('tratamento')
                ->where('id_encaminhamento', $ide)
                ->update([
                    'id_reuniao' => $reu,
                    'dt_fim' => $dia_fim
                ]);
        } else {
            DB::table('tratamento')
                ->where('id_encaminhamento', $ide)
                ->update([
                    'id_reuniao' => $reu
                ]);
        }



        app('flasher')->addSuccess('Troca efetuada com sucesso!');
        return redirect('/gerenciar-encaminhamentos');
    }
    // catch(\Exception $e){

    //     $code = $e->getCode( );
    //     return view('tratamento-erro.erro-inesperado', compact('code'));
    //         }
    //     }

}
