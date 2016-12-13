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
				<a class="font-size-02"> RSCI<br/>Picking Reports<br/></a>
				Printed By: {{Auth::user()->username}} <br>
				Print Date: {{ date('m/d/y h:i A')}}
			</div>
	<table class="table table-striped table-bordered">
		<thead>
			<tr>
				<th>TL no. </th>
						<th> Store </th>
						<th>SKU</th>
						<th>UPC</th>
						<th>Short Name</th>
						<th>F Slot Code</th>
						<th>Qty Ord</th>
					 	<th>Piler Name</th>
					 	<th>Date Entry</th>
						<th>Var</th>
			</tr>
		</thead>
		@if( !CommonHelper::arrayHasValue($results) )
			<tr class="font-size-13">
				<td colspan="10" style="text-align: center;">{{ $text_empty_results }}</td>
			</tr>
		@else
			@foreach( $results as $value )
				<tr class="font-size-13 tblrow" data-id="{{ $value->move_doc_number }}">
					 
		 				<td>{{$value['move_doc_number']}}</td>
		 				<td>{{$value['store_name']}}</td>
		 				<td>{{$value['sku']}}</td>
		 				<td>{{$value['upc']}}</td>
		 				<td>{{$value['description']}}</td>
		 				<td>{{$value['from_slot_code']}}</td>
		 				<td>{{$value['quantity_to_pick']}}</td>
		 				<td>{{$value['firstname']. ' '.$value['lastname']}}</td>
		 				<td>{{$value['updated_at']}}</td>
		 				<td>{{$value['moved_qty'] - $value['quantity_to_pick']}}</td>
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