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
				{{ Form::open(array('url'=>'user', 'class'=>'form-signin', 'id'=>'form-users', 'role'=>'form', 'method' => 'get')) }}
				<div id="collapseOne" class="accordion-body collapse in search-panel-content">
					<div class="span3">
						<div>
							<span class="search-po-left-pane">{{ $label_filter_username }}</span>
							<span class="search-po-right-pane">
								{{ Form::text('filter_username', $filter_username, array('id'=>'filter_username', 'placeholder'=>'')) }}
							</span>
						</div>
					</div>

					<div class="span3">
						<!-- <div>
							<span class="search-po-left-pane">{{ $label_filter_barcode }}</span>
							<span class="search-po-right-pane">
								{{ Form::text('filter_barcode', $filter_barcode, array('id'=>'filter_barcode', 'placeholder'=>'')) }}
							</span>
						</div> -->
					</div>

					<div class="span3">
						<div>
							<span class="search-po-left-pane">{{ $label_filter_user_role }}</span>
							<span class="search-po-right-pane">
								{{ Form::select('filter_user_role', $filter_user_role_options, $filter_user_role, array('id' => 'filter_user_role', 'class' => 'select-width')) }}
							</span>
						</div>
					</div>

					<div class="span11 control-group collapse-border-top">
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
		@if( CommonHelper::arrayHasValue($users) )
	    	<h6 class="paginate">
				<span>{{ $users->appends($arrFilters)->links() }}&nbsp;</span>
			</h6>
		@else
			&nbsp;
		@endif
	</div>
	<div class="div-buttons">
		@if ( CommonHelper::valueInArray('CanInsertUsers', $permissions) )
		<a class="btn btn-info  btn-darkblue" href="{{ $url_insert }}">{{ $button_insert }}</a>
		@endif
	<!-- 	@if ( CommonHelper::valueInArray('CanExportUsers', $permissions) )
		<a class="btn btn-info  btn-darkblue" id="exportList">{{ $button_export }}</a>
		@endif -->
		@if ( CommonHelper::valueInArray('CanDeleteUsers', $permissions) )
		<a class="btn btn-danger" id="removeData">{{ $button_delete }}</a>
		@endif
	</div>
</div>

<div class="widget widget-table action-table">
    <div class="widget-header"> <i class="icon-th-list"></i>
    	<h3>{{ $heading_title }}</h3>
      	<span class="pagination-totalItems">{{ $text_total }} {{ $users_count }}</span>
    </div>
    <!-- /widget-header -->

    <div class="widget-content">
    	<div class="table-responsive">
	    	{{ Form::open(array('url'=>'user/delete', 'class'=>'form-signin', 'id'=>'form', 'role'=>'form', 'method' => 'post', 'style' => 'margin-bottom: 0px;')) }}
			<table class="table table-striped table-bordered">
				<thead>
					<tr>
						@if ( CommonHelper::valueInArray('CanDeleteUsers', $permissions) )
						<th style="width: 20px;" class="align-center"><input type="checkbox" id="main-selected" /></th>
						@endif
						<th>{{ $col_id }}</th>
						<th><a href="{{ $sort_username }}" class="@if( $sort=='username' ) {{ $order }} @endif">{{ $col_username }}</a></th>
					 
						<th><a href="{{ $sort_name }}" class="@if( $sort=='name' ) {{ $order }} @endif">{{ $col_name }}</a></th>
						<th><a href="{{ $sort_role }}" class="@if( $sort=='role' ) {{ $order }} @endif">{{ $col_user_role }}</a></th>
					 
						<th><a href="{{ $sort_date }}" class="@if( $sort=='date' ) {{ $order }} @endif">{{ $col_date }}</a></th>
						<th>Store Name </th>
						@if ( CommonHelper::valueInArray('CanUpdateUsers', $permissions) || CommonHelper::valueInArray('CanChangePasswordUsers', $permissions) )
						<th style="width: 80px;" class="align-center">{{ $col_action }}</th>
						@endif
					</tr>
				</thead>
				<tbody>
				@if( !CommonHelper::arrayHasValue($users) )
					<tr class="font-size-13">
						<td colspan="9" class="align-center">{{ $text_empty_results }}</td>
					</tr>
				@else
					@foreach( $users as $user )
					<tr class="font-size-13 tblrow" data-id="{{ $user->id }}">
						@if ( CommonHelper::valueInArray('CanDeleteUsers', $permissions))
						<td class="align-center">
							@if ( Auth::user()->id != $user->id )
							<input type="checkbox" class="checkbox item-selected" name="selected[]" id="selected-{{ $user->id }}" value="{{ $user->id }}" />
							@endif
						</td>
						@endif
						<td>{{ $counter++ }}</td>
						<td>{{ $user->username }}</td>
					 
						<td>{{ $user->name }}</td>
						<td>{{ $user->role_name }}</td>
					 
						<td>{{ date('M d, Y', strtotime($user->created_at)) }}</td>
						<td>{{$user->store_name}}</td>
						@if ( CommonHelper::valueInArray('CanUpdateUsers', $permissions) || CommonHelper::valueInArray('CanChangePasswordUsers', $permissions) )
						<td class="align-center">
							@if ( CommonHelper::valueInArray('CanUpdateUsers', $permissions))
							<a href="{{ $url_update . '&id=' . $user->id }}" class="icon-edit" title="{{ $link_edit }}"></a>&nbsp;
							@endif
							@if ( CommonHelper::valueInArray('CanChangePasswordUsers', $permissions) || Auth::user()->id == $user->id)
							<a href="{{ $url_password . '&id=' . $user->id }}" class="icon-key" title="{{ $link_change_password }}"></a>
							@endif
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

	@if( CommonHelper::arrayHasValue($users) )
    <h6 class="paginate">
		<span>{{ $users->appends($arrFilters)->links() }}</span>
	</h6>
	@endif
</div>

<script type="text/javascript">
$(document).ready(function() {
    // Submit Form
    $('#submitForm').click(function() {
    	$('#form-users').submit();
    });

    $('#form-users input').keydown(function(e) {
		if (e.keyCode == 13) {
			$('#form-users').submit();
		}
	});

    // Clear Form
    $('#clearForm').click(function() {
    	$('#filter_username').val('');
    	$('#filter_barcode').val('');
    	$('#filter_user_role').val('');
    	$('#form-users').submit();
    });

    // Export List
    $('#exportList').click(function() {
    	url = '';

		var filter_username = $('#filter_username').val();
		url += '?filter_username=' + encodeURIComponent(filter_username);

		var filter_barcode = $('#filter_barcode').val();
		url += '&filter_barcode=' + encodeURIComponent(filter_barcode);

		var filter_user_role = $('select[name=\'filter_user_role\']').val();
		url += '&filter_user_role=' + encodeURIComponent(filter_user_role);

		url += '&sort=' + encodeURIComponent('{{ $sort }}');
		url += '&order=' + encodeURIComponent('{{ $order }}');

      	location = "{{ $url_export }}" + url;
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