<style type="text/css">
	.contents2 {margin-top: 10px; width: 100%;}
	.contents2 th, .contents td { padding: 2px; margin: 0; }
	.contents2 th {text-align: left; padding: 5px;}
	.contents2 th {background-color: #F0F0F0}

	td.underline hr{ margin-top: 20px; border: none; border-bottom: solid 1px #000;}
	td.underline {padding-bottom: 0; }
</style>
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
				<h1>Casual Clothing Retailers Inc.<br/>RECEIVING WORK SHEET</h1>
				Printed By: {{Auth::user()->username}} <br>
				Print Date: {{ date('m/d/y h:i A')}}
			</div>
	<table>
		<tr>
			<td>
				Brand ID / Description:
			</td>
			<td>
				{{$brand.' - '. $brand_description}}
			</td>
		</tr>
		<tr>
			<td>
				Division ID / Description:
			</td>
			<td>
				{{$division.' - '. $div_description}}
			</td>
		</tr>
	</table>
	<table class="table table-striped table-bordered">
		<thead>
			<tr>
				<th>{{ $col_box_code }}</th>
				<th>{{ $col_back_order }}</th>
				<th>{{ $col_carton_id }}</th>
				<th>{{ $col_po_no }}</a></th>
				<th>{{ $col_shipment_ref }}</a></th>
				<th>{{ $col_receiver_no }}</th>
				<th>{{ $col_total_qty }}</th>
				<th>{{ $col_receiving_stock_piler }}</th>
				<th>{{ $col_entry_date }}</th>
				<th>{{ $col_status }}</th>
				<th>{{ $col_sticker_by }}</th>
			</tr>
		</thead>
		@if( !CommonHelper::arrayHasValue($results) )
		<tr class="font-size-13">
			<td colspan="11" style="text-align: center;">{{ $text_empty_results }}</td>
		</tr>
		@else
			@foreach( $results as $key=>$po )
				<tr class="font-size-13 tblrow" data-id="{{ $po->purchase_order_no }}">
					<td>{{ $key+1 }}</td>
					<td>{{ $po->back_order }}</td>
					<td>{{ $po->carton_id }}</td>
					<td>{{ $po->purchase_order_no }}</td>
					<td>{{ $po->shipment_reference_no }}</td>
					<td>{{$po->receiver_no}}</td>
					<td>{{ $po->total_qty }}</td>
					<td>{{ $po->fullname }}</td>
					<td>{{ date("M d, Y", strtotime($po->created_at)) }}</td>
					<td>{{ $po->data_display }}</td>
					<td>{{ $po->fullname }}</td>
				</tr>
				@endforeach
		@endif
		<tr>
			<td>Subtotal = {{count($results) }} </td>
		</tr>
	</table>
	<table class="contents2">
		<tr>
			<td colspan='3'>
				Received and Putaway By / Date:
			</td>
			<td colspan='3'>
				POs Closed By / Date:
			</td>
		</tr>
		<tr>
			<td class="underline" colspan='2'><hr/></td>
			<td></td>
			<td class="underline" colspan='2'><hr/></td>
			<td></td>
		</tr>
	</table>
</div>

</body>
</html>