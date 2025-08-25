@extends('layouts.app')
@section('title')
    INCLUIR QUESTIONÁRIOS
@endsection
@section('content')
    <div class="container">
        <br><br>

        <form method="POST" id="form" action="/salvar-questoes">
            @csrf

            <div class="card mb-3">
                <div class="card-header">
                    INCLUIR QUESTIONÁRIOS
                </div>
                <div class="card-body">
                    <label>Descrição
                        <span class="tooltips">
                            <span class="tooltiptext">Obrigatório</span>
                            <span style="color:red">*</span>
                        </span>
                    </label>
                    <input class="form-control mb-3" style="border-color: gray" type="text" maxlength="120" name="descricao" required>

                    <label>Tipo de Atividade
                        <span class="tooltips">
                            <span class="tooltiptext">Obrigatório</span>
                            <span style="color:red">*</span>
                        </span>
                    </label>
                    <select class="form-select mb-3" style="border-color: gray" name="atividade" required>
                        @foreach ($tipoAtividade as $atividade)
                            <option value="{{ $atividade->id }}">
                                {{ $atividade->descricao }} - {{ $atividade->sigla }} -
                                {{ $atividade->semestre_sigla ?? 'N/P' }}
                            </option>
                        @endforeach
                    </select>
                    <div class="row">
                        <div class="col-md-3">
                            <label>Semana Referente ao Semestre
                                <span class="tooltips">
                                    <span class="tooltiptext">Obrigatório</span>
                                    <span style="color:red">*</span>
                                </span></label>
                            <input class="form-control mb-3" style="border-color: gray" type="number" min="1" max="20" name="semana">
                        </div>
                        <div class="col-md-3">
                            <label>Quantidade de perguntas:</label>
                            <div class="input-group">
                                <input type="number" min="1" style="border-color: gray" max="20" value="1" id="qtdePerguntas"
                                    class="form-control">
                                <button type="button" id="gerarPerguntas" style="border-color: gray" class="btn btn-primary">
                                    Gerar Perguntas
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div id="perguntasContainer"></div>

            <center>
                <div class="col-12" style="margin-top: 50px;">
                    <a href="/gerenciar-questoes" class="btn btn-danger col-3">
                        Cancelar
                    </a>
                    <button type="submit" class="btn btn-primary col-3 offset-3">
                        Confirmar
                    </button>
                </div>
            </center>
        </form>
    </div>

    <script>
        $(document).ready(function() {
            const alternativasPorPergunta = 4; // Sempre 4 alternativas

            function gerarRespostasHtml(perguntaIndex) {
                let html = '';
                for (let i = 0; i < alternativasPorPergunta; i++) {
                    html += `
                <table class="table mb-1 table-striped table-bordered"  style="border-color: gray">
                    <tbody>
                        <tr>
                            <td class="text-center" style="width: 50px; vertical-align: middle;">
                                <input class="form-check-input" style="border-color: gray"
                                    type="radio"
                                    name="perguntas[${perguntaIndex}][correta]"
                                    value="${i}"
                                    required>
                            </td>
                            <td>
                                <input class="form-control" type="text" style="border-color: gray"
                                    name="perguntas[${perguntaIndex}][respostas][${i}]"
                                    required placeholder="opção ${i+1}">
                            </td>
                        </tr>
                    </tbody>
                </table>
            `;
                }
                return html;
            }

            function gerarPerguntas(qtdePerguntas) {
                $('#perguntasContainer').empty();
                for (let i = 0; i < qtdePerguntas; i++) {
                    $('#perguntasContainer').append(`
                <div class="card mb-4">
                    <div class="card-header">
                        Pergunta ${i+1}
                    </div>
                    <div class="card-body">
                        <label>Enunciado:</label>
                        <textarea class="form-control mb-3" style="border-color: gray" name="perguntas[${i}][enunciado]" placeholder="Digite aqui" required></textarea>

                        <label>Assinale a alternativa correta:</label>
                        ${gerarRespostasHtml(i)}
                    </div>
                </div>
            `);
                }
            }

            // Gera 1 pergunta inicial
            gerarPerguntas($('#qtdePerguntas').val());

            // Botão para gerar conforme quantidade escolhida
            $('#gerarPerguntas').click(function() {
                let qtde = parseInt($('#qtdePerguntas').val());
                if (qtde >= 1 && qtde <= 20) {
                    gerarPerguntas(qtde);
                } else {
                    alert("A quantidade deve ser entre 1 e 20 perguntas.");
                }
            });
        });
    </script>
@endsection
