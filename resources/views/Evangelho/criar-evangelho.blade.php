@extends('layouts.app')

@section('content')
<br>
<div class="container">
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col">AGENDAR EVANGELHO</div>
            </div>
        </div>
        <br>
        <div class="card-body">
            <form class="form-horizontal mt-2" method="post" action="/incluir-evangelho/{{$encaminhamento->id}}">
                @csrf
                <div class="row mb-">
                    <div class="col">
                        <label for="id_encaminhamento" class="form-label">Nome</label>
                        <select class="form-control" id="id_encaminhamento" name="id_encaminhamento" disabled>
                            @foreach ($informacoes as $informacao)
                            <option value="{{ $informacao->id_pessoa }}">{{ $informacao->nome_pessoa }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <br>
                <div class="row mb-3">
                    <div class="col">
                        <label for="qtd_adultos" class="form-label">Quantidade de adultos</label>
                        <input type="number" class="form-control" id="qtd_adultos"  name="qtd_adultos" min="1" max="300" name="numero" oninput="javascript: if (this.value.length > 3) this.value = this.value.slice(0, 3); validarNumero(this);" required="required">
                    </div>
                    <div class="col">
                        <label for="qtd_criancas" class="form-label">Quantidade de crianças</label>
                        <input type="number" class="form-control" id="qtd_criancas"  min="1" max="800" name="qtd_criancas"min="1" max="300" name="numero" oninput="javascript: if (this.value.length > 3) this.value = this.value.slice(0, 3); validarNumero(this);" required="required">
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col">
                        <label for="data" class="form-label">Data</label>
                        <input type="date" class="form-control" id="data" name="data">
                    </div>

                    <div class="col">
                        <label for="hora" class="form-label">Hora</label>
                        <input type="time" class="form-control" id="hora" name="hora">
                    </div>
                </div>
                <br>
                <div class="row mt-4 justify-content-center">
                    <div class="col-4">
                        <a class="btn btn-danger w-100" href="/gerenciar-evangelho" role="button">Cancelar</a>
                    </div>
                    <div class="col-4">
                        <button type="submit" class="btn btn-primary w-100">Confirmar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <script>
        // Obtém a data mínima no formato AAAA-MM-DD
        var dataMinima = new Date("2024-01-01").toISOString().split('T')[0];

        // Define a data mínima no campo de entrada
        document.getElementById("data").setAttribute("min", dataMinima);
    </script>
</div>
@endsection
