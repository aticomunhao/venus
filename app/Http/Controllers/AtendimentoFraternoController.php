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

        DB::beginTransaction();

        try {

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

                // Atualiza os atendimentos para o Atendente
                DB::table('atendimentos')
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
                    ->limit(1) // Traz apenas um por vez
                    ->update([
                        'id_atendente' => $atendente, // Marca o usuário logado como atendente deste atendimento
                        'id_sala' => $sala, // Marca a sala que o usuário logado está
                        'status_atendimento' => 4 // Troca o status do atendimento para Analisando
                    ]);

                app('flasher')->addSuccess('O assistido foi selecionado com sucesso.');
                DB::commit();
                return redirect('/atendendo');
            }
        } catch (\Exception $e) {

            app('flasher')->addError("Houve um erro inesperado: #" . $e->getCode());
            DB::rollBack();
            return redirect()->back();
        }
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

    //Método CREATE do BotãoTratamento
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

                return view('/atendimento-assistido/tratamentos', compact('assistido'));
            }
            elseif ($verifi > 0) { // Se tiver algum encaminhamento
                app('flasher')->addError('Tratamentos já rcriados! Limpe para recriá-los!');
                return redirect()->back();

            } elseif ($sit != 1) { // Se o status não for Em Atendimento
                app('flasher')->addError('O assistido deve estar "Em atendimento" para a marcação de tratamentos!');
                return redirect()->back();
            }
        } catch (\Exception $e) {

            app('flasher')->addError("Houve um erro inesperado: #" . $e->getCode());
            return redirect()->back();
        }
    }

    public function entrevistar($idat, $idas)
    {

        try {

            $sit = DB::table('atendimentos AS at')
                ->where('at.id', $idat)
                ->where('status_atendimento', '<', 5)
                ->count();

            $atendido = DB::table('pessoas AS p')
                ->select('nome_completo AS nm', 'p.id AS idas')
                ->where('p.id', $idas)
                ->get();



            $verifi = DB::table('encaminhamento AS enc')
                ->leftJoin('atendimentos AS at', 'enc.id_atendimento', 'at.id')
                ->where('at.id', $idat)
                ->where('id_tipo_encaminhamento', [1])
                ->count();
            //dd($verifi);

            if ($sit > 0) {

                app('flasher')->addError('Para registrar encaminhamentos o atendimento deve estar no mínimo no status "Em atendimento"');

                return redirect()->back();
            }
            if ($verifi < 1) {

                $assistido = DB::table('atendimentos AS at')
                    ->select('at.id as idat', 'at.dh_chegada', 'at.dh_inicio', 'at.dh_fim', 'at.id_assistido', 'p1.nome_completo AS nm_1', 'at.id_representante', 'at.id_atendente')
                    ->leftJoin('pessoas AS p1', 'at.id_assistido', 'p1.id')
                    ->where('at.id', $idat)
                    ->get();

                // dd($atendido);

                return view('/atendimento-assistido/entrevistas', compact('assistido', 'atendido'));
            } elseif ($verifi > 0) {

                app('flasher')->addError('As entrevistas já foram registradas para o atendido ' . $atendido[0]->nm);

                return redirect()->back();
            }

            DB::commit();
        } catch (\Exception $e) {

            app('flasher')->addError("Houve um erro inesperado: #" . $e->getCode());
            DB::rollBack();
            return redirect()->back();
        }
    }

    public function final($idat)
    {

        try {

            $sit = DB::table('atendimentos AS at')->where('at.id', $idat)->where('status_atendimento', '<', 5)->count();


            if ($sit > 0) {

                app('flasher')->addError('Para finalizar o atendimento o status mínimo é "Em atendimento"');

                return redirect()->back();
            } else {

                $assistido = DB::table('atendimentos AS at')
                    ->select('at.id as idat', 'at.dh_chegada', 'at.dh_inicio', 'at.dh_fim', 'at.id_assistido', 'p1.nome_completo AS nm_1', 'at.id_representante', 'at.id_atendente')
                    ->leftJoin('pessoas AS p1', 'at.id_assistido', 'p1.id')
                    ->where('at.id', $idat)
                    ->get();

                return view('/atendimento-assistido/finalizar', compact('assistido'));
            }
        } catch (\Exception $e) {


            app('flasher')->addError("Houve um erro inesperado: #" . $e->getCode());
            DB::rollBack();
            return redirect()->back();
        }
    }

    public function pre_tema($idat)
    {

        $r_tema = DB::table('registro_tema')->where('id_atendimento', $idat)->count();
        $nota = DB::table('atendimentos')->where('id', $idat)->first();


        // dd($ies, $obs, $coj);




        if ($r_tema > 0 or $nota->observacao != null) {

            app('flasher')->addError("As temáticas do atendimento $idat já foram registradas.");

            return Redirect('/atendendo');
        } else {

            $verifi =  $result = DB::table('registro_tema AS rt')
                ->leftJoin('atendimentos AS at', 'rt.id_atendimento', 'at.id')
                ->where('at.id', $idat)->count();

            $assistido = DB::table('atendimentos AS at')
                ->select('at.id as idat', 'at.dh_chegada', 'at.dh_inicio', 'at.dh_fim', 'at.id_assistido', 'p1.nome_completo AS nm_1', 'at.id_representante', 'at.id_atendente')
                ->leftJoin('pessoas AS p1', 'at.id_assistido', 'p1.id')
                ->where('at.id', $idat)
                ->get();
        }

        return view('/atendimento-assistido/tematicas', compact('assistido', 'verifi'));
    }

    public function enc_trat(Request $request, $idat, $idas)
    {

        try {

            $now = Carbon::now()->format('Y-m-d H:m:s');

            $atendente = session()->get('usuario.id_associado');

            $marcados = DB::table('encaminhamento AS enc')
                ->leftJoin('atendimentos AS at', 'enc.id_atendimento', 'at.id')
                ->where('at.id', $idat)->select('*')->first();

            $harmonia = isset($request->pph) ? 1 : 0;
            $desobsessivo = isset($request->ptd) ? 1 : 0;
            // $integral = isset($request->ptig) ? 1 : 0;
            // $intensivo = isset($request->pti) ? 1 : 0;
            $acolher = isset($request->ga) ? 1 : 0;
            $viver = isset($request->gv) ? 1 : 0;
            //$evangelho = isset($request->gel) ? 1 : 0;
            $quimica = isset($request->gdq) ? 1 : 0;
            //dd($harmonia, $desobsessivo, $integral);

            $atendido = DB::table('pessoas AS p')
                ->select('nome_completo AS nm')
                ->where('p.id', $idas)
                ->get();


            $countEncaminhamentos = DB::table('encaminhamento as enc')
                ->leftJoin('atendimentos as at', 'enc.id_atendimento', 'at.id')
                ->where('enc.id_tipo_encaminhamento', 2)
                ->where('at.id_assistido', $idas)
                ->where('enc.status_encaminhamento', '<', 5)
                ->pluck('id_tipo_tratamento')->toArray();

            // dd($countEncaminhamentos, in_array(1, $countEncaminhamentos));

            // PTD
            if (in_array(1, $countEncaminhamentos) and $desobsessivo > 0) {
                app('flasher')->addWarning('Já existe um encaminhamento PTD ativo para esta pessoa!');
            } else if ($desobsessivo > 0) {
                DB::table('encaminhamento AS enc')->insert([
                    'dh_enc' => $now,
                    'id_usuario' => $atendente,
                    'id_tipo_encaminhamento' => 2,
                    'id_atendimento' => $idat,
                    'id_tipo_tratamento' => 1,
                    'status_encaminhamento' =>  1
                ]);

                app('flasher')->addSuccess('O encaminhamento para PTD foi criado com sucesso.');
            }

            // PTH
            if ($harmonia > 0 and in_array(3, $countEncaminhamentos)) {
                app('flasher')->addWarning('Já existe um encaminhamento para o PTH ativo para esta pessoa!');
            } else if ($harmonia > 0) {
                DB::table('encaminhamento AS enc')->insert([
                    'dh_enc' => $now,
                    'id_usuario' => $atendente,
                    'id_tipo_encaminhamento' => 2,
                    'id_atendimento' => $idat,
                    'id_tipo_tratamento' => 3,
                    'status_encaminhamento' =>  3
                ]);

                app('flasher')->addSuccess('O encaminhamento para Grupo de Harmonização foi criado com sucesso.');
            }

            // Acolher
            if ($acolher > 0 and in_array(7, $countEncaminhamentos)) {
                app('flasher')->addWarning('Já existe um encaminhamento para o Grupo Acolher ativo para esta pessoa!');
            } else if ($acolher > 0) {
                DB::table('encaminhamento AS enc')->insert([
                    'dh_enc' => $now,
                    'id_usuario' => $atendente,
                    'id_tipo_encaminhamento' => 3,
                    'id_atendimento' => $idat,
                    'id_tipo_tratamento' => 7,
                    'status_encaminhamento' =>  3
                ]);

                app('flasher')->addSuccess('O encaminhamento para Grupo Acolher foi criado com sucesso.');
            }


            // Dependência Quimica
            if ($quimica > 0 and in_array(9, $countEncaminhamentos)) {
                app('flasher')->addWarning('Já existe um encaminhamento para o Grupo de Dependência Química ativo para esta pessoa!');
            } else if ($quimica > 0) {
                DB::table('encaminhamento AS enc')->insert([
                    'dh_enc' => $now,
                    'id_usuario' => $atendente,
                    'id_tipo_encaminhamento' => 3,
                    'id_atendimento' => $idat,
                    'id_tipo_tratamento' => 9,
                    'status_encaminhamento' =>  3
                ]);

                app('flasher')->addSuccess('O encaminhamento para Grupo de Dependência Química foi criado com sucesso.');
            }

            //Viver
            if ($viver > 0 and in_array(10, $countEncaminhamentos)) {
                app('flasher')->addWarning('Já existe um encaminhamento para o Grupo Viver ativo para esta pessoa!');
            } else if ($viver > 0) {
                DB::table('encaminhamento AS enc')->insert([
                    'dh_enc' => $now,
                    'id_usuario' => $atendente,
                    'id_tipo_encaminhamento' => 3,
                    'id_atendimento' => $idat,
                    'id_tipo_tratamento' => 10,
                    'status_encaminhamento' =>  3
                ]);

                app('flasher')->addSuccess('O encaminhamento para Grupo Viver foi criado com sucesso.');
            }


            return Redirect('/atendendo');
        } catch (\Exception $e) {


            app('flasher')->addError("Houve um erro inesperado: #" . $e->getCode());
            DB::rollBack();
            return redirect()->back();
        }
    }

    public function enc_entre(Request $request, $idat, String $idas)
    {

        // try {

        $now = Carbon::now()->format('Y-m-d H:m:s');

        $atendente = session()->get('usuario.id_pessoa');

        $ame = isset($request->ame) ? 1 : 0;
        $afe = isset($request->afe) ? 1 : 0;
        $diamo = isset($request->diamo) ? 1 : 0;
        $nutres = isset($request->nutres) ? 1 : 0;
        $evangelho = isset($request->gel) ? 1 : 0;

        //  dd($ame, $afe, $diamo, $nutres  );

        $countTratamentos = DB::table('encaminhamento as enc')
            ->leftJoin('atendimentos as at', 'enc.id_atendimento', 'at.id')
            ->where('enc.id_tipo_encaminhamento', 2)
            ->where('at.id_assistido', $idas)
            ->where('enc.status_encaminhamento', '<', 5)
            ->pluck('id_tipo_tratamento')->toArray();

        $countEntrevistas = DB::table('encaminhamento as enc')
            ->leftJoin('atendimentos as at', 'enc.id_atendimento', 'at.id')
            ->where('enc.id_tipo_encaminhamento', 1)
            ->where('at.id_assistido', $idas)
            ->where('enc.status_encaminhamento', '<', 5)
            ->pluck('id_tipo_entrevista')->toArray();


        //AME
        if ($ame > 0 and in_array(5, $countEntrevistas)) {
            app('flasher')->addWarning('Já existe um encaminhamento para o  Integral ativo para esta pessoa!');
        } else if ($ame > 0 and in_array(1, $countTratamentos)) {

            // Traz todos os encaminhamentos em tratamento daquela pessoa
            $encaminhamentoPTD = DB::table('tratamento as tr')
                ->leftJoin('encaminhamento as enc', 'tr.id_encaminhamento', 'enc.id')
                ->leftJoin('atendimentos as at', 'enc.id_atendimento', 'at.id')
                ->where('enc.id_tipo_tratamento', 1)
                ->where('at.id_assistido', $idas)
                ->where('tr.status', '<', 3)
                ->whereNot('tr.dt_fim', null)
                ->select('tr.id', 'tr.dt_fim')
                ->get();

            // Atualiza com mais 8 semanas todos os PTDs Ativos
            foreach ($encaminhamentoPTD as $ptd) {
                $data = Carbon::parse($ptd->dt_fim)->addWeek(8);
                DB::table('tratamento')
                    ->where('id', $ptd->id)
                    ->update([
                        'dt_fim' => $data
                    ]);
            }

            // Insere a entrevista AME
            DB::table('encaminhamento AS enc')->insert([
                'dh_enc' => $now,
                'id_usuario' => $atendente,
                'id_tipo_encaminhamento' => 1,
                'id_atendimento' => $idat,
                'id_tipo_entrevista' => 5,
                'status_encaminhamento' =>  1
            ]);

            app('flasher')->addSuccess('O encaminhamento para AME foi criado com sucesso.');
        } else if ($ame > 0) {
            //Insere entrevista AME
            DB::table('encaminhamento AS enc')->insert([
                'dh_enc' => $now,
                'id_usuario' => $atendente,
                'id_tipo_encaminhamento' => 1,
                'id_atendimento' => $idat,
                'id_tipo_entrevista' => 5,
                'status_encaminhamento' =>  1
            ]);

            DB::table('encaminhamento AS enc')->insert([
                'dh_enc' => $now,
                'id_usuario' => $atendente,
                'id_tipo_encaminhamento' => 2,
                'id_atendimento' => $idat,
                'id_tipo_tratamento' => 1,
                'status_encaminhamento' =>  1
            ]);

            app('flasher')->addSuccess('O encaminhamento para AME e PTD  foi criado com sucesso.');
        }



        if ($diamo > 0 and in_array(6, $countEntrevistas)) {
            app('flasher')->addWarning('Já existe um encaminhamento para o Proamo ativo para esta pessoa!');
        } else if ($diamo > 0) {
            //Inserir Diamo na tabela
            DB::table('encaminhamento AS enc')->insert([
                'dh_enc' => $now,
                'id_usuario' => $atendente,
                'id_tipo_encaminhamento' => 1,
                'id_atendimento' => $idat,
                'id_tipo_entrevista' => 6,
                'status_encaminhamento' =>  1
            ]);

            app('flasher')->addSuccess('O encaminhamento para o Proamo foi criado com sucesso.');
        }


        if ($nutres > 0 and in_array(4, $countEntrevistas)) {
            app('flasher')->addWarning('Já existe um encaminhamento para o PTI ativo para esta pessoa!');
        } else if ($nutres > 0 and in_array(1, $countTratamentos)) {

            // Todos aguardando para Aguardando PTI
            DB::table('encaminhamento as enc')
                ->leftJoin('atendimentos as at', 'enc.id_atendimento', 'at.id')
                ->where('enc.id_tipo_encaminhamento', 2)
                ->where('at.id_assistido', $idas)
                ->where('enc.status_encaminhamento', 1)
                ->update([
                    'status_encaminhamento' => 2
                ]);


            // Todos agendado para Agendado PTI
            DB::table('encaminhamento as enc')
                ->leftJoin('atendimentos as at', 'enc.id_atendimento', 'at.id')
                ->where('enc.id_tipo_encaminhamento', 2)
                ->where('at.id_assistido', $idas)
                ->where('enc.status_encaminhamento', 3)
                ->update([
                    'status_encaminhamento' => 4
                ]);


            DB::table('encaminhamento AS enc')->insert([
                'dh_enc' => $now,
                'id_usuario' => $atendente,
                'id_tipo_encaminhamento' => 1,
                'id_atendimento' => $idat,
                'id_tipo_entrevista' => 4,
                'status_encaminhamento' =>  1
            ]);

            app('flasher')->addSuccess('O encaminhamento para o PTI foi criado com sucesso.');
        } else if ($nutres > 0) {
            DB::table('encaminhamento AS enc')->insert([
                'dh_enc' => $now,
                'id_usuario' => $atendente,
                'id_tipo_encaminhamento' => 1,
                'id_atendimento' => $idat,
                'id_tipo_entrevista' => 4,
                'status_encaminhamento' =>  1
            ]);

            DB::table('encaminhamento AS enc')->insert([
                'dh_enc' => $now,
                'id_usuario' => $atendente,
                'id_tipo_encaminhamento' => 2,
                'id_atendimento' => $idat,
                'id_tipo_tratamento' => 1,
                'status_encaminhamento' =>  2
            ]);

            app('flasher')->addSuccess('O encaminhamento para o PTI e PTD foi criado com sucesso.');
        }

        if ($evangelho > 0 and in_array(8, $countEntrevistas)) {
            app('flasher')->addWarning('Já existe um encaminhamento para o Grupo de Evangelho no Lar ativo para esta pessoa!');
        } else if ($evangelho > 0) {
            DB::table('encaminhamento AS enc')->insert([
                'dh_enc' => $now,
                'id_usuario' => $atendente,
                'id_tipo_encaminhamento' => 1,
                'id_atendimento' => $idat,
                'id_tipo_entrevista' => 8,
                'status_encaminhamento' =>  1
            ]);

            app('flasher')->addSuccess('O encaminhamento para o Grupo de Evangelho no Lar foi criado com sucesso.');
        }

        return Redirect('/atendendo');
        // } catch (\Exception $e) {


        //     app('flasher')->addError("Houve um erro inesperado: #" . $e->getCode());
        //     DB::rollBack();
        //     return redirect()->back();
        // }
    }

    public function finaliza(Request $request, $idat)
    {


        try {

            $now = Carbon::now();
            $emergencia = $request->emergencia == 'on' ? 1 : 0;
            $atendente = session()->get('usuario.id_associado');

            // $sit = DB::table('atendimentos AS at')->where('at.id_atendente', $atendente)->where('at.status_atendimento', '<', 5)->count();

            $atendendo = DB::table('atendimentos AS at')->where('at.id', $idat)->value('id_atendente');

            $status =  DB::table('atendimentos AS at')->where('at.id', $idat)->value('status_atendimento');

            if ($status = 5 && $atendendo <> $atendente) {

                app('flasher')->addError('Este atendimento não é sua responsabilidade.');

                return redirect('/atendendo');
            } elseif ($status = 5 && $atendendo = $atendente) {
                DB::table('atendimentos AS at')
                    ->where('status_atendimento', '=', 5)
                    ->where('at.id', $idat)
                    ->update([
                        'status_atendimento' => 6,
                        'id_atendente' => $atendente,
                        'dh_fim' => $now,
                        'emergencia' => $emergencia
                    ]);
            }

            app('flasher')->addSuccess('O status do atendimento foi alterado para "Finalizado".');

            return redirect('/atendendo');
        } catch (\Exception $e) {

            app('flasher')->addError("Houve um erro inesperado: #" . $e->getCode());
            DB::rollBack();
            return redirect()->back();
        }
    }

    public function meus_atendimentos()
    {

        try {

            $atendente = session()->get('usuario.id_associado');

            $nome = session()->get('usuario.nome');

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
                ->where('id_atendente', $atendente)
                ->distinct('at.dh_chegada')
                ->orderBy('at.dh_chegada', 'desc')
                ->get();

            foreach ($assistido as $key => $teste) {
                $trata = DB::table('encaminhamento AS enc')
                    ->select('tt.descricao AS tdt')
                    ->leftJoin('tipo_tratamento AS tt', 'enc.id_tipo_tratamento', 'tt.id')
                    ->where('enc.id_atendimento', $teste->ida)
                    ->whereNotNull('enc.id_tipo_tratamento')
                    ->get();
                $teste->tratamentos = $trata;

                $entre = DB::table('encaminhamento AS enc')
                    ->select('te.descricao AS tde')
                    ->leftJoin('tipo_entrevista AS te', 'enc.id_tipo_entrevista', 'te.id')
                    ->where('enc.id_atendimento', $teste->ida)
                    ->whereNotNull('enc.id_tipo_entrevista')
                    ->get();
                $teste->entrevistas = $entre;

                $tematica = DB::table('registro_tema AS rt')
                    ->select('tt.nm_tca as tematica')
                    ->leftJoin('tipo_temas as  tt', 'rt.id_tematica', 'tt.id')
                    ->where('rt.id_atendimento', $teste->ida)
                    ->get();
                $teste->tematicas = $tematica;
            }



            $now = Carbon::now()->format('Y-m-d');

            // $grupo = DB::table('atendente_dia AS ad')
            // ->leftJoin('associado as a', 'atd.id_associado', '=', 'a.id')
            // ->leftjoin('pessoas AS p', 'a.id_pessoa', 'p.id')
            // ->leftJoin('tipo_status_pessoa AS tsp', 'p.status', 'tsp.id')
            // ->leftJoin('salas AS s', 'atd.id_sala', 's.id')
            // ->leftJoin('cronograma AS cro', 'atd.id_grupo', 'cro.id')
            // ->leftJoin('grupo as g', 'cro.id_grupo', 'g.id')
            //     ->where('dh_inicio', '>=', $now)->where('ad.id_associado', $atendente)->value('g.nome');
            $grupo = DB::table('atendente_dia AS atd')
                ->leftJoin('associado as a', 'atd.id_associado', '=', 'a.id')
                ->leftjoin('pessoas AS p', 'a.id_pessoa', 'p.id')
                ->leftJoin('tipo_status_pessoa AS tsp', 'p.status', 'tsp.id')
                ->leftJoin('salas AS s', 'atd.id_sala', 's.id')
                ->leftJoin('cronograma AS cro', 'atd.id_grupo', 'cro.id')
                ->leftJoin('grupo as g', 'cro.id_grupo', 'g.id')->where('atd.id_associado', '=', $atendente)->where('dh_inicio', '>=', $now)->value('g.nome');

            // dd($grupo);





            return view('/atendimento-assistido/meus-atendimentos', compact('assistido', 'atendente', 'nome', 'grupo'));
        } catch (\Exception $e) {

            app('flasher')->addError("Houve um erro inesperado: #" . $e->getCode());
            DB::rollBack();
            return redirect()->back();
        }
    }
    public function tematica(Request $request, $idat)
    {

        // dd($request->all());


        DB::table('atendimentos AS at')->where('id', $idat)->update([
            'observacao' => $request->input('nota')
        ]);

        if ($request->tematicas) {
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



    public function reset(string $idat)
    {
        try {

            DB::table('encaminhamento')->where('id_atendimento', $idat)->delete();

            DB::table('registro_tema')->where('id_atendimento', $idat)->delete();

            $c = DB::table('atendimentos')->where('id', $idat)->update([
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

    public function encaminhamentos_tematicas(String $id)
    {

        $return =  new stdClass();

        $return->encaminhamentos = DB::table('encaminhamento')->where('id_atendimento', $id)->count();
        $return->tematicas = DB::table('registro_tema')->where('id_atendimento', $id)->count();



        return $return;
    }

    public function cancelar(Request $request, $id)
    {
        try {
            DB::table('atendimentos AS a')
                ->where('id', '=', $id)
                ->update([
                    'status_atendimento' => 7,
                    'motivo' => $request->motivo
                ]);

            DB::table('encaminhamento')->where('id_atendimento', $id)->delete();

            DB::table('registro_tema')->where('id_atendimento', $id)->delete();

            $c = DB::table('atendimentos')->where('id', $id)->update([
                'observacao' => null
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
