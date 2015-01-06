<div class="control-group">
	<a href="{{ $url_back }}" class="btn btn-info"> <i class="icon-chevron-left"></i> {{ $button_back }}</a>
	@if ( CommonHelper::valueInArray('CanExportInventoryDetails', $permissions) )
	<a class="btn btn-info" id="exportList">{{ $button_export }}</a>
	@endif
	@if ( CommonHelper::valueInArray('CanSyncInventoryDetails', $permissions) )
	<a class="btn btn-info">{{ $button_jda }}</a>
	@endif
</div>

<div class="control-group">
	<div class="controls">
		<div class="accordion" id="accordion2">
			<div class="accordion-group search-panel">
				{{ Form::open(array('url'=>'inventory/detail', 'class'=>'form-signin', 'id'=>'form-inventory', 'role'=>'form', 'method' => 'get')) }}
				<div id="collapseOne" class="accordion-body collapse in search-panel-content">
					<div class="span4">
						<div>
							<span class="search-po-left-pane">{{ $label_filter_slot_no }}</span>
							<div class="search-po-right-pane">
								{{ Form::text('slot', $slot, array('id'=>'slot', 'readonly'=>'readonly')) }}
								{{ Form::hidden('sku', $sku, array('id'=>'sku')) }}
							</div>
						</div>
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
		@if(CommonHelper::arrayHasValue($inventory) ) 
		    <h6 class="paginate">
				<span>{{ $inventory->appends($arrFilters)->links() }}&nbsp;</span>
			</h6>
		@else
			&nbsp;
		@endif
	</div>
</div>

<div class="widget widget-table action-table">
    <div class="widget-header"> <i class="icon-th-list"></i>
    	<h3>{{ $heading_title_details }}</h3>
      	<span class="pagination-totalItems">{{ $text_total }} {{ $inventory_count }}</span>
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
						<th><a href="{{ $sort_short_name }}" class="@if( $sort=='short_name' ) {{ $order }} @endif">{{ $col_prod_short_name }}</a></th>
						<th><a href="{{ $sort_quantity }}" class="@if( $sort=='quantity' ) {{ $order }} @endif">{{ $col_quantity }}</a></th>
						<th><a href="{{ $sort_expiry_date }}" class="@if( $sort=='expiry_date' ) {{ $order }} @endif">{{ $col_expiry_date }}</a></th>
					</tr>
				</thead>
				<tbody>
				@if( !CommonHelper::arrayHasValue($inventory) ) 
					<tr class="font-size-13">
						<td colspan="6" class="align-center">{{ $text_empty_results }}</td>
					</tr>
				@else
					@foreach( $inventory as $inv )
					<tr class="font-size-13">
						<td>{{ $counter++ }}</td>
						<td>{{ $inv->sku }}</td>
						<td>{{ $inv->upc }}</td>
						<td>{{ $inv->short_description }}</td>
						<td>{{ $inv->quantity }}</td>
						<td>{{ ($inv->created_at!='0000-00-00 00:00:00') ? date('M d, Y', strtotime($inv->created_at)) : '' }}</td>
					</tr>
					@endforeach
				@endif
				</tbody>
			</table>
		</div>
	</div>
	
	@if(CommonHelper::arrayHasValue($inventory) ) 
    <h6 class="paginate">
		<span>{{ $inventory->appends($arrFilters)->links() }}</span>
	</h6>
	@endif
</div>

<script type="text/javascript">
$(document).ready(function() {
	// Export List
    $('#exportList').click(function() {
    	url = '';
    	
		var slot = $('#slot').val();
		url += '?slot=' + encodeURIComponent(slot);
		
		var sku = $('#sku').val();
		url += '&sku=' + encodeURIComponent(sku);
				
		url += '&sort=' + encodeURIComponent('{{ $sort }}');
		url += '&order=' + encodeURIComponent('{{ $order }}');
		
      	location = "{{ $url_export }}" + url;
    });
});	
</script>