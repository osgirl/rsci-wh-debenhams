<div class="control-group">
	<div class="controls">
		<div class="accordion" id="accordion2">
			<div class="accordion-group search-panel">
				{{ Form::open(array('url'=>'unlisted', 'class'=>'form-signin', 'id'=>'form-unlisted', 'role'=>'form', 'method' => 'get')) }}
				<div id="collapseOne" class="accordion-body collapse in search-panel-content">
					<div class="span4">
						<div>
							<span class="search-po-left-pane">{{ $label_filter_reference_no }}</span>
							<span class="search-po-right-pane">
								{{ Form::text('filter_reference_no', $filter_reference_no, array('id'=>'filter_reference_no', 'placeholder'=>'')) }}
							</span>
						</div>
						<div>
							<span class="search-po-left-pane">{{ $label_filter_upc }}</span>
							<span class="search-po-right-pane">
								{{ Form::text('filter_sku', $filter_sku, array('id'=>'filter_sku', 'placeholder'=>'')) }}
							</span>
						</div>
						<div>
							<span class="search-po-left-pane">{{ $label_filter_shipment_reference_no }}</span>
							<span class="search-po-right-pane">
								{{ Form::text('filter_shipment_reference_no', $filter_shipment_reference_no, array('id'=>'filter_shipment_reference_no', 'placeholder'=>'')) }}
							</span>
						</div>
					</div>

					<div class="span11 control-group collapse-border-top" style="margin-top: 6px;">
						<a class="btn btn-success  btn-darkblue" id="submitForm">{{ $button_search }}</a>
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
		@if(CommonHelper::arrayHasValue($unlisted) )
		    <h6 class="paginate">
				<span>{{ $unlisted->appends($arrFilters)->links() }}&nbsp;</span>
			</h6>
		@else
			&nbsp;
		@endif
	</div>
	<div class="div-buttons">
		@if ( CommonHelper::valueInArray('CanExportUnlisted', $permissions) )
		<a class="btn btn-info  btn-darkblue" id="exportList">{{ $button_export }}</a>
		@endif
	</div>
</div>

<div class="widget widget-table action-table">
	<div class="widget-header"> <i class="icon-th-list"></i>
    	<h3>{{ $heading_title }}</h3>
     	<span class="pagination-totalItems">{{ $text_total }} {{ $unlisted_count }}</span>
    </div>
    <!-- /widget-header -->

    <div class="widget-content">
    	<div class="table-responsive">
			<table class="table table-striped table-bordered">
				<thead>
					<tr>
						<th>{{ $col_id }}</th>
						<th><a href="{{ $sort_sku }}" class="@if( $sort=='sku' ) {{ $order }} @endif"> {{ $col_upc }} </a></th>
						<th><a href="{{ $sort_reference }}" class="@if( $sort=='reference_no' ) {{ $order }} @endif">{{ $col_reference }}</a></th>
						<th>{{ $col_shipment_reference }}</th>
						<th>{{ $col_delivery_date }}</th>
						<th>{{ $col_quantity_received }}</th>
						<th>{{ $col_description }}</th>
						<th>{{ $col_style_no }}</th>
						<th>{{ $col_brand }}</th>
						<th>{{ $col_division }}</th>
						<th>{{ $col_scanned_by }}</th>
					</tr>
				</thead>
				<tbody>
				@if( !CommonHelper::arrayHasValue($unlisted) )
					<tr class="font-size-13">
						<td colspan="11" class="align-center">{{ $text_empty_results }}</td>
					</tr>
				@else
					@foreach($unlisted as $unlist)
					<tr class="font-size-13">
						<td>{{ $counter++ }}</td>
						<td>{{ $unlist['sku'] }}</td>
						<td>{{ $unlist['reference_no'] }}</td>
						<td>{{ $unlist['shipment_reference_no'] }}</td>
						<td>{{ date('m/d/Y',strtotime($unlist['delivery_date'])) }}</td>
						<td>{{ $unlist['quantity_received'] }}</td>
						<td>{{ $unlist['description'] }}</td>
						<td>{{ $unlist['style_no'] }}</td>
						<td>{{ $unlist['brand'] }}</td>
						<td>{{ $unlist['division'] }}</td>
						<td>{{ $unlist['firstname'] .' '. $unlist['lastname']}}</td>
					</tr>
					@endforeach
				@endif
				</tbody>
			</table>
		</div>
	</div>

	@if(CommonHelper::arrayHasValue($unlisted) )
    <h6 class="paginate">
		<span>{{ $unlisted->appends($arrFilters)->links() }}</span>
	</h6>
	@endif
</div>

<script type="text/javascript">
$(document).ready(function() {
    // Submit Form
    $('#submitForm').click(function() {
    	$('#form-unlisted').submit();
    });

    $('#form-unlisted input').keydown(function(e) {
		if (e.keyCode == 13) {
			$('#form-unlisted').submit();
		}
	});

    // Clear Form
    $('#clearForm').click(function() {
    	$('#filter_reference_no, #filter_sku, #filter_shipment_reference_no').val('');
    	$('#form-unlisted').submit();
    });

	// Export List
    $('#exportList').click(function() {
    	@if( !CommonHelper::arrayHasValue($unlisted) )
    		alert('Empty Data. There is nothing to export');
    	@elseif($ship_ref_count>1)
    		alert('Multiple Shipment Reference Number is invalid. Search for specific Shipment Reference Number.');
    	@else{
	    	url = '';

			var filter_reference_no = $('#filter_reference_no').val();
			url += '?filter_reference_no=' + encodeURIComponent(filter_reference_no);
			var filter_sku = $('#filter_sku').val();
			url += '&filter_sku=' + encodeURIComponent(filter_sku);
			var filter_shipment_reference_no = $('#filter_shipment_reference_no').val();
			url += '&filter_shipment_reference_no=' + encodeURIComponent(filter_shipment_reference_no);

			url += '&sort=' + encodeURIComponent('{{ $sort }}');
			url += '&order=' + encodeURIComponent('{{ $order }}');

	      	location = "{{ $url_export }}" + url;
	    }
	    @endif
    });
});
</script>