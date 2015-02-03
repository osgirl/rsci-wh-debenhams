<div class="control-group">
	<a href="{{ $url_back }}" class="btn btn-info  btn-darkblue"> <i class="icon-chevron-left"></i> {{ $button_back }}</a>

	@if ( CommonHelper::valueInArray('CanSyncLetDownDetails', $permissions))
	<a class="btn btn-info">{{ $button_jda }}</a>
	@endif

	@if ( CommonHelper::valueInArray('CanExportLetDownDetails', $permissions) )
	<a class="btn btn-info  btn-darkblue" id="exportList">{{ $button_export }}</a>
	@endif

	@if ( CommonHelper::valueInArray('CanCloseLetDownDetails', $permissions) )
		@if($letdown_info->lt_status == Config::get('letdown_statuses.moved'))
			<a id="closeLetdownDetailButton" style="width: 70px;" class="btn btn-success closeLetDown" data-id="{{ $letdown_info->move_doc_number }}">{{ $button_close_letdown }}</a>
		@elseif ($letdown_info->lt_status == Config::get('letdown_statuses.closed'))
			<a style="width: 70px;" disabled="disabled" class="btn btn-danger">{{ $button_close_letdown }}</a>
		@else
			<a style="width: 70px;" disabled="disabled" class="btn">{{ $button_close_letdown }}</a>
		@endif
		{{ Form::open(array('url'=>'letdown/close_letdown', 'id' => 'closeLetdown', 'style' => 'display:none; margin: 0px;')) }}
			{{ Form::hidden('id', $letdown_id) }}
			{{ Form::hidden('doc_no', $letdown_info->move_doc_number) }}
			{{ Form::hidden('sort', $sort) }}
			{{ Form::hidden('order', $order) }}
			{{ Form::hidden('page', $page) }}
			{{ Form::hidden('page_back', $page_back) }}
			{{ Form::hidden('sort_back', $sort_back) }}
			{{ Form::hidden('order_back', $order_back) }}
			{{ Form::hidden('filter_sku', $filter_sku) }}
			{{ Form::hidden('filter_store', $filter_store) }}
			{{ Form::hidden('filter_slot', $filter_slot) }}
			{{ Form::hidden('filter_doc_no', $filter_doc_no) }}
			{{ Form::hidden('module', 'letdown_detail') }}
	  	{{ Form::close() }}
	@endif

</div>

<div class="control-group">
	<div class="controls">
		<div class="accordion" id="accordion2">
          <div class="accordion-group" style="background-color: #FFFFFF;">
            {{ Form::open(array('url'=>$url_detail, 'class'=>'form-signin', 'id'=>'form-let-down', 'role'=>'form', 'method' => 'get')) }}
            <div id="collapseOne" class="accordion-body collapse in" style="padding-top: 20px;">
	                <div class="span4">
			        	<div>
				        	<span class="search-po-left-pane">{{ $label_upc }}</span>
				        	<span class="search-po-right-pane">
				        		{{ Form::text('filter_sku', $filter_sku, array('class'=>'login', 'placeholder'=>'', 'id'=>"filter_sku")) }}
				        	</span>
				        </div>
				        <div>
				        	<span class="search-po-left-pane">{{ $label_store }}</span>
				        	<span class="search-po-right-pane">
				        		{{ Form::text('filter_store', $filter_store, array('class'=>'login', 'placeholder'=>'', 'id'=>"filter_sku")) }}
				        	</span>
				        </div>
			      	</div>


			      	<div class="span4">
			        	<div>
				        	<span class="search-po-left-pane">{{ $label_slot }}</span>
				        	<span class="search-po-right-pane">
				        		{{ Form::text('filter_slot', $filter_slot, array('class'=>'login', 'placeholder'=>'', 'id'=>"filter_sku")) }}
				        	</span>
				        </div>
			      	</div>


			      	<div class="span11 control-group collapse-border-top">
			      		<a class="btn btn-success  btn-darkblue" id="submitForm">{{ $button_search }}</a>
		      			<a class="btn" id="clearForm">{{ $button_clear }}</a>
			      	</div>
            </div>
            {{ Form::hidden('filter_doc_no', $filter_doc_no) }}
            {{ Form::hidden('page_back', $page_back) }}
			{{ Form::hidden('sort_back', $sort_back) }}
			{{ Form::hidden('order_back', $order_back) }}
            {{ Form::hidden('sort', $sort) }}
		    {{ Form::hidden('order', $order) }}
            {{ Form::hidden('id',  $letdown_id) }}
		    {{ Form::hidden('doc_no', $letdown_info->move_doc_number) }}

            {{ Form::close() }}
          </div>
      	</div>

	</div> <!-- /controls -->
</div> <!-- /control-group -->

<div class="clear">
	<div class="div-paginate">
		@if(CommonHelper::arrayHasValue($letdowns) )
		    <h6 class="paginate">
				<span>{{ $letdowns->appends($arrFilters)->links() }}&nbsp;</span>
			</h6>
		@else
			&nbsp;
		@endif
	</div>
</div>

<div class="widget widget-table action-table">
    <div class="widget-header"> <i class="icon-th-list"></i>
      <h3>{{ $heading_title_letdown_contents }}</h3>
      <span class="pagination-totalItems">{{ $text_total }} {{ $letdowns_count }}</span>
    </div>
    <!-- /widget-header -->
    <div class="widget-content">
    	<div class="table-responsive">
			<table class="table table-striped table-bordered">
				<thead>
					<tr>
						<th>{{ $col_id }}</th>
						<th><a href="{{ $sort_sku }}" class="@if( $sort=='sku' ) {{ $order }} @endif">{{ $col_upc }}</a></th>
						<th><a href="#" class=""> SHORT DESCRIPTION </a></th>
						<th><a href="{{ $sort_slot }}" class="@if( $sort=='slot' ) {{ $order }} @endif">{{ $col_slot }}</a></th>
						<th><a href="{{ $sort_store }}" class="@if( $sort=='store' ) {{ $order }} @endif">{{ $col_store }}</th>
						<th>{{ $col_quantity_to_pick }}</th>
						<th>{{ $col_picked_quantity }}</th>
						<th>{{ $col_status }}</th>
					</tr>
				</thead>
				@if( !CommonHelper::arrayHasValue($letdowns) )
				<tr class="font-size-13">
					<td colspan="8" class="align-center">{{ $text_empty_results }}</td>
				</tr>
				@else
					@foreach( $letdowns as $ld )
					<tr class="font-size-13">
						<td>{{ $counter++ }}</td>
						<td>{{ $ld->sku }}</td>
						<td>{{ $ld->short_description }}</td>
						<td>{{ $ld->from_slot_code }}</td>
						<td>{{ $ld->store_name }}</td>
						<td>{{ $ld->quantity_to_letdown }}</td>
						<td>{{ $ld->moved_qty }}</td>
						@if($ld->move_to_picking_area != 0)
							<td>{{$status_in_picking}}</td>
						@else
							<td>{{$status_not_in_picking}}</td>
						@endif

					</tr>
					@endforeach
				@endif
			</table>
		</div>
	</div>

	@if( CommonHelper::arrayHasValue($letdowns) )
    <h6 class="paginate">
		<span>{{ $letdowns->appends($arrFilters)->links() }}</span>
	</h6>
	@endif

</div>

<script type="text/javascript">
$(document).ready(function() {

    // Close Letdown
    $('#closeLetdownDetailButton').click(function() {
    	var answer = confirm('{{ $text_warning }}');

		if (answer) {
	    	$('#closeLetdown').submit();
		}
    });

	// Export List
    $('#exportList').click(function() {
    	url = '';

    	url += '?id=' + encodeURIComponent('{{ $letdown_id }}');
		url += '&sort=' + encodeURIComponent('{{ $sort }}');
		url += '&order=' + encodeURIComponent('{{ $order }}');

      	location = "{{ $url_export }}" + url;
    });

     // Submit Form
    $('#submitForm').click(function() {
    	$('#form-let-down').submit();
    });

    $('#form-let-down input').keydown(function(e) {
		if (e.keyCode == 13) {
			$('#form-let-down').submit();
		}
	});

    // Clear Form
    $('#clearForm').click(function() {
    	$('#filter_sku, #filter_store, #filter_slot').val('');

		$('#form-let-down').submit();
    });
});
</script>