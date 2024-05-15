<div id="app">
    <nav class="navbar navbar-expand-lg navbar-light shadow-sm" style="background-color:#87CEFA; font-family:tahoma; font-weight:bold;">
        <div class="container">
            <a class="navbar-brand" style="color: #fff;" href="{{ url('/login/valida') }}">Vênus</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDarkDropdown" aria-controls="navbarNavDarkDropdown" aria-expanded="false" aria-label="Toggle navigation" style="border:none">
                <span class="navbar-toggler-icon"></span>
                </button>
            <div class="collapse navbar-collapse" id="navbarNavDarkDropdown">
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="1" role="button" data-bs-toggle="dropdown" aria-expanded="false">Gerenciamento AME</a>
                        <ul class="dropdown-menu dropdown-menu-dark" aria-labelledby="navbarDarkDropdownMenuLink">
                            <li><a class="dropdown-item" href="/atendendo-afe">Atendimento Fraterno Especifico</a></li>
                            <li><a class="dropdown-item" href="/gerenciar-grupos-membro">Administrar Grupos</a></li>
                            <li><a class="dropdown-item" href="/gerenciar-encaminhamentos-integral">Encaminhamentos Integral</a></li>
                            <li><a class="dropdown-item" href="/gerenciar-entrevistas">Gerenciar Entrevistas</a></li>
                            <li><a class="dropdown-item" href="/gerenciar-pessoas">Gerenciar Pessoas</a></li>
                            <li><a class="dropdown-item" href="/gerenciar-reunioes">Gerenciar Reuniões </a></li>
                            <li><a class="dropdown-item" href="/gerenciar-integral">Gerenciar Tratamento Integral</a></li>
                        </ul>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="2" role="button" data-bs-toggle="dropdown" aria-expanded="false">Gerenciamento DAO</a>
                        <ul class="dropdown-menu dropdown-menu-dark" aria-labelledby="navbarDarkDropdownMenuLink">
                            <li><a class="dropdown-item" href="/gerenciar-grupos-membro">Administrar Grupos</a></li>
                            <li><a class="dropdown-item" href="/atendendo">Atendimento Fraterno Individual</a></li>
                            <li><a class="dropdown-item" href="/gerenciar-atendentes-apoio">Gerenciar Atendente de Apoio</a></li>
                            <li><a class="dropdown-item" href="/gerenciar-atendente-dia">Gerenciar Atendentes do dia</a></li>
                            <li><a class="dropdown-item" href="/gerenciar-atendimentos">Gerenciar Atendimento Fraterno</a></li>
                            <li><a class="dropdown-item" href="/gerenciar-encaminhamentos">Gerenciar Encaminhamentos</a></li>
                            <li><a class="dropdown-item" href="/gerenciar-grupos">Gerenciar Grupos</a></li>
                            <li><a class="dropdown-item" href="/gerenciar-pessoas">Gerenciar Pessoas</a></li>
                            <li><a class="dropdown-item" href="/gerenciar-presenca">Gerenciar Presença Entrevista</a></li>
                            <li><a class="dropdown-item" href="/gerenciar-reunioes">Gerenciar Reuniões </a></li>
                            <li><a class="dropdown-item" href="/gerenciar-tratamentos">Gerenciar Tratamentos</a></li>


                        </ul>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="2" role="button" data-bs-toggle="dropdown" aria-expanded="false">Gerenciamento DAE</a>
                        <ul class="dropdown-menu dropdown-menu-dark" aria-labelledby="navbarDarkDropdownMenuLink">
                            <li><a class="dropdown-item" href="/gerenciar-grupos-membro">Administrar Grupos</a></li>
                            <li><a class="dropdown-item" href="/gerenciar-encaminhamentos-pti">Encaminhamentos PTI</a></li>
                            <li><a class="dropdown-item" href="/gerenciar-pti">Gerenciar Assistidos PTI</a></li>
                            <li><a class="dropdown-item" href="/gerenciar-grupos">Gerenciar Grupos</a></li>
                            <li><a class="dropdown-item" href="/gerenciar-mediunidades">Gerenciar Mediunidades</a></li>
                            <li><a class="dropdown-item" href="/gerenciar-pessoas">Gerenciar Pessoas</a></li>
                            <li><a class="dropdown-item" href="/gerenciar-atendentes-plantonistas">Gerenciar Plantonistas</a></li>
                            <li><a class="dropdown-item" href="/gerenciar-reunioes">Gerenciar Reuniões </a></li>
                        </ul>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="3" role="button" data-bs-toggle="dropdown" aria-expanded="false">Administrar sistema</a>
                        <ul class="dropdown-menu dropdown-menu-dark" aria-labelledby="navbarDarkDropdownMenuLink">
                            <li><a class="dropdown-item" href="/gerenciar-fatos">Gerenciar Fatos</a></li>
                            <li><a class="dropdown-item" href="/gerenciar-salas">Gerenciar Salas</a></li>
                            <li><a class="dropdown-item" href="/gerenciar-usuario">Gerenciar Usuários</a></li>
                        </ul>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="4" role="button" data-bs-toggle="dropdown" aria-expanded="false">Logout</a>
                        <ul class="dropdown-menu dropdown-menu-dark" aria-labelledby="navbarDarkDropdownMenuLink">
                            <li><a class="dropdown-item" href="/usuario/alterar-senha"><i class="mdi mdi-lock-open-outline font-size-17 text-muted align-middle mr-1"></i>Alterar Senha</a></li>
                            <li><a class="dropdown-item" href="javascript:void();" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i class="mdi mdi-power font-size-17 text-muted align-middle mr-1 text-danger"></i> {{ __('Sair') }}</a></li>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                @csrf
                            </form>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
</div>
