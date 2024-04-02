@extends('layouts.app')

@section('title')
    Editar Dirigentes
@endsection

@section('content')
    <div class="container">
        <br>
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col">
                        EDITAR DIRIGENTE
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
                        <div class="col-12 ">
                        <label for="id_pessoa" clas4s="form-label">Nome</label>
                        <select class="form-selec41t status " id="4" name="id_pessoa" disabled>
                            <option>{{ $dirigente->nome_completo }}</option>
                            </select>
                    </div>
                    {{-- Fim Select2 Nome --}}

                    {{-- Select2 Multiple Grupos --}}
                        <div class="col-12  mb-3 mt-3">
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

                                $('.select2').select2( { theme: 'bootstrap-5'});

                                var infos = <?php echo json_encode($info); ?>; //codifica a variavel do PHP para ser aceita pelo JavaScript
                                var keys = Object.entries(infos); //Pega os valores do JSON e transforma em um array
                                var len = Object.keys(keys).length; // Pega apenas as Keys do Array e conta a quantidade
                                const arr = []; //Inicializa uma variavel vetor vazia

                                for (var i = 0; i < len ; i++) {//Loop de 0 ao tamanho descoberto acima

                                    var key = Object.values(keys[i][1]);//pega o valor do array de duas dimensões e armazena numa variavel
                                    arr.push(key);//Adiciona o valor armazenado ao array vazio

                                  }

                              $('.select2').val(arr).trigger('change');


                            });
                        </script>

                        <br>
                    {{-- Botões Cancelar e Confirmar --}}
                    <div class="row mt-4 justify-content-center">
                        <div class="d-grid gap-1 col-4 mx-auto">
                            <a class="btn btn-danger" href="/gerenciar-dirigentes" role="button">Cancelar</a>
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
