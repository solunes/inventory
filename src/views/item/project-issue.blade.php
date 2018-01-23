@extends('master::layouts/admin')

@section('content')
  <h1>Observación de Proyecto: {{ $item->name }}</h1>
  <h2>Proyecto: {{ $item->inventory->name }}</h2>
  <p>Detalle de tarea de proyecto y actualizaciones más abajo.</p>
@endsection