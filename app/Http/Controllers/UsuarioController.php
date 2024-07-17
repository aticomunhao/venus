<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\ModelUsuario;
use App\Models\ModelPessoa;
use Illuminate\Support\Facades\Hash;
use App\Notifications\ResetPassword;
use Illuminate\Notifications\Notifiable;

class UsuarioController extends Controller
{
    use Notifiable;

    // Enviar email traduziado
    public function sendPasswordResetNotification($token)
    {
        try{
        $this->notify(new ResetPassword($token));
    }
    catch(\Exception $e){

        $code = $e->getCode( );
        return view('administrativo-erro.erro-inesperado', compact('code'));
            }
        }
    private $objUsuario;

    public function __construct()
    {
        try{
        $this->objUsuario = new ModelUsuario();
    }
    catch(\Exception $e){

        $code = $e->getCode( );
        return view('administrativo-erro.erro-inesperado', compact('code'));
            }
        }

    public function getUsuarios()
    {
        try{
        $result = DB::table('usuario as u')->select('u.id', 'u.id_pessoa', 'p.cpf', 'p.nome_completo', 'u.ativo', 'u.bloqueado', 'u.data_ativacao')->leftJoin('pessoas as p', 'u.id_pessoa', 'p.id');

        return $result;
    }
    catch(\Exception $e){

        $code = $e->getCode( );
        return view('administrativo-erro.erro-inesperado', compact('code'));
            }
        }

    public function index(Request $request)
    {
        try{
        //$result= $this->objUsuario->all();
        $result = $this->getUsuarios();

        if ($request->nome) {
            $result->where('p.nome_completo', 'ilike', "%$request->nome%");
        }
        if ($request->cpf) {
            $result->where('p.cpf', 'ilike', "%$request->cpf%");
        }

        $result = $result->get();

        return view('usuario/gerenciar-usuario', compact('result'));
    }
    catch(\Exception $e){

        $code = $e->getCode( );
        return view('administrativo-erro.erro-inesperado', compact('code'));
            }
        }

    public function create(Request $request)
    {
        try{
        $pessoa = new ModelPessoa();
        $result = $pessoa;

        if ($request->nome) {
            $result = $result->where('nome_completo', 'ilike', "%$request->nome%");
        }
        if ($request->cpf) {
            $result = $result->where('cpf', 'ilike', "%$request->cpf%");
        }

        $result = $result->get();  // Use get() to execute the query


        return view('usuario/incluir-usuario', compact('result'));
    }
    catch(\Exception $e){

        $code = $e->getCode( );
        return view('administrativo-erro.erro-inesperado', compact('code'));
            }
        }

    public function store(Request $request)
    {

        $keys_request = array_keys($request->input());

        $senha_inicial = $this->gerarSenhaInicial($request->input('idPessoa'));

        $this->inserirUsuario($request, $senha_inicial);

        $this->excluirUsuarioPerfis($request->input('idPessoa'));

        $this->inserirperfilUsuario($keys_request, $request->input('idPessoa'));

        //$this->inserirUsuarioDeposito($keys_request, $request->input('idPessoa'));

        $this->inserirUsuarioSetor($keys_request, $request->input('idPessoa'));


        app('flasher')->addSuccess('O usuário foi criado com sucesso.');

        return Redirect('/gerenciar-usuario');

        //return view('usuario/gerenciar-usuario', compact('result'));
    }
    // catch(\Exception $e){

    //     $code = $e->getCode( );
    //     return view('administrativo-erro.erro-inesperado', compact('code'));
    //         }
    //     }
    public function show($id)
    {
        //
    }

    public function edit($idUsuario)
    {
        try{
        $resultPerfil = DB::table('perfil')->get();

        //$resultDeposito = $this->getDeposito();

        $resultSetor = DB::table('rotas_setor')->leftJoin('setor', 'rotas_setor.id_setor', 'setor.id')->distinct('id_setor')->get();

        $resultUsuario = DB::table('usuario')->where('id', $idUsuario)->get();

        $result = DB::table('pessoas')
            ->where('id', $resultUsuario[0]->id_pessoa)
            ->get();

        $resultPerfisUsuario = DB::select('select * from usuario_perfil where id_usuario =' . $idUsuario);

        $resultPerfisUsuarioArray = [];
        foreach ($resultPerfisUsuario as $resultPerfisUsuarios) {
            $resultPerfisUsuarioArray[] = $resultPerfisUsuarios->id_perfil;
        }

        $resultDepositoUsuario = DB::select('select * from usuario_deposito where id_usuario =' . $idUsuario);

        $resultDepositoUsuarioArray = [];
        foreach ($resultDepositoUsuario as $resultDepositoUsuarios) {
            $resultDepositoUsuarioArray[] = $resultDepositoUsuarios->id_deposito;
        }

        $resultSetorUsuario = DB::select('select * from usuario_setor where id_usuario =' . $idUsuario);

        $resultSetorUsuarioArray = [];
        foreach ($resultSetorUsuario as $resultSetorUsuarios) {
            $resultSetorUsuarioArray[] = $resultSetorUsuarios->id_setor;
        }

        return view('/usuario/alterar-configurar-usuario', compact('result', 'resultPerfil', 'resultSetor', 'resultUsuario', 'resultPerfisUsuarioArray', 'resultDepositoUsuarioArray', 'resultSetorUsuarioArray'));
    }
    catch(\Exception $e){

        $code = $e->getCode( );
        return view('administrativo-erro.erro-inesperado', compact('code'));
            }
        }

    public function update(Request $request, $id)
    {
        try{
        $ativo = isset($request->ativo) ? 1 : 0;
        $bloqueado = isset($request->bloqueado) ? 1 : 0;
        // echo $id;
        // exit();
        DB::table('usuario')
            ->where('id', $id)
            ->update([
                'ativo' => $ativo,
                'bloqueado' => $bloqueado,
            ]);

        $keys_request = array_keys($request->input());

        $this->excluirUsuarioPerfis($request->input('idPessoa'));

        $this->inserirPerfilUsuario($keys_request, $request->input('idPessoa'));

       // $this->inserirUsuarioDeposito($keys_request, $request->input('idPessoa'));

        $this->inserirUsuarioSetor($keys_request, $request->input('idPessoa'));


        app('flasher')->addSuccess('Usuário alterado com sucesso!');
        return redirect('gerenciar-usuario');
    }
    catch(\Exception $e){

        $code = $e->getCode( );
        return view('administrativo-erro.erro-inesperado', compact('code'));
            }
        }

    public function destroy($id)
    {
        try{
        DB::delete('delete from usuario_perfil where id_usuario =?', [$id]);
        DB::delete('delete from usuario_deposito where id_usuario =?', [$id]);
        DB::delete('delete from usuario_setor where id_usuario =?', [$id]);
        $deleted = DB::delete('delete from usuario where id =?', [$id]);

        $result = $this->getUsuarios();

        app('flasher')->addSuccess('O usuário foi excluido com sucesso.');

        return Redirect('/gerenciar-usuario');
        //return view('usuario/gerenciar-usuario', compact('result'));
    }
    catch(\Exception $e){

        $code = $e->getCode( );
        return view('administrativo-erro.erro-inesperado', compact('code'));
            }
        }


    public function configurarUsuario($id)
    {
        try{
        $resultPerfil = DB::table('perfil')->get();


        $resultSetor = DB::table('rotas_setor')->leftJoin('setor', 'rotas_setor.id_setor', 'setor.id')->distinct('id_setor')->get();

        $result = DB::table('pessoas')->where('id', $id)->get();

        return view('/usuario/configurar-usuario', compact('result', 'resultPerfil', 'resultSetor'));
    }
    catch(\Exception $e){

        $code = $e->getCode( );
        return view('administrativo-erro.erro-inesperado', compact('code'));
            }
        }

    public function inserirUsuario($request, $senha_inicial)
    {
      //  try{
        $ativo = isset($request->ativo) ? 1 : 0;
        $bloqueado = isset($request->bloqueado) ? 1 : 0;

        DB::table('usuario')->insert([
            'id_pessoa' => $request->input('idPessoa'),
            'ativo' => $ativo,
            'data_criacao' => date('m-d-Y'),
            'data_ativacao' => date('m-d-Y'),
            'bloqueado' => $bloqueado,
            'hash_senha' => $senha_inicial,
        ]);
    }
    // catch(\Exception $e){

    //     $code = $e->getCode( );
    //     return view('administrativo-erro.erro-inesperado', compact('code'));
    //         }
    //     }

    public function excluirUsuarioPerfis($idPessoa)
    {
        try{
        $idUsuario = DB::select('select id from usuario where id_pessoa =' . $idPessoa);

        DB::delete('delete from usuario_setor where id_usuario =?', [$idUsuario[0]->id]);
        DB::delete('delete from usuario_deposito where id_usuario =?', [$idUsuario[0]->id]);
        DB::delete('delete from usuario_perfil where id_usuario =?', [$idUsuario[0]->id]);
    }
    catch(\Exception $e){

        $code = $e->getCode( );
        return view('administrativo-erro.erro-inesperado', compact('code'));
            }
        }

    public function inserirPerfilUsuario($perfil, $idPessoa)
    {
        try{
        $idUsuario = DB::select('select id from usuario where id_pessoa =' . $idPessoa);
        $resultPerfil = DB::table('perfil')->get();

        foreach ($perfil as $perfils) {
            foreach ($resultPerfil as $resultPerfils) {
                if ($resultPerfils->descricao == str_replace('_', ' ', $perfils)) {
                    //echo $resultPerfils->id;

                    DB::table('usuario_perfil')->insert([
                        'id_usuario' => $idUsuario[0]->id,
                        'id_perfil' => $resultPerfils->id,
                    ]);
                }

            }
        }

    }
    catch(\Exception $e){

        $code = $e->getCode( );
        return view('administrativo-erro.erro-inesperado', compact('code'));
            }
        }

    // public function inserirTipoEstque($tpEstoque,$idPessoa)
    // {
    //     $idUsuario = DB::select("select id from usuario where id_pessoa =".$idPessoa);
    //     $resultEstoque = DB::select("select id, nome from tipo_estoque");

    //      foreach ($tpEstoque as $tpEstoques) {
    //         foreach ($resultEstoque as $resultEstoques) {

    //             if($resultEstoques->nome ==  str_replace("_", " ",$tpEstoques) ){

    //                 DB::table('usuario_tipo_estoque')->insert([
    //                         'id_usuario' => $idUsuario[0]->id,
    //                         'id_tp_estoque' => $resultEstoques->id,

    //                 ]);
    //             }
    //         }
    //     }
    // }

    // public function inserirUsuarioDeposito($deposito, $idPessoa)
    // {
    //     $idUsuario = DB::select('select id from usuario where id_pessoa =' . $idPessoa);
    //     $resultDeposito = $this->getDeposito();
    //     //dd($resultDeposito);
    //     foreach ($deposito as $depositos) {
    //         foreach ($resultDeposito as $resultDepositos) {
    //             if ($resultDepositos->nome == str_replace('_', ' ', $depositos)) {
    //                 DB::table('usuario_deposito')->insert([
    //                     'id_usuario' => $idUsuario[0]->id,
    //                     'id_deposito' => $resultDepositos->id,
    //                 ]);
    //             }
    //         }
    //     }
    // }


    public function inserirUsuarioSetor($setor, $idPessoa)
    {
        try{
        $idUsuario = DB::select('select id from usuario where id_pessoa =' . $idPessoa);
        $resultSetor = DB::table('rotas_setor')->leftJoin('setor', 'rotas_setor.id_setor', 'setor.id')->distinct('id_setor')->get();
        //dd($resultDeposito);
        foreach ($setor as $setors) {
            foreach ($resultSetor as $resultSetors) {
                if ($resultSetors->nome == str_replace('_', ' ', $setors)) {
                    DB::table('usuario_setor')->insert([
                        'id_usuario' => $idUsuario[0]->id,
                        'id_setor' => $resultSetors->id,
                    ]);
                }
            }
        }
    }
    catch(\Exception $e){

        $code = $e->getCode( );
        return view('administrativo-erro.erro-inesperado', compact('code'));
            }
        }

    public function gerarSenhaInicial($id_pessoa)
    {
        $resultPessoa = DB::select("select cpf, id from pessoas where id =$id_pessoa");

        //dd($resultPessoa[0]->cpf);

        return Hash::make($resultPessoa[0]->cpf);
    }

    public function alteraSenha()
    {
        return view('usuario.alterar-senha');
    }

    public function gravaSenha(Request $request)
    {
        try{
        //dd($request);
        $id_usuario = session()->get('usuario.id_usuario');
        $senhaAtual = $request->input('senhaAtual');
        $resultSenhaAtualHash = DB::select("select hash_senha from usuario where id = $id_usuario");

        if (Hash::check($senhaAtual, $resultSenhaAtualHash[0]->hash_senha)) {
            if($request->input('senhaNova') == $request->input('senhaAtual')){
                app('flasher')->addError('Sua nova senha não pode ser igual a antiga!');
                return redirect()->back();
            }
            $senha_nova = Hash::make($request->input('senhaNova'));

            DB::table('usuario')
                ->where('id', $id_usuario)
                ->update([
                    'hash_senha' => $senha_nova,
                ]);

            //return view('login.alterar-senha')->with('mensagem', 'Senha Alterada com sucesso!');

            app('flasher')->addSuccess('Senha Alterada com sucesso!');

            return redirect('/login/valida');
        }
        return redirect()->back()->with('mensagemErro', 'Senha atual incorreta!');
        //return view('login.alterar-senha')->withErrors(['Senha atual incorreta']);
    }
    catch(\Exception $e){

        $code = $e->getCode( );
        return view('administrativo-erro.erro-inesperado', compact('code'));
            }
        }

    public function gerarSenha($id_pessoa)
    {

        $senha = $this->gerarSenhaInicial($id_pessoa);

        DB::table('usuario')
            ->where('id_pessoa', $id_pessoa)
            ->update([
                'hash_senha' => $senha,
            ]);
        return redirect('gerenciar-usuario')->with('mensagem', 'Senha gerada com sucesso!');
    }
}
