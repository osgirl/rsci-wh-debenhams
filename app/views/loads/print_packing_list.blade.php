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
	<a href="{{url('load/printpacklist/update/'.$loadCode.$url_back)}}" onclick="window.print();">PRINT THIS</a>
	<a href="{{url('load/list').$url_back}}">BACK TO LOAD LIST</a>

</div>
@foreach($records['StoreCode'] as $storeCode => $value)
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
							<td>{{ $storeCode .' - ' . $value['store_name']}}</td>
						</tr>
					</table>
				</td>
				<td>
					<table>
						<tr>
							<th>Username:</th>
							<td>{{Auth::user()->username;}}</td>
						</tr>
						<tr>
							<th>PL Number:</th>
							<td>{{$pl_num}} - Shipped</td>
						</tr>
						<tr>
							<th>Load ID:</th>
							<td>{{$loadCode}}</td>
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
				<th rowspan="2" colspan="3" style="text-align: center">Received By/ Date</th>
			</tr>
            <tr>
				<th style="text-align: center">Issued</th>
				<th style="text-align: center">Received</th>
			</tr>
			<?php
				$boxarray=[];
				$grandTotal = 0;
				$rowcount=0;
			?>
			@foreach($value['StoreOrder'] as $soNo => $val)
				<?php
					$boxes[$soNo]=[];
					$counter=0;
				?>
				<tr>
					@if($rowcount==0)
					<td align="center">{{$val['brand']}}</td>
					@else
					<td></td>
					@endif
					<td align="center"><strong>{{$soNo}}</strong></td>
			    @foreach($val['items'] as $boxNo => $items)
					<?php
						if(!in_array($boxNo, $boxarray)){
							array_push($boxarray, $boxNo);
						}
						if(!in_array($boxNo, $boxes[$soNo])){
							array_push($boxes[$soNo], $boxNo);
		    				$boxTotal = 0;
							$counter++;
						}
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
					<td class="underline"></td>
					@if($counter>1)
						</tr>
					@endif
					<?php $rowcount++; ?>
			    @endforeach
				</tr>
			@endforeach
			<tr>
				<?php
					$numOfBoxTotal=count($boxarray);
				?>
			    <th style="text-align: center" colspan="2">Box Total:</td>
			    <td align="center"> {{ $numOfBoxTotal }} </td>
			    <td align="center">{{$grandTotal}}</td>
				<td class="underline"></td>
				<td class='plain' style='width: 1%'></td>
				<td class="plainUnderline"></td>
				<td class='plain' style='width: 1%'></td>
			</tr>
			<tr>
				<td class="plain"><br></td>
			</tr>
			<tr>
				<th style="text-align: center" colspan="2">Grand Total: </th>
			    <td align="center">{{$numOfBoxTotal}}</td>
			    <td align="center">{{$grandTotal}}</td>
				<td class="underline"></td>
			</tr>
			<tr>
				<td class="plain"><br></td>
			</tr>
			<tr><th style="text-align: center" colspan="8">INTER-TRANSFERS</th></tr>
			<tr>
				<th style="text-align: center" colspan="2">MTS No.</th>
				<th style="text-align: center" colspan="2">Box No.</th>
				<th style="text-align: center" colspan="4">No. of Boxes</th>
			</tr>
			<?php
				$boxarray=[];
				$grandTotal = 0;
			?>
			@foreach($value['InterTransfer'] as $soNo => $val)
				<?php
					$boxes[$soNo]=[];
					$counter=0;
				?>
				<tr>
					<td align="center" colspan="2"><strong>{{$soNo}}</strong></td>
			    @foreach($val['items'] as $boxNo => $items)
					<?php
						if(!in_array($boxNo, $boxarray)){
							array_push($boxarray, $boxNo);
						}
						if(!in_array($boxNo, $boxes[$soNo])){
							array_push($boxes[$soNo], $boxNo);
		    				$boxTotal = 0;
							$counter++;
						}
					?>
					@if($counter>1)
						<tr>
							<td colspan="2"></td>
					@endif

			    	<td align="center" colspan="2">{{$boxNo}}</td>
					@foreach($items as $item)
						<?php
							if($item->mts_number == $soNo){
								$boxTotal += $item->no_of_boxes;
								$grandTotal += $item->no_of_boxes;
							}
						?>
					@endforeach
				    <td align="center" colspan="4">{{$boxTotal}}</td>
					@if($counter>1)
						</tr>
					@endif
			    @endforeach
				</tr>
			@endforeach
			@if($value['InterTransfer']==null)
				<tr>
					<td align="center" colspan="8">N/A</td>
				</tr>
			@else
			<tr>
				<th style="text-align: center" colspan="2">Grand Total: </th>
				<td align="center" colspan="2">{{count($boxarray)}}</td>
				<td align="center" colspan="4">{{$grandTotal}}</td>
			</tr>
			@endif
			<tr>
				<td class="plain"><br></td>
			</tr>
			<tr>
				<td class="plain" style="text-align: right">Seal #1:</th>
				<td colspan="2" class="plainUnderline"></td>
				<td class="plain" style="text-align: right">Seal #2:</th>
				<td colspan="4" class="plainUnderline"></td>
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
		Copy 1 &  2 - WH-OS (for checking), then to WH-DC (for Posting) and then to WH-SCC (for IMS update) <br>   Copy 3 - WH-SCC (file copy upon release)
	</section>
@endforeach