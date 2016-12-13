<style type="text/css">
	
.contents {margin-top: 1px; width: 100%;}
.contents th, .contents td { font-size: 10px; padding: 1px;  border: solid 1px #F0F0F0; margin: 0; }
.contents th {text-align: left; padding: 1px;}
.contents th {background-color: #F0F0F0}
</style>


<div>
	<?php
		$boxarray=[];
		$tempboxarray=[];
		$counter=0;

			
	?>
 
		 
	 
 
      
 @foreach( $records as $asdf )
			<div>
			<p style="font-size: 12px; text-align: center;">RSCI Package Slip<br>
			     <?php echo '<img src="data:image/png;base64,' . DNS1D::getBarcodePNG("$asdf->box_code", "C128A",2,50) . '" alt="barcode"   />'; ?><br>
			    {{$asdf->box_code}}</p></div>
			  
		  
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
						 
					 <TD>MTS No.: {{$asdf->MASTER_EDU}}</TD> 

						
					</tr>
<tr>
			
		</tr>
				</table>

		

			</div>
			<div>


	 
			</div>

 <div >
 	

 </div>
	 	@endforeach
		
 </div>
 


