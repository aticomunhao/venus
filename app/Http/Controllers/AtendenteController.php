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
                ->select('p.id AS idp', 'p.nome_completo', 'p.cpf', 'tps.tipo', 'ag.id_grupo', 'ag.id_atendente', 'ag.dt_inicio', 'ag.dt_fim', 'ag.motivo', 'ag.dt_fim', 'p.dt_nascimento', 'p.sexo', 'p.email', 'p.ddd', 'p.celular', 'tsp.id AS idtps', 'p.status', 'tsp.tipo AS tpsta', 'd.id as did', 'd.descricao as ddesc', 'p.motivo_status', 'g.nome AS gnome')
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

            return view('/atendentes/gerenciar-atendentes', compact('atendente', 'stap', 'soma', 'ddd', 'sexo', 'cpf', 'nome', 'grupos'));
        }
    







    public function edit($idp)
    {
        $ddd = DB::select('select id, descricao from tp_ddd');

        $sexo = DB::select('select id, tipo from tp_sexo');

        $status_p = DB::select('select id, tipo from tipo_status_pessoa');

        $motivo = DB::select('select id, tipo from tipo_motivo order by tipo');



        $lista = DB::select("select p.id as idp, p.nome_completo, p.ddd, p.dt_nascimento, p.sexo, p.email, p.cpf, p.celular, tps.id AS sexid, tps.tipo, d.id AS did, d.descricao as ddesc from pessoas p
        left join tp_sexo tps on (p.sexo = tps.id)
        left join tp_ddd d on (p.ddd = d.id)
        where p.id = $idp");

        return view ('/pessoal/editar-pessoa', compact('lista', 'sexo', 'ddd', 'status_p', 'motivo'));

    }



}
