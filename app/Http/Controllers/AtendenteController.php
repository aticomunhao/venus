<?php

namespace App\Http\Controllers;

use App\Models\Atendente;
use App\Models\Grupo;
use App\Models\Pessoa;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class AtendenteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        //  $atendentes = Atendente::all();
        $grupos  = Grupo::all();
        $pessoas = Pessoa::all();

        //search:
        $search = request('query'); // é query porquê é o name do Form da Search

        if ($search) { // se há pesquisa execute, se não, prossiga com o metodo antigo

            $atendentes = Atendente::where([
                ['id_pessoa', 'like', '%'.$search.'%']
                // Vai buscar o pessoa, busca do tipo like (valor n exato), o % faz parte do like
                // % => qualquer coisa antes e depois do $search


            ])->get();

        } else {
            $atendentes = Atendente::all();

        $atendentes = DB::select("SELECT
        p.nome_completo,
        g.nome as nome_grupo,
        a.status_atendente


        FROM atendentes a
        LEFT JOIN pessoas p ON a.id_pessoa = p.id
        LEFT JOIN grupos g on a.id_grupo = g.id


        "); // metodo query
        }



        // $pessoas = DB::select("select * from pessoa");
        // dd($atendentes);

        return view ('/atendentes/gerenciar-atendentes', compact('atendentes', 'grupos', 'pessoas', 'search'));

    }

    public function tester(Request $request){

        return view ('/tester', compact('request'));
    }

    public function novo()
    {
        $pessoas = Pessoa::all();
        $atententes = Atendente::all();

        return view ('/atendentes.novo-atendente', compact('pessoas','atententes'));
    }

    /*
     * Show the form for creating a new resource.
     */

    public function create()
    {
        //
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request) {

    //    $novoAtendenteData = $request->request->get('request');


        return redirect('/');

    }



    /**
     * Display the specified resource.
     */
    public function show(Atendente $atendente)
    {

        return view('visualizar-atendendes',compact('atendente'));
    }

/*
    public function show_detalhes_atendente($id)
    {
        $result = DB::table('atendentes AS at')
                    ->where('p1.id', $id)
                    ->select('at.id AS id', 'tpd.descricao AS ddd', 'p1.celular', 'at.dh_chegada', 'at.dh_inicio', 'at.dh_fim', 'at.id_assistido', 'p1.id AS idas', 'p1.nome_completo AS nm_1', 'at.id_representante', 'p2.nome_completo as nm_2', 'at.id_atendente_pref', 'p3.id AS idp', 'p3.nome_completo as nm_3', 'at.id_atendente', 'p4.nome_completo as nm_4', 'at.pref_tipo_atendente', 'ts.descricao', 'tp.nome',  'at.parentesco', 'tp.id AS idp', 'tpsx.id AS idsx', 'tpsx.tipo')
                    ->leftJoin('tipo_status_atendimento AS ts', 'at.status_atendimento', 'ts.id')
                    ->leftjoin('pessoas AS p1', 'at.id_pessoa', 'p1.id')
                    ->leftjoin('pessoas AS p2', 'at.id_representante', 'p2.id')
                    ->leftJoin('pessoas AS p3', 'at.id_atendente_pref', 'p3.id')
                    ->leftJoin('pessoas AS p4', 'at.id_atendente', 'p4.id')
                    ->leftJoin('atendentes AS a', 'p4.id', 'a.id_pessoa')
                    ->leftJoin('tp_parentesco AS tp', 'at.parentesco', 'tp.id' )
                    ->leftJoin('tp_sexo AS tpsx', 'at.pref_tipo_atendente', 'tpsx.id')
                    ->leftJoin('tp_ddd AS tpd', 'p1.ddd', 'tpd.id')
                    ->orderBy('dh_chegada', 'ASC')
                    ->get();

        return view('visualizar-atendendes',compact('result'));
    }

    public function visual($idas){


        $result = DB::table('atendimentos AS at')
                    ->where('p1.id', $idas)
                    ->select('at.id AS ida', 'tpd.descricao AS ddd', 'p1.celular', 'at.dh_chegada', 'at.dh_inicio', 'at.dh_fim', 'at.id_assistido', 'p1.id AS idas', 'p1.nome_completo AS nm_1', 'at.id_representante', 'p2.nome_completo as nm_2', 'at.id_atendente_pref', 'p3.id AS idp', 'p3.nome_completo as nm_3', 'at.id_atendente', 'p4.nome_completo as nm_4', 'at.pref_tipo_atendente', 'ts.descricao', 'tp.nome',  'at.parentesco', 'tp.id AS idp', 'tpsx.id AS idsx', 'tpsx.tipo')
                    ->leftJoin('tipo_status_atendimento AS ts', 'at.status_atendimento', 'ts.id')
                    ->leftjoin('pessoas AS p1', 'at.id_assistido', 'p1.id')
                    ->leftjoin('pessoas AS p2', 'at.id_representante', 'p2.id')
                    ->leftJoin('pessoas AS p3', 'at.id_atendente_pref', 'p3.id')
                    ->leftJoin('pessoas AS p4', 'at.id_atendente', 'p4.id')
                    ->leftJoin('atendentes AS a', 'p4.id', 'a.id_pessoa')
                    ->leftJoin('tp_parentesco AS tp', 'at.parentesco', 'tp.id' )
                    ->leftJoin('tp_sexo AS tpsx', 'at.pref_tipo_atendente', 'tpsx.id')
                    ->leftJoin('tp_ddd AS tpd', 'p1.ddd', 'tpd.id')
                    ->orderBy('dh_chegada', 'ASC')
                    ->get();




         return view ('/recepcao-AFI/visualizar-atendimentos', compact('result'));

     }
*/







    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Atendente $atendente)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Atendente $atendente)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Atendente $atendente)
    {
        //
    }
}
