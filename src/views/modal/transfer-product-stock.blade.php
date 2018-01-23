@extends('master::layouts/child-admin')

@section('content')
  <h3>Transferir Producto de Stock</h3><h4>Producto: {{ $product->name.' ('.$product->barcode.')' }}</h4>
  <h4>Cantidad en Stock: {{ $product_stock->quantity }}</h4>
  {!! Form::open(['url'=>'admin/transfer-product-stock', 'method'=>'POST', 'class'=>'form-horizontal filter']) !!}
    <div class="row flex">
      {!! Field::form_input(0, 'edit', ['name'=>'place_id','type'=>'select','required'=>true, 'options'=>$places], ['cols'=>12,'label'=>'Seleccionar sucursal donde realizar transferencia']) !!}
      {!! Field::form_input(0, 'edit', ['name'=>'product_stock_id','type'=>'hidden','required'=>true], ['value'=>$product_stock->id]) !!}
    </div>

    {!! Form::submit('Transferir Stock de Producto', array('class'=>'btn btn-site')) !!}
  {!! Form::close() !!}
@endsection
@section('script')
  @include('master::scripts.child-ajax-js')
@endsection