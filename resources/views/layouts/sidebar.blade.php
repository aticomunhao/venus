<?php
$acesso = session()->get('usuario.acesso');
// dd(session()->get('usuario'));
// dd(session()->get('usuario'));

$setores = [];
foreach (session()->get('acessoInterno') as $perfil) {
    $setores = array_merge($setores, array_column($perfil, 'id_setor'));
}
$setores = DB::table('setor as st')
    ->leftJoin('setor as stp', 'st.setor_pai', 'stp.id')
    ->leftJoin('setor as sta', 'stp.setor_pai', 'sta.id')
    ->select('st.id as ids', 'stp.id as idp', 'sta.id as ida')
    ->whereIn('st.id', $setores)
    ->get()
    ->toArray();

$setores = array_unique(array_merge(array_column($setores, 'ids'), array_column($setores, 'idp'), array_column($setores, 'ida')));
$acessos_vindo_do_banco = DB::table('tipo_rotas')->pluck('id')->toArray();
$acesso = array_intersect($acesso, $acessos_vindo_do_banco);

// Menu completo definido dinamicamente
$menus = [
    'Atendimentos' => [
        'ids' => [3,4,5,6],
        'items' => [
            4 => ['title' => 'Atendentes do dia', 'url' => '/gerenciar-atendente-dia'],
            6 => ['title' => 'Atendimento Fraterno', 'url' => '/atendendo'],
            5 => ['title' => 'Cadastrar Assistido', 'url' => '/gerenciar-atendimentos'],
            3 => ['title' => 'Cadastrar Pessoa', 'url' => '/gerenciar-pessoas'],
        ],
    ],
    'Agendamento' => [
        'ids' => [16,22,23,40,9,18,30,8],
        'items' => [
            16 => ['title' => 'Encaminhamentos', 'url' => '/gerenciar-encaminhamentos'],
            9  => ['title' => 'Entrevistas', 'url' => '/gerenciar-entrevistas'],
            18 => ['title' => 'Presença Assistido', 'url' => '/gerenciar-tratamentos'],
            8  => ['title' => 'Presença Entrevistado', 'url' => '/gerenciar-presenca'],
            30 => ['title' => 'Presença Trabalhador', 'url' => '/gerenciar-presenca-dirigente'],
        ],
    ],
    'Tratamentos' => [
        'ids' => [24,25,39,41],
        'items' => [
            39 => ['title' => 'Passes', 'url' => '/gerenciar-passe'],
            25 => ['title' => 'Tratamento Integral', 'url' => '/gerenciar-integral'],
            41 => ['title' => 'Tratamento PROAMO', 'url' => '/gerenciar-proamo'],
            24 => ['title' => 'Tratamento PTI', 'url' => '/gerenciar-pti'],
        ],
    ],
    'Membros' => [
        'ids' => [14,15,20,21],
        'items' => [
            14 => ['title' => 'Administrar Grupos', 'url' => '/gerenciar-grupos-membro'],
            20 => ['title' => 'Atendente de Apoio', 'url' => '/gerenciar-atendentes-apoio'],
            15 => ['title' => 'Habilidades', 'url' => '/gerenciar-habilidade'],
            21 => ['title' => 'Plantonistas', 'url' => '/gerenciar-atendentes-plantonistas'],
        ],
    ],
    'Relatórios' => [
        'ids' => [31,32,33,34,35,46,47,48,51,52,57],
        'items' => [
            31 => ['title' => 'Relatório de Presença AFI', 'url' => '/gerenciar-relatorio-afi'],
            32 => ['title' => 'Relatório de Temáticas', 'url' => '/relatorio-tematicas'],
            33 => ['title' => 'Calendário', 'url' => '/relatorio-salas-cronograma'],
            34 => ['title' => 'Histórico de Membros', 'url' => '/gerenciar-relatorio-pessoas-grupo'],
            35 => ['title' => 'Relatório de Reuniões', 'url' => '/gerenciar-relatorio-reuniao'],
            46 => ['title' => 'Relatório de Atendimentos', 'url' => '/gerenciar-relatorio-atendimento'],
            47 => ['title' => 'Balanço de Voluntários', 'url' => '/gerenciar-balanco-voluntarios'],
            48 => ['title' => 'Relatório de Vagas em Grupos', 'url' => '/relatorio-vagas-grupos'],
            51 => ['title' => 'Lista de Tratamentos', 'url' => '/visualizarRI-tratamento'],
            52 => ['title' => 'Relatório Quantidade Passes', 'url' => '/relatorio-passes'],
            57 => ['title' => 'Disponibilidade de Vagas', 'url' => '/relatorio-geral-atendimento2'],
        ],
    ],
    'Estudos' => [
        'ids' => [1,58],
        'items' => [
            1  => ['title' => 'Gerenciar Requisitos', 'url' => route('index.req')],
            58 => ['title' => 'Gerenciar Estudos Externos', 'url' => '/gerenciar-estudos-externos'],
        ],
    ],
    'Gerenciar ATI' => [
        'ids' => [1,10,11,12,19,26,27,28,53,59],
        'items' => [
            11 => ['title' => 'Fatos', 'url' => '/gerenciar-fatos'],
            12 => ['title' => 'Salas', 'url' => '/gerenciar-salas'],
            26 => ['title' => 'Perfis', 'url' => '/gerenciar-perfis'],
            27 => ['title' => 'Setor', 'url' => '/gerenciar-setor'],
            1  => ['title' => 'Usuários', 'url' => '/gerenciar-usuario'],
            28 => ['title' => 'Versões', 'url' => '/gerenciar-versoes'],
            10 => ['title' => 'Grupos', 'url' => '/gerenciar-grupos'],
            19 => ['title' => 'Reuniões', 'url' => '/gerenciar-reunioes'],
            53 => ['title' => 'Log Atendimentos', 'url' => '/gerenciar-log-atendimentos'],
            59 => ['title' => 'Instituições', 'url' => route('index.instituicao')],
        ],
    ],
];

function renderMenu($menus, $acesso) {
    foreach ($menus as $menuName => $menu) {
        $hasAccess = array_intersect($menu['ids'], $acesso);
        if ($hasAccess) {
            echo '<ul class="navbar-nav me-2">';
            echo '<li class="nav-item dropdown">';
            echo '<a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">' . $menuName . '</a>';
            echo '<ul class="dropdown-menu dropdown-menu-dark">';

            $renderedUrls = []; // evita repetição
            foreach ($menu['items'] as $id => $item) {
                if (in_array($id, $acesso) && !in_array($item['url'], $renderedUrls)) {
                    echo '<li><a class="dropdown-item" href="' . $item['url'] . '">' . $item['title'] . '</a></li>';
                    $renderedUrls[] = $item['url'];
                }
            }

            echo '</ul></li></ul>';
        }
    }
}
?>

<nav class="navbar navbar-expand-lg navbar-light shadow-sm" style="background-color:#60bbf0; font-family:tahoma; font-weight:bold;">
    <div class="container">
        <a class="navbar-brand" style="color: #fff;" href="{{ url('/login/valida') }}">Vênus</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDarkDropdown"
            aria-controls="navbarNavDarkDropdown" aria-expanded="false" aria-label="Toggle navigation" style="border:none">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNavDarkDropdown">
            <?php renderMenu($menus, $acesso); ?>
            <div class="col">
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="logoutMenu" role="button" data-bs-toggle="dropdown" aria-expanded="false">Logout</a>
                        <ul class="dropdown-menu dropdown-menu-dark">
                            <li><a class="dropdown-item" href="/usuario/alterar-senha"><i class="mdi mdi-lock-open-outline font-size-17 text-muted align-middle mr-1"></i>Alterar Senha</a></li>
                            <li><a class="dropdown-item" id="sair"><i class="mdi mdi-power font-size-17 text-muted align-middle mr-1 text-danger"></i>Sair</a></li>
                            <form id="logout-form" action="{{ route('logout-invalidate') }}" method="POST" style="display: none;">
                                @csrf
                            </form>
                        </ul>
                    </li>
                </ul>
            </div>
            <div class="fst-italic align-middle d-flex d-none d-lg-block justify-content-end" style="color:white">
                {{ DB::table('versoes_venus')->where('dt_fim', null)->first()->versao }}
            </div>
        </div>
    </div>
</nav>

<script>
    function checkSession() {
        $.ajax({
            type: "GET",
            url: "/usuario/sessao",
            dataType: "json",
            success: function(response) {
                session = response;
            },
            error: function(xhr) {
                console.log(xhr.responseText);
            }
        });
    }

    $('#sair').click(function() {
        checkSession();
        setTimeout(function() {
            if (session == 0) {
                window.location.replace("/login/valida");
            } else {
                document.getElementById('logout-form').submit();
            }
        }, 1000);
    });
</script>
