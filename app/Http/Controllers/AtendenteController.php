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
//tratamento de excessão para testar as variaveis
        try{

        $ddd = DB::select('select id, descricao from tp_ddd');//Pega o DDD, uso desconhecido
        $grupos = DB::select('select id, nome from grupo  order by nome ');
        $sexo = DB::select('select id, tipo from tp_sexo');

        //Armazena as informaçoes importantes para o Gerenciar
        $atendente = DB::table('atendentes AS ad')->select('ad.id', 'p.id AS idp', 'p.nome_completo', 'p.cpf', 'tps.tipo', 'p.cpf', 'p.ddd', 'p.email', 'p.celular', 'p.dt_nascimento', 'p.sexo', 'p.email', 'p.ddd', 'p.celular', 'tsp.id AS idtps', 'p.status', 'tsp.tipo AS tpsta', 'd.id as did', 'd.descricao as ddesc', 'p.motivo_status')->leftJoin('pessoas AS p', 'ad.id_pessoa', '=', 'p.id')->leftJoin('tipo_status_pessoa AS tsp', 'p.status', '=', 'tsp.id')->leftJoin('tp_sexo AS tps', 'p.sexo', '=', 'tps.id')->leftJoin('tp_ddd AS d', 'p.ddd', '=', 'd.id');

        //Value do input de pesquisa
        $nome = $request->nome;

        //Pesquisa
        if ($request->nome) {
            $atendente->where('p.nome_completo', 'ilike', "%$request->nome%");
        }

        $cpf = $request->cpf;
        if ($request->cpf) {
            $atendente->where('p.cpf', 'ilike', "%$request->cpf%");
        }

        $status = $request->status == null ? 'undefined' : $request->status;
        if ($request->status) {
            $atendente->where('p.status', $request->status);
        }

        //Organiza primeiramente por Status, deixando inativo no final, depois por ondem Alfabética
        $atendente = $atendente->orderBy('p.status', 'desc')->orderBy('p.nome_completo', 'asc')->get();

        $stap = DB::select('select id as ids, tipo from tipo_status_pessoa t');
        $soma = DB::table('atendentes')->count();

        return view('/atendentes-fraterno/gerenciar-atendentes', compact('atendente', 'stap', 'soma', 'ddd', 'sexo', 'cpf', 'nome', 'grupos', 'status'));

        }

        catch(\Exception $e){

            //Armazena o código de erro em uma variavel
            $code = $e->getCode( );
            //Retorna a tela de erro 500 com o número do erro ocorrido
            return view('tratamento-erro.erro-inesperado', compact('code'));
                }

    }

    public function create(Request $request)
    {

        $pessoas = DB::select('select id as idp, nome_completo from pessoas');
        $grupo = DB::select('select id, nome from grupo where status_grupo <> 2');
        $atendentes = DB::select('select * from atendentes');//Variavel sem uso aparente
        $atendente_grupo = DB::select('select * from atendente_grupo');//Variavel sem uso aparente

        return view('atendentes-fraterno/criar-atendente', compact('pessoas', 'grupo', 'atendentes', 'atendente_grupo'));
    }

    public function store(Request $request)
    {

        //Com tratamento de erro inicialzia a transação
        DB::beginTransaction();
        try{

            $data = date('Y-m-d H:i:s');

            $id_pessoa = $request->input('id_pessoa');
            $selectedGroups = $request->input('id_grupo');

            //Testa se os valores de ID pessoa são validos
            if (!is_numeric($id_pessoa) || $id_pessoa <= 0) {
                app('flasher')->addError('ID de pessoa inválido.');
                return redirect()->back();
            }
                //insere um novo atendente na tabela de atendentes
            $atendenteId = DB::table('atendentes')->insertGetId([
                'id_pessoa' => (int) $id_pessoa,
            ]);


                //Loop de repetição de inserção de grupos
                foreach ($selectedGroups as $groupId) {
                    DB::table('atendente_grupo')->insert([
                        'id_atendente' => $atendenteId,
                        'id_grupo' => (int) $groupId,
                        'dt_inicio' => $data,
                    ]);
                }

            app('flasher')->addSuccess('O cadastro foi realizado com sucesso.');

DB::commit();
}

catch(\Exception $e){

            //Retorna uma mensagem flasher com o código do erro
            app('flasher')->addError("Houve um erro inesperado: #" . $e->getCode( )) ;
            DB::rollBack();

        }



        return redirect('gerenciar-atendentes');
    }

    public function edit($id)
    {
        try{

            $gruposAtendente = DB::table('atendente_grupo')->select('id_grupo')->where('id_atendente', $id)->get();

            $tipo_motivo_status_pessoa = collect([(object) ['id' => 1, 'motivo' => 'mudou'], (object) ['id' => 2, 'motivo' => 'desencarnou']]);

            $pessoas = DB::select('select id as idp, nome_completo from pessoas');
            $tipo_status_pessoa = DB::select('select * from tipo_status_pessoa');
            $grupo = DB::select('select id, nome from grupo where status_grupo <> 2');

            $atendentes = DB::select('select * from atendentes');
            $atendente_grupo = DB::select('select * from atendente_grupo');
            $atendente = DB::table('atendentes AS ad')->select('ad.id', 'p.id AS idp', 'p.nome_completo', 'p.cpf', 'tps.tipo', 'ag.id_grupo', 'p.cpf', 'p.ddd', 'p.email', 'p.celular', 'ag.id_atendente', 'p.motivo_status', 'ag.dt_inicio', 'ag.dt_fim', 'p.motivo_status', 'ag.dt_fim', 'p.dt_nascimento', 'p.sexo', 'p.email', 'p.ddd', 'p.celular', 'tsp.id AS idtps', 'p.status', 'tsp.tipo AS tipos', 'd.id as did', 'd.descricao as ddesc', 'p.motivo_status', 'g.nome AS nome_grupo')->where('ad.id', $id)->leftJoin('pessoas AS p', 'ad.id_pessoa', '=', 'p.id')->leftJoin('atendente_grupo AS ag', 'ag.id_atendente', '=', 'ad.id')->leftJoin('tipo_status_pessoa AS tsp', 'p.status', '=', 'tsp.id')->leftJoin('tp_sexo AS tps', 'p.sexo', '=', 'tps.id')->leftJoin('tp_ddd AS d', 'p.ddd', '=', 'd.id')->leftJoin('grupo AS g', 'ag.id_grupo', '=', 'g.id')->first();

            $info = [];
            foreach ($gruposAtendente as $gr) {
                $info[] = $gr;
            }

            return view('atendentes-fraterno/editar-atendente', compact('tipo_motivo_status_pessoa', 'gruposAtendente', 'pessoas', 'tipo_status_pessoa', 'grupo', 'atendentes', 'atendente_grupo', 'atendente', 'info'));

        }

        catch(\Exception $e){

                    //Retorna um flasher de Erro Caso ocorra Algum erro
                    app('flasher')->addError("Houve um erro inesperado: #" . $e->getCode( )) ;
                    return redirect()->back();
                }

    }

    public function update(Request $request, $id)
    {
        $data = now();
        $dt_fim = $request->input('dt_fim');
        $motivo = $request->input('motivo_status');
        $status = $request->input('status');

        //Inicializa a transação
        DB::beginTransaction();

        try {
            // Update 'status' and 'motivo_status' in 'pessoas' table
            DB::table('pessoas')
                ->join('atendentes', 'pessoas.id', '=', 'atendentes.id_pessoa')
                ->where('atendentes.id', $id)
                ->update([
                    'status' => $status,
                    'motivo_status' => $motivo,
                ]);

            // Obter os grupos existentes associados ao atendente
            $existingGroups = DB::table('atendente_grupo')->where('id_atendente', $id)->pluck('id_grupo')->toArray();

            // Verificar e deletar os registros antigos que não estão mais presentes no formulário
            $groupsToDelete = array_diff($existingGroups, $request->input('id_grupo'));
            DB::table('atendente_grupo')->where('id_atendente', $id)->whereIn('id_grupo', $groupsToDelete)->delete();

            // Inserir ou atualizar os registros, incluindo os grupos existentes e os novos
            if ($request->has('id_grupo') && is_array($request->input('id_grupo'))) {
                foreach ($request->input('id_grupo') as $grupo_id) {
                    $existingRecord = DB::table('atendente_grupo')->where('id_atendente', $id)->where('id_grupo', $grupo_id)->first();

                    if ($existingRecord) {
                        // Se o registro já existe, atualizar
                        DB::table('atendente_grupo')
                            ->where('id_atendente', $id)
                            ->where('id_grupo', $grupo_id)
                            ->update([
                                'dt_inicio' => $data,
                                'dt_fim' => $dt_fim,
                            ]);
                    } else {
                        // Se o registro não existe, inserir
                        DB::table('atendente_grupo')->insert([
                            'id_atendente' => $id,
                            'id_grupo' => $grupo_id,
                            'dt_inicio' => $data,
                            'dt_fim' => $dt_fim,
                        ]);
                    }
                }
            }

            // Inserir novos registros dos grupos adicionais apenas se o checkbox estiver marcado
            if ($request->has('adicionarMaisGrupos') && $request->input('adicionarMaisGrupos')) {
                if ($request->has('novo_grupo') && is_array($request->input('novo_grupo'))) {
                    foreach ($request->input('novo_grupo') as $novo_grupo) {
                        DB::table('atendente_grupo')->insert([
                            'id_atendente' => $id,
                            'id_grupo' => $novo_grupo,
                            'dt_inicio' => $data,
                            'dt_fim' => $dt_fim,
                        ]);
                    }
                }
            }
            //Se tudo estiver correto, salve as informações em banco
            DB::commit();
        } catch (\Exception $e) {

            //Retorna um Flasher de erro, retorna o banco para a posição inicical e volta para a situação correta
            app('flasher')->addError('Houve um erro inesperado: #' . $e->getCode());
            DB::rollBack();
            return redirect()->back();
        }
        return redirect('gerenciar-atendentes');
    }

    public function show($id)
    {
        try {
            $gruposAtendente = DB::table('atendente_grupo')->where('id_atendente', $id)->get();

            $tipo_motivo_status_pessoa = collect([(object) ['id' => 1, 'motivo' => 'mudou'], (object) ['id' => 2, 'motivo' => 'desencarnou']]);

            $pessoas = DB::select('select id as idp, nome_completo from pessoas');
            $tipo_status_pessoa = DB::select('select * from tipo_status_pessoa');
            $grupo = DB::select('select id, nome from grupo');
            $atendentes = DB::select('select * from atendentes');
            $atendente_grupo = DB::select('select * from atendente_grupo');

            $atendente = DB::table('atendentes AS ad')->select('ad.id', 'p.id AS idp', 'p.nome_completo', 'p.cpf', 'tps.tipo', 'ag.id_grupo', 'p.cpf', 'p.ddd', 'p.email', 'p.celular', 'ag.id_atendente', 'p.motivo_status', 'ag.dt_inicio', 'ag.dt_fim', 'p.motivo_status', 'ag.dt_fim', 'p.dt_nascimento', 'p.sexo', 'p.email', 'p.ddd', 'p.celular', 'tsp.id AS idtps', 'p.status', 'tsp.tipo AS tipos', 'd.id as did', 'd.descricao as ddesc', 'p.motivo_status', 'g.nome AS nome_grupo')->where('ad.id', $id)->leftJoin('pessoas AS p', 'ad.id_pessoa', '=', 'p.id')->leftJoin('atendente_grupo AS ag', 'ag.id_atendente', '=', 'ad.id')->leftJoin('tipo_status_pessoa AS tsp', 'p.status', '=', 'tsp.id')->leftJoin('tp_sexo AS tps', 'p.sexo', '=', 'tps.id')->leftJoin('tp_ddd AS d', 'p.ddd', '=', 'd.id')->leftJoin('grupo AS g', 'ag.id_grupo', '=', 'g.id')->first();

            return view('atendentes-fraterno/visualizar-atendente', compact('tipo_motivo_status_pessoa', 'gruposAtendente', 'pessoas', 'tipo_status_pessoa', 'grupo', 'atendentes', 'atendente_grupo', 'atendente'));
        } catch (\Exception $e) {
            app('flasher')->addError('Houve um erro inesperado: #' . $e->getCode());
        }
    }

    public function destroy($id)
    {
        $data = date('Y-m-d H:i:s');

        DB::beginTransaction();
        try {
            DB::table('historico_venus')->insert([
                'id_usuario' => session()->get('usuario.id_usuario'),
                'data' => $data,
                'fato' => 0,
                'obs' => $id,
            ]);
            DB::table('atendente_grupo')->where('id_atendente', $id)->delete();
            DB::table('atendentes')->where('id', $id)->delete();

            app('flasher')->addSuccess('Atendente excluído com sucesso.');
            DB::commit();
        } catch (\Exception $e) {
            app('flasher')->addError('Houve um erro inesperado: #' . $e->getCode());

            DB::rollBack();
        }

        return redirect('/gerenciar-atendentes');
    }
}
