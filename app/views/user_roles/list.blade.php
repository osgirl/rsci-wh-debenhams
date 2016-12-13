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

<div class="control-group">
	<div class="controls">
		<div class="accordion" id="accordion2">
			<div class="accordion-group search-panel">
				{{ Form::open(array('url'=>'user_roles', 'class'=>'form-signin', 'id'=>'form-user-roles', 'role'=>'form', 'method' => 'get')) }}
				<div id="collapseOne" class="accordion-body collapse in search-panel-content">
					<div class="span4">
						<div>
							<span class="search-po-left-pane">{{ $label_filter_role_name }}</span>
							<span class="search-po-right-pane">
								{{ Form::text('filter_role_name', $filter_role_name, array('id'=>'filter_role_name', 'placeholder'=>'')) }}
							</span>
						</div>
					</div>
					
					<div class="span11 control-group collapse-border-top">
						<a class="btn   btn-success btn-darkblue" id="submitForm">{{ $button_search }}</a>
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
		@if( CommonHelper::arrayHasValue($user_roles) ) 
		    <h6 class="paginate">
				<span>{{ $user_roles->appends($arrFilters)->links() }}&nbsp;</span>
			</h6>
		@else
			&nbsp;
		@endif
	</div>
	<div class="div-buttons">
		@if ( CommonHelper::valueInArray('CanInsertUserRoles', $permissions) )
		<a class="btn btn-info" href="{{ $url_insert }}">{{ $button_insert }}</a>
		@endif
		@if ( CommonHelper::valueInArray('CanDeleteUserRoles', $permissions) )
		<a class="btn btn-danger" id="removeData">{{ $button_delete }}</a>
		@endif
	</div>
</div>

<div class="widget widget-table action-table">
    <div class="widget-header"> <i class="icon-th-list"></i>
    	<h3>{{ $heading_title }}</h3>
      	<span class="pagination-totalItems">{{ $text_total }} {{ $user_roles_count }}</span>
    </div>
    <!-- /widget-header -->
    
    <div class="widget-content">
    	<div class="table-responsive">
	    	{{ Form::open(array('url'=>'user_roles/delete', 'class'=>'form-signin', 'id'=>'form', 'role'=>'form', 'method' => 'post', 'style' => 'margin-bottom: 0px;')) }}
			<table class="table table-striped table-bordered">
				<thead>
					<tr>
						@if ( CommonHelper::valueInArray('CanDeleteUserRoles', $permissions) )
						<th style="width: 20px;" class="align-center"><input type="checkbox" id="main-selected" /></th>
						@endif
						<th>{{ $col_id }}</th>
						<th><a href="{{ $sort_role_name }}" class="@if( $sort=='role_name' ) {{ $order }} @endif">{{ $col_role_name }}</a></th>
						@if ( CommonHelper::valueInArray('CanUpdateUserRoles', $permissions) )
						<th style="width: 80px;" class="align-center">{{ $col_action }}</th>
						@endif
					</tr>
				</thead>
				<tbody>
				@if( !CommonHelper::arrayHasValue($user_roles) ) 
					<tr class="font-size-13">
						<td colspan="4" class="align-center">{{ $text_empty_results }}</td>
					</tr>
				@else
					@foreach( $user_roles as $user_role )
					<tr class="font-size-13 tblrow" data-id="{{ $user_role->id }}">
						@if ( CommonHelper::valueInArray('CanDeleteUserRoles', $permissions) )
						<td class="align-center"><input type="checkbox" class="checkbox item-selected" name="selected[]" id="selected-{{ $user_role->id }}" value="{{ $user_role->id }}" /></td>
						@endif
						<td>{{ $counter++ }}</td>
						<td>{{ $user_role->role_name }}</td>
						@if ( CommonHelper::valueInArray('CanUpdateUserRoles', $permissions) )
						<td class="align-center">
							<a href="{{ $url_update . '&id=' . $user_role->id }}" class="icon-edit" title="{{ $link_edit }}"></a>
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
	
	@if( CommonHelper::arrayHasValue($user_roles) ) 
    <h6 class="paginate">
		<span>{{ $user_roles->appends($arrFilters)->links() }}</span>
	</h6>
	@endif
</div>

<script type="text/javascript">
$(document).ready(function() {
    // Submit Form
    $('#submitForm').click(function() {
    	$('#form-user-roles').submit();
    });
    
    $('#form-user-roles input').keydown(function(e) {
		if (e.keyCode == 13) {
			$('#form-user-roles').submit();
		}
	});
    
    // Clear Form
    $('#clearForm').click(function() {
    	$('#filter_role_name').val('');
    	$('#form-user-roles').submit();
    });
    
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