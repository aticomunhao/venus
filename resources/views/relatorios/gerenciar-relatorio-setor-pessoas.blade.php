@extends('layouts.app')

@section('title')
    Relatório de Trabalhadores
@endsection

@section('content')
    <div class="container-fluid">
        <h4 class="card-title" style="font-size:20px; text-align: left; color: gray; font-family: calibri">
            RELATÓRIO DE TRABALHADORES
        </h4>
        <div class="col-12">
            <div class="row justify-content-center">
                <div>
                    <br />
                    <form action="{{ url('/gerenciar-relatorio-setor-pessoas') }}" method="get">
                        <div class="row">
                            <div class="col">
                                Setor
                                <select class="form-select select2" id="setor" name="setor">
                                    <option value="">Todos</option>
                                    @foreach ($setor as $setores)
                                        <option value="{{ $setores->id }}"
                                            {{ request('setor') == $setores->id ? 'selected' : '' }}>
                                            {{ $setores->nome }} - {{ $setores->sigla }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col">
                                Grupo
                                <select class="form-select select2" id="grupo" name="grupo">
                                    <option value="">Todos</option>
                                    @foreach ($grupo as $grupos)
                                        <option value="{{ $grupos->id }}"
                                            {{ request('grupo') == $grupos->id ? 'selected' : '' }}>
                                            {{ $grupos->nome_grupo }} - {{ $grupos->sigla }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-2">
                                Dia
                                <select class="form-select select2" id="dia" name="dia">
                                    <option value="">Todos</option>
                                    @foreach ($dias as $dia)
                                        <option value="{{ $dia->id }}"
                                            {{ request('dia') == $dia->id ? 'selected' : '' }}>
                                            {{ $dia->nome }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-2">
                                Função
                                <select class="form-select select2" id="funcao" name="funcao">
                                    <option value="">Todos</option>
                                    @foreach ($funcao as $item)
                                        <option value="{{ $item->id }}"
                                            {{ request('funcao') == $item->id ? 'selected' : '' }}>
                                            {{ $item->nome }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col">
                                Nome do Membro
                                <select class="form-select select2" id="nome" name="nome">
                                    <option value="">Todos</option>
                                    @foreach ($atendentesParaSelect as $atendente)
                                        <option value="{{ $atendente->ida }}"
                                            {{ request('nome') == $atendente->ida ? 'selected' : '' }}>
                                            {{ $atendente->nm_4 }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col mt-3">
                                <input class="btn btn-light btn-sm me-md-2"
                                    style="font-size: 0.9rem; box-shadow: 1px 2px 5px #000000; margin:5px;" type="submit"
                                    value="Pesquisar">
                                <a href="{{ url('/gerenciar-relatorio-setor-pessoas') }}"
                                    class="btn btn-light btn-sm me-md-2"
                                    style="font-size: 0.9rem; box-shadow: 1px 2px 5px #000000; margin:5px;">Limpar</a>
                            </div>
                        </div>
                    </form>
                    <hr />
                </div>
            </div>
        </div>


        <div class="card">
            <div class="card-body">





                <div class="accordion" id="accordionSetor">


                    @foreach ($result as $keyItem => $item)
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#setor{{ $loop->index }}" aria-expanded="false"
                                    aria-controls="flush-collapseOne">
                                    {{ $keyItem }}
                                </button>
                            </h2>
                            <div id="setor{{ $loop->index }}" class="accordion-collapse collapse show"
                                data-bs-parent="#accordionSetor">
                                <div class="accordion-body">
                                    <div class="accordion" id="accordionDia">


                                        @foreach ($item as $keyDia => $dia)
                                            <div class="accordion-item">
                                                <h2 class="accordion-header">
                                                    <button class="accordion-button type="button"
                                                        data-bs-toggle="collapse" data-bs-target="#dia{{ $keyDia }}"
                                                        aria-expanded="false" aria-controls="flush-collapseOne">
                                                        {{ $keyDia }}
                                                    </button>
                                                </h2>
                                                <div id="dia{{ $keyDia }}" class="accordion-collapse collapse show"
                                                    data-bs-parent="#accordionDia">
                                                    <div class="accordion-body">
                                                        <div class="accordion" id="accordionGrupo">


                                                            @foreach ($dia as $keyGrupo => $grupo)
                                                                <div class="accordion-item">
                                                                    <h2 class="accordion-header">
                                                                        <button class="accordion-button"
                                                                            type="button" data-bs-toggle="collapse"
                                                                            data-bs-target="#grupo{{ current(current($grupo))->id }}"
                                                                            aria-expanded="false"
                                                                            aria-controls="flush-collapseOne">
                                                                            {{ $keyGrupo }}
                                                                        </button>
                                                                    </h2>
                                                                    <div id="grupo{{ current(current($grupo))->id }}"
                                                                        class="accordion-collapse collapse show"
                                                                        data-bs-parent="#accordionGrupo">
                                                                        <div class="accordion-body">
                                                                            <div class="accordion"
                                                                                id="accordionFuncao">


                                                                                @foreach ($grupo as $keyFuncao => $funcao)
                                                                                    <div class="accordion-item">
                                                                                        <h2 class="accordion-header">
                                                                                            <button
                                                                                                class="accordion-button"
                                                                                                type="button"
                                                                                                data-bs-toggle="collapse"
                                                                                                data-bs-target="#funcao{{ current($funcao)->id }}"
                                                                                                aria-expanded="false"
                                                                                                aria-controls="flush-collapseOne">
                                                                                                {{ $keyFuncao }}
                                                                                            </button>
                                                                                        </h2>
                                                                                        <div id="funcao{{ current($funcao)->id }}"
                                                                                            class="accordion-collapse collapse show"
                                                                                            data-bs-parent="#accordionFuncao">
                                                                                            <div class="accordion-body">
                                                                                                <table class="table">
                                                                                                    @foreach ($funcao as $integrante)
                                                                                                        <tr>
                                                                                                            <td>
                                                                                                                {{$integrante->nome_completo}}
                                                                                                            </td>
                                                                                                        </tr>
                                                                                                    @endforeach
                                                                                                </table>
                                                                                            </div>  
                                                                                        </div>
                                                                                    </div>
                                                                                @endforeach

                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            @endforeach

                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach

                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach

                </div>
            </div>
        </div>

        <br />
        {{ $result->appends(request()->query())->links('pagination::bootstrap-5') }}
    </div class="d-flex justify-content-center">

    </div>
    </div>
    <script>
        $(document).ready(function() {
            if ({{ request('dia') === null }}) {
                $('#dia').prop('selectedIndex', 0);
            }

        });
    </script>
@endsection
