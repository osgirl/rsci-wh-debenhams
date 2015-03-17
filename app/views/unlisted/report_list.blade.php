<style type="text/css">
	.signatories {width: 100%; margin-top: 25px; line-height: 20px;  }
	.signatories div {float: left; width: 30%; margin-right: 3%;}
	.signatories hr{margin-top:25px;}
	td.underline hr{ margin-top: 20px; border: none; border-bottom: solid 1px #000;}
	td.underline {padding-bottom: 0; }
	footer {
    position: fixed;
    bottom: -50px;
    height: 40px;
    width: 100%;
    margin: 0 auto;}
</style>

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
				<h1>Casual Clothing Retailers Inc.<br/>UNLISTED ITEMS RECEIVING REPORT</h1>
				{{ $col_shipment_reference ." ". $shipment_reference_no }}<br />
				{{ $col_po_created }}____________<br />
				UIRR No. : {{$results[0]['destination']}}-{{$uirr_no}}<br />
				{{ $col_delivery_date." ".date('m/d/Y',strtotime($delivery_date)) }}<br />
				Print Date: {{ date('m/d/y h:i A')}}
			</div>

	<br /><br />
	<table class="table table-striped table-bordered">
		<thead>
			<tr>
				<th>{{ $col_reference }}</th>
				<th>{{ $col_upc }}</th>
				<th>{{ $col_quantity_received }}</th>
				<th>{{ $col_description }}</th>
				<th>{{ $col_style_no }}</th>
				<th>{{ $col_brand }}</th>
				<th>{{ $col_division }}</th>
				<th>{{ $col_scanned_by }}</th>
				<th>{{ $col_remarks }}</th>
			</tr>
		</thead>
		@if( !CommonHelper::arrayHasValue($results) )
			<tr class="font-size-13">
				<td colspan="9" class="align-center">{{ $text_empty_results }}</td>
			</tr>
		@else
			@foreach($results as $unlist)
			<tr class="font-size-13">
				<td>{{ $unlist['reference_no'] }}</td>
				<td>{{ $unlist['sku'] }}</td>
				<td>{{ $unlist['quantity_received'] }}</td>
				<td>{{ $unlist['description'] }}</td>
				<td>{{ $unlist['style_no'] }}</td>
				<td>{{ $unlist['brand'] }}</td>
				<td>{{ $unlist['division'] }}</td>
				<td>{{ $unlist['firstname'] .' '. $unlist['lastname']}}</td>
				<td></td>
			</tr>
			@endforeach
		@endif
	</table>
</div>

		<div class="signatories">
			<div>
				Checked by/Date: <hr/>
				Printed name/Signature/Date<br/>
			</div>
			<div>
				Validated by/Date: <hr/>
				Printed name/Signature/Date<br/>
			</div>
			<div>
				PO Created by/Date: <hr/>
				Printed name/Signature/Date<br/>
			</div>
		</div>
		<footer>
		Copy 1-Merchandising &emsp;&emsp;&emsp;	Copy 2- ICG &emsp;&emsp;&emsp; Copy 3- Warehouse
		</footer>
</body>
</html>