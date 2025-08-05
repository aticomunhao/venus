@extends('layouts.app')

@section('title')
    Gerenciar Instituições
@endsection

@section('content')
    <div class="container-fluid">
        <h4 class="card-title" class="card-title" style="font-size:20px; text-align: left; color: gray; font-family:calibri">
            GERENCIAR INSTITUIÇÕES</h4>
        <br>
        <div class="col-12">
            <div class="row justify-content-center">
                <div class="row">
                    <div class="d-flex">
                        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#filtros"
                            style="box-shadow: 1px 2px 3px #000000; margin-right: 10px;">
                            Pesquisar <i class="bi bi-funnel"></i>
                        </button>
                        <a href="/incluir-instituicao" class="btn btn-success btn-sm"
                            style="font-size: 0.9rem; box-shadow: 1px 2px 5px #000000;">
                            Novo+
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <br>
        <hr>
        <table {{-- Inicio da tabela de informacoes --}}
            class= "table table-sm table-striped table-bordered border-secondary table-hover align-middle"
            id="tabela-materiais" style="width: 100%">
            <thead style="text-align: center;">{{-- inicio header tabela --}}
                <tr style="background-color: #d6e3ff; font-size:15px; color:#000;" class="align-middle">
                    <th>ID</th>
                    <th>NOME FANTASIA</th>
                    <th>RAZÃO SOCIAL</th>
                    <th>EMAIL</th>
                    <th>CNPJ</th>
                    <th>SITE</th>
                    <th>STATUS</th>
                    <th>AÇÕES</th>
                </tr>
            </thead>{{-- Fim do header da tabela --}}
            <tbody style="font-size: 15px; color:#000000; text-align: center;">
                {{-- Inicio body tabela --}}
                @foreach ($lista as $listas)
                    <tr>
                        <td>{{ $listas->id }}</td>
                        <td>{{ $listas->nome_fantasia }}</td>
                        <td>{{ $listas->razao_social }}</td>
                        <td>{{ $listas->email_contato }}</td>
                        <td>{{ $listas->cnpj }}</td>
                        <td>{{ $listas->site }}</td>
                        <td>{{ $listas->status }}</td>
                        <td>
                            <a href="" class="btn btn-sm btn-outline-primary" data-tt="tooltip"
                                style="font-size: 1rem; color:#303030" data-placement="top" title="Visualizar">
                                <i class="bi bi-search"></i>
                            </a>
                            {{-- @if (in_array($aquisicaos->tipoStatus->id, ['3', '2'])) --}}
                            <a href="" class="btn btn-sm btn-outline-primary" data-tt="tooltip"
                                style="font-size: 1rem; color:#303030" data-placement="top" title="Aprovar">
                                <i class="bi bi-check-lg"></i>
                            </a>
                            {{-- @endif --}}
                            {{-- @if ($aquisicaos->tipoStatus->id == '1') --}}
                            <a href="" class="btn btn-sm btn-outline-warning" data-tt="tooltip"
                                style="font-size: 1rem; color:#303030" data-placement="top" title="Editar">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <a href="" class="btn btn-sm btn-outline-primary" data-tt="tooltip"
                                style="font-size: 1rem; color:#303030" data-placement="top" title="Enviar">
                                <i class="bi bi-cart-check"></i>
                            </a>
                            {{-- @endif --}}
                            {{-- @if (isset($aquisicaos->aut_usu_pres, $aquisicaos->aut_usu_adm, $aquisicaos->aut_usu_daf)) --}}
                            <a href="" class="btn btn-sm btn-outline-info" data-tt="tooltip"
                                style="font-size: 1rem; color:#303030" data-placement="top" title="Anexar">
                                <i class="bi bi-hand-thumbs-up"></i>
                            </a>
                            {{-- @endif --}}
                            {{-- @if ($aquisicaos->tipoStatus->id == '1') --}}
                            <a href="#" class="btn btn-sm btn-outline-danger excluirSolicitacao" data-tt="tooltip"
                                style="font-size: 1rem; color:#303030" data-placement="top" title="Excluir"
                                data-bs-toggle="modal" data-bs-target="#modalExcluirSolicitacao" data-id="">
                                <i class="bi bi-trash"></i>
                            </a>
                            {{-- @endif --}}
                        </td>
                    </tr>
                @endforeach
            </tbody>
            {{-- Fim body da tabela --}}
        </table>
    </div>
    </div>


    <form action="{{ route('index.estExt') }}" class="form-horizontal mt-4" method="GET">
        <div class="modal fade" id="filtros" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header" style="background-color:grey;color:white">
                        <h1 class="modal-title fs-5" id="exampleModalLabel">Filtrar Opções</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <center>
                            <div class="row col-10">
                                <div class="col-12 mb-3">Nome Fantasia
                                    <select class="form-select" name="nome_fantasia">
                                        <option value="">Selecione uma opção</option>
                                        @foreach ($pesquisa as $item)
                                            <option value="{{ $item->nome_fantasia }}"
                                                {{ request('nome_fantasia') == $item->nome_fantasia ? 'selected' : '' }}>
                                                {{ $item->nome_fantasia }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-12 mb-3">Razão Social
                                    <select class="form-select" name="razao_social">
                                        <option value="">Selecione uma opção</option>
                                        @foreach ($pesquisa as $item)
                                            <option value="{{ $item->razao_social }}"
                                                {{ request('razao_social') == $item->razao_social ? 'selected' : '' }}>
                                                {{ $item->razao_social }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-12 mb-3">Email
                                    <select class="form-select" name="email_contato">
                                        <option value="">Selecione uma opção</option>
                                        @foreach ($pesquisa as $item)
                                            <option value="{{ $item->email_contato }}"
                                                {{ request('email_contato') == $item->email_contato ? 'selected' : '' }}>
                                                {{ $item->email_contato }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-12 mb-3">CNPJ
                                    <input class="form-control" type="number" maxlength="14"
                                        placeholder="Insira apenas números" name="cnpj"
                                        value="{{ request('cnpj') }}">
                                </div>

                                <div class="col-12 mb-3">Site
                                    <select class="form-select" name="site">
                                        <option value="">Selecione uma opção</option>
                                        @foreach ($pesquisa as $item)
                                            <option value="{{ $item->site }}"
                                                {{ request('site') == $item->site ? 'selected' : '' }}>
                                                {{ $item->site }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-12 mb-3">Status
                                    <select class="form-select" name="status">
                                        <option value="">Selecione uma opção</option>
                                        @foreach ($pesquisa->unique('status') as $item)
                                            <option value="{{ $item->status }}"
                                                {{ request('status') == $item->status ? 'selected' : '' }}>
                                                {{ $item->status }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </center>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancelar</button>
                        <a class="btn btn-secondary" href="/gerenciar-encaminhamentos">Limpar</a>
                        <button type="submit" class="btn btn-primary">Confirmar</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection

@section('footerScript')
    </script>
@endsection
