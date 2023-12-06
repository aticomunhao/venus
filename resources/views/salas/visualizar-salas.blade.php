@extends('layouts.app')
<link href="https://cdn.jsdelivr.net/npm/bootstrap5-toggle@5.0.4/css/bootstrap5-toggle.min.css" rel="stylesheet">

@section('content')
    <br>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        VISUALIZAR SALA
                    </div>
                    <div class="card-body">
                        <div class="container-fluid">
                            <div class="col-1 text-end offset-11">
                                <label for="status_sala"></label>
                                <input type="checkbox" name="ar_condicionado" @checked($salaEditada->status_sala)
                                    data-toggle="toggle" data-onlabel="Sim" data-offlabel="Não" data-onstyle="success"
                                    data-offstyle="danger" placeholder="Disabled input" disabled>
                            </div>
                            <div class="row">
                                <div class="col-8">
                                    <label for="disabledTextInput" class="form-label">Nome:</label>
                                    <input type="text" id="" value="{{ $salaEditada->nome }}"
                                        class="form-control" placeholder="Disabled input" disabled>
                                </div>
                                <div class="col">
                                    Localização
                                    <select class="form-select" name="id_localizacao" aria-label=".form-select-lg example"
                                        disabled>
                                        @foreach ($tipo_localizacao as $localizacao)
                                            <option value={{ $localizacao->ids }}>{{ $localizacao->nome }}</option>
                                        @endforeach
                                    </select>
                                    <br>
                                </div>
                                <div class="row">
                                    <div class="col">
                                        <label for="disabledTextInput" class="form-label">Número</label>
                                        <input type="number" id="" value="{{ $salaEditada->numero }}"
                                            class="form-control" placeholder="Disabled input" disabled>
                                    </div>
                                    <div class="col">Finalidade sala
                                        <select class="form-select" aria-label=".form-select-lg example" disabled>
                                            @foreach ($tipo_finalidade_sala as $tipo)
                                                <option value={{ $tipo->id }}>{{ $tipo->descricao }}</option>
                                            @endforeach
                                        </select>
                                        <br>
                                    </div>
                                    <div class="col">
                                        <label for="disabledTextInput" class="form-label">M² da sala</label>
                                        <input type="number" id="" value="{{ $salaEditada->tamanho_sala }}"
                                            class="form-control" placeholder="Disabled input" disabled>
                                    </div>
                                    <div class="col">
                                        <label for="disabledTextInput" class="form-label">Número de lugares</label>
                                        <input type="number" id="" value="{{ $salaEditada->nr_lugares }}"
                                            class="form-control" placeholder="Disabled input" disabled>
                                    </div>
                                </div>
                                <div class="row mt-4">
                                    <div class="col">
                                        <label for="ar_condicionado">Ar-cond</label>
                                        <input type="checkbox" name="ar_condicionado" @checked($salaEditada->ar_condicionado)
                                            data-toggle="toggle" data-onlabel="Sim" data-offlabel="Não"
                                            data-onstyle="success" data-offstyle="danger" placeholder="Disabled input"
                                            disabled>
                                    </div>
                                    <div class="col">
                                        <label for="armarios">Armários</label>
                                        <input type="checkbox" name="armarios" @checked($salaEditada->armarios)
                                            data-toggle="toggle" data-onlabel="Sim" data-offlabel="Não"
                                            data-onstyle="success" data-offstyle="danger" placeholder="Disabled input"
                                            disabled>
                                    </div>
                                    <div class="col">
                                        <label for="bebedouro">Bebedouro</label>
                                        <input type="checkbox" name="bebedouro" @checked($salaEditada->bebedouro)
                                            data-toggle="toggle" data-onlabel="Sim" data-offlabel="Não"
                                            data-onstyle="success" data-offstyle="danger" placeholder="Disabled input"
                                            disabled>
                                    </div>
                                    <div class="col">
                                        <label for="controle">Controle</label>
                                        <input type="checkbox" name="controle" @checked($salaEditada->controle)
                                            data-toggle="toggle" data-onlabel="Sim" data-offlabel="Não"
                                            data-onstyle="success" data-offstyle="danger" placeholder="Disabled input"
                                            disabled>
                                    </div>
                                    <div class="col-1">
                                        <label for="computador">PC</label>
                                        <input type="checkbox" name="computador" @checked($salaEditada->computador)
                                            data-toggle="toggle" data-on="Sim" data-off="Não" data-onstyle="success"
                                            data-offstyle="danger" placeholder="Disabled input" disabled>
                                    </div>
                                    <div class="col">
                                        <label for="projetor">Projetor</label>
                                        <input type="checkbox" name="projetor" @checked($salaEditada->projetor)
                                            data-toggle="toggle" data-onlabel="Sim" data-offlabel="Não"
                                            data-onstyle="success" data-offstyle="danger" placeholder="Disabled input"
                                            disabled>
                                    </div>
                                    <div class="col">
                                        <label for="tela_projetor">Tela_proj</label>
                                        <input type="checkbox" name="tela_projetor" @checked($salaEditada->tela_projetor)
                                            data-toggle="toggle" data-onlabel="Sim" data-offlabel="Não"
                                            data-onstyle="success" data-offstyle="danger" placeholder="Disabled input"
                                            disabled>
                                    </div>
                                    <div class="col">
                                        <label for="quadro">Quadro</label>
                                        <input type="checkbox" @checked($salaEditada->quadro) name="quadro"
                                            data-toggle="toggle" data-onlabel="Sim" data-offlabel="Não"
                                            data-onstyle="success" data-offstyle="danger" placeholder="Disabled input"
                                            disabled>
                                    </div>
                                    <div class="col">
                                        <label for="som">Som</label>
                                        <input type="checkbox" name="som" @checked($salaEditada->som)
                                            data-toggle="toggle" data-onlabel="Sim" data-offlabel="Não"
                                            data-onstyle="success" data-offstyle="danger" placeholder="Disabled input"
                                            disabled>
                                    </div>
                                    <div class="col">
                                        <label for="ventilador">Ventilador</label>
                                        <input type="checkbox" name="ventilador" @checked($salaEditada->ventilador)
                                            data-toggle="toggle" data-onlabel="Sim" data-offlabel="Não"
                                            data-onstyle="success" data-offstyle="danger" placeholder="Disabled input"
                                            disabled>
                                    </div>
                                    <div class="col">
                                        <label for="luz_azul">Luz azul</label>
                                        <input type="checkbox" name="luz_azul" @checked($salaEditada->luz_azul)
                                            data-toggle="toggle" data-onlabel="Sim" data-offlabel="Não"
                                            data-onstyle="success" data-offstyle="danger" placeholder="Disabled input"
                                            disabled>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <br>

                        <?php $a = 1; $b = 1; $c = 1; $d = 1; $e = 1; ?>
                        @foreach ($sala as $salas)
                            </tbody>
                        @endforeach

                        <div class="row justify-content-center">
                            <div class="d-grid gap-1 col-3 mx-auto">
                                <br>
                                <a class="btn btn-danger" href="/gerenciar-salas" role="button">Fechar</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('footerScript')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap5-toggle@5.0.4/js/bootstrap5-toggle.ecmas.min.js"></script>
    <script src="{{ URL::asset('/js/pages/mascaras.init.js') }}"></script>
@endsection
