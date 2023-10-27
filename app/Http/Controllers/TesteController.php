<?php

namespace App\Http\Controllers;

use App\Models\Atendente;
use App\Models\Grupo;
use App\Models\Pessoa;
use App\Models\Teste;
use App\Models\Tp_cidade;
use App\Models\Tp_nacionalidade;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class TesteController extends Controller
{


    /**
     * Display a listing of the resource.
     */
    public function index()
    {


        $search = 'ma';
        $pessoasAtendentes = Atendente::whereBelongsTo(Pessoa::where('nome_completo', 'like','%'.$search.'%')->get())->get()->toArray();
        dump($pessoasAtendentes);

        $pessoasAtendentes = Atendente::whereHas('pessoa', function ($query) use ($search) {
            $query->where('nome_completo', 'ilike', '%'.$search.'%');
        })->get()->toArray();
        dump($pessoasAtendentes);

        $atendenteDisponivel = Atendente::whereBelongsTo(Pessoa::find(6)->nome_completo, 'Maria da Penha')->get();
        dump($atendenteDisponivel);

        // $atendentes = Atendente::whereHas('pessoa', function ($query) use ($search)
        // {
        //     $query->where('nome_completo', 'like','%'.$search.'%');
        // })->get();
        // dump($search,$atendentes);



        return view('tester');
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
    public function show(Teste $teste)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Teste $teste)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Teste $teste)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Teste $teste)
    {
        //
    }

    public function QueriesStudies()
    {
        // Foreing Fields Pessoa:
        // $table->foreign('nacionalidade')->references('id')->on('tp_nacionalidade');
        // $table->foreign('naturalidade')->references('id_cidade')->on('tp_cidade');
        // $table->foreign('orgao_expedidor')->references('id')->on('tp_orgao_exp');
        // $table->foreign('status')->references('id')->on('tipo_status_pessoa');
        // $table->foreign('uf_idt')->references('id')->on('tp_uf');
        // $table->foreign('uf_natural')->references('id')->on('tp_uf');"

        // OneToOne
        // Pessoa Foreign DATA:
        // $data = Pessoa::all()->first()->tp_nacionalidade->local; // select * from "tp_nacionalidade" where "tp_nacionalidade"."id" = 3 limit
        // $data = Pessoa::all()->first()->tp_cidade->descricao;
        // $data = Pessoa::all()->first()->tp_orgao_exp->descricao;
        // $data = Pessoa::all()->first()->tipo_status_pessoa->status;
        // $data = Pessoa::all()->first()->tp_uf->sigla;
        // $data = Pessoa::all()->first()->tp_uf->extenso;

        // Simplifing Belogs to queries:

        //dd($pessoa->first());

        $pessoa = collect([
            [
                'id' => 1,
                'name' => 'Jão',
                'email' => 'jão@email.com'
            ],
            [
                'id' => 2,
                'name' => 'Maria',
                'email' => 'maria@email.com'
            ],
            [
                'id' => 6,
                'name' => 'Pedro',
                'email' => 'pedro@email.com'
            ]
        ]);


        dump($pessoa);
        dump($pessoa[0]);
        dump($pessoa[0]['id']);
        dump($pessoa->first()['id']);
        dump($pessoa->get(0)['id']);
        $pessoaArr = $pessoa->toArray();
        dump($pessoaArr);
        dump($pessoaArr[0]['id']);
        $id = $pessoa[0]['id'];



        $pessoaFromPessoas = Atendente::all()->first()->pessoa; dump($pessoa);
        // When querying for the children of a "belongs to" relationship, you may:
        $atendente = Atendente::where('id_pessoa', $pessoa[2]['id'])->first()->toArray(); dump($atendente);
        // Automatically determine the proper relationship and foreign key for the given model:
        $atendente = Atendente::whereBelongsTo($pessoaFromPessoas)->first()->toArray(); dump($atendente);

        // OneToMany
        //foreign keys from Tp_cidade on pessoas:
        // $table->foreign('naturalidade')->references('id_cidade')->on('tp_cidade');

        // Tp_cidade Foreing DATA:
        //  $pessoasFromTp_cidadeID3 = Tp_cidade::where('id_cidade',3)->first()->pessoa; // ↓
        // // Select * from "tp_cidade" where "id_cidade" = 3 limit 1 &&
        // // Select * from "pessoas" where "pessoas"."naturalidade" = 3 and "pessoas"."naturalidade" is not null
        // foreach ($pessoasFromTp_cidadeID3 as $pessoaFromTp_cidadeID3) {
        //     $ListaNomesDeBrasileiros = $pessoaFromTp_cidadeID3->nome_completo;
        //     dump($ListaNomesDeBrasileiros);
        // }

        // Tp_cidade Foreing DATA: Adding further constraints to the relationship query
        // $pessoasFromTp_cidadeID3 = Tp_cidade::where('id_cidade',3)->first()->pessoa()
        //             ->where('orgao_expedidor', '2')
        //             ->first()
        //             ->toArray();
        //             dump($pessoasFromTp_cidadeID3);

        // Teste One to Many:
        // $data = Tp_nacionalidade::all();
        //  foreach ($data as $nomes) {
        //     $nome = $nomes->pessoa->nome_completo;
        //     dump($nome) ;
        //  }

        // Treinando Queries :
        //$pessoa = Pessoa::find(6)->atendente; // select * from "pessoas" where "pessoas"."id" = 6 limit 1

        //$atendente = Atendente::find(6)->pessoa->nome_completo;
        // select * from "atendentes" where "atendentes"."id" = 6 limit 1 &&
        // select * from "pessoas" where "pessoas"."id" = 6 limit 1
        // -> "Maria da Penha" // trouxe o nome_completo do atendente.id(6)

        // $user = Atendente::all()->where('status_atendente',1)->get(0)->pessoa->nome_completo; // get(0) == first() ;
        // select * from "atendentes" &&
        // select * from "pessoas" where "pessoas"."id" = 6 limit 1


        //$atendente = Atendente::find(6)->id_pessoa;
        //$atendente = Atendente::all();


        //$atendente = Atendente::find(1)->pessoas->nome_completo;
        //$pessoa = Pessoa::find(6)->atendendes; // null?

        //dump($pessoa);
        // dump($atendente);
    }

    public function referenceCodes(){

        $Atendente = Pessoa::find(1)->atendente;

        // Metodo Query - Resgatando todas as Rows de "pessoas"
         $pessoas = DB::select("SELECT * from pessoas");

        // Metodo Query - Regatando dados tabela Atendente relacionamento Pessoa OneToOne

            $atendentes = DB::select("SELECT
            p.nome_completo,
            g.nome as nome_grupo,
            a.status_atendente

            FROM atendentes a
            LEFT JOIN pessoas p ON a.id_pessoa = p.id
            LEFT JOIN grupos g on a.id_grupo = g.id

            ");
    }
}
