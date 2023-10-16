@extends('layouts.app')

@section('title', 'TESTER')

@section('content')

<div class="container">
    <h1>    Essa é a Pagina de Teste    </h1>
    <div class="dataTesting">
        <p> {{$atendente}}  </p>
        <p> {{$pessoa}}     </p>
    </div>
    @dump($atendente)
    @dump($pessoa)





</div>
@endsection
