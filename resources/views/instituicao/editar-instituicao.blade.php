@extends('layouts.app')

@section('title')
    Editar Instituições
@endsection

@section('content')
    <br>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12">
                <form class="form-horizontal" method="post" action="/atualizar-instituicao/{{ $instituicao->id }}"
                    enctype="multipart/form-data">
                    @csrf
                    <!-- Card principal -->
                    <div class="card">
                        <div class="card-header">
                            <div class="row">
                                <div class="col">
                                    EDITAR INSTITUIÇÕES
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
                                            value="{{ $instituicao->cnpj }}" maxlength="18" required
                                            placeholder="Apenas números">
                                    </div>
                                    <div class="col-4">
                                        <label for="nome_fantasia">Nome Fantasia *</label>
                                        <input type="text" class="form-control" name="nome_fantasia" maxlength="100" required
                                            value="{{ $instituicao->nome_fantasia }}">
                                    </div>

                                    <div class="col-4">
                                        <label for="razao_social">Razão Social *</label>
                                        <input type="text" class="form-control" name="razao_social" maxlength="100" required
                                            value="{{ $instituicao->razao_social }}">
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-4">
                                        <label for="insc_est">Inscrição Estadual</label>
                                        <input type="text" class="form-control" name="insc_est" maxlength="14"
                                            value="{{ $instituicao->inscricao_estadual }}">
                                    </div>
                                    <div class="col-4">
                                        <label for="nome_cont">Nome de Contato</label>
                                        <input type="text" class="form-control" name="nome_cont" maxlength="100"
                                            value="{{ $instituicao->nome_contato }}">
                                    </div>
                                    <div class="col-4">
                                        <label for="email_contato">Email de Contato</label>
                                        <input type="email" class="form-control" name="email_contato" maxlength="100"
                                            value="{{ $instituicao->email_contato }}">
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-4">
                                        <label for="ibge">Ibge *</label>
                                        <input type="text" class="form-control" name="ibge" required maxlength="7"
                                            value="{{ $instituicao->ibge }}">
                                    </div>
                                    <div class="col-8">
                                        <label for="site">Site</label>
                                        <input type="url" class="form-control" name="site" maxlength="100"
                                            value="{{ $instituicao->site }}">
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-4">
                                        <label for="cnpj">CEP *</label>
                                        <input type="text" class="form-control" name="cep" id="cep"
                                            maxlength="9" required placeholder="Apenas números"
                                            value="{{ $instituicao->cep }}">
                                    </div>
                                    <div class="col-4">
                                        <label for="logradouro">Logradouro *</label>
                                        <input type="text" class="form-control" name="logradouro" id="logradouro" maxlength="100"
                                            value="{{ $instituicao->logradouro }}" maxlength="18" required>
                                    </div>
                                    <div class="col-4">
                                        <label for="bairro">Bairro *</label>
                                        <input type="text" class="form-control" name="bairro" id="bairro" required maxlength="50"
                                            value="{{ $instituicao->bairro }}">
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-4">
                                        <label for="uf">Uf *</label>
                                        <select class="form-control select2" name="uf" id="uf">
                                            <option value="{{ $instituicao->uf }}" selected>{{ $instituicao->sigla }}
                                            </option>
                                            @foreach ($uf as $item)
                                                @if ($item->id != $instituicao->uf)
                                                    <option value="{{ $item->id }}">{{ $item->sigla }}</option>
                                                @endif
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-4">
                                        <label for="cidade">Cidade *</label>
                                        <select class="js-example-responsive form-select select2" id="cidade"
                                            name="cidade" style="border: 1px solid #999999 !important; padding: 5px;">
                                            <!-- Opções serão carregadas pelo AJAX -->
                                        </select>

                                    </div>
                                    <div class="col-4">
                                        <label for="complemento">Complemento</label>
                                        <input type="text" class="form-control" name="complemento" id="complemento" maxlength="50"
                                            value="{{ $instituicao->complemento }}">
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-4">
                                        <label for="unidade">Unidade</label>
                                        <input type="text" class="form-control" name="unidade" id="unidade"
                                            value="{{ $instituicao->unidade }}" maxlength="50">
                                    </div>
                                    <div class="col-4">
                                        <label for="gia">Gia</label>
                                        <input type="text" class="form-control" name="gia" maxlength="8"
                                            value="{{ $instituicao->gia }}">
                                    </div>
                                    <div class="col-4">
                                        <label for="numero">Número</label>
                                        <input type="text" class="form-control" name="numero" maxlength="10"
                                            value="{{ $instituicao->numero }}">
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
            $('#cep').on('input', function() {
                let cep = $(this).val().replace(/\D/g, '');

                if (cep.length === 8) {
                    let estados = @JSON($uf);

                    $.ajax({
                        type: "GET",
                        url: 'https://viacep.com.br/ws/' + cep + '/json/',
                        dataType: "json",
                        success: function(response) {
                            console.log(response);

                            // Preenchendo os campos automaticamente
                            $('#logradouro').val(response.logradouro);
                            $('#bairro').val(response.bairro);
                            $('#complemento').val(response.complemento);

                            // Encontrando o estado correspondente
                            let estadoEncontrado = estados.find(estado => estado.sigla ===
                                response.uf);

                            if (estadoEncontrado) {
                                $('#uf').val(estadoEncontrado.id).trigger('change');

                                // Buscar cidades automaticamente e selecionar pelo nome
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

            // Pega o ID do UF e da cidade carregados no blade
            let ufSelecionado = "{{ $instituicao->uf }}";
            let cidadeSelecionada = "{{ $instituicao->cidade_id }}";

            // Popula as cidades do UF já selecionado no load
            populateCities($('#cidade'), ufSelecionado, cidadeSelecionada);

            // Se quiser, ao mudar o UF no select, atualiza o select de cidade:
            $('#uf').on('change', function() {
                let ufId = $(this).val();
                populateCities($('#cidade'), ufId, null);
            });

            function populateCities(selectElement, uf, cidadeNome) {
                $.ajax({
                    type: "GET",
                    url: "/retorna-cidades/" + uf,
                    dataType: "JSON",
                    success: function(response) {
                        selectElement.empty();
                        selectElement.removeAttr('disabled');

                        let cidadeSelecionada = null;

                        $.each(response, function(indexInArray, item) {
                            selectElement.append('<option value="' + item.id_cidade + '">' +
                                item.descricao + '</option>');

                            // Verifica se o nome da cidade retornado pelo ViaCEP é igual ao da lista
                            if (item.descricao.toLowerCase() === cidadeNome.toLowerCase()) {
                                cidadeSelecionada = item.id_cidade;
                            }
                        });

                        // Se encontramos a cidade pelo nome, selecionamos ela
                        if (cidadeSelecionada) {
                            selectElement.val(cidadeSelecionada).trigger('change');
                        }
                    }
                });
            }
        });
    </script>
@endsection
