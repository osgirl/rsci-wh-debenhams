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
				<a class="font-size-02"> Debenhams<br/>Reverse Logistic Unlisted Report<br/></a>
				Printed By: {{Auth::user()->username}} <br>
				Print Date: {{ date('m/d/y h:i A')}}
			</div>
	<table class="table table-striped table-bordered">
		<thead>
			<tr>
				<th>TL number</th>
			 
				<th>{{ $col_store_name }}</th>
				<th> SKU </th>
				<th> UPC </th>
				<th> Short Name </th>
				<th>{{ $col_order_date }}</th>
				<th> Piler Name</th>
				<th> Quantity Received</th>
			 
			</tr>
		</thead>
		@if( !CommonHelper::arrayHasValue($results) )
		<tr class="font-size-13">
			<td colspan="9" style="text-align: center;">{{ $text_empty_results }}</td>
		</tr>
		@else
			@foreach( $results as $so )
			<tr class="font-size-13 tblrow" data-id="{{ $so->move_doc_number }}">
				 <td>{{$so->move_doc_number}}</td>
				 <td>{{$so->store_name}}</td>
				 <td>{{$so->sku}}</td>
				 <td>{{$so->upc}}</td>
				 <td>{{$so->description}}</td>
				 <td>{{$so->created_at}}</td>
				 <td>{{$so->firstname.''.$so->lastname}}</td>
				 <td>{{$so->moved_qty}}</td>
			</tr>
			@endforeach
		@endif
		<tr>
			<td>Total item:{{count($results) }} </td>
		</tr>
	</table>
</div>

</body>
</html>