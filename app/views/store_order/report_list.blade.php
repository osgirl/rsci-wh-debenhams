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
				<th>{{ $col_so_no }}</th>
				<th>{{ $col_store }}</th>
				<th>{{ $col_store_name }}</th>
				<th>{{ $col_order_date }}</th>
				<th>{{ $col_status }}</th>
				<th>{{ $col_load_code }}</th>
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
				<td>{{ $so->store_code }}</td>
				<td>{{ $so->store_name }}</td>
				<td>{{ date("M d, Y", strtotime($so->order_date)) }}</td>
				<td>{{$so_status_type[$so->so_status]}}</td>
				<td>{{ $so->load_code }}</td>
			</tr>
			@endforeach
		@endif
	</table>
</div>

</body>
</html>