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
            {{ Form::open(array('url'=>'letdown', 'class'=>'form-signin', 'id'=>'form-let-down', 'role'=>'form', 'method' => 'get')) }}
            <div id="collapseOne" class="accordion-body collapse in" style="padding-top: 20px;">
	                
			      	<div class="span5">
			      		<div>
				        	<span class="search-po-left-pane">{{ $label_doc_no }}</span>
				        	<span class="search-po-right-pane">
				        		{{ Form::text('filter_doc_no', $filter_doc_no, array('class'=>'login', 'placeholder'=>'', 'id'=>"filter_doc_no")) }}
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
		@if(CommonHelper::arrayHasValue($letdowns) ) 
		    <h6 class="paginate">
				<span>{{ $letdowns->appends($arrFilters)->links() }}&nbsp;</span>
			</h6>
		@else
			&nbsp;
		@endif
	</div>
	<div class="div-buttons">
		@if ( CommonHelper::valueInArray('CanExportStoreOrders', $permissions) )
		<a href="{{$url_export}}" class="btn btn-info" id="exportList">{{ $button_export }}</a>
		@endif
		@if ( CommonHelper::valueInArray('CanViewLetdownLockTags', $permissions) )
		<a href="{{$url_locktags}}" class="btn btn-info" id="exportList">{{ $button_lock_tags }}</a>
		@endif
	</div>
</div>


<div class="widget widget-table action-table">
    <div class="widget-header"> <i class="icon-th-list"></i>
      <h3>{{ $heading_title }}</h3>
      <span class="pagination-totalItems">{{ $text_total }} {{ $letdowns_count }}</span>
    </div>
    <!-- /widget-header -->
    <div class="widget-content">
    	<div class="table-responsive">
			<table class="table table-striped table-bordered">
				<thead>
					<tr>
						<th>{{ $col_id }}</th>
						<th><a href="{{ $sort_doc_no }}" class="@if( $sort=='doc_no' ) {{ $order }} @endif">{{ $col_doc_number }}</a></th>
						<th class="align-center">{{ $col_action }}</th>
					</tr>
				</thead>
				@if( !CommonHelper::arrayHasValue($letdowns) ) 
				<tr class="font-size-13">
					<td colspan="9" style="text-align: center;">{{ $text_empty_results }}</td>
				</tr>
				@else
					@foreach( $letdowns as $ld ) 
					<tr class="font-size-13 tblrow" data-id="{{ $ld->id }}">
						<td>{{ $counter++ }}</td>
						<td><a href="{{ $url_detail . '&id=' . $ld->id . '&doc_no=' . $ld->move_doc_number }}">{{ $ld->move_doc_number }}</a></td>
						<td class="align-center">
	
						  	<!--For letdown close-->
						  	@if ( CommonHelper::valueInArray('CanCloseLetDown', $permissions))
							  	@if($ld->lt_status == Config::get('letdown_statuses.moved'))
									<a style="width: 70px;" class="btn btn-success closeLetdown" data-id="{{ $ld->id }}">{{ $button_close_letdown }}</a>
								@elseif ($ld->lt_status == Config::get('letdown_statuses.closed')) 
									<a style="width: 70px;" disabled="disabled" class="btn btn-danger">{{ $button_close_letdown }}</a>
								@else
									<a style="width: 70px;" disabled="disabled" class="btn">{{ $button_close_letdown }}</a>
								@endif
							@endif


						  	<!--For letdown close-->
							{{ Form::open(array('url'=>'letdown/close_letdown', 'id' => 'closeLetdown_' . $ld->id, 'style' => 'margin: 0px;')) }}
								{{ Form::hidden('doc_no', $ld->move_doc_number) }}
								{{ Form::hidden('filter_doc_no', $filter_doc_no) }}
								{{ Form::hidden('sort', $sort) }}
								{{ Form::hidden('order', $order) }}
								{{ Form::hidden('page', $page) }}
						  	{{ Form::close() }}

						</td>
					</tr>
					</tr>
					@endforeach
				@endif
			</table>
		</div>
	</div>
	
	@if( CommonHelper::arrayHasValue($letdowns) ) 
    <h6 class="paginate">
		<span>{{ $letdowns->appends($arrFilters)->links() }}</span>
	</h6>
	@endif

</div>


<script type="text/javascript">
$(document).ready(function() {
    
    // Close Letdown
    $('.closeLetdown').click(function() {
    	var answer = confirm('{{ $text_warning }}');
			
		if (answer) {
			var letdown = $(this).data('id');
	    	$('#closeLetdown_' + letdown).submit();
		}
    });


    // Submit Form
    $('#submitForm').click(function() {
    	$('#form-let-down').submit();
    });

    $('#form-let-down input').keydown(function(e) {
		if (e.keyCode == 13) {
			$('#form-let-down').submit();
		}
	});

    // Clear Form
    $('#clearForm').click(function() {
    	$('#filter_doc_no, #filter_type').val('');
		$('#form-let-down').submit();
    });


});	
</script>
