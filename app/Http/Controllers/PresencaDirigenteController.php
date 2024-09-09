<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Tipo_fato;
use Illuminate\Database\DBAL\TimestampType;
use Illuminate\Support\Carbon;


class PresencaDirigenteController extends Controller
{
    // Exibe a tela de dar presença
    public function index(Request $request)
    {

        $hoje = Carbon::today();

        //Traz todas as reuniões onde a pessoa logada é Dirigente ou Sub-dirigente
        $reunioesDirigentes = DB::table('membro as mem')
            ->select('ass.id_pessoa', 'gr.nome', 'cr.id', 'gr.status_grupo', 'd.nome as dia')
            ->leftJoin('associado as ass', 'mem.id_associado', 'ass.id')
            ->leftJoin('cronograma as cr', 'mem.id_cronograma', 'cr.id')
            ->leftJoin('grupo as gr', 'cr.id_grupo', 'gr.id')
            ->leftJoin('tipo_dia as d', 'cr.dia_semana', 'd.id')
            ->where('ass.id_pessoa', session()->get('usuario.id_pessoa'))
            ->where('id_funcao', '<', 3)
            ->orderBy('gr.nome')
            ->distinct('gr.nome');

        //Salva esse select completo em uma variável separada
        $reunioes = $reunioesDirigentes->get();

        //Caso nenhum, grupo seja pesquisado, traz o primeiro da lista como padrão, senão o pesquisado
        if ($request->grupo == null) {

            $reunioesDirigentes = $reunioesDirigentes->pluck('id');
        } else {
            $reunioesDirigentes = $reunioesDirigentes->where('cr.id', $request->grupo)->pluck('id');
        }

        //Traz todos os membros do grupo selecionado
        $query = DB::table('membro as m')
        ->select('m.id', 'm.id_cronograma', 'p.nome_completo', 'tf.nome')
        ->leftJoin('associado as ass', 'm.id_associado', 'ass.id')
        ->leftJoin('pessoas as p', 'ass.id_pessoa', 'p.id')
        ->leftJoin('tipo_funcao as tf', 'm.id_funcao', 'tf.id')
        ->where('m.dt_fim', null)
        ->where('m.id_cronograma', $reunioesDirigentes[0])
        ->whereNotIn('m.id_funcao', [5, 6]); // Exclui id_funcao 5 e 6
        
        // Filtra pelo nome do setor se estiver presente na requisição
        if ($request->nome_setor) {
            $query->where('m.id', $request->nome_setor);
        }

        $membros = $query->get();

        //Checa pelo ID da reunião selecionado para ver o seu cronograma naquele dia
        $dias_cronograma_selecionada = DB::table('dias_cronograma')
            ->where('data', $hoje)
            ->where('id_cronograma', $reunioesDirigentes[0])
            ->pluck('id');

        // Gera uma variável, checa se todos os requisitos para ela são encontrador e marca o ID de todos os membros já presentes
        $presencas = [];
        if (count($dias_cronograma_selecionada) > 0) {
            $presencas =  DB::table('presenca_membros')
                ->where('id_dias_cronograma', $dias_cronograma_selecionada)
                ->pluck('id_membro');

            //Transforma essa variável de STDClass pra Array
            $presencas = json_decode(json_encode($presencas), true);
        }

        return view('presenca-dirigente.gerenciar-presenca-dirigente', compact('reunioes', 'reunioesDirigentes', 'membros', 'presencas'));
    }




    // Método para marcar a presença
    public function marcarPresenca($id, $idg)
    {
        //$id = id_membro, $idg = id_reuniao

        $hoje = Carbon::today();

        //Confere o cronograma do dia de hoje para o grupo selecionado
        $dias_cronograma_selecionada = DB::table('dias_cronograma')
            ->where('data', $hoje)
            ->where('id_cronograma', $idg)
            ->pluck('id');

        // Caso nenhum cronograma seja encontrado ao dar a presença, retorna um erro
        if (count($dias_cronograma_selecionada) == 0) {
            app('flasher')->addError('Essa reunião não pertence ao dia de hoje!');
            return redirect()->back();
        }

        //Checa as presenças daquele membro naquele cronograma
        $presencas =  DB::table('presenca_membros')->where('id_membro', $id)->where('id_dias_cronograma', $dias_cronograma_selecionada[0])->first();

        //Caso ele já tenha presença, retorna um aviso e não possibilita novas presenças
        if ($presencas) {
            app('flasher')->addWarning('Presença já registrada!');
            return redirect()->back();
        }

        //Caso todos os requisitos acima sejam aceitos, gera a presença para o membro
        DB::table('presenca_membros')->insert([
            'presenca' => true,
            'id_membro' => $id,
            'id_dias_cronograma' => $dias_cronograma_selecionada[0]
        ]);

        app('flasher')->addSuccess('Presença salva com sucesso!');
        return redirect()->back();
    }

    // Método para cancelar a presença
    public function cancelarPresenca($id, $idg)
    {

        $hoje = Carbon::today();

        //Encontra o cronograma daquele grupo na data de hoje
        $dias_cronograma_selecionada = DB::table('dias_cronograma')
            ->where('data', $hoje)
            ->where('id_cronograma', $idg)
            ->pluck('id');

        //Deleta a presença do membro selecionado no dia de hoje para aquele grupo
        DB::table('presenca_membros')->where('id_membro', $id)->where('id_dias_cronograma', $dias_cronograma_selecionada[0])->delete();

        app('flasher')->addSuccess('Presença cancelada com sucesso!');
        return redirect()->back();
    }
}
