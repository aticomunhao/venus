<?php

namespace App\Http\Controllers;

use App\Models\Atendente;
use App\Models\Grupo;
use App\Models\Pessoa;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;


class AtendenteController extends Controller
{


        /**
         * Display a listing of the resource.
         */
        public function index(Request $request)
        {

            $ddd = DB::select('select id, descricao from tp_ddd');
            $grupos = DB::select('select id, nome from grupo order by nome');
            $sexo = DB::select('select id, tipo from tp_sexo');

            $atendente = DB::table('atendentes AS ad')
                ->select('ad.id','p.id AS idp', 'p.nome_completo', 'p.cpf', 'tps.tipo', 'ag.id_grupo','p.cpf','p.ddd','p.email','p.celular', 'ag.id_atendente', 'ag.dt_inicio', 'ag.dt_fim', 'ag.motivo', 'ag.dt_fim', 'p.dt_nascimento', 'p.sexo', 'p.email', 'p.ddd', 'p.celular', 'tsp.id AS idtps', 'p.status', 'tsp.tipo AS tpsta', 'd.id as did', 'd.descricao as ddesc', 'p.motivo_status', 'g.nome AS gnome')
                ->leftJoin('pessoas AS p', 'ad.id_pessoa', '=', 'p.id')
                ->leftJoin('atendente_grupo AS ag', 'ag.id_atendente', '=', 'ad.id')
                ->leftJoin('tipo_status_pessoa AS tsp', 'p.status', '=', 'tsp.id')
                ->leftJoin('tp_sexo AS tps', 'p.sexo', '=', 'tps.id')
                ->leftJoin('tp_ddd AS d', 'p.ddd', '=', 'd.id')
                ->leftJoin('grupo AS g', 'ag.id_grupo', '=', 'g.id');

            $nome = $request->nome;
            if ($request->nome) {
                $atendente->where('p.nome_completo', 'ilike', "%$request->nome%");
            }

            $grupo = $request->grupo;
            if ($request->grupo) {
                $atendente->where('g.id', $request->grupo);
            }

            $cpf = $request->cpf;
            if ($request->cpf) {
                $atendente->where('p.cpf', $request->cpf);
            }

            $status = $request->status;
            if ($request->status) {
                $atendente->where('p.status', $request->status);
            }

            $atendente = $atendente->orderBy('p.status', 'asc')->orderBy('p.nome_completo', 'asc')->paginate(50);

            $stap = DB::select("select
                            id as ids,
                            tipo
                            from tipo_status_pessoa t
                            ");

            $soma = DB::table('atendentes')->count();

            return view('/atendentes/gerenciar-atendente', compact('atendente', 'stap', 'soma', 'ddd', 'sexo', 'cpf', 'nome', 'grupos'));
        }



        public function create(Request $request)
        {

            $pessoas = DB::select('select id as idp, nome_completo from pessoas');
            $tipo_status_pessoa = DB::select('select *from tipo_status_pessoa');
            $grupo = DB::select('select id, nome from grupo');
            $atendentes = DB::select('select * from atendentes');
            $atendente_grupo = DB::select('select * from atendente_grupo');







            return view('atendentes/criar-atendente', compact('pessoas','grupo','atendentes','atendente_grupo'));


        }

        public function store(Request $request)
        {
            $data = date("Y-m-d H:i:s");

            $atendenteId = DB::table('atendentes')->insertGetId([
                'id_pessoa' => $request->input('id_pessoa'),

            ]);


            DB::table('atendente_grupo')->insert([
                'id_atendente' => $atendenteId,
                'id_grupo' => $request->input('id_grupo'),
                'dt_inicio' => $data,
            ]);

            app('flasher')->addSuccess('O cadastro foi realizado com sucesso.');

            return redirect('gerenciar-atendentes');
        }


        public function update(Request $request, $idp)
        {
            $data = date("Y-m-d H:i:s");


            DB::table('atendentes')
                ->where('id_pessoa', $idp)
                ->update([
                    'id_pessoa' => $request->input('id_pessoa'),

                ]);

            DB::table('atendente_grupo')
                ->where('id_atendente', $idp)
                ->update([
                    'id_grupo' => $request->input('id_grupo'),
                    'dt_inicio' => $data,

                ]);

            app('flasher')->addSuccess('Os dados foram atualizados com sucesso.');

            return redirect('gerenciar-atendentes');
        }

    public function edit($id)
{
    $pessoas = DB::select('select id as idp, nome_completo from pessoas');
    $tipo_status_pessoa = DB::select('select * from tipo_status_pessoa');
    $grupo = DB::select('select id, nome from grupo');
    $atendentes = DB::select('select * from atendentes');
    $atendente_grupo = DB::select('select * from atendente_grupo');

    $atendente = DB::table('atendentes AS ad')
    ->select('ad.id', 'p.id AS idp', 'p.nome_completo', 'p.cpf', 'tps.tipo',
    'ag.id_grupo', 'p.cpf', 'p.ddd', 'p.email', 'p.celular', 'ag.id_atendente',
    'ag.dt_inicio', 'ag.dt_fim', 'ag.motivo', 'ag.dt_fim', 'p.dt_nascimento', 'p.sexo',
    'p.email', 'p.ddd', 'p.celular', 'tsp.id AS idtps', 'p.status', 'tsp.tipo AS tipos',
    'd.id as did', 'd.descricao as ddesc', 'p.motivo_status', 'g.nome AS nome_grupo')->where('ad.id',$id)
    ->leftJoin('pessoas AS p', 'ad.id_pessoa', '=', 'p.id')
    ->leftJoin('atendente_grupo AS ag', 'ag.id_atendente', '=', 'ad.id')
    ->leftJoin('tipo_status_pessoa AS tsp', 'p.status', '=', 'tsp.id')
    ->leftJoin('tp_sexo AS tps', 'p.sexo', '=', 'tps.id')
    ->leftJoin('tp_ddd AS d', 'p.ddd', '=', 'd.id')
    ->leftJoin('grupo AS g', 'ag.id_grupo', '=', 'g.id')
    ->first();

    return view('atendentes/editar-atendente', compact('pessoas', 'tipo_status_pessoa', 'grupo', 'atendentes', 'atendente_grupo', 'atendente'));

}

    public function show($id)
    {

            $pessoas = DB::select('select id as idp, nome_completo from pessoas');
            $tipo_status_pessoa = DB::select('select * from tipo_status_pessoa');
            $grupo = DB::select('select id, nome from grupo');
            $atendentes = DB::select('select * from atendentes');
            $atendente_grupo = DB::select('select * from atendente_grupo');

            $atendente = DB::table('atendentes AS ad')
                ->select('ad.id', 'p.id AS idp', 'p.nome_completo', 'p.cpf', 'tps.tipo',
                'ag.id_grupo', 'p.cpf', 'p.ddd', 'p.email', 'p.celular', 'ag.id_atendente',
                'ag.dt_inicio', 'ag.dt_fim', 'ag.motivo', 'ag.dt_fim', 'p.dt_nascimento', 'p.sexo',
                'p.email', 'p.ddd', 'p.celular', 'tsp.id AS idtps', 'p.status', 'tsp.tipo AS tpsta',
                 'd.id as did', 'd.descricao as ddesc', 'p.motivo_status', 'g.nome AS nome_grupo')->where('ad.id',$id)
                ->leftJoin('pessoas AS p', 'ad.id_pessoa', '=', 'p.id')
                ->leftJoin('atendente_grupo AS ag', 'ag.id_atendente', '=', 'ad.id')
                ->leftJoin('tipo_status_pessoa AS tsp', 'p.status', '=', 'tsp.id')
                ->leftJoin('tp_sexo AS tps', 'p.sexo', '=', 'tps.id')
                ->leftJoin('tp_ddd AS d', 'p.ddd', '=', 'd.id')
                ->leftJoin('grupo AS g', 'ag.id_grupo', '=', 'g.id')
                ->first();

            return view('atendentes/visualizar-atendente', compact('pessoas', 'tipo_status_pessoa', 'grupo', 'atendentes', 'atendente_grupo', 'atendente'));


    }


    public function destroy($id)
    {


        {

            $ids = DB::table('atendentes')->where('id', $id)->get();
            $teste = session()->get('usuario');

            $verifica = DB::table('historico_venus')->where('fato', $id)->count('fato');


            $data = date("Y-m-d H:i:s");






            DB::table('historico_venus')->insert([

                'id_usuario' => session()->get('usuario.id_usuario'),
                'data' => $data,
                'fato' => 0,
                'obs' => $id

            ]);

            DB::table('grupo')->where('id', $id)->delete();





            app('flasher')->addError('Excluido com sucesso.');
            return redirect('/gerenciar-atendentes');
        }

    }
}


