@extends('layouts.app')

@section('content')
    <div class="container mt-4">
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col">
                        EDITAR ATENDENTE
                    </div>
                </div>
            </div>
            <div class="card-body">
                <form class="form-horizontal mt-2" method="post" action="/atualizar-atendente/{{ $atendente->id }}">
                    @csrf

                    <div class="row">
                        {{-- Div para input de nomes, atualmente disabled --}}
                        <div class="col">
                            <label for="id_pessoa" class="form-label">Nome</label>
                            <select class="form-control" name="id_pessoa" disabled>
                                <option value="{{ $atendente->id }}">{{ $atendente->nome_completo }}</option>
                                {{-- Parte inútil de código, lixo --}}
                                @foreach ($pessoas as $pessoa)
                                    <option value="{{ $pessoa->idp }}">{{ $pessoa->nome_completo }}</option>
                                @endforeach
                                {{-- Fim da parte inútil --}}
                            </select>
                        </div>
                        {{-- Fim Div input nomes --}}

                        {{-- Inicio row Status/DataFim/Motivo --}}
                        <div class="row mt-4">
                            {{-- Select de status --}}
                            <div class="col">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select" name="status">
                                    {{-- Automaticamente seleciona a opção proveniente do banco de dados --}}
                                    <option value="{{ $atendente->status }}" selected>{{ $atendente->tipos }}</option>
                                    @foreach ($tipo_status_pessoa as $status)
                                    {{-- Testa se o status na tabela Atendente é valido na tabela Status --}}
                                        @if ($status->id != $atendente->id && $status->id != $atendente->status)
                                            <option value="{{ $status->id }}">{{ $status->tipo }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                            {{-- Fim select de Status --}}

                            {{-- Input de data final --}}
                            <div class="col">
                                <label for="data_fim" class="form-label">Data fim</label>
                                <input type="date" class="form-select" id="dt_fim" name="dt_fim" value="{{ $atendente->dt_fim }}">
                            </div>
                            {{-- Fim input data final --}}

                            {{-- Select  Motivo --}}
                            <div class="col">
                                <label for="motivo_status" class="form-label">Motivo</label>
                                <select class="form-select" aria-label=".form-select-lg example" name="motivo_status" id="motivo_status" >
                                    <option value="" {{ is_null($atendente->motivo_status) ? 'selected' : '' }}></option>
                                    @foreach ($tipo_motivo_status_pessoa as $motivo)
                                        @if ($motivo->motivo == 'mudou' || $motivo->motivo == 'desencarnou')
                                            <option value="{{ $motivo->id }}" {{ $atendente->motivo_status == $motivo->id ? 'selected' : '' }}>
                                                {{ $motivo->motivo }}
                                            </option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                            {{-- Fim select motivo --}}
                        </div>
                        {{-- Fim row Status/DataFim/Motivo --}}


                        {{-- Inicio Select2 Multiple Grupos --}}
                        <div class="row mt-4">
                            <div class="col">
                                <div class="col-12 mt-3 mb-3">
                                    <label for="id_grupo" class="form-label">Nome grupo</label>
                                    <select class="form-select select2" aria-label=".form-select-lg example" name="id_grupo[]" id="id_grupo" multiple required>
                                        @foreach ($grupo as $grupos)
                                            <option value="{{ $grupos->id }}" >{{ $grupos->nome }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        {{-- Fim Select2 Multiple Grupos --}}

                        <div class="row mt-4 justify-content-center">
                            {{-- Inicio Botões Cancelar e Confirmar --}}
                            <div class="d-grid gap-1 col-4 mx-auto">
                                <a class="btn btn-danger" href="/gerenciar-atendentes" role="button">Cancelar</a>
                            </div>
                            <div class="d-grid gap-2 col-4 mx-auto">
                                <button type="submit" class="btn btn-primary">Confirmar</button>
                            </div>
                            {{-- Fim Botões Cancelar e Confirmar --}}

                            {{-- Import JQuery --}}
                            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

                            <script>
                                $(document).ready(function() {
                                    $('.select2').select2( { theme: 'bootstrap-5'});

                                        var infos = <?php echo json_encode($info); ?>; //codifica a variavel do PHP para ser aceita pelo JavaScript
                                        var keys = Object.entries(infos); //Pega os valores do JSON e transforma em um array
                                        var len = Object.keys(keys).length; // Pega apenas as Keys do Array e conta a quantidade
                                        const arr = []; //Inicializa uma variavel vetor vazia

                                        for (var i = 0; i < len ; i++) {//Loop de 0 ao tamanho descoberto acima

                                            var key = Object.values(keys[i][1]);//pega o valor do array de duas dimensões e armazena numa variavel
                                            arr.push(key);//Adiciona o valor armazenado ao array vazio

                                          }

                                      $('.select2').val(arr).trigger('change');//pega o Array completo, força como valor do Select e atualiza o campo
                                })

                            </script>
                        </div>
                </form>
            </div>
        </div>
    </div>
@endsection
