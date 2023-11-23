<?php

namespace App\Http\Controllers;

use App\Models\Pessoa;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Filament\Support\RawJs;

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
                    ->select('p.id AS idp', 'nome_completo', 'p.cpf', 'tps.tipo', 'dt_nascimento', 'sexo', 'email', 'ddd', 'celular', 'tpsp.id AS idtps', 'p.status', 'tpsp.status AS nmstatus', 'd.id as did', 'd.descricao as ddesc')
                    ->leftjoin('tipo_status_pessoa AS tpsp', 'p.status', 'tpsp.id')
                    ->leftJoin('tp_sexo AS tps', 'p.sexo', 'tps.id')
                    ->leftJoin('tp_ddd AS d', 'p.ddd', 'd.id');

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

        $pessoa = $pessoa->orderBy('p.status','asc')->orderBy('p.nome_completo', 'asc')->paginate(50);

        //dd($pessoa);
        $stap = DB::select("select
                        id as ids,
                        status
                        from tipo_status_pessoa t
                        ");

        $soma = DB::table('pessoas')->count();



        return view ('/pessoal/gerenciar-pessoas', compact('pessoa', 'stap', 'soma', 'ddd', 'sexo'));

    }

    public function store()
    {
        $ddd = DB::select('select id, descricao from tp_ddd');

        $sexo = DB::select('select id, tipo from tp_sexo');

        return view ('/pessoal/incluir-pessoa', compact('ddd', 'sexo'));
    }


    public function create(Request $request)
    {

        $today = Carbon::today()->format('Y-m-d');

        $cpf = $request->cpf;

        $vercpf = DB::table('pessoas')->where('cpf', $cpf)->count();

        //dd($vercpf);

        try{
            $validated = $request->validate([
                //'telefone' => 'required|telefone',
                'cpf' => 'required|cpf',
                //'cnpj' => 'required|cnpj',
                // outras validações aqui
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {

            app('flasher')->addError('Este CPF não é válido');

            return redirect()->back()->withInput();
            //dd($e->errors());
        }

        if ($vercpf > 1) {


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

        DB::table('historico_venus')->insert([
            'id_usuario' => session()->get('usuario.id_usuario'),
            'data' => $today,
            'fato' => 2,
            'pessoa' => $request->input('nome')
        ]);


        }

        app('flasher')->addSuccess('O cadastro foi realizado com sucesso');

        return redirect('/gerenciar-pessoas');
    }




    public function edit($idp)
    {
        $ddd = DB::select('select id, descricao from tp_ddd');

        $sexo = DB::select('select id, tipo from tp_sexo');

        $status_p = DB::select('select id, status from tipo_status_pessoa');

        $lista = DB::select("select p.id as idp, p.nome_completo, p.ddd, p.dt_nascimento, p.sexo, p.email, p.cpf, p.celular, tps.id AS sexid, tps.tipo, d.id AS did, d.descricao as ddesc from pessoas p
        left join tp_sexo tps on (p.sexo = tps.id)
        left join tp_ddd d on (p.ddd = d.id)
        where p.id = $idp");

        return view ('/pessoal/editar-pessoa', compact('lista', 'sexo', 'ddd', 'status_p'));

    }

    public function show()
    {

    }


    public function update(Request $request, $idp)
    {
        $today = Carbon::today()->format('Y-m-d H:m:s');

        $cpf = $request->cpf;

        $vercpf = DB::table('pessoas')->where('cpf', $cpf)->count();


        try{
            $validated = $request->validate([
                //'telefone' => 'required|telefone',
                'cpf' => 'required|cpf',
                //'cnpj' => 'required|cnpj',
                // outras validações aqui
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {

            app('flasher')->addError('Este CPF não é válido');

            return redirect()->back()->withInput();
            //dd($e->errors());
        }

        if ($vercpf > 1) {


            app('flasher')->addError('Existe outro cadastro usando este número de CPF');

            return redirect()->back()->withInput();
        }
        else
        {

        DB::table('pessoas AS p')->where('p.id', $idp)->update([
                'nome_completo' => $request->input('nome'),
                'cpf' => $request->input('cpf'),
                'dt_nascimento' => $request->input('dt_nasc'),
                'sexo' => $request->input('sex'),
                'ddd' => $request->input('ddd'),
                'celular' => $request->input('celular'),
                'email' => $request->input('email'),
                'status' => $request->input('status')
        ]);

        //dd($pessoa);
        DB::table('historico')->insert([
            'id_usuario' => session()->get('usuario.id_usuario'),
            'data' => $today,
            'fato' => 3,
            'pessoa' => $idp
        ]);


        app('flasher')->addSuccess('O cadastro da pessoa foi alterado com sucesso');

        return redirect('/gerenciar-pessoas');
        }

    }


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
           DB::delete('delete from pessoas where id = ?', [$idp]);

           DB::table('historico')->insert([
            'id_usuario' => session()->get('usuario.id_usuario'),
            'data' => $data,
            'fato' => 1,
            'pessoa' => $pessoa
        ]);


            app('flasher')->addSuccess('O cadastro da pessoa foi excluido com sucesso.');

            return redirect ('/gerenciar-pessoas');




    }
    }
}
