@extends('layouts.app')

@section('title') Visualizar @endsection

@section('content')


<br>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <legend style="color:red; font-size:14px; font-family:Tahoma">Visualizar sala</legend>
                    <fieldset class="border rounded border-secondary p-4">
                    <div class="form-group row">
                        <div class="col">
                          <div class="col">Nome 
                            <input type="text" class="form-control" id="nome" name="nome">
                          </div>
                          <div class="col">Número 
                            <input type="number" class="form-control" id="numero" name="numero" >
                            <br>
                            <div class="col">Número de lugares
                              <input type="number" class="form-control" id="nr_lugares" name="nr_lugares" >
                          </div>
                            <div class="col">localização
                              <input type="text" class="form-control" id="localizacao" name="localizacao">
                            </div>
                          <br>
                          <div class="col">M² da sala
                            <input type="number" class="form-control" id="tamanho_sala" name="tamanho_sala">
                          </div>
                          <br>
                          <div>
                          <label for="disabledTextInput" class="form-label">Projetor:</label>
                          <input type="text"  name="projetor" value="{{$sala->projetor}}" class="form-control" placeholder="Disabled input" disabled>
                      </div>
                         
                      <div>
                      <label for="disabledTextInput" class="form-label">Quadro:</label>
                          <input type="text"  name="projetor" value="{{$sala->quadro}}" class="form-control" placeholder="Disabled input" disabled>
                      </div>
                          <label for="disabledTextInput" class="form-label">Ar concionado:</label>
                            <input type="text"  name="ar_condicionado" value="{{$sala->ar_condicionado}}" class="form-control" placeholder="Disabled input" disabled>
                        </div>
                        <div class="col-1">
                            <label for="disabledTextInput" class="form-label">Armários</label>
                            <input type="text"name="armarios"  value="{{$sala->armarios}}" class="form-control" placeholder="Disabled input" disabled>
                        </div>
                              <div class="col-3">
                          <label for="disabledTextInput" class="form-label">Controle_proj:</label>
                          <input type="text"  name= "controle"  value="{{$sala->controle}}" class="form-control" placeholder="Disabled input" disabled>
                      </div>
                      <div class="col-3">
                        <label for="disabledTextInput" class="form-label">som:</label>
                        <input type="text"  name= "controle"  value="{{$sala->som}}" class="form-control" placeholder="Disabled input" disabled>
                    </div>
                    <div class="col-3">
                        <label for="disabledTextInput" class="form-label">luz_azul:</label>
                        <input type="text"  name= "controle"  value="{{$sala->luz_azul}}" class="form-control" placeholder="Disabled input" disabled>
                    </div>
                    <label for="disabledTextInput" class="form-label">bebedouro:</label>
                        <input type="text"  name= "controle"  value="{{$sala->bebedouro}}" class="form-control" placeholder="Disabled input" disabled>
                    </div>
                    <label for="disabledTextInput" class="form-label">status_sala:</label>
                        <input type="text"  name= "controle"  value="{{$sala->status_sala}}" class="form-control" placeholder="Disabled input" disabled>
                    </div>
                  </div>
                  <label for="disabledTextInput" class="form-label">tela_projetor:</label>
                      <input type="text"  name= "controle"  value="{{$sala->tela_projetor}}" class="form-control" placeholder="Disabled input" disabled>
                  </div>
                </div>
                <label for="disabledTextInput" class="form-label">ventilador:</label>
                    <input type="text"  name= "controle"  value="{{$sala->ventilador}}" class="form-control" placeholder="Disabled input" disabled>
                </div>
                    </div>
                    </fieldset>
                    <br>
                    <legend style="color:blue; font-size:12px; font-weight:bold; font-family:Verdana, Geneva, Tahoma, sans-serif">Lista de atendimentos</legend>
                    <?php $a=1; $b=1; $c=1; $d=1; $e=1; ?>
                    @foreach($salas as $sala)
                 
                            <div id="flush-collapse{{$d++}}" class="accordion-collapse collapse" aria-labelledby="{{$e++}}" data-bs-parent="#accordionFlushExample">
                                <div class="accordion-body">
                                    <table class="table table-sm table-bordered table-striped">
                                        <thead style="text-align:center; background: #daffe0;">
                                            <tr style="text-align:center; font-weight: bold; font-size:12px">
                                                <td class="col-3">NOME</td>
                                                <td class="col-1">NÚMERO</td>
                                                <td class="col-3">LOCALIZAÇÃO</td>
                                                <td class="col-1">M² DA SALA</td>
                                                <td class="col-1">NÚMERO DE CADEIRAS</td>
                                                <td class="col-2">STATUS</td>
                                            </tr>

                                        </thead>
                                        <tbody>
                                            <tr style="text-align:center;font-size:11px">       
                                                <td>{{$sala->nome}}</td>
                                                <td>{{$sala->numero}}</td>
                                                <td>{{$sala->nr_lugares}}</td>
                                                <td>{{$sala->localizacao}}</td>
                                                <td>{{$sala->projetor}}</td>
                                                <td>{{$sala->quadro}}</td>
                                                <td>{{$sala->tela_projetor}}</td>
                                                <td>{{$sala->ventilador}}</td>
                                                <td>{{$sala->ar_condicionado}}</td>
                                                <td>{{$sala->computador}}</td>
                                                <td>{{$sala->controle}}</td>
                                                <td>{{$sala->som}}</td>
                                                <td>{{$sala->luz_azul}}</td>
                                                <td>{{$sala->bebedouro}}</td>
                                                <td>{{$sala->armarios}}</td>
                                                <td>{{$sala->tamanho_sala}}</td>
                                                <td>{{$sala->status_sala}}</td>
                                              
                                            </tr>
                                        </tbody>
                                    </table>
                                    <br>
                                    

                                        </thead>
                                        <tbody>
                                            <tr style="text-align:center;font-size:11px">       
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                    @endforeach
                    <br>
                    <div class="row">
                        <div class="col">    
                            <a class="btn btn-danger" href="/gerenciar-salas" style="text-align:right;" role="button">Fechar</a>

                            
                        </div>
                        
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>



@endsection

@section('footerScript')


<script src="{{ URL::asset('/js/pages/mascaras.init.js')}}"></script>

@endsection






{{-- 
@extends('layouts.app')

@section('content')
<link href="https://cdn.jsdelivr.net/npm/bootstrap5-toggle@5.0.4/css/bootstrap5-toggle.min.css" rel="stylesheet">
  <br>
  <div class="container">
    <div class="row justify-content-center">
       <div class="col-12">
        <div class="card">
          <div class="card-header">

                  CADASTRAR SALA
          
                </div>
          <div class="card-body">
            <div class="container-fluid";>
                 <div class="col-12">
                      <div class="row justify-content-center">
                   
             <form class="form-horizontal mt-2" method="post" action="/incluir-salas/">
            @csrf
            <div class="col">Nome 
              <input type="text" class="form-control" id="nome" name="nome">
            </div>
            <br>
              <div class="col">Número 
                <input type="number" class="form-control" id="numero" name="numero" >
                <br>
                <div class="col">localização
                  <input type="text" class="form-control" id="localizacao" name="localizacao">
                </div>
              <br>
              <div class="col">M² da sala
                <input type="number" class="form-control" id="tamanho_sala" name="tamanho_sala">
              </div>
              <br>
            <div class="col">Número de lugares
              <input type="number" class="form-control" id="nr_lugares" name="nr_lugares" >
          </div>
          <br>
         
          <br>
          <div class="row form-group">
            <div class="col">
              <label for="ar_condicionado">Ar-cond</label>
              <input type="checkbox"  name="ar_condicionado" data-toggle="toggle" data-onlabel="Sim" data-offlabel="Não" data-onstyle="success" data-offstyle="danger">
            </div>
            <div class="col">
              <label for="armarios">Armários</label>
              <input type="checkbox"   name="armarios"  data-toggle="toggle" data-onlabel="Sim" data-offlabel="Não" data-onstyle="success" data-offstyle="danger" >
            </div>
            <div class="col">
              <label for="bebedouro">Bebedouro</label>
              <input type="checkbox"   name="bebedouro"  data-toggle="toggle" data-onlabel="Sim" data-offlabel="Não" data-onstyle="success" data-offstyle="danger" >
            </div>
            <div class="col">
              <label for="controle">Controle</label>
              <input type="checkbox"  name="controle"  data-toggle="toggle" data-onlabel="Sim" data-offlabel="Não" data-onstyle="success" data-offstyle="danger" >
            </div>
            <div class="col-1">
              <label for="computador">PC</label>
              <input type="checkbox"  name="computador"  data-toggle="toggle" data-on="Sim" data-off="Não" data-onstyle="success" data-offstyle="danger" >
            </div>
            <div class="col">
              <label for="projetor">Projetor</label>
              <input type="checkbox"  name="projetor"   data-toggle="toggle" data-onlabel="Sim" data-offlabel="Não" data-onstyle="success" data-offstyle="danger" >
            </div>
            <div class="col">
              <label for="tela_projetor">Tela_proj</label>
              <input type="checkbox"  name="tela_projetor"  data-toggle="toggle" data-onlabel="Sim" data-offlabel="Não" data-onstyle="success" data-offstyle="danger" >
            </div>
            <div class="col">
              <label for="quadro">Quadro</label>
              <input type="checkbox"  name="quadro"  data-toggle="toggle" data-onlabel="Sim" data-offlabel="Não" data-onstyle="success" data-offstyle="danger" >
              </div>            
            <div class="col">
            <label for="som">Som</label>
              <input type="checkbox"  name="som"  data-toggle="toggle" data-onlabel="Sim" data-offlabel="Não" data-onstyle="success" data-offstyle="danger" >
            </div>            
            <div class="col">
              <label for="ventilador">Ventilador</label>
               <input type="checkbox"  name="ventilador"  data-toggle="toggle" data-onlabel="Sim" data-offlabel="Não" data-onstyle="success" data-offstyle="danger" >
            </div>
            <div class="col">
             <label for="luz_azul">Luz azul</label>
                <input type="checkbox"  name="luz_azul"   data-toggle="toggle" data-onlabel="Sim" data-offlabel="Não" data-onstyle="success" data-offstyle="danger" >
            </div>
          </div>
               
          <div class="row justify-content-center">
            <div class="d-grid gap-1 col-4 mx-auto">
              <br>
              <a class="btn btn-danger" href="/gerenciar-salas" role="button">Cancelar</a>
            </div>
            <div class="d-grid gap-2 col-4 mx-auto" >
              <br>
              <button class="btn btn-primary">Confirmar</button>
              </div>
          
          
                
        <script src="https://cdn.jsdelivr.net/npm/bootstrap5-toggle@5.0.4/js/bootstrap5-toggle.ecmas.min.js"></script>
     
@endsection
 --}}
