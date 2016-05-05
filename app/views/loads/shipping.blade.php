
<div class="control-group">
	<div class="controls">
		<div class="accordion" id="accordion2">
          <div class="accordion-group" style="background-color: #FFFFFF;">
            {{ Form::open(array('url'=>'shipping/list', 'class'=>'form-signin', 'id'=>'form-shipping', 'role'=>'form', 'method' => 'get')) }}
            <div id="collapseOne" class="accordion-body collapse in" style="padding-top: 20px;">
	               
			      	<div class="span4">
			      		<div>
				        	<span class="search-po-left-pane">Load Number :</span>
				        	<span class="search-po-right-pane">
				        		{{ Form::text('filter_load_code', $filter_load_code , array('class'=>'login', 'placeholder'=>'', 'id'=>"filter_load_code")) }}
				        	</span>
				        </div>
				
                        <div>
                            <span class="search-po-left-pane">Assign To :</span>
                            <span class="search-po-right-pane">
                                {{ Form::select('filter_assigned_to_user_id', array('' => 'Please Select') + $stock_piler_list, $filter_stock_piler, array('class'=>'select-width', 'id'=>"filter_stock_piler")) }}
                            </span>
                        </div>
                    </div>
                    <div class="span4">
			      		<div>
				        	<span class="search-po-left-pane">Ship By :</span>
				        	<div class="search-po-right-pane input-append date">
								{{ Form::text('filter_entry_date', $filter_entry_date, array('class'=>'span2', 'id'=>"filter_entry_date", 'readonly'=>'readonly')) }}
								<span class="add-on"><i class="icon-th"></i></span>
				        	</div>
				        </div>
			      	</div>

			      	<div class="span11 control-group collapse-border-top">
			      		<a class="btn btn-success btn-darkblue" id="submitForm">Search</a>
		      			<a class="btn" id="clearForm">Clear</a>
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
		@if(CommonHelper::arrayHasValue($load_list) )
		    <h6 class="paginate">
				<span>{{ $load_list->appends($arrparam)->links() }}&nbsp;</span>
			</h6>
		@else
			&nbsp;
		@endif
	</div>
	<div class="div-buttons">
		{{-- @if ( CommonHelper::valueInArray('CanAccessBoxingLoading', $permissions) ) --}}
		<a  class="btn btn-info btn-darkblue" id="generate-load">Add Load</a>
		{{-- @endif --}}
		@if ( CommonHelper::valueInArray('CanExportPacking', $permissions) )
            <a role="button" class="btn btn-info btn-darkblue assignPicklist" title="Assign To Picker" data-toggle="modal">Assign To Stock Piler</a>

        @endif


	</div>
</div>
<div class="widget widget-table action-table">
	<div class="widget-header"> <i class="icon-th-list"></i>
	 <h3>Load Lists</h3>
      <span class="pagination-totalItems">{{$list_count}}</span>
	</div>

	<div class="widget-content">
		<div class="table-responsive">
			<table class="table table-striped table-bordered">
				<thead>
					<tr>
						<th style="width: 20px;" class="align-center"><input type="checkbox" id="main-selected"></th>
						<th>NO.</th>
						<th><a href="{{ $sort_load_code}}" class="@if( $sort=='load_code' ) {{ $order }} @endif">Load Number</a></th>
						<th>Stock Piler</th>
						<th><a href="{{ $sort_date_created}}" class="@if( $sort=='created_at' ) {{ $order }} @endif">Date Created</a></th>
						<th><a href="{{ $sort_ship_at}}" class="@if( $sort=='ship_at' ) {{ $order }} @endif">Ship By Date</a></th>
						<th>ACTION</th>
					</tr>
				</thead>
				@if( !CommonHelper::arrayHasValue($load_list) )
					<tr class="font-size-13">
						<td colspan="13" style="text-align: center;">No Results Found</td>
					</tr>
				@else
					@foreach( $load_list as $lo)
						<tr class="font-size-13 tblrow" data-id="{{ $lo->load_code }}">
						<td class="align-center">
						@if( $lo->is_shipped == 0 )
							<input type="checkbox" class="checkbox item-selected" name="selected[]" id="selected-{{ $lo->load_code }}" value="{{ $lo->load_code }}" /> 
						@endif
						</td>
						<td>{{ $counter++ }}</td>
						<td><a href="{{ Url::to('load/load_details?load_code='.$lo->load_code.'&filer='.$lo->firstname.' '.$lo->lastname.'&date_at='.date("M d, Y",strtotime($lo->created_at)).'&shipped='.$lo->ship_at) }}"> {{ $lo->load_code }}</a></td>
						<td>{{ $lo->firstname.' '.$lo->lastname }}</td>
						<td>{{ date("M d, Y",strtotime($lo->created_at)) }}</td>
						<td>
								@if($lo->ship_at!='0000-00-00 00:00:00')
									{{ date("M d, Y",strtotime($lo->ship_at)) }}
								@endif
						</td>
						<td>
							@if( $lo->is_shipped == 0 )
								@if ( CommonHelper::valueInArray('CanAccessShipping', $permissions))
								<a class="btn btn-info shipLoad" data-id="{{ $lo->id }}">Shipped</a>
						  		{{ Form::open(array('url','id' => 'formLoadShip_' . $lo->id, 'style' => 'margin: 0px;display:none;', 'method'=> 'post')) }}
									{{ Form::hidden('load_code', $lo->load_code) }}
									{{ Form::hidden('id', $lo->id) }}
						  		{{ Form::close() }}
								@endif
							@else
								<a disabled class="btn btn-danger">Shipped</a>

							@endif
							
							&nbsp;&nbsp;<a href="" target="_blank" class="btn btn-info">Print MTS</a>
							
						</td>
					@endforeach
				@endif
			</table>
		</div>
	</div>	
</div>

<div id="add-load-modal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="load-boxes-modal-label" aria-hidden="true">
	 <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title">New Load Code</h4>
      </div>
      <div class="modal-body">
      	{{ Form::open(array('role'=> 'form', "class"=> "form-horizontal", "id"	=> 'add-load-form'))}}
      	{{ Form::close()}}
       	<span id="load-code-created"></span>
      </div>
      <div class="modal-footer">
      	  <button type="button" class="btn btn-default" id="close-add-load" >Close</button>
      </div>
    </div><!-- /.modal-content -->
</div>





<script type="text/javascript">

$(document).ready(function()
{


    $('#submitForm').click(function() {
    	$('#form-shipping').submit();
    });

    $('#clearForm').click(function() {
    	$('#filter_load_code, #filter_stock_piler,#filter_entry_date').val('');

		$('#form-shipping').submit();
    });

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

    $('.date').datepicker({
      format: 'yyyy-mm-dd'
    });

    $('#generate-load').click(function() {
	var token = $('#add-load-form input[name="_token"]').val();
	  $.ajax({
	      url: "box/new/load",
	      type: "POST",
	      data: {'_token': token},
	      success: function(response){
	      	response = JSON.parse(response);
	      	$('#load-code-created').html('You have generated ' + response.load_code);
	        $('#add-load-modal').modal('show');
	      }
	   });
    });

    $('.assignPicklist').click(function() {
    var count = $("[name='selected[]']:checked").length;

    if (count>0) {
        var answer = confirm('Assign Selected Loads?');
        if (answer) {
			var boxes = new Array();
			$.each($("input[name='selected[]']:checked"), function() {
				boxes.push($(this).val());
			});
			$('#box-codes').val(boxes.join(','));

            location = "assigned?" + '&load_code=' + encodeURIComponent(boxes.join(','));

		} else {
			return false;
		}
    } else {
        alert('Please Choose Load/s');
        return false;
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
});
</script>