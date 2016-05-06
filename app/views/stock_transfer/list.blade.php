<!--@if( CommonHelper::arrayHasValue($error) )-->
    <div class="alert alert-danger">
    	<button class="close" data-dismiss="alert" type="button">&times;</button>
    <!--	{{ $error }}-->
    </div>
<!--@endif-->

<!--@if( CommonHelper::arrayHasValue($success) )-->
    <div class="alert alert-success">
    	<button class="close" data-dismiss="alert" type="button">&times;</button>
    	<!--{{ $success }}-->
    </div>
<!--@endif-->

<div class="control-group">
	<div class="controls">
		<div class="accordion" id="accordion2">
          <div class="accordion-group" style="background-color: #FFFFFF;">
        <!--    {{ Form::open(array('url'=>'store_return', 'class'=>'form-signin', 'id'=>'form-store-order', 'role'=>'form', 'method' => 'get')) }}-->
            <div id="collapseOne" class="accordion-body collapse in" style="padding-top: 20px;">
	                <div class="span4">
			        	<div>
				        	<span class="search-po-left-pane"></span>
				        	<span class="search-po-right-pane">
				        	<!--	{{ Form::text('filter_so_no', $filter_so_no, array('class'=>'login', 'placeholder'=>'', 'id'=>"filter_so_no")) }}-->
				        	</span>
				        </div>
				        <div>
				        	<span class="search-po-left-pane"></span>
				        	<span class="search-po-right-pane">
				        		<!--{{ Form::select('filter_status', array('' => $text_select) + $so_status_type, $filter_status, array('class'=>'select-width', 'id'=>"filter_status")) }}-->
				        	</span>
				        </div>
			      	</div>
			      	<div class="span4">
			      		<div>
				        	<span class="search-po-left-pane"></span>
				        	<span class="search-po-right-pane">
				        		<!--{{ Form::select('filter_store', array('' => $text_select) + $store_list, $filter_store, array('class'=>'select-width', 'id'=>"filter_store")) }}-->
				        	</span>
				        </div>
				     </div>
			      	<div class="span3">
			      		<div>
				        	<span class="search-po-left-pane"><!--{{ $label_order_date }}--></span>
				        	<div class="search-po-right-pane input-append date">
								<!--{{ Form::text('filter_created_at', $filter_created_at, array('class'=>'span2', 'id'=>"filter_created_at", 'readonly'=>'readonly')) }}-->
								<span class="add-on"><i class="icon-th"></i></span>
				        	</div>
				        </div>
			      	</div>
			      	<div class="span11 control-group collapse-border-top">
			      		<a class="btn btn-success btn-darkblue" id="submitForm"></a>
		      			<a class="btn" id="clearForm"></a>
			      	</div>
            </div>
        <!--    {{ Form::hidden('sort', $sort) }}
		    {{ Form::hidden('order', $order) }}

            {{ Form::close() }}-->
          </div>
      	</div>

	</div> <!-- /controls -->
</div> <!-- /control-group -->


<div class="clear">
	<div class="div-paginate">
		<!--@if(CommonHelper::arrayHasValue($store_return) )-->
		    <h6 class="paginate">
				<span> <!--{{ $store_return->appends($arrFilters)->links() }}&nbsp;--></span>
			</h6>
		<!--@else-->
			&nbsp;
	<!--	@endif -->
	</div>
	<div class="div-buttons">
		<!--@if ( CommonHelper::valueInArray('CanAssignStoreReturn', $permissions))-->
			<a><!-- role="button" class="btn btn-info btn-darkblue assignStoreOrder" title="{{ $button_assign_to_stock_piler }}" data-toggle="modal">{{ $button_assign_to_stock_piler }}--></a>
	<!--	@endif-->
	<!--	@if ( CommonHelper::valueInArray('CanExportStoreReturn', $permissions) )
			<a class="btn btn-info btn-darkblue" id="exportList">{{ $button_export }}</a>
		@endif-->	
	</div>
</div>

<div class="widget widget-table action-table">
    <div class="widget-header"> <i class="icon-th-list"></i>
      <h3> </h3>
      <span class="pagination-totalItems"><!--{{ $text_total }} {{ $store_return_count }}--></span>
    </div>
    <!-- /widget-header -->
    <div class="widget-content">
    	<div class="table-responsive">
			<table class="table table-bordered">
				<thead>
					<tr>
					<!--	@if ( CommonHelper::valueInArray('CanAssignStoreReturn', $permissions) )-->
							<th> <!--style="width: 20px;" class="align-center"><input type="checkbox" id="main-selected" />--></th>
					<!--	@endif-->
						<th></th>
						<th><!--<a href="{{ $sort_so_no }}" class="@if( $sort=='so_no' ) {{ $order }} @endif">{{ $col_so_no }}</a>--></th>
						<th><!--<a href="{{ $sort_store }}" class="@if( $sort=='store' ) {{ $order }} @endif">{{ $col_store }}</a>--></th>
						<th></th>
						<th><!--<a href="{{ $sort_created_at }}" class="@if( $sort=='created_at' ) {{ $order }} @endif">{{ $col_order_date }}</a>--></th>
						<th><!--{{ $col_receiving_stock_piler }}--></th>
						<th></th>
						<th></th>
						<th></th>
					</tr>
				</thead>
			<!--	@if( !CommonHelper::arrayHasValue($store_return) )-->
				<tr class="font-size-13">
					<td colspan="10" style="text-align: center;"></td>
				</tr>
				<!--@else-->
					<!--@foreach( $store_return as $so )-->
					<tr><!-- class="font-size-13 tblrow" data-id="{{ $so['so_no'] }}"
						@if ( array_key_exists('discrepancy',$so) )
							style="background-color:#F29F9F"
						@endif
					
						@if ( CommonHelper::valueInArray('CanAssignStoreReturn', $permissions) )-->
						<td class="align-center">
						<!--	@if($so['data_display'] == 'Open' || $so['data_display'] == 'Assigned')
							<input type="checkbox" class="checkbox item-selected" name="selected[]" id="selected-{{ $so['so_no'] }}" value="{{ $so['so_no'] }}" />-->
						<!--	@endif-->
						</td>
						<!--@endif-->	
						<td></td>
						<td><!--<a href="{{ $url_detail . '&id='.$so['id'].'&so_no=' . $so['so_no'] }}">{{ $so['so_no'] }}</a>--></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td class="align-center">
						<!--	@if ( CommonHelper::valueInArray('CanCloseStoreReturn', $permissions) )

							@if($so['data_display'] === 'Posted')
								<a style="width: 70px;" disabled="disabled" class="btn btn-danger">{{ $text_posted }}</a>
								  && ($so['quantity_to_pick'] != $so['moved_qty']) 
							@elseif ( $so['data_display'] === 'Done' )-
								<a style="width: 70px;" class="btn btn-success closeStoreReturn" data-id="{{ $so['so_no'] }}">{{ $button_close_store_return }}</a>
							@else
								<a style="width: 70px;" disabled="disabled" class="btn"><{{ $button_close_store_return }}</a>
						@endif

							{{ Form::open(array('url'=>'store_return/close', 'id' => 'closeSO_' . $so['so_no'], 'style' => 'margin: 0px;')) }}
								{{ Form::hidden('so_no', $so['so_no']) }}
					            {{ Form::hidden('filter_so_no', $filter_so_no) }}
								{{ Form::hidden('filter_store', $filter_store) }}
								{{ Form::hidden('filter_created_at', $filter_created_at) }}
								{{ Form::hidden('filter_status', $filter_status) }}
							    {{ Form::hidden('page', $page) }}
					            {{ Form::hidden('sort', $sort) }}
							    {{ Form::hidden('order', $order) }}
								{{ Form::hidden('module', 'store_return') }}
					  		{{ Form::close() }}
					  	@endif-->
						</td>
					</tr>
					<!--@endforeach-->
				<!--@endif-->
			</table>
		</div>
	</div>

	<!--@if( CommonHelper::arrayHasValue($store_return) )-->
    <h6 class="paginate">
		<span><!-- {{ $store_return->appends($arrFilters)->links() }}--></span>
	</h6>
<!--	@endif-->

</div>
