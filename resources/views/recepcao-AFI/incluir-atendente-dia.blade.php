@extends('layouts.app')

@section('title') Definir AFI @endsection

@section('content')


<div class="container-fluid";>
<h4 class="card-title" class="card-title" style="font-size:20px; text-align: left; color: gray; font-family:calibri">DEFINIR SALA DE TRABALHO</h4>
    <div class="col-12">
        <div class="row justify-content-center">
            <div>
                <form action="{{route('atesal')}}" class="form-horizontal mt-4" method="GET" >
                <div class="row">
                    <div class="col-2">Grupo
                        <select class="form-select" id="" name="grupo" type="number">                           
                            <option value=""></option>
                            @foreach ($grupo as $grupos)
                            <option @if(old('grupo')==$grupos->id) {{'selected="selected"'}} @endif value="{{ $grupos->id }}">{{$grupos->nome}}</option>
                            @endforeach               
                        </select>                       
                    </div>
                    <div class="col-2">Atendente
                        <select class="form-select" id="" name="atendente" type="number">                           
                            <option value=""></option>
                            @foreach ($atende as $atendes)
                            <option @if(old('atendente')==$atendes->idat) {{'selected="selected"'}} @endif value="{{ $atendes->idat}}">{{$atendes->nm_4}}</option>
                            @endforeach               
                        </select>                       
                    </div>
                    <div class="col-2">Status
                        <select class="form-select" id="" name="status" type="number">                           
                            <option value="">Todos</option>
                            @foreach ($situacao as $sit)
                            <option @if(old('status')==$sit->id) {{'selected="selected"'}} @endif value="{{ $sit->id }}">{{$sit->tipo}}</option>
                            @endforeach               
                        </select>                       
                    </div>
                        <div class="col"><br>
                            <input class="btn btn-light btn-sm me-md-2" style="box-shadow: 1px 2px 5px #000000; margin:5px;" type="submit" value="Pesquisar">
                            <a href="/definir-sala-atendente"><input class="btn btn-light btn-sm me-md-2" style="box-shadow: 1px 2px 5px #000000; margin:5px;" type="button" value="Limpar"></a>
                    </form>
                        </div>
                </div>
                <br>
            </div style="text-align:right;">
            <hr>
            <form action="/gravar-escolha{{$atende[0]->ida}}" class="form-horizontal mt-4" method="POST" >
            <div class="col" style="text-align: center;"><input class="btn btn-success btn-sm me-md-2" style="box-shadow: 1px 2px 5px #000000; margin:5px;" type="submit" value="Incluir selecionados"></div>
            
            <div class="table">Total selecionados: 
                <table class="table table-sm table-striped table-bordered border-secondary table-hover align-middle">
                    <thead style="text-align: center;">
                        <tr style="background-color: #d6e3ff; font-size:14px; color:#000000">
                            <th class="col"><input class="form-check-input" type="checkbox"  value="" id="flexCheckDefault"></th>
                            <th class="col">NR</th>
                            <th class="col">GRUPO</th>                                               
                            <th class="col">ATENDENTE</th>
                            <th class="col">SALA</th>                           
                            <th class="col">STATUS</th>
                            <th class="col">AÇÕES</th>
                        </tr>
                    </thead>
                    <tbody style="font-size: 14px; color:#000000; text-align: center;">
                        <tr>
                        @foreach($atende as $atendes)
                            <td scope=""><input class="form-check-input" type="checkbox" value="" id="flexCheckDefault"></td>
                            <td scope="">{{$atendes->ida}}</td>
                            <td scope="">{{$atendes->nomeg}}</td>                                                      
                            <td scope="">{{$atendes->nm_4}}</td>                            
                            <td scope=""><select class="form-select text-center" id="" name="sala" type="number">
                            @foreach ($sala as $salas)
                            <option @if(old('sala')==$salas->id) {{'selected="selected"'}} @endif value="{{ $salas->id }}">{{$salas->numero}}</option>
                            @endforeach               
                        </select>     </td>
                            <td scope="">{{$atendes->tipo}}</td>
                            <td scope="">                                
                                <a href="/gravar-escolha/{{$atendes->ida}}"><button type="button" class="btn btn-outline-success btn-sm">Incluir</button></a>                                
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                </form>
            </div class="d-flex justify-content-center">
            {{$atende->withQueryString()->links()}}
        </div>
    </div>
</div>

<script>
  const masterCheckBox = document.querySelector('th input');
 const checkBoxes = Array.from(document.querySelectorAll('td input'));

// Marca ou desmarca todas as checkboxes de acordo com a checkbox master
masterCheckBox.onchange = (event) => checkBoxes.forEach(e => {
  e.checked = event.target.checked;
  changeBackground(e);
});

// Marca masterCheckBox se todas as outras estiverem marcadas
// Desmarca se pelo menos uma estiver desmarcada
checkBoxes.forEach(e => e.onchange = (event) => {
  masterCheckBox.checked = checkBoxes.every(f => f.checked);
  changeBackground(e);
});

// Destaca background das linhas selecionadas
function changeBackground(input) {
  const tableRow = input.parentElement.parentElement;

  if (input.checked) tableRow.style.background = '#aaa';
  else tableRow.style.background = '';
}

</script>

@endsection

@section('footerScript')  


@endsection
