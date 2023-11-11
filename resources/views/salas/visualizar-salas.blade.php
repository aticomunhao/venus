{{-- @extends('layouts.app')
<link href="https://cdn.jsdelivr.net/npm/bootstrap5-toggle@5.0.4/css/bootstrap5-toggle.min.css" rel="stylesheet">

@section('head')



@section('content')

<div class="container p-5 my-5 border ">



<br>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="card">
                <div class="card-body">

                    <div class="form-group row">
                        <div class="row">
                        <div class="col-3"> --}}
                            @extends('layouts.app')

@section('content')
<link href="https://cdn.jsdelivr.net/npm/bootstrap5-toggle@5.0.4/css/bootstrap5-toggle.min.css" rel="stylesheet">
  <br>
  <div class="container">
    <div class="row justify-content-center">
       <div class="col-12">
        <div class="card">
          <div class="card-header">

                  VISUALIZAR SALA

                </div>
                <div class="card-body">
                  <div class="container-fluid";>

                            <div class="col">
                            <label for="disabledTextInput" class="form-label">Nome:</label>
                            <input type="text" id="" value="{{$salas->nome}}" class="form-control" placeholder="Disabled input" disabled>
                        </div>
                        <br>
                        <div class="col">
                            <label for="disabledTextInput" class="form-label">Número:</label>
                            <input type="number" id="" value="{{$salas->numero}}" class="form-control" placeholder="Disabled input" disabled>
                        </div>
                        <br>
                        <div class="col">
                            <label for="disabledTextInput" class="form-label">Localização:</label>
                            <input type="text" id=""   value="{{$salas->localizacao}}"   class="form-control" placeholder="Disabled input" disabled>
                        </div>
                        <br>
                        <div class="col">
                            <label for="disabledTextInput" class="form-label">M² da sala:</label>
                            <input type="number" id=""   value="{{$salas->tamanho_sala}}"   class="form-control" placeholder="Disabled input" disabled>
                        </div>
                        <br>
                        <div class="col">
                            <label for="disabledTextInput" class="form-label">Número de cadeiras:</label>
                            <input type="number" id=""  value="{{$salas->nr_lugares}}"   class="form-control" placeholder="Disabled input" disabled>
                        </div>
                        </div>
                        <br>

                        <div class="row mt-4">
                                <div class="col">
                                <label for="ar_condicionado">Ar-cond</label>
                                <input type="checkbox"  name="ar_condicionado" @checked($salas->ar_condicionado) data-toggle="toggle" data-onlabel="Sim" data-offlabel="Não" data-onstyle="success" data-offstyle="danger"placeholder="Disabled input" disabled>
                            </div>
                            <div class="col">
                                <label for="armarios">Armários</label>
                                <input type="checkbox"   name="armarios" @checked($salas->armarios)  data-toggle="toggle" data-onlabel="Sim" data-offlabel="Não" data-onstyle="success" data-offstyle="danger" placeholder="Disabled input" disabled>
                            </div>
                            <div class="col">
                                <label for="bebedouro">Bebedouro</label>
                                <input type="checkbox"   name="bebedouro"  @checked($salas->bebedouro) data-toggle="toggle" data-onlabel="Sim" data-offlabel="Não" data-onstyle="success" data-offstyle="danger" placeholder="Disabled input" disabled>
                            </div>
                            <div class="col">
                                <label for="controle">Controle</label>
                                <input type="checkbox"  name="controle"   @checked($salas->controle) data-toggle="toggle" data-onlabel="Sim" data-offlabel="Não" data-onstyle="success" data-offstyle="danger"placeholder="Disabled input" disabled >
                            </div>
                            <div class="col-1">
                                <label for="computador">PC</label>
                                <input type="checkbox"  name="computador"   @checked($salas->computador) data-toggle="toggle" data-on="Sim" data-off="Não" data-onstyle="success" data-offstyle="danger"placeholder="Disabled input" disabled >
                            </div>
                            <div class="col">
                                <label for="projetor">Projetor</label>
                                <input type="checkbox"  name="projetor"   @checked($salas->projetor)  data-toggle="toggle" data-onlabel="Sim" data-offlabel="Não" data-onstyle="success" data-offstyle="danger" placeholder="Disabled input" disabled>
                            </div>
                            <div class="col">
                                <label for="tela_projetor">Tela_proj</label>
                                <input type="checkbox"  name="tela_projetor"  @checked($salas->tela_projetor) data-toggle="toggle" data-onlabel="Sim" data-offlabel="Não" data-onstyle="success" data-offstyle="danger" placeholder="Disabled input" disabled>
                            </div>
                            <div class="col">
                                <label for="quadro">Quadro</label>
                                <input type="checkbox" @checked($salas->quadro)  name="quadro"  data-toggle="toggle" data-onlabel="Sim" data-offlabel="Não" data-onstyle="success" data-offstyle="danger"placeholder="Disabled input" disabled >
                                </div>
                            <div class="col">
                            <label for="som">Som</label>
                                <input type="checkbox"  name="som"  @checked($salas->som) data-toggle="toggle" data-onlabel="Sim" data-offlabel="Não" data-onstyle="success" data-offstyle="danger"placeholder="Disabled input" disabled >
                            </div>
                            <div class="col">
                                <label for="ventilador">Ventilador</label>
                                <input type="checkbox"  name="ventilador" @checked($salas->ventilador) data-toggle="toggle" data-onlabel="Sim" data-offlabel="Não" data-onstyle="success" data-offstyle="danger"placeholder="Disabled input" disabled >
                            </div>
                            <div class="col">
                            <label for="luz_azul">Luz azul</label>
                                <input type="checkbox"  name="luz_azul"  @checked($salas->luz_azul)  data-toggle="toggle" data-onlabel="Sim" data-offlabel="Não" data-onstyle="success" data-offstyle="danger"placeholder="Disabled input" disabled >
                            </div>


                        </fieldset>
                </div>
                    </div>

                        </div>

                    </fieldset>
                    <br>

                    <?php $a=1; $b=1; $c=1; $d=1; $e=1; ?>
                    @foreach($salas as $sala)

                                        </thead>
                                        <tbody>

                            </div>
                        </div>

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

@endsection

@section('footerScript')
<script src="https://cdn.jsdelivr.net/npm/bootstrap5-toggle@5.0.4/js/bootstrap5-toggle.ecmas.min.js"></script>
<script src="{{ URL::asset('/js/pages/mascaras.init.js')}}"></script>
