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

    public function index(Request $request)
    {



        $atendente = session()->get('usuario.id_associado') ?? 0;

        $pref_m = session()->get('usuario.sexo');

        $nome = session()->get('usuario.nome');

        $now =  Carbon::now()->format('Y-m-d');

        $lista = DB::table('atendimentos AS at')
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
            ->leftJoin('salas as sl', 'at.id_sala', 'sl.id')->leftjoin('tipo_status_atendimento AS ts', 'at.status_atendimento', 'ts.id');

        $data_inicio = $request->input('dt_ini', Carbon::today()->toDateString());

        $motivo = DB::table('tipo_motivo_atendimento')->get();



        // $grupo = DB::table('atendente_dia AS ad')
        //     ->leftJoin('grupo AS g', 'ad.id_grupo', 'g.id')
        //     ->where('dh_inicio', '>=', $now)->where('ad.id_associado', $atendente)->value('g.nome');
        $grupo = DB::table('atendente_dia AS ad')
            ->leftJoin('cronograma as cro', 'cro.id', 'ad.id_grupo')
            ->leftJoin('grupo', 'grupo.id', 'cro.id_grupo')
            ->where('ad.id_associado',  $atendente)
            ->where('dh_inicio', '>', $data_inicio)
            ->whereNull('dh_fim')
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
            ->where('at.status_atendimento', '<', 6)
            ->where('at.afe',  null)
            ->where('at.id_atendente', $atendente)
            ->groupby('at.id', 'p1.id', 'p2.nome_completo', 'p3.nome_completo', 'p4.nome_completo', 'ts.descricao', 'tx.tipo', 'pa.nome', 'pr.descricao', 'pr.sigla')
            ->orderby('status_atendimento', 'ASC')
            ->get();



        //dd($assistido, $grupo, $now, $nome, $pref_m, $atendente);

        return view('/atendimento-assistido/atendendo', compact('assistido', 'atendente', 'now', 'nome', 'grupo', 'motivo'));
    }

    public function pessoas_para_atender()
    {

        $id_associado = session()->get('usuario.id_associado');
        $sexo = session()->get('usuario.sexo');
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
            ->whereDate('dh_chegada', Carbon::today()->toDateString())
            ->where('at.status_atendimento', '=', 2)
            ->where(function ($query) use ($id_associado) {
                $query->where('at.id_atendente_pref', '=',   $id_associado)
                    ->orWhereNull('at.id_atendente_pref'); // Inclui registros onde não há atendente preferencial
            })
            ->where(function ($query) use ($sexo) {
                $query->where('at.pref_tipo_atendente', '=',   $sexo)
                    ->orWhereNull('at.pref_tipo_atendente'); // Inclui registros onde não há atendente preferencial
            });


        $numero_de_assistidos_para_atender = $numero_de_assistidos_para_atender->count();


        return response()->json($numero_de_assistidos_para_atender);
    }

    public function atende_agora()
    {

        DB::beginTransaction();

        try {

            $now =  Carbon::today();
            $no =  Carbon::today()->addDay(1);
            $atendente = session()->get('usuario.id_associado');
            $pref_m = session()->get('usuario.sexo');

            //Conta todos os atendimentos do específico atendente, que não sejam AFE e não estejam finalizados
            $atendendo = DB::table('atendimentos AS at')
                ->leftjoin('membro AS m', 'at.id_atendente', 'm.id')
                ->leftjoin('associado AS a', 'm.id_associado', 'a.id')
                ->leftJoin('pessoas AS p', 'a.id_pessoa', 'p.id')
                ->where('at.id_atendente', $atendente)
                ->whereNull('afe')
                ->where('at.status_atendimento', '<', 6)
                ->count();
            //dd($atendendo);



            //Conta quantos atendimentos estão Aguardando Atendimento
            $atende = DB::table('atendimentos')
                ->where('status_atendimento', 2)
                ->whereNull('afe')
                ->whereNull('id_atendente_pref')
                ->whereNull('pref_tipo_atendente')
                ->pluck('id')
                ->toArray();


            //$atende = json_decode(json_encode($atende), true);

            $atende1 = DB::table('atendimentos')->where('status_atendimento', 2)
                ->whereNull('afe')
                ->where('id_atendente_pref', $atendente)
                ->pluck('id')
                ->toArray();

            // $atende1 = json_decode(json_encode($atende1), true);
            $atende2 = DB::table('atendimentos')->where('status_atendimento', 2)
                ->whereNull('afe')
                ->where('pref_tipo_atendente', $pref_m)
                ->pluck('id')
                ->toArray();
            //$atende2 = json_decode(json_encode($atende2), true);
            $atendeFinal = array_merge($atende, $atende1, $atende2);

            $assistido = count($atendeFinal);


            //traz os dados de atendente_dia, no intervalo entre o começo do dia de hoje e o fim de ontem, onde não estejam finalizados, para o atendente
            $sala = DB::table('atendente_dia AS atd')
                ->where('dh_inicio', '>', $now)
                ->where('dh_inicio', '<', $no)
                ->whereNull('dh_fim')
                ->where('id_associado', $atendente)
                ->value('id_sala');


            if ($atendendo > 0) {

                app('flasher')->addError('Você não pode atender dois assistidos ao mesmo tempo.');

                return redirect('/atendendo');
            } elseif ($assistido < 1) {

                app('flasher')->addError('Todos os assistidos foram atendidos.');

                return redirect('/atendendo');
            } elseif ($atendendo < 1 && $sala == null) {

                app('flasher')->addError('O atendente deve estar designado para o trabalho de hoje.');

                return redirect('/atendendo');
            } elseif ($atendendo < 1 && $sala > 0) {

                //Pega todos os atendimentos em ordem de status, prioridade e chegada, apenas um por vez, e troca o status para analisando e adiciona o atendente a ele


                //dd($atendente);
                $lixo = DB::table('atendimentos')
                    //->whereIn('id', $atendeFinal)
                    //->orderby('status_atendimento', 'ASC')->orderby('id_prioridade')->orderBy('dh_chegada')
                    //->limit(1)
                    //->whereNull('afe')
                    //->orWhereNot('afe')
                    ->where('status_atendimento', 2)
                    ->where(function ($query) {
                        $query->whereNull('afe')
                            ->orWhere('afe', false);
                    })
                    ->where(function ($query) use ($atendente) {
                        $query->whereNull('id_atendente_pref')
                            ->orWhere('id_atendente_pref', $atendente);
                    })
                    ->where(function ($query) use ($pref_m) {
                        $query->whereNull('pref_tipo_atendente')
                            ->orWhere('pref_tipo_atendente', $pref_m);
                    })
                    ->orderby('id_prioridade')->orderBy('dh_chegada')
                    ->limit(1)
                    //->get();
                    ->update([
                        'id_atendente' => $atendente,
                        'id_sala' => $sala,
                        'status_atendimento' => 4
                    ]);

                //dd($lixo);

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

            $atendimentos = DB::table('atendimentos AS at')->where('id_assistido', $idas)->get('id');
            //dd($atendimentos);


            $analisa = DB::table('atendimentos AS at')
                ->select('at.id AS ida', 'at.observacao', 'p1.id AS idas', 'p1.ddd', 'p1.sexo', 'p1.celular', 'at.dh_chegada', 'at.dh_inicio', 'at.dh_fim', 'at.id_assistido', 'p1.nome_completo AS nm_1', 'at.id_representante', 'p2.nome_completo AS nm_2', 'at.id_atendente_pref', 'ps1.nome_completo AS nm_3', 'at.id_atendente', 'ps2.nome_completo AS nm_4', 'at.pref_tipo_atendente', 'ts.descricao AS tst', 'tsx.tipo', 'pa.nome', 'p1.dt_nascimento')
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

            //Pega uma variável e popula com dados de duas tabelas diferentes
            foreach ($analisa as $key => $teste) {
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


            //dd($analisa);
            $now = Carbon::now()->format('Y-m-d H:m:s');

            $atendente = session()->get('usuario.id_associado');

            $nome = DB::table('atendimentos AS at')->select('at.id_atendente')->where('at.id', $idat);

            $grupo = DB::table('atendente_dia AS ad')->select('ad.id_grupo')->where('dh_inicio', '>=', $now)->where('ad.id_associado', $atendente);

            $atendendo = DB::table('atendimentos AS at')->where('at.id', $idat)->value('id_atendente');
            //dd($atendendo);
            $status = DB::table('atendimentos AS at')->where('at.id', $idat)->value('status_atendimento');

            $sit = DB::table('atendimentos AS at')->where('at.id_atendente', $atendente)->where('at.status_atendimento', '<', 5)->count();


            if ($sit > 0 && $atendendo == null) {
                app('flasher')->addError('Não é permitido atender dois assistidos ao mesmo tempo.');

                return redirect('/atendendo');
            }
            //99% dos casos
            if ($atendendo = $atendente && $status > 2) {
                app('flasher')->addInfo('Retomando análise.');

                return view('/atendimento-assistido/historico-assistido', compact('atendente', 'analisa'));
            }
            //Caso inútil, já que é impossivel ter um atendente para um atendimento com atendente em status 1, mas precaução
            if ($atendendo = $atendente && $status = 1) {
                DB::table('atendimentos AS at')
                    ->where('status_atendimento', '=', 2)
                    ->where('at.id', $idat)
                    ->update([
                        'status_atendimento' => 4,
                        'id_atendente' => $atendente
                    ]);

                app('flasher')->addSuccess('O status do atendimento foi alterado para em análise.');
            }

            return view('/atendimento-assistido/historico-assistido', compact('atendente', 'analisa', 'grupo'));
        } catch (\Exception $e) {

            app('flasher')->addError("Houve um erro inesperado: #" . $e->getCode());
            return redirect()->back();
        }
    }
    //dd($assistido);

    //Botão Chamar Assistido na VIEW
    public function fimanalise($idat)
    {

        DB::beginTransaction();
        try {

            $atendente = session()->get('usuario.id_associado');

            $sit = DB::table('atendimentos AS at')
                ->where('at.id', $idat)
                ->where('at.status_atendimento', 4)
                ->count();

            $status =  DB::table('atendimentos AS at')
                ->where('at.id', $idat)
                ->value('status_atendimento');

            if ($status > 4) {

                app('flasher')->addError('Esta ação não pode ser executada, este status já foi ultrapassado.');

                return redirect()->back();
            }
            if ($sit = 1) {
                //Atualiza o status para Aguardando Assistido, atualizar o id_atendente não muda nada,
                // logo que o usuário só ve atendimentos dele, atualizando sempre pro mesmo valor original
                DB::table('atendimentos AS at')
                    ->where('status_atendimento', '=', 4)
                    ->where('at.id', $idat)
                    ->update([
                        'status_atendimento' => 1,
                        'id_atendente' => $atendente
                    ]);
            }

            app('flasher')->addSuccess('O status do atendimento foi alterado para "Aguardando o assistido".');

            DB::commit();
            return redirect()->back();
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

            $atendente = session()->get('usuario.id_associado');
        

            if (DB::table('atendimentos AS at')->where('at.id', $idat)->value('status_atendimento') > 4) {

                app('flasher')->addError('O início do atendimento já foi registrado.');

                return redirect()->back();
            }
            // Troca o Status do Atendimento para Em Atendimento
            elseif (DB::table('atendimentos AS at')->where('at.id', $idat)->value('status_atendimento') < 5) {

                DB::table('atendimentos AS at')
                    ->where('at.id', $idat)
                    ->update([
                        'status_atendimento' => 5,
                        'dh_inicio' => $now
                    ]);

                app('flasher')->addSuccess('O status do atendimento foi alterado para "Em atendimento".');

                DB::commit();
                return redirect()->back();
            }
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

            $sit = DB::table('atendimentos AS at')
                ->where('at.id', $idat)
                ->where('status_atendimento', '<', 5)
                ->count();

            $atendido = DB::table('pessoas AS p')
                ->select('nome_completo AS nm')
                ->where('p.id', $idas)
                ->get();

            // Confere se tem algum encaminhamento de tratamento já criado
            $verifi = DB::table('encaminhamento AS enc')
                ->leftJoin('atendimentos AS at', 'enc.id_atendimento', 'at.id')
                ->where('at.id', $idat)
                ->where('id_tipo_encaminhamento', [2, 3])
                ->where('id_tipo_tratamento', '<>', 1)
                ->count();

            if ($sit > 0) {

                app('flasher')->addError('Para registrar encaminhamentos o atendimento deve estar no mínimo no status "Em atendimento"');

                return redirect()->back();
            }
            if ($verifi < 1) {

                $assistido = DB::table('atendimentos AS at')
                    ->select('at.id as idat', 'at.id_assistido as idas', 'at.dh_chegada', 'at.dh_inicio', 'at.dh_fim', 'at.id_assistido', 'p1.nome_completo AS nm_1', 'at.id_representante', 'at.id_atendente')
                    ->leftJoin('pessoas AS p1', 'at.id_assistido', 'p1.id')
                    ->where('at.id', $idat)
                    ->get();

                return view('/atendimento-assistido/tratamentos', compact('assistido'));

                //Se já tiver encaminhamentos de tratamento, trava para nao reinserir dados
            } elseif ($verifi > 0) {

                app('flasher')->addError('Os tratamentos já foram registrados para o atendido ' . $atendido[0]->nm);

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
            if(in_array(1, $countEncaminhamentos) and $desobsessivo > 0){
                app('flasher')->addWarning('Já existe um encaminhamento PTD ativo para esta pessoa!');
            }else if($desobsessivo > 0){
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
            if($harmonia > 0 and in_array(3, $countEncaminhamentos)){
                app('flasher')->addWarning('Já existe um encaminhamento para o PTH ativo para esta pessoa!');
            }else if($harmonia > 0){
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
            if($acolher > 0 and in_array(7, $countEncaminhamentos)){
                app('flasher')->addWarning('Já existe um encaminhamento para o Grupo Acolher ativo para esta pessoa!');
            }else if($acolher > 0){
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
            if($quimica > 0 and in_array(9, $countEncaminhamentos)){
                app('flasher')->addWarning('Já existe um encaminhamento para o Grupo de Dependência Química ativo para esta pessoa!');
            }else if($quimica > 0){
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
            if($viver > 0 and in_array(10, $countEncaminhamentos)){
                app('flasher')->addWarning('Já existe um encaminhamento para o Grupo Viver ativo para esta pessoa!');
            }else if($viver > 0){
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
            if($ame > 0 and in_array(5, $countEntrevistas)){
                app('flasher')->addWarning('Já existe um encaminhamento para o  Integral ativo para esta pessoa!');
            }else if($ame > 0 and in_array(1, $countTratamentos)){
                
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
               foreach($encaminhamentoPTD as $ptd){
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
            }else if($ame > 0){
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



            if($diamo > 0 and in_array(6, $countEntrevistas)){
                app('flasher')->addWarning('Já existe um encaminhamento para o Proamo ativo para esta pessoa!');
            }else if($diamo > 0){
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


            if($nutres > 0 and in_array(4, $countEntrevistas)){
                app('flasher')->addWarning('Já existe um encaminhamento para o PTI ativo para esta pessoa!');
            }else if($nutres > 0 and in_array(1, $countTratamentos)){

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

            }else if($nutres > 0 ){
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

            if($evangelho > 0 and in_array(8, $countEntrevistas)){
                app('flasher')->addWarning('Já existe um encaminhamento para o Grupo de Evangelho no Lar ativo para esta pessoa!');
            }else if($evangelho > 0){
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