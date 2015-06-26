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
				<h1>Casual Clothing Retailers Inc.<br/>STORE ORDER MTS REPORT</h1>
				Print Date: {{ date('m/d/y h:i A')}}
			</div>
	<table class="table table-striped table-bordered">
		<thead>
			<tr>
				<th>{{ $col_box_no }}</th>
				<th>{{ $col_upc }}</th>
				<th>{{ $col_short_name }}</th>
				<th>{{ $col_issued }}</th>
				<th>{{ $col_received }}</th>
				<th>{{ $col_damaged }}</th>
			</tr>
		</thead>
		@if( !CommonHelper::arrayHasValue($results) )
		<tr class="font-size-13">
			<td colspan="11" class="align-center">{{ $text_empty_results }}</td>
		</tr>
		@else
			@foreach( $results as $so )
			<tr class="font-size-13">
				<td>{{ $so->box_code }}</td>
				<td>{{ $so->upc }}</td>
				<td>{{ $so->description }}</td>
				<td>{{ $so->moved_qty }}</td>
				<td>{{ $so->delivered_qty }}</td>
				<td></td>
			</tr>
			@endforeach
		@endif
	</table>
</div>

</body>
</html>