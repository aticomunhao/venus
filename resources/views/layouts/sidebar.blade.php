<?php $acesso = session()->get('usuario.acesso');
$setor = session()->get('usuario.setor');
?>
<div id="app" class="row">
    <nav class="navbar navbar-expand-lg navbar-light shadow-sm"
        style="background-color:#60bbf0; font-family:tahoma; font-weight:bold;">
        <div class="container">
            <a class="navbar-brand" style="color: #fff;" href="{{ url('/login/valida') }}">Vênus</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDarkDropdown"
                aria-controls="navbarNavDarkDropdown" aria-expanded="false" aria-label="Toggle navigation"
                style="border:none">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNavDarkDropdown">
                @if (in_array(38, $setor) or in_array(25, $setor))
                    <ul class="navbar-nav" id="AME">
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="1" role="button"
                                data-bs-toggle="dropdown" aria-expanded="false">Gerenciamento AME</a>
                            <ul class="dropdown-menu dropdown-menu-dark" aria-labelledby="navbarDarkDropdownMenuLink">
                                @if (in_array(13, $acesso) or in_array(14, $acesso))
                                    <li><a class="dropdown-item" href="/gerenciar-grupos-membro">Administrar Grupos</a>
                                    </li>
                                @endif

                                @if (in_array(7, $acesso))
                                    <li><a class="dropdown-item" href="/atendendo-afe">Atendimento Fraterno
                                            Especifico</a>
                                    </li>
                                @endif
                                @if (in_array(23, $acesso))
                                    <li><a class="dropdown-item"
                                            href="/gerenciar-encaminhamentos-integral">Encaminhamentos
                                            Integral</a></li>
                                @endif

                                @if (in_array(9, $acesso))
                                    <li><a class="dropdown-item" href="/gerenciar-entrevistas">Gerenciar Entrevistas</a>
                                    </li>
                                @endif

                                @if (in_array(2, $acesso) or in_array(3, $acesso))
                                    <li><a class="dropdown-item" href="/gerenciar-pessoas">Gerenciar Pessoas</a></li>
                                @endif

                                @if (in_array(19, $acesso))
                                    <li><a class="dropdown-item" href="/gerenciar-reunioes">Gerenciar Reuniões </a></li>
                                @endif

                                @if (in_array(25, $acesso))
                                    <li><a class="dropdown-item" href="/gerenciar-integral">Gerenciar Tratamento
                                            Integral</a></li>
                                @endif


                            </ul>
                        </li>
                    </ul>
                @endif
                @if (in_array(7, $setor) or in_array(25, $setor))
                    <ul class="navbar-nav" id="DAO">
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="2" role="button"
                                data-bs-toggle="dropdown" aria-expanded="false">Gerenciamento DAO</a>
                            <ul class="dropdown-menu dropdown-menu-dark" aria-labelledby="navbarDarkDropdownMenuLink">

                                @if (in_array(13, $acesso) or in_array(14, $acesso))
                                    <li><a class="dropdown-item" href="/gerenciar-grupos-membro">Administrar Grupos</a>
                                    </li>
                                @endif
                                @if (in_array(6, $acesso))
                                    <li><a class="dropdown-item" href="/atendendo">Atendimento Fraterno Individual</a>
                                    </li>
                                @endif
                                @if (in_array(20, $acesso))
                                    <li><a class="dropdown-item" href="/gerenciar-atendentes-apoio">Gerenciar Atendente
                                            de
                                            Apoio</a></li>
                                @endif
                                @if (in_array(4, $acesso))
                                    <li><a class="dropdown-item" href="/gerenciar-atendente-dia">Gerenciar Atendentes do
                                            dia</a>
                                    </li>
                                @endif
                                @if (in_array(5, $acesso))
                                    <li><a class="dropdown-item" href="/gerenciar-atendimentos">Gerenciar Atendimento
                                            Fraterno</a></li>
                                @endif
                                @if (in_array(16, $acesso))
                                    <li><a class="dropdown-item" href="/gerenciar-encaminhamentos">Gerenciar
                                            Encaminhamentos</a>
                                    </li>
                                @endif
                                @if (in_array(9, $acesso))
                                    <li><a class="dropdown-item" href="/gerenciar-entrevistas">Gerenciar Entrevistas</a>
                                    </li>
                                @endif
                                @if (in_array(10, $acesso))
                                    <li><a class="dropdown-item" href="/gerenciar-grupos">Gerenciar Grupos</a></li>
                                @endif
                                @if (in_array(2, $acesso) or in_array(3, $acesso))
                                    <li><a class="dropdown-item" href="/gerenciar-pessoas">Gerenciar Pessoas</a></li>
                                @endif
                                @if (in_array(8, $acesso))
                                    <li><a class="dropdown-item" href="/gerenciar-presenca">Gerenciar Presença
                                            Entrevista</a></li>
                                @endif
                                {{-- @if (in_array(8, $acesso)) --}}
                                <li><a class="dropdown-item" href="/gerenciarpresencadirigente">Gerenciar Presença
                                        Dirigente</a></li>
                            {{-- @endif --}}
                                @if (in_array(19, $acesso))
                                    <li><a class="dropdown-item" href="/gerenciar-reunioes">Gerenciar Reuniões</a></li>
                                @endif
                                @if (in_array(18, $acesso))
                                    <li><a class="dropdown-item" href="/gerenciar-tratamentos">Gerenciar Tratamentos</a>
                                    </li>
                                @endif

                            </ul>
                        </li>
                    </ul>
                @endif
                @if (in_array(6, $setor) or in_array(25, $setor))
                    <ul class="navbar-nav" id="DAE">
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="2" role="button"
                                data-bs-toggle="dropdown" aria-expanded="false">Gerenciamento DAE</a>
                            <ul class="dropdown-menu dropdown-menu-dark" aria-labelledby="navbarDarkDropdownMenuLink">
                                @if (in_array(13, $acesso) or in_array(14, $acesso))
                                    <li><a class="dropdown-item" href="/gerenciar-grupos-membro">Administrar Grupos</a>
                                    </li>
                                @endif
                                @if (in_array(22, $acesso))
                                    <li><a class="dropdown-item" href="/gerenciar-encaminhamentos-pti">Encaminhamentos
                                            PTI</a>
                                    </li>
                                @endif
                                @if (in_array(24, $acesso))
                                    <li><a class="dropdown-item" href="/gerenciar-pti">Gerenciar Assistidos PTI</a></li>
                                @endif
                                @if (in_array(9, $acesso))
                                    <li><a class="dropdown-item" href="/gerenciar-entrevistas">Gerenciar Entrevistas</a>
                                    </li>
                                @endif
                                @if (in_array(10, $acesso))
                                    <li><a class="dropdown-item" href="/gerenciar-grupos">Gerenciar Grupos</a></li>
                                @endif
                                @if (in_array(15, $acesso))
                                    <li><a class="dropdown-item" href="/gerenciar-mediunidades">Gerenciar
                                            Mediunidades</a></li>
                                @endif
                                @if (in_array(2, $acesso) or in_array(3, $acesso))
                                    <li><a class="dropdown-item" href="/gerenciar-pessoas">Gerenciar Pessoas</a></li>
                                @endif
                                @if (in_array(21, $acesso))
                                    <li><a class="dropdown-item" href="/gerenciar-atendentes-plantonistas">Gerenciar
                                            Plantonistas</a></li>
                                @endif
                                @if (in_array(19, $acesso))
                                    <li><a class="dropdown-item" href="/gerenciar-reunioes">Gerenciar Reuniões </a>
                                    </li>
                                @endif

                            </ul>
                        </li>
                    </ul>
                @endif

                    <ul class="navbar-nav" id="ADM">
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="3" role="button"
                                data-bs-toggle="dropdown" aria-expanded="false">Administrar sistema</a>
                            <ul class="dropdown-menu dropdown-menu-dark" aria-labelledby="navbarDarkDropdownMenuLink">
                                @if (in_array(11, $acesso))
                                    <li><a class="dropdown-item" href="/gerenciar-fatos">Gerenciar Fatos</a></li>
                                @endif
                                @if (in_array(12, $acesso))
                                    <li><a class="dropdown-item" href="/gerenciar-salas">Gerenciar Salas</a></li>
                                @endif
                                @if (in_array(1, $acesso))
                                    <li><a class="dropdown-item" href="/gerenciar-perfis">Gerenciar Perfis</a></li>
                                @endif
                                @if (in_array(26, $acesso))
                                    <li><a class="dropdown-item" href="/gerenciar-setor">Gerenciar Setor</a></li>
                                @endif
                                @if (in_array(27, $acesso))
                                    <li><a class="dropdown-item" href="/gerenciar-usuario">Gerenciar Usuários</a></li>
                                @endif
                                @if (in_array(28, $acesso))
                                    <li><a class="dropdown-item" href="/gerenciar-versoes">Gerenciar Versões</a></li>
                                @endif

                            </ul>
                        </li>
                    </ul>

                <div class="col">
                    <ul class="navbar-nav">
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="4" role="button"
                                data-bs-toggle="dropdown" aria-expanded="false">Logout</a>
                            <ul class="dropdown-menu dropdown-menu-dark" aria-labelledby="navbarDarkDropdownMenuLink">
                                <li><a class="dropdown-item" href="/usuario/alterar-senha"><i
                                            class="mdi mdi-lock-open-outline font-size-17 text-muted align-middle mr-1"></i>Alterar
                                        Senha</a></li>
                                <li><a class="dropdown-item" id="sair"><i
                                            class="mdi mdi-power font-size-17 text-muted align-middle mr-1 text-danger"></i>
                                        {{ __('Sair') }}</a></li>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                    style="display: none;">
                                    @csrf
                                </form>
                            </ul>
                        </li>
                    </ul>
                </div>
                    <div class=" fst-italic align-middle d-flex d-none d-lg-block justify-d-content-end" style="color:white">{{ DB::table('versoes_venus')->where('dt_fim', NULL)->first()->versao }}</div>
            </div>
        </div>
    </nav>
</div>

<script>



    function checkSession(){
        $.ajax({
            type: "GET",
             url: "/usuario/sessao",
             dataType: "json",
             success: function(response) {

                session=response

             },
             error: function(xhr) {
                 console.log(xhr.responseText);
             }
         });
    }

    if($('#ADM .dropdown-item').length == 0){
        $('#ADM').hide();
    }

    $('#sair').click(function(){
        checkSession();
        setTimeout(function(){
            if(session == 0){
                window. location. replace("/login/valida")
            }else{
                console.log(session)
                document.getElementById('logout-form').submit();
            }
        }, 1000);


    })

</script>
