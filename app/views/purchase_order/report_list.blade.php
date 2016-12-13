<style type="text/css">
	.contents2 {margin-top: 10px; width: 100%; font-family: Courier;}
	.contents2 th, .contents td { padding: 2px; margin: 0; }
	.contents2 th {text-align: left; padding: 5px;}
	.contents2 th {background-color: #F0F0F0}
 
	td.underline hr{ margin-top: 20px; border: none; border-bottom: solid 1px #000;}
	td.underline {padding-bottom: 0; }
</style>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>{{ $col_po_no }}</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
<meta name="apple-mobile-web-app-capable" content="yes">
{{ HTML::style('resources/css/bootstrap.min.css') }}
{{ HTML::style('resources/css/bootstrap-responsive.min.css') }}
{{ HTML::style('resources/css/style.css') }}
</head>
<body>
<div class="table-responsive contnt2" >
			<div style="text-align: center"; >
				<a class="font-size-02"> RSCI - eWMS <br/> Shortage/Overage Report<br/></a>
				Printed By: {{Auth::user()->username}} <br>
				Print Date: {{ date('m/d/y h:i A')}}<br>
			</div>
	 <table class="contents2" >
		<tr>
			<th colspan='3'>
				
				SHIPMENT REFERENCE :{{$po_info->shipment_reference_no}}
			</th>
			<th colspan='3'>
				PO NO.: {{$po_no}}
			</th>
		</tr>
		<tr>
			 
			<th colspan='3'>
				INVOICE NO. :  {{$po_info->invoice_no}}
				
			</th>
			<th colspan='3'>
				RCR NO. :{{$receiver_no}}
				</th>
		</tr>
		<br>
		<br>
	</table> 
			 
				
			 
	 
	<table class="table table-striped table-bordered contnt2">
		<thead>
				<tr>  
					<th style="text-align: center"   rowspan="2">Dept</th>
					<th  style="text-align: center"   rowspan="2">Style No. </th>
					<th style="text-align: center"   rowspan="2">SKU</th>
						<th style="text-align: center"   rowspan="2">UPC</th>
					<th  style="text-align: center" colspan="2">Quantity</th>
					<th style="text-align: center" rowspan="2">Discrepancy <br>(Short/Over)</th>
					<th style="text-align: center" rowspan="2">Invoice No.</th>
					<th style="text-align: center" rowspan="2">Remarks</th>
				</tr>
					<tr>
					 
						<th style="text-align: center" >Advised Per RA </th>
						<th style="text-align: center" > Actual Receipt</th>
						 
			 
						 
					</tr>
				</thead>
		@if( !CommonHelper::arrayHasValue($results) )
		<tr class="font-size-13">
			<td colspan="11" style="text-align: center;">{{ $text_empty_results }}</td>
		</tr>
		@else
		<?php 	$total=0;
			 	$qty_ord=0; 
			 	$qty_rcv=0;


		?>

			@foreach( $results as $asdf )
				<tr class="font-size-13 tblrow" >
				
						 
						<td style="text-align: center" > {{$asdf->dept_number}}</td>
						<td style="text-align: center" >{{$asdf->short_description}}</td>
						<td style="text-align: center" > {{$asdf->sku}}</td>
						<td style="text-align: center" > {{$asdf->upc}}</td>
						<td style="text-align: center" >{{$asdf->quantity_ordered}}</td> 
						<td style="text-align: center" >{{$asdf->qty}}</td> 
						<td style="text-align: center" > {{$asdf->qty - $asdf->quantity_ordered}}</td>
						<td style="text-align: center" >{{$asdf->invoice_no}}</td> 
						<td style="text-align: center" ></td> 

					<?php  		$qty_ord+=$asdf->quantity_ordered;  
							 	$total=$total+($asdf->qty - $asdf->quantity_ordered); 
							 	$qty_rcv+=$asdf->qty;


					?>

			 
				</tr>
				
				@endforeach
		@endif

		<tr>
			<td style="text-align: center" >Total item:{{count($results) }} </td>

						 
						<td></td>
						<td> </td>
						<td> </td>
						<td style="text-align: center" ><?php echo $qty_ord; ?></td> 
						<td style="text-align: center" ><?php echo $qty_rcv; ?></td> 
						<td style="text-align: center" ><?php echo $total; ?> </td>
						<td style="text-align: center" > </td> 
						<td> </td> 
				</tr>
		 
	</table>
	<table class="contents2" >
		<tr>
			<td colspan='3'>
				Prepared By :
			</td>
			<td colspan='3'>
				Noted by :
			</td>
		</tr>
		<tr>
			<td class="underline" colspan='2'><hr/></td>
			<td></td>
			<td class="underline" colspan='2'><hr/></td>
			<td></td>
		</tr>
	</table>
</div>

</body>
</html>