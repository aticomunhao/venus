<?php $acesso = session()->get('usuario.acesso');

$setores = [];
foreach (session()->get('acessoInterno') as $perfil) {
    $setores = array_merge($setores, array_column($perfil, 'id_setor'));
}

$setores = DB::table('setor as st')->leftJoin('setor as stp', 'st.setor_pai', 'stp.id')->leftJoin('setor as sta', 'stp.setor_pai', 'sta.id')->select('st.id as ids', 'stp.id as idp', 'sta.id as ida')->whereIn('st.id', $setores)->get()->toArray();

$setores = array_unique(array_merge(array_column($setores, 'ids'), array_column($setores, 'idp'), array_column($setores, 'ida')));
?>

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
            @if (in_array(38, $setores))
                <ul class="navbar-nav" id="AME">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="1" role="button"
                            data-bs-toggle="dropdown" aria-expanded="false">Gerenciar AME</a>
                        <ul class="dropdown-menu dropdown-menu-dark" aria-labelledby="navbarDarkDropdownMenuLink">
                            @if (in_array(14, $acesso))
                                <li><a class="dropdown-item" href="/gerenciar-grupos-membro">Administrar Grupos</a>
                                </li>
                            @endif

                            {{-- @if (in_array(7, $acesso))
                                <li><a class="dropdown-item" href="/atendendo-afe">Atendimento Fraterno
                                        Especifico</a>
                                </li>
                            @endif --}}
                            @if (in_array(7, $acesso))
                            <li><a class="dropdown-item" href="/atendendo">Atendimento Fraterno</a>
                            </li>
                        @endif
                            @if (in_array(33, $acesso))
                                <li><a class="dropdown-item" href="/relatorio-salas-cronograma">Calendário</a>
                                </li>
                            @endif
                            @if (in_array(23, $acesso))
                                <li><a class="dropdown-item" href="/gerenciar-encaminhamentos">Encaminhamentos
                                    </a></li>
                            @endif

                            @if (in_array(9, $acesso))
                                <li><a class="dropdown-item" href="/gerenciar-entrevistas">Entrevistas</a>
                                </li>
                            @endif

                            @if (in_array(3, $acesso))
                                <li><a class="dropdown-item" href="/gerenciar-pessoas">Pessoas</a></li>
                            @endif

                            @if (in_array(25, $acesso))
                                <li><a class="dropdown-item" href="/gerenciar-integral">Tratamento
                                        Integral</a></li>
                            @endif
                            @if (in_array(30, $acesso))
                                <li><a class="dropdown-item" href="/gerenciar-presenca-dirigente">Presença
                                        Trabalhador</a></li>
                            @endif
                            @if (in_array(47, $acesso))
                                <li><a class="dropdown-item" href="/gerenciar-balanco-voluntarios">Balanço de
                                        Voluntários</a>
                                </li>
                            @endif
                            @if (in_array(35, $acesso))
                                <li><a class="dropdown-item" href="/gerenciar-relatorio-tratamento">Relatório de
                                        Tratamentos</a>
                                </li>
                            @endif
                             @if (in_array(57, $acesso))
                                <li><a class="dropdown-item" href="/relatorio-geral-atendimento">Relatório Geral de
                                        Passes</a>
                                </li>
                            @endif
                            @if (in_array(34, $acesso))
                                <li><a class="dropdown-item" href="/gerenciar-relatorio-pessoas-grupo">Histórico de
                                        Membros</a>
                                </li>
                            @endif
                            @if (in_array(34, $acesso))
                                <li><a class="dropdown-item" href='/relatorio-setor-trabalhador'>Relatório de Membros
                                        por Setor</a>
                                </li>
                            @endif
                            @if (in_array(35, $acesso))
                                <li><a class="dropdown-item" href="/gerenciar-relatorio-reuniao">Relatório de
                                        Reuniões</a>
                                </li>
                            @endif
                            @if (in_array(48, $acesso))
                                <li><a class="dropdown-item" href='/relatorio-vagas-grupos'>Relatório de
                                        Vagas em Grupos</a>
                                </li>
                            @endif
                        </ul>
                    </li>
                </ul>
            @endif
            @if (in_array(31, $setores))
                <ul class="navbar-nav" id="DAC">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="2" role="button"
                            data-bs-toggle="dropdown" aria-expanded="false">Gerenciar DAC</a>
                        <ul class="dropdown-menu dropdown-menu-dark" aria-labelledby="navbarDarkDropdownMenuLink">

                            @if (in_array(14, $acesso))
                                <li><a class="dropdown-item" href="/gerenciar-grupos-membro">Administrar Grupos</a>
                                </li>
                            @endif
                            @if (in_array(33, $acesso))
                                <li><a class="dropdown-item" href="/relatorio-salas-cronograma">Calendário</a>
                                </li>
                            @endif
                            @if (in_array(3, $acesso))
                                <li><a class="dropdown-item" href="/gerenciar-pessoas">Pessoas</a></li>
                            @endif
                            @if (in_array(15, $acesso))
                                <li><a class="dropdown-item" href="/gerenciar-habilidade">Habilidades</a></li>
                            @endif
                            @if (in_array(30, $acesso))
                                <li><a class="dropdown-item" href="/gerenciar-presenca-dirigente">Presença
                                        Trabalhador</a></li>
                            @endif


                            @if (in_array(47, $acesso))
                                <li><a class="dropdown-item" href="/gerenciar-balanco-voluntarios">Balanço de
                                        Voluntários</a>
                                </li>
                            @endif
                            @if (in_array(34, $acesso))
                                <li><a class="dropdown-item" href="/gerenciar-relatorio-pessoas-grupo">Histórico de
                                        Membros</a>
                                </li>
                            @endif
                            @if (in_array(34, $acesso))
                                <li><a class="dropdown-item" href='/relatorio-setor-trabalhador'>Relatório de Membros
                                        por Setor</a>
                                </li>
                            @endif
                            @if (in_array(57, $acesso))
                                <li><a class="dropdown-item" href="/relatorio-geral-atendimento">Relatório Geral de
                                        Passes</a>
                                </li>
                            @endif
                            @if (in_array(35, $acesso))
                                <li><a class="dropdown-item" href="/gerenciar-relatorio-reuniao">Relatório de
                                        Reuniões</a>
                                </li>
                            @endif
                            @if (in_array(48, $acesso))
                                <li><a class="dropdown-item" href='/relatorio-vagas-grupos'>Relatório de
                                        Vagas em Grupos</a>
                                </li>
                            @endif
                        </ul>
                    </li>
                </ul>
            @endif
            @if (in_array(7, $setores))
                <ul class="navbar-nav" id="DAO">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="2" role="button"
                            data-bs-toggle="dropdown" aria-expanded="false">Gerenciar DAO</a>
                        <ul class="dropdown-menu dropdown-menu-dark" aria-labelledby="navbarDarkDropdownMenuLink">

                            @if (in_array(14, $acesso))
                                <li><a class="dropdown-item" href="/gerenciar-grupos-membro">Administrar Grupos</a>
                                </li>
                            @endif
                            @if (in_array(6, $acesso))
                                <li><a class="dropdown-item" href="/atendendo">Atendimento Fraterno</a>
                                </li>
                            @endif
                            @if (in_array(20, $acesso))
                                <li><a class="dropdown-item" href="/gerenciar-atendentes-apoio">Atendente
                                        de
                                        Apoio</a></li>
                            @endif
                            @if (in_array(4, $acesso))
                                <li><a class="dropdown-item" href="/gerenciar-atendente-dia">Atendentes do
                                        dia</a>
                                </li>
                            @endif
                            @if (in_array(5, $acesso))
                                <li><a class="dropdown-item" href="/gerenciar-atendimentos">Cadastrar Assistido
                                    </a></li>
                            @endif
                            @if (in_array(16, $acesso))
                                <li><a class="dropdown-item" href="/gerenciar-encaminhamentos">
                                        Encaminhamentos</a>
                                </li>
                            @endif
                            @if (in_array(33, $acesso))
                                <li><a class="dropdown-item" href="/relatorio-salas-cronograma">Calendário</a>
                                </li>
                            @endif
                            @if (in_array(9, $acesso))
                                <li><a class="dropdown-item" href="/gerenciar-entrevistas">Entrevistas</a>
                                </li>
                            @endif
                            {{-- @if (in_array(10, $acesso))
                                    <li><a class="dropdown-item" href="/gerenciar-grupos">Grupos</a></li>
                                @endif --}}
                            @if (in_array(3, $acesso))
                                <li><a class="dropdown-item" href="/gerenciar-pessoas">Pessoas</a></li>
                            @endif
                            @if (in_array(8, $acesso))
                                <li><a class="dropdown-item" href="/gerenciar-presenca">Presença
                                        Entrevistado</a></li>
                            @endif
                            @if (in_array(30, $acesso))
                                <li><a class="dropdown-item" href="/gerenciar-presenca-dirigente">Presença
                                        Trabalhador</a></li>
                            @endif
                            {{-- @if (in_array(19, $acesso))
                                    <li><a class="dropdown-item" href="/gerenciar-reunioes">Reuniões</a></li>
                                @endif --}}
                            @if (in_array(18, $acesso))
                                <li><a class="dropdown-item" href="/gerenciar-tratamentos">Tratamentos/Presença</a>
                                </li>
                            @endif
                            @if (in_array(47, $acesso))
                                <li><a class="dropdown-item" href="/gerenciar-balanco-voluntarios">Balanço de
                                        Voluntários</a>
                                </li>
                            @endif
                            @if (in_array(46, $acesso))
                                <li><a class="dropdown-item" href="/gerenciar-relatorio-atendimento">Relatório de
                                        Atendimentos</a>
                                </li>
                            @endif
                            @if (in_array(57, $acesso))
                                <li><a class="dropdown-item" href="/relatorio-geral-atendimento">Relatório Geral de
                                        Passes</a>
                                </li>
                            @endif
                            @if (in_array(35, $acesso))
                                <li><a class="dropdown-item" href="/gerenciar-relatorio-tratamento">Relatório de
                                        Tratamentos</a>
                                </li>
                            @endif
                            @if (in_array(31, $acesso))
                                <li><a class="dropdown-item" href="/gerenciar-relatorio-afi">Relatório de Presença
                                        AFI</a>
                                </li>
                            @endif
                            @if (in_array(32, $acesso))
                                <li><a class="dropdown-item" href="/relatorio-tematicas">Relatório de Temáticas</a>
                                </li>
                            @endif
                            @if (in_array(34, $acesso))
                                <li><a class="dropdown-item" href="/gerenciar-relatorio-pessoas-grupo">Histórico de
                                        Membros</a>
                                </li>
                            @endif
                            @if (in_array(34, $acesso))
                                <li><a class="dropdown-item" href='/relatorio-setor-trabalhador'>Relatório de Membros
                                        por Setor</a>
                                </li>
                            @endif
                            @if (in_array(35, $acesso))
                                <li><a class="dropdown-item" href="/gerenciar-relatorio-reuniao">Relatório de
                                        Reuniões</a>
                                </li>
                            @endif
                            @if (in_array(51, $acesso))
                                <li><a class="dropdown-item" href="/visualizarRI-tratamento">Lista de
                                        Tratamentos</a>
                                </li>
                            @endif
                            @if (in_array(48, $acesso))
                                <li><a class="dropdown-item" href='/relatorio-vagas-grupos'>Relatório de
                                        Vagas em Grupos</a>
                                </li>
                            @endif
                        </ul>
                    </li>
                </ul>
            @endif
            @if (in_array(6, $setores))
                <ul class="navbar-nav" id="DAE">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="2" role="button"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            Gerenciar DAE
                        </a>
                        <ul class="dropdown-menu dropdown-menu-dark" aria-labelledby="navbarDarkDropdownMenuLink">
                            @if (in_array(14, $acesso))
                                <li><a class="dropdown-item" href="/gerenciar-grupos-membro">Administrar Grupos</a>
                                </li>
                            @endif
                            @if (in_array(33, $acesso))
                                <li><a class="dropdown-item" href="/relatorio-salas-cronograma">Calendário</a>
                                </li>
                            @endif
                            @if (in_array(22, $acesso) or in_array(40, $acesso))
                                <li><a class="dropdown-item" href="/gerenciar-encaminhamentos">Encaminhamentos</a>
                                </li>
                            @endif
                            @if (in_array(24, $acesso))
                                <li><a class="dropdown-item" href="/gerenciar-pti">Tratamento PTI</a></li>
                            @endif
                            @if (in_array(9, $acesso))
                                <li><a class="dropdown-item" href="/gerenciar-entrevistas">Entrevistas</a></li>
                            @endif
                            {{-- @if (in_array(10, $acesso))
                                <li><a class="dropdown-item" href="/gerenciar-grupos">Gerenciar Grupos</a></li>
                            @endif --}}
                            @if (in_array(15, $acesso))
                                <li><a class="dropdown-item" href="/gerenciar-habilidade">Habilidades</a></li>
                            @endif
                            @if (in_array(39, $acesso))
                                <li><a class="dropdown-item" href="/gerenciar-passe">Passes</a></li>
                            @endif
                            @if (in_array(30, $acesso))
                                <li><a class="dropdown-item" href="/gerenciar-presenca-dirigente">Presença
                                        Trabalhador</a></li>
                            @endif
                            @if (in_array(3, $acesso))
                                <li><a class="dropdown-item" href="/gerenciar-pessoas">Pessoas</a></li>
                            @endif
                            @if (in_array(21, $acesso))
                                <li><a class="dropdown-item"
                                        href="/gerenciar-atendentes-plantonistas">Plantonistas</a></li>
                            @endif
                            @if (in_array(41, $acesso))
                                <li><a class="dropdown-item" href="/gerenciar-proamo">
                                        Tratamento PROAMO</a></li>
                            @endif
                            {{-- @if (in_array(19, $acesso))
                                <li><a class="dropdown-item" href="/gerenciar-reunioes">Gerenciar Reuniões </a></li>
                            @endif --}}
                            @if (in_array(47, $acesso))
                                <li><a class="dropdown-item" href="/gerenciar-balanco-voluntarios">Balanço de
                                        Voluntários</a>
                                </li>
                            @endif
                            @if (in_array(35, $acesso))
                                <li><a class="dropdown-item" href="/gerenciar-relatorio-tratamento">Relatório de
                                        Tratamentos</a>
                                </li>
                            @endif
                            @if (in_array(57, $acesso))
                                <li><a class="dropdown-item" href="/relatorio-geral-atendimento">Relatório Geral de
                                        Passes</a>
                                </li>
                            @endif
                            @if (in_array(34, $acesso))
                                <li><a class="dropdown-item" href="/gerenciar-relatorio-pessoas-grupo">Histórico de
                                        Membros</a></li>
                            @endif
                            @if (in_array(34, $acesso))
                                <li><a class="dropdown-item" href='/relatorio-setor-trabalhador'>Relatório de Membros
                                        por Setor</a>
                                </li>
                            @endif
                            @if (in_array(35, $acesso))
                                <li><a class="dropdown-item" href="/gerenciar-relatorio-reuniao">Relatório de
                                        Reuniões</a></li>
                            @endif
                            @if (in_array(48, $acesso))
                                <li><a class="dropdown-item" href='/relatorio-vagas-grupos'>Relatório de Vagas em
                                        Grupos</a></li>
                            @endif
                            @if (in_array(52, $acesso))
                                <li><a class="dropdown-item" href='/relatorio-passes'>Relatório Quantidade Passes</a>
                                </li>
                            @endif

                        </ul>
                    </li>
                </ul>
            @endif
            @if (in_array(32, $setores))
                <ul class="navbar-nav" id="DED">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="2" role="button"
                            data-bs-toggle="dropdown" aria-expanded="false">Gerenciar DED</a>
                        <ul class="dropdown-menu dropdown-menu-dark" aria-labelledby="navbarDarkDropdownMenuLink">

                            @if (in_array(14, $acesso))
                                <li><a class="dropdown-item" href="/gerenciar-grupos-membro">Administrar Grupos</a>
                                </li>
                            @endif
                            @if (in_array(33, $acesso))
                                <li><a class="dropdown-item" href="/relatorio-salas-cronograma">Calendário</a>
                                </li>
                            @endif
                            @if (in_array(3, $acesso))
                                <li><a class="dropdown-item" href="/gerenciar-pessoas">Pessoas</a></li>
                            @endif

                            @if (in_array(30, $acesso))
                                <li><a class="dropdown-item" href="/gerenciar-presenca-dirigente">Presença
                                        Trabalhador</a></li>
                            @endif


                            @if (in_array(47, $acesso))
                                <li><a class="dropdown-item" href="/gerenciar-balanco-voluntarios">Balanço de
                                        Voluntários</a>
                                </li>
                            @endif
                            @if (in_array(34, $acesso))
                                <li><a class="dropdown-item" href="/gerenciar-relatorio-pessoas-grupo">Histórico de
                                        Membros</a>
                                </li>
                            @endif
                            @if (in_array(57, $acesso))
                                <li><a class="dropdown-item" href="/relatorio-geral-atendimento">Relatório Geral de
                                        Passes</a>
                                </li>
                            @endif
                            @if (in_array(34, $acesso))
                                <li><a class="dropdown-item" href='/relatorio-setor-trabalhador'>Relatório de Membros
                                        por Setor</a>
                                </li>
                            @endif
                            @if (in_array(35, $acesso))
                                <li><a class="dropdown-item" href="/gerenciar-relatorio-reuniao">Relatório de
                                        Reuniões</a>
                                </li>
                            @endif
                            @if (in_array(48, $acesso))
                                <li><a class="dropdown-item" href='/relatorio-vagas-grupos'>Relatório de
                                        Vagas em Grupos</a>
                                </li>
                            @endif
                        </ul>
                    </li>
                </ul>
            @endif
               @if (in_array(17, $setores))
                <ul class="navbar-nav" id="DPS">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="2" role="button"
                            data-bs-toggle="dropdown" aria-expanded="false">Gerenciar DPS</a>
                        <ul class="dropdown-menu dropdown-menu-dark" aria-labelledby="navbarDarkDropdownMenuLink">

                            @if (in_array(14, $acesso))
                                <li><a class="dropdown-item" href="/gerenciar-grupos-membro">Administrar Grupos</a>
                                </li>
                            @endif
                            @if (in_array(33, $acesso))
                                <li><a class="dropdown-item" href="/relatorio-salas-cronograma">Calendário</a>
                                </li>
                            @endif
                            @if (in_array(3, $acesso))
                                <li><a class="dropdown-item" href="/gerenciar-pessoas">Pessoas</a></li>
                            @endif
                            @if (in_array(15, $acesso))
                                <li><a class="dropdown-item" href="/gerenciar-habilidade">Habilidades</a></li>
                            @endif
                            @if (in_array(30, $acesso))
                                <li><a class="dropdown-item" href="/gerenciar-presenca-dirigente">Presença
                                        Trabalhador</a></li>
                            @endif


                            @if (in_array(47, $acesso))
                                <li><a class="dropdown-item" href="/gerenciar-balanco-voluntarios">Balanço de
                                        Voluntários</a>
                                </li>
                            @endif
                            @if (in_array(34, $acesso))
                                <li><a class="dropdown-item" href="/gerenciar-relatorio-pessoas-grupo">Histórico de
                                        Membros</a>
                                </li>
                            @endif
                            @if (in_array(34, $acesso))
                                <li><a class="dropdown-item" href='/relatorio-setor-trabalhador'>Relatório de Membros
                                        por Setor</a>
                                </li>
                            @endif
                            @if (in_array(57, $acesso))
                                <li><a class="dropdown-item" href="/relatorio-geral-atendimento">Relatório Geral de
                                        Passes</a>
                                </li>
                            @endif
                            @if (in_array(35, $acesso))
                                <li><a class="dropdown-item" href="/gerenciar-relatorio-reuniao">Relatório de
                                        Reuniões</a>
                                </li>
                            @endif
                            @if (in_array(48, $acesso))
                                <li><a class="dropdown-item" href='/relatorio-vagas-grupos'>Relatório de
                                        Vagas em Grupos</a>
                                </li>
                            @endif
                        </ul>
                    </li>
                </ul>
            @endif
            <ul class="navbar-nav" id="ADM">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="3" role="button"
                        data-bs-toggle="dropdown" aria-expanded="false">Gerenciar ATI</a>
                    <ul class="dropdown-menu dropdown-menu-dark" aria-labelledby="navbarDarkDropdownMenuLink">
                        @if (in_array(33, $acesso))
                            <li><a class="dropdown-item" href="/relatorio-salas-cronograma">Calendário</a>
                            </li>
                        @endif
                        @if (in_array(11, $acesso))
                            <li><a class="dropdown-item" href="/gerenciar-fatos">Fatos</a></li>
                        @endif
                        @if (in_array(12, $acesso))
                            <li><a class="dropdown-item" href="/gerenciar-salas">Salas</a></li>
                        @endif
                        @if (in_array(26, $acesso))
                            <li><a class="dropdown-item" href="/gerenciar-perfis">Perfis</a></li>
                        @endif
                        @if (in_array(27, $acesso))
                            <li><a class="dropdown-item" href="/gerenciar-setor">Setor</a></li>
                        @endif
                        @if (in_array(1, $acesso))
                            <li><a class="dropdown-item" href="/gerenciar-usuario">Usuários</a></li>
                        @endif
                        @if (in_array(28, $acesso))
                            <li><a class="dropdown-item" href="/gerenciar-versoes">Versões</a></li>
                        @endif
                        @if (in_array(10, $acesso))
                            <li><a class="dropdown-item" href="/gerenciar-grupos">Grupos</a></li>
                        @endif

                        @if (in_array(19, $acesso))
                            <li><a class="dropdown-item" href="/gerenciar-reunioes">Reuniões </a>
                            </li>
                        @endif
                        @if (in_array(53, $acesso))
                            <li><a class="dropdown-item" href="/gerenciar-log-atendimentos">Log Atendimentos</a>
                            </li>
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
                            <form id="logout-form" action="{{ route('logout-invalidate') }}" method="POST"
                                style="display: none;">
                                @csrf
                            </form>
                        </ul>
                    </li>
                </ul>
            </div>
            <div class=" fst-italic align-middle d-flex d-none d-lg-block justify-d-content-end" style="color:white">
                {{ DB::table('versoes_venus')->where('dt_fim', null)->first()->versao }}</div>
        </div>
    </div>
</nav>
</div>

<script>
    function checkSession() {
        $.ajax({
            type: "GET",
            url: "/usuario/sessao",
            dataType: "json",
            success: function(response) {

                session = response

            },
            error: function(xhr) {
                console.log(xhr.responseText);
            }
        });
    }

    if ($('#ADM .dropdown-item').length == 0) {
        $('#ADM').hide();
    }

    $('#sair').click(function() {
        checkSession();
        setTimeout(function() {
            if (session == 0) {
                window.location.replace("/login/valida")
            } else {
                console.log(session)
                document.getElementById('logout-form').submit();
                //document.getElementById('logout-form').submit();
            }
        }, 1000);


    })
</script>
