@extends('master::layouts/admin')

@section('content')
  <h2>Reporte de Stock al {{ date('d/m/Y') }}</h2>
  <p>Esta tabla contiene el reporte de stock a la fecha.</p>
  {!! Form::open(['id'=>'profile', 'name'=>'login', 'url'=>request()->url(), 'method'=>'get']) !!}
    <div class="row">
      {!! Field::form_input(NULL, 'create', ['name'=>'agencies', 'type'=>'checkbox', 'required'=>0, 'options'=>$agencies_list], ['label'=>'Filtro de Agencias', 'cols'=>12]) !!}
      {!! Field::form_input(NULL, 'create', ['name'=>'categories', 'type'=>'checkbox', 'required'=>0, 'options'=>$categories_list], ['label'=>'Filtro de Productos', 'cols'=>12]) !!}
      {!! Field::form_input(NULL, 'create', ['name'=>'variation_options', 'type'=>'checkbox', 'required'=>0, 'options'=>$variation_options_list], ['label'=>'Filtro de Variaciones', 'cols'=>12]) !!}
    </div>
    <input type="hidden" name="filter" value="filter">
    <input type="submit" value="Filtrar" class="btn btn-site-black btn-full">
  {!! Form::close() !!}

  @if(count($graph_items)>0)
  <div id="list-graph-grafico" style="min-height: 400px;"></div>
  @endif
  
  <table class="admin-table editable-list table table-striped table-bordered table-hover @if(config('solunes.list_horizontal_scroll')=='true') nowrap @else dt-responsive @endif">
    <thead>
      <tr class="title">
        <td>#</td>
        <td>Agencia</td>
        @foreach($products as $product)
          <td>{{ $product->name }}</td>
        @endforeach
      </tr>
    </thead>
    <tbody>
      @foreach ($agencies as $subkey => $agency)
        <tr>
          <td>{{ $subkey+1 }}</td>
          <td>{{ $agency->name }}</td>
          @foreach($products as $product)
            <td>{{ $stock[$agency->id.'-'.$product->id] }}</td>
          @endforeach
        </tr>
      @endforeach
    </tbody>
  </table>
@endsection

@section('script')
  <script src="https://code.highcharts.com/highcharts.js"></script>
  <script src="http://code.highcharts.com/modules/exporting.js"></script>
  @include('master::scripts.graph-bar-js', ['graph_name'=>'grafico','label'=>'Agencia - Producto','graph_items'=>$graph_items])
@endsection