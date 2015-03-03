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
				<h1>Casual Clothing Retailers Inc.<br/>PICKING DETAIL REPORT</h1>
				Print Date: {{ date('m/d/y h:i A')}}
			</div>
	<table class="table table-striped table-bordered">
		<thead>
			<tr>
				<th>{{ $col_sku }}</th>
				<th>{{ $col_upc }}</th>
				<th>SHORT DESCRIPTION</th>
				<th>{{ $col_so_no }}</th>
				<th>{{ $col_from_slot_code }}</th>
				<th>{{ $col_qty_to_pick }}</th>
				<th>{{ $col_to_move }}</th>
				<th>{{ $col_store_name }}</th>
				<th>{{ $col_store_code }}</th>
			</tr>
		</thead>
		@if( !CommonHelper::arrayHasValue($results) )
			<tr class="font-size-13">
				<td colspan="10" class="align-center">{{ $text_empty_results }}</td>
			</tr>
			@else
				@foreach( $results as $pd )
				<tr class="font-size-13">
					<td>{{$pd['sku']}}</td>
					<td>{{$pd['upc']}}</td>
					<td>{{$pd['short_description']}}</td>
					<td>{{$pd['so_no']}}</td>
					<td>{{$pd['from_slot_code']}}</td>
					<td>{{$pd['quantity_to_pick']}}</td>
					<td>{{$pd['moved_qty']}}</td>
					<td>{{$pd['store_name']}}</td>
					<td>{{$pd['store_code']}}</td>

				</tr>
				@endforeach
			@endif
	</table>
</div>

</body>
</html>