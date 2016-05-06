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
		        	<span class="left-pane">{{Form::text('loadNumber', $Contentbox->load_code, array('readonly'=>'readonly')) }}
		        	
		        	
		        </div>
		 
	        	<div>
		        	<span class="left-pane">Picker : </span>
		        	<span class="left-pane">{{Form::text('stockpiler', $Contentbox->firstname.' '. $Contentbox->lastname, array('readonly'=>'readonly')) }}
		        	</span>
		        </div>
		        <div>
		        	<span class="left-pane">Box Number : </span>
		        	<span class="left-pane"><input  type="textfield" disabled value="{{ $box_code }}"> </input>
		        	</span>
		        </div>
		        
		     <!--    <div>
		        	<span class="left-pane">TL Number : </span>
		        	<span class="left-pane">
		        	{{Form::text('tl_number', $Contentbox->box_code, array('readonly'=>'readonly')) }}


		        	</span>
		        </div>-->
	      	</div>

	      	<div class="span4">
	      		<div>
		        	<span class="left-pane">Date Created :</span>
		        	<span class="left-pane">{{Form::text('created_at',date("M d, Y", strtotime( $Contentbox->created_at)), array('readonly'=>'readonly')) }} </span>
		        </div>
		       <div>
		        	<span class="left-pane">Ship by Date :</span>
		        	@if($Contentbox->updated_at == "0000-00-00 00:00:00")
		        		<span class="left-pane">{{Form::text('is_shipped', 'Not Available', array('readonly'=>'readonly')) }}</span>
		        	@else
		        		<span class="left-pane">{{Form::text('is_shipped', date("M d, Y", strtotime($Contentbox->updated_at)), array('readonly'=>'readonly')) }}</span>
		        	@endif
		        </div>
		        <div>
		        	<span class="left-pane">Store : </span>
		        	<span class="left-pane">{{Form::text('is_shipped', $Contentbox->store_name, array('readonly'=>'readonly')) }}
		        	</span>
		        </div>
	      </div>

	      

	   </div>
	 </div>
</div>

<div class="clear">
	<div class="div-paginate">
		@if(CommonHelper::arrayHasValue($boxesYong) )
		    <h6 class="paginate">
				<span>{{ $boxesYong->appends($arrFilters)->links() }}&nbsp;</span>
			</h6>
		@else
			&nbsp;
		@endif
	</div>

<div class="widget widget-table action-table">
    <div class="widget-header"> <i class="icon-th-list"></i>
      <h3>Loading Content</h3>
       <span class="pagination-totalItems"></span>
    </div>
    <!-- /widget-header -->
    <div class="widget-content">
    	<div class="table-responsive">
			<table class="table table-striped table-bordered">
				<thead>
					<tr>
						
					<!--	<th style="width: 20px;" class="align-center"><input type="checkbox" id="main-selected" /></th> -->
					
						<th>No.</th>
						<th><a href=""> UPC</a></th>
						<th><a href=""> Short Description</a></th>
						<th>Box Code</th>
						<th><a href="">Quantity</a></th>
					
						
					</tr>
				</thead>
				
			
			@foreach($boxesYong as $boxYong)
				<tr>
							<td>{{ $counter++ }}</td>
							<td>{{$boxYong->sku}} </td>
							<td>{{$boxYong->short_description}}</td>
							<td>{{$boxYong->box_code}}</td>
							<td>{{$boxYong->moved_qty}}</td>
				@endforeach
		
					</tr>
				
			</table>
		</div>
	</div>

</body>
</html>