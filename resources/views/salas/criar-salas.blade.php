
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
