@extends('layouts.app')

@section('content')


  <div class="col-md-4">
    <div class="form-group">
      <label for="nome">Nome:</label>
      <input type="text" class="form-control" id="nome" name="nome" required>
      
    </div>
  </div>
  <div class="col-md-4">
    <div class="form-group">
      <label for="numero">Número:</label>
      <input type="text" class="form-control" id="numero" name="numero" required>
      
    </div>
  </div>
  <div class="col-md-4">
    <div class="form-group">
      <label for="nr_lugares">Nr_lugares:</label>
      <input type="text" class="form-control" id="nr_lugares" name="nr_lugares" required pattern="[0-9]+">
      
    </div>
    <div class="form-group">
      <label for="localização">Localização:</label>
      <input type="text" class="form-control" id="nr_lugares" name="nr_lugares" required pattern="[0-9]+">
      
    </div>
    <div class="form-group">
      <label for="Projetor">Projetor:</label>
      <input type="text" class="form-control" id="nr_lugares" name="nr_lugares" required pattern="[0-9]+">
     
    </div>
    <div class="form-group">
      <label for="Pc">Pc:</label>
      <input type="text" class="form-control" id="nr_lugares" name="nr_lugares" required pattern="[0-9]+">
     
    </div>
    <div class="form-group">
      <label for="Quadro">Quadro:</label>
      <input type="text" class="form-control" id="nr_lugares" name="nr_lugares" required pattern="[0-9]+">
      <button class="btn btn-primary" >Confirmar</a>
    </div>
  </div>
</div>
    {{-- <div class="container-fluid">
      <div class="card-body">
                <br>
                <h5 class="card-title"></h5>
          <div class="row">
                                <form class="form-horizontal mt-4" method="post" action="/incluir-salas">
                      @csrf
              <div class="col-2"> Nome:
                  <input type="text" class="form-control" id="sala" aria-describedby="" name = "sala">
                  <button type="submit"class="btn btn-primary">Confirmar</button>
              </div> 
              <div class="col-3"> Número:
                <input type="text" class="form-control" id="sala" aria-describedby="" name = "sala">
                <button type="submit"class="btn btn-primary">Confirmar</button>
              </div>
            <div class="col-4"> Nr_lugares:
              <input type="text" class="form-control" id="sala" aria-describedby="" name = "sala">
              <button type="submit"class="btn btn-primary">Confirmar</button>
          </div> 
          <div class="col-5"> Localização:
            <input type="text" class="form-control" id="sala" aria-describedby="" name = "sala">
            <button type="submit"class="btn btn-primary">Confirmar</button>
          </div>
          <div class="col-6"> Projetor:
            <input type="text" class="form-control" id="sala" aria-describedby="" name = "sala">
            <button type="submit"class="btn btn-primary">Confirmar</button>
          </div>
          <div class="col-7"> PC:
            <input type="text" class="form-control" id="sala" aria-describedby="" name = "sala">
            <button type="submit"class="btn btn-primary">Confirmar</button>
          </div>
          <div class="col-8"> Quadro:
            <input type="text" class="form-control" id="sala" aria-describedby="" name = "sala">
            <button type="submit"class="btn btn-primary">Confirmar</button>
          </div>
          <div class="col-9"> Ações:
            <input type="text" class="form-control" id="sala" aria-describedby="" name = "sala">
          </div>
          </div>
                  <button type="submit"class="btn btn-primary">Confirmar</button>
                </form>
      </div>
    </div> --}}
 
    
@endsection
