<?php

namespace App\Http\Controllers;

use App\Models\Atendente;
use App\Models\Grupo;
use App\Models\Pessoa;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class AtendenteController extends Controller
{
    public function index(Request $request)
    {
        // Recupera dados do banco de dados
        $ddd = DB::select('select id, descricao from tp_ddd');
        $grupos = DB::select('select id, nome from grupo order by nome');
        $sexo = DB::select('select id, tipo from tp_sexo');

        // Constrói a query para atendente
        $atendente = DB::table('atendentes AS ad')
            ->select(
                'ad.id',
                'p.id AS idp',
                'p.nome_completo',
                'p.cpf',
                'tps.tipo',
                'ag.id_grupo',
                'p.cpf',
                'p.ddd',
                'p.email',
                'p.celular',
                'ag.id_atendente',
                'ag.dt_inicio',
                'ag.dt_fim',
                'ag.motivo',
                'ag.dt_fim',
                'p.dt_nascimento',
                'p.sexo',
                'p.email',
                'p.ddd',
                'p.celular',
                'tsp.id AS idtps',
                'p.status',
                'tsp.tipo AS tpsta',
                'd.id as did',
                'd.descricao as ddesc',
                'p.motivo_status',
                'g.nome AS gnome'
            )
            ->leftJoin('pessoas AS p', 'ad.id_pessoa', '=', 'p.id')
            ->leftJoin('atendente_grupo AS ag', 'ag.id_atendente', '=', 'ad.id')
            ->leftJoin('tipo_status_pessoa AS tsp', 'p.status', '=', 'tsp.id')
            ->leftJoin('tp_sexo AS tps', 'p.sexo', '=', 'tps.id')
            ->leftJoin('tp_ddd AS d', 'p.ddd', '=', 'd.id')
            ->leftJoin('grupo AS g', 'ag.id_grupo', '=', 'g.id');

        // Aplica filtros
        $nome = $request->nome;
        if ($request->nome) {
            $atendente->where('p.nome_completo', 'ilike', "%$request->nome%");
        }

        $grupo = $request->grupo;
        if ($request->grupo) {
            $atendente->where('g.id', $request->grupo);
        }

        $cpf = $request->cpf;
        if ($request->cpf) {
            $atendente->where('p.cpf', $request->cpf);
        }

        $status = $request->status;
        if ($request->status) {
            $atendente->where('p.status', $request->status);
        }

        // Pagina os resultados
        $atendente = $atendente->orderBy('p.status', 'asc')->orderBy('p.nome_completo', 'asc')->paginate(50);

        // Recupera dados adicionais
        $stap = DB::select("select id as ids, tipo from tipo_status_pessoa t");
        $soma = DB::table('atendentes')->count();

        // Retorna a view com os dados
        return view('/atendentes/gerenciar-atendentes', compact('atendente', 'stap', 'soma', 'ddd', 'sexo', 'cpf', 'nome', 'grupos'));
    }


    public function create(Request $request)
    {

        $selecionados = 1;
        $pessoas = DB::select('select id as idp, nome_completo from pessoas');
        $tipo_status_pessoa = DB::select('select * from tipo_status_pessoa');
        $grupo = DB::select('select id, nome from grupo');
        $atendentes = DB::select('select * from atendentes');
        $atendente_grupo = DB::select('select * from atendente_grupo');


        return view('atendentes/criar-atendente', compact('pessoas', 'grupo', 'atendentes', 'atendente_grupo'));
    }

    public function store(Request $request)
    {
        $data = date("Y-m-d H:i:s");

        $id_pessoa = $request->input('id_pessoa');
        $selectedGroups = $request->input('id_grupo');

        if (!is_numeric($id_pessoa) || $id_pessoa <= 0) {
            app('flasher')->addError("ID de pessoa inválido.");
            return redirect()->back();
        }


        $atendenteId = DB::table('atendentes')->insertGetId([
            'id_pessoa' => (int) $id_pessoa,
        ]);


        try {

            foreach ($selectedGroups as $groupId) {
                DB::table('atendente_grupo')->insert([
                    'id_atendente' => $atendenteId,
                    'id_grupo' => (int) $groupId,
                    'dt_inicio' => $data,
                ]);
            }


            if ($request->has('additional_id_grupo')) {
                $additionalGroups = $request->input('additional_id_grupo');


                foreach ($additionalGroups as $additionalGroupId) {
                    DB::table('atendente_grupo')->insert([
                        'id_atendente' => $atendenteId,
                        'id_grupo' => (int) $additionalGroupId,
                        'dt_inicio' => $data,
                    ]);
                }
            }
        } catch (\Exception $e) {
            app('flasher')->addError("Erro ao inserir grupos: " . $e->getMessage());

            DB::table('atendentes')->where('id', $atendenteId)->delete();
            return redirect()->back();
        }

        app('flasher')->addSuccess('O cadastro foi realizado com sucesso.');

        return redirect('gerenciar-atendentes');
    }


    public function edit($id)
    {

        $gruposAtendente = DB::table('atendente_grupo')
        ->where('id_atendente', $id)
        ->get();



        $pessoas = DB::select('select id as idp, nome_completo from pessoas');
        $tipo_status_pessoa = DB::select('select * from tipo_status_pessoa');
        $grupo = DB::select('select id, nome from grupo');
        $atendentes = DB::select('select * from atendentes');
        $atendente_grupo = DB::select('select * from atendente_grupo');

        $atendente = DB::table('atendentes AS ad')
            ->select(
                'ad.id',
                'p.id AS idp',
                'p.nome_completo',
                'p.cpf',
                'tps.tipo',
                'ag.id_grupo',
                'p.cpf',
                'p.ddd',
                'p.email',
                'p.celular',
                'ag.id_atendente',
                'ag.dt_inicio',
                'ag.dt_fim',
                'ag.motivo',
                'ag.dt_fim',
                'p.dt_nascimento',
                'p.sexo',
                'p.email',
                'p.ddd',
                'p.celular',
                'tsp.id AS idtps',
                'p.status',
                'tsp.tipo AS tipos',
                'd.id as did',
                'd.descricao as ddesc',
                'p.motivo_status',
                'g.nome AS nome_grupo'
            )
            ->where('ad.id', $id)
            ->leftJoin('pessoas AS p', 'ad.id_pessoa', '=', 'p.id')
            ->leftJoin('atendente_grupo AS ag', 'ag.id_atendente', '=', 'ad.id')
            ->leftJoin('tipo_status_pessoa AS tsp', 'p.status', '=', 'tsp.id')
            ->leftJoin('tp_sexo AS tps', 'p.sexo', '=', 'tps.id')
            ->leftJoin('tp_ddd AS d', 'p.ddd', '=', 'd.id')
            ->leftJoin('grupo AS g', 'ag.id_grupo', '=', 'g.id')
            ->first();


        // Retorna a view com os dados
        return view('atendentes/editar-atendente', compact('gruposAtendente','pessoas', 'tipo_status_pessoa', 'grupo', 'atendentes', 'atendente_grupo', 'atendente'));
    }

    public function update(Request $request,$id)
{

    dd($request->input('id_pessoa'));
    $data = date("Y-m-d H:i:s");

    // Atualizar tabela "atendentes"
    DB::table('atendentes')
        ->where('id_pessoa', $id)
        ->update([
            'id_pessoa' => $request->input('id_pessoa'),
        ]);

    // Atualizar tabela "atendente_grupo"
    DB::table('atendente_grupo')
        ->where('id_atendente', $id)
        ->delete(); // Excluir os registros antigos

    // Inserir os novos registros
    if ($request->has('id_grupo') && is_array($request->input('id_grupo'))) {
        foreach ($request->input('id_grupo') as $idGrupo) {
            DB::table('atendente_grupo')->insert([
                'id_atendente' => $id,
                'id_grupo' => $idGrupo,
                'dt_inicio' => $data,
            ]);
        }
    }

    app('flasher')->addSuccess('Os dados foram atualizados com sucesso.');

    return redirect('gerenciar-atendentes');
}



    public function show($id)
{


    $pessoas = DB::select('select id as idp, nome_completo from pessoas');
    $tipo_status_pessoa = DB::select('select * from tipo_status_pessoa');
    $grupo = DB::select('select id, nome from grupo');
    $atendentes = DB::select('select * from atendentes');
    $atendente_grupo = DB::select('select * from atendente_grupo');

    $atendente = DB::table('atendentes AS ad')
        ->select(
            'ad.id',
            'p.id AS idp',
            'p.nome_completo',
            'p.cpf',
            'tps.tipo',
            'ag.id_grupo',
            'p.cpf',
            'p.ddd',
            'p.email',
            'p.celular',
            'ag.id_atendente',
            'ag.dt_inicio',
            'ag.dt_fim',
            'ag.motivo',
            'ag.dt_fim',
            'p.dt_nascimento',
            'p.sexo',
            'p.email',
            'p.ddd',
            'p.celular',
            'tsp.id AS idtps',
            'p.status',
            'tsp.tipo AS tipos',
            'd.id as did',
            'd.descricao as ddesc',
            'p.motivo_status',
            'g.nome AS nome_grupo'
        )



        ->where('ad.id', $id)
        ->leftJoin('pessoas AS p', 'ad.id_pessoa', '=', 'p.id')
        ->leftJoin('atendente_grupo AS ag', 'ag.id_atendente', '=', 'ad.id')
        ->leftJoin('tipo_status_pessoa AS tsp', 'p.status', '=', 'tsp.id')
        ->leftJoin('tp_sexo AS tps', 'p.sexo', '=', 'tps.id')
        ->leftJoin('tp_ddd AS d', 'p.ddd', '=', 'd.id')
        ->leftJoin('grupo AS g', 'ag.id_grupo', '=', 'g.id')
        ->first();

    // Retorna a view com os dados
    return view('atendentes/visualizar-atendente', compact('pessoas', 'tipo_status_pessoa', 'grupo', 'atendentes', 'atendente_grupo', 'atendente'));
}

    public function destroy($id)
    {
        $ids = DB::table('atendentes')->where('id', $id)->get();
        $teste = session()->get('usuario');

        $verifica = DB::table('historico_venus')->where('fato', $id)->count('fato');

        $data = date("Y-m-d H:i:s");

        DB::table('historico_venus')->insert([
            'id_usuario' => session()->get('usuario.id_usuario'),
            'data' => $data,
            'fato' => 0,
            'obs' => $id
        ]);

        DB::table('atendentes')->where('id', $id)->delete();

        app('flasher')->addError('Excluído com sucesso.');
        return redirect('/gerenciar-atendentes');
    }
}
