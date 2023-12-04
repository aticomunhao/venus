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
                ->select('g.id', 'g.nome', 'g.h_inicio', 'g.h_fim', 'g.max_atend', 'g.status_grupo', 'tg.nm_tipo_grupo', 'tg.id AS idg', 'tt.descricao')
                ->leftJoin('tipo_grupo AS tg', 'g.id_tipo_grupo', 'tg.id')
                ->leftJoin('tipo_tratamento AS tt', 'g.id_tipo_tratamento', 'tt.id');

            $nome = $request->nome_pesquisa;

            if ($request->nome_pesquisa) {
                $grupo->where('g.nome', 'like', "%$request->nome_pesquisa%");
            }


            $grupo = $grupo->orderBy('g.nome', 'ASC')->paginate(50);

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
        $tipo_tratamento = DB::select('select id, descricao from tipo_tratamento');


        return view('grupos/criar-grupos', compact('grupos','tipo_grupo','tipo_tratamento'));

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        DB::table('grupo')->insert([
            'status_grupo' =>$request->input('status_grupo'),
            'nome' => $request->input('nome'),
            'h_inicio' => $request->input('h_inicio'),
            'h_fim' => $request->input('h_fim'),
            'max_atend' => $request->input('max_atend'),
            'id_tipo_grupo' => $request->input('id_tipo_grupo'),
            'id_tipo_tratamento'=>$request->input('id_tipo_tratamento')

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
        ->leftJoin('tipo_tratamento AS tt', 'g.id_tipo_tratamento', 'tt.id')
        ->select('g.id', 'g.nome', 'g.h_inicio', 'g.h_fim', 'g.max_atend', 'g.status_grupo', 'tg.nm_tipo_grupo', 'tt.descricao')->where('g.id', $id)
        ->get();
        $tipo_grupo = DB::table('tipo_grupo')->get();
        $tipo_tratamento = DB::table('tipo_tratamento')->get();

        return view('grupos/visualizar-grupos', compact('grupo','tipo_grupo','tipo_tratamento'));

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {


        $grupo = DB::table('grupo AS g')
        ->leftJoin('tipo_grupo AS tg', 'g.id_tipo_grupo', 'tg.id')
        ->leftJoin('tipo_tratamento AS tt', 'g.id_tipo_tratamento', 'tt.id')
        ->select('g.id', 'g.nome', 'g.h_inicio', 'g.h_fim', 'g.max_atend', 'g.status_grupo', 'tg.nm_tipo_grupo', 'tt.descricao')->where('g.id', $id)
        ->get();
        $tipo_grupo = DB::table('tipo_grupo')->get();
        $tipo_tratamento = DB::table('tipo_tratamento')->get();


        return view('grupos/editar-grupos', compact('grupo','tipo_grupo','tipo_tratamento'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
{






    DB::table('grupo')->where('id', $id)->update([
        'nome' => $request->input('nome'),
        'h_inicio' => $request->input('h_inicio'),
        'h_fim' => $request->input('h_fim'),
        'max_atend' => $request->input('max_atend'),
        'id_tipo_grupo' => $request->input('id_tipo_grupo'),
        'status_grupo' => $request->input('status_grupo'),
        'id_tipo_tratamento' => $request->input('id_tipo_tratamento')

    ]);

    app('flasher')->addSuccess("Alterado com Sucesso");

    return redirect('gerenciar-grupos');
}


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        DB::table('grupo')->where('id', $id)->delete();


        app('flasher')->addError('Excluido com sucesso.');
        return redirect('/gerenciar-grupos');
    }

}
