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

    public function index(Request $request)
    {

        $now =  Carbon::now()->format('Y-m-d');
        $reuniao = DB::table('cronograma AS cro')
            ->select(
                'cro.id AS idr',
                'gr.nome AS nomeg',
                'cro.dia_semana AS idd',
                'cro.id_sala',
                'cro.id_tipo_tratamento',
                'cro.id_tipo_tratamento',
                'cro.h_inicio',
                'td.nome AS nomed',
                'cro.h_fim',
                'cro.max_atend',
                'gr.status_grupo AS idst',
                'tst.descricao AS tstd',
                'sa.numero',
                DB::raw("(CASE WHEN cro.data_fim < '$now' THEN 'Inativo' ELSE 'Ativo' END) as status")
            )
            ->leftJoin('tipo_tratamento AS tst', 'cro.id_tipo_tratamento', 'tst.id')
            ->leftjoin('grupo AS gr', 'cro.id_grupo', 'gr.id')
            ->leftJoin('membro AS me', 'gr.id', 'me.id_cronograma')
            ->leftJoin('salas AS sa', 'cro.id_sala', 'sa.id')
            ->leftJoin('tipo_dia AS td', 'cro.dia_semana', 'td.id');


        $semana = $request->semana == null ? "undefined" : $request->semana;

        $grupo = $request->grupo;

        $status = $request->status == null ? "undefined" : $request->status;

        if (in_array(25, session()->get('usuario.setor'))) {
        } else {
            $reuniao = $reuniao->whereIn('gr.id_setor', session()->get('usuario.setor'));
        }

        if ($request->semana != null && $request->semana != 'todos') {
            $reuniao->where('cro.dia_semana', '=', $request->semana);
        }

        if ($request->grupo) {

            $reuniao->where('gr.nome', 'ilike', "%$request->grupo%");

        }



        $reuniao = $reuniao->orderby('status', 'ASC')->orderby('cro.id_tipo_tratamento', 'ASC')->orderby('nomeg', 'ASC')->paginate(50);

        //dd($request->semana);
        //dd($status);

        $contar = $reuniao->count('cro.id');

        $situacao = DB::table('tipo_status_grupo')->select('id AS ids', 'descricao AS descs')->get();

        $tpdia = DB::table('tipo_dia')->select('id AS idtd', 'nome AS nomed')->get();




        return view('/reuniao-mediunica/gerenciar-reunioes', compact('reuniao', 'tpdia', 'situacao', 'status', 'contar', 'semana', 'grupo', 'status'));
    }


    public function create()
    {

        try {

            $grupo = DB::table('grupo AS gr')
                ->select('gr.id AS idg', 'gr.nome', 'gr.id_tipo_grupo')
                ->orderBy('gr.nome');


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

            if (in_array(25, session()->get('usuario.setor'))) {
            } else {
                $grupo = $grupo->whereIn('gr.id_setor', session()->get('usuario.setor'));
            }

            $grupo = $grupo->get();


            return view('/reuniao-mediunica/criar-reuniao', compact('grupo', 'tipo',  'tratamento',  'dia', 'salas'));
        } catch (\Exception $e) {

            $code = $e->getCode();
            return view('administrativo-erro.erro-inesperado', compact('code'));
        }
    }

    public function store(Request $request)
    {
        try {

            $usuario = session()->get('usuario.id_pessoa');
            $now =  Carbon::now()->format('Y-m-d');
            $data_inicio = $request->dt_inicio ? $request->dt_inicio : $now;

            $grupo = intval($request->grupo);
            $numero = intval($request->id_sala);
            $h_inicio = Carbon::createFromFormat('G:i', $request->h_inicio)->subMinutes(30);
            $h_fim = Carbon::createFromFormat('G:i', $request->h_fim)->addMinutes(30);
            $dia = intval($request->dia);

            $repeat = DB::table('cronograma AS rm')
                ->leftJoin('grupo AS g', 'rm.id_grupo', 'g.id')
                ->leftJoin('salas AS s', 'rm.id_sala', 's.id')
                ->where('rm.dia_semana', $dia)
                ->whereNot('rm.data_fim', '<', $now)
                ->where('rm.id_sala', $numero)
                ->where(function ($query) use ($h_inicio, $h_fim) {
                    $query->where(function ($hour) use ($h_inicio) {
                        $hour->where('rm.h_inicio', '<=', $h_inicio);
                        $hour->where('rm.h_fim', '>=', $h_inicio);
                    });
                    $query->orWhere(function ($hour) use ($h_fim) {
                        $hour->where('rm.h_inicio', '<=', $h_fim);
                        $hour->where('rm.h_fim', '>=', $h_fim);
                    });
                })
                ->count();



            if ($repeat > 0) {

                app('flasher')->addError('Existe uma outra reunião nesse horário.');

                return redirect('/gerenciar-reunioes');
            } else {
            }


            DB::table('cronograma AS rm')->insert([
                'id_grupo' => $request->input('grupo'),
                'id_sala' => $request->input('id_sala'),
                'h_inicio' => $request->input('h_inicio'),
                'h_fim' => $request->input('h_fim'),
                'max_atend' => $request->input('max_atend'),
                'dia_semana' => $request->input('dia'),
                'id_tipo_tratamento' => $request->input('tratamento'),
                'data_inicio' => $data_inicio,
                'data_fim' => $request->dt_fim
            ]);

            $result = DB::table('cronograma')->max('id');

            DB::table('historico_venus')->insert([
                'id_usuario' => $usuario,
                'data' => $now,
                'fato' => 34,
                'id_ref' => $result
            ]);


            app('flasher')->addSuccess('A reunião foi cadastrada com sucesso.');

            return redirect('/gerenciar-reunioes');
        } catch (\Exception $e) {

            $code = $e->getCode();
            return view('administrativo-erro.erro-inesperado', compact('code'));
        }
    }

    public function show(string $id)
    {
        try {

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
                ->select('crn.id', 'gr.nome', 'tpd.nome as dia', 'tpt.descricao', 'crn.max_atend', 'sl.numero', 'sl.nome as sala', 'crn.h_inicio', 'crn.h_fim', 'crn.id_sala', 'sl.id_localizacao as nome_localizacao', 'crn.data_inicio', 'crn.data_fim')
                ->leftJoin('grupo as gr', 'crn.id_grupo', 'gr.id')
                ->leftJoin('tipo_dia as tpd', 'crn.dia_semana', 'tpd.id')
                ->leftJoin('tipo_tratamento as tpt', 'crn.id_tipo_tratamento', 'tpt.id')
                ->leftJoin('salas as sl', 'crn.id_sala', 'sl.id')
                ->where('crn.id', "$id")
                ->first();



            return view('/reuniao-mediunica/visualizar-reuniao', compact('info', 'salas', 'grupo', 'tipo',  'tratamento',  'dia'));
        } catch (\Exception $e) {

            $code = $e->getCode();
            return view('administrativo-erro.erro-inesperado', compact('code'));
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {

        try {

            $grupo = DB::table('grupo AS gr')
                ->select('gr.id AS idg', 'gr.nome', 'gr.id_tipo_grupo')
                ->where('id_tipo_grupo', 1)
                ->orderBy('gr.nome');


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
                ->select('crn.id', 'gr.nome', 'tpd.nome as dia', 'tpt.descricao', 'crn.max_atend', 'sl.numero', 'sl.nome as sala', 'crn.h_inicio', 'crn.h_fim', 'crn.id_sala', 'sl.id_localizacao as nome_localizacao', 'crn.data_inicio', 'crn.data_fim')
                ->leftJoin('grupo as gr', 'crn.id_grupo', 'gr.id')
                ->leftJoin('tipo_dia as tpd', 'crn.dia_semana', 'tpd.id')
                ->leftJoin('tipo_tratamento as tpt', 'crn.id_tipo_tratamento', 'tpt.id')
                ->leftJoin('salas as sl', 'crn.id_sala', 'sl.id')
                ->where('crn.id', "$id")
                ->first();

            if (in_array(25, session()->get('usuario.setor'))) {
            } else {
                $grupo = $grupo->whereIn('gr.id_setor', session()->get('usuario.setor'));
            }

            $grupo = $grupo->get();

            return view('/reuniao-mediunica/editar-reuniao', compact('info', 'salas', 'grupo', 'tipo',  'tratamento',  'dia'));
        } catch (\Exception $e) {

            $code = $e->getCode();
            return view('administrativo-erro.erro-inesperado', compact('code'));
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {

            $usuario = session()->get('usuario.id_pessoa');
            $now =  Carbon::now()->format('Y-m-d');
            $data_inicio = $request->dt_inicio ? $request->dt_inicio : $now;

            $grupo = intval($request->grupo);
            $numero = intval($request->id_sala);
            $h_inicio = Carbon::createFromDate($request->h_inicio)->subMinutes(30);
            $h_fim = Carbon::createFromDate($request->h_fim)->addMinutes(30);
            $dia = intval($request->dia);
            $repeat = DB::table('cronograma AS rm')
                ->leftJoin('grupo AS g', 'rm.id_grupo', 'g.id')
                ->leftJoin('salas AS s', 'rm.id_sala', 's.id')
                ->where('rm.dia_semana', $dia)
                ->whereNot('rm.data_fim', '<', $now)
                ->where('rm.id_sala', $numero)
                ->where(function ($query) use ($h_inicio, $h_fim) {
                    $query->where(function ($hour) use ($h_inicio) {
                        $hour->where('rm.h_inicio', '<=', $h_inicio);
                        $hour->where('rm.h_fim', '>=', $h_inicio);
                    });
                    $query->orWhere(function ($hour) use ($h_fim) {
                        $hour->where('rm.h_inicio', '<=', $h_fim);
                        $hour->where('rm.h_fim', '>=', $h_fim);
                    });
                })
                ->count();

            if ($repeat > 0) {

                app('flasher')->addError('Existe uma outra reunião nesse horário.');

                return redirect('/gerenciar-reunioes');
            } else {
            }


            DB::table('cronograma AS rm')->where('id', $id)->update([
                'id_grupo' => $request->input('grupo'),
                'id_sala' => $request->input('numero_sala'),
                'h_inicio' => $request->input('h_inicio'),
                'h_fim' => $request->input('h_fim'),
                'max_atend' => $request->input('max_atend'),
                'dia_semana' => $request->input('dia'),
                'id_tipo_tratamento' => $request->input('tratamento'),
                'data_inicio' => $data_inicio,
                'data_fim' => $request->dt_fim
            ]);

            $result = DB::table('cronograma')->max('id');

            DB::table('historico_venus')->insert([
                'id_usuario' => $usuario,
                'data' => $now,
                'fato' => 34,
                'id_ref' => $result
            ]);


            app('flasher')->addSuccess('A reunião foi atualizada com sucesso.');

            return redirect('/gerenciar-reunioes');
        } catch (\Exception $e) {

            $code = $e->getCode();
            return view('administrativo-erro.erro-inesperado', compact('code'));
        }
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
                'data_fim' => $now
            ]);




        return redirect('/gerenciar-reunioes');
    }
}
