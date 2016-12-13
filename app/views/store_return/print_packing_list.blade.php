<style>
@media print {
    .soContainer {page-break-after: always; page-break-inside: avoid;}
    #actionButtons {display: none;}

}
@media screen {
    #mainContainer {width: 750px; }

}
body { font: normal 12px arial; margin: 0; counter-reset:pageNumber;}
table {padding: 0; border-collapse: collapse;}
h1 {margin-bottom: 5px;}
header {margin-bottom: 20px;}

.soContainer { border: solid 1px #000; padding: 10px;}
.soContainer:after { content:""; display:block; clear:both; }

.doctitle {text-align: center;}
.commonInfo {width: 100%; border: none;}
.commonInfo th, .commonInfo td  {text-align: left;}
.commonInfo th {width: 150px;}

.contents {margin-top: 10px; width: 100%;}
.contents th, .contents td {border: solid 1px #F0F0F0; margin: 0; }
.contents th {text-align: left; padding: 5px;}
.contents th {background-color: #F0F0F0}

.contents2 {margin-top: 10px; width: 100%;}
.contents2 th, .contents td {margin: 0; }
.contents2 th {text-align: left; padding: 5px;}
.contents2 th {background-color: #F0F0F0}

.comments {width: 100%; margin-top: 15px;}
.comments hr{ margin-top:25px;}

.signatories {width: 100%; margin-top: 25px; line-height: 20px;  }
.signatories div {float: left; width: 30%; margin-right: 3%;}
.signatories hr{margin-top:25px;}
td.underline hr{ margin-top: 10px; border: none;border-bottom: solid 1px #000;}
td.underline {border-bottom: solid 1px #000;}
td.plain {border: 1px #F0F0F0; margin: 0;}
td.plainUnderline {border: 1px #F0F0F0; margin: 0;border-bottom: solid 1px #000;}

#actionButtons { top:0; left: 0; background-color: #DFF1F7; padding: 5px;}
#actionButtons a {display: inline-block; padding: 1em; background-color: #3199BE; text-decoration: none; font: bold 1em Verdana ; color: #FFF;}
#actionButtons a:hover {background-color: #1F6077 ;}

</style>

<div id="actionButtons">
	<a  href="#" onclick="window.print();">PRINT THIS</a>
	<a href="{{url('stocktransfer/stocktranferload').$url_back}}">BACK TO SUBLOC LOAD LIST</a>

</div>
 
	<section class="soContainer">
		<header>
			<div class="doctitle">
				<h1>RSCI - eWMS<br/>MTS LIST</h1>
			 
			 
			</div>
		</header>
		<table class="commonInfo">
			<tr>
				<td>
					<table><tr>
							<th style="text-align: right;"> From Location :</th>
							<td>  {{ Store::getStoreName($storelocation->from_store_code) }}</td>
						</tr><tr>
							<th style="text-align: right;"> To Location : </th>
							<td> {{ Store::getStoreName($storelocation->to_store_code) }} </td>
						</tr>
					</table>
				</td>
				<td>
					<table>
						 
						 
						<tr>
							<th style="text-align: right">Pell ID:</th>
							<td >{{$loadCode}}</td>
						</tr>
					</table>
				</td>
			<tr>
		</table>
		<table class="contents">
			<tr> 
				<th rowspan="2" style="text-align: center">Ref MTS No.</th>

				<th rowspan="2" style="text-align: center">Boxes</th> 
				<th rowspan="2" style="text-align: center">UPC</th>
				<th rowspan="2" style="text-align: center">Description</th>
				<th colspan="3" style="text-align: center">Quantity</th> 
			</tr>
            <tr>
				<th style="text-align: center">Issued</th>
				<th style="text-align: center">Received</th>
				<th style="text-align: center">Damaged</th>
			</tr>
		 
		 
				<tr>
	<?php
		$boxarray=[];
		$grandTotal = 0;
	?>
	<?php
	$boxcount=0;
	$qtycount=0;
	$mtscount=0;

	$masteryong="";
	?>

			@foreach( $records as $doc_no )
				<tr class="font-size-13 tblrow" >
			 	<?php 

			 	?>
	 				<td style="text-align: center">{{$doc_no->move_doc_number}} </td>
	 				<td style="text-align: center"> {{$doc_no->box_code}} </td>
	 				<td style="text-align: center">  {{$doc_no->sku}}  </td>
	 				<td style="text-align: center">  {{$doc_no->description}} </td>
	 				<td style="text-align: center">  {{$doc_no->moved_qty}} </td>
	 				<td class="underline"></td>
	 				<td class="underline"></td>
					<?php
					//$boxcount+=1;
	 			 	$boxcount=$boxcount+1;
	 			 	$qtycount=$qtycount+$doc_no->moved_qty;
	 			 	$mtscount+=1;
	 			 	?>
				</tr>
			 @endforeach
		 
			 
			<tr>
				 
			    <th style="text-align: right" colspan="4">Total:</td>
			    <th style="text-align: center"  ><?php echo $qtycount; ?></td>
			   
			 
				<th class="plain"><br></th>
				<th class="plain"><br></th>
			</tr>
			 <table class="contents2">
			<tr>
				<td colspan='3'>
				Prepared By / Date:
				</td>
				<td></td>
				<td colspan='4'>
				Other Remarks:
				</td>
			</tr>
			<tr>
				<td colspan='3' class="underline"><br/></td>
				<td></td>
				<td colspan='5' class="underline"></td>
			</tr>
			<tr>
				<td colspan='3'>
					<br>
				</td>
			<tr>
			<tr>
				<td colspan='3'>
				Issued By / Date:
				</td>
				<td></td>
				<td colspan='3'>
				Issuance/Validated By / Date:
				</td>
				<td></td>
				<td colspan='3'>
				Delivery Van Opened By / Date:
				</td>
			</tr>
				<td colspan='3' class="underline"><br/></td>
				<td></td>
				<td colspan='3' class="underline"></td>
				<td></td>
				<td colspan='2' class="underline"></td>
			</tr>
			<tr>
				<td colspan='3'>
				Signature over Printed Name
				</td>
				<td></td>
				<td colspan='3'>
				Signature over Printed Name
				</td>
				<td></td>
				<td colspan='3'>
				Signature over Printed Name
				</td>
			</tr>
			<tr>
				<td colspan='3'>
					<br>
				</td>
			<tr>
			<tr>
				<td colspan='3'>
				Plate No.:
				</td>
				<td></td>
				<td colspan='3'>
				Deliveryman:
				</td>
				<td></td>
				<td colspan='3'>
				Driver:
				</td>
			</tr>
			<tr>
				<td colspan='3' class="underline"><br/></td>
				<td></td>
				<td colspan='3' class="underline"></td>
				<td></td>
				<td colspan='2' class="underline"></td>
			</tr>
			<tr>
				<td colspan='3'>
				</td>
				<td></td>
				<td colspan='3'>
				Signature over Printed Name
				</td>
				<td></td>
				<td colspan='3'>
				Signature over Printed Name
				</td>
			</tr>
			<tr>
				<td colspan='3'>
					<br>
				</td>
			<tr>

		</table>
		 
	<!-- 	Copy 1 &  2 - WH-OS (for checking), then to WH-DC (for Posting) and then to WH-SCC (for IMS update) <br>   Copy 3 - WH-SCC (file copy upon release) -->
	</section>
 