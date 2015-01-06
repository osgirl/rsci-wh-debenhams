<div class="control-group">
	<div class="controls">
		<div class="accordion" id="accordion2">
			<div class="accordion-group search-panel">
				{{ Form::open(array('url'=>'stores', 'class'=>'form-signin', 'id'=>'form-stores', 'role'=>'form', 'method' => 'get')) }}
				<div id="collapseOne" class="accordion-body collapse in search-panel-content">
					<div class="span4">
						<div>
							<span class="search-po-left-pane">{{ $label_filter_store_name }}</span>
							<span class="search-po-right-pane">
								{{ Form::text('filter_store_name', $filter_store_name, array('id'=>'filter_store_name', 'placeholder'=>'')) }}
							</span>
						</div>
					</div>

					<div class="span4">
						<div>
							<span class="search-po-left-pane">{{ $label_filter_store_code }}</span>
							<span class="search-po-right-pane">
								{{ Form::text('filter_store_code', $filter_store_code, array('id'=>'filter_store_code', 'placeholder'=>'')) }}
							</span>
						</div>
					</div>

					<div class="span11 control-group collapse-border-top" style="margin-top: 6px;">
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
		@if(CommonHelper::arrayHasValue($stores) ) 
		    <h6 class="paginate">
				<span>{{ $stores->appends($arrFilters)->links() }}&nbsp;</span>
			</h6>
		@else
			&nbsp;
		@endif
	</div>
	<div class="div-buttons">
		@if ( CommonHelper::valueInArray('CanExportSlotMasterList', $permissions) )
		<a class="btn btn-info" id="exportList">{{ $button_export }}</a>
		@endif
	</div>
</div>

<div class="widget widget-table action-table">
	<div class="widget-header"> <i class="icon-th-list"></i>
    	<h3>{{ $heading_title }}</h3>
     	<span class="pagination-totalItems">{{ $text_total }} {{ $stores_count }}</span>
    </div>
    <!-- /widget-header -->
    
    <div class="widget-content">
    	<div class="table-responsive">
			<table class="table table-striped table-bordered">
				<thead>
					<tr>
						<th width="10%">{{ $col_id }}</th>
						<th><a href="{{ $sort_store_name }}" class="@if( $sort=='store_name' ) {{ $order }} @endif">{{ $col_store_name }}</a></th>
						<th><a href="{{ $sort_store_code }}" class="@if( $sort=='store_code' ) {{ $order }} @endif">{{ $col_store_code }}</a></th>
						<th>{{ $col_store_address }} </th>
					</tr>
				</thead>
				<tbody>
				@if( !CommonHelper::arrayHasValue($stores) ) 
					<tr class="font-size-13">
						<td colspan="2" class="align-center">{{ $text_empty_results }}</td>
					</tr>
				@else
					@foreach($stores as $store)
					<tr class="font-size-13">
						<td>{{ $counter++ }}</td>
						<td>{{ $store->store_name }}</td>
						<td>{{ $store->store_code }}</td>
						<td>{{ $store->address1.' '.$store->address2.' '.$store->address3 }}</td>
					</tr>
					@endforeach
				@endif
				</tbody>
			</table>
		</div>
	</div>
	
	@if(CommonHelper::arrayHasValue($stores) ) 
    <h6 class="paginate">
		<span>{{ $stores->appends($arrFilters)->links() }}</span>
	</h6>
	@endif
</div>

<script type="text/javascript">
$(document).ready(function() {
    // Submit Form
    $('#submitForm').click(function() {
    	$('#form-stores').submit();
    });
    
    $('#form-stores input').keydown(function(e) {
		if (e.keyCode == 13) {
			$('#form-stores').submit();
		}
	});
    
    // Clear Form
    $('#clearForm').click(function() {
    	$('#filter_store_code').val('');
    	$('#filter_store_name').val('');
    	$('#form-stores').submit();
    });
	
	// Export List
    $('#exportList').click(function() {
    	url = '';
    	
		var filter_store_code = $('#filter_store_code').val();
		var filter_store_name = $('#filter_store_name').val();
		
		url += '?filter_store_code=' + encodeURIComponent(filter_store_code);
		url += '&filter_store_name=' + encodeURIComponent(filter_store_name);
		url += '&sort=' + encodeURIComponent('{{ $sort }}');
		url += '&order=' + encodeURIComponent('{{ $order }}');
		
      	location = "{{ $url_export }}" + url;
    });
});	
</script>