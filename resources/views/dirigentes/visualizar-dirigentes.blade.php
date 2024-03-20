@extends('layouts.app')

@section('title')
    Visualizar Dirigentes
@endsection

@section('content')
    <div class="container">
        <br>
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col">
                    VISUALIZAR DIRIGENTE
                    </div>
                </div>
            </div>
            <br>
            <div class="card-body">
                <form class="form-horizontal mt-2" method="post" action="/atualizar-dirigentes/{{ $dirigente->id }}">
                    @csrf
                    {{-- Inicio Row --}}
                    <div class="row">
                        {{-- Select2 Nome  --}}
                        <div class="col-12">
                        <label for="id_pessoa" class="form-label">Nome</label>
                        <select class="form-select status " id="4" name="id_pessoa" disabled>
                            <option>{{ $dirigente->nome_completo }}</option>
                            </select>
                    </div>
                    {{-- Fim Select2 Nome --}}

                    {{-- Select2 Multiple Grupos --}}
                    <div class="col-12 mt-4" >Grupos<hr />
                    @foreach ($grupos as $grupo)

                    <input class="form-control mt-3" type="text" id="1" name="cpf" value="{{ $grupo->nome }}" disabled>
                    @endforeach
                    </div>
                        {{-- Fim Select2 Multiplw Grupos --}}
                    </div>
                    {{-- Fim row --}}

                    {{-- Import JQuery --}}
                        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
                        <script>
                            $(document).ready(function() {

                                //Importa o select2 com tema do Bootstrap para a classe "select2"
                                $('.select2').select2( { theme: 'bootstrap-5'});




                            });
                        </script>

                        <br>
                    {{-- Botões Cancelar e Confirmar --}}
                    <div class="row mt-4 justify-content-center">
                        <div class="d-grid gap-1 col-4 mx-auto">
                            <a class="btn btn-primary" href="/gerenciar-dirigentes" role="button">Retornar</a>
                        </div>

                    </div>
                    {{-- Fim botões Cancelar e Confirmar --}}
                </form>
            </div>
        </div>
    </div>
@endsection
