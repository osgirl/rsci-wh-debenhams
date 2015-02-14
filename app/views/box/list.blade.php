@if( CommonHelper::arrayHasValue($error) )
    <div class="alert alert-danger">
    	<button class="close" data-dismiss="alert" type="button">&times;</button>
    	{{ $error }}
    </div>
@endif

@if( CommonHelper::arrayHasValue($success) )
    <div class="alert alert-success">
    	<button class="close" data-dismiss="alert" type="button">&times;</button>
    	{{ $success }}
    </div>
@endif

<div class="control-group">
	<div class="controls">
		<div class="accordion" id="accordion2">
          <div class="accordion-group" style="background-color: #FFFFFF;">
            {{ Form::open(array('url'=>'box/list', 'class'=>'form-signin', 'id'=>'form-box', 'role'=>'form', 'method' => 'get')) }}
            <div id="collapseOne" class="accordion-body collapse in" style="padding-top: 20px;">
	                <div class="span4">
			        	<div>
				        	<span class="search-po-left-pane">{{ $label_store }}</span>
				        	<span class="search-po-right-pane">
				        		{{ Form::text('filter_store', $filter_store, array('class'=>'login', 'placeholder'=>'', 'id'=>"filter_store")) }}
				        	</span>
				        </div>
			      	</div>
			      	<div class="span5">
			      		<div>
				        	<span class="search-po-left-pane">{{ $label_box_code }}</span>
				        	<span class="search-po-right-pane">
				        		{{ Form::text('filter_box_code', $filter_box_code, array('class'=>'login', 'placeholder'=>'', 'id'=>"filter_box_code")) }}
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
		@if(CommonHelper::arrayHasValue($boxes) )
		    <h6 class="paginate">
				<span>{{ $boxes->appends($arrFilters)->links() }}&nbsp;</span>
			</h6>
		@else
			&nbsp;
		@endif
	</div>
	<div class="div-buttons">
		{{-- @if ( CommonHelper::valueInArray('CanLoadPicking', $permissions) ) --}}
			<a role="button" class="btn btn-warning" id="load-boxes" title="{{ $button_load }}" data-toggle="modal">{{ $button_load }}</a>
		{{-- @endif --}}
		{{-- @if ( CommonHelper::valueInArray('CanAddLoad', $permissions) ) --}}
		<a  class="btn btn-info btn-darkblue" id="generate-load">{{ $button_add_store }}</a>
		{{-- @endif --}}
		{{-- @if ( CommonHelper::valueInArray('CanCreateBox', $permissions) ) --}}
		<a class="btn btn-info btn-darkblue" href="{{$url_add_box}}">{{ $button_create_box }}</a>
		{{-- @endif --}}

		@if ( CommonHelper::valueInArray('CanExportBoxingLoading', $permissions) )
		<a class="btn btn-info btn-darkblue" href="{{$url_export_box}}" >{{ $button_export_box }}</a>
		@endif

	</div>

</div>

<div class="widget widget-table action-table">
    <div class="widget-header"> <i class="icon-th-list"></i>
      <h3>{{ $heading_title }}</h3>
      <span class="pagination-totalItems">{{ $text_total }} {{ $boxes_count }}</span>
    </div>
    <!-- /widget-header -->
    <div class="widget-content">
    	<div class="table-responsive">
			<table class="table table-striped table-bordered">
				<thead>
					<tr>
						{{ Form::open(array('url'=>'box/delete', 'class'=>'form-signin', 'id'=>'form-box-delete', 'role'=>'form', 'method' => 'get')) }}
						{{ Form::hidden('sort', $sort) }}
					    {{ Form::hidden('order', $order) }}
					    {{ Form::hidden('page', $page) }}
					    {{ Form::hidden('filter_store', $filter_store) }}
					    {{ Form::hidden('filter_box_code', $filter_box_code) }}
					    {{ Form::hidden('box_codes', '', array('id'=>'box-codes')) }}

			            {{ Form::close() }}
						<th style="width: 20px;" class="align-center"><input type="checkbox" id="main-selected"></th>
						<th>{{ $col_id }}</th>
						<th><a href="{{ $sort_store }}" class="@if( $sort=='store' ) {{ $order }} @endif">{{ $col_store }}</a></th>
						<th><a href="{{ $sort_box_code }}" class="@if( $sort=='box_code' ) {{ $order }} @endif">{{ $col_box_code }}</a></th>
						<th><a href="{{ $sort_date_created }}" class="@if( $sort=='date_created' ) {{ $order }} @endif">{{ $col_date_created }}</a></th>
						<th>{{ $col_action }}</th>
					</tr>
				</thead>
				@if( !CommonHelper::arrayHasValue($boxes) )
				<tr class="font-size-13">
					<td colspan="9" style="text-align: center;">{{ $text_empty_results }}</td>
				</tr>
				@else
					@foreach( $boxes as $box )
					<tr class="font-size-13 tblrow" data-id="{{$box['box_code']}}">
						<td class="align-center">
							@if($box['in_use'] == 0)
							<input type="checkbox" class="checkbox item-selected" name="selected[]" id="selected-{{$box['box_code']}}" value="{{$box['box_code']}}">
							@endif
						</td>
						<td>{{ $counter++ }}</td>
						<td>{{ $box['store_name'] }}</td>
						<td><a href="{{ $url_detail . '&id=' . $box['id'] . '&box_code=' . $box['box_code'] }}">{{ $box['box_code'] }}</a></td>
						<td>{{ date("M d, Y", strtotime($box['created_at']))}}</td>
						<td class="align-center">
							{{-- @if ( CommonHelper::valueInArray('CanLoadPicking', $permissions)  || CommonHelper::valueInArray('CanEditPicklist', $permissions)) --}}
								{{-- @if ( CommonHelper::valueInArray('CanLoadPicking', $permissions) )--}}
									@if( $box['in_use'] == 0 && BoxDetails::isBoxEmpty($box['box_code']) )
									<a data-id="{{ $box['box_code'] }}" role="button" class="btn btn-info load-boxes-single" title="{{ $button_load }}" data-toggle="modal">{{ $button_load }}</a>
									@endif
								{{-- @endif --}}
							{{-- @endif --}}
							{{-- @if ( CommonHelper::valueInArray('CanEditBoxes', $permissions) && $box['picklist_detail_id'] === null ) --}}
								<a href="{{$url_update_box}}&box_code={{$box['box_code']}}" class="icon-edit"></a>
							{{-- @endif --}}
							{{-- @if ( CommonHelper::valueInArray('CanDeleteBoxes', $permissions) && !CommonHelper::hasValue($box['picklist_detail_id']) ) --}}
								<a  data-id="{{$box['box_code']}}"  class="icon-remove single-box-delete"></a>
							{{-- @endif --}}

						</td>
					</tr>
					</tr>
					@endforeach
				@endif
			</table>
		</div>
	</div>
</div>

<!--modal for load picklist-->
<div id="load-boxes-modal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="load-boxes-modal-label" aria-hidden="true">
	 <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title">{{$entry_load}}</h4>
      </div>
      <div class="modal-body">
        {{ Form::open(array('url'=>$url_load, 'role'=> 'form', "class"=> "form-horizontal", 'id'=> 'form-box-load'))}}
        <div class="form-group">
        	{{ Form::label('load_code',$label_load_code, array("style" => "margin-right:10px", "class" => "col-sm-2 control-label" ))}}
        	<div class="col-sm-10">
        		{{ Form::select('load_codes', array('' => $text_select) + $load_codes, array('class'=>'select-width', 'id'=>"load_codes")) }}
        	</div>

		</div>
		<br/>
		<div class="form-group">
        	{{ Form::label('box_code',$label_box_code, array("style" => "margin-right:10px", "class" => "col-sm-2 control-label" ))}}
        	<div class="col-sm-10">
        		{{ Form::text('box-ids-load', '', array('required','class'=> " box-ids-load form-control", "disabled"=>"true"))}}
        	</div>

		</div>

		{{-- Form::hidden('filter_type', $filter_type) --}}
		{{ Form::hidden('filter_box_code', $filter_box_code) }}
		{{-- Form::hidden('filter_status', $filter_status) --}}
  		{{ Form::hidden('sort', $sort) }}
		{{ Form::hidden('order', $order) }}
		{{ Form::hidden('page', $page) }}
		{{ Form::hidden('module', 'box') }}
		{{ Form::hidden('box_lists','' , array('class'=>'box-ids-load')) }}
		</br>

        {{ Form::close()}}
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" id="load-boxes-main-button">{{ $button_load}}</button>
      </div>
    </div><!-- /.modal-content -->
</div>

<div id="add-load-modal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="load-boxes-modal-label" aria-hidden="true">
	 <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title">{{$entry_load_create}}</h4>
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
$(document).ready(function() {

    // Submit Form
    $('#submitForm').click(function() {
    	//TODO
    	$('#form-box').submit();
    });


    /**************Search events***************/

    //Search form on enter sumbmit filters
    $('#form-box input').keydown(function(e) {
		if (e.keyCode == 13) {
			$('#form-box').submit();
		}
	});

    // Clear Search Form
    $('#clearForm').click(function() {
    	$('#filter_store, #filter_box_code').val('');

		$('#form-box').submit();
    });

    //Delete

    $('#deleteMass').click(function(e){
    	var count = $("[name='selected[]']:checked").length;
    	if (count>0) {
			var answer = confirm('{{ $text_confirm_delete }}')

			if (answer) {
				var boxes = new Array();
				$.each($("input[name='selected[]']:checked"), function() {
					boxes.push($(this).val());
				});

    			$('#box-codes').val(boxes.join(','));
    			$('#form-box-delete').submit();

			} else {
				return false;
			}
		} else {
			alert('{{ $error_delete }}');
			return false;
		}
    });

    $('.single-box-delete').click(function(e){
    	var answer = confirm('{{ $text_confirm_delete_single }}')

		if (answer) {
			var box = $(this).attr('data-id');
    		$('#box-codes').val(box);
    		$('#form-box-delete').submit();

		} else {
			return false;
		}

    });


    // Select
    $('.tblrow').click(function() {

    	var rowid = $(this).data('id');

    	if ($('#selected-' + rowid).length>0) {
    		console.log($('#selected-' + rowid));
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

    // add load
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
    	      }
    	   });
    });
 	$('#close-add-load').click(function() {
   		window.location.reload();
    });

    //load picklist
    $('#load-boxes').click(function() {
    	var count = $("[name='selected[]']:checked").length;

		if (count>0) {
			var picklist = new Array();
			$.each($("input[name='selected[]']:checked"), function() {
				picklist.push($(this).val());
			});
			//form-picking-load

			$('.box-ids-load').val(picklist.join(','));


			$('#load-boxes-modal').modal('show');

			//show modal

		} else {
			alert('{{ $error_load }}');
			return false;
		}
    });

    $('.load-boxes-single').click(function(){
    	var box_id= $(this).attr('data-id');

    	$('.box-ids-load').val(box_id);
    	$('#load-boxes-modal').modal('show');
    });

    $('#load-boxes-main-button').click(function(){
    	if ($('select[name="load_codes"]').val()== '') {
    		alert('{{ $error_load_no_load_code }}');
    		return false;
    	}

    	var answer = confirm('{{ $text_confirm_load }}');
    	if (answer ) {
    		$('#form-box-load').submit();
    	} else {
    			alert('{{ $error_load }}');
			return false;
		}

    });

});
</script>