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
h2 {margin-bottom: 1px;}
header {margin-bottom: 20px;}

.soContainer { border: solid 1px #000; padding: 10px;}
.soContainer:after { content:""; display:block; clear:both; }

.doctitle {text-align: center;}
.commonInfo {width: 100%; border: none;}
.commonInfo th, .commonInfo td  {text-align: left;}
.commonInfo th {width: 150px;}

.contents {margin-top: 5px; width: 100%;}
.contents th, .contents td {border: solid 1px #F0F0F0; margin: 0; }
.contents th {text-align: left;}
.contents th {background-color: #F0F0F0}

.contentasdfs {width: 100%; }
.contentasdfs th, .contentasdfs  td {border: solid 0px #F0F0F0; margin-right: 0px; }
  

.comments {width: 100%; margin-top: 15px;}
.comments hr{ margin-top:25px;}

.signatories {width: 100%; margin-top: 25px; line-height: 20px;  }
.signatories div {float: left; width: 30%; margin-right: 3%;}
.signatories hr{margin-top:25px;}

#actionButtons { top:0; left: 0; background-color: #DFF1F7; padding: 5px;}
#actionButtons a {display: inline-block; padding: 1em; background-color: #3199BE; text-decoration: none; font: bold 1em Verdana ; color: #FFF;}
#actionButtons a:hover {background-color: #1F6077 ;}

</style>

<div id="actionButtons">
	<a href="#" onclick="window.print();">PRINT THIS</a>
	<a href="{{$url_back}}">BACK TO LOAD LIST</a>

</div>
	<section class="soContainer">
	  			<div class="doctitle"> <h2>RSCI - eWMS<br/>PACKING, EQUIPMENT & LOADING LIST</h2></div>
				 <!-- <div style="text-align: center">Print Date: {{ date('m/d/y h:i A')}}</div> -->
			<hr>
	 
		<table class="commonInfo">
			<tr>
				<td>
					<table>
				 
					    <tr><th><td>Van Seal Number:</td></th></tr>
					    <tr><th style="text-align: right";>Seal #1 :
					    <td><input type=""  value="" placeholder="" style="border: solid 0px #000;"> </td>
					    </th></tr>
					     <tr><th style="text-align: right";>Seal #2 :
					    <td><input type=""   value="" style="border: solid 0px #000;"></td>
					    </th></tr>
					    <tr><th style="text-align: right";>Seal #3 :
					    <td><input type=""   value="" style="border: solid 0px #000;"></td>
					    </th></tr>
					    <tr><th style="text-align: right";>Seal #4 :
					    <td><input type=""  value="" style="border: solid 0px #000;"></td>
					    </th></tr>
					
					     <tr><th style="text-align: right";>Truck Van Plate no.:
					    <td><input type=""  value="" style="border: solid 0px #000;"></td>
					    </th></tr>
					  <tr><th style="text-align: right";>Delivery Date:
					    <td><input type=""  value="" style="border: solid 0px #000;"></td>
					    </th></tr>
					</table>
				</td>

				<td>
				 
					<table>

						<tr><th><td>Van Sealed By:</td></th></tr>
						 <tr><th style="text-align: right";>Seal #1 :
					    <td><input type=""  value=""  style="border: solid 0px #000;"></td>
					    </th></tr>
					 
						 <tr><th style="text-align: right";>Seal #2 :
					    <td><input type=""  value=""  style="border: solid 0px #000;"></td>
					    </th></tr>
						 <tr><th style="text-align: right";>Seal #3 :
					    <td><input type=""  value=""  style="border: solid 0px #000;"></td>
					    </th></tr>
						 <tr><th style="text-align: right";>Seal #4 :
					    <td><input type=""  value=""  style="border: solid 0px #000;"></td>
					    </th></tr>
					    <tr><th style="text-align: right";>Origin :
					    <td><input type=""  placeholder="8001 - Warehouse "  style="border: solid 0px #000;"></td>
					    </th></tr>   
						<tr><th style="text-align: right";>Destination : 
					    <td> </td>
					    </th></tr>
				 
					</table>
				</td>
          		 
			<tr>
		</table>
		<table class="contents">
			<tr>
				<th colspan="3" style="text-align: center"><h3> Pell no. : {{$loadCode}}</h3></th>
			</tr>
			 
            <tr>
				<th style="text-align: center">MTS No.</th>
				<th style="text-align: center">Box No.</th>
				<th style="text-align: center">QTY</th> 
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
	?>
			@foreach( $records as $doc_no )
				<tr class="font-size-13 tblrow" >
			 	
	 				<td style="text-align: center">{{$doc_no->move_doc_number}} </td>
	 				<td style="text-align: center"> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{$doc_no->box_code}}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
	 				<td style="text-align: center"> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{$doc_no->total_qty}}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>

					<?php
					//$boxcount+=1;
	 			 	$boxcount=$boxcount+1;
	 			 	$qtycount=$qtycount+$doc_no->total_qty;
	 			 	$mtscount+=1;
	 			 	?>
				</tr>
			 @endforeach
			<tr>
				 
	 				<th style="text-align: right"> Total : </th> 
	 				<th style="text-align: center"><?php echo $boxcount; ?></th>
	 				<th style="text-align: center"> <?php echo $qtycount; ?> </th>

			</tr>
		</table>
	 
<br>
	<table class="commonInfo">
			<tr>
				<td>
					<table>
					   <tr><th style="text-align: right";> Counted by:
					   <td><input type=""  placeholder=" (Whse. Sup.)"  style="border: solid 0px #000;"></td> </td>
					   </th></tr>
					   <tr><th style="text-align: right";> Loaded By/Date:
					   <td><input    style="border: solid 0px #000;"></td> </td>
					   </th></tr>
					   <tr><th style="text-align: right";> Driver:
					   <td><input    style="border: solid 0px #000;"></td> </td>
					   </th></tr>
					</table>
				</td>

				<td>
					<table>
					<tr><th style="text-align: right";> Witnessed By/Date:
					   <td><input type=""  placeholder=" (Sec. Guard)"  style="border: solid 0px #000;"></td> </td>
					   </th></tr>
					<tr><th style="text-align: right";> Rec'd Date/Time:
					   <td><input   style="border: solid 0px #000;"></td> </td>
					   </th></tr>
					<tr><th style="text-align: right";> Helper:
					   <td><input   style="border: solid 0px #000;"></td> </td>
					   </th></tr>
					</table>
				</td>
				<div >
                 <table class="contentasdfs" >
                 	
                 	<tr  >
                 		<th style="text-align: right;";>Equipment:</th>
                 		<th style="text-align: center";> Tray</th>
                 		<th style="text-align: center";> Bag</th>'

                 	</tr>
                 	<tr class="font-size-13 tblrow"  >
                 		
                 		<td style="text-align: right;"> Beginning Balance:</td>
                 		<td style="text-align: center"><input  rows="2" cols="30"> </input></td>
                 		<td style="text-align: center"><input  rows="2" cols="30"> </input></td>
                 		 
                 	</tr>
                 	<tr class="font-size-13 tblrow"  >
                 		
                 		<td style="text-align: right;">Issued to Store:</td>
                 		<td style="text-align: center"><input  rows="2" cols="30"> </input></td>
                 		<td style="text-align: center"><input  rows="2" cols="30"> </input></td>
                 		 
                 	</tr>
                 	<tr class="font-size-13 tblrow"  >
                 		
                 		<td style="text-align: right;">Additional (from Warehouse):</td>
                 		<td style="text-align: center"><input  rows="2" cols="30"> </input></td>
                 		<td style="text-align: center"><input  rows="2" cols="30"> </input></td>
                 		 
                 	</tr>
                 	<tr class="font-size-13 tblrow"  >
                 		
                 		<td style="text-align: right;" >Ending Balance:</td>
                 		<td style="text-align: center"><input  rows="2" cols="30"> </input></td>
                 		<td style="text-align: center"><input  rows="2" cols="30"> </input></td>
                 		 
                 	</tr>
                  
                 </table>
                 </div>
			<tr>
		</table>
	</section>