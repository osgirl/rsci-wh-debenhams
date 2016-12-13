<style>
@page {
       
        page-break-after: always;
    }
/* ///@page { size: a4 landscape; } or @page { size: 360pt 360pt; }*/
@media print {
    .soContainer {page-break-after: always; page-break-inside: avoid;   width: 9in;
        height: 9in;}
    #actionButtons {display: none;}

   	 
     
}
@media screen {
    #mainContainer {width: 780px; }

}
body { font: normal 12px arial; margin: 0; counter-reset:pageNumber;}
table {padding: 0; border-collapse: collapse;}
h1 {margin-bottom: 5px;}
header {margin-bottom: 20px;}

.soContainer { padding: 10px;}
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
.asdf{
	.padding-left: 30px;
}
    .rotated_vertical {
        -webkit-transform:rotate(270deg);
        -moz-transform:rotate(270deg);
        -ms-transform:rotate(270deg);
        -o-transform:rotate(270deg);
        transform:rotate(270deg);
        transform-origin: 50%;
        width: 70%; 
    }
</style>
<div id="actionButtons">
	<a href="#" onclick="window.print();">PRINT THIS</a>
	<a href="{{$url_back}}">BACK TO SUBLOC PICKING</a>

</div>
	<?php
		$boxarray=[];
		$tempboxarray=[];
		$counter=0;

			
	?>
 
		 
	<section class="soContainer  "  >
 
      
 @foreach( $records as $asdf )
			<div class="rotated_vertical" style="width:350px; height:350px; border: solid 1px #000; padding: 0px;" >
			<h1 style="text-align: center; margin-bottom: 0px;">RSCI Package Slip</h1>
			   <p style="text-align: center; margin-top: 1px; margin-bottom: 1px"> <?php echo '<img src="data:image/png;base64,' . DNS1D::getBarcodePNG("$asdf->box_code", "C128A",1,40) . '" alt="barcode"   />'; ?></p> <div style="text-align: center; margin-top: 1px; " > {{$asdf->box_code}}</div>
			  
		  
					  <table class="contents"  >
		<tr>
			<th  >
				From :  8001 - Warehouse
			</th>
			 
			<th >
				Ship Date : 
								@if($asdf->ship_date != Null )
									{{ date("M d, Y",strtotime($asdf->ship_date)) }}
								@else 
									'No Date found'
								@endif
			</th>
		</tr>
		 <tr >
			<th  >
				{{$col_to_label_print}}  {{$asdf->store_code}} - {{$asdf->store_name}}
			</th>
		 <th>Total Qty: {{$asdf->qty}}</th>
		</tr>
		</table>
	 
	</table>  
				<table class="contents">
					<tr>
						 
					 <TD>MTS No.:{{$asdf->MASTER_EDU}}</TD> 

						
					</tr>
<tr>
			
		</tr>
				</table>

		

			</div>
			<div>


	 
			</div>

 <div style="page-break-after: always; margin-top: 40px;">
 	

 </div>
	 	@endforeach
		
	</section>
 


