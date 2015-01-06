<div class="control-group">
	<a href="{{ $url_back }}" class="btn btn-info"> <i class="icon-chevron-left"></i> {{ $button_back }}</a>	
	<a class="btn btn-info" id="exportList">{{ $button_export }}</a>

</div>

<div class="control-group">
	<div class="controls">
		<div class="accordion" id="accordion2">
          <div class="accordion-group" style="background-color: #FFFFFF;">
          	{{ Form::open(array('url'=>$url_detail, 'class'=>'form-signin', 'id'=>'form-box-detail', 'role'=>'form', 'method' => 'get')) }}
            <div id="collapseOne" class="accordion-body collapse in" style="padding-top: 20px;">
	                <div class="span4">
			        	<div>
				        	<span class="search-po-left-pane">{{ $label_upc }}</span>
				        	<span class="search-po-right-pane">
				        		{{ Form::text('filter_sku', $filter_sku, array('class'=>'login', 'placeholder'=>'', 'id'=>"filter_sku")) }}
				        	</span>
				        </div>
			      	</div>
			      	<div class="span11 control-group collapse-border-top">
			      		<a class="btn btn-success" id="submitForm">{{ $button_search }}</a>
		      			<a class="btn" id="clearForm">{{ $button_clear }}</a>
			      	</div>
            </div>
            {{ Form::hidden('box_code', $box_code) }}
            {{ Form::hidden('page_back', $page_back) }}
			{{ Form::hidden('sort_back', $sort_back) }}
			{{ Form::hidden('order_back', $order_back) }}
            {{ Form::hidden('sort', $sort) }}
		    {{ Form::hidden('order', $order) }}
            {{ Form::hidden('id',  $box_id) }}
            
            {{ Form::close() }}
          </div>
      	</div>
          
	</div> <!-- /controls -->	
</div> <!-- /control-group -->

<div class="clear">
	<div class="div-paginate">
		@if(CommonHelper::arrayHasValue($boxes) ) 
		    <h6 class="paginate">
				<span>{{ $boxes->appends($arrFilters)->links() }}&nbsp;</span>
			</h6>
		@else
			&nbsp;
		@endif
	</div>
</div>

<div class="widget widget-table action-table">
    <div class="widget-header"> <i class="icon-th-list"></i>
      <h3>{{ $heading_title_detail_contents }}</h3>
      <span class="pagination-totalItems">{{ $text_total }} {{ $boxes_count }}</span>
    </div>
    <!-- /widget-header -->
    <div class="widget-content">
    	<div class="table-responsive">
			<table class="table table-striped table-bordered">
				<thead>
					<tr>
						<th>{{ $col_id }}</th>
						<th>{{ $col_upc }}</th>
						<th>SHORT DESCRIPTION</th>
						<th>{{ $col_box_code }}</th>
						<th>{{ $col_moved_qty }}</th>
					</tr>
				</thead>
				@if( !CommonHelper::arrayHasValue($boxes) ) 
				<tr class="font-size-13">
					<td colspan="6" class="align-center">{{ $text_empty_results }}</td>
				</tr>
				@else
					@foreach( $boxes as $box )
					<tr class="font-size-13">
						<td>{{ $counter++ }}</td>
						<td>{{ $box->sku }}</td>
						<td>{{ $box->short_description }}</td>
						<td>{{ $box->box_code }}</td>
						<td>{{ $box->moved_qty }}</td>
						
					</tr>
					@endforeach
				@endif				
			</table>
		</div>
	</div>
	
	@if( CommonHelper::arrayHasValue($boxes) ) 
    <h6 class="paginate">
		<span>{{ $boxes->appends($arrFilters)->links() }}</span>
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
    	
    	url += '?box_code=' + encodeURIComponent('{{ $box_code }}');		
		url += '&sort=' + encodeURIComponent('{{ $sort }}');
		url += '&order=' + encodeURIComponent('{{ $order }}');

      	location = "{{ $url_export }}" + url;
    });

     // Submit Form
    $('#submitForm').click(function() {
    	$('#form-box-detail').submit();
    });

    $('#form-box-detail input').keydown(function(e) {
		if (e.keyCode == 13) {
			$('#form-box-detail').submit();
		}
	});

    // Clear Form
    $('#clearForm').click(function() {
    	$('#filter_sku').val('');
		
		$('#form-box-detail').submit();
    });
});	
</script>