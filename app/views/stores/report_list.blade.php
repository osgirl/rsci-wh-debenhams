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
				<a class="font-size-02"> Debenhams<br/>Stores<br/></a>
				Printed By: {{Auth::user()->username}} <br>
				Print Date: {{ date('m/d/y h:i A')}}
			</div>
	<table class="table table-striped table-bordered">
		<thead>
			<tr>
				<th>{{ $col_store_name }}</th>
				<th>{{ $col_store_code }}</th>
				<th>{{ $col_store_address }} </th>
			</tr>
		</thead>
		@if( !CommonHelper::arrayHasValue($results) )
			<tr class="font-size-13">
				<td colspan="3" class="align-center">{{ $text_empty_results }}</td>
			</tr>
		@else
			@foreach($results as $store)
			<tr class="font-size-13">
				<td>{{ $store->store_name }}</td>
				<td>{{ $store->store_code }}</td>
				<td>{{ $store->address1.' '.$store->address2.' '.$store->address3 }}</td>
			</tr>
			@endforeach
		@endif
	</table>
</div>

</body>
</html>