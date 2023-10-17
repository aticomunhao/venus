@extends('layouts.app')

@section('title', 'Request Teste')

@if (session('msg'))
   <p class="msg">{{ session('msg') }}</p>
@endif

@section('content')
