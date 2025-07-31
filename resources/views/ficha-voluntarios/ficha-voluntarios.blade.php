@extends('layouts.app')
@section('head')
    <title>Editar Ficha Pessoa</title>
@endsection

@section('content')
    <div class="container"> {{-- Container completo da página  --}}
        <div class="justify-content-center">
            <div class="col-12">
                <br>
                <div class="card">
                    <div class="card-header">
                        DADOS PESSOAIS
                    </div>
                    <div class="card-body">
                        <form class="form-horizontal mt-2" method='POST'
                            action="/atualizar-ficha-voluntario/{{ $edit_associado->ida }}/{{ $edit_associado->idp }}">
                            @csrf
                            <div class="container-fluid">
                                <div class="row g-3 d-flex justify-content-around">
                                    <div class="col-xl-6 col-md-6 col-sm-12">Nome Completo
                                        <input type="text" class="form-control" name="nome_completo" maxlength="45"
                                            oninput="this.value = this.value.replace(/[0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');"
                                            value="{{ $edit_associado->nome_completo }}" disabled>
                                    </div>
                                    <div class="col-xl-2 col-md-3 col-sm-12">CPF
                                        <input type="text" class="form-control" id="cpf" name="cpf"
                                            maxlength="11" value="{{ $edit_associado->cpf }}" disabled>
                                    </div>
                                    <div class="col-xl-2 col-md-3 col-sm-12">
                                        <label for="2">N.º Associado</label>
                                        <input type="text" class="form-control" id="nrassociado" name="nrassociado"
                                            maxlength="11" value="{{ $edit_associado->nr_associado }}" disabled>
                                    </div>
                                    <div class="col-lg-2 col-md-4 col-sm-12">Identidade
                                        <input type="text" class="form-control" name="idt" maxlength="9"
                                            oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');"
                                            value="{{ $edit_associado->idt }}" required>
                                    </div>
                                    <div class="col-xl-2 col-lg-3 col-md-4 col-sm-12">Data de Nascimento
                                        <input type="date" class="form-control" name="dt_nascimento" id="3"
                                            value="{{ $edit_associado->dt_nascimento }}" required="required">
                                    </div>
                                    <div class="col-lg-2 col-md-4 col-sm-12">Sexo
                                        <select id="sexo" class="form-select" name="sexo" required>
                                            @foreach ($tpsexo as $tpsexos)
                                                <option value="{{ $tpsexos->id }}"
                                                    {{ $edit_associado->id_sexo == $tpsexos->id ? 'selected' : null }}>
                                                    {{ $tpsexos->tipo }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-xl-1 col-lg-2 col-md-4 col-sm-12">
                                        <label for="3">DDD</label>
                                        <select id="ddd" class="form-select" name="ddd">
                                            </option>
                                            @foreach ($tpddd as $tpddds)
                                                <option value="{{ $tpddds->id }}"
                                                    {{ $tpddds->id == $edit_associado->ddd ? 'selected' : null }}>
                                                    {{ $tpddds->descricao }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-xl-2 col-lg-3 col-md-8 col-sm-12">
                                        <label for="2">Celular</label>
                                        <input type="text" class="form-control" id="2" maxlength="9"
                                            name="telefone" value="{{ $edit_associado->celular }}" required>
                                    </div>
                                    <div class="col-md-12 col-xl-5">
                                        <label for="2">Email</label>
                                        <input type="text" class="form-control" id="2" maxlength="100"
                                            name="email" value="{{ $edit_associado->email }}" required>
                                    </div>
                                </div>
                            </div>
                    </div>
                </div>
            </div>
        </div>
        <br>

        <div class="card">
            <div class="card-header">
                DADOS RESIDENCIAIS
            </div>
            <div class="card-body">
                <div class="container-fluid">
                    <div class="row g-3 d-flex justify-content-around">
                        <div class="col-xl-2 col-md-3 col-sm-12">CEP
                            <div class=" input-group has-validation">
                                <input type="text" class="form-control" id="cep" name="cep" maxlength="8"
                                    value="{{ $edit_associado->cep }}" required>
                                <div class="invalid-tooltip">
                                    CEP Inválido!
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-1 col-md-2 col-sm-12">UF
                            <select class="form-select" id="uf2" name="uf_end">
                                @foreach ($tpufidt as $tp_ufes)
                                    <option value="{{ $tp_ufes->id }}"
                                        {{ $tp_ufes->id == $edit_associado->id_uf ? 'selected' : null }}>
                                        {{ $tp_ufes->sigla }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-xl-4 col-md-7 col-sm-12">Cidade
                            <select class="form-select" id="cidade2" name="cidade">

                            </select>
                        </div>
                        <div class="col-xl-5 col-lg-6 col-md-12">Logradouro
                            <input type="text" class="form-control" id="logradouro" name="logradouro" maxlength="50"
                                value="{{ $edit_associado->logradouro }}" required>
                        </div>

                        <div class="col-xl-4 col-lg-6 col--12">Complemento
                            <input type="text" class="form-control" id="complemento" name="complemento"
                                maxlength="50" value="{{ $edit_associado->complemento }}" required>
                        </div>
                        <div class="col-xl-4 col-md-4 col-sm-12">Número
                            <input type="text" class="form-control" id="numero" name="numero" maxlength="10"
                                value="{{ $edit_associado->numero }}" required>
                        </div>
                        <div class="col-xl-4 col-md-8 col-sm-12">Bairro
                            <input type="text" class="form-control" id="bairro" name="bairro" maxlength="50"
                                value="{{ $edit_associado->bairro }}" required>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="mb-5 mt-5">
            <a class="btn btn-danger col-md-3 col-sm-12 col-2 mt-3 offset-md-2" href="#"
                class="btn btn-danger">Cancelar</a>
            <button type="submit" class="btn btn-primary col-md-3 col-sm-12 col-2 mt-3 offset-md-2">Confirmar</button>
            </form>
        </div>
    </div>





    <script>
        $(document).ready(function() {

            $('#cpf').val($('#cpf').val().replace(/^(\d{3})(\d{3})(\d{3})(\d{2})$/, '***.$2.$3-**'));
            let cep = $('#cep').val().replace(/\D/g, '');
            if (cep.length === 8) {
                populateCities($('#cidade2'), @JSON($edit_associado->id_uf), @JSON($edit_associado->nat))
            }


            if (!@JSON($edit_associado->id_sexo)) $('#sexo').prop('selectedIndex', -1)
            if (!@JSON($edit_associado->ddd)) $('#ddd').prop('selectedIndex', -1)



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
                            if (item.descricao.toLowerCase() == cidadeNome.normalize("NFD")
                                .replace(/[\u0300-\u036f]/g, "").toLowerCase()) {
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

            function retornaCEP(cep) {
                let estados = @JSON($tpufidt);

                $.ajax({
                    type: "GET",
                    url: 'https://viacep.com.br/ws/' + cep + '/json/',
                    dataType: "json",
                    success: function(response) {
                        console.log(response);

                        if (response.erro) {
                            $('#cep').addClass('is-invalid')
                            return;
                        }


                        // Preenchendo os campos automaticamente
                        $('#logradouro').val(response.logradouro);
                        $('#bairro').val(response.bairro);
                        $('#complemento').val(response.complemento);
                        $('#numero').val('');

                        // Encontrando o estado correspondente
                        let estadoEncontrado = estados.find(estado => estado.sigla ===
                            response.uf);

                        if (estadoEncontrado) {
                            $('#uf2').val(estadoEncontrado.id).trigger('change');

                            // Buscar cidades automaticamente e selecionar pelo nome
                            populateCities($('#cidade2'), estadoEncontrado.id, response.localidade);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("Erro ao buscar o CEP:", error);
                    }
                });
            }

            $('#cep').on('input', function() {
                let cep = $(this).val().replace(/\D/g, '');

                if (cep.length === 8) {
                    retornaCEP(cep)
                }
            });
        });
    </script>
@endsection
