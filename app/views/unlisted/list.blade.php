<div class="control-group">
	<div class="controls">
		<div class="accordion" id="accordion2">
			<div class="accordion-group search-panel">
				{{ Form::open(array('url'=>'unlisted', 'class'=>'form-signin', 'id'=>'form-unlisted', 'role'=>'form', 'method' => 'get')) }}
				<div id="collapseOne" class="accordion-body collapse in search-panel-content">
					<div class="span6">
						<div>
							<span class="search-po-left-pane">{{ $label_filter_reference_no }}</span>
							<span class="search-po-right-pane">
								{{ Form::text('filter_reference_no', $filter_reference_no, array('id'=>'filter_reference_no', 'placeholder'=>'')) }}
							</span>
						</div>
						<div>
							<span class="search-po-left-pane">{{ $label_filter_upc }}</span>
							<span class="search-po-right-pane">
								{{ Form::text('filter_sku', $filter_sku, array('id'=>'filter_sku', 'placeholder'=>'')) }}
							</span>
						</div>
					
					</div>
						<div>
							<span class="search-po-left-pane">{{ $label_filter_shipment_reference_no }} :</span>
							<span class="search-po-left-pane">
								{{ Form::text('filter_shipment_reference_no', $filter_shipment_reference_no, array('id'=>'filter_shipment_reference_no', 'placeholder'=>'')) }}
							</span>
						</div>
					<div class="span11 control-group collapse-border-top" style="margin-top: 6px;">
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
	 
		    <h6 class="paginate">
				<span> &nbsp;</span>
			</h6>
	 
			&nbsp;
		 
	</div>
	 
</div>

<div class="widget widget-table action-table">
	<div class="widget-header"> <i class="icon-th-list"></i>
    	<h3> Unlisted list</h3>
     	<span class="pagination-totalItems"> </span>
    </div>
    <!-- /widget-header -->

    <div class="widget-content">
    	<div class="table-responsive">
			<table class="table table-striped table-bordered" style="table-layout: fixed;">
				<thead>
					<tr>
							<th style="width: 30px; max-width: 30px;">{{ $col_id }}</th>
						<th style="width: 100px; max-width: 100px;"><a href=" "> {{ $col_upc }} </a></th>
						<th style="width: 80px; max-width: 80px;"><a href=" ">{{ $col_reference }}</a></th>
						<th style="width: 100px; max-width: 100px;">{{ $col_shipment_reference }}</th>
				 
						<th style="width: 80px; max-width: 100px;">{{ $col_quantity_received }}</th>
						<th style="width: 110px; max-width: 110px;">{{ $col_description }}</th>
						<th style="width: 100px; max-width: 100px;">{{ $col_style_no }}</th>
				 
						<th style="width: 90px; max-width: 90px;">{{ $col_division }}</th>
						<th style="width: 100px; max-width: 100px;">{{ $col_scanned_by }}</th>
					</tr>
				</thead>
				<tbody>
			 
				@if( !CommonHelper::arrayHasValue($unlisted) )
					<tr class="font-size-13">
						<td colspan="11" class="align-center">{{ $text_empty_results }}</td>
					</tr>
				@else
				@foreach($unlisted as $unlist)
					<tr class="font-size-13">
						<td> {{$counter++}}</td>
						<td style="word-wrap:break-word"> {{$unlist->upc}}</td>
						<td> {{$unlist->purchase_order_no}}</td>
						<td style="word-wrap:break-word">{{$unlist->shipment_reference_no}} </td>
					 
						<td> {{$unlist->quantity_delivered}}</td>
						<td style="word-wrap:break-word">{{$unlist->description}} </td>
						<td style="word-wrap:break-word">{{$unlist->short_description}} </td>
			 
						<td>{{$unlist->division}} </td>
						<td style="word-wrap:break-word"> {{$unlist->fullname}} </td>
					</tr>
					@endforeach
			 	@endif
				</tbody>
			</table>
		</div>
	</div>

 
    <h6 class="paginate">
		<span> </span>
	</h6>
 
</div>
 