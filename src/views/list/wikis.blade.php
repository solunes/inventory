@extends('master::layouts/admin')

@section('content')
  <h1>Wikis</h1>

  <table class="table">
    <thead>
      <tr class="title">
        <td>ASd</td>
        <td>ASd</td>
        <td>ASd</td>
      </tr>
    </thead>
    <tbody>
      @foreach($items as $item)
        <tr>
          <td>{{ $item->id }}</td>
          <td>{{ $item->name }}</td>
          <td class="edit"><a href="{{ url('admin/wiki/'.$item->id) }}">Editar</a></td>
        </tr>
      @endforeach
    </tbody>
  </table>

@endsection