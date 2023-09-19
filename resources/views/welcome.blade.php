@extends('layouts.app')

@section('title', 'Atendimento AFI')

@section('content')

<div class="container">
<p><h1 class="text-center"> Aplicação em modo Beta </h1></p>
<p><h1 class="text-center"> Seleciona uma das views para continuar: </h1></p>
    <div class="btn d-flex justify-content-center ">

        <a class="btn btn-primary p-3 mx-5 mt-3 " href="\gerenciar-atendimentos">Gerenciar Atendimento </a> </h3>
        <a class="btn btn-primary p-3 mx-5 mt-3 " href="\gerenciar-atendentes">Gerenciar Atendentes </a> </h3>
        <a class="btn btn-primary p-3 mx-5 mt-3 " href="\gerenciar-pessoas">Gerenciar Pessoas</a> </h3>

    </div>

</div>
@endsection
