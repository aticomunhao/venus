<?php

namespace App\Http\Controllers;

use App\Models\Pessoa;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class PessoaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        $ddd = DB::select('select id, descricao from tp_ddd');

        $sexo = DB::select('select id, tipo from tp_sexo');
        
        $pessoa = DB::table('pessoas AS p')
                    ->select('p.id AS idp', 'nome_completo', 'p.cpf', 'tps.tipo', 'dt_nascimento', 'sexo', 'email', 'ddd', 'celular', 'tpsp.id AS idtps', 'p.status', 'tpsp.status AS nmstatus')
                    ->leftjoin('tipo_status_pessoa AS tpsp', 'p.status', 'tpsp.id')
                    ->leftJoin('tp_sexo AS tps', 'p.sexo', 'tps.id');
                    
                    
        $nome = $request->nome;   
                   
        if ($request->nome) {
                $pessoa->where('p.nome_completo', 'like', "%$request->nome%");
        }

        $cpf = $request->cpf;

        if ($request->cpf) {
                $pessoa->where('p.cpf', $request->cpf);
        }
        
        $status = $request->status; 

        if ($request->status) {
                $pessoa->where('p.status', $request->status);
        }
        
        $pessoa = $pessoa->orderBy('p.status','asc', 'p.nome_completo', 'asc')->paginate(50);

        //dd($pessoa);
        $stap = DB::select("select
                        id as ids,
                        status
                        from tipo_status_pessoa t
                        ");
 
        $soma = DB::table('pessoas')->count();



        return view ('/pessoal/gerenciar-pessoas', compact('pessoa', 'stap', 'soma', 'ddd', 'sexo'));

    }

    
    public function create(Request $request)
    {

        $today = Carbon::today()->format('Y-m-d');

        $cpf = $request->cpf;

        $vercpf = DB::table('pessoas')->where('cpf', $cpf)->count();

        //dd($vercpf);

        if ($vercpf > 0) {


            app('flasher')->addError('Existe outro cadastro usando este número de CPF');

            return redirect()->back()->withInput();
        }
        else
        {

        DB::table('pessoas')->insert([
                
            'nome_completo' => $request->input('nome'),
            'cpf' => $request->input('cpf'),
            'dt_nascimento' => $request->input('dt_na'),
            'sexo' => $request->input('sex'),
            'ddd' => $request->input('ddd'),
            'celular' => $request->input('celular'),
            'email' => $request->input('email'),
            'status' => 1

        ]);

        $pessoa = DB::table('pessoas')->max('id');

        DB::table('historico')->insert([
            'id_usuario' => 1,
            'data' => $today,
            'fato' => "Incluiu Pessoa",
            'id_pessoa' => $request->input('nome') 
        ]);


        }

        app('flasher')->addSuccess('O cadastro foi realizado com sucesso');

        return redirect('/gerenciar-pessoas');
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
    public function show(Pessoa $pessoa)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Pessoa $pessoa)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Pessoa $pessoa)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($idp)
    {
        $data = date("Y-m-d H:i:s");

        $pessoa = DB::table('pessoas')->select('nome_completo')->where('id', $idp)->get();
        
        $funcionario = DB::table('funcionarios')
        ->where('id_pessoa', $idp)
        ->count('id_pessoa');

        $assistido = DB::table('atendimentos')
        ->where('id_assistido', $idp)
        ->count('id_assistido');
        
        //dd($assistido);
        
        if ($funcionario > 0){     
                    
            app('flasher')->addError('Essa pessoa não pode ser excluída porque é um funcionário.');
            return redirect ('/gerenciar-pessoas');
        
        }if($assistido > 0){     
                    
                app('flasher')->addError('Essa pessoa não pode ser excluída porque passou por atendimento.');
                return redirect ('/gerenciar-pessoas');

        }else{

            //dd($pessoa);
            DB::table('historico')->insert([
                'id_usuario' => 1,
                'data' => $data,
                'fato' => "Excluiu pessoa",
                'pessoa' => $pessoa
            ]);

            DB::delete('delete from pessoas where id = ?', [$idp]);

            

            app('flasher')->addSuccess('O cadastro da pessoa foi excluido com sucesso.');
            
            return redirect ('/gerenciar-pessoas');




    }


    
        
    }
}
