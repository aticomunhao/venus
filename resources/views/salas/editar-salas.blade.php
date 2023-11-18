
@extends('layouts.app')

@section('content')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap5-toggle@5.0.4/css/bootstrap5-toggle.min.css" rel="stylesheet">
    <br>
    <div class="container">

        <div class="row justify-content-center">

            <div class="col-12">

                <div class="card">

                    <div class="card-header">
                        <div class="row">

                            <div class="col">
                                EDITAR SALA
                            </div>


                        </div>


                    </div>
                    <div class="card-body">
                        <div class="container-fluid">
                            <div class="col-12">
                                <div class="row justify-content-center">

                                    <form class="form-horizontal mt-4" method="post" action="/atualizar-salas/{{ $salas[0]giyt->id }}">
                                        @csrf
                                        <div class="col-1 text-end offset-11"><label for="status_sala"></label>
                                            <input type="checkbox"  name="ar_condicionado" @checked($salaEditada->status_sala) data-toggle="toggle" data-onlabel="Sim" data-offlabel="Não" data-onstyle="success" data-offstyle="danger"placeholder="Disabled input">

                                        </div>
                                        </div>
                                        <div class="row">
                                              <div class="col-8"><label for="nome">Nome</label>
                                            <input type="text" value="{{$salaEditada->nome}}" class="form-control" id="nome" name="nome">
                                        </div>
                                        <br>

                                            <div class="col">Finalidade sala
                                                <select class= "form-select " aria-label=".form-select-lg example">

                                                        @foreach ($tipo_finalidade_sala as $tipo)
                                                        <option value={{$tipo->id}}>{{$tipo->descricao}}</option>
                                                    @endforeach
                                                </select>
                                                <br>
                                            </div>
                                        </div>
                                        <div class="row">
                                                <div class="col">Número
                                                    <input type="number" value="{{$salaEditada->numero}}" class="form-control" id="numero"
                                                        name="numero">
                                                    <br>
                                                </div>

                                                <div class="col">Localização
                                                    <select class="form-select "name="id_localizacao" aria-label=".form-select-lg example">


                                                        @foreach ($tipo_localizacao as $localizacao )
                                                        <option value={{$localizacao->ids}}>{{$localizacao->nome}}</option>
                                                    @endforeach
                                                </select>
                                                    <br>
                                                    </div>
                                                    <div class="col">M² da sala
                                                        <input type="number"value="{{$salaEditada->tamanho_sala}}" class="form-control" id="tamanho_sala"
                                                            name="tamanho_sala">
                                                    </div>
                                                    <br>
                                                    <div class="col">Número de lugares
                                                        <input type="number"value="{{$salaEditada->nr_lugares}}" class="form-control" id="nr_lugares"
                                                            name="nr_lugares" required>

                                                    </div>
                                                    <br>
                                                    <br>

                                                </div>
                                                <br>

                                                <br>
                                                <div class="row mt-4">
                                                    <div class="col">
                                                    <label for="ar_condicionado">Ar-cond</label>
                                                    <input type="checkbox"  name="ar_condicionado" @checked($salas[0]->ar_condicionado) data-toggle="toggle" data-onlabel="Sim" data-offlabel="Não" data-onstyle="success" data-offstyle="danger"placeholder="Disabled input">
                                                </div>
                                                <div class="col">
                                                    <label for="armarios">Armários</label>
                                                    <input type="checkbox"   name="armarios" @checked($salas[0]->armarios)  data-toggle="toggle" data-onlabel="Sim" data-offlabel="Não" data-onstyle="success" data-offstyle="danger" placeholder="Disabled input">
                                                </div>
                                                <div class="col">
                                                    <label for="bebedouro">Bebedouro</label>
                                                    <input type="checkbox"   name="bebedouro"  @checked($salas[0]->bebedouro) data-toggle="toggle" data-onlabel="Sim" data-offlabel="Não" data-onstyle="success" data-offstyle="danger" placeholder="Disabled input">
                                                </div>
                                                <div class="col">
                                                    <label for="controle">Controle</label>
                                                    <input type="checkbox"  name="controle"   @checked($salas[0]->controle) data-toggle="toggle" data-onlabel="Sim" data-offlabel="Não" data-onstyle="success" data-offstyle="danger"placeholder="Disabled input">
                                                </div>
                                                <div class="col-1">
                                                    <label for="computador">PC</label>
                                                    <input type="checkbox"  name="computador"   @checked($salas[0]->computador) data-toggle="toggle" data-on="Sim" data-off="Não" data-onstyle="success" data-offstyle="danger"placeholder="Disabled input" >
                                                </div>
                                                <div class="col">
                                                    <label for="projetor">Projetor</label>
                                                    <input type="checkbox"  name="projetor"   @checked($salas[0]->projetor)  data-toggle="toggle" data-onlabel="Sim" data-offlabel="Não" data-onstyle="success" data-offstyle="danger" placeholder="Disabled input">
                                                </div>
                                                <div class="col">
                                                    <label for="tela_projetor">Tela_proj</label>
                                                    <input type="checkbox"  name="tela_projetor"  @checked($salas[0]->tela_projetor) data-toggle="toggle" data-onlabel="Sim" data-offlabel="Não" data-onstyle="success" data-offstyle="danger" placeholder="Disabled input">
                                                </div>
                                                <div class="col">
                                                    <label for="quadro">Quadro</label>
                                                    <input type="checkbox" @checked($salas[0]->quadro)  name="quadro"  data-toggle="toggle" data-onlabel="Sim" data-offlabel="Não" data-onstyle="success" data-offstyle="danger"placeholder="Disabled input">
                                                    </div>
                                                <div class="col">
                                                <label for="som">Som</label>
                                                    <input type="checkbox"  name="som"  @checked($salas[0]->som) data-toggle="toggle" data-onlabel="Sim" data-offlabel="Não" data-onstyle="success" data-offstyle="danger"placeholder="Disabled input">
                                                </div>
                                                <div class="col">
                                                    <label for="ventilador">Ventilador</label>
                                                    <input type="checkbox"  name="ventilador" @checked($salas[0]->ventilador) data-toggle="toggle" data-onlabel="Sim" data-offlabel="Não" data-onstyle="success" data-offstyle="danger"placeholder="Disabled input">
                                                </div>
                                                <div class="col">
                                                <label for="luz_azul">Luz azul</label>
                                                    <input type="checkbox"  name="luz_azul"  @checked($salas[0]->luz_azul)  data-toggle="toggle" data-onlabel="Sim" data-offlabel="Não" data-onstyle="success" data-offstyle="danger"placeholder="Disabled input">
                                                </div>

                                                    </div>
                                                </div>

                                                <div class="row justify-content-center">
                                                    <div class="d-grid gap-1 col-4 mx-auto">
                                                        <br>
                                                        <a class="btn btn-danger" href="/gerenciar-salas"
                                                            role="button">Cancelar</a>
                                                    </div>
                                                    <div class="d-grid gap-2 col-4 mx-auto">
                                                        <br>
                                                        <button class="btn btn-primary">Confirmar</button>
                                                    </div>
                                                </div>
                                            </div>

                                            </div>


                                        </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <script src="https://cdn.jsdelivr.net/npm/bootstrap5-toggle@5.0.4/js/bootstrap5-toggle.ecmas.min.js"></script>


@endsection



