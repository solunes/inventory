@extends('master::layouts/admin')

@section('content')
  <h1>Proyecto: {{ $item->name }}</h1>
  Menu de proyecto con 4 tabs
  @include('includes.inventory-'.$tab)
@endsection