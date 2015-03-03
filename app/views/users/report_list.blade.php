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
				<h1>Casual Clothing Retailers Inc.<br/>USERS REPORT</h1>
				Print Date: {{ date('m/d/y h:i A')}}
			</div>
	<table class="table table-striped table-bordered">
		<thead>
			<tr>
				<th>{{ $col_username }}</th>
				<th>{{ $col_barcode }}</th>
				<th>{{ $col_name }}</th>
				<th>{{ $col_user_role }}</th>
				<th>{{ $col_brand }}</th>
				<th>{{ $col_date }}</th>
			</tr>
		</thead>
		@if( !CommonHelper::arrayHasValue($results) )
			<tr class="font-size-13">
				<td colspan="9" class="align-center">{{ $text_empty_results }}</td>
			</tr>
		@else
			@foreach( $results as $user )
			<tr class="font-size-13 tblrow" data-id="{{ $user->id }}">
				<td>{{ $user->username }}</td>
				<td>{{ $user->barcode }}</td>
				<td>{{ $user->name }}</td>
				<td>{{ $user->role_name }}</td>
				<td>{{ $user->brand_name }}</td>
				<td>{{ date('M d, Y', strtotime($user->created_at)) }}</td>
			</tr>
			@endforeach
		@endif
	</table>
</div>

</body>
</html>