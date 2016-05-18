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
		        	<span class="left-pane"><input  type="textfield" disabled value="{{$load_code}}"> </input>
		        	
		        	
		        </div>
		 
	        	<div>
		        	<span class="left-pane">Picker : </span>
		        	<span class="left-pane"><input  type="textfield" disabled value=" {{$filer}}"> </input>
		        
		        	</span>
		        </div>
	      	</div>

	      	<div class="span4">
	      		<div>
		        	<span class="left-pane">Entry Date :</span>
		        	<span class="left-pane"> <input  type="textfield" disabled value="{{$date_at}}"> </input>
				</span>
		        </div>
		       <div>
		        	<span class="left-pane">Ship by Date :</span>
		        	@if($is_shipped == "0000-00-00 00:00:00")
		        		<span class="left-pane">{{Form::text('is_shipped', 'Not Available', array('readonly'=>'readonly')) }}</span>
		        	@else
		        		<span class="left-pane">{{Form::text('is_shipped', date("M d, Y", strtotime($is_shipped)), array('readonly'=>'readonly')) }}</span>
		        	@endif
		        </div>
	      </div>

	      

	   </div>
	 </div>
</div>
<div class="clear">
	<div class="div-paginate">
		@if(CommonHelper::arrayHasValue($BigBoxes)) 
		    <h6 class="paginate">
				<span>{{$BigBoxes->appends($arrFilters)->links()}}&nbsp;</span>
			</h6>
		@else
			&nbsp;
		@endif
	</div>


<div class="widget widget-table action-table">
    <div class="widget-header"> <i class="icon-th-list"></i>
      <h3>Loading Content</h3>
      <span class="pagination-totalItems"><!--{{$boxes_count}}--></span>
    </div>
    <!-- /widget-header -->
    <div class="widget-content">
    	<div class="table-responsive">
			<table class="table table-striped table-bordered">
				<thead>
					<tr>
						
					<!--	<th style="width: 20px;" class="align-center"><input type="checkbox" id="main-selected" /></th> -->
					
						<th>No.</th>
						<th>Box Number</th>
						<th>TL Number</th>
						<th>Store</th>
										
					</tr>
				</thead>
				@foreach($BigBoxes as $boxYong)
				<tr>
							<td>  {{ $counter++ }}</td>
							<td><a href="box_content?box_code={{$boxYong['box_code']}}">{{$boxYong['box_code']}}</a></td>
							<td>{{$boxYong['tl_number']}}</td>
							<td>{{$boxYong['store_name']}}</td>
				@endforeach
				</tr>
				
			</table>
		</div>
	</div>

</body>
</html>