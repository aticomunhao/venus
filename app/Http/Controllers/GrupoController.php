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
    public function index()
    {
        $grupo = DB::select('select * from grupo');
        return view('grupos/gerenciar-grupos', compact('grupo'));
    }




    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $grupos = DB::select('select * from grupo');
        return view('grupos.criar-grupos', compact('grupos'));
    
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
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        DB::table('grupo')->where('id', $id)->update([
            'id_tipo_grupo' => $request->input('id_tipo_grupo'),
            'status_grupo' => $request->input('status_grupo'),
            'id_tipo_tratamento' => $request->input('id_tipo_tratamento'),

        ]);

        app('flasher')->addSuccess("Alterado com Sucesso");

        return redirect('gerenciar-grupos');

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
