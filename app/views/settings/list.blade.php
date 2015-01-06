@if( CommonHelper::arrayHasValue($error) )
    <div class="alert alert-error">
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

<div class="clear">
	<div class="div-paginate">
		@if( CommonHelper::arrayHasValue($settings) ) 
		    <h6 class="paginate">
				<span>{{ $settings->appends($arrFilters)->links() }}&nbsp;</span>
			</h6>
		@else
			&nbsp;
		@endif
	</div>
	<div class="div-buttons">
		@if ( CommonHelper::valueInArray('CanInsertSettings', $permissions) )
		<a class="btn btn-info" href="{{ $url_insert }}">{{ $button_insert }}</a>
		@endif
		@if ( CommonHelper::valueInArray('CanDeleteSettings', $permissions) )
		<a class="btn btn-danger" id="removeData">{{ $button_delete }}</a>
		@endif
	</div>
</div>

<div class="widget widget-table action-table">
    <div class="widget-header"> <i class="icon-th-list"></i>
    	<h3>{{ $heading_title }}</h3>
      	<span class="pagination-totalItems">{{ $text_total }} {{ $settings_count }}</span>
    </div>
    <!-- /widget-header -->
    
    <div class="widget-content">
    	<div class="table-responsive">
	    	{{ Form::open(array('url'=>'settings/delete', 'class'=>'form-signin', 'id'=>'form', 'role'=>'form', 'method' => 'post', 'style' => 'margin-bottom: 0px;')) }}
			<table class="table table-striped table-bordered">
				<thead>
					<tr>
						@if ( CommonHelper::valueInArray('CanDeleteSettings', $permissions) )
						<th style="width: 20px;"><input type="checkbox" id="main-selected" /></th>
						@endif
						<th>{{ $col_id }}</th>
						<th><a href="{{ $sort_brand }}" class="@if( $sort=='brand' ) {{ $order }} @endif">{{ $col_brand }}</a></th>
						<th>{{ $col_product_identifier }}</th>
						<th>{{ $col_product_action }}</th>
						@if ( CommonHelper::valueInArray('CanUpdateSettings', $permissions) )
						<th style="width: 80px;" class="align-center">{{ $col_action }}</th>
						@endif
					</tr>
				</thead>
				<tbody>
				@if( !CommonHelper::arrayHasValue($settings) ) 
					<tr class="font-size-13">
						<td colspan="6" style="text-align: center;">{{ $text_empty_results }}</td>
					</tr>
				@else
					@foreach( $settings as $setting )
					<tr class="font-size-13 tblrow" data-id="{{ $setting->id }}">
						@if ( CommonHelper::valueInArray('CanDeleteSettings', $permissions) )
						<td><input type="checkbox" class="checkbox item-selected" name="selected[]" id="selected-{{ $setting->id }}" value="{{ $setting->id }}" /></td>
						@endif
						<td>{{ $counter++ }}</td>
						<td>{{ $setting->brand_name }}</td>
						<td>{{ $product_identifier_options[$setting->product_identifier] }}</td>
						<td>{{ $product_action_options[$setting->product_action] }}</td>
						@if ( CommonHelper::valueInArray('CanUpdateSettings', $permissions) )
						<td class="align-center">
							<a href="{{ $url_update . '&id=' . $setting->id }}" class="icon-edit" title="{{ $link_edit }}"></a>
						</td>
						@endif
					</tr>
					@endforeach
				@endif
				</tbody>
			</table>
			{{ Form::hidden('sort', $sort) }}
	        {{ Form::hidden('order', $order) }}
	        {{ Form::hidden('page', $page) }}
			
			{{ Form::close() }}
		</div>
	</div>
	
	@if( CommonHelper::arrayHasValue($settings) ) 
    <h6 class="paginate">
		<span>{{ $settings->appends($arrFilters)->links() }}</span>
	</h6>
	@endif
</div>

<script type="text/javascript">
$(document).ready(function() {
    // Delete
    $('#removeData').click(function() {
    	var count = $("[name='selected[]']:checked").length;
		
		if (count>0) {
			var answer = confirm('{{ $text_confirm }}')
			
			if (answer) {
				$('#form').submit();
			}
		} else {
			alert('{{ $error_delete }}');
		}
    });
    
    // Select
    $('.tblrow').click(function() {
    	var rowid = $(this).data('id');
    	
    	if ($('#selected-' + rowid).is(':checked')) {
    		$('#selected-' + rowid).prop('checked', false);
    		$(this).children('td').removeClass('tblrow-active');
    	} else {
    		$('#selected-' + rowid).prop('checked', true);
    		$(this).children('td').addClass('tblrow-active');
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