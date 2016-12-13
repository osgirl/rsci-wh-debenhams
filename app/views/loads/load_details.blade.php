<!DOCTYPE html>
<html>
<head>
	<title>Hello</title>
</head>
<body>
	<div class="widget widget-table action-table">

    <div class="widget-header"> <i class="icon-th-list"></i>
      <h3>Loading Details</h3>
    </div>
    <!-- /widget-header -->
	<div class="widget-content">
	   <div class="row-fluid stats-box">
	      	<div class="span4">
	        	<div>
		        	<span class="left-pane">Load Number :</span>
		        	<span class="left-pane"><input  type="textfield" disabled value=" "> </input>
		        	
		        	
		        </div>
		 
	        	<div>
		        	<span class="left-pane">Picker : </span>
		        	<span class="left-pane"><input  type="textfield" disabled value="  "> </input>
		        
		        	</span>
		        </div>
	      	</div>

	      	<div class="span4">
	      		<div>
		        	<span class="left-pane">Entry Date :</span>
		        	<span class="left-pane"> <input  type="textfield" disabled value=" "> </input>
				</span>
		        </div>
		       <!-- <div>
		        	<span class="left-pane">Ship by Date :</span>
		        	@if($is_shipped == "0000-00-00 00:00:00")
		        		<span class="left-pane">{{Form::text('is_shipped', 'Not Available', array('readonly'=>'readonly')) }}</span>
		        	@else
		        		<span class="left-pane">{{Form::text('is_shipped', date("M d, Y", strtotime($is_shipped)), array('readonly'=>'readonly')) }}</span>
		        	@endif
		        </div> -->
	      </div>

	      

	   </div>
	 </div>
</div>
<div class="clear">
	<div class="div-paginate">
		 
		    <h6 class="paginate">
				<span> &nbsp;</span>
			</h6>
		 
			&nbsp;
		 
	</div>


<div class="widget widget-table action-table">
    <div class="widget-header"> <i class="icon-th-list"></i>
      <h3>Loading Content</h3>
      <span class="pagination-totalItems"> </span>
    </div>
    <!-- /widget-header -->
    <div class="widget-content">
    	<div class="table-responsive">
			<table class="table table-striped table-bordered">
				<thead>
					<tr>
						
					<!--	<th style="width: 20px;" class="align-center"><input type="checkbox" id="main-selected" /></th> -->
				
						<th>No.</th>
						<th><a href="{{$sort_box_code}}" class="@if($sort=='box_code'){{$order }} @endif">Box Number</a></th>
						<th>TL Number</th>
						<th>Store</th>
										
					</tr>
				</thead>
				 
				<tr>
							<td>   </td>
							<td> </td>
							<td> </td>
							<td> </td>
			 
				</tr>
				
			</table>
		</div>
	</div>

</body>
</html>