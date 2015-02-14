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
				<th>{{ $col_store }}</th>
				<th>{{ $col_box_code }}</th>
				<th>{{ $col_date_created }}</th>
			</tr>
		</thead>
		@if( !CommonHelper::arrayHasValue($results) )
		<tr class="font-size-13">
			<td colspan="7" style="text-align: center;">{{ $text_empty_results }}</td>
		</tr>
		@else
			@foreach( $results as $box )
			<tr class="font-size-13 tblrow" data-id="{{$box['box_code']}}">
				<td>{{ $box['store_name'] }}</td>
				<td>{{ $box['box_code'] }}</td>
				<td>{{ date("M d, Y", strtotime($box['created_at']))}}</td>
			</tr>
			</tr>
			@endforeach
		@endif
	</table>
</div>

</body>
</html>