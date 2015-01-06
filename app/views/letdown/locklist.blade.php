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
	@if ( CommonHelper::valueInArray('CanAccessLetDowns', $permissions) )
	<a href="{{$url_to_letdown}}" class="btn btn-info"> <i class="icon-chevron-left"></i> {{ $button_to_letdown }}</a>
	@endif
</div>

<div class="control-group">
	<div class="controls">
		<div class="accordion" id="accordion2">
          <div class="accordion-group" style="background-color: #FFFFFF;">
            {{ Form::open(array('url'=>'letdown/locktags', 'class'=>'form-signin', 'id'=>'form-lt-locktags', 'role'=>'form', 'method' => 'get')) }}
            <div id="collapseOne" class="accordion-body collapse in" style="padding-top: 20px;">
			      	<div class="span6">
			      		<div>
				        	<span class="search-po-left-pane">{{ $label_stock_piler }}</span>
				        	<span class="search-po-right-pane">
				        		{{ Form::select('filter_stock_piler', array('' => $text_select) + $stock_piler_list, $filter_stock_piler, array('class'=>'select-width', 'id'=>"filter_stock_piler")) }}
				        	</span>
				        </div>
				        <div>
				        	<span class="search-po-left-pane">{{ $label_doc_no }}</span>
				        	<span class="search-po-right-pane">
				        		{{ Form::text('filter_doc_no', $filter_doc_no, array('class'=>'login', 'placeholder'=>'', 'id'=>"filter_doc_no")) }}
				        	</span>
				        </div>
			      		
			      	</div>

			      	<div class="span3">
				        <div>
				        	<span class="search-po-left-pane">{{ $label_upc }}</span>
				        	<span class="search-po-right-pane">
				        		{{ Form::text('filter_sku', $filter_sku, array('class'=>'login', 'placeholder'=>'', 'id'=>"filter_sku")) }}
				        	</span>
				        </div>
			      		
			      	</div>
			      	<div class="span11 control-group collapse-border-top">
			      		<a class="btn btn-success" id="submitForm">{{ $button_search }}</a>
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
		@if(CommonHelper::arrayHasValue($lock_tag) ) 
		    <h6 class="paginate">
				<span>{{ $lock_tag->appends($arrFilters)->links() }}&nbsp;</span>
			</h6>
		@else
			&nbsp;
		@endif
	</div>
	<div class="div-buttons">
		@if ( CommonHelper::valueInArray('CanUnlockLetDown', $permissions) )
		<a class="btn btn-info" id="mass-unlock-lt-tag">{{ $button_unlock_tags }}</a> 
		@endif
	</div>
</div>

<div class="widget widget-table action-table">
    <div class="widget-header"> <i class="icon-th-list"></i>
      <h3>{{ $heading_title_letdown_lock_tags }}</h3>
      <span class="pagination-totalItems">{{ $text_total }} {{ $lock_tag_count }}</span>
    </div>

    <div class="widget-content">
    	<div class="table-responsive">
			<table class="table table-striped table-bordered">
				<thead>
					<tr>
						@if ( CommonHelper::valueInArray('CanUnlockLetDown', $permissions) )
						{{ Form::open(array('url'=>$url_unlock, 'class'=>'form-signin', 'id'=>'form-unlock', 'role'=>'form', 'method' => 'post')) }}

						{{ Form::hidden('filter_stock_piler', $filter_stock_piler) }}
						{{ Form::hidden('filter_doc_no', $filter_doc_no) }}
						{{ Form::hidden('filter_sku', $filter_sku) }}
						{{ Form::hidden('page', $page) }}
						{{ Form::hidden('sort', $sort) }}
					    {{ Form::hidden('order', $order) }}
					    {{ Form::hidden('lock_tag', NULL, array('id'=>'lock_tag_input')) }}
			            
			            {{ Form::close() }}
						<th style="width: 20px;" class="align-center"><input type="checkbox" id="main-selected" /></th>
						@endif
						<th><a href="{{ $sort_lock_tag }}" class="@if( $sort=='lock_tag' ) {{ $order }} @endif">{{ $col_time_locked }}</a></th>
						<th class="align-center">{{ $col_stock_piler }}</th>
						<th class="align-center">{{ $col_action }}</th>
					</tr>
				</thead>
				@if( !CommonHelper::arrayHasValue($lock_tag) ) 
				<tr class="font-size-13">
					<td colspan="9" style="text-align: center;">{{ $text_empty_results }}</td>
				</tr>
				@else
					@foreach( $lock_tag as $value )
					<tr class="font-size-13 tblrow" data-id="{{ $value['lock_tag']  }}">
						@if ( CommonHelper::valueInArray('CanUnlockLetDown', $permissions) )
						<td class="align-center">
							@if($value['sum_moved'] <= 0)
							<input type="checkbox" class="checkbox item-selected" name="selected[]" id="selected-{{ $value['lock_tag'] }}" value="{{ $value['lock_tag']  }}" />
							@endif
							
						</td>
						@endif
						<td><a href="{{$url_lock_detail}}&lock_tag={{$value['lock_tag']}}">{{ $value['lock_tag'] }}</a></td>
						<td>{{ $value['username'] }}</td>
						<td class="align-center">@if ( CommonHelper::valueInArray('CanUnlockLetDown', $permissions) )
							@if($value['sum_moved'] <= 0)
							<a class="btn btn-info single-unlock-lt-tag" data-id="{{$value['lock_tag']}}" >{{ $button_unlock_tag }}</a>  
							@else
							<a class="btn btn-danger" disabled=true>{{ $button_unlock_tag }}</a>
							@endif
						@endif</td>
					</tr>
					@endforeach
				@endif
			</table>
		</div>
	</div>
    
</div>

<script>
 	// Submit Form
    $('#submitForm').click(function() {
    	$('#form-lt-locktags').submit();
    });

    $('#form-lt-locktags input').keydown(function(e) {
		if (e.keyCode == 13) {
			$('#form-lt-locktags').submit();
		}
	});

    // Clear Form
    $('#clearForm').click(function() {
    	$('#filter_doc_no, #filter_sku').val('');
		
		$('select').val('');
		$('#form-lt-locktags').submit();
    });

    //unlock functions
    //lock_tag_input

    $('.single-unlock-lt-tag').click(function() {
    	var answer = confirm('{{ $text_warning_unlock_single }}')
			
		if (answer) {
			var lockTag = $(this).attr('data-id');

    		$('#lock_tag_input').val(lockTag);

    		$('#form-unlock').submit();
		} else {
			return false;
		}
    	
    });

    $('#mass-unlock-lt-tag').click(function() {
    	var count = $("[name='selected[]']:checked").length;
		
		if (count>0) {
			var answer = confirm('{{ $text_warning_unlock }}')
			
			if (answer) {
				var lockTag = new Array();
				$.each($("input[name='selected[]']:checked"), function() {
					lockTag.push($(this).val());
				});
    	
    			$('#lock_tag_input').val(lockTag.join(','));
    			$('#form-unlock').submit();
			} else {
				return false;
			}
		} else {
			alert('{{ $error_no_lock_tag }}');
			return false;
		}
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
   
</script>