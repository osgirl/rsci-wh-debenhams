<div class="control-group">
	<a href="{{ $url_back }}" class="btn btn-info btn-darkblue"> <i class="icon-chevron-left"></i> {{ $button_back }}</a>
	@if ( CommonHelper::valueInArray('CanExportPacking', $permissions) )
	<a href="{{$url_export_detail}}" class="btn btn-info btn-darkblue"> {{ $button_export_detail }}</a>
	@endif

</div>

<div class="control-group">
	<div class="controls">
		<div class="accordion" id="accordion2">
          <div class="accordion-group" style="background-color: #FFFFFF;">
            {{ Form::open(array('url'=>$url_detail, 'class'=>'form-signin', 'id'=>'form-picking-detail', 'role'=>'form', 'method' => 'get')) }}
            <div id="collapseOne" class="accordion-body collapse in" style="padding-top: 20px;">
	                <div class="span4">
			        	<div>
				        	<span class="search-po-left-pane">{{ $entry_sku }}</span>
				        	<span class="search-po-right-pane">
				        		{{ Form::text('filter_sku', $filter_sku, array('class'=>'login', 'placeholder'=>'', 'id'=>"filter_sku")) }}
				        	</span>
				        </div>
				        <div>
				        	<span class="search-po-left-pane">{{ $entry_so }}</span>
				        	<span class="search-po-right-pane">
				        		{{ Form::text('filter_so', $filter_so, array('class'=>'login', 'placeholder'=>'', 'id'=>"filter_so")) }}
				        	</span>
				        </div>
			      	</div>


			      	<div class="span4">
			        	<div>
				        	<span class="search-po-left-pane">{{ $entry_from_slot_code }}</span>
				        	<span class="search-po-right-pane">
				        		{{ Form::text('filter_from_slot', $filter_from_slot, array('class'=>'login', 'placeholder'=>'', 'id'=>"filter_from_slot")) }}
				        	</span>
				        </div>
			      	</div>

			      	<div class="span3">

			      	</div>



			      	<div class="span11 control-group collapse-border-top">
			      		<a class="btn btn-success btn-darkblue" id="submitForm">{{ $button_search }}</a>
		      			<a class="btn" id="clearForm">{{ $button_clear }}</a>
			      	</div>
            </div>
            {{ Form::hidden('filter_type', $filter_type) }}
            {{ Form::hidden('filter_doc_no', $filter_doc_no) }}
            {{ Form::hidden('filter_status', $filter_status) }}
            {{ Form::hidden('page_back', $page_back) }}
			{{ Form::hidden('sort_back', $sort_back) }}
			{{ Form::hidden('order_back', $order_back) }}
            {{ Form::hidden('sort', $sort) }}
		    {{ Form::hidden('order', $order) }}
            {{ Form::hidden('picklist_doc',  $picklist_doc) }}

            {{ Form::close() }}
          </div>
      	</div>

	</div> <!-- /controls -->
</div> <!-- /control-group -->

<div class="clear">
	<div class="div-paginate">
		@if(CommonHelper::arrayHasValue($picklist_detail) )
		    <h6 class="paginate">
				<span>{{ $picklist_detail->appends($arrFilters)->links() }}&nbsp;</span>
			</h6>
		@else
			&nbsp;
		@endif
	</div>
</div>

<div class="widget widget-table action-table">
    <div class="widget-header"> <i class="icon-th-list"></i>
      <h3>{{ $heading_title_picking_details }}</h3>
      <span class="pagination-totalItems">{{ $text_total }} {{ $picklist_detail_count }}</span>
    </div>
    <!-- /widget-header -->
    <div class="widget-content">
    	<div class="table-responsive">
			<table class="table table-bordered">
				<thead>
					<tr>
						<th>{{ $col_no }}</th>
						<th><a href="{{ $sort_sku }}" class="@if( $sort=='sku' ) {{ $order }} @endif">{{ $col_sku }}</a></th>
						<th><a href="{{ $sort_upc }}" class="@if( $sort=='upc' ) {{ $order }} @endif">{{ $col_upc }}</a></th>
						<th>SHORT DESCRIPTION</th>
						<th><a href="{{ $sort_so_no }}" class="@if( $sort=='so_no' ) {{ $order }} @endif">{{ $col_so_no }}</a></th>
						<th><a href="{{ $sort_from_slot_code }}" class="@if( $sort=='from_slot_code' ) {{ $order }} @endif">{{ $col_from_slot_code }}</th>
						<th>{{ $col_qty_to_pick }}</th>
						<th>{{ $col_to_move }}</th>
						<th>{{ $col_store_name }}</th>
						<th>{{ $col_store_code }}</th>
						<!-- <th>{{ $col_status }}</th> -->
					</tr>
				</thead>
				@if( !CommonHelper::arrayHasValue($picklist_detail) )
				<tr class="font-size-13">
					<td colspan="10" class="align-center">{{ $text_empty_results }}</td>
				</tr>
				@else
					@foreach( $picklist_detail as $pd )
					<tr class="font-size-13"
					@if ( ($pd['quantity_to_pick'] - $pd['moved_qty']) > 0 )
						style="background-color:#F29F9F"
					@endif
					>
						<td>{{$counter++}}</td>
						<td>{{$pd['sku']}}</td>
						<td>{{$pd['upc']}}</td>
						<td>{{$pd['short_description']}}</td>
						<td>{{$pd['so_no']}}</td>
						<td>{{$pd['from_slot_code']}}</td>
						<td>{{$pd['quantity_to_pick']}}</td>
						<td>{{$pd['moved_qty']}}</td>
						<td>{{$pd['store_name']}}</td>
						<td>{{$pd['store_code']}}</td>

					</tr>
					@endforeach
				@endif
			</table>
		</div>
	</div>

	@if( CommonHelper::arrayHasValue($picklist_detail) )
    <h6 class="paginate">
		<span>{{ $picklist_detail->appends($arrFilters)->links() }}</span>
	</h6>
	@endif

</div>

<script type="text/javascript">
$(document).ready(function() {
	// Submit Form
    $('#submitForm').click(function() {
    	$('#form-picking-detail').submit();
    });

    $('#form-picking-detail').keydown(function(e) {
		if (e.keyCode == 13) {
			$('#form-picking-detail').submit();
		}
	});

    // Clear Form
    $('#clearForm').click(function() {
    	$('#filter_sku, #filter_so, #filter_from_slot').val(''); //,#filter_to_slot

		$('select').val('');
		$('#form-picking-detail').submit();
    });
});
</script>