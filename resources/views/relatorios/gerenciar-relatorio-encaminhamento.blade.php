@extends('layouts.app')

@section('title', 'Relatório de Encaminhamentos')

@section('content')
<div class="container-fluid">

    <h4 class="card-title" style="font-size:20px; text-align:left; color:gray; font-family:calibri">
        RELATÓRIO DE ENCAMINHAMENTOS
    </h4>
    <br>

    <div class="container-fluid">
        <form method="GET" action="/gerenciar-relatorio-encaminhamento">
            <div class="d-flex flex-wrap align-items-end gap-2 justify-content-start">

                <div class="me-2" style="min-width: 180px;">
                    <label for="dt_inicio" class="form-label mb-1">Data Início</label>
                    <input type="date" name="dt_inicio" id="dt_inicio" class="form-control"
                        value="{{ request('dt_inicio') }}">
                </div>

                <div class="me-2" style="min-width: 180px;">
                    <label for="dt_fim" class="form-label mb-1">Data Fim</label>
                    <input type="date" name="dt_fim" id="dt_fim" class="form-control"
                        value="{{ request('dt_fim') }}">
                </div>

                <div class="me-2" style="min-width: 260px; max-width: 100%;">
                    <label for="search" class="form-label mb-1">Pesquisar</label>
                    <input type="text" name="search" id="search" class="form-control"
                        placeholder="🔍 Pesquisar..." value="{{ request('search') }}">
                </div>

                <div class="d-flex gap-2">
                    <button class="btn btn-success px-4" type="submit">Pesquisar</button>
                    <a href="/gerenciar-relatorio-encaminhamento" class="btn btn-light px-4"
                        style="box-shadow: 1px 1px 5px #000000;">
                        Limpar
                    </a>
                </div>

            </div>
        </form>
    </div>

    <hr>

    <div class="row">
        <table class="table table-sm table-striped table-bordered border-secondary table-hover align-middle" id="tabelaEnca">
            <thead style="text-align:center;">
                <tr style="background-color: #d6e3ff; font-size:14px; color:#000000">
                    <th class="col-1">DIA</th>
                    <th class="col">ASSISTIDO</th>
                    <th class="col d-none" id="colAtendente">ATENDENTE</th>
                    <th class="col">TRATAMENTO</th>
                    <th class="col-1">TEMPO</th>
                    <th class="col-1">STATUS</th>
                    <th class="col-1">MOTIVO</th>
                </tr>
            </thead>

            <tbody style="font-size:14px; color:#000000; text-align:center;">
                @foreach ($encaminhamento as $enc)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($enc->dh_enc)->format('d/m/Y') }}</td>
                        <td>{{ $enc->nome_assistido }}</td>
                        <td class="d-none">
                            {{ $atendente[$enc->id_encaminhamento]->nome_atendente ?? '-' }}
                        </td>
                        <td>{{ $enc->des_trata ?? '-' }}</td>
                        <td>{{ $enc->tempo_atendimento }} min</td>
                        <td>{{ $enc->status }}</td>
                        <td>{{ $enc->motivo ?? '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{-- <div class="mt-3 d-flex justify-content-center">
            {{ $encaminhamento->appends(request()->query())->links('pagination::bootstrap-5') }}
        </div> --}}
    </div>

</div>

<script>
$(document).ready(function() {
    const $searchInput = $('#search');
    const $colAtendente = $('#colAtendente');
    const $rows = $('#tabelaEnca tbody tr');

    // Função para normalizar strings: remove acentos e deixa minúscula
    function normalize(str) {
        return str.normalize('NFD').replace(/[\u0300-\u036f]/g, '').toLowerCase();
    }

    $searchInput.on('keyup', function() {
        const value = normalize($(this).val().trim());
        let showAtendente = false;

        $rows.each(function() {
            const assistido = normalize($(this).find('td:nth-child(2)').text());
            const atendente = normalize($(this).find('td:nth-child(3)').text());
            const tratamento = normalize($(this).find('td:nth-child(4)').text());
            const tempo = normalize($(this).find('td:nth-child(5)').text());
            const status = normalize($(this).find('td:nth-child(6)').text());
            const motivo = normalize($(this).find('td:nth-child(7)').text());

            let match = assistido.includes(value) ||
                        atendente.includes(value) ||
                        tratamento.includes(value) ||
                        tempo.includes(value) ||
                        status.includes(value) ||
                        motivo.includes(value);

            $(this).toggle(match);

            // Mostrar coluna atendente apenas se pesquisa envolver atendente
            if (match && atendente.includes(value)) {
                showAtendente = true;
            }
        });

        if (showAtendente) {
            $colAtendente.removeClass("d-none");
            $("#tabelaEnca tbody tr td:nth-child(3)").removeClass("d-none");
        } else {
            $colAtendente.addClass("d-none");
            $("#tabelaEnca tbody tr td:nth-child(3)").addClass("d-none");
        }
    });
});
</script>
@endsection
