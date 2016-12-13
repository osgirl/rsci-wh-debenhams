 

<div class="control-group">
	<div class="controls">
		<div class="accordion" id="accordion2">
          <div class="accordion-group" style="background-color: #FFFFFF;">
            {{ Form::open(array('url'=>'stocktransfer/PickAndPackStore', 'class'=>'form-signin', 'id'=>'form-pick-list', 'role'=>'form', 'method' => 'get')) }}
            <div id="collapseOne" class="accordion-body collapse in" style="padding-top: 20px;">

			      	<div class="span5">
			      		<div>
				        	<span class="search-po-left-pane">MTS no.:</span>
				        	<span class="search-po-right-pane">
				        		{{ Form::text('filter_doc_no', $filter_doc_no, array('class'=>'login', 'placeholder'=>'', 'id'=>"filter_doc_no")) }}
				        	</span>
				        </div>

				        <div>
				        	<span class="search-po-left-pane">{{ $label_status }}</span>
				        	<span class="search-po-right-pane">
				        		{{ Form::select('filter_status', array('' => $text_select) + $pl_status_type, $filter_status, array('class'=>'select-width', 'id'=>"filter_status")) }}
				        	</span>
				        </div>
			 
				    </div>

				    <div class="span5">
				       
				        <div>
				        	<span class="search-po-left-pane">{{ $label_store }}</span>
				        	<span class="search-po-left-pane">
				        		{{ Form::select('filter_store', array('' => $text_select) + $stores, $filter_store, array('class'=>'select-width', 'id'=>"filter_store")) }}
				        	</span>
				        </div>
				      
				     <div>
				        	<span class="search-po-left-pane"> {{$label_store_to}}</span>
				        	<span class="search-po-left-pane">
				        		{{ Form::select('filter_store_name', array('' => $text_select) + $po_info, $filter_store_name, array('class'=>'select-width', 'id'=>"filter_store_name")) }}
				        	</span>
				        </div>
				    </div>
				     <div class="span5">
				       
				        
				        <div>
				        	<span class="search-po-left-pane"> {{$label_picker}}</span>
				        	<span class="search-po-right-pane">
				        		{{ Form::select('filter_stock_piler', array('' => $text_select) + $stock_piler_list, $filter_stock_piler, array('class'=>'select-width', 'id'=>"filter_stock_piler")) }}
				        	</span>
				        </div>
				    
				    </div>
  			 
				    
			      	<div class="span11 control-group collapse-border-top">
			      		<a class="btn btn-success btn-darkblue" id="submitForm">{{ $button_search }}</a>
		      			<a class="btn" id="clearForm">{{ $button_clear }}</a>
			      	</div>
            </div>
            {{ Form::hidden('sort', $sort) }}
		    {{ Form::hidden('order', $order) }}

            {{ Form::close() }}
          </div>
      	</div>

	</div> <!-- /controls -->
</div> <!-- /control-group -->

<div class="clear">
	<div class="div-paginate">
		@if(CommonHelper::arrayHasValue($picklist) )
		    <h6 class="paginate">
				<span>{{ $picklist->appends($arrFilters)->links() }}&nbsp;</span>
			</h6>
		@else
			&nbsp;
		@endif
	</div>
	<div class="div-buttons">
	 	<a role="button" class="btn btn-info btn-darkblue assignPicklist" title="{{ $button_assign_to_stock_piler }}" data-toggle="modal">Assign Stock Piler</a>

<a   role="button" class="btn btn-info btn-darkblue" href={{URL::to('stock_transfer/PickingTLnumbersync')}}> Sync to Mobile</a> 

             <!-- <a href="{{$url_export}}" class="btn btn-info btn-darkblue">Report</a> -->
</div>
 
	 
 
<div class="widget widget-table action-table">
    <div class="widget-header"> <i class="icon-th-list"></i>
      <h3> Subloc Transfer Picking List</h3>
      <span class="pagination-totalItems">{{ $text_total }} {{ $picklist_count }}</span>
    </div>
    <!-- /widget-header -->
    <div class="widget-content">
    	<div class="table-responsive">
			<table class="table table-striped table-bordered">
				<thead >
				 
					<th style="width: 20px;" class="align-center"><input type="checkbox" id="main-selected" /></th>
				 
					<th>{{ $col_no }}</th>
					<th >MTS no.</a></th>
					<th  >Ship Date ( Y : M : D )</th>
					<th  >from  </th>
					<th  >To  </th>
					<th  >Piler Name</th>
					
					<th  class="align-center"> {{ $col_action }}</th>
				</thead>
			 
				 @if( !CommonHelper::arrayHasValue($picklist) )
					<tr class="font-size-13">
						<td colspan="10" style="text-align: center;">{{ $text_empty_results }}</td>
					</tr>
				@else
					@foreach( $picklist as $asdf )
						<tr class="font-size-13 tblrow" data-id="{{ $asdf['move_doc_number'] }}" >
						 
							<td class="align-center">
								 @if ( $asdf['data_display'] != 'In Process' &&  $asdf['data_display'] != 'Done' &&$asdf['data_display'] != 'Posted')
								<input type="checkbox" class="checkbox item-selected" name="selected[]" id="selected-{{ $asdf['move_doc_number'] }}" value="{{ $asdf['move_doc_number'] }}" />
								 @endif
								 
							</td>
						 
							<td>{{ $counter++ }}</td>
					 
							<td><a href="{{$url_detail}}&picklist_doc={{$asdf['move_doc_number']}}&filter_stock_piler={{ $asdf['firstname'].' '.$asdf['lastname'] }}"> {{$asdf['move_doc_number']}}</a></td>

							<td>
						  
							{{ Form::open(array('url'=>'store_return/pickingstock', 'class'=>'form-signin', 'id'=>'form-pick-list', 'role'=>'form', 'method' => 'get')) }}
							{{ Form::hidden('move_doc_number', $asdf['move_doc_number']) }}
                       
                             {{ Form::text('filter_date_entry',  ($asdf['ship_date']), array('class'=>'form-signin', 'placeholder'=>'', 'id'=>"readonly"), ['size' => '1x1']) }}
                          
							{{ Form::close() }} 
                            <td>   {{ Store::getStoreName($asdf['from_store_code']) }}</td> 
                            <td> {{ Store::getStoreName($asdf['to_store_code']) }}</td> 
							<td> {{$asdf['firstname'].' '.$asdf['lastname']}} </td> 
							<td class="align-center">
								@if($asdf['data_display'] === 'Posted')
									<a style="width: 60px;" disabled="disabled" class="btn btn-info">{{ $text_posted }}</a>  
<a style="width: 140px;"   class="btn btn-danger" href={{URL('picking/printboxlabelstock/'.$asdf['move_doc_number'])}}>{{$print_pagkaging_slip}}</a> 	 
								@elseif ( $asdf['data_display'] === 'Done' )
								
							<a style="width: 80px;" class="btn btn-success " data-id="{{ $asdf['move_doc_number'] }}" href={{URL::TO('stock_transfer/closetlnumberpick?tl_number='.$asdf['move_doc_number'])}}>{{ $button_close_picklist }}</a>  
<a style="width: 140px;"   disabled class="btn btn-danger">{{$print_pagkaging_slip}}</a>
									 
									 
								@elseif ( $asdf['data_display'] === 'Assigned' )

								<a style="width: 70px;" disabled="disabled" class="btn btn-danger">Assigned</a>
<a style="width: 140px;"   disabled class="btn btn-danger">{{$print_pagkaging_slip}}</a>
								@elseif ( $asdf['data_display'] === 'In Process' )

								<a style="width: 78px;" disabled="disabled" class="btn btn-danger">In Process</a>
<a style="width: 140px;"   disabled class="btn btn-danger">{{$print_pagkaging_slip}}</a>
								@else
									<a style="width: 60px;" disabled="disabled" class="btn">{{ $button_close_picklist }}</a>
									
<a style="width: 140px;"   disabled class="btn btn-danger">{{$print_pagkaging_slip}}</a>
								
								@endif

								<!--    -->
						</tr>
					@endforeach
				@endif
			</table>
		</div>
	</div>

	 
</div>



<script type="text/javascript">
$(document).ready(function() {

     
    $('.date').datepicker({
      format: 'yyyy-mm-dd'
    });
 
  

	// Submit Form
    $('#submitForm').click(function() {
    	$('#form-pick-list').submit();
    });

    $('#form-pick-list').keydown(function(e) {
		if (e.keyCode == 13) {
			$('#form-pick-list').submit();
		}
	});
	// Clear Form
    $('#clearForm').click(function() {
    	$('#filter_doc_no, #filter_status, #filter_type, #filter_transfer_no, #filter_action_date').val('');
		$('select').val('');
		$('#form-pick-list').submit();
    });

	 // Select
    $('.tblrow').click(function() {
    	var rowid = $(this).data('id');

    	if ($('#selected-' + rowid).length>0) {
	    	if ($('#selected-' + rowid).is(':checked')) {
	    		$('#selected-' + rowid).prop('checked', false);
	    		$(this).children('td').removeClass('tblrow-active');
	    	} else {
	    		$('#selected-' + rowid).prop('checked', true);
	    		$(this).children('td').addClass('tblrow-active');
	    	}
    	} else {
    		$(this).children('td').removeClass('tblrow-active');
    	}
    });

    $('.item-selected').click(function() {
    	var rowid = $(this).data('id');

    	if ($(this).is(':checked')) {
    		$(this).prop('checked', false);
    		$(this).children('td').removeClass('tblrow-active');
    	} else {
    		$(this).prop('checked', true);
    		$(this).children('td').addClass('tblrow-active');
    	}
    });

    $('#main-selected').click(function() {
    	if ($('#main-selected').is(':checked')) {
    		$('input[name*=\'selected\']').prop('checked', true);
    		$('.table tbody tr > td').addClass('tblrow-active');
    	} else {
    		$('input[name*=\'selected\']').prop('checked', false);
    		$('.table tbody tr > td').removeClass('tblrow-active');
    	}
   	});

   	// Assign PO
    $('.assignPicklist').click(function() {
    	var count = $("[name='selected[]']:checked").length;

		if (count>0) {
			/*var answer = confirm('{{ $text_confirm_assign }}')*/

			if (count) {
				var doc_no = new Array();
				$.each($("input[name='selected[]']:checked"), function() {
					doc_no.push($(this).val());
				});

    			$('#doc_no').val(doc_no.join(','));

    			// http://local.ccri.com/purchase_order/assign
    			location = "assignpicking?" + '&doc_no=' + encodeURIComponent(doc_no.join(','));
			} else {
				return false;
			}
		} else {
			alert('{{ $error_assign }}');
			return false;
		}
    });

    $('.closePicklist').click(function() {
    	var doc_no = $(this).data('id');

    	var answer = confirm('Are you sure you want to close this Picklist?');
   		if (answer) {
	    	$('#closePicklist_' + doc_no).submit();
    	} else {
			return false;
		}

    });
});
</script>
