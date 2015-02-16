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
td.underline hr{ margin-top: 20px; border: none; border-bottom: solid 1px #000;}
td.underline {padding-bottom: 0; }

#actionButtons { top:0; left: 0; background-color: #DFF1F7; padding: 5px;}
#actionButtons a {display: inline-block; padding: 1em; background-color: #3199BE; text-decoration: none; font: bold 1em Verdana ; color: #FFF;}
#actionButtons a:hover {background-color: #1F6077 ;}

</style>
<div id="actionButtons">
	<a href="#" onclick="window.print();">PRINT THIS</a>
	<a href="{{url('load/list')}}">BACK TO LOAD LIST</a>

</div>
@foreach($records['StoreOrder'] as $soNo => $val)
	<?php $grandTotal = 0;?>
	<section class="soContainer">
		<header>
			<div class="doctitle">
				<h1>Philippine Gap/Old Navy CVS Inc.<br/>MTS REPORT</h1>
				Print Date: {{ date('m/d/y h:i A')}}
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
							<th>Store Order / TL No:</th>
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
							<td>{{$val['order_date']}}</td>
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
				<?php $boxTotal = 0;?>
				<tr>
					<td colspan="6"><strong>{{$boxNo}}</strong></td>
				</tr>
				@foreach($items as $item)
					<?php
						$boxTotal += $item->moved_qty;
						$grandTotal += $item->moved_qty;
					?>
					<tr>
						<td></td>
						<td>{{$item->upc}}</td>
						<td>{{$item->description}}</td>
						<td align="right">{{$item->moved_qty}}</td>
						<td class="underline"><hr/></td>
						<td class="underline"><hr/></td>
					</tr>
				@endforeach
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
				Checked By / Issued By / Date: <hr/><br/>
				Issuance Validated by:<hr/><br/>
			</div>
			<div>
				Delivered By / Date: <hr/><br/>
				Received by / Date:<hr/><br/>
			</div>
			<div>
				Posted By / Date:<hr/><br/>
			</div>
		</div>
	</section>
@endforeach


