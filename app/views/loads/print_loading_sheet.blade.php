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
	<section class="soContainer">
		<header>
			<div class="doctitle">
				<h1>Casual Clothing Retailers Inc.<br/>WAREHOUSE LOADING SHEET</h1>
				Print Date: {{ date('m/d/y h:i A')}}
			</div>
		</header>
		<table class="commonInfo">
			<tr>
				<td>
					<table>
					    <tr>
					    <th style="text-align: right">Delivery Van Plate No:</th>
					    <td>_____________</td>
						</tr><tr>
							<th style="text-align: right">Destination:</th>
							<td>{{ $records['store_code'] .' - ' . $records['store_name']}}</td>
					    </tr>
					</table>
				</td>

				<td>
					<table>
					    <tr>
					    <th style="text-align: right">Seal #1:</th>
					    <td>_____________</td>
						</tr>
					    <tr>
					    <th style="text-align: right">Seal #2:</th>
					    <td>_____________</td>
						</tr>
						<tr>
							<th style="text-align: right">Manual PL#:</th>
							<td>_____________</td>
					    </tr><tr>
					    <th style="text-align: right">PL#:</th>
					    <td>_____________</td>
					    </tr>
					</table>
				</td>
                <td>
					<table>
					    <tr>
					    <td></td>
						</tr><tr>
							<td></td>
					    </tr>
					</table>
				</td>
			<tr>
		</table>
		<table class="contents">
			<tr>
				<th colspan="3" style="text-align: center">Regular Transfer from Warehouse</th>
				<th colspan="4" style="text-align: center">Inter-Store Transfer (IT)</th>
			</tr>
            <tr>
				<th style="text-align: center">MTS No.</th>
				<th style="text-align: center">Box #</th>
				<th style="text-align: center">QTY</th>
				<th style="text-align: center">MTS No.</th>
				<th style="text-align: center">Box #</th>
				<th style="text-align: center">QTY</th>
				<th style="text-align: center">Remarks</th>
			</tr>
            <tr>
	<?php 
		$boxarray=[];
		$grandTotal = 0;
		$counter=0;
	?>
			@foreach($records['StoreOrder'] as $soNo => $val)
				<tr>
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
					<td align="center"> {{$boxTotal}} </td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					@if($counter>1)
						</tr>
					@endif
				@endforeach
				</tr>
			@endforeach
			</tr>
			<tr>
				<?php 
					$numOfBoxTotal=count($boxarray);
				?>
				<th align="right">Total: </th>
				<td align="center">{{$numOfBoxTotal}}</td>
				<td align="center">{{$grandTotal}}</td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
			</tr>
		</table>

		<div class="signatories">
			<div>
				Prepared by: <hr/>
				Signature over Printed Name<br/>
			</div>
			<div>
				<br/>
			</div>
			<div>
				Checked by: <hr/>
				Signature over Printed Name<br/>
			</div>
		</div>
	</section>