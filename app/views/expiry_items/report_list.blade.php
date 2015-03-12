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
				<h1>Casual Clothing Retailers Inc.<br/>EXPIRY REPORT</h1>
				Print Date: {{ date('m/d/y h:i A')}}
			</div>
	<table class="table table-striped table-bordered">
		<thead>
			<tr>
				<th>{{ $col_shipment_ref_no }}</th>
				<th>{{ $col_purchase_order_no }}</th>
				<th>{{ $col_sku }}</th>
				<th>{{ $col_upc }}</th>
				<th>{{ $col_slot }}</th>
				<th>{{ $col_short_name }}</th>
				<th>{{ $col_expiry_date }}</th>
				<!-- <th>{{ $col_expected_quantity }}</th> -->
				<th>{{ $col_received_quantity }}</th>
				<th>{{ $col_received_by }}</th>
				<!-- <th> VARIANCE </th> -->
			</tr>
		</thead>
		@if( !CommonHelper::arrayHasValue($results) )
		<tr class="font-size-13">
			<td colspan="9" class="align-center">{{ $text_empty_results }}</td>
		</tr>
		@else
			@foreach( $results as $po )
			<tr class="font-size-13">
				<td>{{ $po->shipment_reference_no }}</td>
				<td>{{ $po->purchase_order_no }}</td>
				<td>{{ $po->sku }}</td>
				<td>{{ $po->upc }}</td>
				<td>{{ $po->slot_code }}</td>
				<td>{{ $po->short_description }}</td>
				<td>
					@if ($po->expiry_date == '0000-00-00 00:00:00' )
						N/A
					@else
						{{ date('M d, Y', strtotime($po->expiry_date)) }}
					@endif
				</td>
				<!-- <td>{{ $po->quantity_ordered }}</td> -->
				<td>{{ $po->quantity_delivered }}</td>
				<td>{{ $po->firstname .' '. $po->lastname}}</td>
				<!-- <td>{{ $po->quantity_ordered- $po->quantity_delivered }}</td> -->
			</tr>
			@endforeach
		@endif
	</table>
</div>

</body>
</html>