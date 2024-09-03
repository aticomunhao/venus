@extends('layouts.app')
@section('title')
    Relatório Presença AFI
@endsection
@section('content')
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js'></script>
<script src="{{ asset('js/bootstrap5/index.global.min.js') }}"></script>
<script>
    
    document.addEventListener('DOMContentLoaded', function() {
      var calendarEl = document.getElementById('calendar');
      var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        themeSystem: 'bootstrap5',
        initialDate: '2024-09-03',
        timeZone: 'local',
        slotDuration: '02:00'
      });
      calendar.render();
    });

  </script>
   <div id='calendar'></div>
@endsection


