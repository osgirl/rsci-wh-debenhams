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
				<th>{{ $col_vendor_name }}</th>
				<th>{{ $col_vendor_no }}</th>
			</tr>
		</thead>
		@if( !CommonHelper::arrayHasValue($results) )
			<tr class="font-size-13">
				<td colspan="2" class="align-center">{{ $text_empty_results }}</td>
			</tr>
		@else
			@foreach($results as $vendor)
			<tr class="font-size-13">
				<td>{{ $vendor->vendor_name }}</td>
				<td>{{ $vendor->vendor_code }}</td>
			</tr>
			@endforeach
		@endif
	</table>
</div>

</body>
</html>