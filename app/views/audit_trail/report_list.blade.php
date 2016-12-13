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
				<h1>RSCI - eWMS<br/>AUDIT TRAIL REPORT</h1>
				Print Date: {{ date('m/d/y h:i A')}}
			</div>
	<table class="table table-striped table-bordered">
		<thead>
			<tr>
				<th>{{ $col_transaction_date }}</th>
				<th>{{ $col_module }}</th>
				<th>{{ $col_reference }}</th>
				<th>{{ $col_username }}</th>
				<th>{{ $col_action }}</th>
				<th>{{ $col_details }}</th>
			</tr>
		</thead>
		@if( !CommonHelper::arrayHasValue($results) )
			<tr class="font-size-13">
				<td colspan="6" class="align-center">{{ $text_empty_results }}</td>
			</tr>
		@else
			@foreach( $results as $audit_trail )
			<tr class="font-size-13">
				<td>{{ $audit_trail->created_at }}</td>
				<td>{{ $audit_trail->module }}</td>
				<td>{{ $audit_trail->reference }}</td>
				<td>{{ $audit_trail->username }}</td>
				<td>{{ $audit_trail->action }}</td>
				<td>{{ $audit_trail->data_after }}</td>
			</tr>
			@endforeach
		@endif
	</table>
</div>

</body>
</html>