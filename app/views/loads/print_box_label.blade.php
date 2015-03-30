<style>
@media print {
    .soContainer {page-break-after: always; page-break-inside: avoid;}
    #actionButtons {display: none;}

}
@media screen {
    #mainContainer {width: 800px; }

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
		$counter=0;
	?>
@foreach($records as $boxNo => $val)
	@foreach($val['items'] as $item)
		<?php 
			if(!in_array($boxNo, $tempboxarray)){
				$sonoarray[$boxNo] =[];
				if( $item->box_code== $boxNo && !in_array($item->so_no, $sonoarray[$boxNo]))
					array_push($sonoarray[$boxNo], $item->so_no);
				array_push($tempboxarray, $boxNo);
			}
			else if( $item->box_code== $boxNo && !in_array($item->so_no, $sonoarray[$boxNo]))
				array_push($sonoarray[$boxNo], $item->so_no);

			$totalBox=count($tempboxarray);
		?>
	@endforeach
@endforeach
@foreach($records as $boxNo => $val)
		<?php 
			$boxTotal=0;
		?>
	@if($counter%3==0)
	<section class="soContainer" style="width:400px;">
	@endif

	@foreach($val['items'] as $item)
            <?php
            if( $item->box_code== $boxNo)
                $boxTotal += $item->moved_qty;
            ?>
    @endforeach

	@foreach($val['items'] as $item)
		@if(!in_array($boxNo, $boxarray) && $item->box_code== $boxNo)
			<div style="width:375px; height:225px; border: solid 1px #000; padding: 10px;" >
				<h1>Box {{$counter+1 .' of '. $totalBox }}</h1>
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
							<td>{{$item->sub_dept.' - '.$item->description}}</td>
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

			</div>
			<?php $counter++; ?>
		@endif
	@endforeach
	@if($counter%3==0)
	</section>
	@endif
@endforeach


