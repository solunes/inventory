<!doctype html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <style>
      .block { display: inline-block; margin-left: 1%; margin-right: 1%; width: auto; margin-bottom: 30px; text-align: center; width: 30%; }
      .block img { margin-bottom: 10px; }
    </style>
</head>
<body style="margin:0; padding:0;">
  	@foreach($products as $product_id => $product)
  		<div class="block">
	  		{!! $product['image'] !!}<br>
	  		{{ $product['name'] }}
	  	</div>
  	@endforeach
  <br>
</body>
</html>