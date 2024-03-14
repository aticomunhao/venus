<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class AtendimentoApoioController extends Controller
{
    /**
     * Display a listing of the resource.
     */


/*
|--------------------------------------------------------------------------
| Consertar tabela LOG
|--------------------------------------------------------------------------
*/


    public function index(Request $request)
    {
        $pesquisaNome = $request->input('nome');
        $pesquisaCpf = $request->input('cpf');

        $atendente = DB::table('atendente_apoio AS at')
        ->select('at.id', 'p.nome_completo', 'p.cpf', 'tp.tipo')
        ->leftJoin('pessoas AS p', 'at.id_pessoa', '=', 'p.id')
        ->leftJoin('tipo_status_pessoa AS tp', 'p.status', '=', 'tp.id');



        if ($pesquisaNome) {
            $atendente =
            $atendente->where('p.nome_completo', 'ilike', "%$pesquisaNome%");

        } elseif ($pesquisaCpf) {
            $atendente = $atendente
                ->where('p.cpf', 'ilike', "%$pesquisaCpf%");

        }

        $atendente = $atendente->orderBy('p.status', 'desc')->orderBy('nome_completo')->get();
        $conta = $atendente->count();

        return view('/atendentes-apoio/gerenciar-atendente-apoio', compact('atendente', 'conta', 'pesquisaNome', 'pesquisaCpf'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $nomes = DB::table('pessoas')
            ->where('status', '=', '1')
            ->get();

        $dias = DB::table('tipo_dia')->get();

        return view('/atendentes-apoio/incluir-atendente-apoio', compact('nomes', 'dias'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        DB::beginTransaction();
        try{

            $req = $request->all();
            $dataHoje = Carbon::today()->toDateString();





            $idAtendente = DB::table('atendente_apoio')->insertGetId([
                'id_pessoa' => $request->input('nome'),
            ]);

            $i = 0;

            foreach ($request->checkbox as $checked) {
                $horaInicio = Carbon::createFromFormat(' G:i', ($req['dhFim'][$i]));
                $horaFim =  Carbon::createFromFormat(' G:i', ($req['dhInicio'][$i]));
                $horas = $horaFim->diffInMinutes($horaInicio, false);

                if($horas <= 0){

                    app('flasher')->addError("Um dos horários é inválido!");
                    DB::rollBack();
                    return back()->withInput();
                }

                DB::table('atendente_apoio_dia')->insert([
                    'id_atendente' => $idAtendente,
                    'id_dia' => $checked, // Estou assumindo que $checked é o ID do dia
                    'dh_inicio' => $req['dhInicio'][$i], // Use o array $req para obter os horários
                    'dh_fim' => $req['dhFim'][$i],
                    'dt_inicio' => $dataHoje, // Se você também tiver um array para dhFim
                ]);
                $i += 1;
            }
            $i = 0;


            DB::commit();
            return redirect()->route('indexAtendenteApoio');

}

catch(\Exception $e){

            app('flasher')->addError("Houve um erro inesperado: #" . $e->getCode( )) ;
            DB::rollBack();
            return redirect()->back();

        }

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $idp = DB::table('atendente_apoio')
            ->where('id', '=', $id)
            ->get();

        $nomes = DB::table('pessoas')
            ->where('id', '=', $idp[0]->id_pessoa)
            ->get();

        $historico = DB::table('atendente_apoio_dia as hs')
            ->select(['hs.dt_inicio', 'hs.dt_fim', 'hs.dh_inicio', 'hs.dh_fim', 'd.nome'])
            ->leftJoin('tipo_dia as d', 'hs.id_dia', '=', 'd.id')
            ->where('id_atendente', '=', $id, 'and', 'dt_fim', '=', null)
            ->get();

        $dias = DB::table('tipo_dia')->get();

        return view('/atendentes-apoio/visualizar-atendente-apoio', compact('nomes', 'dias', 'historico'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $nomes = DB::table('atendente_apoio as at')
            ->select('p.nome_completo', 'at.id', 'p.status')
            ->leftJoin('pessoas as p', 'at.id_pessoa', '=', 'p.id')
            ->where('at.id', '=', $id)
            ->get();
            $pessoas = DB::select('select id as idp, nome_completo from pessoas');
        $tipo_status_pessoa = DB::select('select * from tipo_status_pessoa');

        $dias = DB::table('tipo_dia')->get();

        $diasHorarios = DB::table('atendente_apoio_dia')
            ->where('id_atendente', '=', $id)
            ->get();
        $checkTheBox = [];

        foreach ($diasHorarios as $dia) {
            $checkTheBox[] = $dia->id_dia;
        }
        return view('atendentes-apoio/editar-atendente-apoio', compact('nomes', 'dias', 'diasHorarios', 'checkTheBox'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {

        DB::beginTransaction();
        try{

            DB::table('atendente_apoio_dia')->where('id_atendente', '=', $id)->delete();
            $req = $request->all();
            $dataHoje = Carbon::today()->toDateString();

            $idAtendente = $id;

            $i = 0;

            foreach ($request->checkbox as $checked) {


                DB::table('atendente_apoio_dia')->insert([
                    'id_atendente' => $idAtendente,
                    'id_dia' => $checked, // Estou assumindo que $checked é o ID do dia
                    'dh_inicio' => $req['dhInicio'][$checked], // Use o array $req para obter os horários
                    'dh_fim' => $req['dhFim'][$checked],
                    'dt_inicio' => $dataHoje, // Se você também tiver um array para dhFim
                ]);
                $i += 1;
            }
            $i = 0;


            DB::commit();
            return redirect()->route('indexAtendenteApoio');

}

catch(\Exception $e){

            app('flasher')->addError("Houve um erro inesperado: #" . $e->getCode( )) ;
            DB::rollBack();
            return redirect()->back();

        }

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
