<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class RegrasRotasMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, Mixed $rota): Response
    {

        try {
            $rotasAutorizadas = session()->get('usuario.acesso');
            session()->put('acessoAtual', $rota);
            if (!$rotasAutorizadas) {
                app('flasher')->addError('É necessário fazer login para acessar!');
                return redirect('/');
            } else if(count(explode('-', $rota)) > 1){

                foreach(explode('-', $rota) as $id){
                    if(in_array($id, $rotasAutorizadas)){
                        return $next($request);
                    }
                }
                app('flasher')->addError('Você não tem autorização para acessar esta funcionalidade!');
                return redirect('/login/valida');
            } elseif (in_array(current(explode('-', $rota)), $rotasAutorizadas)) {
                return $next($request);
            } else {
                app('flasher')->addError('Você não tem autorização para acessar esta funcionalidade!');
                return redirect('/login/valida');
            }
        } catch (\Exception $e) {
            app('flasher')->addError('Houve um Erro Inesperado!!');
            return redirect('/login/valida');
        }
    }
}
