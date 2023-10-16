<div id="app">
    <nav class="navbar navbar-expand-md navbar-light shadow-sm" style="background-color:#87CEFA; font-family:tahoma; font-weight:bold;">
        <div class="container" >
            <a class="navbar-brand" href="{{ url('/login/valida') }}">
                {{ config('app.name', 'Laravel') }}
            </a>
            <div class="dropdown"  >
                <button class="btn dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="color:#ffffff;">Atendimento Espírita</button> 
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="/gerenciar-atendimentos">Recepção Atendimento Fraterno</a></li>
                        <li><a class="dropdown-item" href="#">Recepção Geral</a></li>
                        <li><a class="dropdown-item" href="#">Gerenciar Atendentes</a></li>
                        <li><a class="dropdown-item" href="/atendendo">Atender o assistido</a></li>
                    </ul>
            </div>
            <div class="dropdown"  >
                <button class="btn dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="color:#ffffff;">Logout</button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="/usuario/alterar-senha"><i class="mdi mdi-lock-open-outline font-size-17 text-muted align-middle mr-1"></i>Alterar Senha</a></li>
                        <li><a class="dropdown-item text-danger" href="javascript:void();" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i class="mdi mdi-power font-size-17 text-muted align-middle mr-1 text-danger"></i> {{ __('Sair') }}</a></li>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                    </ul>
            </div> 
        </div>
    </nav>
</div>
