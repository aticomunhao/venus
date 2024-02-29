<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Carbon;


use function Laravel\Prompts\select;

class ReuniaoMediunicaController extends Controller
{

        public function index(Request $request){

            $now =  Carbon::now()->format('Y-m-d');


            $reuniao = DB::table('cronograma AS cro')
                        ->select('cro.id AS idr', 'gr.nome AS nomeg', 'cro.dia AS idd', 'cro.dia', 'cro.id_sala', 'cro.id_tipo_tratamento', 'cro.id_tipo_tratamento', 'cro.h_inicio','td.nome AS nomed', 'cro.h_fim', 'cro.max_atend', 'gr.status_grupo AS idst', 'tsg.descricao as descst', 'tst.descricao AS tstd', 'sa.numero' )
                        ->leftJoin('tipo_tratamento AS tst', 'cro.id_tipo_tratamento', 'tst.id')
                        ->leftjoin('grupo AS gr', 'cro.id_grupo', 'gr.id')
                        ->leftjoin('tipo_status_grupo AS tsg', 'gr.status_grupo', 'tsg.id')
                        ->leftJoin('medium AS me', 'gr.id', 'me.id_grupo')
                        ->leftJoin('salas AS sa', 'cro.id_sala', 'sa.id')
                        ->leftJoin('tipo_dia AS td', 'cro.dia', 'td.id');


            $semana = $request->semana;

            $grupo = $request->grupo;

            $status = $request->status;


            if ($request->semana){
                $reuniao->where('cro.dia', '=', $request->semana);
            }

            if ($request->grupo){
                $reuniao->where('gr.nome', 'ilike', "%$request->grupo%");
            }

            if ($request->status){
                $reuniao->where('tsg.id', '=', $request->status);
            }

            $reuniao = $reuniao->orderby('gr.status_grupo', 'ASC')->orderby('cro.id_tipo_tratamento', 'ASC')->paginate(50);

             //dd($request->semana);
              //dd($status);

            $contar = $reuniao->count('cro.id');

            $situacao = DB::table('tipo_status_grupo')->select('id AS ids', 'descricao AS descs')->get();

            $tpdia = DB::table('tipo_dia')->select('id AS idtd', 'nome AS nomed')->get();
            dd($tpdia);
         // dd($tpdia);


            return view('/reuniao-mediunica/gerenciar-reunioes', compact('reuniao', 'tpdia', 'situacao', 'status', 'contar', 'semana', 'grupo'));


        }

        public function create(){

            $sala = DB::table('salas AS sl')
                        ->select('sl.id AS ids', 'sl.nome', 'sl.numero', 'sl.nr_lugares')
                        ->where('id_finalidade', 6)
                        ->orderBy('numero', 'asc')
                        ->get();

            $grupo = DB::table('grupo AS gr')
                        ->select('gr.id AS idg', 'gr.nome', 'gr.id_tipo_grupo')
                        ->where('id_tipo_grupo', 1)
                        ->orderBy('gr.nome')
                        ->get();

            $tipo = DB::table('tipo_grupo AS tg')
                        ->select('tg.id AS idtg', 'tg.nm_tipo_grupo')
                        ->get();

            $tratamento = DB::table('tipo_tratamento AS tt')
                        ->select('tt.id AS idt', 'tt.descricao', 'tt.sigla')
                        ->get();

            $dia = DB::table('tipo_dia AS td')
                        ->select('td.id AS idd', 'td.nome', 'td.sigla')
                        ->get();



            return view ('/reuniao-mediunica/criar-reuniao', compact('sala', 'grupo', 'tipo',  'tratamento',  'dia'));


        }

        public function store(Request $request){

            $usuario = session()->get('usuario.id_pessoa');

            $now =  Carbon::now()->format('Y-m-d');

            $grupo = intval($request->grupo);
            $numero = intval($request->numero);
            $h_inicio = $request->h_inicio;
            $h_fim = $request->h_fim;
            $dia = intval($request->dia);

            // $arrayDeTempos =  DB::table('cronograma AS rm')
            // ->select('h_fim')->pluck('h_fim')->toArray();

            // $arrayDeTemposAtualizados = array_map(function ($tempo) {
            //     $carbonTempo = Carbon::parse($tempo);
            //     return $carbonTempo->addMinutes(30)->toTimeString(); // Convertendo de volta para string
            // }, $arrayDeTempos);

            // // $arrayDeTemposAtualizados agora contém os tempos atualizados


            // dd($arrayDeTemposAtualizados);






            $repeat = DB::table('cronograma AS rm')
            ->leftJoin('grupo AS g', 'rm.id_grupo', 'g.id')
            ->leftJoin('salas AS s', 'rm.id_sala', 's.id')
            ->where('rm.dia', $dia)
            ->where('rm.data_fim', null)
            ->where('rm.id_sala', $numero)
            ->where(function ($query) use ($h_inicio, $h_fim) {
                $query->where('rm.h_inicio', '>=', $h_inicio)
                      ->where('rm.h_inicio', '<=', $h_fim)
                      ->orWhere(function ($query) use ($h_inicio, $h_fim) {
                          $query->where('rm.h_fim', '>=', $h_inicio)
                                ->where('rm.h_fim', '<=', $h_fim);
                      });
            })
            ->count();

            //dd($repeat);

            if($repeat > 0){

                app('flasher')->addError('Existe uma outra reunião nesse horário.');

                return redirect ('/gerenciar-reunioes');

            }else{

            }


           DB::table('cronograma AS rm')->insert([
                    'id_grupo'=>$request->input('grupo'),
                    'id_sala'=>$request->input('numero'),
                    'h_inicio'=>$request->input('h_inicio'),
                    'h_fim'=>$request->input('h_fim'),
                    'max_atend'=>$request->input('max_atend'),
                    'dia'=>$request->input('dia'),
                    'id_tipo_tratamento'=>$request->input('tratamento'),
                    'data_inicio' => $now
                ]);

            $result = DB::table('cronograma')->max('id');

           DB::table('historico_venus')->insert([
            'id_usuario' => $usuario,
            'data' => $now,
            'fato' => 34,
            'id_ref' => $result
        ]);


            app('flasher')->addSuccess('A reunião foi cadastrada com sucesso.');

            return redirect ('/gerenciar-reunioes');

        }


        public function SetPessoa(Request $request){


           DB::table('pessoas AS p')->insert([

            'nome_completo'=>$request->input('nomepes'),

           ]);


            app('flasher')->addSuccess('O cadastro de pessoa foi realizado com sucesso.');

            return redirect ('/gerenciar-atendimentos');

        }


        public function sobeStatus($ida){

            $data = date("Y-m-d H:i:s");


            $sta_at = DB::table('atendimentos AS a')->where('id','=', $ida)->value('a.status_atendimento');


            if ($sta_at == 1){

                app('flasher')->addError('Utilize a opção de vincula o Atendente Fraterno ao atendimento.');
                return redirect ('/gerenciar-atendimentos');
            }
            elseif ($sta_at > 4){

                app('flasher')->addError('O atendimento foi cancelado ou finalizado e não pode ser alterado.');
                return redirect ('/gerenciar-atendimentos');
            }
            elseif ($sta_at == 2){

                app('flasher')->addError('Somente o Atendente Fraterno pode alterar este status');
                return redirect ('/gerenciar-atendimentos');

            }elseif ($sta_at == 3){

                DB::table('atendimentos AS a')->where('id', '=', $ida)->update([
                    'status_atendimento' => 4,
                    'dh_inicio' => $data
                ]);

            }elseif ($sta_at == 4){

                DB::table('atendimentos AS a')->where('id', '=', $ida)->update([
                    'status_atendimento' => 5,
                    'dh_fim' => $data
                ]);

            }

            app('flasher')->addSuccess('O satus foi alterado com sucesso.');
            return redirect ('/gerenciar-atendimentos');

        }

        public function desceStatus($ida){

            $sta_at = DB::table('atendimentos AS a')->where('id','=', $ida)->value('a.status_atendimento');


            if ($sta_at == 1){

                app('flasher')->addWarning('Não existe um status menor.');
                return redirect ('/gerenciar-atendimentos');
            }
            if ($sta_at == 2){

                app('flasher')->addInfo('Somente o atendente pode alterar este status.');
                return redirect ('/gerenciar-atendimentos');
            }
            if ($sta_at == 6){

                app('flasher')->addWarning('O atendimento foi cancelado e não pode ser alterado.');
                return redirect ('/gerenciar-atendimentos');

            }elseif ($sta_at == 5){

                app('flasher')->addWarning('O atendimento foi Finalizado e não pode ser alterado.');
                return redirect ('/gerenciar-atendimentos');

            }elseif ($sta_at == 3){

                DB::table('atendimentos AS a')->where('id', '=', $ida)->update([
                    'status_atendimento' => 1
                ]);

            }elseif ($sta_at == 4){

                DB::table('atendimentos AS a')->where('id', '=', $ida)->update([
                    'status_atendimento' => 1,
                    'dh_inicio' => null,
                    'id_atendente' => null
                ]);

                app('flasher')->addSuccess('O status foi alterado com sucesso.');
                return redirect ('/gerenciar-atendimentos');

            }

            app('flasher')->addError('Aconteceu um problema desconhecido contate o administrador.');
            return redirect ('/gerenciar-atendimentos');

        }

        public function cancelar($ida){

            dd(session());

                DB::table('atendimentos AS a')->where('id', '=', $ida)->update([
                    'status_atendimento' => 6
                ]);

            app('flasher')->addSuccess('O status do atendimento foi alterado para "Cancelado".');
            return redirect ('/gerenciar-atendimentos');

        }


        public function edit($ida){

            $result = DB::table('atendimentos AS at')
                        ->where('at.id', $ida)
                        ->select('at.id AS ida', 'p1.id as idas', 'p1.ddd', 'p1.celular', 'at.dh_chegada', 'at.dh_inicio', 'at.dh_fim', 'at.id_assistido', 'p1.nome_completo AS nm_1', 'at.id_representante as idr', 'p2.nome_completo as nm_2', 'at.id_atendente_pref AS iap', 'p3.nome_completo as nm_3', 'at.id_atendente as idaf', 'p4.nome_completo as nm_4', 'at.pref_tipo_atendente AS pta', 'ts.descricao', 'tp.nome',  'at.parentesco', 'tp.id AS idp', 'tpsx.id AS idsx', 'tpsx.tipo', 'at.id_prioridade', 'pr.id AS prid', 'pr.descricao AS prdesc', 'pr.sigla AS prsigla' )
                        ->leftJoin('tipo_status_atendimento AS ts', 'at.status_atendimento', 'ts.id')
                        ->leftJoin('atendentes AS att', 'at.id_atendente', 'att.id')
                        ->leftJoin('pessoas AS p', 'att.id_pessoa', 'p.id')
                        ->leftjoin('pessoas AS p1', 'at.id_assistido', 'p1.id')
                        ->leftjoin('pessoas AS p2', 'at.id_representante', 'p2.id')
                        ->leftjoin('pessoas AS p3', 'at.id_atendente_pref', 'p3.id')
                        ->leftjoin('pessoas AS p4', 'at.id_atendente', 'p4.id')
                        ->leftjoin('tp_sexo AS tpsx', 'at.pref_tipo_atendente', 'tpsx.id')
                        ->leftJoin('tp_parentesco AS tp', 'at.parentesco', 'tp.id')
                        ->leftJoin('tipo_prioridade AS pr', 'at.id_prioridade', 'pr.id')
                        ->get();

            $lista = DB::select("select
                        p.id,
                        p.ddd,
                        p.celular,
                        p.nome_completo,
                        a.id_pessoa
                        from pessoas p
                        left join atendentes a on (p.id = a.id_pessoa)
                        order by nome_completo
                        ");

            //dd($lista);

            $afi = DB::select("select
                        p.id as iaf,
                        p.nome_completo as nm_afi,
                        p.ddd,
                        p.celular,
                        a.id_pessoa,
                        a.id
                        from atendentes a
                        left join pessoas p on (a.id_pessoa = p.id)
                        order by nm_afi
                        ");

            $sexo = DB::select("select
                        id as idsx,
                        tipo,
                        sigla
                        from tp_sexo
                        ");

            $pare = DB::select("select
                        id as idp,
                        nome
                        from tp_parentesco
                        ");

            $priori = DB::select("select
                        pr.id as prid,
                        pr.descricao as prdesc,
                        pr.sigla as prsigla
                        from tipo_prioridade pr
                        order by id
                        ");


            return view ('/recepcao-AFI/editar-atendimento', compact('result', 'priori', 'sexo', 'pare', 'afi', 'lista'));

        }

        public function altera(Request $request, $ida){


            DB::table('atendimentos AS at')->where('at.id', $ida)->update([

                'id_assistido'=>$request->input('assist'),
                'id_representante'=>$request->input('repres'),
                'parentesco'=>$request->input('parent'),
                'id_atendente_pref'=>$request->input('afi_p'),
                'pref_tipo_atendente'=>$request->input('tipo_afi'),
                'id_prioridade'=>$request->input('priori')

            ]);


             app('flasher')->addSuccess('O cadastro de pessoa foi alterado com sucesso.');

             return redirect ('/gerenciar-atendimentos');

         }

         public function visual($idas){


            $result = DB::table('atendimentos AS at')
                        ->where('p1.id', $idas)
                        ->select('at.id AS ida', 'at.pref_tipo_atendente', 'p1.dt_nascimento', 'at.dh_chegada',  'at.dh_fim', 'at.dh_inicio', 'at.id_assistido', 'at.id_representante', 'at.id_atendente_pref', 'at.id_atendente', 'at.parentesco', 'tdd.descricao AS ddd', 'p1.celular', 'p1.id AS idas', 'p1.nome_completo AS nm_1',  'p2.nome_completo as nm_2',  'p3.id AS idp', 'p3.nome_completo as nm_3',  'p4.nome_completo as nm_4',  'ts.descricao', 'tp.nome',   'tp.id AS idp', 'tpsx.id AS idsx', 'tpsx.tipo')
                        ->leftjoin('tipo_status_atendimento AS ts', 'at.status_atendimento', 'ts.id')
                        ->leftJoin('atendentes AS att', 'at.id_atendente', 'att.id')
                        ->leftJoin('pessoas AS p', 'att.id_pessoa', 'p.id')
                        ->leftjoin('pessoas AS p1', 'at.id_assistido', 'p1.id')
                        ->leftjoin('pessoas AS p2', 'at.id_representante', 'p2.id')
                        ->leftjoin('pessoas AS p3', 'att.id_pessoa', 'p3.id')
                        ->leftjoin('pessoas AS p4', 'att.id_pessoa', 'p4.id')
                        ->leftjoin('tp_sexo AS tpsx', 'at.pref_tipo_atendente', 'tpsx.id')
                        ->leftJoin('tp_parentesco AS tp', 'at.parentesco', 'tp.id')
                        ->leftJoin('tp_ddd AS tdd', 'p1.ddd', 'tdd.id')
                        ->orderBy('dh_chegada', 'DESC')
                        ->get();




             return view ('/recepcao-AFI/visualizar-atendimentos', compact('result'));

        }

         public function salvaatend(Request $request, $ida){

            $sta_at = DB::table('atendimentos AS a')
                    ->where('id','=', $ida)
                    ->value('a.status_atendimento');

            $att = $request->atendente;

            $sit_afi = DB::table('atendimentos AS at')
                    ->where('id_atendente','=', $att)
                    ->where('at.status_atendimento', '<', '5')
                    ->count();

            //dd($sit_afi);


            if ($sta_at == 2){

                app('flasher')->addInfo('Somente o atendente pode alterar este status.');
                return redirect ('/gerenciar-atendimentos');

            }elseif ($sta_at == 3){

                app('flasher')->addWarning('O atendimento está direcionado para outro atendente.');
                return redirect ('/gerenciar-atendimentos');

            }elseif ($sta_at == 5){

                app('flasher')->addWarning('O atendimento foi Finalizado e não pode ser alterado.');
                return redirect ('/gerenciar-atendimentos');

            }elseif ($sta_at == 6){

                app('flasher')->addWarning('O atendimento foi cancelado e não pode ser alterado.');
                return redirect ('/gerenciar-atendimentos');

            }if ($sta_at == 1 && $sit_afi > 0){

                app('flasher')->addWarning('O atendente está ocupado.');
                return redirect ('/gerenciar-atendimentos');

            }if ($sta_at == 1 && $sit_afi == 0 ){

                DB::table('atendimentos AS a')->where('id', '=', $ida)->update([
                    'status_atendimento' => 3,
                    'id_atendente' => $request->input('atendente')
                ]);
            }

            app('flasher')->addSuccess('O status foi alterado e o atendente incluído.');
            return redirect ('/gerenciar-atendimentos');
        }



}
