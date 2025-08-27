@extends('layouts.app')

@section('title')
    Incluir Instituições
@endsection

@section('content')
    <br>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12">
                <form class="form-horizontal" method="post" action="/salvar-instituicao" enctype="multipart/form-data">
                    @csrf
                    <!-- Card principal -->
                    <div class="card">
                        <div class="card-header">
                            <div class="row">
                                <div class="col">
                                    INSERIR INSTITUIÇÕES
                                </div>
                                <a href="{{ route('index.instituicao') }}" class="btn-close" aria-label="Fechar"></a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="col-12">
                                <div class="row">
                                    <div class="col-4 mb-3">
                                        <label for="cnpj">CNPJ *</label>
                                        <input type="text" class="form-control" name="cnpj" id="cnpj"
                                            maxlength="18" required placeholder="Apenas números"
                                            value="{{ old('cnpj') }}">
                                    </div>
                                    <div class="col-4">
                                        <label for="nome_fantasia">Nome Fantasia *</label>
                                        <input type="text" class="form-control" name="nome_fantasia" required
                                            maxlength="100" value="{{ old('nome_fantasia') }}">
                                    </div>

                                    <div class="col-4">
                                        <label for="razao_social">Razão Social *</label>
                                        <input type="text" class="form-control" name="razao_social" required
                                            maxlength="100" value="{{ old('razao_social') }}">
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-4">
                                        <label for="insc_est">Inscrição Estadual</label>
                                        <input type="text" class="form-control" name="insc_est" maxlength="14"
                                            value="{{ old('insc_est') }}">
                                    </div>
                                    <div class="col-4">
                                        <label for="nome_cont">Nome de Contato</label>
                                        <input type="text" class="form-control" name="nome_cont" maxlength="100"
                                            value="{{ old('nome_cont') }}">
                                    </div>
                                    <div class="col-4">
                                        <label for="email_contato">Email de Contato</label>
                                        <input type="email" class="form-control" name="email_contato" maxlength="100"
                                            value="{{ old('email_contato') }}">
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-4">
                                        <label for="ibge">Ibge *</label>
                                        <input type="text" class="form-control" name="ibge" required maxlength="7"
                                            value="{{ old('ibge') }}">
                                    </div>
                                    <div class="col-8">
                                        <label for="site">Site</label>
                                        <input type="url" class="form-control" name="site" maxlength="100"
                                            value="{{ old('site') }}">
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-4">
                                        <label for="cnpj">CEP *</label>
                                        <input type="text" class="form-control" name="cep" id="cep"
                                            maxlength="9" required placeholder="Apenas números"
                                            value="{{ old('cep') }}">
                                    </div>
                                    <div class="col-4">
                                        <label for="logradouro">Logradouro *</label>
                                        <input type="text" class="form-control" name="logradouro" id="logradouro"
                                            maxlength="100" maxlength="18" required value="{{ old('logradouro') }}">
                                    </div>
                                    <div class="col-4">
                                        <label for="bairro">Bairro *</label>
                                        <input type="text" class="form-control" name="bairro" id="bairro" required
                                            maxlength="50" value="{{ old('bairro') }}">
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-4">
                                        <label for="uf">Uf *</label>
                                        <select class="form-control select2" name="uf" id="uf">
                                            <option value=""></option>
                                            @foreach ($uf as $item)
                                                <option value="{{ $item->id }}"
                                                    {{ old('uf') == $item->id ? 'selected' : '' }}>
                                                    {{ $item->sigla }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-4">
                                        <label for="cidade">Cidade *</label>
                                        <select class="js-example-responsive form-select select2" id="cidade"
                                            name="cidade" style="border: 1px solid #999999 !important; padding: 5px;"
                                            {{ old('cidade') ? '' : 'disabled' }}>
                                            @if (old('cidade'))
                                                <option value="{{ old('cidade') }}" selected>{{ old('cidade') }}</option>
                                            @endif
                                        </select>
                                    </div>
                                    <div class="col-4">
                                        <label for="complemento">Complemento</label>
                                        <input type="text" class="form-control" name="complemento" id="complemento"
                                            maxlength="50" value="{{ old('complemento') }}">
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-4">
                                        <label for="unidade">Unidade</label>
                                        <input type="text" class="form-control" name="unidade" id="unidade"
                                            maxlength="50" maxlength="18" value="{{ old('unidade') }}">
                                    </div>
                                    <div class="col-4">
                                        <label for="gia">Gia</label>
                                        <input type="text" class="form-control" name="gia" maxlength="8"
                                            value="{{ old('gia') }}">
                                    </div>
                                    <div class="col-4">
                                        <label for="numero">Número</label>
                                        <input type="text" class="form-control" name="numero" maxlength="10"
                                            value="{{ old('numero') }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Botões de ação -->
                    <br>
                    <div class="row mb-3">
                        <div class="d-grid gap-1 col-4 mx-auto">
                            <a class="btn btn-danger" href="{{ route('index.instituicao') }}"
                                role="button">Cancelar</a>
                        </div>
                        <div class="d-grid gap-2 col-4 mx-auto">
                            <button type="submit" class="btn btn-primary" style="color:#fff;">Confirmar</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script>
        $(document).ready(function() {
            let estados = @JSON($uf);

            // Evento de preenchimento via CEP
            $('#cep').on('input', function() {
                let cep = $(this).val().replace(/\D/g, '');

                if (cep.length === 8) {
                    $.ajax({
                        type: "GET",
                        url: 'https://viacep.com.br/ws/' + cep + '/json/',
                        dataType: "json",
                        success: function(response) {
                            if (response.erro) {
                                alert('CEP não encontrado.');
                                limparCamposEndereco();
                                return;
                            }

                            $('#logradouro').val(response.logradouro);
                            $('#bairro').val(response.bairro);
                            $('#complemento').val(response.complemento);

                            let estadoEncontrado = estados.find(estado => estado.sigla ===
                                response.uf);

                            if (estadoEncontrado) {
                                $('#uf').val(estadoEncontrado.id).trigger('change');

                                populateCities($('#cidade'), estadoEncontrado.id, response
                                    .localidade);
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error("Erro ao buscar o CEP:", error);
                        }
                    });
                }
            });

            // Função para carregar cidades
            function populateCities(selectElement, uf, cidadeNome = null) {
                $.ajax({
                    type: "GET",
                    url: "/retorna-cidades/" + uf,
                    dataType: "JSON",
                    success: function(response) {
                        selectElement.empty().removeAttr('disabled');

                        $.each(response, function(indexInArray, item) {
                            selectElement.append('<option value="' + item.id_cidade + '">' +
                                item.descricao + '</option>');
                        });

                        if (cidadeNome) {
                            // Seleciona cidade pelo nome (via CEP)
                            let cidadeEncontrada = response.find(item => item.descricao
                            .toLowerCase() === cidadeNome.toLowerCase());
                            if (cidadeEncontrada) {
                                selectElement.val(cidadeEncontrada.id_cidade).trigger('change');
                            }
                        } else if ("{{ old('cidade') }}") {
                            // Seleciona cidade pelo ID (old value)
                            selectElement.val("{{ old('cidade') }}").trigger('change');
                        }
                    }
                });
            }

            // Preenche cidades automaticamente se houver old('uf') na validação
            if ("{{ old('uf') }}") {
                $('#uf').val("{{ old('uf') }}");
                populateCities($('#cidade'), "{{ old('uf') }}");
            }
        });
    </script>
@endsection
