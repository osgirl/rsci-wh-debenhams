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
            {{ Form::open(array('url'=>'shipping', 'class'=>'form-signin', 'id'=>'form-let-down', 'role'=>'form', 'method' => 'get')) }}
            <div id="collapseOne" class="accordion-body collapse in" style="padding-top: 20px;">
	                <div class="span4">
			        	<div>
				        	<span class="search-po-left-pane">{{ $label_type }}</span>
				        	<span class="search-po-right-pane">
				        		{{ Form::text('filter_type', $filter_type, array('class'=>'login', 'placeholder'=>'', 'id'=>"filter_type")) }}
				        	</span>
				        </div>
			      	</div>
			      	<div class="span5">
			      		<div>
				        	<span class="search-po-left-pane">{{ $label_doc_no }}</span>
				        	<span class="search-po-right-pane">
				        		{{ Form::text('filter_doc_no', $filter_doc_no, array('class'=>'login', 'placeholder'=>'', 'id'=>"filter_doc_no")) }}
				        	</span>
				        </div>
				    </div>
			      	<div class="span11 control-group collapse-border-top">
			      		<a class="btn btn-success" id="submitForm">{{ $button_search }}</a>
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
		@if(CommonHelper::arrayHasValue($letdowns) ) 
		    <h6 class="paginate">
				<span>{{ $letdowns->appends($arrFilters)->links() }}&nbsp;</span>
			</h6>
		@else
			&nbsp;
		@endif
	</div>

	<div class="div-buttons">
		@if ( CommonHelper::valueInArray('CanSyncBoxManifest', $permissions) )
		<a class="btn btn-info">{{ $button_jda }}</a> 
		@endif
	</div>
</div>

<div class="widget widget-table action-table">
    <div class="widget-header"> <i class="icon-th-list"></i>
      <h3>{{ $heading_title }}</h3>
      <span class="pagination-totalItems">{{ $text_total }} {{ $letdowns_count }}</span>
    </div>
    <!-- /widget-header -->
    <div class="widget-content">
    	<div class="table-responsive">
			<table class="table table-striped table-bordered">
				<thead>
					<tr>
						<th>{{ $col_id }}</th>
						<th>{{ $col_type }}</th>
						
						<th><a href="{{ $sort_doc_no }}" class="@if( $sort=='doc_no' ) {{ $order }} @endif">{{ $col_doc_number }}</a></th>
					</tr>
				</thead>
				@if( !CommonHelper::arrayHasValue($letdowns) ) 
				<tr class="font-size-13">
					<td colspan="9" style="text-align: center;">{{ $text_empty_results }}</td>
				</tr>
				@else
					@foreach( $letdowns as $ld )
					<tr class="font-size-13 tblrow" data-id="{{ $ld->id }}">
						<td>{{ $ld->id }}</td>
						<td>{{ $ld->type }}</td>
						<td></td>
						<td></td>
						<td></td>
						<td><a href="{{ $url_detail . '?doc_no=' . $ld->move_doc_number}}">{{ $ld->move_doc_number }}</a></td>
					</tr>
					</tr>
					@endforeach
				@endif
			</table>
		</div>
	</div>
</div>


<script type="text/javascript">
$(document).ready(function() {

    // Submit Form
    $('#submitForm').click(function() {
    	//TODO
    	$('#form-let-down').submit();
    });


    /**************Search events***************/

    //Search form on enter sumbmit filters
    $('#form-let-down input').keydown(function(e) {
		if (e.keyCode == 13) {
			$('#form-let-down').submit();
		}
	});

    // Clear Search Form
    $('#clearForm').click(function() {
    	$('#filter_doc_no, #filter_type').val('');
		
		$('#form-let-down').submit();
    });



});	
</script>