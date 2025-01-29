<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Redirect;
use PhpParser\Node\Expr\AssignOp\Coalesce;
use PhpParser\Node\Expr\BinaryOp\Coalesce as BinaryOpCoalesce;
use stdClass;

use function Laravel\Prompts\select;

class AtendimentoFraternoController extends Controller
{
    /*
    /--------------------------------------------------------------------------
    /              Controller de Atendimento Fraterno
    /

    / #Fuções:

        +Mostrar quem está sendo atendido pelo específico atendente naquele momento
        +Buscar um novo atendido, se tiver algum em espera, com o botão "Atender Agora" na view
        +Buscar o hisórico de atendimentos do assistido e o do atendente
        +Realizar todas as funções: chamar assistido, iniciar, tratamento, entrevistas, temátics, finalizar e reset

    /--------------------------------------------------------------------------
    */

    // Função de resposta para Ajax, usada para conferir se o atendente marcou os encaminhamentos e temáticas
    public function encaminhamentos_tematicas(String $id)
    {
        $return =  new stdClass();
        $return->encaminhamentos = DB::table('encaminhamento')->where('id_atendimento', $id)->count(); // Conta os encaminhamentos
        $return->tematicas = DB::table('registro_tema')->where('id_atendimento', $id)->count(); //Conta as temáticas
        return $return;
    }


    public function index()
    {

        $atendente = session()->get('usuario.id_associado') ?? 0; // Usuário está logado? Boolean
        $pref_m = session()->get('usuario.sexo'); // Dados se a pessoa é [ 1 => 'Masculino', 2 => 'Feminino', 3 => 'Outros']
        $nome = session()->get('usuario.nome'); // Nome completo de quem está logado, vem de tabela pessoas
        $now =  Carbon::now()->format('Y-m-d'); // Pega a data de hoje com formato de banco de dados
        $data_inicio = Carbon::today()->toDateString(); // Data de hoje
        $motivo = DB::table('tipo_motivo_atendimento')->get(); // Traz os motivos para o modal de cancelamento


        $grupo = DB::table('atendente_dia AS ad') // traz o grupo que a pessoa foi indicada em Atendente Dia
            ->leftJoin('cronograma as cro', 'cro.id', 'ad.id_grupo')
            ->leftJoin('grupo', 'grupo.id', 'cro.id_grupo')
            ->where('ad.id_associado',  $atendente)
            ->where('dh_inicio', '>', $data_inicio) // dh_inicio é um datetime, por isso tem que ser maior e não igual
            ->whereNull('dh_fim') // Só traz ativos
            ->value('grupo.nome');


        //Traz todas as informações do assistido que está em sendo atendido pelo proprio atendente, que não sejam AFE
        $assistido = DB::table('atendimentos AS at')
            ->select(
                'at.id AS idat',
                'p1.ddd',
                'p1.celular',
                'at.dh_chegada',
                'at.dh_inicio',
                'at.dh_fim',
                'at.id_assistido AS idas',
                'p1.nome_completo AS nm_1',
                'at.id_representante',
                'p2.nome_completo AS nm_2',
                'at.id_atendente_pref',
                'p3.nome_completo AS nm_3',
                'at.id_atendente',
                'p4.nome_completo AS nm_4',
                'at.pref_tipo_atendente AS pta',
                'ts.descricao',
                'tx.tipo',
                'pa.nome',
                'at.id_prioridade',
                'pr.descricao AS prdesc',
                'pr.sigla AS prsigla',
                'at.status_atendimento',

            )
            ->leftJoin('associado AS a', 'at.id_atendente', 'a.id')
            ->leftJoin('tipo_status_atendimento AS ts', 'at.status_atendimento', 'ts.id')
            ->leftJoin('pessoas AS p1', 'at.id_assistido', 'p1.id')
            ->leftJoin('pessoas AS p2', 'at.id_representante', 'p2.id')
            ->leftJoin('associado AS ass_at_preferido', 'at.id_atendente_pref', 'ass_at_preferido.id')
            ->leftJoin('pessoas AS p3', 'ass_at_preferido.id_pessoa', 'p3.id')
            ->leftJoin('pessoas AS p4', 'at.id_atendente', 'p4.id')
            ->leftJoin('tp_sexo AS tx', 'at.pref_tipo_atendente', 'tx.id')
            ->leftJoin('tp_parentesco AS pa', 'at.parentesco', 'pa.id')
            ->leftJoin('tipo_prioridade AS pr', 'at.id_prioridade', 'pr.id')
            ->whereIn('at.status_atendimento', [1, 4, 5]) // Apenas aguardando assistido, analisando, ou Em Atendimento
            ->where('at.afe',  null) // Exclui os atendimentos para Atendente Fraterno Específico
            ->where('at.id_atendente', $atendente) // Garante que seja o seu atendimento
            ->get();


        return view('atendimento-assistido.atendendo', compact('assistido', 'atendente', 'now', 'nome', 'grupo', 'motivo'));
    }

    public function pessoas_para_atender()
    {

        $id_associado = session()->get('usuario.id_associado'); // ID associado do usuário logado
        $sexo = session()->get('usuario.sexo'); // // Dados se a pessoa é [ 1 => 'Masculino', 2 => 'Feminino', 3 => 'Outros']


        // Count de atendimentos que seguem as regras de preferidos (Atendente e Sexo)
        $numero_de_assistidos_para_atender = DB::table('atendimentos AS at')
            ->select(
                'at.id as ida',
                'p1.id as idas',
                'p.nome_completo as nm_3',
                'at.status_atendimento',
                'at.id_prioridade',
                'at.dh_chegada',
                'tx.tipo',
                'tp.descricao as prdesc',
                'p1.nome_completo as nm_1',
                'p2.nome_completo as nm_2',
                'p3.nome_completo as nm_4',
                'sl.numero as nr_sala',
                'ts.descricao',
                DB::raw("(CASE WHEN at.afe = true THEN 'AFE' ELSE 'AFI' END) as afe")
            )->leftJoin('associado as ass', 'at.id_atendente', 'ass.id')
            ->leftJoin('associado as ass1', 'at.id_atendente_pref', 'ass1.id')
            ->leftJoin('pessoas as p', 'ass.id_pessoa', 'p.id')
            ->leftJoin('pessoas as p3', 'ass1.id_pessoa', 'p3.id')
            ->leftJoin('tp_sexo as tx', 'at.pref_tipo_atendente', 'tx.id')
            ->leftJoin('tipo_prioridade as tp', 'at.id_prioridade', 'tp.id')
            ->leftJoin('pessoas as p1', 'at.id_assistido', 'p1.id')
            ->leftJoin('pessoas as p2', 'at.id_representante', 'p2.id')
            ->leftJoin('salas as sl', 'at.id_sala', 'sl.id')
            ->leftjoin('tipo_status_atendimento AS ts', 'at.status_atendimento', 'ts.id')
            ->whereDate('dh_chegada', Carbon::today()->toDateString()) // Cofere se as datas do DateTime e do Carbon batem
            ->where('at.status_atendimento', 2) // Status Aguardando Atendimento
            ->where(function ($query) use ($id_associado) {
                $query->where('at.id_atendente_pref', $id_associado) // Atendente prefrido é o usuário logado
                    ->orWhereNull('at.id_atendente_pref'); // Inclui registros onde não há atendente preferencial
            })
            ->where(function ($query) use ($sexo) {
                $query->where('at.pref_tipo_atendente', $sexo) // Sexo preferido é o mesmo que o do usuário logado
                    ->orWhereNull('at.pref_tipo_atendente'); // Inclui registros onde não há atendente preferencial
            })->count(); // Conta


        return response()->json($numero_de_assistidos_para_atender); // Retorna para o Ajax o número de pessoas na fila
    }

    public function atende_agora()
    {

        // DB::beginTransaction();

        // try {

            $hoje =  Carbon::today(); // Data de Hoje
            $atendente = session()->get('usuario.id_associado'); // Id associado de quem está logado
            $pref_m = session()->get('usuario.sexo'); // Dados se a pessoa é [ 1 => 'Masculino', 2 => 'Feminino', 3 => 'Outros']


            //Conta todos os atendimentos  ativos do atendente
            $atendendo = DB::table('atendimentos AS at')
                ->leftjoin('membro AS m', 'at.id_atendente', 'm.id')
                ->leftjoin('associado AS a', 'm.id_associado', 'a.id')
                ->leftJoin('pessoas AS p', 'a.id_pessoa', 'p.id')
                ->where('at.id_atendente', $atendente)
                ->whereNull('afe') // Não conta os Atendimentos Fraternos Específicos
                ->whereIn('at.status_atendimento', [1, 4, 5]) // Apenas aguardando assistido, analisando, ou Em Atendimento
                ->count();


            //Devolve os IDs atendimento que estão Aguardando Atendimento
            $atende = DB::table('atendimentos')
                ->where('status_atendimento', 2)
                ->whereNull('afe')
                ->whereNull('id_atendente_pref') // Atendente preferido null
                ->whereNull('pref_tipo_atendente') // Sexo de atendimento preferido null
                ->pluck('id')
                ->toArray();

            // Devolve os IDs que estão Aguardando Atendimento
            $atende1 = DB::table('atendimentos')->where('status_atendimento', 2)
                ->whereNull('afe')
                ->where('id_atendente_pref', $atendente) // O atendente preferido é o usuário logado
                ->pluck('id')
                ->toArray();


            /* Devolve os IDs que estão Aguardando Atendimento
                    *Caso o Atendente esteja sem sexo em pessoas, esse item não pegará nada,
                    gerando um bug que ele não consegue buscar essas pessoas */
            $atende2 = DB::table('atendimentos')->where('status_atendimento', 2)
                ->whereNull('afe')
                ->where('pref_tipo_atendente', $pref_m) // O Sexo de preferência é o mesmo do Atendente
                ->pluck('id')
                ->toArray();

            $atendeFinal = array_merge($atende, $atende1, $atende2); // Une os ids em uma única variável
            $assistido = count($atendeFinal); // Conta a quaantidade de IDs retornados


            // Usado para validar se o atendente está em uma sala
            $sala = DB::table('atendente_dia AS atd')
                ->whereDate('dh_inicio', Carbon::today()->toDateString()) // Se o item de sala dele é do dia de hoje
                ->whereNull('dh_fim') // Não pode ter sido finalizado
                ->where('id_associado', $atendente) // Apenas para o usuário logado
                ->value('id_sala');


            if ($atendendo > 0) { // Valida se outra pessoa já está em atendimento

                app('flasher')->addError('Você não pode atender dois assistidos ao mesmo tempo.');

                return redirect('/atendendo');
            } elseif ($assistido < 1) { // Checa se, seguindo as regras de sexo e atendimento preferido, existe pessoas na fila

                app('flasher')->addError('Todos os assistidos foram atendidos.');

                return redirect('/atendendo');
            } elseif ($atendendo < 1 && $sala == null) { // Se não estiver atendendo niguém, porém sem uma sala cadastrada

                app('flasher')->addError('O atendente deve estar designado para o trabalho de hoje.');

                return redirect('/atendendo');
            } elseif ($atendendo < 1 && $sala > 0) { // Se não estiver atendendo ninguem e com uma sala cadastrada

                // Usado na inserção de LOG
                $dt_hora = Carbon::now();

                // Atualiza os atendimentos para o Atendente
                $atendimentoSelecionado = DB::table('atendimentos')
                    ->where('status_atendimento', 2) // Status tem que ser Aguardando Atendimento
                    ->where(function ($query) {
                        $query->whereNull('afe')  // AFE tem que ser null
                            ->orWhere('afe', false); // Caso alguma funcionalidade inclua AFE como false, Fallback
                    })
                    ->where(function ($query) use ($atendente) {
                        $query->whereNull('id_atendente_pref') // Atendente preferido vazio
                            ->orWhere('id_atendente_pref', $atendente); // Atendente preferido sendo o usuário logado
                    })
                    ->where(function ($query) use ($pref_m) {
                        $query->whereNull('pref_tipo_atendente') // Sexo preferido null
                            ->orWhere('pref_tipo_atendente', $pref_m); // Sexo preferido igual ao do usuário logado
                    })
                    ->orderby('id_prioridade')->orderBy('dh_chegada') // Ordena pela prioridade e após pelo horário de chegada
                    ->limit(1); // Traz apenas um por vez
                    // dd( $atendimentoSelecionado->first()->id);
                // Usado para conseguir o ID do atendimento selecionado, para a inserção no LOG
                $ida = ( $atendimentoSelecionado->first()->id);
                $atendimentoSelecionado = $atendimentoSelecionado->update([
                    'id_atendente' => $atendente, // Marca o usuário logado como atendente deste atendimento
                    'id_sala' => $sala, // Marca a sala que o usuário logado está
                    'status_atendimento' => 4 // Troca o status do atendimento para Analisando
                ]);

                // Insere no histórico a criação do atendimento
                DB::table('log_atendimentos')->insert([
                    'id_referencia' => $ida,
                    'id_usuario' => session()->get('usuario.id_usuario'),
                    'id_acao' => 1, // mudou de Status para
                    'id_observacao' => 4, // Analisando
                    'id_origem' => 1, // Atendimento
                    'data_hora' => $dt_hora
                ]);

                app('flasher')->addSuccess('O assistido foi selecionado com sucesso.');
                DB::commit();
                return redirect('/atendendo');
            }
        // } catch (\Exception $e) {

        //     app('flasher')->addError("Houve um erro inesperado: #" . $e->getCode());
        //     DB::rollBack();
        //     return redirect()->back();
        // }
    }

    //Botão Analisar na VIEW
    public function history($idat, $idas)
    {

        try {

            // Armazena todos os dados dos atendimentos para ser usado no Accordion
            $analisa = DB::table('atendimentos AS at')
                ->select(
                    'at.id AS ida',
                    'at.observacao',
                    'p1.id AS idas',
                    'p1.ddd',
                    'p1.sexo',
                    'p1.celular',
                    'at.dh_chegada',
                    'at.dh_inicio',
                    'at.dh_fim',
                    'at.id_assistido',
                    'p1.nome_completo AS nm_1',
                    'at.id_representante',
                    'p2.nome_completo AS nm_2',
                    'at.id_atendente_pref',
                    'ps1.nome_completo AS nm_3',
                    'at.id_atendente',
                    'ps2.nome_completo AS nm_4',
                    'at.pref_tipo_atendente',
                    'ts.descricao AS tst',
                    'tsx.tipo',
                    'pa.nome',
                    'p1.dt_nascimento'
                )
                ->leftJoin('tipo_status_atendimento AS ts', 'at.status_atendimento', 'ts.id')
                ->leftJoin('pessoas AS p1', 'at.id_assistido', 'p1.id')
                ->leftJoin('pessoas AS p2', 'at.id_representante', 'p2.id')
                ->leftJoin('associado AS ad1', 'at.id_atendente', 'ad1.id')
                ->leftJoin('pessoas AS ps1', 'ad1.id_pessoa', 'ps1.id')
                ->leftJoin('membro AS m1', 'at.id_atendente_pref', 'm1.id_associado')
                ->leftJoin('associado AS ad2', 'm1.id_associado', 'ad2.id')
                ->leftJoin('pessoas AS ps2', 'ad1.id_pessoa', 'ps2.id')
                ->leftJoin('tp_sexo AS tx', 'at.pref_tipo_atendente', 'tx.id')
                ->leftJoin('tp_parentesco AS pa', 'at.parentesco', 'pa.id')
                ->leftJoin('tp_sexo AS tsx', 'p1.sexo', 'tsx.id')
                ->where('at.id_assistido', $idas)
                ->orderBy('at.dh_chegada', 'desc')
                ->get();

            //Pega a variável e popula com dados de duas tabelas diferentes
            foreach ($analisa as $key => $teste) {

                $trata = DB::table('encaminhamento AS enc') // Traz todos os tipos de encaminhamento do Atendimento Atual
                    ->select('tt.descricao AS tdt')
                    ->leftJoin('tipo_tratamento AS tt', 'enc.id_tipo_tratamento', 'tt.id')
                    ->where('enc.id_atendimento', $teste->ida)
                    ->whereNotNull('enc.id_tipo_tratamento')
                    ->get();
                $teste->tratamentos = $trata; // Para cada atendimento insere os tipos de tratamento dele

                $entre = DB::table('encaminhamento AS enc') // Traz todos os encaminhamentos de entrevita para o Atendimento Atual
                    ->select('te.descricao AS tde')
                    ->leftJoin('tipo_entrevista AS te', 'enc.id_tipo_entrevista', 'te.id')
                    ->where('enc.id_atendimento', $teste->ida)
                    ->whereNotNull('enc.id_tipo_entrevista')
                    ->get();
                $teste->entrevistas = $entre; // Para cada atendimento insere os tipos de entrevista dele

                $tematica = DB::table('registro_tema AS rt') // Busca todas as temáticas do Atendimento Atual
                    ->select('tt.nm_tca as tematica')
                    ->leftJoin('tipo_temas as  tt', 'rt.id_tematica', 'tt.id')
                    ->where('rt.id_atendimento', $teste->ida)
                    ->get();
                $teste->tematicas = $tematica; // Insere todas as temáticas do Atendimento Atual
            }

            return view('/atendimento-assistido/historico-assistido', compact('analisa'));
        } catch (\Exception $e) {
            app('flasher')->addError("Houve um erro inesperado: #" . $e->getCode());
            return redirect()->back();
        }
    }

    //Botão Chamar Assistido na VIEW
    public function fimanalise($idat)
    {

        DB::beginTransaction();
        try {

            $dt_hora = Carbon::now();
            $atendente = session()->get('usuario.id_associado'); // Traz o ID associado do usuário atual

            // Conta se o atendimento está no status Analisando
            $sit = DB::table('atendimentos AS at')
                ->where('at.id', $idat)
                ->where('at.status_atendimento', 4) // Status Analisando
                ->count();

            if ($sit == 1) {
                //Atualiza o status para Aguardando Assistido
                DB::table('atendimentos AS at')
                    ->where('status_atendimento', '=', 4)
                    ->where('at.id', $idat)
                    ->update([
                        'status_atendimento' => 1,
                        'id_atendente' => $atendente
                    ]);

                // Insere no histórico a criação do atendimento
                DB::table('log_atendimentos')->insert([
                    'id_referencia' => $idat,
                    'id_usuario' => session()->get('usuario.id_usuario'),
                    'id_acao' => 1, // mudou de Status para
                    'id_observacao' => 1, // Aguardando Assistido
                    'id_origem' => 1, // Atendimento
                    'data_hora' => $dt_hora
                ]);

                app('flasher')->addSuccess('O status do atendimento foi alterado para "Aguardando o assistido".');
                DB::commit();
                return redirect()->back();
            } else {
                app('flasher')->addError('Esta ação não pode ser executada, este status já foi ultrapassado.');
                return redirect()->back();
            }
        } catch (\Exception $e) {

            app('flasher')->addError("Houve um erro inesperado: #" . $e->getCode());
            DB::rollBack();
            return redirect()->back();
        }
    }
    // Botão Iniciar na VIEW
    public function inicio($idat)
    {

        DB::beginTransaction();
        try {

            $now =  Carbon::now();
            $statusAtendimento = DB::table('atendimentos AS at')->where('at.id', $idat)->value('status_atendimento');
            if ($statusAtendimento == 1) { // status igual a Aguardando Assistido

                // Troca o Status para Em Atendimento e Marca o Inicio como Agora
                DB::table('atendimentos AS at')
                    ->where('at.id', $idat)
                    ->update([
                        'status_atendimento' => 5,
                        'dh_inicio' => $now
                    ]);

                // Insere no histórico a criação do atendimento
                DB::table('log_atendimentos')->insert([
                    'id_referencia' => $idat,
                    'id_usuario' => session()->get('usuario.id_usuario'),
                    'id_acao' => 1, // mudou de Status para
                    'id_observacao' => 5, // Em atendimento
                    'id_origem' => 1, // Atendimento
                    'data_hora' => $now
                ]);

                app('flasher')->addSuccess('O status do atendimento foi alterado para "Em atendimento".');
                DB::commit();
            } elseif ($statusAtendimento == 5) {
                app('flasher')->addError('Atendimento já iniciado!');
            } else {
                app('flasher')->addError('Chame o assistido antes de iniciar!');
            }

            return redirect()->back();
        } catch (\Exception $e) {
            app('flasher')->addError("Houve um erro inesperado: #" . $e->getCode());
            DB::rollBack();
            return redirect()->back();
        }
    }

    //Método CREATE do Botão Tratamento
    public function tratar($idat, $idas)
    {
        try {

            // Confere se o tratamento está em Atendimento
            $sit = DB::table('atendimentos AS at')
                ->where('at.id', $idat)
                ->where('status_atendimento', 5)
                ->count();

            // Traz o nome completo do assistido para a view, através do id_pessoa
            $atendido = DB::table('pessoas AS p')
                ->select('nome_completo AS nm')
                ->where('p.id', $idas)
                ->get();

            // Confere se tem algum encaminhamento de tratamento já criado, para bloquear nova inclusão
            $verifi = DB::table('encaminhamento AS enc')
                ->leftJoin('atendimentos AS at', 'enc.id_atendimento', 'at.id')
                ->where('at.id', $idat) // Para esse atendimento
                ->whereIn('id_tipo_encaminhamento', [2, 3]) // Tratamento ou Grupo de Apoio
                ->count();

            // Confere se o atendimento está ativo e se não tem nenhum encaminhamento
            if ($sit == 1 and $verifi == 0) {

                // Traz os dados necessários do atendimento para a view
                $assistido = DB::table('atendimentos AS at')
                    ->select(
                        'at.id as idat',
                        'at.id_assistido as idas',
                        'at.dh_chegada',
                        'at.dh_inicio',
                        'at.dh_fim',
                        'at.id_assistido',
                        'p1.nome_completo AS nm_1',
                        'at.id_representante',
                        'at.id_atendente'
                    )
                    ->leftJoin('pessoas AS p1', 'at.id_assistido', 'p1.id')
                    ->where('at.id', $idat)
                    ->get();

                return view('atendimento-assistido.tratamentos', compact('assistido'));
            } else if ($verifi > 0) { // Se tiver algum encaminhamento
                app('flasher')->addError('Tratamentos já criados! Limpe para recriá-los!');
            } else if ($sit != 1) { // Se o status não for Em Atendimento
                app('flasher')->addError('O assistido deve estar "Em atendimento" para a marcação de tratamentos!');
            }
            return redirect()->back();
        } catch (\Exception $e) {

            app('flasher')->addError("Houve um erro inesperado: #" . $e->getCode());
            return redirect()->back();
        }
    }

    // Método Create do Botão de Entrevista
    public function entrevistar($idat, $idas)
    {
        try {

            // Confere se o atendimento está Em Atendimento
            $sit = DB::table('atendimentos AS at')
                ->where('at.id', $idat)
                ->where('status_atendimento', 5) // Em Atendimento
                ->count();

            // Confere se o atendimento já tem encaminhamentos de entrevista
            $verifi = DB::table('encaminhamento AS enc')
                ->leftJoin('atendimentos AS at', 'enc.id_atendimento', 'at.id')
                ->where('at.id', $idat)
                ->where('id_tipo_encaminhamento', 1) // Tipo Entrevista
                ->count();


            if ($sit == 1 and $verifi == 0) {

                // Busca os dados para popular a view
                $assistido = DB::table('atendimentos AS at')
                    ->select(
                        'at.id as idat',
                        'at.dh_chegada',
                        'at.dh_inicio',
                        'at.dh_fim',
                        'at.id_assistido as idas',
                        'p1.nome_completo AS nm_1',
                        'at.id_representante',
                        'at.id_atendente'
                    )
                    ->leftJoin('pessoas AS p1', 'at.id_assistido', 'p1.id')
                    ->where('at.id', $idat)
                    ->get();

                return view('atendimento-assistido.entrevistas', compact('assistido'));
            } else if ($verifi > 0) { // Se tiver algum encaminhamento
                app('flasher')->addError('Tratamentos já criados! Limpe para recriá-los!');
            } else if ($sit != 1) { // Se o status não for Em Atendimento
                app('flasher')->addError('O assistido deve estar "Em atendimento" para a marcação de tratamentos!');
            }
            return redirect()->back();
        } catch (\Exception $e) {
            app('flasher')->addError("Houve um erro inesperado: #" . $e->getCode());
            return redirect()->back();
        }
    }

    // Método Create do Botão de Temáticas
    public function pre_tema($idat)
    {

        $r_tema = DB::table('registro_tema')->where('id_atendimento', $idat)->count(); // Confere se existem temas para o atendimento
        $nota = DB::table('atendimentos')->where('id', $idat)->first(); // Tem o objetivo de conseguir a anotação de atendimentos
        // Confere se o atendimento está Em Atendimento
        $sit = DB::table('atendimentos AS at')
            ->where('at.id', $idat)
            ->where('status_atendimento', 5) // Em Atendimento
            ->count();

        if ($r_tema > 0 or $nota->observacao != null) {
            app('flasher')->addError("As temáticas do atendimento $idat já foram registradas.");
            return redirect()->back();
        } else if ($sit == 0) {
            app('flasher')->addError('O assistido deve estar "Em atendimento" para a marcação de tratamentos!');
            return redirect()->back();
        } else {

            $assistido = DB::table('atendimentos AS at')
                ->select(
                    'at.id as idat',
                    'at.dh_chegada',
                    'at.dh_inicio',
                    'at.dh_fim',
                    'at.id_assistido',
                    'p1.nome_completo AS nm_1',
                    'at.id_representante',
                    'at.id_atendente'
                )
                ->leftJoin('pessoas AS p1', 'at.id_assistido', 'p1.id')
                ->where('at.id', $idat)
                ->get();
        }

        return view('atendimento-assistido.tematicas', compact('assistido'));
    }

    public function enc_trat(Request $request, $idat, $idas)
    {
        $now = Carbon::today(); // datetime de agora

        // Transforma o "on" do toggle em boolean
        $harmonia = isset($request->pph) ? 1 : 0;
        $desobsessivo = isset($request->ptd) ? 1 : 0;
        $acolher = isset($request->ga) ? 1 : 0;
        $viver = isset($request->gv) ? 1 : 0;
        $quimica = isset($request->gdq) ? 1 : 0;

        // Busca todos os encaminhamentos de Tratamento ativos da pessoa que está sendo atendida
        $countEncaminhamentos = DB::table('encaminhamento as enc')
            ->leftJoin('atendimentos as at', 'enc.id_atendimento', 'at.id')
            ->leftJoin('tratamento as trat', 'enc.id', 'trat.id_encaminhamento')
            ->where('enc.id_tipo_encaminhamento', 2) // Encaminhamento tipo "Tratamento"
            ->where('at.id_assistido', $idas) // Do Assistido
            ->where('enc.status_encaminhamento', '<', 3) // Para agendar, Agendado, ou seja, apenas ativos
            ->where(function ($query) use ($now){
                $query->where(function ($innerQuery) use ($now){
                    $innerQuery->whereNotNull('trat.dt_fim'); // Regra apenas para tratamentos que tem DT_FIM
                    $innerQuery->whereNot('trat.dt_fim', $now); // Tratamentos que acabam no dia do atendimento, podem ser renovados
                });
                $query->orWhereNull('trat.dt_fim'); // Exclui da regra todos os que não tem DT_FIM
            })
            ->pluck('id_tipo_tratamento')->toArray();
        dd($countEncaminhamentos);
        // Busca todos os encaminhamentos  de Grupo de Apoio ativos da pessoa que está sendo atendida
        $countGrupoApoio = DB::table('encaminhamento as enc')
            ->leftJoin('atendimentos as at', 'enc.id_atendimento', 'at.id')
            ->where('enc.id_tipo_encaminhamento', 3) // Encaminhamento tipo "Grupo de Apoio"
            ->where('at.id_assistido', $idas) // Do Assistido
            ->where('enc.status_encaminhamento', '<', 3) // Para agendar, Agendado, ou seja, apenas ativos
            ->pluck('id_tipo_tratamento')->toArray();



        // PTD -> Passe de Tratamento Desobsessivo
        if (in_array(1, $countEncaminhamentos) and $desobsessivo) { // Confere se njá tem um PTD
            app('flasher')->addWarning('Já existe um encaminhamento PTD ativo para esta pessoa!');
        } else if (in_array(2, $countEncaminhamentos) and $desobsessivo) { // Confere se já tem PTI
            app('flasher')->addWarning('Existe um encaminhamento PTI ativo para esta pessoa!');
        } else if ($desobsessivo) {
            DB::table('encaminhamento AS enc')->insert([
                'id_tipo_encaminhamento' => 2,
                'id_atendimento' => $idat,
                'id_tipo_tratamento' => 1,
                'status_encaminhamento' =>  1
            ]);

            app('flasher')->addSuccess('O encaminhamento para PTD foi criado com sucesso.');
        }

        // PTH -> Palestra/Passe de Harmonização
        if (in_array(3, $countEncaminhamentos) and $harmonia) {
            app('flasher')->addWarning('Já existe um encaminhamento para o PTH ativo para esta pessoa!');
        } else if ($harmonia) {
            DB::table('encaminhamento AS enc')->insert([
                'id_tipo_encaminhamento' => 2,
                'id_atendimento' => $idat,
                'id_tipo_tratamento' => 3,
                'status_encaminhamento' =>  1
            ]);

            app('flasher')->addSuccess('O encaminhamento para Grupo de Harmonização foi criado com sucesso.');
        }

        /* A Partir desse ponto as validações estão canceladas, logo que estes encaminhamentos são apenas estatísticas*/

        // Acolher -> Grupo Acolher
        // if (in_array(7, $countGrupoApoio) and $acolher) {
        //     app('flasher')->addWarning('Já existe um encaminhamento para o Grupo Acolher ativo para esta pessoa!');
        // }
        if ($acolher) {
            DB::table('encaminhamento AS enc')->insert([
                'id_tipo_encaminhamento' => 3,
                'id_atendimento' => $idat,
                'id_tipo_tratamento' => 7,
                'status_encaminhamento' =>  1
            ]);

            app('flasher')->addSuccess('O encaminhamento para Grupo Acolher foi criado com sucesso.');
        }


        // Dependência Quimica -> Grupo de Dependência Química
        // if (in_array(9, $countGrupoApoio) and $quimica) {
        //     app('flasher')->addWarning('Já existe um encaminhamento para o Grupo de Dependência Química ativo para esta pessoa!');
        // }
        if ($quimica) {
            DB::table('encaminhamento AS enc')->insert([
                'id_tipo_encaminhamento' => 3,
                'id_atendimento' => $idat,
                'id_tipo_tratamento' => 9,
                'status_encaminhamento' =>  1
            ]);

            app('flasher')->addSuccess('O encaminhamento para Grupo de Dependência Química foi criado com sucesso.');
        }

        // Viver -> Grupo Viver
        // if (in_array(10, $countGrupoApoio) and $viver) {
        //     app('flasher')->addWarning('Já existe um encaminhamento para o Grupo Viver ativo para esta pessoa!');
        // }
        if ($viver) {
            DB::table('encaminhamento AS enc')->insert([
                'id_tipo_encaminhamento' => 3,
                'id_atendimento' => $idat,
                'id_tipo_tratamento' => 10,
                'status_encaminhamento' =>  1
            ]);

            app('flasher')->addSuccess('O encaminhamento para Grupo Viver foi criado com sucesso.');
        }

        return Redirect('/atendendo');
    }

    public function enc_entre(Request $request, $idat, String $idas)
    {


        $hoje = Carbon::today(); // datetime de agora

        // Transforma o "on" do toggle em boolean
        $ame = isset($request->ame) ? 1 : 0;
        $afe = isset($request->afe) ? 1 : 0;
        $diamo = isset($request->diamo) ? 1 : 0;
        $nutres = isset($request->nutres) ? 1 : 0;
        $evangelho = isset($request->gel) ? 1 : 0;

        // Retorna todos os IDs dos encaminhamentos de tratamento
        $countTratamentos = DB::table('encaminhamento as enc')
            ->leftJoin('atendimentos as at', 'enc.id_atendimento', 'at.id')
            ->leftJoin('tratamento as trat', 'enc.id', 'trat.id_encaminhamento')
            ->where('enc.id_tipo_encaminhamento', 2) // Encaminhamento de Tratamento
            ->where('at.id_assistido', $idas)
            ->where('enc.status_encaminhamento', '<', 3) // 3 => Finalizado, Traz apenas os ativos (Para Agendar, Agendado)
            ->whereNot('trat.dt_fim', $hoje) // Tratamentos que acabam no dia do atendimento, podem ser renovados
            ->pluck('id_tipo_tratamento')->toArray();

        // Retorna todos os IDs dos encaminhamentos de entrevista
        $countEntrevistas = DB::table('encaminhamento as enc')
            ->leftJoin('atendimentos as at', 'enc.id_atendimento', 'at.id')
            ->where('enc.id_tipo_encaminhamento', 1) // Encaminhamento de Entrevista
            ->where('at.id_assistido', $idas)
            ->where('enc.status_encaminhamento', '<', 3) // 3 => Finalizado, Traz apenas os ativos (Para Agendar, Agendado)
            ->pluck('id_tipo_entrevista')->toArray();

        /*
        /Todos os suportados pelo sistema checam se existe uma entrevista ou se tem algum tratamento ativo
        / As entrevistas que precicisam de PTD checam se não existe um PTI ativo também, logo que são equivalentes
        */

        // AFE => Atendente Fraterno Específico
        if (in_array(5, $countEntrevistas) and $afe) {
            app('flasher')->addWarning('Já existe um encaminhamento para o AFE ativo para esta pessoa!');
        } else if ($afe) {
            // Insere a entrevista AFE
            DB::table('encaminhamento AS enc')->insert([
                'id_tipo_encaminhamento' => 1,
                'id_atendimento' => $idat,
                'id_tipo_entrevista' => 3,
                'status_encaminhamento' =>  1
            ]);

            app('flasher')->addSuccess('O encaminhamento para o AFE foi criado com sucesso.');
        }

        // AME => Tratamento Fluidoterápico Integral (TFI)
        if ((in_array(5, $countEntrevistas) or in_array(6, $countTratamentos)) and $ame) {
            app('flasher')->addWarning('Já existe um encaminhamento para o Integral ativo para esta pessoa!');
        } else if ((in_array(1, $countTratamentos) or in_array(2, $countTratamentos)) and $ame) {
            // Insere a entrevista AME
            DB::table('encaminhamento AS enc')->insert([
                'id_tipo_encaminhamento' => 1,
                'id_atendimento' => $idat,
                'id_tipo_entrevista' => 5,
                'status_encaminhamento' =>  1
            ]);

            app('flasher')->addSuccess('O encaminhamento para AME foi criado com sucesso.');
        } else if ($ame) {
            //Insere entrevista AME
            DB::table('encaminhamento AS enc')->insert([
                'id_tipo_encaminhamento' => 1,
                'id_atendimento' => $idat,
                'id_tipo_entrevista' => 5,
                'status_encaminhamento' =>  1
            ]);

            // Insere o encaminhamento PTD
            DB::table('encaminhamento AS enc')->insert([
                'id_tipo_encaminhamento' => 2,
                'id_atendimento' => $idat,
                'id_tipo_tratamento' => 1,
                'status_encaminhamento' =>  1
            ]);

            app('flasher')->addSuccess('Os encaminhamentos para AME e PTD  foi criado com sucesso.');
        }
        // DIAMO => Programa de Apoio a Portadores de Mediunidade Ostensiva (PROAMO)
        if ((in_array(6, $countEntrevistas) or in_array(4, $countTratamentos)) and $diamo) {
            app('flasher')->addWarning('Já existe um encaminhamento para o Proamo ativo para esta pessoa!');
        } else if ((in_array(1, $countTratamentos) or in_array(2, $countTratamentos)) and $diamo) {

            // Atualiza todos os tratamentos PTD ativos para infinitos
            DB::table('tratamento as tr')
                ->leftJoin('encaminhamento as enc', 'tr.id_encaminhamento', 'enc.id')
                ->leftJoin('atendimentos as at', 'enc.id_atendimento', 'at.id')
                ->where('at.id_assistido', $idas)
                ->where('enc.id_tipo_tratamento', 1)
                ->where('tr.status', '<', 3)
                ->update([
                    'tr.dt_fim' => null
                ]);

            //Inserir estrevista DiAMO na tabela
            DB::table('encaminhamento AS enc')->insert([
                'id_tipo_encaminhamento' => 1,
                'id_atendimento' => $idat,
                'id_tipo_entrevista' => 6,
                'status_encaminhamento' =>  5
            ]);

            app('flasher')->addSuccess('O encaminhamento para o Proamo foi criado com sucesso.');
        } else if ($diamo) {
            //Insere entrevista DIAMO
            DB::table('encaminhamento AS enc')->insert([
                'id_tipo_encaminhamento' => 1,
                'id_atendimento' => $idat,
                'id_tipo_entrevista' => 6,
                'status_encaminhamento' =>  5
            ]);

            // Insere o encaminhamento PTD
            DB::table('encaminhamento AS enc')->insert([
                'id_tipo_encaminhamento' => 2,
                'id_atendimento' => $idat,
                'id_tipo_tratamento' => 1,
                'status_encaminhamento' =>  1
            ]);

            app('flasher')->addSuccess('Os encaminhamentos para o Proamo e PTD foram criados com sucesso.');
        }

        // NUTRES => Passe Tratamento Intensivo (PTI)
        if ((in_array(4, $countEntrevistas) or in_array(2, $countTratamentos)) and $nutres) {
            app('flasher')->addWarning('Já existe um encaminhamento para o PTI ativo para esta pessoa!');
        } else if (in_array(1, $countTratamentos) and $nutres) {

            // Atualiza todos os tratamentos PTD ativos para infinitos
            DB::table('tratamento as tr')
                ->leftJoin('encaminhamento as enc', 'tr.id_encaminhamento', 'enc.id')
                ->leftJoin('atendimentos as at', 'enc.id_atendimento', 'at.id')
                ->where('at.id_assistido', $idas)
                ->where('enc.id_tipo_tratamento', 1)
                ->where('tr.status', '<', 3)
                ->update([
                    'tr.dt_fim' => null
                ]);

            // Insere a entrevista PTI
            DB::table('encaminhamento AS enc')->insert([
                'id_tipo_encaminhamento' => 1,
                'id_atendimento' => $idat,
                'id_tipo_entrevista' => 4,
                'status_encaminhamento' =>  1
            ]);

            app('flasher')->addSuccess('O encaminhamento para o PTI foi criado com sucesso.');
        } else if ($nutres) {
            // Insere a entrevista PTI
            DB::table('encaminhamento AS enc')->insert([
                'id_tipo_encaminhamento' => 1,
                'id_atendimento' => $idat,
                'id_tipo_entrevista' => 4,
                'status_encaminhamento' =>  1
            ]);

            // Insere um novo Encaminhamento PTD
            DB::table('encaminhamento AS enc')->insert([
                'id_tipo_encaminhamento' => 2,
                'id_atendimento' => $idat,
                'id_tipo_tratamento' => 1,
                'status_encaminhamento' =>  2
            ]);

            app('flasher')->addSuccess('O encaminhamento para o PTI e PTD foi criado com sucesso.');
        }

        // Evangelho => Grupo de Evangelho no Lar (GEL)
        // if (in_array(8, $countEntrevistas) and $evangelho ) {
        //     app('flasher')->addWarning('Já existe um encaminhamento para o Grupo de Evangelho no Lar ativo para esta pessoa!');
        // }
        if ($evangelho) {
            DB::table('encaminhamento AS enc')->insert([
                'id_tipo_encaminhamento' => 1,
                'id_atendimento' => $idat,
                'id_tipo_entrevista' => 8,
                'status_encaminhamento' =>  1
            ]);

            app('flasher')->addSuccess('O encaminhamento para o Grupo de Evangelho no Lar foi criado com sucesso.');
        }

        return redirect('/atendendo');
    }

    public function finaliza(Request $request, $idat)
    {
        try {

            $now = Carbon::now(); // Retorna um datetime de agora
            $emergencia = $request->emergencia == 'on' ? 1 : 0; // Transforma o checkbox de emergencia em boolean
            $status =  DB::table('atendimentos AS at')->where('at.id', $idat)->value('status_atendimento');

            if ($status == 5) {

                // Finaliza o atendimento
                DB::table('atendimentos AS at')
                    ->where('at.id', $idat) // Atendimento Atual
                    ->update([
                        'status_atendimento' => 6, // Status Finalizado
                        'dh_fim' => $now, // Marca o horário de fim
                        'emergencia' => $emergencia // Marca se é um caso de emergência ou não
                    ]);
                app('flasher')->addSuccess('Atendimento Finalizado com Sucesso.');
            } elseif ($status) {
                app('flasher')->addError('O assistido deve estar "Em atendimento" para a marcação de tratamentos!');
            }

            return redirect()->back();
        } catch (\Exception $e) {
            app('flasher')->addError("Houve um erro inesperado: #" . $e->getCode());
            DB::rollBack();
            return redirect()->back();
        }
    }

    public function meus_atendimentos()
    {
        try {

            $atendente = session()->get('usuario.id_associado'); // Retorna o ID associado do usuário logado
            $nome = session()->get('usuario.nome'); // Retorna o nome completo, da tabela "pessoas", do usuário logado
            $now = Carbon::now()->format('Y-m-d'); // Traz a data de hoje formatada para banco de dados

            // Retorna todos os dados de atendimentos do usuário logado, gera um bug se alguém que não seja associado abrir
            $assistido = DB::table('atendimentos AS at')
                ->select('at.id AS ida', 'at.observacao',   'p1.id AS idas', 'p1.ddd', 'p1.sexo', 'p1.celular', 'at.dh_chegada', 'at.dh_inicio', 'at.dh_fim', 'at.id_assistido', 'p1.nome_completo AS nm_1', 'at.id_representante', 'p2.nome_completo AS nm_2', 'at.id_atendente_pref', 'ps1.nome_completo AS nm_3', 'at.id_atendente', 'ps2.nome_completo AS nm_4', 'at.pref_tipo_atendente', 'ts.descricao AS tst', 'tsx.tipo', 'pa.nome', 'at.status_atendimento', 'p1.dt_nascimento')
                ->leftJoin('tipo_status_atendimento AS ts', 'at.status_atendimento', 'ts.id')
                ->leftJoin('pessoas AS p1', 'at.id_assistido', 'p1.id')
                ->leftJoin('pessoas AS p2', 'at.id_representante', 'p2.id')
                ->leftJoin('membro AS m', 'at.id_atendente', 'm.id_associado')
                ->leftJoin('associado AS ad1', 'm.id_associado', 'ad1.id')
                ->leftJoin('pessoas AS ps1', 'ad1.id_pessoa', 'ps1.id')
                ->leftJoin('membro AS m1', 'at.id_atendente_pref', 'm1.id_associado')
                ->leftJoin('associado AS ad2', 'm1.id_associado', 'ad2.id')
                ->leftJoin('pessoas AS ps2', 'ad1.id_pessoa', 'ps2.id')
                ->leftJoin('tp_sexo AS tx', 'at.pref_tipo_atendente', 'tx.id')
                ->leftJoin('tp_parentesco AS pa', 'at.parentesco', 'pa.id')
                ->leftJoin('tp_sexo AS tsx', 'p1.sexo', 'tsx.id')
                ->leftJoin('registro_tema AS rt', 'at.id', 'rt.id_atendimento')
                ->where('id_atendente', $atendente) // Aqui acontece o bug
                ->distinct('at.dh_chegada')
                ->orderBy('at.dh_chegada', 'desc')
                ->get();

            foreach ($assistido as $key => $teste) {

                // Traz todos os encaminhamentos de Tratamento de cada atendimento e insere na variável total
                $trata = DB::table('encaminhamento AS enc')
                    ->select('tt.descricao AS tdt')
                    ->leftJoin('tipo_tratamento AS tt', 'enc.id_tipo_tratamento', 'tt.id')
                    ->where('enc.id_atendimento', $teste->ida)
                    ->whereNotNull('enc.id_tipo_tratamento')
                    ->get();
                $teste->tratamentos = $trata;

                // Traz todos os encaminhamentos de Entrevista de cada atendimento e insere na variável total
                $entre = DB::table('encaminhamento AS enc')
                    ->select('te.descricao AS tde')
                    ->leftJoin('tipo_entrevista AS te', 'enc.id_tipo_entrevista', 'te.id')
                    ->where('enc.id_atendimento', $teste->ida)
                    ->whereNotNull('enc.id_tipo_entrevista')
                    ->get();
                $teste->entrevistas = $entre;

                // Busca todas as temáticas de cada atendimento e insere na variável total
                $tematica = DB::table('registro_tema AS rt')
                    ->select('tt.nm_tca as tematica')
                    ->leftJoin('tipo_temas as  tt', 'rt.id_tematica', 'tt.id')
                    ->where('rt.id_atendimento', $teste->ida)
                    ->get();
                $teste->tematicas = $tematica;
            }

            // Traz o nome do grupo cujo o atendente de Apoio indicou para o Atendente na data de hoje
            $grupo = DB::table('atendente_dia AS atd')
                ->leftJoin('associado as a', 'atd.id_associado', '=', 'a.id')
                ->leftjoin('pessoas AS p', 'a.id_pessoa', 'p.id')
                ->leftJoin('tipo_status_pessoa AS tsp', 'p.status', 'tsp.id')
                ->leftJoin('salas AS s', 'atd.id_sala', 's.id')
                ->leftJoin('cronograma AS cro', 'atd.id_grupo', 'cro.id')
                ->leftJoin('grupo as g', 'cro.id_grupo', 'g.id')
                ->where('atd.id_associado', '=', $atendente)
                ->where('dh_inicio', '>=', $now)
                ->value('g.nome');

            return view('/atendimento-assistido/meus-atendimentos', compact('assistido', 'atendente', 'nome', 'grupo'));
        } catch (\Exception $e) {
            app('flasher')->addError("Houve um erro inesperado: #" . $e->getCode());
            DB::rollBack();
            return redirect()->back();
        }
    }
    public function tematica(Request $request, $idat)
    {

        // Atualiza a anotação criada pelo atendente na tabela de Atendimentos
        DB::table('atendimentos AS at')->where('id', $idat)->update([
            'observacao' => $request->input('nota')
        ]);

        // Inclui todas as temáticas marcadas
        if ($request->tematicas) { // IF necessário pois não é necessário que alguem marque os botões, e isso gera um bug
            foreach ($request->tematicas as $tematica) {
                DB::table('registro_tema AS rt')->insert([
                    'id_atendimento' => $idat,
                    'id_tematica' => $tematica,
                ]);
            }
        }

        app('flasher')->addSuccess('Os temas foram salvos com sucesso.');
        return Redirect('/atendendo');
    }

    // Função de Limpar na tela de Atendendo
    public function reset(string $idat)
    {
        try {

            DB::table('encaminhamento')->where('id_atendimento', $idat)->delete(); // Apaga todos os Tratamentos gerados
            DB::table('registro_tema')->where('id_atendimento', $idat)->delete(); //  Apaga todas as Entrevistas geradas
            DB::table('atendimentos')->where('id', $idat)->update([ // Limpa o campo de anotação do atendimento
                'observacao' => null
            ]);

            app('flasher')->addSuccess('Todos os dados foram apagados com sucesso!');
            return redirect()->back();
        } catch (\Exception $e) {
            app('flasher')->addError("Houve um erro inesperado: #" . $e->getCode());
            DB::rollBack();
            return redirect()->back();
        }
    }


    public function cancelar(Request $request, $id)
    {
        try {
            $dt_hora = Carbon::now();

            // Atualiza o status para cancelado, e adiciona o motivo do cancelamento
            DB::table('atendimentos AS a')
                ->where('id', '=', $id)
                ->update([
                    'status_atendimento' => 7, // Cancelado
                    'motivo' => $request->motivo
                ]);

            DB::table('encaminhamento')->where('id_atendimento', $id)->delete(); // Apaga Todos os Encaminhamentos Gerados
            DB::table('registro_tema')->where('id_atendimento', $id)->delete(); // Apaga todas as temáticas geradas
            DB::table('atendimentos')->where('id', $id)->update([ // Limpa o campo de anotação de atendimentos
                'observacao' => null
            ]);

            // Insere no histórico a criação do atendimento
            DB::table('log_atendimentos')->insert([
                'id_referencia' => $id,
                'id_usuario' => session()->get('usuario.id_usuario'),
                'id_acao' => 1, // mudou de Status para
                'id_observacao' => 7, // Cancelado
                'id_origem' => 1, // Atendimento
                'data_hora' => $dt_hora
            ]);

            app('flasher')->addSuccess('O status do atendimento foi alterado para "Cancelado".');
            return redirect('/atendendo');
        } catch (\Exception $e) {
            app('flasher')->addError('Houve um erro inesperado: #' . $e->getCode());
            DB::rollBack();
            return redirect()->back();
        }
    }
}
