<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Carbon;
use Mockery\Undefined;

use function Laravel\Prompts\select;

class ReuniaoMediunicaController extends Controller
{

        public function index(Request $request){

            $now =  Carbon::now()->format('Y-m-d');


            $reuniao = DB::table('cronograma AS cro')
                        ->select('cro.id AS idr', 'gr.nome AS nomeg', 'cro.dia_semana AS idd', 'cro.id_sala', 'cro.id_tipo_tratamento', 'cro.id_tipo_tratamento', 'cro.h_inicio','td.nome AS nomed', 'cro.h_fim', 'cro.max_atend', 'gr.status_grupo AS idst', 'tst.descricao AS tstd', 'sa.numero', DB::raw("(CASE WHEN cro.data_fim IS NULL THEN 'Ativo' ELSE 'Inativo' END) as status"))
                        ->leftJoin('tipo_tratamento AS tst', 'cro.id_tipo_tratamento', 'tst.id')
                        ->leftjoin('grupo AS gr', 'cro.id_grupo', 'gr.id')
                        ->leftJoin('membro AS me', 'gr.id', 'me.id_cronograma')
                        ->leftJoin('salas AS sa', 'cro.id_sala', 'sa.id')
                        ->leftJoin('tipo_dia AS td', 'cro.dia_semana', 'td.id');


            $semana = $request->semana == null ? "undefined" : $request->semana  ;

            $grupo = $request->grupo;

            $status = $request->status == null ? "undefined" : $request->status;


            if ($request->semana != null){
                $reuniao->where('cro.dia_semana', '=', $request->semana);
            }

            if ($request->grupo){
                $reuniao->where('gr.nome', 'ilike', "%$request->grupo%");
            }

            if ($request->status){
                $reuniao->where('tsg.id', '=', $request->status);
            }

            $reuniao = $reuniao->orderby('status', 'ASC')->orderby('cro.id_tipo_tratamento', 'ASC')->orderby('nomeg', 'ASC')->paginate(50);

             //dd($request->semana);
              //dd($status);

            $contar = $reuniao->count('cro.id');

            $situacao = DB::table('tipo_status_grupo')->select('id AS ids', 'descricao AS descs')->get();

            $tpdia = DB::table('tipo_dia')->select('id AS idtd', 'nome AS nomed')->get();




            return view('/reuniao-mediunica/gerenciar-reunioes', compact('reuniao', 'tpdia', 'situacao', 'status', 'contar', 'semana', 'grupo', 'status'));


        }

        public function create(){

            // $sala = DB::table('salas AS sl')
            //             ->select('sl.id AS ids', 'sl.nome', 'sl.numero', 'sl.nr_lugares','sl.id_localizacao')
            //             ->where('id_finalidade', 6)
            //             ->orderBy('numero', 'asc')
            //             ->get();

            $grupo = DB::table('grupo AS gr')
                        ->select('gr.id AS idg', 'gr.nome', 'gr.id_tipo_grupo')
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

            $salas = DB::table('salas')
                        ->join('tipo_localizacao', 'salas.id_localizacao', '=', 'tipo_localizacao.id')
                        ->where('id_finalidade', 6)
                        ->select('salas.*', 'tipo_localizacao.nome AS nome_localizacao')
                        ->get();

            return view ('/reuniao-mediunica/criar-reuniao', compact( 'grupo', 'tipo',  'tratamento',  'dia','salas'));


        }

        public function store(Request $request){

            $usuario = session()->get('usuario.id_pessoa');

            $now =  Carbon::now()->format('Y-m-d');

            $grupo = intval($request->grupo);
            $numero = intval($request->numero);
            $h_inicio = $request->h_inicio;
            $h_fim = $request->h_fim;
            $dia = intval($request->dia);

            $repeat = DB::table('cronograma AS rm')
            ->leftJoin('grupo AS g', 'rm.id_grupo', 'g.id')
            ->leftJoin('salas AS s', 'rm.id_sala', 's.id')
            ->where('rm.dia_semana', $dia)
            ->where('rm.data_fim', null)
            ->where('rm.id_sala', $numero)

            ->count();



            if($repeat > 0){

                app('flasher')->addError('Existe uma outra reunião nesse horário.');

                return redirect ('/gerenciar-reunioes');

            }else{

            }


           DB::table('cronograma AS rm')->insert([
                    'id_grupo'=>$request->input('grupo'),
                    'id_sala'=>$request->input('id_sala'),
                    'h_inicio'=>$request->input('h_inicio'),
                    'h_fim'=>$request->input('h_fim'),
                    'max_atend'=>$request->input('max_atend'),
                    'dia_semana'=>$request->input('dia'),
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

        public function show(string $id)
        {
            // $sala = DB::table('salas AS sl')
            // ->select('sl.id AS ids', 'sl.nome', 'sl.numero', 'sl.nr_lugares')
            // ->where('id_finalidade', 6)
            // ->orderBy('numero', 'asc')
            // ->get();

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

            $salas = DB::table('salas')
            ->join('tipo_localizacao', 'salas.id_localizacao', '=', 'tipo_localizacao.id')
            ->where('id_finalidade', 6)
            ->select('salas.*', 'tipo_localizacao.nome AS nome_localizacao')
            ->orderBy('numero', 'asc')
            ->get();

            $dia = DB::table('tipo_dia AS td')
            ->select('td.id AS idd', 'td.nome', 'td.sigla')
            ->get();

            $info = DB::table('cronograma as crn')
            ->select('crn.id','gr.nome', 'tpd.nome as dia', 'tpt.descricao', 'crn.max_atend', 'sl.numero', 'sl.nome as sala', 'crn.h_inicio', 'crn.h_fim','crn.id_sala','sl.id_localizacao as nome_localizacao')
            ->leftJoin('grupo as gr', 'crn.id_grupo', 'gr.id')
            ->leftJoin('tipo_dia as tpd', 'crn.dia_semana', 'tpd.id')
            ->leftJoin('tipo_tratamento as tpt', 'crn.id_tipo_tratamento', 'tpt.id')
            ->leftJoin('salas as sl', 'crn.id_sala', 'sl.id')
            ->where('crn.id', "$id")
            ->first();



return view ('/reuniao-mediunica/visualizar-reuniao', compact('info','salas', 'grupo', 'tipo',  'tratamento',  'dia'));
        }

        /**
         * Show the form for editing the specified resource.
         */
        public function edit(string $id)
        {

            // $sala = DB::table('salas AS sl')
            // ->select('sl.id AS ids', 'sl.nome', 'sl.numero', 'sl.nr_lugares','sl.id_localizacao')
            // ->where('id_finalidade', 6)
            // ->orderBy('numero', 'asc')
            // ->get();

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

            $salas = DB::table('salas')
            ->join('tipo_localizacao', 'salas.id_localizacao', '=', 'tipo_localizacao.id')
            ->select('salas.*', 'tipo_localizacao.nome AS nome_localizacao')
            ->where('id_finalidade', 6)
            ->orderBy('numero', 'asc')
            ->get();

            $info = DB::table('cronograma as crn')
            ->select('crn.id','gr.nome', 'tpd.nome as dia', 'tpt.descricao', 'crn.max_atend', 'sl.numero', 'sl.nome as sala', 'crn.h_inicio', 'crn.h_fim','crn.id_sala','sl.id_localizacao as nome_localizacao')
            ->leftJoin('grupo as gr', 'crn.id_grupo', 'gr.id')
            ->leftJoin('tipo_dia as tpd', 'crn.dia_semana', 'tpd.id')
            ->leftJoin('tipo_tratamento as tpt', 'crn.id_tipo_tratamento', 'tpt.id')
            ->leftJoin('salas as sl', 'crn.id_sala', 'sl.id')
            ->where('crn.id', "$id")
            ->first();



return view ('/reuniao-mediunica/editar-reuniao', compact('info','salas', 'grupo', 'tipo',  'tratamento',  'dia'));

        }

        /**
         * Update the specified resource in storage.
         */
        public function update(Request $request, string $id)
        {

            $usuario = session()->get('usuario.id_pessoa');

            $now =  Carbon::now()->format('Y-m-d');

            $grupo = intval($request->grupo);
            $numero = intval($request->numero);
            $h_inicio = $request->h_inicio;
            $h_fim = $request->h_fim;
            $dia = intval($request->dia);

            $repeat = DB::table('cronograma AS rm')
            ->where('rm.id','!=', $id)
            ->leftJoin('grupo AS g', 'rm.id_grupo', 'g.id')
            ->leftJoin('salas AS s', 'rm.id_sala', 's.id')
            ->where('rm.dia_semana', $dia)
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


           DB::table('cronograma AS rm')->where('id', $id)->update([
                    'id_grupo'=>$request->input('grupo'),
                    'id_sala'=>$request->input('numero_sala'),
                    'h_inicio'=>$request->input('h_inicio'),
                    'h_fim'=>$request->input('h_fim'),
                    'max_atend'=>$request->input('max_atend'),
                    'dia_semana'=>$request->input('dia'),
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


            app('flasher')->addSuccess('A reunião foi atualizada com sucesso.');

            return redirect ('/gerenciar-reunioes');

        }

        /**
         * Remove the specified resource from storage.
         */
        public function destroy(string $id)
        {
            $now =  Carbon::now()->format('Y-m-d');
             DB::table('cronograma as cro')
                       ->where('cro.id', $id)
             ->update([
                'status_reuniao'=> 2,
                'data_fim' => $now
            ]);




            return redirect ('/gerenciar-reunioes');

        }

}
