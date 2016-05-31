<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title></title>
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
<meta name="apple-mobile-web-app-capable" content="yes">
{{ HTML::style('resources/css/bootstrap.min.css') }}
{{ HTML::style('resources/css/bootstrap-responsive.min.css') }}
{{ HTML::style('resources/css/style.css') }}
</head>
<body>
<div class="table-responsive">
			<div style="text-align: center">
				<a class="font-size-02"> RSCI - eWMS<br/>P.O. Unlisted Report<br/></a>
				Printed By: {{Auth::user()->username}} <br>
				Print Date: {{ date('m/d/y h:i A')}}
			</div>
	<table class="table table-striped table-bordered">
		<thead>
			<tr>
				<th> P.0. no</th>
				<th> SKU</th>
				<th> UPC</th>
				<th> Short Name</th>
				<th> Received Quantity</th>
				
				<th> Piler Receiver</th>
				<th> Received Date</th>
				 
			</tr>
		</thead>
		@if( !CommonHelper::arrayHasValue($results) )
		<tr class="font-size-13">
			<td colspan="7" class="align-center">No Found Result</td>
		</tr>
		@else
			@foreach( $results as $po )
			<tr class="font-size-13"
		 
			>
				 
				<td>{{$po->purchase_order_no}} </td>
				<td> {{$po->sku}}</td>
				<td>{{$po->upc}} </td>
				<td> {{$po->description}}
				<td> {{$po->quantity_delivered}}</td>

				<td> {{$po->firstname.' '.$po->lastname}}</td>
				<td>{{$po->created_at}} </td>
			 
			</tr>
			@endforeach
		@endif
		<tr>
			<td>Total item:{{count($results) }} </td>
		</tr>
	</table>
</div>

</body>
</html>