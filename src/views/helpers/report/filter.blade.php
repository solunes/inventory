@if(!request()->has('download-pdf'))
	<h3><a href="{{ $url }}" target="_blank">Exportar reporte a PDF</a></h3>
	{!! Form::open(['url'=>'admin/'.$path, 'method'=>'get']) !!}
	<div class="row">
	  	{!! Field::form_input($i, $dt, ['name'=>'period', 'required'=>false, 'type'=>'radio', 'options'=>$periods], ['label'=>'Seleccionar Periodo', 'cols'=>6]) !!}
	</div>
	<div id="custom-date" class="row">
		{!! Field::form_input($i, $dt, ['name'=>'initial_date', 'required'=>false, 'type'=>'date'], ['label'=>'Fecha Inicial', 'cols'=>6, 'class'=>'datepicker-initial']) !!}
		{!! Field::form_input($i, $dt, ['name'=>'end_date', 'required'=>false, 'type'=>'date'], ['label'=>'Fecha Final', 'cols'=>6, 'class'=>'datepicker-end']) !!}
	</div>
	<div class="row">
	  	{!! Field::form_input($i, $dt, ['name'=>'currency_id', 'required'=>true, 'type'=>'select', 'options'=>$currencies], ['value'=>$currency->id, 'label'=>'Seleccionar Moneda del Informe', 'cols'=>6]) !!}
	  	@if($show_place)
	  		{!! Field::form_input($i, $dt, ['name'=>'place_id', 'required'=>true, 'type'=>'select', 'options'=>$places], ['value'=>$place, 'label'=>'Seleccionar Sucursal', 'cols'=>6]) !!}
	  	@endif
	  	@if($show_account_id)
	  		{!! Field::form_input($i, $dt, ['name'=>'account_id', 'required'=>true, 'type'=>'select', 'options'=>$accounts], ['label'=>'Seleccionar Cuenta', 'cols'=>6]) !!}
	  	@endif
	</div>
	{!! Form::submit('Filtrar', array('class'=>'btn btn-site')) !!}

	{!! Form::close() !!}
@endif
<h4>Moneda del Informe: {{ $currency->name }}</h4>
<h4>Rango de Fechas: {{ $initial_date.' - '.$end_date }}</h4>
<h4>Operador: {{ auth()->user()->name }}</h4>
@if($show_place)
  <h4>Sucursal: {{ $place_name }}</h4>
@endif