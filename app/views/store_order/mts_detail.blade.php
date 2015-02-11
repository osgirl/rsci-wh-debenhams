<div class="control-group">
	<a href="{{ $url_back }}" class="btn btn-info btn-darkblue"> <i class="icon-chevron-left"></i> {{ $button_back }}</a>

	@if ( CommonHelper::valueInArray('CanExportStoreOrders', $permissions) )
	<a class="btn btn-info btn-darkblue" id="exportMtsList">{{ $button_export }}</a>
	@endif

</div>

<!-- PO Detail -->
<div class="widget widget-table action-table">
    <div class="widget-header"> <i class="icon-th-list"></i>
      <h3>{{ $heading_title_so_details }}</h3>
    </div>
    <!-- /widget-header -->
	<div class="widget-content">
	   <div class="row-fluid stats-box">
	      	<div class="span4">
	        	<div>
		        	<span class="left-pane">{{ $label_store_order_no }}</span>
		        	<span class="right-pane">{{ Form::text('store_order_no', $so_info->so_no, array('readonly' => 'readonly')) }}</span>
		        </div>
		        <div>
		        	<span class="left-pane">{{ $label_store }}</span>
		        	<span class="right-pane">{{ Form::text('store', $so_info->store_code, array('readonly' => 'readonly')) }}</span>
		        </div>
	      	</div>

	      	<div class="span4">
	      		<div>
		        	<span class="left-pane">{{ $label_order_date }}</span>
		        	<span class="right-pane">{{ Form::text('order_date', date('M d, Y', strtotime($so_info->order_date)), array('readonly' => 'readonly')) }}</span>
		        </div>
		        <div>
		        </div>
		        <div>
		        	<span class="left-pane">{{ $label_status }}</span>
		        	<span class="right-pane">{{ Form::text('data_display', $so_status_type[$so_info->so_status], array('readonly' => 'readonly')) }}</span>
		        </div>
	      </div>

	      <div class="span4">
		        <div>
		        </div>
	      </div>
	   </div>
	 </div>
</div>

<div class="clear">
	<div class="div-paginate">
		@if(CommonHelper::arrayHasValue($store_orders) )
		    <h6 class="paginate">
				<span>{{ $store_orders->appends($arrFilters)->links() }}&nbsp;</span>
			</h6>
		@else
			&nbsp;
		@endif
	</div>
</div>

<div class="widget widget-table action-table">
    <div class="widget-header"> <i class="icon-th-list"></i>
      <h3>{{ $heading_title_so_contents }}</h3>
      <span class="pagination-totalItems">{{ $text_total }} {{ $store_orders_count }}</span>
    </div>
    <!-- /widget-header -->
    <div class="widget-content">
    	<div class="table-responsive">
			<table class="table table-striped table-bordered">
				<thead>
					<tr>
						<th>{{ $col_id }}</th>
						<th>{{ $col_box_no }}</th>
						<th><a href="{{ $sort_sku }}" class="@if( $sort_detail=='sku' ) {{ $order_detail }} @endif">{{ $col_upc }}</a></th>
						<th><a href="{{ $sort_short_name }}" class="@if( $sort_detail=='short_name' ) {{ $order_detail }} @endif">{{ $col_short_name }}</a></th>
						<th>{{ $col_issued }}</a></th>
						<th>{{ $col_received }}</th>
						<th>{{ $col_damaged }}</th>

					</tr>
				</thead>
				@if( !CommonHelper::arrayHasValue($store_orders) )
				<tr class="font-size-13">
					<td colspan="12" class="align-center">{{ $text_empty_results }}</td>
				</tr>
				@else
					@foreach( $store_orders as $so )
					<tr class="font-size-13">
						<td>{{ $counter++ }}</td>
						<td>{{ $so->box_code }}</td>
						<td>{{ $so->upc }}</td>
						<td>{{ $so->description }}</td>
						<td>{{ $so->moved_qty }}</td>
						<td></td>
						<td></td>
					</tr>
					@endforeach
				@endif
			</table>
		</div>
	</div>

	@if( CommonHelper::arrayHasValue($store_orders) )
    <h6 class="paginate">
		<span>{{ $store_orders->appends($arrFilters)->links() }}</span>
	</h6>
	@endif

</div>

<script type="text/javascript">
$(document).ready(function() {

    // Close SO
    $('.closeSO').click(function() {
    	var answer = confirm('{{ $text_warning }}');

		if (answer) {
			var store_code = $(this).data('id');
	    	$('#closeSO_' + store_code).submit();
		}
    });

	// Export List
    $('#exportMtsList').click(function() {
    	url = '';

    	url += '?id=' + encodeURIComponent('{{ $so_info->id }}');
		url += '&sort_detail=' + encodeURIComponent('{{ $sort_detail }}');
		url += '&order_detail=' + encodeURIComponent('{{ $order_detail }}');

      	location = "{{ $url_mts_export }}" + url;
    });
});
</script>