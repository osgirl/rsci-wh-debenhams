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
				<th>SHORT DESCRIPTION</th>
				<th>{{ $col_slot }}</th>
				<th>{{ $col_store }}</th>
				<th>{{ $col_quantity_to_pick }}</th>
				<th>{{ $col_picked_quantity }}</th>
				<th>{{ $col_status }}</th>
			</tr>
		</thead>
		@if( !CommonHelper::arrayHasValue($results) )
		<tr class="font-size-13">
			<td colspan="8" class="align-center">{{ $text_empty_results }}</td>
		</tr>
		@else
			@foreach( $results as $ld )
			<tr class="font-size-13">
				<td>{{ $ld->sku }}</td>
				<td>{{ $ld->short_description }}</td>
				<td>{{ $ld->from_slot_code }}</td>
				<td>{{ $ld->store_name }}</td>
				<td>{{ $ld->quantity_to_letdown }}</td>
				<td>{{ $ld->moved_qty }}</td>
				@if($ld->move_to_picking_area != 0)
					<td>{{$status_in_picking}}</td>
				@else
					<td>{{$status_not_in_picking}}</td>
				@endif

			</tr>
			@endforeach
		@endif
	</table>
</div>

</body>
</html>