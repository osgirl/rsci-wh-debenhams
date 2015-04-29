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

.comments {width: 100%; margin-top: 15px;}
.comments hr{ margin-top:25px;}

.signatories {width: 100%; margin-top: 25px; line-height: 20px;  }
.signatories div {float: left; width: 30%; margin-right: 3%;}
.signatories hr{margin-top:25px;}
td.underline hr{ margin-top: 10px; border: none; }
td.underline {padding-bottom: 0; border-bottom: solid 1px #000;}

#actionButtons { top:0; left: 0; background-color: #DFF1F7; padding: 5px;}
#actionButtons a {display: inline-block; padding: 1em; background-color: #3199BE; text-decoration: none; font: bold 1em Verdana ; color: #FFF;}
#actionButtons a:hover {background-color: #1F6077 ;}

</style>
<div id="actionButtons">
	<a href="{{url('load/print/update/'.$loadCode)}}" onclick="window.print();">PRINT THIS</a>
	<a href="{{url('load/list')}}">BACK TO LOAD LIST</a>
</div>
@foreach($records['StoreOrder'] as $soNo => $val)
	<?php $grandTotal = 0;?>
	<section class="soContainer">
		<header>
			<div class="doctitle">
				<h1>Casual Clothing Retailers Inc.<br/>MTS REPORT</h1>
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
					<table>
						<tr>
							<th>Load Code:</th>
							<td>{{$loadCode}}</td>
						</tr><tr>
							<th>MTS Number:</th>
							<td>{{ $soNo}}</td>
						</tr><tr>
							<th>From Location:</th>
							<td>Warehouse</td>
						</tr><tr>
							<th>To Location:</th>
							<td>{{ $val['store_code'] .' - ' . $val['store_name']}}</td>
						</tr>
					</table>
				</td>
				<td>
					<table>
						<tr>
							<th>Delivery Date:</th>
							<td>_____________</td>
						</tr><tr>
							<th>MTS Date:</th>
							<td>{{date('m/d/Y',strtotime($val['order_date'])) }}</td>
						</tr><tr>
							<th>User ID:</th>
							<td>{{Auth::user()->username;}}</td>
						</tr>
					</table>
				</td>
			<tr>
		</table>
		<table class="contents">
			<tr>
				<th>Box No.</th>
				<th>UPC</th>
				<th>Description</th>
				<th>Issued</th>
				<th>Received</th>
				<th>Damaged</th>
			</tr>
			@foreach($val['items'] as $boxNo => $items)
				<?php $boxTotal = 0;
				$counter=0;?>
				<tr>
					<td rowpan="3"><strong>{{$boxNo}}</strong></td>
				@foreach($items as $item)
					<?php
						if($item->so_no == $soNo){
							$boxTotal += $item->moved_qty;
							$grandTotal += $item->moved_qty;
							$counter++;
						}
					?>
					@if($item->so_no == $soNo)
						@if($counter>1)
							<tr>
								<td></td>
						@endif
							<td>{{$item->upc}}</td>
							<td>{{$item->description}}</td>
							<td align="right">{{$item->moved_qty}}</td>
							<td class="underline"><hr/></td>
							<td class="underline"><hr/></td>
						@if($counter>1)
							</tr>
						@endif
					@endif
				@endforeach
				</tr>
				<tr>
					<td colspan="3" align="right"><strong>Box Total: </strong></td>
					<td align="right">{{$boxTotal}}</td>
					<td class="underline"><hr/></td>
					<td class="underline"><hr/></td>
				</tr>
			@endforeach
			<tr>
				<th colspan="3" align="right">Grand Total: </th>
				<td align="right">{{$grandTotal}}</td>
				<td class="underline"><hr/></td>
				<td class="underline"><hr/></td>
			</tr>
		</table>

		<div class="comments">
			Comments:
			<hr/><hr/><hr/>


		</div>

		
		<div class="signatories">
			<div>
				Checked By / Issued By / Date: <hr/>
				Issuance Validated by:<hr/><br/>
			</div>
			<div>
				<br/>
			</div>

			<div>
				Delivered By / Date: <hr/>
				Received by / Date:<hr/><br/>
			</div>
		</div>
			Copy 1 - ICG &nbsp;&nbsp;&nbsp;    Copy 2 - Receiving / To  &nbsp;&nbsp;&nbsp;   Copy 3 - From Location   &nbsp;&nbsp;&nbsp;  Copy 4 - From Location
	</section>
@endforeach


