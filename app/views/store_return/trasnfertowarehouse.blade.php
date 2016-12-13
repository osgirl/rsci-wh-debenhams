 
<!--     <div class="alert alert-danger">
    	<button class="close" data-dismiss="alert" type="button">&times;</button>
    	 error  
    </div>
 
    <div class="alert alert-success">
    	<button class="close" data-dismiss="alert" type="button">&times;</button>
    	 success  
    </div>
  -->

<div class="control-group">
	<div class="controls">
		<div class="accordion" id="accordion2">
          <div class="accordion-group" style="background-color: #FFFFFF;">
  
            <div id="collapseOne" class="accordion-body collapse in" style="padding-top: 20px;">
	                <div class="span4">
			        	<div>
				        	<span class="search-po-left-pane"> MTS No. </span>
				        	<span class="search-po-right-pane"><input type="" name="">
				        	 
				        	</span>
				        </div>
				 <!-- 
				        <div>
				        	<span class="search-po-left-pane"> label_shipment_reference_no  </span>
				        	<span class="search-po-right-pane"> <input type="" name="">
				        	</span>
				        </div> -->
			      	</div>
			      <!-- 	<div class="span4">
			      		<div>
				        	<span class="search-po-left-pane"> label_entry_date  </span>
				        	<div class="search-po-right-pane input-append date">
						 
								<span class="add-on"><i class="icon-th"></i></span>
				        	</div>
				        </div>
				 
			      	</div>
 -->			    
			      	<div class="span3">
			       
				
			      		<div>
				        	<span class="search-po-left-pane"> label_status </span>
				        	<span class="search-po-right-pane"><input type="" name="">
				        	 
				        	</span>
				        </div>
				       
				       
			      	</div>
			      	<div class="span11 control-group collapse-border-top">
			      		<a class="btn btn-success btn-darkblue" id="submitForm"> search </a>
		      			<a class="btn" id="clearForm"> clear  </a>
			      	</div>
            </div>
          
      	</div>

	</div> <!-- /controls -->
</div> <!-- /control-group -->

<div class="clear">
	<div class="div-paginate"> 
		    <h6 class="paginate">
				<span> <!-- purchase_orders->appends($arrFilters)->links &nbsp; --></span>
			</h6>
		 
	</div>
	<div class="div-buttons">
		<table>
			<tr>
				 
			     <th>
					<div class="div-buttons"> 
						<a class="btn btn-info btn-darkblue" href= > Assign to Stock Piler</a>
					 
					</div>
				</th>
			</tr>
		</table>
	</div>
</div>

<div class="widget widget-table action-table">
    <div class="widget-header"> <i class="icon-th-list"></i>
      <h3>MTS  Reverse Lists</h3>
      <span class="pagination-totalItems"> text_total   count </span>
    </div>
    <!-- /widget-header -->
    <div class="widget-content">
    	<div class="table-responsive">
			<table class="table table-striped table-bordered">
				<thead>
					<tr>
				 
						<th style="width: 20px;" class="align-center"><input type="checkbox" id="main-selected" /></th>
						<th>No.</th>
						<th><a href=" ">  MTS Receiving  </a></th>
						<th>  total quantity </th>
						<th><a href=" "> created date </a></th>
						<th> StockPiler  </th>
						<th> From Store</th>
						<th> Status </th>
					 
					</tr>
				</thead>
				 
				<tr class="font-size-13">
					<td colspan="13" style="text-align: center;"> text_empty_results  </td>
				</tr>
			 
					<tr class="font-size-13 tblrow" data-id="   ">
						 
						<td> </td>
						<td> </a></td>
						<td> </td>
									
						<td> </td>
						<td> </td>
						<td> </td>
						 
						<td class="align-center"> </td>
					</tr>
					 
			</table>
		</div>
	</div>

 
    <h6 class="paginate">
		<span> </span>
	</h6>
 

	<!-- Button to trigger modal -->
	<!-- Modal -->
	 
	<div id="myModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		 
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			<h3 id="myModalLabel">MTS Receiving List </h3>
  		</div>
  		<div class="modal-body add-piler-wrapper">
			<div class="control-group">
				<label class="control-label" for="po_no">  MTS number </label>
				<div class="controls">
					 
				</div> <!-- /controls -->
			</div> <!-- /control-group -->
 
  		</div>
  		<div class="modal-footer">
  			<button class="btn btn-primary" id="btn-assign"> button_assign  </button>
			<button class="btn" data-dismiss="modal" aria-hidden="true"> button_cancel  </button>
  		</div>
  		 
	</div>
	 
	<!-- /widget-content -->
</div>

<!--modal for close po-->
<div id="closePoModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="closePoModalLabel" aria-hidden="true">
	 <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title"> </h4>
      </div>
      <div class="modal-body">
        
        <div class="form-group">
        	 
        	<div class="col-sm-10">
        		 
        	</div>
		</div>
		</br>
		<div class="form-group">
        	 
        	<div class="col-sm-10">
         
        	</div>
		</div>
        
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" id=" ">Close PO</button>
      </div>
    </div><!-- /.modal-content -->
</div>

<!-- endi of modal for close po-->
