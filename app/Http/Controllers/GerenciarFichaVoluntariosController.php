<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GerenciarFichaVoluntariosController extends Controller
{



    public function retornaCidades(String $id)
    {
        $cidadeDadosResidenciais = DB::table('tp_cidade')
            ->where('id_uf', $id)
            ->get();

        return response()->json($cidadeDadosResidenciais);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        return view('ficha-voluntarios.teste-camera');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $edit_associado = DB::table('associado as ass')
            ->leftJoin('pessoas AS p', 'ass.id_pessoa', 'p.id')
            ->leftJoin('tp_sexo', 'p.sexo', 'tp_sexo.id')
            ->leftJoin('endereco_pessoas AS endp', 'p.id', 'endp.id_pessoa')
            ->leftJoin('tp_uf', 'endp.id_uf_end', '=', 'tp_uf.id')
            ->leftJoin('tp_ddd', 'tp_ddd.id', '=', 'p.ddd')
            ->leftJoin('tp_cidade AS tc', 'endp.id_cidade', '=', 'tc.id_cidade')
            ->where('p.id', $id)
            ->select(
                'ass.nr_associado',
                'ass.id as ida',
                'p.id AS idp',
                'p.nome_completo',
                'p.cpf',
                'p.celular',
                'p.email',
                'p.idt',
                'p.sexo AS id_sexo',
                'p.ddd',
                'p.dt_nascimento',
                'tp_sexo.tipo AS nome_sexo',
                'tp_ddd.id AS tpd',
                'tp_ddd.descricao AS dddesc',
                'tp_uf.id AS tuf',
                'tp_uf.sigla AS ufsgl',
                'endp.cep',
                'endp.logradouro',
                'endp.numero',
                'endp.bairro',
                'endp.complemento',
                'endp.id_uf_end as id_uf',
                'tc.id_cidade',
                'tc.descricao AS nat',
            )
            ->orderBy('endp', 'DESC')
            ->first();


        $tpddd = DB::table('tp_ddd')->select('id', 'descricao')->get();
        $tpsexo = DB::table('tp_sexo')->select('id', 'tipo')->get();
        $tpcidade = DB::table('tp_cidade')->select('id_cidade', 'descricao')->get();
        $tpufidt = DB::table('tp_uf')->select('id', 'sigla')->get();


        return view('ficha-voluntarios.ficha-voluntarios', compact('edit_associado', 'tpddd', 'tpcidade', 'tpufidt', 'tpsexo'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, String $ida, String $idp)
    {
        $idEndereco = DB::table('endereco_pessoas')
            ->where('id_pessoa', $idp)
            ->orderByDesc('id') // Pegando o endereço mais recente
            ->value('id'); // Retorna o maior ID

        $endereco = DB::table('endereco_pessoas as ep')
            ->leftJoin('pessoas as p', 'ep.id_pessoa', 'p.id')
            ->where('p.id', $idp)
            ->count();

        $associado = DB::table('pessoas as p')
            ->leftJoin('associado as a', 'p.id', 'a.id_pessoa')
            ->where('p.id', $idp)
            ->whereNotNull('a.id')->count();

        $pessoa = DB::table('pessoas as p')->where('p.id', $idp)->count();

        if ($pessoa == 1 && $associado == 1 && $endereco == 0) {
            DB::table('pessoas as p')
                ->where('p.id', $idp)
                ->update([
                    'ddd' => $request->input('ddd'),
                    'celular' => $request->input('telefone'),
                    'email' => $request->input('email'),
                    'idt' => $request->input('idt'),
                    'sexo' => $request->input('sexo'),
                    'dt_nascimento' => $request->input('dt_nascimento'),
                    'status' => '1',
                ]);

            DB::table('endereco_pessoas as ep')
                ->insert([
                    'id_pessoa' => $idp,
                    'cep' => str_replace('-', '', $request->input('cep')),
                    'dt_inicio' =>  Carbon::now()->toDateString(),
                    'id_uf_end' => $request->input('uf_end'),
                    'id_cidade' => $request->input('cidade'),
                    'logradouro' => $request->input('logradouro'),
                    'numero' => $request->input('numero'),
                    'bairro' => $request->input('bairro'),
                    'complemento' => $request->input('complemento'),
                ]);

            app('flasher')->addSuccess('Foi incluido o endereço e atualizados os dados do associado!');

            return redirect('/gerenciar-pessoas');
        } elseif ($pessoa == 1 && $associado == 1 && $endereco > 0) {

            DB::table('pessoas as p')
                ->where('p.id', $idp)
                ->update([
                    'ddd' => $request->input('ddd'),
                    'celular' => $request->input('telefone'),
                    'email' => $request->input('email'),
                    'sexo' => $request->input('sexo'),
                    'dt_nascimento' => $request->input('dt_nascimento'),
                    'idt' => $request->input('idt')
                ]);

            if ($idEndereco) {
                DB::table('endereco_pessoas')
                    ->where('id', $idEndereco)
                    ->update([
                        'cep'         => $request->input('cep'),
                        'id_uf_end'   => $request->input('uf_end'),
                        'id_cidade'   => $request->input('cidade'),
                        'logradouro'  => $request->input('logradouro'),
                        'numero'      => $request->input('numero'),
                        'bairro'      => $request->input('bairro'),
                        'complemento' => $request->input('complemento'),
                    ]);
            }

            app('flasher')->addSuccess('Todos os dados do associado foram atualizados!');

            return redirect('/gerenciar-pessoas');
        }

        app('flasher')->addError('Ocorreu um erro inesperado, avise a ATI.');
        return redirect()->back()->withInput();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
