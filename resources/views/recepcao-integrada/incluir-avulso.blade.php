@extends('layouts.app')

@section('title')
    Incluir Avulso
@endsection

@section('content')
    <br>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12">
                <form class="form-horizontal" method="post" action="/armazenar-avulso">
                    @csrf
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col">
                                INCLUIR ATENDIMENTO DE EMERGÊNCIA
                            </div>
                        </div>
                    </div>
                    <div class="card-body ">

                        <div class="mt-3 row">

                            <div class=" mb-3 col-6">
                                Nome Assistido
                                <span class="tooltips">
                                    <span class="tooltiptext">Obrigatório</span>
                                    <span style="color:red">*</span>
                                </span>
                                <select class="form-select select2" aria-label="Default select example" name="assistido" required>

                                    @foreach($assistidos as $assistido)
                                    <option value="{{ $assistido->id }}">{{ $assistido->nome_completo }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class=" mb-3 col-6">
                                Número de Acompanhantes
                                <input type="number" class="form-control" name="acompanhantes"  placeholder="0">
                            </div>
                            <div class=" mb-3 col-6">
                                Reunião Mediúnica
                                <span class="tooltips">
                                    <span class="tooltiptext">Obrigatório</span>
                                    <span style="color:red">*</span>
                                </span>
                                <select class="form-select select2" aria-label="Default select example" name="reuniao" required>
                                    @foreach($reuniao as $reunioes)
                                    <option value="{{ $reunioes->id }}">{{ $reunioes->nome }} - {{ date('H:i', strtotime($reunioes->h_inicio)) }}/{{ date('H:i', strtotime($reunioes->h_fim)) }} - Sala {{ $reunioes->sala }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class=" mb-3 col-6">
                                Motivo
                                <span class="tooltips">
                                    <span class="tooltiptext">Obrigatório</span>
                                    <span style="color:red">*</span>
                                </span>
                                <select class="form-select" aria-label="Default select example" name="motivo" required>
                                    @foreach($motivo as $motivos)
                                    <option value="{{ $motivos->id }}">{{ $motivos->nome }}</option>
                                    @endforeach
                                </select>
                            </div>

                        </div>


                    </div>
                    <div class="row mb-3">
                        <div class="d-grid gap-1 col-4 mx-auto">
                            <a class="btn btn-danger" href="/gerenciar-tratamentos" role="button">Cancelar</a>
                        </div>
                        <div class="d-grid gap-2 col-4 mx-auto">
                            <button type="submit" class="btn btn-primary" style="color:#fff;">Confirmar</button>
                        </div>
                    </div>
                </div>
            </form>
            </div>
        </div>
    </div>

<script>
    $(document).ready(function() {
        $('.select2').select2({ theme: 'bootstrap-5'});
    });
</script>
@endsection

@section('footerScript')
    <script src="{{ URL::asset('/js/pages/mascaras.init.js') }}"></script>
@endsection
