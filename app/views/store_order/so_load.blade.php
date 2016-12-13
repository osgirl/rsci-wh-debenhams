<div class="control-group">
	<div class="controls">
		<div class="accordion" id="accordion2">
			<div class="accordion-group search-panel">
			 
				<div id="collapseOne" class="accordion-body collapse in search-panel-content">
					<div class="span4">
						<div>
							<span class="search-po-left-pane"> SO Load # :</span>
							<span class="search-po-right-pane"> <input type="" name="" disabled="">
							 
							</span>
						</div>
						
					</div>
					<div class="span4">
					<div>
							<span class="search-po-left-pane"> SO Ship by :</span>
							<span class="search-po-right-pane"> <input type="" name="" disabled="">
							 
							</span>
						</div>
						</div>
					<div class="span11 control-group collapse-border-top" style="margin-top: 6px;">
						<a class="btn btn-success btn-darkblue" id="submitForm">Search </a>
						<a class="btn" id="clearForm">Clear </a>
					</div>
				</div>
				 
			</div>
		</div>
	</div> <!-- /controls -->
</div> <!-- /control-group -->

<div class="clear">
	<div class="div-paginate">
	 
		    <h6 class="paginate">
				<span> &nbsp;</span>
			</h6>
		 
			&nbsp;
		 
	</div>
	<div class="div-buttons">
	 
			 
		<a  class="btn btn-info btn-darkblue" id="generate-load">Add Load</a>
	 
	 
            <a role="button" class="btn btn-info btn-darkblue assignPicklist" title="Assign To Picker" data-toggle="modal">Assign To Stock Piler</a>
   
       
	</div>
	<a class="btn btn-info btn-darkblue" href={{URL::to('load/loadnumbersync')}}>Sync To Mobile </a>
</div>

<div class="widget widget-table action-table">
	<div class="widget-header"> <i class="icon-th-list"></i>
    	<h3> </h3>
     	<span class="pagination-totalItems"> </span>
    </div>
    <!-- /widget-header -->

    <div class="widget-content">
    	<div class="table-responsive">
			<table class="table table-striped table-bordered">
				<thead>
					<tr>
						<th style="width: 20px;" class="align-center"><input type="checkbox" id="main-selected"></th>
						<th>NO.</th>
						<th>Load Number</th>
						<th>Stock Piler</th>
						<th> Date Created </th>
						<th> Ship By Date </th>
						<th>ACTION</th>
					</tr>
				</thead>
				<tbody>
				 
					<tr class="font-size-13">
						<td colspan="5" class="align-center"> </td>
					</tr>
				 
					<tr class="font-size-13">
						<td> </td>
						<td> </td>
						
						<td> </td>
						<td> </td>
						<td> </td>
						<td>
							 
						</td>
						<td>
							 
						</td>
					</tr>
				 
				</tbody>
			</table>
		</div>
	</div>

 
    <h6 class="paginate">
		<span> </span>
	</h6>
 
</div>

 