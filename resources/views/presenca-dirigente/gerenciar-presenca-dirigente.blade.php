@extends('layouts.app')

@section('title')
Gerenciar Presença Dirigente
@endsection

@section('content')
<div class="container-fluid">
    <h4 class="card-title" style="font-size:20px; text-align: left; color: gray; font-family:calibri">GERENCIAR PRESENÇA DIRIGENTE</h4>

    <form action="" class="form-horizontal mt-4" method="GET">
        <div class="row justify-content-center" style="display: flex; align-items:flex-end">
            <div class="col-12">
                Grupo
                <select class="form-select select2" name="grupo">
                    @foreach ($reunioes as $reuniao)
                        <option value="{{ $reuniao->id }}" {{ $reuniao->id == $reunioesDirigentes[0] ? 'selected' : '' }}>
                            {{ $reuniao->nome . ' - ' . $reuniao->dia }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-12">
                Nome
                <select class="form-select select2" name="nome_setor">
                    <!-- Opções serão adicionadas dinamicamente -->
                </select>
            </div>
            <div class="col"><br />
                <input class="btn btn-light btn-sm me-md-2 col-6 col-12" style="font-size: 0.9rem; box-shadow: 1px 2px 5px #000000; margin:5px;" type="submit" value="Pesquisar">
            </div>
            <div class="col"><br />
                <a href="/gerenciar-presenca-dirigente">
                    <input class="btn btn-light btn-sm me-md-2 col-12" style="font-size: 0.9rem; box-shadow: 1px 2px 5px #000000; margin:5px;" type="button" value="Limpar">
                </a>
            </div>
        </div>
    </form>
    <hr />

    <div class="col">
        <table class="table table-sm table-striped table-bordered border-secondary table-hover align-middle">
            <thead style="text-align: center;">
                <tr style="background-color: #d6e3ff; font-size:14px; color:#000000">
                    <th class="col">NOME</th>
                    <th class="col">FUNÇÃO</th>
                    <th class="col">AÇÕES</th>
                </tr>
            </thead>
            <tbody style="font-size: 14px; color:#000000; text-align:center;">
                @foreach ($membros as $membro)
                <tr>
                    <td>{{ $membro->nome_completo }}</td>
                    <td>{{ $membro->nome }}</td>
                    <td>
                        <!-- Formulário de Marcar Presença -->
                        <form action="{{ route('marcar.presenca') }}" method="POST" style="display: inline;">
                            @csrf
                            <input type="hidden" name="membro_id" value="{{ $membro->id }}">
                            <button type="submit" class="btn btn-success marcar" id="marcar-{{ $membro->id }}">
                                Presença
                            </button>
                        </form>

                        <!-- Formulário de Cancelar Presença -->
                        <form action="{{ route('cancelar.presenca') }}" method="POST" style="display: inline;">
                            @csrf
                            <input type="hidden" name="membro_id" value="{{ $membro->id }}">
                            <button type="submit" class="btn btn-danger cancelar" id="cancelar-{{ $membro->id }}" style="display:none;">
                                Cancelar
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <script>
        document.querySelectorAll('.marcar').forEach(function (button) {
            button.addEventListener('click', function () {
                let id = this.id.split('-')[1]; // Pegando o ID do membro
                let cancelarButton = document.getElementById('cancelar-' + id);
                let marcarButton = document.getElementById('marcar-' + id);

                // Enviar requisição para marcar presença
                fetch('{{ route("marcar.presenca") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: new URLSearchParams({
                        'membro_id': id
                    })
                }).then(response => response.json()).then(data => {
                    if (data.success) {
                        marcarButton.style.display = 'none';
                        cancelarButton.style.display = 'inline';
                    }
                });
            });
        });

        document.querySelectorAll('.cancelar').forEach(function (button) {
            button.addEventListener('click', function () {
                let id = this.id.split('-')[1]; // Pegando o ID do membro
                let cancelarButton = document.getElementById('cancelar-' + id);
                let marcarButton = document.getElementById('marcar-' + id);

                // Enviar requisição para cancelar presença
                fetch('{{ route("cancelar.presenca") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: new URLSearchParams({
                        'membro_id': id
                    })
                }).then(response => response.json()).then(data => {
                    if (data.success) {
                        cancelarButton.style.display = 'none';
                        marcarButton.style.display = 'inline';
                    }
                });
            });
        });
    </script>
@endsection
