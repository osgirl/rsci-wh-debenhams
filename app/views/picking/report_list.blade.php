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
	<table class="table table-striped table-bordered">
		<thead>
			<tr>
				<th>{{ $col_doc_no }}</th>
				<th>STORE</th>
				<th>{{ $col_receiving_stock_piler }}</th>
				<th>{{ $col_status }}</th>
			</tr>
		</thead>
		@if( !CommonHelper::arrayHasValue($results) )
			<tr class="font-size-13">
				<td colspan="10" style="text-align: center;">{{ $text_empty_results }}</td>
			</tr>
		@else
			@foreach( $results as $value )
				<tr class="font-size-13 tblrow" data-id="{{ $value['move_doc_number'] }}">
					<td> {{ $value['move_doc_number'] }} </td>
					<td>{{ Store::getStoreName($value['store_code']) }}</td>
					<td>{{ $value['fullname'] }}</td>
					<td>{{ $value['data_display'] }}</td>
				</tr>
			@endforeach
		@endif
	</table>
</div>

</body>
</html>