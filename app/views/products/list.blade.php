<div class="control-group">
	<div class="controls">
		<div class="accordion" id="accordion2">
			<div class="accordion-group search-panel">
				{{ Form::open(array('url'=>'products', 'class'=>'form-signin', 'id'=>'form-products', 'role'=>'form', 'method' => 'get')) }}
				<div id="collapseOne" class="accordion-body collapse in search-panel-content">
					<div class="span3">
						<div>
							<span class="search-po-left-pane">{{ $label_filter_prod_sku }}</span>
							<span class="search-po-right-pane">
								{{ Form::text('filter_prod_sku', $filter_prod_sku, array('id'=>'filter_prod_sku', 'placeholder'=>'')) }}
							</span>
						</div>
						<div>
							<span class="search-po-left-pane">{{ $label_filter_prod_upc }}</span>
							<span class="search-po-right-pane">
								{{ Form::text('filter_prod_upc', $filter_prod_upc, array('id'=>'filter_prod_upc', 'placeholder'=>'')) }}
							</span>
						</div>
					</div>

					<div class="span4">
						<div>
							<span class="search-po-left-pane">{{ $label_filter_prod_full_name }}</span>
							<span class="search-po-right-pane">
								{{ Form::text('filter_prod_full_name', $filter_prod_full_name, array('id'=>'filter_prod_full_name', 'placeholder'=>'')) }}
							</span>
						</div>
						<div>
							<span class="search-po-left-pane">{{ $label_filter_prod_short_name }}</span>
							<span class="search-po-right-pane">
								{{ Form::text('filter_prod_short_name', $filter_prod_short_name, array('id'=>'filter_prod_short_name', 'placeholder'=>'')) }}
							</span>
						</div>
					</div>

					<div class="span4">
						<div>
							<span class="search-po-left-pane">{{ $label_filter_dept_name }}</span>
							<span class="search-po-right-pane">
								{{ Form::select('filter_dept_no', $filter_department_options, $filter_dept_no, array('id' => 'filter_dept_no', 'class' => 'select-width')) }}
							</span>
						</div>
						<div>
							<span class="search-po-left-pane">{{ $label_filter_sub_dept_name }}</span>
							<span class="search-po-right-pane" id="sub_dept">
								{{ Form::select('filter_sub_dept_no', $filter_sub_department_options, $filter_sub_dept_no, array('id' => 'filter_sub_dept_no', 'class' => 'select-width')) }}
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
		@if( CommonHelper::arrayHasValue($products) ) 
		    <h6 class="paginate">
				<span>{{ $products->appends($arrFilters)->links() }}&nbsp;</span>
			</h6>
		@else
			&nbsp;
		@endif
	</div>
	<div class="div-buttons">
		@if ( CommonHelper::valueInArray('CanExportProductMasterList', $permissions) )
		<a class="btn btn-info" id="exportList">{{ $button_export }}</a>
		@endif
	</div>
</div>

<div class="widget widget-table action-table">
    <div class="widget-header"> <i class="icon-th-list"></i>
    	<h3>{{ $heading_title }}</h3>
      	<span class="pagination-totalItems">{{ $text_total }} {{ $products_count }}</span>
    </div>
    <!-- /widget-header -->
    
    <div class="widget-content">
    	<div class="table-responsive">
			<table class="table table-striped table-bordered">
				<thead>
					<tr>
						<th>{{ $col_id }}</th>
						<th><a href="{{ $sort_sku }}" class="@if( $sort=='sku' ) {{ $order }} @endif">{{ $col_prod_sku }}</a></th>
						<th><a href="{{ $sort_upc }}" class="@if( $sort=='upc' ) {{ $order }} @endif">{{ $col_prod_upc }}</a></th>
						<th><a href="{{ $sort_full_name }}" class="@if( $sort=='full_name' ) {{ $order }} @endif">{{ $col_prod_full_name }}</a></th>
						<th><a href="{{ $sort_short_name }}" class="@if( $sort=='short_name' ) {{ $order }} @endif">{{ $col_prod_short_name }}</a></th>
						<th><a href="{{ $sort_dept }}" class="@if( $sort=='dept' ) {{ $order }} @endif">{{ $col_department }}</a></th>
						<th><a href="{{ $sort_sub_dept }}" class="@if( $sort=='sub_dept' ) {{ $order }} @endif">{{ $col_sub_department }}</a></th>
					</tr>
				</thead>
				<tbody>
				@if( !CommonHelper::arrayHasValue($products) ) 
					<tr class="font-size-13">
						<td colspan="7" class="align-center">{{ $text_empty_results }}</td>
					</tr>
				@else
					@foreach( $products as $product )
					<tr class="font-size-13">
						<td>{{ $counter++ }}</td>
						<td>{{ $product->sku }}</td>
						<td>{{ $product->upc }}</td>
						<td>{{ $product->description }}</td>
						<td>{{ $product->short_description }}</td>
						<td>{{ $product->dept_code . ' - ' . $product->dept_name }}</td>
						<td>{{ $product->sub_dept . ' - ' . $product->sub_dept_name }}</td>
					</tr>
					@endforeach
				@endif
				</tbody>
			</table>
		</div>
	</div>
	
	@if( CommonHelper::arrayHasValue($products) ) 
    <h6 class="paginate">
		<span>{{ $products->appends($arrFilters)->links() }}</span>
	</h6>
	@endif
</div>

<script type="text/javascript">
$(document).ready(function() {
    // Submit Form
    $('#submitForm').click(function() {
    	$('#form-products').submit();
    });
    
    $('#form-products input').keydown(function(e) {
		if (e.keyCode == 13) {
			$('#form-products').submit();
		}
	});
	
	// Change Sub Dept
	$('#filter_dept_no').change(function() {		
		
	  	$.ajax({
			url: '{{ $url_department }}',
			type: 'GET',
			dataType: 'json',
			data: "filter_dept_no=" + $("#filter_dept_no").val(),
			success: function(output_string) {
				sub_dept = '<select name="filter_sub_dept_no" id="filter_sub_dept_no" class="select-width">';
				sub_dept += '<option value="">{{ Lang::get('general.text_select') }}</option>';
				
				jQuery.each(output_string, function(i, val) {
					sub_dept += '<option value="'+i+'">'+val+'</option>';
				});
				
				sub_dept += '</select>';
				
				$("#sub_dept").html(sub_dept);
			}
		}); 
	});
    
    // Clear Form
    $('#clearForm').click(function() {
    	$('#filter_prod_sku').val('');
		$('#filter_prod_upc').val('');
		$('#filter_prod_full_name').val('');
		$('#filter_prod_short_name').val('');
		$('#filter_dept_no').val('');
		$('#filter_sub_dept_no').val('');
		$('#form-products').submit();
    });
	
	// Export List
    $('#exportList').click(function() {
    	url = '';
    	
		var filter_prod_sku = $('#filter_prod_sku').val();
		url += '?filter_prod_sku=' + encodeURIComponent(filter_prod_sku);
		
		var filter_prod_upc = $('#filter_prod_upc').val();
		url += '&filter_prod_upc=' + encodeURIComponent(filter_prod_upc);
		
		var filter_prod_full_name = $('#filter_prod_full_name').val();
		url += '&filter_prod_full_name=' + encodeURIComponent(filter_prod_full_name);
		
		var filter_prod_short_name = $('#filter_prod_short_name').val();
		url += '&filter_prod_short_name=' + encodeURIComponent(filter_prod_short_name);
		
		var filter_dept_no = $('#filter_dept_no').val();
		url += '&filter_dept_no=' + encodeURIComponent(filter_dept_no);
		
		var filter_dept_name = $('#filter_dept_name').val();
		url += '&filter_dept_name=' + encodeURIComponent(filter_dept_name);
		
		var filter_sub_dept_no = $('#filter_sub_dept_no').val();
		url += '&filter_sub_dept_no=' + encodeURIComponent(filter_sub_dept_no);
		
		var filter_sub_dept_name = $('#filter_sub_dept_name').val();
		url += '&filter_sub_dept_name=' + encodeURIComponent(filter_sub_dept_name);
		
		url += '&sort=' + encodeURIComponent('{{ $sort }}');
		url += '&order=' + encodeURIComponent('{{ $order }}');
		
      	location = "{{ $url_export }}" + url;
    });
});	
</script>