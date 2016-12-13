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
<style type="text/css">
<link  rel="stylesheet" type="text/css" media="print" />
.single_record{
 page-break-after: always;
}
</style>

</head>
<body> 
	 
<div>

			 
	<table>
	<thead> 
	<tr>
			<th></th>
			 
		</tr> 
 	</thead>
<thead> 
	<tr>
			<th>TL number : {{$picklist_doc}} </th>
			 
		</tr> 
 	</thead>
		 </table>

		 <table>
 
		  <thead> 
		 
		 <tr class="font-size-02 tblrow">
					

					<td  > From </td>

					<td  > To  </td>
				 	<td>QTY </td>
				 </tr>

				</thead>
	 	
		 
			@foreach( $results as $asdf )
				<tr class="single_record">
			 
		 
				 <td> 8001-Warehouse  </td>
		 		
				 <td> {{$asdf->store_code.'-'.$asdf->store_name}}</td>

				 <td> {{$asdf->total_qty}}</td>
	 	
	 
				</tr>
			 
				@endforeach
	 

	</table>
 
</div>
</body>
</html>