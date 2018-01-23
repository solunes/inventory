@extends('master::layouts/admin')

@section('content')
  <h1>Tarea de Proyecto: {{ $item->name }}</h1>
  <h2>Proyecto: {{ $item->inventory->name }}</h2>
  <p>Detalle de tarea de proyecto y actualizaciones más abajo.</p>
  <p>Botón de iniciar, pausear, concluyes.</p>
@endsection