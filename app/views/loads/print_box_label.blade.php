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

#actionButtons { top:0; left: 0; background-color: #DFF1F7; padding: 5px;width: 52%;}
#actionButtons a {display: inline-block; padding: 1em; background-color: #3199BE; text-decoration: none; font: bold 1em Verdana ; color: #FFF;}
#actionButtons a:hover {background-color: #1F6077 ;}

</style>
<div id="actionButtons">
	<a href="#" onclick="window.print();">PRINT THIS</a>
	<a href="{{url('load/list')}}">BACK TO LOAD LIST</a>

</div>
	<?php 
		$boxarray=[];
		$tempboxarray=[];
		$counter=1;
	?>
@foreach($records['StoreOrder'] as $soNo => $val)
	@foreach($val['items'] as $boxNo => $items)
		<?php 
			if(!in_array($boxNo, $tempboxarray)){
				$sonoarray[$boxNo] =[];
				array_push($sonoarray[$boxNo], $soNo);
				array_push($tempboxarray, $boxNo);
			}
			else
				array_push($sonoarray[$boxNo], $soNo);

			$totalBox=count($tempboxarray);
		?>
	@endforeach
@endforeach
@foreach($records['StoreOrder'] as $soNo => $val)
	@foreach($val['items'] as $boxNo => $items)
		<?php 
			$boxTotal=0;
		?>
		@foreach($items as $item)
			<?php
				$boxTotal += $item->moved_qty;
			?>
		@endforeach
		@if(!in_array($boxNo, $boxarray))
		<section class="soContainer" style="width:375px; height:225px" >
			<h1>Box {{$counter .' of '. $totalBox }}</h1>
			<div class="doctitle">
				<h1>Box No:<br/>{{$boxNo}}</h1>
			</div>
			<table class="contents">
				<tr>
					<th>Category</th>
					<th>MTS No</th>
					<th>From</th>
					<th>To</th>
					<th>Quantity</th>
				</tr>
					<tr>
						<td>{{$items[0]->sub_dept.' - '.$items[0]->description}}</td>
								<td> 
								@foreach($sonoarray[$boxNo] as $so_no)
									@if(count($sonoarray[$boxNo])>1)
										{{$so_no}}, 
									@else
										{{$so_no}}
									@endif
								@endforeach
								</td>
							<td>7000 - Warehouse</td>
							<td>{{ $val['store_code'] .' - ' . $val['store_name']}}</td>
							<td align="right"> {{$boxTotal}} </td>
					</tr>
			</table>
			<?php 
				array_push($boxarray, $boxNo);
			?>

			@if($records['is_shipped'])
				Shipped by date: {{$records['ship_date']}}
			@else
				This box is not yet shipped
			@endif
		</section>
			<?php $counter++; ?>
		@endif
	@endforeach
@endforeach


