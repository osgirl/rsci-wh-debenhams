<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>{{ $col_po_no }}</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
<meta name="apple-mobile-web-app-capable" content="yes">
{{ HTML::style('resources/css/bootstrap.min.css') }}
{{ HTML::style('resources/css/bootstrap-responsive.min.css') }}
{{ HTML::style('resources/css/style.css') }}
</head>
<body>
<div class="table-responsive">
			<div style="text-align: center">
				<h1>Casual Clothing Retailers Inc.<br/>PURCHASE ORDER BACK ORDER REPORT</h1>
				Print Date: {{ date('m/d/y h:i A')}}
			</div>
	<table class="table table-striped table-bordered">
		<thead>
			<tr>
				<th>{{ $col_back_order }}</th>
				<th>{{ $col_carton_id }}</th>
				<th>{{ $col_po_no }}</a></th>
				<th>{{ $col_receiver_no }}</th>
				<th>{{ $col_total_qty }}</th>
				<th>{{ $col_receiving_stock_piler }}</th>
				<th>{{ $col_entry_date }}</th>
				<th>{{ $col_status }}</th>
			</tr>
		</thead>
		@if( !CommonHelper::arrayHasValue($results) )
		<tr class="font-size-13">
			<td colspan="11" style="text-align: center;">{{ $text_empty_results }}</td>
		</tr>
		@else
			@foreach( $results as $po )
				<tr class="font-size-13 tblrow" data-id="{{ $po->purchase_order_no }}">
					<td>{{ $po->back_order }}</td>
					<td>{{ $po->carton_id }}</td>
					<td>{{ $po->purchase_order_no }}</td>
					<td>{{$po->receiver_no}}</td>
					<td>{{ $po->total_qty }}</td>
					<td>{{ $po->fullname }}</td>
					<td>{{ date("M d, Y", strtotime($po->created_at)) }}</td>
					<td>{{ $po->data_display }}</td>
				</tr>
				@endforeach
		@endif
	</table>
</div>

</body>
</html>