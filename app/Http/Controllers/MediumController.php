<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Carbon;
use function Laravel\Prompts\select;

class MediumController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        $medium=db::select("select pessoas.nome_completo,
        tipo_mediunidade.tipo,medium.id,medium.id_pessoa,medium.id_tp_mediunidade
        from medium
        join tipo_mediunidade
        on tipo_mediunidade.id = medium.id_tp_mediunidade
        join pessoas
        on pessoas.id = medium.id_pessoa
        ORDER BY nome_completo ASC");


        



        return view('medium/gerenciar-mediuns', compact('medium'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {

        $medium = DB::select('select *from medium');
        $tipo_mediunidade = DB::select('select *from tipo_mediunidade');
        $pessoas = DB::select('select *from pessoas');


        return view('medium/criar-mediuns', compact('medium', 'tipo_mediunidade', 'pessoas'));
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        DB::table('medium')->insert([

            'id_tp_mediunidade' => $request->input('id_tp_mediunidade'),
            'id_pessoa'=> $request->input('id_pessoa'),
        ]);

        app('flasher')->addSuccess("Cadastrado com Sucesso");
        return redirect('gerenciar-mediuns');
    }
    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $mediuns = DB::table('medium')->where('id',$id)->select('*')->first();
        $medium = db::select('select * from medium');
        $tipo_mediunidade=db::select('select * from tipo_mediunidade');
        $pessoas= DB::select('select * from pessoas');





        return view('medium/visualizar-mediuns', compact('mediuns','tipo_mediunidade','medium','pessoas'));

        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit( $id)
    {

        $mediuns = DB::table('medium')->where('id',$id)->select('*')->first();
        $medium = db::select('select * from medium');
        $tipo_mediunidade=db::select('select * from tipo_mediunidade');
        $pessoas= DB::select('select * from pessoas');





        return view('medium/editar-mediuns', compact('mediuns','tipo_mediunidade','medium','pessoas'));

        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {

        DB::table('medium')->where('id', $id)->UPDATE([
            'id_tp_mediunidade' => $request->input('id_tp_mediunidade'),
            'id_pessoa'=> $request->input('id_pessoa')
        ]);

        app('flasher')->addSuccess("Alterado com Sucesso");

        return redirect('gerenciar-mediuns');



    }
    //


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {

        $ids=DB::table('medium')->select('id')->where('id', $id)->get();
        $teste=session()->get('usuario');

        $verifica=DB::table('historico_venus') -> where('fato',$id)->count('fato');


        $data = date("Y-m-d H:i:s");






        DB::table('historico_venus')->insert([

            'id_usuario' => session()->get('usuario.id_usuario'),
            'data' => $data,
            'fato' => 0,
            'obs'=>$ids
        ]);

        DB::table('medium')->where('id', $id)->delete();


        app('flasher')->addError('Excluido com sucesso.');
        return redirect('/gerenciar-mediuns');

        //
    }
}
