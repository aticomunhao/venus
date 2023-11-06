
<div class="modal fade" id="pessoa" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fs-5" id="">Cadastrar Pessoa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <form class="form-horizontal mt-4 needs-validation" novalidate method="POST" action="/criar-pessoa" >
                @csrf
                <div class="row">
                    <div class="col-12" style="text-align:left;">
                    <label for="validationCustom01" class="form-label">Nombre</label>
                        <input class="form-control" type="text" maxlength="45" oninput="this.value = this.value.replace(/[0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" id="" name="nome" value="{{old('nome')}}" required>
                        <div class="valid-feedback">
      ¡Se ve bien!
    </div>
                    </div>
                    <br>
                </div><br>
                <div class="row">
                    <div class="col" style="text-align:left;">CPF
                        <input class="form-control" type="numeric" maxlength="11" placeholder="888.888.888-88"  oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" value="{{old('cpf')}}" id="" name="cpf" required >
                    </div>
                    <div class="col" style="text-align:left;">Data nascimento
                        <input class="form-control" type="date" id="" name="dt_na" value="{{old('dt_na')}}" required >
                    </div>
                </div><br>
                <div class="row">
                    <div class="col" style="text-align:left;">Sexo
                        <select class="form-select" id="" name="sex" required>
                            <option value=""></option>
                            <@foreach($sexo as $sexos)
                            <option @if (old ('sex') == $sexos->id) {{'selected="selected"'}} @endif value="{{ $sexos->id }}">{{$sexos->tipo}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-2" style="text-align:left;">DDD
                    <select class="form-select" id="" name="ddd" required>
                                    <option value=""></option>
                                    <@foreach($ddd as $ddds)
                                    <option @if(old ('ddd') == $ddds->id) {{'selected="selected"'}} @endif value="{{ $ddds->id }}">{{$ddds->descricao}}</option>
                                    @endforeach
                                </select>
                    </div>
                    <div class="col" style="text-align:left;">Nr Celular
                        <input class="form-control" maxlength="9" type="numeric" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" placeholder="Ex.: 99999-9999" value="{{old('celular')}}" id="" name="celular" required >
                    </div>
                </div><br>
                <div class="row">
                    <div class="col" style="text-align:left;">email
                        <input class="form-control" type="email" maxlength="45" id="" name="email" value="{{old('email')}}" required >
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <div class="row">
                    <div class="col" style="text-align: left;">
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancelar</button>
                    </div>
                    <div class="col" style="text-align: left;">
                        <button type="submit" class="btn" style="background-color:#007bff; color:#fff;" data-bs-dismiss="modal">Confirmar</button>
                    </div>
                </form>
                </div>
            </div>
        </div>
    </div>
</div>