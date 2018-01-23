@extends('master::layouts/admin')

@section('content')
  <h1>Wiki: {{ $item->name }}</h1>
  {!! $item->content !!}
  <p>Botón para editar</p>
@endsection