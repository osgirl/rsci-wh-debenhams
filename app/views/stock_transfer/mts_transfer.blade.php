 
 

<div class="control-group">
	<div class="controls">
		<div class="accordion" id="accordion2">
          <div class="accordion-group" style="background-color: #FFFFFF;">
      
            <div id="collapseOne" class="accordion-body collapse in" style="padding-top: 20px;">
	                <div class="span4">
			        	<div>
				        	<span class="search-po-left-pane">MTS # :</span>
				        	<span class="search-po-left-pane">
				        	 <input type="text" name="">
				        	</span>
				        		 	<div>
				        	<span class="search-po-left-pane">Store Name : </span>
				        	<span class="search-po-left-pane">
				        	 <input type="text" name="">
				        	</span>
				        </div>
				        </div>
			
				       
		

			      	</div>
			    
			      	<div class="span3">
			  
				
			      	<div>
				        	<span class="search-po-right-pane">TL Number :</span>
				        	<span class="search-po-left-pane">
				        	 <input type="text" name="">
				        	</span>

				        </div>
				       
				  
			      	</div>
			      		<div class="span3">
			  
				
			     
				       
				  
			      	</div>
			      	<div class="span11 control-group collapse-border-top">
			      		<a class="btn btn-success btn-darkblue" id="submitForm"> Search </a>
		      			<a class="btn" id="clearForm">Clear Filter  </a>
			      	</div>
            </div>
           
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
 
</div>

<div class="widget widget-table action-table">
    <div class="widget-header"> <i class="icon-th-list"></i>
      <h3> Stocktransfer list </h3>
      <span class="pagination-totalItems"> </span>
    </div>
    <!-- /widget-header -->
    <div class="widget-content">
    	<div class="table-responsive">
			<table class="table table-striped table-bordered">
				<thead>
					<tr>
			 
						<th>No  </th>
						<th><a href=" " class=" "> MTS # </a></th>
						<th> Ship Date</th>
						<th> Status</th>
		 
						
						 <th> Action </th>
					 
						 
					</tr>
				</thead>
			 
				<tr class="font-size-13">
					<td colspan="13" style="text-align: center;"> </td>
				</tr>
			 
					<tr class="font-size-13 tblrow" data-id="
					">

						<td></td>
						<td><a href=" "> </a></td>
						<td> </td>		
						<td> </td>
						<td> </td>
					 
					</tr>
				 
			</table>
		</div>
	</div>
    <h6 class="paginate">
		<span> </span>
	</h6>

	<div id="myModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
 
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			<h3 id="myModalLabel"> </h3>
  		</div>
  		<div class="modal-body add-piler-wrapper">
			<div class="control-group">
				<label class="control-label" for="po_no"> </label>
				<div class="controls">
				 
				</div>  
			</div>  

			<div class="control-group piler-block">
				<label class="control-label" for="stock_piler"> </label>
				<div class="controls">	 
					<a class="add-piler-btn"><i class="icon-plus-sign" style="font-size: 1.5em;"></i></a>
				</div>  
			</div>  
  		</div>
  		<div class="modal-footer">
  			<button class="btn btn-primary" id="btn-assign"> </button>
			<button class="btn" data-dismiss="modal" aria-hidden="true"> </button>
  		</div>
  	 
	</div>
 
</div>

 
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
        <button type="button" class="btn btn-primary" id="closePOModalButton"> </button>
      </div>
    </div> 
</div>

 