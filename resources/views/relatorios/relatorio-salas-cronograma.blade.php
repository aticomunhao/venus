@extends('layouts.app')
@section('title')
    Relatório Presença AFI
@endsection
@section('content')

<br />
    <div class="container">
        <div class="card">
            <div class="card-header">
                Relatório de Salas por Dia
            </div>
            <div class="card-body">
                <div id='calendar'></div>
            </div>
          </div>
    </div>


   
  

    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Modal title</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary">Save changes</button>
                </div>
            </div>
        </div>
    </div>



    <script>

        document.addEventListener('DOMContentLoaded', function() {
           
            event = []
          
            event = @JSON($eventosCronogramas);
        
            var calendarEl = document.getElementById('calendar');

            var calendar = new FullCalendar.Calendar(calendarEl, {
            
                locale: 'br',
                timeZone: 'BRT',
                themeSystem: 'bootstrap5',
                aspectRatio: 1.7,
                events: event,
                selectable: true,
                "displayEventEnd": true,
                eventClick: function(info) {
                   
                    const myModal = new bootstrap.Modal('#exampleModal', show);
                }
            });

            calendar.render();
        });
    </script>
@endsection
