 
<div class="control-group">
<h2><span class="label label-important" style="font-size: 15px; font-weight: normal;">Notes: Please use filter first before using the Report button.</span></h2>
	<div class="controls">
		<div class="accordion" id="accordion2">
          <div class="accordion-group" style="background-color: #FFFFFF;">
         {{ Form::open(array('url'=>'stock_transfer/discrepansymts', 'class'=>'form-signin', 'id'=>'form-purchase-order', 'role'=>'form', 'method' => 'get')) }}
            <div id="collapseOne" class="accordion-body collapse in" style="padding-top: 20px;">
	                <div class="span4">
			        	<div>
				        	<span class="search-po-left-pane"> MTS number </span>
				        	<span class="search-po-right-pane"> 
				        	 {{ Form::text('filter_doc_no', $filter_doc_no, array('class'=>'login', 'placeholder'=>'', 'id'=>"filter_doc_no")) }}
				        	</span>
				        </div>
			 
			      	</div>
			   	<div class="span4">
			 
                          <!--   <span class="search-po-left-pane"> Date Entry:</span>
                            <div class="search-po-right-pane input-append date">
                                {{ Form::text('filter_date_entry', $filter_date_entry, array('class'=>'span2', 'id'=>"filter_date_entry", 'readonly'=>'readonly')) }}
                                <span class="add-on"><i class="icon-th"></i></span>
                            </div> -->
                        </div>
			    
			      	<div class="span3">
			      
			      	</div>
			      	<div class="span11 control-group collapse-border-top">
			      		<a class="btn btn-success btn-darkblue" id="submitForm">{{ $button_search }}</a>
		      			<a class="btn" id="clearForm">{{ $button_clear }}</a>
			      	</div>
            </div>
    
          </div>
      	</div>

	</div> <!-- /controls -->
</div> <!-- /control-group -->

<div class="clear">
	<div class="div-paginate">
		@if(CommonHelper::arrayHasValue($stocktranferdisc) )
		    <h6 class="paginate">
				<span>{{ $stocktranferdisc->appends($arrFilters)->links() }}&nbsp;</span>
			</h6>
		@else
			&nbsp;
		@endif
	</div>
 
				<th>
					
			      	</div>
			      	<div class="btn-group div-buttons">
				        <button type="button" class="btn btn-info btn-darkblue dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Report <span class="caret"></span>
				        </button>
				        <ul class="dropdown-menu">
				          <li><a id="exportlistpdf">Export pdf</a></li>
					          <li><a id="exportlistexcel">Export excel</a></li>
				        </ul>
			      	</div>
			     </th>
			</tr>
		</table>
	</div>
	
</div>

<div class="widget widget-table action-table">
    <div class="widget-header"> <i class="icon-th-list"></i>
      <h3></h3>
      <span class="pagination-totalItems"> </span>
    </div>
 
    <div class="widget-content">
    	<div class="table-responsive">
			<table class="table table-striped table-bordered">
				<thead>
					<tr>
				 		<th>No.</th>
						<th>MTS no. </th>
						<th>from </th>
						<th>to </th>
						<th>SKU</th>
						<th>UPC</th>
						<th>Short Name</th>
					 	<th>Piler Name</th>
					 	<th>Date Entry</th>
						<th>Variances</th>
		 
						 
					</tr>
				</thead>
				 
				    @if( !CommonHelper::arrayHasValue($stocktranferdisc) )
                    <tr class="font-size-13">
                        <td colspan="10" style="text-align: center;">{{ $text_empty_results }}</td>
                    </tr>
                @else
                    @foreach( $stocktranferdisc as $value )
					<tr class="font-size-13 tblrow">
				 	
		 				<td>{{$counter++}}</td>
		 			 	<td>{{$value->so_no}}</td>
		 			 	<td>{{$value->store_name}}</td>
		 			 	<td>{{$value->to_store_code}}</td>
		 			 	<td>{{$value->sku}}</td>
		 			 	<td>{{$value->upc}}</td>
		 			 	<td>{{$value->short_name}}</td>
		 			 	<td>{{$value->firstname.''.$value->lastname}}</td>
		 			 	<td>{{$value->created_at}}</td>
		 			 	<td>{{$value->received_qty - $value->delivered_qty}}</td>
		 			 
					</tr>
					@endforeach
					@endif
				 
			</table>
		</div>
	</div>

 
 
 

 <script type="text/javascript">
$(document).ready(function() {

  $('.date').datepicker({
      format: 'yyyy-mm-dd'
    });

     $('#submitForm').click(function() {
    	$('#form-purchase-order').submit();
    });

    $('#form-purchase-order input').keydown(function(e) {
		if (e.keyCode == 13) {
			$('#form-purchase-order').submit();
		}
	});
	 $('#clearForm').click(function() {
    	$('#filter_doc_no, #filter_entry_date, #filter_supplier, #filter_shipment_reference_no').val('');
		$('#filter_entry_date, #filter_back_order').val('');

		$('select').val('');
		$('#form-purchase-order').submit();
    });

  $('#exportlistexcel').click(function() {
    	url = '';

		var filter_doc_no = $('#filter_doc_no').val();
		url += '?filter_doc_no=' + encodeURIComponent(filter_doc_no);
 
	 
	 

      	location = "{{ $url_exportexcel }}" + url;
    });
  $('#exportlistpdf').click(function() {
    	url = '';

		var filter_doc_no = $('#filter_doc_no').val();
		url += '?filter_doc_no=' + encodeURIComponent(filter_doc_no);
 
	 
	 

      	location = "{{ $url_exportpdf }}" + url;
    });
	 
  });
</script>