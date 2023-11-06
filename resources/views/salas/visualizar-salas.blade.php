@extends('layouts.app')

@section('content')
 @include('pessoal.popUp-incluir')

<div class="row form-group">
    <div class="row justify-content-center">
      <div class="col-1">Ar-cond
      <input type="checkbox"  name="ar_condicionado" data-toggle="toggle" data-onlabel="Sim" data-offlabel="Não" data-onstyle="success" data-offstyle="danger" checked>
    </div>
      <div class="col-1">Controle
      <input type="checkbox" name= "controle" checked data-toggle="toggle" data-onlabel="Sim" data-offlabel="Não" data-onstyle="success" data-offstyle="danger" checked>
    </div>
    <div class="col-1">PC
      <input type="checkbox" name= "computador" checked data-toggle="toggle" data-onlabel="Sim" data-offlabel="Não" data-onstyle="success" data-offstyle="danger" checked>
    </div>
      <div class="col-1">Projetor
      <input type="checkbox"name= "projetor" checked data-toggle="toggle" data-onlabel="Sim" data-offlabel="Não" data-onstyle="success" data-offstyle="danger" checked>
    </div>
    <div class="col-1">Quadro
      <input type="checkbox" name= "quadro" checked data-toggle="toggle" data-onlabel="Sim" data-offlabel="Não" data-onstyle="success" data-offstyle="danger" checked>
    </div>
          <div class="col-1">Som
      <input type="checkbox" name="som" checked data-toggle="toggle" data-onlabel="Sim" data-offlabel="Não" data-onstyle="success" data-offstyle="danger" checked>
    </div>
    <div class="col-1">Tela_proj 
      <input type="checkbox" name="tela_projeto" checked data-toggle="toggle" data-onlabel="Sim" data-offlabel="Não" data-onstyle="success" data-offstyle="danger" checked>
    </div>
    <div class="col-1">Ventilador
      <input type="checkbox" name= "ventilador" checked data-toggle="toggle" data-onlabel="Sim" data-offlabel="Não" data-onstyle="success" data-offstyle="danger" checked>
    </div>
  </div>

  @endsection