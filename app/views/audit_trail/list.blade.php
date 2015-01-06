@if( CommonHelper::arrayHasValue($error_date) )
    <div class="alert alert-error">
    	<button class="close" data-dismiss="alert" type="button">&times;</button>
    	{{ $error_date }}
    </div>
@endif

<div class="control-group">
	<div class="controls">
		<div class="accordion" id="accordion2">
			<div class="accordion-group search-panel">
				{{ Form::open(array('url'=>'audit_trail', 'class'=>'form-signin', 'id'=>'form-audit-trail', 'role'=>'form', 'method' => 'get')) }}
				<div id="collapseOne" class="accordion-body collapse in search-panel-content">
					<div class="span4">
						<div>
							<span class="search-po-left-pane">{{ $label_filter_date_from }}</span>
							<div class="search-po-right-pane input-append date">
								{{ Form::text('filter_date_from', $filter_date_from, array('id'=>'filter_date_from', 'readonly'=>'readonly')) }}
								<span class="add-on"><i class="icon-th"></i></span>
							</div>
						</div>
						<div>
							<span class="search-po-left-pane">{{ $label_filter_date_to }}</span>
							<div class="search-po-right-pane input-append date">
								{{ Form::text('filter_date_to', $filter_date_to, array('id'=>'filter_date_to', 'readonly'=>'readonly')) }}
								<span class="add-on"><i class="icon-th"></i></span>
							</div>
						</div>
					</div>

					<div class="span4">
						<div>
							<span class="search-po-left-pane">{{ $label_filter_module }}</span>
							<span class="search-po-right-pane">
								{{ Form::select('filter_module', $filter_module_options, $filter_module, array('class' => 'select-width')) }}
							</span>
						</div>
						<div>
							<span class="search-po-left-pane">{{ $label_filter_action }}</span>
							<span class="search-po-right-pane">
								{{ Form::text('filter_action', $filter_action, array('id'=>'filter_action', 'placeholder'=>'')) }}
							</span>
						</div>
					</div>

					<div class="span3">
						<div>
							<span class="search-po-left-pane">{{ $label_filter_reference }}</span>
							<span class="search-po-right-pane">
								{{ Form::text('filter_reference', $filter_reference, array('id'=>'filter_reference', 'placeholder'=>'', 'maxlength'=>'100')) }}
							</span>
						</div>
						<div>
							<span class="search-po-left-pane">{{ $label_filter_user }}</span>
							<span class="search-po-right-pane">
								{{ Form::select('filter_user', $filter_user_options, $filter_user, array('class' => 'select-width')) }}
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
		@if(CommonHelper::arrayHasValue($audit_trails) ) 
		    <h6 class="paginate">
				<span>{{ $audit_trails->appends($arrFilters)->links() }}&nbsp;</span>
			</h6>
		@else
			&nbsp;
		@endif
	</div>
	<div class="div-buttons">
		@if ( CommonHelper::valueInArray('CanExportAuditTrail', $permissions) )
		<a class="btn btn-info" id="exportList">{{ $button_export }}</a>
		@endif
		@if ( CommonHelper::valueInArray('CanArchiveAuditTrail', $permissions) )
		<a class="btn btn-info">{{ $button_archive }}</a>
		@endif
	</div>
</div>

<div class="widget widget-table action-table">
    <div class="widget-header"> <i class="icon-th-list"></i>
    	<h3>{{ $heading_title }}</h3>
      	<span class="pagination-totalItems">{{ $text_total }} {{ $audit_trails_count }}</span>
    </div>
    <!-- /widget-header -->
    
    <div class="widget-content">
    	<div class="table-responsive">
			<table class="table table-striped table-bordered">
				<thead>
					<tr>
						<th>{{ $col_id }}</th>
						<th><a href="{{ $sort_date }}" class="@if( $sort=='date' ) {{ $order }} @endif">{{ $col_transaction_date }}</a></th>
						<th><a href="{{ $sort_module }}" class="@if( $sort=='module' ) {{ $order }} @endif">{{ $col_module }}</a></th>
						<th><a href="{{ $sort_reference }}" class="@if( $sort=='reference' ) {{ $order }} @endif">{{ $col_reference }}</a></th>
						<th><a href="{{ $sort_username }}" class="@if( $sort=='username' ) {{ $order }} @endif">{{ $col_username }}</a></th>
						<th><a href="{{ $sort_action }}" class="@if( $sort=='action' ) {{ $order }} @endif">{{ $col_action }}</a></th>
						<th><a href="{{ $sort_details }}" class="@if( $sort=='details' ) {{ $order }} @endif">{{ $col_details }}</a></th>
					</tr>
				</thead>
				<tbody>
				@if( !CommonHelper::arrayHasValue($audit_trails) ) 
					<tr class="font-size-13">
						<td colspan="7" class="align-center">{{ $text_empty_results }}</td>
					</tr>
				@else
					@foreach( $audit_trails as $audit_trail )
					<tr class="font-size-13">
						<td>{{ $counter++ }}</td>
						<td>{{ $audit_trail->created_at }}</td>
						<td>{{ $audit_trail->module }}</td>
						<td>{{ $audit_trail->reference }}</td>
						<td>{{ $audit_trail->username }}</td>
						<td>{{ $audit_trail->action }}</td>
						<td>{{ $audit_trail->data_after }}</td>
					</tr>
					@endforeach
				@endif
				</tbody>
			</table>
		</div>
	</div>
	
	@if( CommonHelper::arrayHasValue($audit_trails) ) 
    <h6 class="paginate">
		<span>{{ $audit_trails->appends($arrFilters)->links() }}</span>
	</h6>
	@endif
</div>

<script type="text/javascript">
$(document).ready(function() {
    $('.date').datepicker({
      format: 'yyyy-mm-dd'
    });
    
    // Submit Form
    $('#submitForm').click(function() {
    	$('#form-audit-trail').submit();
    });
    
    $('#form-audit-trail input').keydown(function(e) {
		if (e.keyCode == 13) {
			$('#form-audit-trail').submit();
		}
	});
    
    // Clear Form
    $('#clearForm').click(function() {
    	$('#filter_date_from').val('');
		$('#filter_date_to').val('');
		$('#filter_action').val('');
		$('#filter_reference').val('');
		
		$('select').val('');
		$('#form-audit-trail').submit();
    });
	
	// Export List
    $('#exportList').click(function() {
    	url = '';
    	
		var filter_date_from = $('#filter_date_from').val();
		url += '?filter_date_from=' + encodeURIComponent(filter_date_from);
		
		var filter_date_to = $('#filter_date_to').val();
		url += '&filter_date_to=' + encodeURIComponent(filter_date_to);
		
		var filter_module = $('select[name=\'filter_module\']').val();
		url += '&filter_module=' + encodeURIComponent(filter_module);
				
		var filter_action = $('#filter_action').val();
		url += '&filter_action=' + encodeURIComponent(filter_action);
		
		var filter_reference = $('#filter_reference').val();
		url += '&filter_reference=' + encodeURIComponent(filter_reference);
		
		var filter_user = $('select[name=\'filter_user\']').val();
		url += '&filter_user=' + encodeURIComponent(filter_user);
		
		url += '&sort=' + encodeURIComponent('{{ $sort }}');
		url += '&order=' + encodeURIComponent('{{ $order }}');
		
      	location = "{{ $url_export }}" + url;
    });
});	
</script>