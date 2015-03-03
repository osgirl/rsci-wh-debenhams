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
				<h1>Casual Clothing Retailers Inc.<br/>BOX DETAIL REPORT</h1>
				Print Date: {{ date('m/d/y h:i A')}}
			</div>
	<table class="table table-striped table-bordered">
		<thead>
			<tr>
				<th>{{ $col_upc }}</th>
				<th>SHORT DESCRIPTION</th>
				<th>{{ $col_box_code }}</th>
				<th>{{ $col_moved_qty }}</th>
			</tr>
		</thead>
		@if( !CommonHelper::arrayHasValue($results) )
		<tr class="font-size-13">
			<td colspan="4" class="align-center">{{ $text_empty_results }}</td>
		</tr>
		@else
			@foreach( $results as $box )
			<tr class="font-size-13">
				<td>{{ $box->sku }}</td>
				<td>{{ $box->short_description }}</td>
				<td>{{ $box->box_code }}</td>
				<td>{{ $box->moved_qty }}</td>

			</tr>
			@endforeach
		@endif
	</table>
</div>

</body>
</html>