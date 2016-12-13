
<div class="control-group">
	<div class="controls">
		<div class="accordion" id="accordion2">
          <div class="accordion-group" style="background-color: #FFFFFF;">
            {{ Form::open(array('url'=>'load/shipping', 'class'=>'form-signin', 'id'=>'form-shipping', 'role'=>'form', 'method' => 'get')) }}
            <div id="collapseOne" class="accordion-body collapse in" style="padding-top: 20px;">
	               
			      	<div class="span4">
			      		<div>
				        	<span class="search-po-left-pane">{{$col_pell_no_label}}</span>
				        	<span class="search-po-right-pane">
				        		{{ Form::text('filter_load_code', $filter_load_code , array('class'=>'login', 'placeholder'=>'', 'id'=>"filter_load_code")) }}
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
		<a  class="btn btn-info btn-darkblue" id="generate-load">{{$button_gnerteload}}</a>
		
	 
            <a role="button" class="btn btn-info btn-darkblue assignPicklist" title="Assign To Picker" data-toggle="modal">Assign To Stock Piler</a>

	 
			
	<a class="btn btn-info btn-darkblue" href={{URL::to('load/loadnumbersync')}}>Sync To Mobile </a>
	{{-- @endif --}}

	</div>
	
</div>
<div class="widget widget-table action-table">
	<div class="widget-header"> <i class="icon-th-list"></i>
	 <h3>{{$header_pell_list}}</h3>
      <span class="pagination-totalItems">{{$list_count}}</span>
	</div>

	<div class="widget-content">
		<div class="table-responsive">
			<table class="table table-striped table-bordered">
				<thead>
					<tr>
						<th style="width: 20px;" class="align-center"><input type="checkbox" id="main-selected"></th>
						<th>NO.</th>
						<th>{{$pell_no_th}}</th>
						<th>Stock Piler</th>
						<th> Date Created </th>
						<th> Ship By Date </th>
						<th class="align-center">ACTION</th>
					</tr>
				</thead>
				@if( !CommonHelper::arrayHasValue($load_list)  )
					<tr class="font-size-13">
						<td colspan="13" style="text-align: center;">No Results Found</td>
					</tr>
				@else
					@foreach( $load_list as $lo)

					<tr class="font-size-13 tblrow" data-id="{{ $lo->load_code }}">
					<td class="align-center">
						@if( $lo->is_shipped == 0 && $lo->data_value != 1)
							<input type="checkbox" class="checkbox item-selected" name="selected[]" id="selected-{{ $lo->load_code }}" value="{{ $lo->load_code }}" /> 
						@endif
					</td>
					<td>{{ $counter++ }}</td>
					<td> <a href="{{URL::to('load/boxdetails?loadnumber='.$lo->load_code.'&pilername='.$lo->firstname.' '.$lo->lastname.'&filter_data_value='.$lo->data_value)}}">{{ $lo->load_code }}</a></td>
					<td>{{ $lo->firstname.' '.$lo->lastname }}</td>
					<td>{{ date("M d, Y",strtotime($lo->created_at)) }}</td>
					<td>
								@if($lo->ship_at != Null	)
									{{ date("M d, Y",strtotime($lo->ship_at)) }}
								@else 
									'Not yet Available'
								@endif
					</td>
					<td>	

					@if( $lo->is_shipped == 1 &&  $lo->assigned_to_user_id != 0 && $lo->assigned_by != 0 && $lo->data_value == 1)
							@if ( CommonHelper::valueInArray('CanAccessShipping', $permissions))
							<a style="width: 60px;" class="btn btn-info" href="{{URL::to('load/shipLoad?loadnumber='.$lo->load_code)}}">Ship</a>
						    		<a disabled class="btn btn-info">{{$col_A_loNum}}</a>
									<a disabled class="btn btn-danger">{{$col_load_sheet}}</a> 
									<a disabled  class="btn btn-danger">{{$col_mts_report}}</a>
							@endif

					
					@endif

					@if($lo->is_shipped == 2 )
					<a disabled class="btn btn-info">Shipped</a>
					<a class="btn btn-info" disabled>{{$col_A_loNum}}</a>
					<a href="{{url('load/printloadingsheet/' . $lo->load_code)}}" target="_blank" class="btn btn-danger">{{$col_load_sheet}}</a> 
					 <a href="{{url('load/printpacklist/' . $lo->load_code)}}" target="_blank" class="btn btn-danger">{{$col_mts_report}}</a>


					 @endif
			 

				 
					
					@if( $lo->is_shipped == 0 )
			 		<a style="width: 60px;" disabled class="btn btn-default" >Ship</a>
							@if ($lo->data_value == 0)
							<a href="{{URL::to('load/loadnumber?loadnumber='.$lo->load_code.'&pilername='.$lo->firstname.' '.$lo->lastname.'&created_at='.date('M d, Y',strtotime($lo->created_at)))}}" class="btn btn-info">{{$col_A_loNum}}</a>

							@else
							<a disabled class="btn btn-info">{{$col_A_loNum}}</a>
							@endif
							
					<a disabled class="btn btn-danger">{{$col_load_sheet}}</a> 
					<a disabled  class="btn btn-danger">{{$col_mts_report}}</a>
				
				 
						@endif  
					
				 
					 
				<!-- 	<a class="btn btn-info btn-darkblue" href="{{URL('load/pringloadsheet/'.$lo->load_code)}}">
					{{ $button_export }}</a> -->

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
	 
	</section>
       	
      </div>
      <div class="modal-footer">
      	  <button type="button" class="btn btn-default" id="close-add-load" >OK</button>
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
	  	 	  	
	      url: "{{$url_generate_load_code}}",
	      type: "POST",
	      data: {'_token': token},
	      success: function(response){
	      	response = JSON.parse(response);
	      	$('#load-code-created').html('You have generated ' + response.load_code);
	        $('#add-load-modal').modal('show');
	        $('#textfield-load-modal').modal('show');
	      }
	   });
    });
$('#close-add-load').click(function() {
   		window.location.reload();
    });

    $('.assignPicklist').click(function() {
    var count = $("[name='selected[]']:checked").length;

    if (count>0) {
       /* var answer = confirm('Assign Selected Loads?');*/
        if (count) {
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
   	    $('#exportList').click(function() {
    	url = '';

		var filter_load_code = $('#filter_load_code').val();
		var filter_load_code = $('#filter_load_code').val();

		url += '?filter_load_code=' + encodeURIComponent(filter_load_code);
		url += '&filter_load_code=' + encodeURIComponent(filter_load_code);
		url += '&sort=' + encodeURIComponent('{{ $sort }}');
		url += '&order=' + encodeURIComponent('{{ $order }}');

      	location = "{{ $url_export }}" +url;
    });
});
</script>