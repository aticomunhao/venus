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
    public function index(Request $request)
    {



            $medium = DB::table('medium AS m')
                ->leftJoin('tipo_mediunidade AS tm', 'm.id_tp_mediunidade', '=', 'tm.id')
                ->leftJoin('pessoas AS p', 'm.id_pessoa', '=', 'p.id')
                ->select('p.nome_completo', 'tm.tipo', 'm.status', 'm.id', 'm.id_pessoa', 'm.id_tp_mediunidade')
                ->orderBy('p.nome_completo', 'ASC');


            $nome = $request->nome;
            if ($nome) {
                $medium->where('p.nome_completo', 'like', "%$nome%");
            }


            $medium = $medium->orderBy('p.status', 'asc')
                ->orderBy('p.nome_completo', 'asc')
                ->paginate(50);


            $tipo_mediunidade = DB::table('tipo_mediunidade')->get();


            return view('medium.gerenciar-mediuns', compact('medium', 'tipo_mediunidade','nome'));
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


            DB::table('medium')
            ->where('id_pessoa', $request->input('id_pessoa'))
            ->delete();

        DB::table('medium')->insert([
            'id_tp_mediunidade' => $request->input('id_tp_mediunidade'),
            'id_pessoa'=> $request->input('id_pessoa'),
            'status'=> $request->has('status'),
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

            // $mediuns = DB::table('medium')->where('id',$id)->select('*')->first();
            // $medium = db::select('select * from medium');
            // $tipo_mediunidade=db::select('select * from tipo_mediunidade');
            // $pessoas= DB::select('select * from pessoas');

    //         $medium = DB::table('medium AS m')
    //         ->leftJoin('pessoas AS p','m.id_pessoa','p.id')
    //         ->select('m.id','p.nome_completo','m.status')
    //         ->get();
    //         $tipo_mediunidade=db::select('select * from tipo_mediunidade');


    //     return view('medium/editar-mediuns', compact('tipo_mediunidade','medium'));

    //     //
    // }

        $medium = DB::table('medium AS m')
            ->leftJoin('pessoas AS p', 'm.id_pessoa', 'p.id')
            ->select('m.id', 'p.nome_completo', 'm.id_pessoa', 'm.status', 'm.id_tp_mediunidade')
            ->get();
            $pessoas= DB::table('pessoas')->get();
        $tipo_mediunidade = DB::table('tipo_mediunidade')->get();

        return view('medium/editar-mediuns', compact('tipo_mediunidade', 'medium','pessoas'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        dd('medium');
        DB::table('medium')->where('id', $id)->update([
            'id_tp_mediunidade' => $request->input('id_tp_mediunidade'),
            'id_pessoa' => $request->input('id_pessoa'),
            'status'=>$request->input('status')
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
