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
				<a class="font-size-02"> Debenhams<br/>MTS Unlisted Report<br/></a>
				Printed By: {{Auth::user()->username}} <br>
				Print Date: {{ date('m/d/y h:i A')}}
			</div>
	<table class="table table-striped table-bordered">
		<thead>
			<tr>
				<th> MTS Number</th>
				<th> SKU</th>
				<th> UPC</th>
				<th> Short Name</th>
				<th> Received Quantity</th>
				<th> Store name </th>

				<th> Piler Receiver</th>
				<th> Received Date</th>
				 
			</tr>
		</thead>
		@if( !CommonHelper::arrayHasValue($results) )
		<tr class="font-size-13">
			<td colspan="7" class="align-center">{{ $text_empty_results }}</td>
		</tr>
		@else
			@foreach( $results as $po )
			<tr class="font-size-13"
		 
			>
				 <TD>{{$po->so_no}}</TD>
				 <TD>{{$po->sku}}</TD>
				 
				 <TD>{{$po->upc}}</TD>
			 
				 <TD>{{$po->description}}</TD>

				 <TD>{{$po->received_qty}}</TD>
				  <TD>{{$po->store_name}}</TD>


				  <TD>{{$po->firstname.' '.$po->lastname}}</TD>
				  <TD>{{$po->created_at}}</TD>
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