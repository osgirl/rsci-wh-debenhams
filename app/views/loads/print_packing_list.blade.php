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
.contents th, .contents td { padding: 2px;  border: solid 1px #F0F0F0; margin: 0; }
.contents th {text-align: left; padding: 5px;}
.contents th {background-color: #F0F0F0}

.contents2 {margin-top: 10px; width: 100%;}
.contents2 th, .contents td { padding: 2px; margin: 0; }
.contents2 th {text-align: left; padding: 5px;}
.contents2 th {background-color: #F0F0F0}

.comments {width: 100%; margin-top: 15px;}
.comments hr{ margin-top:25px;}

.signatories {width: 100%; margin-top: 25px; line-height: 20px;  }
.signatories div {float: left; width: 30%; margin-right: 3%;}
.signatories hr{margin-top:25px;}
td.underline hr{ margin-top: 20px; border: none; border-bottom: solid 1px #000;}
td.underline {padding-bottom: 0; }
td.plain { padding: 2px;  border: 1px #F0F0F0; margin: 0;}

#actionButtons { top:0; left: 0; background-color: #DFF1F7; padding: 5px;}
#actionButtons a {display: inline-block; padding: 1em; background-color: #3199BE; text-decoration: none; font: bold 1em Verdana ; color: #FFF;}
#actionButtons a:hover {background-color: #1F6077 ;}

</style>

<div id="actionButtons">
	<a href="{{url('load/printpacklist/update/'.$loadCode)}}" onclick="window.print();">PRINT THIS</a>
	<a href="{{url('load/list')}}">BACK TO LOAD LIST</a>

</div>
	<section class="soContainer">
		<header>
			<div class="doctitle">
				<h1>Casual Clothing Retailers Inc.<br/>PACKING LIST</h1>
				Print Date: {{ date('m/d/y h:i A')}} <br>
				@if($print_status == 0)
				ORIGINAL
				@else
				REPRINT
				@endif
			</div>
		</header>
		<table class="commonInfo">
			<tr>
				<td>
					<table><tr>
							<th>From Location:</th>
							<td>Warehouse</td>
						</tr><tr>
							<th>To Location:</th>
							<td>{{ $records['store_code'] .' - ' . $records['store_name']}}</td>
						</tr>
					</table>
				</td>
				<td>
					<table><tr>
							<th>Username:</th>
							<td>{{Auth::user()->username;}}</td>
						</tr><tr>
							<th>PL Number:</th>
							<td>_____________</td>
						</tr>
					</table>
				</td>
			<tr>
		</table>
		<table class="contents">
			<tr>
				<th rowspan="2" style="text-align: center">Brand</th>
				<th rowspan="2" style="text-align: center">Ref MTS No.</th>
				<th rowspan="2" style="text-align: center">Boxes</th>
				<th colspan="2" style="text-align: center">Quantity</th>
				<th rowspan="2" style="text-align: center">Received By/ Date</th>
			</tr>
            <tr>
				<th style="text-align: center">Issued</th>
				<th style="text-align: center">Received</th>
			</tr>
			<?php 
				$boxarray=[];
				$grandTotal = 0;
				$counter=0;
			?>
			@foreach($records['StoreOrder'] as $soNo => $val)
				<tr>
					<td align="center">{{$val['brand']}}</td>
					<td align="center"><strong>{{$soNo}}</strong></td>
			    @foreach($val['items'] as $boxNo => $items)
					<?php 
						if(!in_array($boxNo, $boxarray))
							array_push($boxarray, $boxNo);
	    				$boxTotal = 0;
						$counter++;
					?>
					@if($counter>1)
						<tr>
							<td></td>
							<td></td>
					@endif
			    
			    	<td align="center">{{$boxNo}}</td>
					@foreach($items as $item)
						<?php
							if($item->so_no == $soNo){
								$boxTotal += $item->moved_qty;
								$grandTotal += $item->moved_qty;
							}
						?>
					@endforeach
				    <td align="center">{{$boxTotal}}</td>
					<td class="underline"><hr/></td>
					@if($counter>1)
						</tr>
					@endif
			    @endforeach
				</tr>
			@endforeach
			<tr>
				<?php 
					$numOfBoxTotal=count($boxarray);
				?>
					<td class="plain"></td>
			    <th style="text-align: center">Box Total:</td>
			    <td align="center"> {{ $numOfBoxTotal }} </td>
			    <td align="center">{{$grandTotal}}</td>
				<td class="underline"><hr/></td>
				<td class="plain underline"><hr/></td>
			</tr>
			<tr>
				<td class="plain"><br></td>
			</tr>
			<tr>
					<td class="plain"></td>
				<th style="text-align: center">Grand Total: </th>
			    <td align="center">{{$numOfBoxTotal}}</td>
			    <td align="center">{{$grandTotal}}</td>
				<td class="underline"><hr/></td>
			</tr>
			<tr>
				<td class="plain"><br></td>
			</tr>
			<tr><th colspan="6">INTER-TRANSFERS</th></tr>
			<tr>
					<td class="plain"></td>
				<th style="text-align: center">Grand Total: </th>
				<td class="underline"><hr/></td>
				<td class="underline"><hr/></td>
			</tr>
			<tr>
				<td class="plain"><br></td>
			</tr>
			<tr>
				<td class="plain" style="text-align: right">Seal #1:</th>
				<td colspan="2" class="plain underline"><hr/></td>
				<td class="plain" style="text-align: right">Seal #2:</th>
				<td colspan="2" class="plain underline"><hr/></td>
			</tr>
		</table>

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
				<td colspan='3' class="underline"><hr/></td>
				<td></td>
				<td colspan='5' class="underline"><hr/></td>
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
				<td colspan='3' class="underline"><hr/></td>
				<td></td>
				<td colspan='3' class="underline"><hr/></td>
				<td></td>
				<td colspan='2' class="underline"><hr/></td>
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
				<td colspan='3' class="underline"><hr/></td>
				<td></td>
				<td colspan='3' class="underline"><hr/></td>
				<td></td>
				<td colspan='2' class="underline"><hr/></td>
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
			<tr>
				<td colspan='3'>
				</td>
				<td></td>
				<td colspan='3'>
				</td>
				<td></td>
				<td  colspan='3'>
				Posted By / Date:
				</td>
			</tr>
			<tr>
				<td colspan='3'>
				</td>
				<td></td>
				<td colspan='3'>
				</td>
				<td></td>
				<td colspan='2' class="underline"><hr/></td>
			</tr>
			<tr>
				<td colspan='3'>
				</td>
				<td></td>
				<td colspan='3'>
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
		Copy 1 &  2 - WH-OS (for checking), then to WH-DC (for Posting) and then to WH-SCC (for IMS update) <br>   Copy 3 - WH-SCC (file copy upon release)
	</section>