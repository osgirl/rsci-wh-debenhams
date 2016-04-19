@if( CommonHelper::arrayHasValue($error) )
    <div class="alert alert-danger">
    	<button class="close" data-dismiss="alert" type="button">&times;</button>
    	{{ $error }}
    </div>
@endif

@if( CommonHelper::arrayHasValue($success) )
    <div class="alert alert-success">
    	<button class="close" data-dismiss="alert" type="button">&times;</button>
    	{{ $success }}
    </div>
@endif

<div class="control-group">
	<div class="controls">
		<div class="accordion" id="accordion2">
          <div class="accordion-group" style="background-color: #FFFFFF;">
            {{ Form::open(array('url'=>'store_order', 'class'=>'form-signin', 'id'=>'form-store-order', 'role'=>'form', 'method' => 'get')) }}
            <div id="collapseOne" class="accordion-body collapse in" style="padding-top: 20px;">
	                <div class="span4">
			        	<div>
				        	<span class="search-po-left-pane">TL Number :</span>
				        	<span class="search-po-right-pane">
				        		{{ Form::text('filter_so_no', $filter_so_no, array('class'=>'login', 'placeholder'=>'', 'id'=>"filter_so_no")) }}
				        	</span>
				        </div>
				        <div>
				        	<span class="search-po-left-pane">{{ $label_status }}</span>
				        	<span class="search-po-right-pane">
				        		{{ Form::select('filter_status', array('' => $text_select) + $so_status_type, $filter_status, array('class'=>'select-width', 'id'=>"filter_status")) }}
				        	</span>
				        </div>
			      	</div>
			      	<div class="span4">
			      		<div>
				        	<span class="search-po-left-pane">{{ $label_store }}</span>
				        	<span class="search-po-right-pane">
				        		{{ Form::select('filter_store', array('' => $text_select) + $store_list, $filter_store, array('class'=>'select-width', 'id'=>"filter_store")) }}
				        	</span>
				        </div>
				     </div>
			      	<div class="span3">
			      		<div>
				        	<span class="search-po-left-pane">{{ $label_order_date }}</span>
				        	<div class="search-po-right-pane input-append date">
								{{ Form::text('filter_order_date', $filter_order_date, array('class'=>'span2', 'id'=>"filter_order_date", 'readonly'=>'readonly')) }}
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
		@if(CommonHelper::arrayHasValue($store_orders) )
		    <h6 class="paginate">
				<span>{{ $store_orders->appends($arrFilters)->links() }}&nbsp;</span>
			</h6>
		@else
			&nbsp;
		@endif
	</div>
	<div class="btn-group div-buttons">
        <button type="button" class="btn btn-info btn-darkblue dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> Report  <span class="caret"></span>
        </button>
        <ul class="dropdown-menu">
          <li><a href="#">Store Receiving Discrepansy</a></li>
        </ul>
  	</div>
</div>

<div class="widget widget-table action-table">
    <div class="widget-header"> <i class="icon-th-list"></i>
      <h3>{{ $heading_title }}</h3>
      <span class="pagination-totalItems">{{ $text_total }} {{ $store_orders_count }}</span>
    </div>
    <!-- /widget-header -->
    <div class="widget-content">
    	<div class="table-responsive">
			<table class="table table-striped table-bordered">
				<thead>
					<tr>
						<th>{{ $col_id }}</th>
						<th><a href="{{ $sort_so_no }}" class="@if( $sort=='so_no' ) {{ $order }} @endif">Load Code</a></th>
						<th><a href="{{ $sort_so_no }}" class="@if( $sort=='so_no' ) {{ $order }} @endif">TL Number</a></th>
						<th>{{ $col_store }}</th>
						<th>{{ $col_store_name }}</th>
						<th><a href="{{ $sort_order_date }}" class="@if( $sort=='order_date' ) {{ $order }} @endif">Ship By Date</a></th>
						<th>{{ $col_status }}</th>
						<th>{{ $col_action }}</th>
					</tr>
				</thead>
				@if( !CommonHelper::arrayHasValue($store_orders) )
				<tr class="font-size-13">
					<td colspan="9" style="text-align: center;">{{ $text_empty_results }}</td>
				</tr>
				@else
					@foreach( $store_orders as $so )
					<tr class="font-size-13 tblrow" data-id="{{ $so->so_no }}">
						<td>{{ $counter++ }}</td>
						<td><a href="{{ $url_detail . '&id='.$so->id.'&so_no=' . $so->so_no }}">{{ $so->load_code }}</a></td>
						<td><a href="{{ $url_detail . '&id='.$so->id.'&so_no=' . $so->so_no }}">{{ $so->so_no }}</a></td>
						<td>{{ $so->store_code }}</td>
						<td>{{ $so->store_name }}</td>
						<td>{{ date("M d, Y", strtotime($so->order_date)) }}</td>
						<td>{{$so_status_type[$so->so_status]}}</td>
						<td class="align-center">
							<a href="{{ $url_mts_detail . '&id='.$so->id.'&so_no=' . $so->so_no }}" class="icon-share" title="{{ $link_view_mts }}"></a>&nbsp;
						</td>
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
    $('.date').datepicker({
      format: 'yyyy-mm-dd'
    });


    // Close SO
    $('.closeSO').click(function() {
    	var answer = confirm('{{ $text_warning }}');

		if (answer) {
			var so_no = $(this).data('id');
	    	$('#closeSO_' + so_no).submit();
		}
    });

    // Submit Form
    $('#submitForm').click(function() {
    	$('#form-store-order').submit();
    });

    $('#form-store-order input').keydown(function(e) {
		if (e.keyCode == 13) {
			$('#form-store-order').submit();
		}
	});

    // Clear Form
    $('#clearForm').click(function() {
    	$('#filter_so_no').val('');
		$('#filter_order_date').val('');

		$('select').val('');
		$('#form-store-order').submit();
    });

	// Export List
    $('#exportList').click(function() {
    	url = '';

		var filter_so_no = $('#filter_so_no').val();
		url += '?filter_so_no=' + encodeURIComponent(filter_so_no);

		var filter_store = $('#filter_store').val();
		url += '&filter_store=' + encodeURIComponent(filter_store);

		var filter_order_date = $('#filter_order_date').val();
		url += '&filter_order_date=' + encodeURIComponent(filter_order_date);

		var filter_status = $('select[name=\'filter_status\']').val();
		url += '&filter_status=' + encodeURIComponent(filter_status);

		url += '&sort=' + encodeURIComponent('{{ $sort }}');
		url += '&order=' + encodeURIComponent('{{ $order }}');

      	location = "{{ $url_export }}" + url;
    });

    // Select
    $('.tblrow').click(function() {
    	var rowid = $(this).data('id');

    	if ($('#selected-' + rowid).length>0) {
	    	if ($('#selected-' + rowid).is(':checked')) {
	    		$('#selected-' + rowid).prop('checked', false);
	    		$(this).children('td').removeClass('tblrow-active');
	    	} else {
	    		$('#selected-' + rowid).prop('checked', true);
	    		$(this).children('td').addClass('tblrow-active');
	    	}
    	} else {
    		$(this).children('td').removeClass('tblrow-active');
    	}
    });

    $('.item-selected').click(function() {
    	var rowid = $(this).data('id');

    	if ($(this).is(':checked')) {
    		$(this).prop('checked', false);
    		$(this).children('td').removeClass('tblrow-active');
    	} else {
    		$(this).prop('checked', true);
    		$(this).children('td').addClass('tblrow-active');
    	}
    });

    $('#main-selected').click(function() {
    	if ($('#main-selected').is(':checked')) {
    		$('input[name*=\'selected\']').prop('checked', true);
    		$('.table tbody tr > td').addClass('tblrow-active');
    	} else {
    		$('input[name*=\'selected\']').prop('checked', false);
    		$('.table tbody tr > td').removeClass('tblrow-active');
    	}
   	});
});
</script>