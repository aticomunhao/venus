@extends('layouts.app')

@section('content')
<link href="https://cdn.jsdelivr.net/npm/bootstrap5-toggle@5.0.4/css/bootstrap5-toggle.min.css" rel="stylesheet">
<br>
<div class="container">
    <div class="card">
     <h5 class="card-header">Editar</h5>
        <div class="card-body">
                   
             <form class="form-horizontal mt-2" method="post" action="/atualizar-salas/{{$sala[0]->id}}">
            @csrf
            <div class="col">Nome 
              <input type="text" class="form-control" id="nome" name="nome" value="{{$sala[0]->nome}}">
            </div>
            <br>
              <div class="col">Número 
                <input type="number" class="form-control" id="numero" name="numero" value="{{$sala[0]->numero}}">
                <br>
                <div class="col">localização
                  <input type="text" class="form-control" id="localizacao" name="localizacao"value="{{$sala[0]->localizacao}}">
                </div>
              <br>
              <div class="col">Tamanho da sala
                <input type="number" class="form-control" id="tamanho_sala" name="tamanho_sala"value="{{$sala[0]->tamanho_sala}}">
              </div>
              <br>
            <div class="col">Número de lugares
              <input type="number" class="form-control" id="nr_lugares" name="nr_lugares" value="{{$sala[0]->nr_lugares}}">
          </div>
          <br>
         
          <br>
          <div class="row form-group">
            <div class="col">
              <label for="ar_condicionado">Ar-cond</label>
              <input type="checkbox" name="ar_condicionado" value="{{$sala[0]->ar_condicionado}}" data-toggle="toggle" data-onlabel="Sim" data-offlabel="Não" data-onstyle="success" data-offstyle="danger" checked>
            </div>
            <div class="col">
              <label for="armarios">Armários</label>
              <input type="checkbox" name="armarios" value="1" checked data-toggle="toggle" data-onlabel="Sim" data-offlabel="Não" data-onstyle="success" data-offstyle="danger" checked>
            </div>
            <div class="col">
              <label for="bebedouro">Bebedouro</label>
              <input type="checkbox" name="bebedouro" value="1" checked data-toggle="toggle" data-onlabel="Sim" data-offlabel="Não" data-onstyle="success" data-offstyle="danger" checked>
            </div>
            <div class="col">
              <label for="controle">Controle_proj</label>
              <input type="checkbox" name="controle" value="1" checked data-toggle="toggle" data-onlabel="Sim" data-offlabel="Não" data-onstyle="success" data-offstyle="danger" checked>
            </div>
            <div class="col-1">
              <label for="computador">PC</label>
              <input type="checkbox" name="computador" value="1" checked data-toggle="toggle" data-onlabel="Sim" data-offlabel="Não" data-onstyle="success" data-offstyle="danger" checked>
            </div>
            <div class="col">
              <label for="projetor">Projetor</label>
              <input type="checkbox" name="projetor" value="1" checked data-toggle="toggle" data-onlabel="Sim" data-offlabel="Não" data-onstyle="success" data-offstyle="danger" checked>
            </div>
            <div class="col">
              <label for="tela_projetor">Tela_proj</label>
              <input type="checkbox" name="tela_projetor" value="1" checked data-toggle="toggle" data-onlabel="Sim" data-offlabel="Não" data-onstyle="success" data-offstyle="danger" checked>
            </div>
            <div class="col">
              <label for="quadro">Quadro</label>
              <input type="checkbox" name="quadro" value="1" checked data-toggle="toggle" data-onlabel="Sim" data-offlabel="Não" data-onstyle="success" data-offstyle="danger" checked>
            </div>
            <div class="col">
              <label for="som">Som</label>
              <input type="checkbox" name="som" value="1" checked data-toggle="toggle" data-onlabel="Sim" data-offlabel="Não" data-onstyle="success" data-offstyle="danger" checked>
            </div>
            
            <div class="col">
              <label for="ventilador">Ventilador</label>
              <input type="checkbox" name="ventilador" value="1" checked data-toggle="toggle" data-onlabel="Sim" data-offlabel="Não" data-onstyle="success" data-offstyle="danger" checked>
            </div>
            <div class="col">
              <label for="luz_azul">Luz azul</label>
              <input type="checkbox" name="luz_azul" value="1" checked data-toggle="toggle" data-onlabel="Sim" data-offlabel="Não" data-onstyle="success" data-offstyle="danger" checked>
            </div>
               </div>  
          <div class="row justify-content-center">
              <div class="d-grid gap-2 col-4 mx-auto" >
              <br>
              <button class="btn btn-primary">Confirmar</button>
              </div>
              
      
          
                
        <script src="https://cdn.jsdelivr.net/npm/bootstrap5-toggle@5.0.4/js/bootstrap5-toggle.ecmas.min.js"></script>
     
@endsection