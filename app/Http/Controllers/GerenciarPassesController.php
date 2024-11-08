<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Carbon;
use Mockery\Undefined;
use Psy\Command\WhereamiCommand;

class GerenciarPassesController extends Controller
{

    public function index(Request $request)
    {

        // Obtém a data atual formatada
        $now = Carbon::now()->format('Y-m-d');

        // Inicializa a consulta
        $reuniao = DB::table('cronograma AS cro')
            ->select(
                'cro.id AS idr',
                'gr.nome AS nomeg',
                'cro.dia_semana AS idd',
                'cro.id_sala',
                'cro.id_tipo_tratamento',
                'cro.h_inicio',
                'td.nome AS nomed',
                'cro.h_fim',
                'cro.max_atend',
                'gr.status_grupo AS idst',
                'tst.descricao AS tstd',
                's.sigla as nsigla',
                'sa.numero',
                DB::raw("(CASE WHEN cro.data_fim is not null THEN 'Inativo' ELSE 'Ativo' END) as status")
            )
            ->leftJoin('tipo_tratamento AS tst', 'cro.id_tipo_tratamento', 'tst.id')
            ->leftJoin('grupo AS gr', 'cro.id_grupo', 'gr.id')
            ->leftJoin('setor as s', 'gr.id_setor', 's.id')
            ->leftJoin('membro AS me', 'gr.id', 'me.id_cronograma')
            ->leftJoin('salas AS sa', 'cro.id_sala', 'sa.id')
            ->leftJoin('tipo_dia AS td', 'cro.dia_semana', 'td.id')
            ->where('s.id', '=', 48);





        // Obtém os valores de pesquisa da requisição
        $semana = $request->input('semana', null);
        $grupo = $request->input('grupo', null);
        $setor = $request->input('setor', null);
        $status = $request->input('status', null);




        // Aplica filtro por semana
        if ($semana && $semana !== 'todos') {
            $reuniao->where('cro.dia_semana', '=', $semana);
        }

        // Aplica filtro por nome de grupo com insensibilidade a maiúsculas/minúsculas e acentos
        if ($grupo) {
            $reuniao->where('gr.id', $grupo);
        }

        // Aplica filtro por setor
        if ($setor) {
            $reuniao->where('s.id', $setor);
        }
        // Aplica filtro por status com base na expressão CASE WHEN


        // Conta o número de registros
        // $contar = $reuniao->distinct()->count('cro.id');

        // Aplica a paginação e mantém os parâmetros de busca na URL
        $reuniao = $reuniao
            ->orderBy('status', 'ASC')
            ->orderBy('cro.id_tipo_tratamento', 'ASC')
            ->orderBy('nomeg', 'ASC')
            ->groupBy('idr', 'gr.nome', 'td.nome', 'gr.status_grupo', 'tst.descricao', 's.sigla', 'sa.numero')
            ->paginate(50)
            ->appends([
                'semana' => $semana,
                'grupo' => $grupo,
                'setor' => $setor
            ]);

        // Obtém os dados para os filtros
        $situacao = DB::table('tipo_status_grupo')->select('id AS ids', 'descricao AS descs')->get();

        $tipo_motivo = DB::table('tipo_mot_inat_gr_reu')->get();
        $dias_cronograma=DB::table('dias_cronograma')->get();

        $tpdia = DB::table('tipo_dia')
            ->select('id AS idtd', 'nome AS nomed')
            ->orderByRaw('CASE WHEN id = 0 THEN 1 ELSE 0 END, idtd ASC')
            ->get();

        // Carregar a lista de setores para o Select2
        $setores = DB::table('setor')->orderBy('nome', 'asc')->get();

        // Carregar a lista de grupos para o Select2
        $grupos = DB::table('grupo AS g')
            ->leftJoin('setor AS s', 'g.id_setor', 's.id')
            ->select('g.id AS idg', 'g.nome AS nomeg', 's.sigla')
            ->where('s.id', '=', '48')
            ->orderBy('g.nome', 'asc')
            ->get();


        // Retorna a view com os dados
        return view('passes.gerenciar-passe', compact('dias_cronograma','tipo_motivo', 'reuniao', 'tpdia', 'situacao', 'status', 'semana', 'grupos', 'setores'));
    }


    public function create()
    {


        $hoje = Carbon::today();
        $dia = Carbon::today()->weekday();

        $assistidos = DB::table('pessoas')->select('id', 'nome_completo')->orderBy('nome_completo')->get();

        $reuniao = DB::table('cronograma as cro')
            ->leftJoin('grupo as gr', 'cro.id_grupo', 'gr.id')
            ->leftJoin('salas as sl', 'cro.id_sala', 'sl.id')
            ->leftJoin('tipo_dia as td', 'cro.dia_semana', 'td.id')
            ->where('cro.id_tipo_tratamento', 1)
            ->where('cro.dia_semana', $dia)
            ->where(function ($query) use ($hoje) {
                $query->whereRaw("cro.data_fim < ?", [$hoje])
                    ->orWhereNull('cro.data_fim');
            })
            ->select('cro.id', 'cro.h_inicio', 'cro.h_fim', 'td.nome as nomedia', 'gr.nome', 'sl.numero as sala')
            ->get();



        $motivo = DB::table('tipo_motivo_presenca')->get();


        return view('incluir-passe', compact('assistidos', 'reuniao', 'motivo'));
    }

    public function store(Request $request, $id)
{
    // Obtém a data de hoje
    $hoje = Carbon::today();

    // Validação dos dados de entrada
    $request->validate([
        'acompanhantes' => 'required|integer|min:0', // Validação para garantir que acompanhantes é um número inteiro não negativo
    ]);

    // Verifica se existe um registro correspondente
    $registro = DB::table('dias_cronograma')
        ->where('id_cronograma', $id)
        ->where('data', $hoje)
        ->first();
        if ($registro) {
            // Atualiza o número de acompanhantes
            DB::table('dias_cronograma')
                ->where('id_cronograma', $id)
                ->where('data', $hoje)
                ->update([
                    'nr_acompanhantes' => $request->acompanhantes,
                ]);

            // Insere um registro no histórico
            DB::table('historico_venus')->insert([
                'id_usuario' => session()->get('usuario.id_usuario'),
                'data' => $hoje,
                'pessoa' => $request->input('nome'),
                'obs' => 'Quantidade de passes registrada no cronograma.',
                'fato' => 22,
            ]);

        return redirect('/gerenciar-passe')->with('success', 'Quantidade de passes registrada com sucesso!');
    } else {
        return redirect('/gerenciar-passe')->with('error', 'Registro não encontrado para a data de hoje.');
    }
}




    public function show(string $id)
    {
        // Obtendo a data atual
        $hoje = Carbon::today();

        // Obtém todos os grupos
        $cronograma = DB::table('cronograma')
        ->leftJoin('grupo','grupo.id','=', 'cronograma.id_grupo')
        ->leftJoin('tipo_dia as td', 'cronograma.dia_semana', 'td.id')
        ->leftJoin('setor as st', 'grupo.id_setor', 'st.id')
        ->where('cronograma.id', '=', $id)
        ->select('cronograma.id', 'grupo.nome', 'cronograma.h_inicio', 'cronograma.h_fim', 'td.nome as dia', 'st.sigla as setor')
        ->first();
        // dd($cronograma);



        // Obtém os dias do cronograma
        $dias_cronograma = DB::table('dias_cronograma')->where('id_cronograma', '=', $id)->get();



        return view('passes.visualizar-passe', compact('dias_cronograma','hoje','cronograma'));
    }
    public function edit(string $id)
    {
        $hoje = Carbon::today();

        // Obtém todos os grupos
        $cronograma = DB::table('cronograma')
        ->leftJoin('grupo','grupo.id','=', 'cronograma.id_grupo')
        ->leftJoin('tipo_dia as td', 'cronograma.dia_semana', 'td.id')
        ->leftJoin('setor as st', 'grupo.id_setor', 'st.id')
        ->where('cronograma.id', '=', $id)
        ->select('cronograma.id', 'grupo.nome', 'cronograma.h_inicio', 'cronograma.h_fim', 'td.nome as dia', 'st.sigla as setor')
        ->first();
        // dd($cronograma);



        // Obtém os dias do cronograma
        $dias_cronograma = DB::table('dias_cronograma')->where('id_cronograma', '=', $id)->get();



        return view('passes.editar-passe', compact('hoje', 'dias_cronograma', 'cronograma'));
    }


    public function update(Request $request, string $id)
    {

        $hoje = $request->data;
        

        DB::table('dias_cronograma')
        ->where('id_cronograma',$id)
        ->where('data',$hoje)
        ->update([
        'nr_acompanhantes' => $request->nr_acompanhantes,
        ]);

        DB::table('historico_venus')->insert([
            'id_usuario' => session()->get('usuario.id_usuario'),
            'data' => $hoje,
            'pessoa' => $request->input('nome'),
            'obs' => 'Quantidade de passes editada no cronograma.',
            'fato' => 23,
        ]);

        return redirect('/gerenciar-passe')->with('success', 'Quantidade de passes alterada com sucesso!');
    }



    public function destroy(string $id)
    {
    }
}
