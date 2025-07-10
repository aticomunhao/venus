@extends('layouts.app')
@section('title')
    Incluir Questões
@endsection
@section('content')
    <div class="container">


        <br />
        <br />

        <form method="get" id="form" action="/armazenar-questoes">
        <div class="card mb-3">
            <div class="card-header">
                INCLUIR QUESTÕES
            </div>
            <div class="card-body">
                    @csrf
                    Descrição
                    <span class="tooltips">
                        <span class="tooltiptext">Obrigatório</span>
                        <span style="color:red">*</span>
                    </span>
                    <div class="tooltips">
                        <i class="fa-solid fa-circle-info" style="color: #74C0FC;"></i>
                        <span class="tooltiptext">Resumo para Pesquisa da Questão</span>
                    </div>
                    <input class="form-control mb-3" type="text" maxlength="120" name="descricao" required>

                    Tipo de Atividade
                    <span class="tooltips">
                        <span class="tooltiptext">Obrigatório</span>
                        <span style="color:red">*</span>
                    </span>
                    <select class="form-select mb-3" name="atividade" required>
                        @foreach ($tipoAtividade as $atividade)
                            <option value="{{ $atividade->id }}">{{ $atividade->descricao }} - {{ $atividade->sigla }}
                            </option>
                        @endforeach
                    </select>
            </div>
        </div>
        <div class="card">
            <div class="card-header">
                QUESTÃO
            </div>
            <div class="card-body">
                Enunciado
                <span class="tooltips">
                    <span class="tooltiptext">Obrigatório</span>
                    <span style="color:red">*</span>
                </span>
                <textarea class="form-control mb-3" type="text" name="enunciado" required></textarea>

                <hr />

                <div id="respostas">

                </div>



            </div>
        </div>
        <center>
            <div class="col-12" style="margin-top: 50px;">
                <a href="/gerenciar-questoes" class="btn btn-danger col-3">
                    Cancelar
                </a>
                <button type = "submit" class="btn btn-primary col-3 offset-3">
                    Confirmar
                </button>
            </div>
        </center>
        </form>

    </div>
    <script>
        // Essa parte do código compreende uma adaptação preparando para uma futura modificação

        $(document).ready(function() {

            const numeroRespostas = 4; // Número de Questões


              for (let i = 0; i < numeroRespostas; i++) {
            $('#respostas').append(
                   '<div class="row col mb-3">' +
                   '<div class="col-1">' +
                   '<center>' +
                '<input class="form-check-input" type="radio" name="radioResposta" id="radio' + i + '" value="' + i + '" required>' +
                  '</center>' +
                   '</div>' +
                   '<div class="col">' +
                   '<input class="form-control" type="text" name="resposta[' + i + ']" id="resposta' + i +
                   '" required>' +
                    '</div>' +
                   '</div>' +
                   '</form>'

            )
             }


        });
    </script>
@endsection
