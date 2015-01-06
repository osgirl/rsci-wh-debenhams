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
	<a href="{{ $url_back }}" class="btn btn-info"> <i class="icon-chevron-left"></i> {{ $button_back }}</a>
	@if ( CommonHelper::valueInArray('CanUnlockPicking', $permissions))
		@if($sum_moved ==0 && $sum_moved_qty ==0)	
			<a class="btn btn-info" id="unlock-picking-tag">{{ $button_unlock_tag }}</a> 
			{{ Form::open(array('url'=>$url_unlock, 'class'=>'form-signin', 'id'=>'form-unlock', 'role'=>'form', 'method' => 'post')) }}

			{{ Form::hidden('filter_stock_piler', $filter_stock_piler) }}
			{{ Form::hidden('filter_doc_no', $filter_doc_no) }}
			{{ Form::hidden('filter_sku', $filter_sku) }}
			{{ Form::hidden('page', $page_back) }}
			{{ Form::hidden('sort', $sort_back) }}
		    {{ Form::hidden('order', $order_back) }}
		    {{ Form::hidden('lock_tag', $lock_tag) }}
            
            {{ Form::close() }}
		@else
			<a class="btn btn-danger" disabled=true>{{ $button_unlock_tag }}</a> 
		@endif
	@endif
</div>

<div class="widget widget-table action-table">
    <div class="widget-header"> <i class="icon-th-list"></i>
      <h3>{{ $heading_title_picking_lock_tags }}</h3>
      <span class="pagination-totalItems">{{ $text_total }} {{ $lock_tag_details_count }}</span>
    </div>

    <div class="widget-content">
    	<div class="table-responsive">
			<table class="table table-striped table-bordered">
				<thead>
					<tr>
						<th class="align-center">{{ $col_doc_number }}</th>
						<th class="align-center">{{ $col_upc }}</th>
						<th class="align-center">{{ $col_product_name }}</th>
						<th class="align-center">{{ $col_store_code }}</th>
						<th class="align-center">{{ $col_store }}</th>
					</tr>
				</thead>
				@if( !CommonHelper::arrayHasValue($lock_tag_details) ) 
				<tr class="font-size-13">
					<td colspan="9" style="text-align: center;">{{ $text_empty_results }}</td>
				</tr>
				@else
					@foreach( $lock_tag_details as $value )
					<tr class="font-size-13 tblrow" data-id="{{ $value->lock_tag  }}">
						<td>{{ $value->move_doc_number }}</td>
						<td>{{ $value->upc }}</td>
						<td>{{ $value->description }}</td>
						<td>{{ $value->store_code }}</td>
						<td>{{ $value->store_name }}</td>
					</tr>
					@endforeach
				@endif
			</table>
		</div>
	</div>
    
</div>

<script type="text/javascript">
$(document).ready(function() {
	//unlock tag
    $('#unlock-picking-tag').click(function() {
    	var answer = confirm('{{ $text_warning_unlock_single }}')
		if (answer) {
    		$('#form-unlock').submit();
    	} else {
			return false;
		}
    });

		
 });	
</script>
