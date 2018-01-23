@extends('master::layouts/admin')

@section('content')
  <h1>Reporte de Ventas Resumido</h1>

  @include('inventory::helpers.report.filter')
  
  <h3>Detalle de Ventas</h3>
  <table class="table table-striped table-bordered table-hover dt-responsive">
    <thead>
      <tr class="title">
        <td>Concepto</td>
        <td>Monto</td>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td>Efectivo </td>
        <td>{{ number_format($cash, 2, '.', '').' '.$currency->name }}</td>
      </tr>
      <tr>
        <td>POS </td>
        <td>{{ number_format($pos, 2, '.', '').' '.$currency->name }}</td>
      </tr>
      <tr style="font-weight: bold;">
        <td>En Tienda</td>
        <td>{{ number_format($inventory, 2, '.', '').' '.$currency->name }}</td>
      </tr>
      <tr style="font-weight: bold;">
        <td>Web </td>
        <td>{{ number_format($web, 2, '.', '').' '.$currency->name }}</td>
      </tr>
      <tr style="font-weight: bold;">
        <td>Online </td>
        <td>{{ number_format($online, 2, '.', '').' '.$currency->name }}</td>
      </tr>
      <tr style="font-weight: bold;">
        <td>Devolución de Mercaderia </td>
        <td>{{ number_format($refund_total, 2, '.', '').' '.$currency->name }}</td>
      </tr>
      <tr class="title">
        <td>VENTAS TOTALES COBRADAS / DEVUELTAS</td>
        <td>{{ number_format($total + $refund_total, 2, '.', '').' '.$currency->name }}</td>
      </tr>
      <tr style="font-weight: bold;">
        <td>Crédito de Ventas Otorgado (CxC)</td>
        <td>{{ number_format($pending, 2, '.', '').' '.$currency->name }}</td>
      </tr>
      <tr class="title">
        <td>VENTAS TOTALES</td>
        <td>{{ number_format($total + $refund_total + $pending, 2, '.', '').' '.$currency->name }}</td>
      </tr>
    </tbody>
  </table>
  <div class="row">
    <div class="col-sm-12">
      <div id="list-graph-type"></div>
    </div>
  </div>
@endsection
@section('script')
  @include('inventory::helpers.report.datepicker')
  <script src="https://code.highcharts.com/highcharts.js"></script>
  <script src="http://code.highcharts.com/modules/exporting.js"></script>
  @foreach($graphs as $graph_name => $graph)
    @include('master::scripts.graph-'.$graph["type"].'-js', ['graph_name'=>$graph_name, 'column'=>$graph["name"], 'label'=>$graph["label"], 'graph_items'=>$graph["items"], 'graph_subitems'=>$graph["subitems"], 'graph_field_names'=>$graph["field_names"]])
  @endforeach
@endsection