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
            ->leftJoin('tipo_grupo AS tg', 'g.id_tipo_grupo', 'tg.id')
            ->leftJoin('tipo_status_grupo AS ts', 'g.status_grupo', 'ts.id')
            ->leftJoin('tipo_motivo AS tm', 'g.id_motivo_inativacao', 'tm.id')
            ->leftJoin('setor AS st', 'g.id_setor', 'st.id')
            ->select(
                'g.id',
                'g.nome',
                'g.data_inicio',
                'g.data_fim',
                'g.status_grupo',
                'g.id_motivo_inativacao',
                'tg.nm_tipo_grupo',
                'tg.id AS idg',
                'ts.descricao as descricao1',
                'tm.tipo',
                'g.id_setor',
                'st.nome AS nm_setor'
            );
         
            


        $nome = $request->nome_pesquisa;

        if ($request->nome_pesquisa) {
            $grupo->where('g.nome', 'ilike', "%$nome%");
        }

        $grupo = $grupo->orderBy('g.status_grupo', 'ASC')
            ->orderBy('g.nome', 'ASC')
            ->paginate(50);

        return view('grupos/gerenciar-grupos', compact('grupo'));
    }







    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $grupos = DB::select('select * from grupo');
        $tipo_grupo = DB::select('select id as idg,nm_tipo_grupo from tipo_grupo order by nm_tipo_grupo asc');
        $tipo_status_grupo = DB::select('select id as ids, descricao as descricao from tipo_status_grupo');
        $tipo_motivo = DB::select('select id id,tipo from tipo_motivo');
        $setor = DB::select('select id, nome as nm_setor from setor order by nome asc');




        return view('grupos/criar-grupos', compact('grupos', 'tipo_grupo', 'tipo_status_grupo', 'tipo_motivo', 'setor'));
    }



    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $data = date("Y-m-d H:i:s");
        DB::table('grupo')->insert([
            'status_grupo' => $request->input('status_grupo'),
            'nome' => ucwords(trans($request->input('nome'))),
            'data_inicio' => $data,
            'id_tipo_grupo' => $request->input('id_tipo_grupo'),
            'id_motivo_inativacao' => $request->input('id_motivo_inativacao'),
            'id_setor' => $request->input('id_setor'),

        ]);


        app('flasher')->addSuccess('O cadastro foi realizado com sucesso.');





        return redirect('gerenciar-grupos');
    }





    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $grupo = DB::table('grupo AS g')
            ->leftJoin('tipo_grupo AS tg', 'g.id_tipo_grupo', 'tg.id')
            ->leftJoin('tipo_status_grupo AS ts', 'g.status_grupo', 'ts.id')
            ->leftJoin('tipo_motivo AS tm', 'g.id_motivo_inativacao', 'tm.id')
            ->leftJoin('setor AS st', 'g.id_setor', 'st.id')
            ->select('g.id', 'g.nome', 'g.data_inicio', 'g.data_fim', 'g.status_grupo', 'g.id_motivo_inativacao', 'tg.nm_tipo_grupo', 'ts.descricao as descricao1', 'tm.tipo', 'g.id_setor', 'st.nome AS nm_setor')->where('g.id', $id)
            ->get();
        $tipo_grupo = DB::table('tipo_grupo')->get();
        $tipo_status_grupo = DB::table('tipo_status_grupo')->select('descricao as descricao1', 'id')->get();
        $tipo_motivo = DB::table('tipo_motivo')->get();
        $setor = DB::table('setor')->get();

        return view('grupos/visualizar-grupos', compact('setor', 'grupo', 'tipo_grupo', 'tipo_status_grupo', 'tipo_motivo'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {



        $grupo = DB::table('grupo AS g')
            ->leftJoin('tipo_grupo AS tg', 'g.id_tipo_grupo', 'tg.id')
            ->leftJoin('tipo_status_grupo AS ts', 'g.status_grupo', 'ts.id')
            ->leftJoin('tipo_motivo AS tm', 'g.id_motivo_inativacao', 'tm.id')
            ->leftJoin('setor AS st', 'g.id_setor', 'st.id')
            ->select('g.id', 'g.nome', 'g.data_inicio', 'g.data_fim', 'g.status_grupo', 'tg.nm_tipo_grupo as nmg', 'ts.descricao as descricao1', 'g.id_tipo_grupo', 'g.status_grupo', 'g.id_motivo_inativacao', 'tm.tipo', 'g.id_setor', 'st.nome AS nm_setor')->where('g.id', $id)
            ->get();
        $tipo_grupo = DB::table('tipo_grupo')->get();
        $tipo_status_grupo = DB::table('tipo_status_grupo')->select('descricao as descricao1', 'id')->get();
        $tipo_motivo = DB::table('tipo_motivo')->get();
        $setor = DB::table('setor')->get();


        return view('grupos/editar-grupos', compact('setor', 'grupo', 'tipo_grupo', 'tipo_status_grupo', 'tipo_motivo'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
     {
    

// Obter a data atual
$now = Carbon::now()->format('Y-m-d');

// Atualizar o registro do grupo
DB::table('grupo')->where('id', $id)->update([
    'nome' => $request->input('nome'),
    'data_inicio' => $request->input('data_inicio'),
    'data_fim' => $request->input('data_fim'),
    'id_tipo_grupo' => $request->input('id_tipo_grupo'),
    'status_grupo' => $request->input('status_grupo'),
    'id_motivo_inativacao' => $request->input('id_motivo_inativacao'),
    'id_setor' => $request->input('id_setor')
]);

// Verificar se o status do grupo foi alterado para inativo
if ($request->input('status_grupo') == 2) {
    // Atualizar o cronograma com o status de reunião inativo e a data de término
    DB::table('cronograma as cro')
        ->where('cro.id_grupo', $id)
        ->update([
            'status_reuniao' => 2,
            'data_fim' => $now
        ]);
}

app('flasher')->addSuccess("Alterado com Sucesso");

return redirect('gerenciar-grupos');

     }

    // public function update(Request $request, $id)
    // {

    //     $now =  Carbon::now()->format('Y-m-d');
    //     DB::table('grupo')->where('id', $id)->update([
    //         'nome' => $request->input('nome'),
    //         'data_inicio' => $request->input('data_inicio'),
    //         'data_fim' => $request->input('data_fim'),
    //         'id_tipo_grupo' => $request->input('id_tipo_grupo'),
    //         'status_grupo' => $request->input('status_grupo'),
    //         'id_motivo_inativacao' => $request->input('id_motivo_inativacao'),
    //         'id_setor' => $request->input('id_setor')


    //     ]);

    //     if ($request->input('status_grupo') == 2) {


    //         DB::table('cronograma as cro')
    //             ->where('cro.id_grupo', $id)
    //             ->update([
    //                 'status_reuniao' => 2,
    //                 'data_fim' => $now
    //             ]);
    //     }

    //     app('flasher')->addSuccess("Alterado com Sucesso");

    //     return redirect('gerenciar-grupos');
    // }


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

        DB::table('cronograma')->where('id_grupo', $id)->delete();






        DB::table('grupo')->where('id', $id)->delete();





        app('flasher')->addError('Excluido com sucesso.');
        return redirect('/gerenciar-grupos');
    }
}
