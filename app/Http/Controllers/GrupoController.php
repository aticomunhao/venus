<?php


namespace App\Http\Controllers;

use App\Models\Grupo;
use App\Models\Sala;
use Doctrine\DBAL\Driver\Mysqli\Exception\InvalidOption;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Spatie\FlareClient\Http\Exceptions\InvalidData;



class Grupocontroller extends Controller
{
    public function index(Request $request)
    {


            $grupo = DB::table('grupo AS g')
                ->select('g.id', 'g.nome', 'g.data_inicio', 'g.data_fim', 'g.status_grupo','g.id_tipo_motivo', 'tg.nm_tipo_grupo', 'tg.id AS idg' ,'ts.descricao as descricao1','tm.tipo')
                ->leftJoin('tipo_grupo AS tg', 'g.id_tipo_grupo', 'tg.id')
                ->leftJoin('tipo_status_grupo AS ts', 'g.status_grupo', 'ts.id')
                ->leftJoin('tipo_motivo AS tm', 'g.id_tipo_motivo', 'tm.id');


<<<<<<< Updated upstream
                $nome = $request->nome_pesquisa;

                if ($request->nome_pesquisa) {
                    $grupo->where('g.nome', 'ilike', "%$nome%");
                }
=======
            if ($request->nome_pesquisa) {
                $grupo->where('g.nome', 'ilike', "%$request->nome_pesquisa%");
            }
>>>>>>> Stashed changes

                $grupo = $grupo->orderBy('g.status_grupo', 'ASC')
                               ->orderBy('g.nome', 'ASC')
                               ->paginate(50);

                return view('grupos/gerenciar-grupos', compact('grupo'));
            }




    //




    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $grupos = DB::select('select * from grupo');
        $tipo_grupo = DB::select('select id as idg,nm_tipo_grupo from tipo_grupo ');
        // $tipo_tratamento = DB::select('select id, descricao from tipo_tratamento');
        $tipo_status_grupo = DB::select('select id as ids, descricao as descricao from tipo_status_grupo');
        $tipo_motivo = DB::select('select id id,tipo from tipo_motivo');




        return view('grupos/criar-grupos', compact('grupos','tipo_grupo','tipo_status_grupo','tipo_motivo'));

    }



    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $data = date("Y-m-d H:i:s");
        DB::table('grupo')->insert([
            'status_grupo' =>$request->input('status_grupo'),
            'nome' => $request->input('nome'),
            'data_inicio' => $data,
            'id_tipo_grupo' => $request->input('id_tipo_grupo'),
            // 'id_tipo_tratamento'=>$request->input('id_tipo_tratamento'),
            'id_tipo_motivo'=>$request->input('id_tipo_motivo'),

        ]);


        app('flasher')->addSuccess('O cadastro foi realizado com sucesso.');





        return redirect('gerenciar-grupos');
    }

// Supondo que você tenha um método no seu controlador para retornar a view



    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $grupo = DB::table('grupo AS g')
        ->leftJoin('tipo_grupo AS tg', 'g.id_tipo_grupo', 'tg.id')
        ->leftJoin('tipo_status_grupo AS ts', 'g.status_grupo', 'ts.id')
        ->leftJoin('tipo_motivo AS tm', 'g.id_tipo_motivo', 'tm.id')
        ->select('g.id', 'g.nome', 'g.data_inicio', 'g.data_fim', 'g.status_grupo','g.id_tipo_motivo', 'tg.nm_tipo_grupo','ts.descricao as descricao1','tm.tipo')->where('g.id', $id)
        ->get();
        $tipo_grupo = DB::table('tipo_grupo')->get();
        $tipo_status_grupo = DB::table('tipo_status_grupo')->select('descricao as descricao1','id')->get();
        $tipo_motivo = DB::table('tipo_motivo')->get();

        return view('grupos/visualizar-grupos', compact('grupo','tipo_grupo','tipo_status_grupo','tipo_motivo'));

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {



        $grupo = DB::table('grupo AS g')
        ->leftJoin('tipo_grupo AS tg', 'g.id_tipo_grupo', 'tg.id')
        ->leftJoin('tipo_status_grupo AS ts', 'g.status_grupo', 'ts.id')
        ->leftJoin('tipo_motivo AS tm', 'g.id_tipo_motivo', 'tm.id')
        ->select('g.id', 'g.nome', 'g.data_inicio', 'g.data_fim', 'g.status_grupo', 'tg.nm_tipo_grupo as nmg','ts.descricao as descricao1','g.id_tipo_grupo','g.status_grupo','g.id_tipo_motivo','tm.tipo')->where('g.id', $id)
        ->get();
        $tipo_grupo = DB::table('tipo_grupo')->get();
        $tipo_status_grupo = DB::table('tipo_status_grupo')->select('descricao as descricao1','id')->get();
        $tipo_motivo = DB::table('tipo_motivo')->get();


        return view('grupos/editar-grupos', compact('grupo','tipo_grupo','tipo_status_grupo','tipo_motivo'));

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
{

    DB::table('grupo')->where('id', $id)->update([
        'nome' => $request->input('nome'),
        'data_inicio' => $request->input('data_inicio'),
        'data_fim' => $request->input('data_fim'),
        'id_tipo_grupo' => $request->input('id_tipo_grupo'),
        'status_grupo' => $request->input('status_grupo'),
        'id_tipo_motivo' => $request->input('id_tipo_motivo')


    ]);

    app('flasher')->addSuccess("Alterado com Sucesso");

    return redirect('gerenciar-grupos');
}


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)

    {

        $ids = DB::table('grupo')->select('nome')->where('id', $id)->get();
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
        return redirect('/gerenciar-grupos');
    }

}
