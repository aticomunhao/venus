@extends('layouts.app')
@section('title', 'Visualizar Entrevista')
@section('content')
    <div class="container">
        <br>
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col">VISUALIZAR ENTREVISTA</div>
                </div>
            </div>
            <br>
            <div class="card-body">
                <form class="form-horizontal mt-2" method="post" action="/visualizar-entrevista/{{ $id }}">
                    @csrf
                    <div class="row mb-5">
                        <div class="col">
                            <label for="id_encaminhamento_nome" class="form-label">Nome assistido</label>
                            <input class="form-control" id="id_encaminhamento_nome" name="id_encaminhamento_nome"
                                value="{{ $entrevistas->nome_completo }}" disabled>
                        </div>
                        <div class="col">
                            <label for="id_encaminhamento_telefone" class="form-label">Telefone</label>
                            <input class="form-control" id="id_encaminhamento_telefone" name="id_encaminhamento_telefone"
                                value="{{ $entrevistas->ddd ? '(' . $entrevistas->ddd . ')' : null }} {{ $entrevistas->celular }}"
                                disabled>

                        </div>
                    </div>

                    <div class="row mb-5">
                        <div class="col">
                            <label for="id_entrevistador" class="form-label">Entrevistador</label>
                            <input class="form-control" id="id_entrevistador" name="id_entrevistador" disabled
                                value="{{ $entrevistas->entrevistador ?? null }}">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col">
                            <label for="data" class="form-label">Data</label>
                            <input type="date" class="form-control" id="data" name="data"
                                value="{{ $entrevistas->data }}" disabled>
                        </div>
                        <div class="col">
                            <label for="hora" class="form-label">Hora</label>
                            <input type="time" class="form-control" id="hora" name="hora"
                                value="{{ $entrevistas->hora }}" disabled>
                        </div>
                    </div>
            </div>
        </div>
        <br>
        <div class="form-group">
            <div id="accordion">
                <div class="card">
                    <div class="card-header" id="headingOne">
                        SALA
                    </div>
                    <div id="collapseOne" class="collapse show" aria-labelledby="headingOne" data-parent="#accordion">
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col">
                                    <label for="numero" class="form-label">Número </label>
                                    <input type="text" class="form-control" id="numero" name="numero"
                                        value="{{ $entrevistas ? $entrevistas->numero : '' }}" readonly disabled>
                                </div>
                                <div class="col">
                                    <label for="nome" class="form-label">Nome </label>
                                    <input type="text" class="form-control" id="nome" name="nome"
                                        value="{{ $entrevistas ? $entrevistas->nome : '' }}" readonly disabled>
                                </div>
                                <div class="col">
                                    <label for="localizacao" class="form-label">Localização</label>
                                    <input type="text" class="form-control" id="localizacao" name="localizacao"
                                        value="{{ $entrevistas ? $entrevistas->local : '' }}" readonly disabled>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <br>

        @if($presencas)
        <div class="card">
            <div class="card-header" id="headingOne">
                PRESENÇAS
            </div>
            <div id="collapseOne" class="collapse show" aria-labelledby="headingOne" data-parent="#accordion">
                <div class="card-body">

                    <table class="table table-sm table-bordered table-striped">
                        <thead style="text-align:center; background: #daffe0;">
                            <tr style="text-align:center; font-weight: bold; font-size:12px">
                                <td>DATA</td>
                                <td>GRUPO</td>
                                <td>PRESENÇA</td>
                            </tr>
                        </thead>
                        <tbody>

                            @foreach($presencas as $presenca)
                            <tr style="text-align:center;font-size:13px">
                                <td> {{ date('d-m-Y', strtotime($presenca->data)) }} </td>
                                <td>{{ $presenca->nome }}</td>
                                @if ($presenca->presenca == true)
                                <td style="background-color:#90EE90;">Sim</td>
                            @else
                                <td style="background-color:#FA8072;">Não</td>
                            @endif
                            </tr>

                            @endforeach
                        </tbody>

                    </table>

                </div>
            </div>
        </div>
@endif
        <br>

        <div class="row mt-4 justify-content-center">
            <div class="d-grid gap-1 col-4 mx-auto">
                <a class="btn btn-danger" href="/gerenciar-entrevistas" role="button">Fechar</a>
            </div>
        </div>
        </form>
    </div>
    </div>
    </div>
    <script>
        document.getElementById('id_sala').addEventListener('change', function() {
            var selectedOption = this.options[this.selectedIndex];
            document.getElementById('numero').value = selectedOption.getAttribute('data-numero');
            document.getElementById('localizacao').value = selectedOption.getAttribute('data-localizacao');
        });
    </script>
@endsection
