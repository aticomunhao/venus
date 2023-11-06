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
              @if ($sala[0]->ar_condicionado = false) 
              <input type="checkbox"  name="ar_condicionado"  data-toggle="toggle" data-onlabel="Sim" data-offlabel="Não" data-onstyle="success" data-offstyle="danger">
              @elseif ($sala[0]->ar_condicionado = true)
                <input type="checkbox" checked name="ar_condicionado" data-toggle="toggle" data-onlabel="Sim" data-offlabel="Não" data-onstyle="success" data-offstyle="danger">
              @endif

            </div>
            <div class="col">
              <label for="armarios">Armários</label>
              @if ($sala[0]->armarios = false) 
              <input type="checkbox"  name="armarios"  data-toggle="toggle" data-onlabel="Sim" data-offlabel="Não" data-onstyle="success" data-offstyle="danger" >
              @elseif ($sala[0]->armarios = true)
              <input type="checkbox"  checked name="armarios"  data-toggle="toggle" data-onlabel="Sim" data-offlabel="Não" data-onstyle="success" data-offstyle="danger" >            
              @endif
            </div>
            <div class="col">
              <label for="bebedouro">Bebedouro</label>
              @if ($sala[0]->bebedouro = false) 
              <input type="checkbox"   name="bebedouro"  data-toggle="toggle" data-onlabel="Sim" data-offlabel="Não" data-onstyle="success" data-offstyle="danger" >
              @elseif ($sala[0]->bebedouro = true)
              <input type="checkbox"  checked name="bebedouro"  data-toggle="toggle" data-onlabel="Sim" data-offlabel="Não" data-onstyle="success" data-offstyle="danger" >
               @endif
            </div>
            <div class="col">
              <label for="controle">Controle_proj</label>
              @if ($sala[0]->controle_projetor = false)
              <input type="checkbox"  name="controle"  data-toggle="toggle" data-onlabel="Sim" data-offlabel="Não" data-onstyle="success" data-offstyle="danger" >
              @elseif ($sala[0]->controle_projetor = true)
              <input type="checkbox" checked name="controle"  data-toggle="toggle" data-onlabel="Sim" data-offlabel="Não" data-onstyle="success" data-offstyle="danger" >            
              @endif
            </div>
            <div class="col-1">
              <label for="computador">PC</label>
              @if ($sala[0]->computador= false)
              <input type="checkbox"  name="computador"  data-toggle="toggle" data-on="Sim" data-off="Não" data-onstyle="success" data-offstyle="danger" >
              @elseif ($sala[0]->computador = true)
              <input type="checkbox" checked name="computador"  data-toggle="toggle" data-on="Sim" data-off="Não" data-onstyle="success" data-offstyle="danger" >
              @endif
            </div>
            <div class="col">
              <label for="projetor">Projetor</label>
              @if ($sala[0]->projetor= false)
              <input type="checkbox"  name="projetor"   data-toggle="toggle" data-onlabel="Sim" data-offlabel="Não" data-onstyle="success" data-offstyle="danger" >
              @elseif ($sala[0]->projetor = true)           
              <input type="checkbox" checked name="projetor"   data-toggle="toggle" data-onlabel="Sim" data-offlabel="Não" data-onstyle="success" data-offstyle="danger" >        
              @endif
            </div>
            <div class="col">
              <label for="tela_projetor">Tela_proj</label>
              @if ($sala[0]->tela_projetor= false)
              <input type="checkbox"  name="tela_projetor"  data-toggle="toggle" data-onlabel="Sim" data-offlabel="Não" data-onstyle="success" data-offstyle="danger" >
              @elseif ($sala[0]->tela_projetor = true)  
              <input type="checkbox" checked name="tela_projetor"  data-toggle="toggle" data-onlabel="Sim" data-offlabel="Não" data-onstyle="success" data-offstyle="danger" >
              @endif
            </div>
            <div class="col">
              <label for="quadro">Quadro</label>
              @if ($sala[0]->ar_condicionado = false)
              <input type="checkbox"  name="quadro"  data-toggle="toggle" data-onlabel="Sim" data-offlabel="Não" data-onstyle="success" data-offstyle="danger" >
              @elseif ($sala[0]->ar_condicionado = true)
              <input type="checkbox" checked  name="quadro"  data-toggle="toggle" data-onlabel="Sim" data-offlabel="Não" data-onstyle="success" data-offstyle="danger" >
              @endif
              </div>            
            <div class="col">
            <label for="som">Som</label>
            @if ($sala[0]->som = false)
              <input type="checkbox" name="som"  data-toggle="toggle" data-onlabel="Sim" data-offlabel="Não" data-onstyle="success" data-offstyle="danger" >
              @elseif ($sala[0]->som = true)
              <input type="checkbox" checked name="som"  data-toggle="toggle" data-onlabel="Sim" data-offlabel="Não" data-onstyle="success" data-offstyle="danger" >
            @endif
            </div>            
            <div class="col">
              <label for="ventilador">Ventilador</label>
              @if ($sala[0]->ventilador= false)
               <input type="checkbox" name="ventilador"  data-toggle="toggle" data-onlabel="Sim" data-offlabel="Não" data-onstyle="success" data-offstyle="danger" >
              @elseif ($sala[0]->ventilador= true)
               <input type="checkbox" checked name="ventilador"  data-toggle="toggle" data-onlabel="Sim" data-offlabel="Não" data-onstyle="success" data-offstyle="danger" >
              @endif
            </div>
            <div class="col">
             <label for="luz_azul">Luz azul</label>
              @if ($sala[0]->luz_azul= false)
                <input type="checkbox"  name="luz_azul"   data-toggle="toggle" data-onlabel="Sim" data-offlabel="Não" data-onstyle="success" data-offstyle="danger" >
              @elseif ($sala[0]->luz_azul= true)
                <input type="checkbox" checked name="luz_azul"   data-toggle="toggle" data-onlabel="Sim" data-offlabel="Não" data-onstyle="success" data-offstyle="danger" >
              @endif
            </div>
          </div>
               
          <div class="row justify-content-center">
              <div class="d-grid gap-2 col-4 mx-auto" >
              <br>
              <button class="btn btn-primary">Confirmar</button>
          </div>
          <!--<script>
           //function toggleOnByInput() {
           //     $('#checked').prop('checked', true).change()
          //    }
           //   function toggleOffByInput() {
          //      $('#checked').prop('checked', false).change()
         //     }
       //   </script>--> 
      
          
                
        <script src="https://cdn.jsdelivr.net/npm/bootstrap5-toggle@5.0.4/js/bootstrap5-toggle.ecmas.min.js"></script>
     
@endsection