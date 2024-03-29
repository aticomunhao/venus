@extends('layouts.app')

@section('content')
    <div class="container">
        <br>
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col">
                        CADASTRAR ATENDENTE
                    </div>
                </div>
            </div>
            <br>
            <div class="card-body">
                <form class="form-horizontal mt-2" method="post" action="{{ route('cadastrar') }}">
                    @csrf
                    {{-- Inicio Row --}}
                    <div class="row">
                        {{-- Select2 Nome  --}}
                        <div class="col-12">
                        <label for="id_pessoa" class="form-label">Nome</label>
                        <select class="form-select status select2" id="4" name="id_pessoa" >
                            @foreach ($pessoas as $pessoa)
                                <option  value="{{ $pessoa->idp }}">{{ $pessoa->nome_completo }}
                                </option>
                            @endforeach
                            </select>
                    </div>
                    {{-- Fim Select2 Nome --}}

                    {{-- Select2 Multiple Grupos --}}
                        <div class="col-12 mt-3 mb-3">
                            <label for="id_grupo" class="form-label">Nome grupo</label>
                            <select class="form-select select2" aria-label=".form-select-lg example" name="id_grupo[]" id="id_grupo" multiple>
                                @foreach ($grupo as $grupos)
                                    <option value="{{ $grupos->id }}" >{{ $grupos->nome }}</option>
                                @endforeach
                            </select>
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
                            <a class="btn btn-danger" href="/gerenciar-atendentes" role="button">Cancelar</a>
                        </div>
                        <div class="d-grid gap-2 col-4 mx-auto">
                            <button type="submit" class="btn btn-primary">Confirmar</button>
                        </div>
                    </div>
                    {{-- Fim botões Cancelar e Confirmar --}}
                </form>
            </div>
        </div>
    </div>
@endsection
