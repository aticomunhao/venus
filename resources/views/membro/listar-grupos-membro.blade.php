@extends('layouts.app')
@section('title', 'Administrar Grupos')
@section('content')
    <div class="container-fluid">
        <h4 class="card-title" style="font-size:20px; text-align: left; color: gray; font-family:calibri">ADMINISTRAR GRUPOS
        </h4>

        <div class="col-12">
            <form action="/gerenciar-grupos-membro" class="form-horizontal mt-4" method="GET">
                <div class="row">
                    <div class="col-6">
                        Grupo
                        <select class="form-select select2 grupo" type="text" id="nome_grupo" name="nome_grupo"
                            value="{{ request('nome_grupo') }}">
                            <option></option>
                            @foreach ($membro_cronograma as $grupos)
                                <option value="{{ $grupos->id }}">{{ $grupos->nome_grupo }} - {{ $grupos->dia }}-
                                    {{ date('H:i', strtotime($grupos->h_inicio)) }}/{{ date('H:i', strtotime($grupos->h_fim)) }}
                                    - Sala {{ $grupos->sala }}</option>>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-2">
                        Membro
                        <select class="form-select select2 membro" type="text" id="nome_membro" name="nome_membro"
                            value="{{ request('nome_grupo') }}">
                            <option></option>
                            @foreach ($membro as $membros)
                                <option value="{{ $membros->id_associado }}">{{ $membros->nome_completo }}</option>>
                            @endforeach
                        </select>
                    </div>

                    <div class="col">
                        <br>
                        <input class="btn btn-light btn-sm me-md-2"
                            style="font-size: 0.9rem; box-shadow: 1px 2px 5px #000000; margin:5px;" type="submit"
                            value="Pesquisar">
                        <a href="/gerenciar-grupos-membro"><input class="btn btn-light btn-sm me-md-2"
                                style="font-size: 0.9rem; box-shadow: 1px 2px 5px #000000; margin:5px;" type="button"
                                value="Limpar"></a>

                        @if (in_array(13, session()->get('usuario.acesso')))
                            <a href="/criar-membro"><input class="btn btn-success btn-sm me-md-2" style="font-size: 0.9rem;"
                                    type="button" value="Novo membro +"></a>
                        @endif
                    </div>
                </div>

            </form>
            
        </div>
        <br>
      <hr>
        Quantidade de grupos: {{ $contar }}
        <div class="table">
            <table
                class="table table-sm table-striped table-bordered border-secondary table-hover align-middle text-center">
                <tr style="background-color: #d6e3ff; font-size:14px; color:#000000">
                    <th>ID</th>
                    <th class="small-column">GRUPO</th>
                    <th>DIA</th>
                    <th>HORÁRIO INICIO</th>
                    <th>HORÁRIO FIM</th>
                    <th>SALA</th>
                    <th class="small-column">SETOR</th>
                    <th>STATUS</th>
                    <th>AÇÕES</th>

                </tr>



                <tbody>
                    @foreach ($membro_cronograma as $membros)
                        <tr>
                            <td>{{ $membros->id }}</td>
                            <td>{{ $membros->nome_grupo }}</td>
                            <td>{{ $membros->dia }}</td>
                            <td>{{ $membros->h_inicio }}</td>
                            <td>{{ $membros->h_fim }}</td>
                            <td>{{ $membros->sala }}</td>
                            <td>{{ $membros->nome_setor }}</td>
                            <td>{{ $membros->status }}</td>

                            <td>

                                <a href="/gerenciar-membro/{{ $membros->id }}" type="button"
                                    class="btn btn-outline-warning btn-sm tooltips">
                                    <span class="tooltiptext">Gerenciar</span>
                                    <i class="bi bi-three-dots" style="font-size: 1rem; color:#000;"></i>
                                </a>

                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>


    <script>
        $(document).ready(function() {
            $('.select2').select2({
                theme: 'bootstrap-5'
            });

            //Deixa o select status como padrao vazio
            $(".grupo").prop("selectedIndex", 0);
            $(".membro").prop("selectedIndex", 0);

        });
    </script>


    <script>
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-tt="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })

        function confirmarExclusao(id, nome) {
            document.getElementById('btn-confirmar-exclusao').setAttribute('data-id', id);
            document.getElementById('modal-body-text').innerText = nome;
            $('#confirmacaoDelecao').modal('show');
        }

        function confirmarDelecao() {
            var id = document.getElementById('btn-confirmar-exclusao').getAttribute('data-id');
            window.location.href = '/deletar-membro/' + id;
        }
    </script>
    <style>
        .small-column {
            width: 22%;
        }
    </style>

    </div class="d-flex justify-content-center">
    {{ $membro_cronograma->links('pagination::bootstrap-5') }}
    </div>

@endsection
