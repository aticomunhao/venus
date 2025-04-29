@vite(['resources/sass/app.scss', 'resources/css/app.css', 'resources/js/app.js'])


<div class="row">
    <div class="col-8">
        Nome Completo
        <input class="form-control" id="nome" name="nome" type="text" maxlength="100" placeholder="Nome..." required>
    </div>


    <div class="col-4">

        Habilidade
        <select class="form-select" id="Habilidade" aria-label="Default select example" required name="Habilidade">
            @foreach ($habilidades as $habilidade)
                <option value="{{ $habilidade->id }}"> {{ $habilidade->tipo }} </option>
            @endforeach
        </select>

    </div>
</div>
<div class="row mt-3">
    <div class="col-2">
        Data de Inicio
        <input class="form-control" id="dt_inicio" name="dt_inicio" type="date">
    </div>
    <div class="col-2">
        Data de Fim
        <input class="form-control" id="dt_fim" name="dt_fim" type="date">
    </div>
    <div class="col-8">
        Cronograma
        <select class="form-select select2" id="Cronograma" aria-label="Default select example" required
            name="Cronograma">
            @foreach ($cronogramas as $gr)
                <option value="{{ $gr->idg }}">{{ $gr->nomeg }} ({{ $gr->sigla }})-{{ $gr->dia_semana }} | {{ date('H:i', strtotime($gr->h_inicio)) }}/{{ date('H:i', strtotime($gr->h_fim)) }} | Sala {{ $gr->sala }} | {{ $gr->status == 'Inativo' ? 'Inativo' : $gr->descricao_status }}
                </option>
            @endforeach
        </select>
    </div>
</div>


<script>
    $(document).ready(function() {
        let dados = {'Habilidade' : $('#Habilidade option:selected').html(), 'Cronograma' : $('#Cronograma option:selected').html()}
        $('#cardTitle').html('Exemplo 2')

        $('#cardBody').on('teste123', function(event) {
            // Trigger de evento customizado
            $('#cardBody').trigger('teste1234', JSON.stringify(dados));
        });




    });
</script>

</div>
