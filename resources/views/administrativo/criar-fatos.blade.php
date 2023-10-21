@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="col-3"
              <div class="card-body">
                <br>
                <h5 class="card-title">INCLUIR</h5>
                
                <div class="row">
                  <div class="col-12">
                    <form class="form-horizontal mt-4" method="post" action="/incluir-fatos">
                      @csrf
                      <div class="mb-3">
                        <label for="fatos" class="form-label"></label>
                        <input type="text" class="form-control" id="fatos" aria-describedby="" name = "fato">
                      </div>
                    </div>
                </div>
                  <button type="submit"class="btn btn-primary">Confirmar</button>
                </form>
              </div>
            </div>
        </div>
    </div>
@endsection


