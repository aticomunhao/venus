<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Carbon;

class GerenciarDirigentesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        $dirigente = DB::table('dirigentes as drg')
        ->select('drg.id','p.nome_completo','p.cpf')
        ->leftJoin('pessoas as p', 'drg.id_pessoa', '=', 'p.id');

        $pesquisaNome = $request->input('nome');
        if($request->nome){

            $dirigente =   $dirigente->where('p.nome_completo', 'ilike', "%$request->nome%");

        }

        $pesquisaCpf = $request->input('cpf');
        if($request->cpf){

            $dirigente = $dirigente->where('p.cpf', 'ilike', "%$request->cpf%");

        }

        $conta = $dirigente->count();
        $dirigente = $dirigente->orderBy('p.nome_completo')->get();

        return view('dirigentes.gerenciar-dirigentes', compact('dirigente', 'pesquisaNome', 'pesquisaCpf', 'conta'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {

        $pessoas = DB::table('pessoas')->get();
        $grupo = DB::table('grupo')->get();


        return view('dirigentes.incluir-dirigentes', compact('pessoas', 'grupo'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

         //Com tratamento de erro inicialzia a transação
         DB::beginTransaction();
         try{

             $data = date('Y-m-d H:i:s');

             $id_pessoa = $request->input('id_pessoa');
             $selectedGroups = $request->input('id_grupo');

             //Testa se os valores de ID pessoa são validos
             if (!is_numeric($id_pessoa) || $id_pessoa <= 0) {
                 app('flasher')->addError('ID de pessoa inválido.');
                 return redirect()->back();
             }
                 //insere um novo dirigente
               $dirigenteID = DB::table('dirigentes')->insertGetId([
                 'id_pessoa' =>  $id_pessoa
             ]);


             foreach ($selectedGroups as $groupId) {
                DB::table('dirigentes_grupo')->insert([
                    'id_dirigente' => $dirigenteID,
                    'id_grupo' => (int) $groupId,
                    'dt_inicio' => $data,
                ]);
            }



             app('flasher')->addSuccess('O cadastro foi realizado com sucesso.');

 DB::commit();
 }

 catch(\Exception $e){

             //Retorna uma mensagem flasher com o código do erro
             app('flasher')->addError("Houve um erro inesperado: #" . $e->getCode( )) ;
             DB::rollBack();

         }



         return redirect('gerenciar-dirigentes');




    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {


        $dirigente = DB::table('dirigentes as drg')
        ->select('drg.id','p.nome_completo')
        ->leftJoin('pessoas as p', 'drg.id_pessoa', '=', 'p.id')
        ->where('drg.id', $id)->first();

        $grupos = DB::table('dirigentes_grupo as dg')
        ->leftJoin('grupo as gr', 'dg.id_grupo', 'gr.id')
        ->select('gr.nome')
        ->where('dg.id_dirigente', $id)
        ->get();





        return view('dirigentes.visualizar-dirigentes', compact('grupos', 'dirigente'));
    }


    public function edit(string $id)
    {




        $dirigente = DB::table('dirigentes as dr')->select('dr.id', 'p.nome_completo')->leftJoin('pessoas as p', 'id_pessoa', 'p.id')->where('dr.id', $id)->first();

        $grupo = DB::table('grupo')->get();



        $selectedGroups = DB::table('dirigentes_grupo')->select('id_grupo as id')->where('id_dirigente', $id)->get();

            $info = [];
            foreach ($selectedGroups as $gr) {
                $info[] = $gr;
            }



        return view('dirigentes.editar-dirigentes', compact('grupo', 'dirigente', 'info'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {



        DB::table('dirigentes as drg')
        ->where('drg.id', $id)->
        update([
            'id_grupo' => $request->input('id_grupo')
        ]);

        return redirect('gerenciar-dirigentes');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {

        DB::table('dirigentes')->where('id', $id)->delete();

        return back();

    }
}
