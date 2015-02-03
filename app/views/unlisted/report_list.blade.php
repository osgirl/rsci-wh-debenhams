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
				<th>{{ $col_upc }}</th>
				<th>{{ $col_reference }}</th>
				<th>{{ $col_quantity_received }}</th>
				<th>{{ $col_description }}</th>
				<th>{{ $col_style_no }}</th>
				<th>{{ $col_brand }}</th>
				<th>{{ $col_division }}</th>
			</tr>
		</thead>
		@if( !CommonHelper::arrayHasValue($results) )
			<tr class="font-size-13">
				<td colspan="9" class="align-center">{{ $text_empty_results }}</td>
			</tr>
		@else
			@foreach($results as $unlist)
			<tr class="font-size-13">
				<td>{{ $unlist['sku'] }}</td>
				<td>{{ $unlist['reference_no'] }}</td>
				<td>{{ $unlist['quantity_received'] }}</td>
				<td>{{ $unlist['description'] }}</td>
				<td>{{ $unlist['style_no'] }}</td>
				<td>{{ $unlist['brand'] }}</td>
				<td>{{ $unlist['division'] }}</td>
			</tr>
			@endforeach
		@endif
	</table>
</div>

</body>
</html>