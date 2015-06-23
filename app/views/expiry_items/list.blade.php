<div class="control-group">

</div>

<div class="control-group">
	<div class="controls">
		<div class="accordion" id="accordion2">
          <div class="accordion-group" style="background-color: #FFFFFF;">
            {{ Form::open(array('url'=>'expiry_items', 'class'=>'form-signin', 'id'=>'form-expiry-items', 'role'=>'form', 'method' => 'get')) }}
            <div id="collapseOne" class="accordion-body collapse in" style="padding-top: 20px;">
	                <div class="span3">
			        	<div>
				        	<span class="search-po-left-pane">{{ $label_purchase_no }}</span>
				        	<span class="search-po-right-pane">
				        		{{ Form::text('filter_po_no', $filter_po_no, array('class'=>'login', 'placeholder'=>'', 'id'=>"filter_po_no")) }}
				        	</span>
				        </div>
			      	</div>
			      	<div class="span4">
				        <div>
				        	<span class="search-po-left-pane">{{ $label_shipment_reference_no }}</span>
				        	<span class="search-po-right-pane">
				        		{{ Form::text('filter_shipment_reference_no', $filter_shipment_reference_no, array('class'=>'back-order', 'placeholder'=>'', 'id'=>"filter_shipment_reference_no")) }}
				        	</span>
				        </div>
			      	</div>
	                <div class="span3">
			        	<div>
				        	<span class="search-po-left-pane">{{ $label_date_from }}</span>
				        	<div class="search-po-right-pane input-append date">
								{{ Form::text('filter_from_date', null, array('class'=>'span2', 'id'=>"filter_from_date", 'readonly'=>'readonly')) }}
								<span class="add-on"><i class="icon-th"></i></span>
				        	</div>
				        </div>
			        	<div>
				        	<span class="search-po-left-pane">{{ $label_date_to }}</span>
				        	<div class="search-po-right-pane input-append date">
								{{ Form::text('filter_to_date', null, array('class'=>'span2', 'id'=>"filter_to_date", 'readonly'=>'readonly')) }}
								<span class="add-on"><i class="icon-th"></i></span>
				        	</div>
				        </div>
			      	</div>
			      	<div class="span11 control-group collapse-border-top">
			      		<a class="btn btn-success btn-darkblue" id="submitForm">{{ $button_search }}</a>
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
		@if(CommonHelper::arrayHasValue($expiry_items) )
		    <h6 class="paginate">
				<span>{{ $expiry_items->appends($arrFilters)->links() }}&nbsp;</span>
			</h6>
		@else
			&nbsp;
		@endif
	</div>
	<div class="div-buttons">
		@if ( CommonHelper::valueInArray('CanExportExpiryItems', $permissions) )
		<a href= {{ $url_export }} class="btn btn-info btn-darkblue">{{ $button_export }}</a> <!--  id="exportList" -->
		@endif
	</div>
</div>

<div class="widget widget-table action-table">
    <div class="widget-header"> <i class="icon-th-list"></i>
      <h3>{{ $heading_title_po_contents }}</h3>
      <span class="pagination-totalItems">{{ $text_total }} {{ $expiry_items_count }}</span>
    </div>
    <!-- /widget-header -->
    <div class="widget-content">
    	<div class="table-responsive">
			<table class="table table-striped table-bordered">
				<thead>
					<tr>
						<th>{{ $col_id }}</th>
						<th>{{ $col_shipment_ref_no }}</th>
						<th><a href="{{ $sort_po_no }}" class="@if( $sort=='purchase_order_no' ) {{ $order }} @endif">{{ $col_purchase_order_no }}</a></th>
						<th>{{ $col_sku }}</th>
						<th>{{ $col_upc }}</th>
						<th>{{ $col_slot }}</th>
						<th>{{ $col_short_name }}</th>
						<th><a href="{{ $sort_expiry_date }}" class="@if( $sort=='expiry_date' ) {{ $order }} @endif">{{ $col_expiry_date }}</a></th>
						<!-- <th>{{ $col_expected_quantity }}</th> -->
						<th>{{ $col_received_quantity }}</th>
						<th>{{ $col_received_by }}</th>
						<!-- <th> VARIANCE </th> -->
					</tr>
				</thead>
				@if( !CommonHelper::arrayHasValue($expiry_items) )
				<tr class="font-size-13">
					<td colspan="10" class="align-center">{{ $text_empty_results }}</td>
				</tr>
				@else
					@foreach( $expiry_items as $po )
					<tr class="font-size-13">
						<td>{{ $counter++ }}</td>
						<td>{{ $po->shipment_reference_no }}</td>
						<td>{{ $po->purchase_order_no }}</td>
						<td>{{ $po->sku }}</td>
						<td>{{ $po->upc }}</td>
						<td>{{ $po->slot_code }}</td>
						<td>{{ $po->short_description }}</td>
						<td>
							@if ($po->expiry_date == '0000-00-00 00:00:00' )
								N/A
							@else
								{{ date('M d, Y', strtotime($po->expiry_date)) }}
							@endif
						</td>
						<!-- <td>{{ $po->quantity_ordered }}</td> -->
						<td>{{ $po->quantity_delivered }}</td>
						<td>{{ $po->firstname .' '. $po->lastname}}</td>
						<!-- <td>{{ $po->quantity_ordered- $po->quantity_delivered }}</td> -->
					</tr>
					@endforeach
				@endif
			</table>
		</div>
	</div>

	@if( CommonHelper::arrayHasValue($expiry_items) )
    <h6 class="paginate">
		<span>{{ $expiry_items->appends($arrFilters)->links() }}</span>
	</h6>
	@endif

	<!-- /widget-content -->
</div>

<script type="text/javascript">
$(document).ready(function() {
	// Submit Form
    $('#submitForm').click(function() {
    	$('#form-expiry-items').submit();
    });
    $('.date').datepicker({
      format: 'yyyy-mm-dd'
    });

    // Clear Form
    $('#clearForm').click(function() {
    	$('#filter_po_no, #filter_shipment_reference_no').val('');
		$('#filter_from_date, #filter_to_date').val('');

		$('select').val('');
		$('#form-expiry-items').submit();
    });

});
</script>
