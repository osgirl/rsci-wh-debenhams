<div class="control-group">
	<div class="controls">
		<div class="accordion" id="accordion2">
			<div class="accordion-group search-panel">
				{{ Form::open(array('url'=>'inventory', 'class'=>'form-signin', 'id'=>'form-inventory', 'role'=>'form', 'method' => 'get')) }}
				<div id="collapseOne" class="accordion-body collapse in search-panel-content">
					<div class="span4">
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
					
					<div class="span6">
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
					<!--TODO:Remove this if not needed-->
<!-- 				<div class="span3">
						<div>
							<span class="search-po-left-pane">{{ $label_filter_slot_no }}</span>
							<span class="search-po-right-pane">
								{{ Form::text('filter_slot_no', $filter_slot_no, array('id'=>'filter_slot_no', 'placeholder'=>'', 'maxlength'=>'100')) }}
							</span>
						</div>
					</div>
					 -->
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
		@if(CommonHelper::arrayHasValue($inventory) ) 
		    <h6 class="paginate">
				<span>{{ $inventory->appends($arrFilters)->links() }}&nbsp;</span>
			</h6>
		@else
			&nbsp;
		@endif
	</div>
	<div class="div-buttons">
		@if ( CommonHelper::valueInArray('CanExportInventory', $permissions) )
		<a class="btn btn-info" id="exportList">{{ $button_export }}</a>
		@endif
		@if ( CommonHelper::valueInArray('CanSyncInventory', $permissions) )
		<a class="btn btn-info">{{ $button_jda }}</a>
		@endif
	</div>
</div>

<div class="widget widget-table action-table">
    <div class="widget-header"> <i class="icon-th-list"></i>
    	<h3>{{ $heading_title }}</h3>
      	<span class="pagination-totalItems">{{ $text_total }} {{ $inventory_count }}</span>
    </div>
    <!-- /widget-header -->
    
    <div class="widget-content">
    	<div class="table-responsive">
			<table class="table table-striped table-bordered">
				<thead>
					<tr>
						<th>{{ $col_id }}</th>
						<!--TODO:Remove this if not needed-->
						<th><a href="{{ $sort_sku }}" class="@if( $sort=='sku' ) {{ $order }} @endif">{{ $col_prod_sku }}</a></th>
						<th><a href="{{ $sort_upc }}" class="@if( $sort=='upc' ) {{ $order }} @endif">{{ $col_prod_upc }}</a></th>
						<th><a href="{{ $sort_short_name }}" class="@if( $sort=='short_name' ) {{ $order }} @endif">{{ $col_prod_short_name }}</a></th>
						<th><a href="{{ $sort_slot_no }}" class="@if( $sort=='slot_no' ) {{ $order }} @endif">{{ $col_slot_no }}</a></th>
						<!-- <th>{{ $col_slot_no }}</th> -->
						<th><a href="{{ $sort_quantity }}" class="@if( $sort=='quantity' ) {{ $order }} @endif">{{ $col_total_quantity }}</th>
						<th><a href="{{ $sort_expiry_date }}" class="@if( $sort=='expiry_date' ) {{ $order }} @endif">{{ $col_earliest_expiry_date }}</a></th>
					</tr>
				</thead>
				<tbody>
				@if( !CommonHelper::arrayHasValue($inventory) ) 
					<tr class="font-size-13">
						<td colspan="7" class="align-center">{{ $text_empty_results }}</td>
					</tr>
				@else
					@foreach( $inventory as $inv )
					<tr class="font-size-13">
						<td>{{ $counter++ }}</td>
						<!--TODO:Remove this if not needed-->
						<!-- <td>
							@if ( CommonHelper::valueInArray('CanAccessInventoryDetails', $permissions) )
								<a href="{{ $url_details . '&slot=' . $inv->slot_id . '&sku=' . $inv->sku }}" title="{{ $text_view_details }}">{{ $inv->slot_id }}</a></td>
							@else
								{{ $inv->slot_id }}
							@endif -->
						<td>{{ $inv->upc }}</td>
						<td>{{ $inv->sku }}</td>
						<td>{{ $inv->short_description }}</td>
						<td>{{ $inv->slot_id }}</td>
						<td>{{ $inv->total_qty }}</td>
						<td>{{ ($inv->early_expiry!='0000-00-00 00:00:00') ? date('M d, Y', strtotime($inv->early_expiry)) : '' }}</td>
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
	$('.date').datepicker({
      format: 'yyyy-mm-dd'
    });
	
    // Submit Form
    $('#submitForm').click(function() {
    	$('#form-inventory').submit();
    });
    
    $('#form-inventory input').keydown(function(e) {
		if (e.keyCode == 13) {
			$('#form-inventory').submit();
		}
	});
    
    // Clear Form
    $('#clearForm').click(function() {
    	$('#filter_prod_sku').val('');
		$('#filter_prod_upc').val('');
		$('#filter_date_from').val('');
		$('#filter_date_to').val('');
		$('#filter_slot_no').val('');
		$('#form-inventory').submit();
    });
    
    // Export List
    $('#exportList').click(function() {
    	url = '';
    	
		var filter_prod_sku = $('#filter_prod_sku').val();
		url += '?filter_prod_sku=' + encodeURIComponent(filter_prod_sku);
		
		var filter_prod_upc = $('#filter_prod_upc').val();
		url += '&filter_prod_upc=' + encodeURIComponent(filter_prod_upc);
		
		var filter_date_from = $('#filter_date_from').val();
		url += '&filter_date_from=' + encodeURIComponent(filter_date_from);
		
		var filter_date_to = $('#filter_date_to').val();
		url += '&filter_date_to=' + encodeURIComponent(filter_date_to);
		
		var filter_slot_no = $('#filter_slot_no').val();
		url += '&filter_slot_no=' + encodeURIComponent(filter_slot_no);
				
		url += '&sort=' + encodeURIComponent('{{ $sort }}');
		url += '&order=' + encodeURIComponent('{{ $order }}');
		
      	location = "{{ $url_export }}" + url;
    });
});	
</script>