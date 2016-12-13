<div class="control-group">
	<a href="{{$url_back}}" class="btn btn-info btn-darkblue"> <i class="icon-chevron-left"></i> {{$button_back}} </a>

</div>

<div class="control-group">
	<div class="controls">
		<div class="accordion" id="accordion2">
          <div class="accordion-group" style="background-color: #;">
             {{ Form::open(array('url'=>'reverse_logistic/detail', 'class'=>'form-signin', 'id'=>'form-picking-detail', 'role'=>'form', 'method' => 'get')) }}
             <div id="collapseOne" class="accordion-body collapse in" style="padding-top: 20px;">
	                <div class="span4">
			        	<div>
				        	<span class="search-po-left-pane">{{ $entry_sku }}</span>
				        	<span class="search-po-right-pane">
				        		{{ Form::text('filter_sku', $filter_sku, array('class'=>'login', 'placeholder'=>'', 'id'=>"filter_sku")) }}
				        	</span>
				        </div>
				        <div>
				        	<span class="search-po-left-pane"> SKU :</span>
				        	<span class="search-po-right-pane">
				        		{{ Form::text('filter_so', $filter_so, array('class'=>'login', 'placeholder'=>'', 'id'=>"filter_so")) }}
				        	</span>
				        </div>
			      	</div>


			      	<div class="span4">
			        	<div>
				        	<span class="search-po-left-pane"> MTS no.:</span>
				        	<span class="search-po-right-pane">
				        		{{ Form::text('picklist_doc', $picklist_doc, array('readonly' => 'readonly')) }}
				        	</span>
				        </div>
			        	<div>
				        	<span class="search-po-left-pane"> Piler Name:</span>
				        	<span class="search-po-right-pane">
				        		{{ Form::text('filter_stock_piler', $filter_stock_piler, array('readonly' => 'readonly')) }}
				        	</span>
				        </div>
			      	</div>

			      	<div class="span3">

			      	</div>



			      	<div class="span11 control-group collapse-border-top">
			      		<a class="btn btn-success btn-darkblue" id="submitForm">{{ $button_search }}</a>
		      			<a class="btn" id="clearForm">{{ $button_clear }}</a>
			      	</div>
            </div>
          
          </div>
      	</div>

	</div> <!-- /controls -->
</div> <!-- /control-group -->

<div class="clear">
	<div class="div-paginate">
		    <h6 class="paginate">
				<span>{{ $reversecountdetail->appends($arrFilters)->links() }}</span>
			</h6>
		
	</div>
</div>

<div class="widget widget-table action-table">
    <div class="widget-header"> <i class="icon-th-list"></i>
      <h3> Return to Warehouse Details</h3>
      <span class="pagination-totalItems">{{$text_total}} {{$picklist_detail_count}}</span>
    </div>
    <!-- /widget-header -->
    <div class="widget-content">
    	<div class="table-responsive">
			<table class="table table-bordered">
				<thead>
					<tr>
						<th>{{ $col_no }}</th>
					 
						<th> {{ $col_sku }} </th>
						<th> {{ $col_upc }} </th>
						<th>SHORT DESCRIPTION</th>
					 
						<th>{{ $col_qty_to_pick }}</th>
						<th>{{ $col_to_move }}</th>
						<th>Variance</th> 
					</tr>
				</thead>
				@if( !CommonHelper::arrayHasValue($reversecountdetail) )
				<tr class="font-size-13">
					<td colspan="10" class="align-center" style="background-color:#f6f6f6">{{ $text_empty_results }}</td>
				</tr>
				@else
					@foreach( $reversecountdetail as $pd )
					<tr class="font-size-13"
					@if ( ($pd['quantity_to_pick'] - $pd['moved_qty']) > 0 )
						style="background-color:#F29F9F"
					@endif>
						<td>{{$counter++}}</td>
				 
						<td>{{$pd['sku']}}</td>
						<td> {{$pd['upc']}}</td>
						<td>{{$pd['description']}}</td>
				 		<td>{{$pd['quantity_to_pick']}}</td>
						<td>{{$pd['moved_qty']}}</td>
						<td>{{$pd['moved_qty'] - $pd['quantity_to_pick']}}</td>

					</tr>
					@endforeach
				@endif
			</table>
		</div>
	</div>

	@if( CommonHelper::arrayHasValue($reversecountdetail) )
    <h6 class="paginate">
		<span>{{ $reversecountdetail->appends($arrFilters)->links() }}</span>
	</h6>
	@endif

</div>

<script type="text/javascript">
$(document).ready(function() {
	// Submit Form
    $('#submitForm').click(function() {
    	$('#form-picking-detail').submit();
    });

    $('#form-picking-detail').keydown(function(e) {
		if (e.keyCode == 13) {
			$('#form-picking-detail').submit();
		}
	});

    // Clear Form
    $('#clearForm').click(function() {
    	$('#filter_sku, #filter_so, #filter_from_slot').val(''); //,#filter_to_slot

		$('select').val('');
		$('#form-picking-detail').submit();
    });
});
</script>