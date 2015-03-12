<div class="control-group">
	<div class="controls">
		<div class="accordion" id="accordion2">
			<div class="accordion-group search-panel">
				{{ Form::open(array('url'=>'load/list', 'class'=>'form-signin', 'id'=>'form-loads', 'role'=>'form', 'method' => 'get')) }}
				<div id="collapseOne" class="accordion-body collapse in search-panel-content">
					<div class="span4">
						<div>
							<span class="search-po-left-pane">{{ $label_filter_load_code }}</span>
							<span class="search-po-right-pane">
								{{ Form::text('filter_load_code', $filter_load_code, array('id'=>'filter_load_code', 'placeholder'=>'')) }}
							</span>
						</div>
					</div>

					<div class="span11 control-group collapse-border-top" style="margin-top: 6px;">
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
		@if(CommonHelper::arrayHasValue($loads) )
		    <h6 class="paginate">
				<span>{{ $loads->appends($arrFilters)->links() }}&nbsp;</span>
			</h6>
		@else
			&nbsp;
		@endif
	</div>
	<div class="div-buttons">
		@if ( CommonHelper::valueInArray('CanExportShipping', $permissions) )
		<a class="btn btn-info btn-darkblue" id="exportList">{{ $button_export }}</a>
		@endif
	</div>
</div>

<div class="widget widget-table action-table">
	<div class="widget-header"> <i class="icon-th-list"></i>
    	<h3>{{ $heading_title }}</h3>
     	<span class="pagination-totalItems">{{ $text_total }} {{ $load_count }}</span>
    </div>
    <!-- /widget-header -->

    <div class="widget-content">
    	<div class="table-responsive">
			<table class="table table-striped table-bordered">
				<thead>
					<tr>
						<th width="10%">{{ $col_id }}</th>
						<th><a href="{{ $sort_load_code }}" class="@if( $sort=='load_code' ) {{ $order }} @endif">{{ $col_load_no }}</a></th>
						<th>STORES</th>
						<th><a href="{{ $sort_status }}" class="@if( $sort=='status' ) {{ $order }} @endif">{{ $col_status }}</a></th>
						<th>{{$col_action}}</th>
					</tr>
				</thead>
				<tbody>
				@if( !CommonHelper::arrayHasValue($loads) )
					<tr class="font-size-13">
						<td colspan="5" class="align-center">{{ $text_empty_results }}</td>
					</tr>
				@else
					@foreach($loads as $load)
					<tr class="font-size-13">
						<td>{{ $counter++ }}</td>
						<td>{{ $load['load_code'] }}</td>
						<td>{{ $load['stores'] }}</td>
						<td>
							@if( $load['is_shipped'] == 0 )
								{{$text_ship}}
							@else
								{{$text_shipped}}
							@endif
						</td>
						<td>
							@if( $load['pl_status'] == 17 )
								<a disabled class="btn btn-info">{{ $button_ship }}</a>
							@elseif( $load['is_shipped'] == 0 )
								@if ( CommonHelper::valueInArray('CanAccessShipping', $permissions))
								<a class="btn btn-info shipLoad" data-id="{{ $load['id'] }}">{{ $button_ship }}</a>
						  		{{ Form::open(array('url'=>$url_ship_load,'id' => 'formLoadShip_' . $load['id'], 'style' => 'margin: 0px;display:none;', 'method'=> 'post')) }}
									{{ Form::hidden('load_code', $load['load_code']) }}
									{{ Form::hidden('id', $load['id']) }}
						  		{{ Form::close() }}
								@endif
							@else
								<a disabled class="btn btn-danger">{{ $button_shipped }}</a>
							@endif

								&nbsp;&nbsp;<a href="{{url('load/print/' . $load['load_code'])}}" target="_blank" class="btn btn-danger">Print MTS</a>
								&nbsp;&nbsp;<a href="{{url('load/printboxlabel/' .$load['load_code'] )}}" target="_blank" class="btn btn-success">Print Box Label</a>
								&nbsp;&nbsp;<a href="{{url('load/printloadingsheet/' . $load['load_code'])}}" target="_blank" class="btn btn-info">Print Loading Sheet</a>
								&nbsp;&nbsp;<a href="{{url('load/printpacklist/' . $load['load_code'])}}" target="_blank" class="btn btn-default">Print Packing List</a>
						</td>
					</tr>
					@endforeach
				@endif
				</tbody>
			</table>
		</div>
	</div>

	@if(CommonHelper::arrayHasValue($loads) )
    <h6 class="paginate">
		<span>{{ $loads->appends($arrFilters)->links() }}</span>
	</h6>
	@endif
</div>

<script type="text/javascript">
$(document).ready(function() {
    // Submit Form
    $('#submitForm').click(function() {
    	$('#form-loads').submit();
    });

    $('#form-loads input').keydown(function(e) {
		if (e.keyCode == 13) {
			$('#form-loads').submit();
		}
	});

    // Clear Form
    $('#clearForm').click(function() {
    	$('#filter_load_code').val('');
    	$('#form-loads').submit();
    });

    // Ship load
    $('.shipLoad').click(function() {
    	var answer = confirm('{{ $text_confirm_load }}');
    	if (answer) {
    		var load_code = $(this).data('id');
	    	$('#formLoadShip_' + load_code).submit();
    	}
    });

	// Export List
    $('#exportList').click(function() {
    	url = '';

		var filter_load_code = $('#filter_load_code').val();
		url += '?filter_load_code=' + encodeURIComponent(filter_load_code);

		url += '&sort=' + encodeURIComponent('{{ $sort }}');
		url += '&order=' + encodeURIComponent('{{ $order }}');

      	location = "{{ $url_export }}" + url;
    });
});
</script>