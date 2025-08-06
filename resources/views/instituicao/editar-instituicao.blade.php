@extends('layouts.app')

@section('title')
    Editar Instituições
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
                                    EDITAR INSTITUIÇÕES
                                </div>
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
                                        <input type="text" class="form-control" name="nome_fantasia" required
                                            value="{{ $instituicao->nome_fantasia }}">
                                    </div>

                                    <div class="col-4">
                                        <label for="razao_social">Razão Social *</label>
                                        <input type="text" class="form-control" name="razao_social" required
                                            value="{{ $instituicao->razao_social }}">
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-4">
                                        <label for="insc_est">Inscrição Estadual</label>
                                        <input type="text" class="form-control" name="insc_est"
                                            value="{{ $instituicao->inscricao_estadual }}">
                                    </div>
                                    <div class="col-4">
                                        <label for="nome_cont">Nome de Contato</label>
                                        <input type="text" class="form-control" name="nome_cont"
                                            value="{{ $instituicao->nome_contato }}">
                                    </div>
                                    <div class="col-4">
                                        <label for="email_contato">Email de Contato</label>
                                        <input type="email" class="form-control" name="email_contato"
                                            value="{{ $instituicao->email_contato }}">
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-4">
                                        <label for="ibge">Ibge *</label>
                                        <input type="text" class="form-control" name="ibge" required
                                            value="{{ $instituicao->ibge }}">
                                    </div>
                                    <div class="col-8">
                                        <label for="site">Site</label>
                                        <input type="url" class="form-control" name="site"
                                            value="{{ $instituicao->site }}">
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-4">
                                        <label for="cnpj">CEP *</label>
                                        <input type="text" class="form-control" name="cep" id="cep"
                                            maxlength="18" required placeholder="Apenas números"
                                            value="{{ $instituicao->cep }}">
                                    </div>
                                    <div class="col-4">
                                        <label for="logradouro">Logradouro *</label>
                                        <input type="text" class="form-control" name="logradouro" id="logradouro"
                                            value="{{ $instituicao->logradouro }}" maxlength="18" required>
                                    </div>
                                    <div class="col-4">
                                        <label for="bairro">Bairro *</label>
                                        <input type="text" class="form-control" name="bairro" required
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
                                        <label for="localidade">Localidade *</label>
                                        <input type="text" class="form-control" name="localidade"
                                            value="{{ $instituicao->localidade }}" required>
                                    </div>
                                    <div class="col-4">
                                        <label for="complemento">Complemento</label>
                                        <input type="text" class="form-control" name="complemento"
                                            value="{{ $instituicao->complemento }}">
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-4">
                                        <label for="unidade">Unidade</label>
                                        <input type="text" class="form-control" name="unidade" id="unidade"
                                            value="{{ $instituicao->unidade }}" maxlength="18">
                                    </div>
                                    <div class="col-4">
                                        <label for="gia">Gia</label>
                                        <input type="text" class="form-control" name="gia"
                                            value="{{ $instituicao->gia }}">
                                    </div>
                                    <div class="col-4">
                                        <label for="numero">Número</label>
                                        <input type="text" class="form-control" name="numero"
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
@endsection
