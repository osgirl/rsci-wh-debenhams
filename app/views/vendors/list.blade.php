<div class="control-group">
	<div class="controls">
		<div class="accordion" id="accordion2">
			<div class="accordion-group search-panel">
				{{ Form::open(array('url'=>'vendors', 'class'=>'form-signin', 'id'=>'form-vendors', 'role'=>'form', 'method' => 'get')) }}
				<div id="collapseOne" class="accordion-body collapse in search-panel-content">
					<div class="span4">
						<div>
							<span class="search-po-left-pane">{{ $label_filter_vendor_name }}</span>
							<span class="search-po-right-pane">
								{{ Form::text('filter_vendor_name', $filter_vendor_name, array('id'=>'filter_vendor_name', 'placeholder'=>'')) }}
							</span>
						</div>
					</div>

					<div class="span4">
						<div>
							<span class="search-po-left-pane">{{ $label_filter_vendor_no }}</span>
							<span class="search-po-right-pane">
								{{ Form::text('filter_vendor_no', $filter_vendor_no, array('id'=>'filter_vendor_no', 'placeholder'=>'')) }}
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
		@if(CommonHelper::arrayHasValue($vendors) ) 
		    <h6 class="paginate">
				<span>{{ $vendors->appends($arrFilters)->links() }}&nbsp;</span>
			</h6>
		@else
			&nbsp;
		@endif
	</div>
	<div class="div-buttons">
		@if ( CommonHelper::valueInArray('CanExportVendorMasterList', $permissions) )
		<a class="btn btn-info" id="exportList">{{ $button_export }}</a>
		@endif
	</div>
</div>

<div class="widget widget-table action-table">
	<div class="widget-header"> <i class="icon-th-list"></i>
    	<h3>{{ $heading_title }}</h3>
     	<span class="pagination-totalItems">{{ $text_total }} {{ $vendors_count }}</span>
    </div>
    <!-- /widget-header -->
    
    <div class="widget-content">
    	<div class="table-responsive">
			<table class="table table-striped table-bordered">
				<thead>
					<tr>
						<th width="10%">{{ $col_id }}</th>
						<th><a href="{{ $sort_vendor_name }}" class="@if( $sort=='vendor_name' ) {{ $order }} @endif">{{ $col_vendor_name }}</a></th>
						<th><a href="{{ $sort_vendor_no }}" class="@if( $sort=='vendor_code' ) {{ $order }} @endif">{{ $col_vendor_no }}</a></th>
					</tr>
				</thead>
				<tbody>
				@if( !CommonHelper::arrayHasValue($vendors) ) 
					<tr class="font-size-13">
						<td colspan="2" class="align-center">{{ $text_empty_results }}</td>
					</tr>
				@else
					@foreach($vendors as $vendor)
					<tr class="font-size-13">
						<td>{{ $counter++ }}</td>
						<td>{{ $vendor->vendor_name }}</td>
						<td>{{ $vendor->vendor_code }}</td>
					</tr>
					@endforeach
				@endif
				</tbody>
			</table>
		</div>
	</div>
	
	@if(CommonHelper::arrayHasValue($vendors) ) 
    <h6 class="paginate">
		<span>{{ $vendors->appends($arrFilters)->links() }}</span>
	</h6>
	@endif
</div>

<script type="text/javascript">
$(document).ready(function() {
    // Submit Form
    $('#submitForm').click(function() {
    	$('#form-vendors').submit();
    });
    
    $('#form-vendors input').keydown(function(e) {
		if (e.keyCode == 13) {
			$('#form-vendors').submit();
		}
	});
    
    // Clear Form
    $('#clearForm').click(function() {
    	$('#filter_vendor_no').val('');
    	$('#filter_vendor_name').val('');
    	$('#form-vendors').submit();
    });
	
	// Export List
    $('#exportList').click(function() {
    	url = '';
    	
		var filter_vendor_no = $('#filter_vendor_no').val();
		var filter_vendor_name = $('#filter_vendor_name').val();
		
		url += '?filter_vendor_no=' + encodeURIComponent(filter_vendor_no);
		url += '&filter_vendor_name=' + encodeURIComponent(filter_vendor_name);
		url += '&sort=' + encodeURIComponent('{{ $sort }}');
		url += '&order=' + encodeURIComponent('{{ $order }}');
		
      	location = "{{ $url_export }}" + url;
    });
});	
</script>