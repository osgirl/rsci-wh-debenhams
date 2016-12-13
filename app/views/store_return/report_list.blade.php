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
				<a class="font-size-02"> RSCI - eWMS<br/>SUBLOC TRANSFER MTS RECEIVING SHORTAGE/OVERAGE REPORT<br/></a>
				Printed By: {{Auth::user()->username}} <br>
				Print Date: {{ date('m/d/y h:i A')}}
			</div>
	<table class="table table-striped table-bordered">
		<thead>
			<tr>
				<th>{{ $col_so_no }}</th>
				<th> SKU</th>
				<th>UPC</th>
				<th>DESCRIPTION</th>
				<th>{{ $col_store_name }}</th>
				<th>{{ $col_order_date }}</th>
				<th>{{ $col_receiving_stock_piler }}</th>
				<th>VARIANCE</th>
			</tr>
		</thead>
		@if( !CommonHelper::arrayHasValue($results) )
		<tr class="font-size-13">
			<td colspan="9" style="text-align: center;">{{ $text_empty_results }}</td>
		</tr>
		@else
			@foreach( $results as $so )
			<tr class="font-size-13 tblrow" data-id="{{ $so->so_no }}">
				<td>{{ $so->so_no }}</td>
				<td>{{ $so->sku }}</td>
				
				<td>{{ $so->upc }}</td>
				<td>{{ $so->short_name }}</td>
				<td>{{ $so->store_name }}</td>
				<td>{{date("M d, Y", strtotime($so->created_at))}}  </td>
				<td>{{$so->firstname.' '.$so->lastname}}</td>
				<td>{{$so->variance}}</td>
			</tr>
			@endforeach
		@endif
		<tr>
			<td>total item:{{count($results) }} </td>
		</tr>
	</table>
</div>

</body>
</html>