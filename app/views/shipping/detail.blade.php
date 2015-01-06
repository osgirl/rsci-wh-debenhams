


<?php $counter=1;?>

@foreach ($stores as $key => $boxes) 
	<div class="widget widget-table action-table">
		<div class="widget-header"> </i>
	        <h3>Store {{$key}}</h3>
	        <div class="div-buttons button-inside-table">
				@if ( CommonHelper::valueInArray('CanExportShippingBoxManifest', $permissions) )
				<a href="{{$url_export . $key}}" class="btn btn-info">{{ $button_export }}</a> 
				@endif 
		    </div>
		    <!--TODO:: fix checking if shipped -->
		    <div class="div-buttons button-inside-table">
		    	@if(in_array($key, $shipped_stores))
					<a style="width: 100px;" isabled="disabled" class="btn btn-danger " data-id="{{ $key }}">{{ $button_shipped_letdown }}</a>
				@else
					<a style="width: 100px;" class="btn btn-success shipLetdown" data-id="{{ $key }}">{{ $button_ship_letdown }}</a>
				@endif
				{{ Form::open(array('url'=>$url_ship, 'id' => 'shipLetdown_' . $key, 'style' => 'margin: 0px;')) }}
					{{ Form::hidden('store_code', $key) }}
					{{ Form::hidden('doc_no', $move_doc_number) }}
					{{ Form::hidden('module', 'shipping_detail') }}
			  	{{ Form::close() }}
		    </div>
		</div>
	</div>
	
	@foreach ($boxes as $key => $details) 
		<div class="widget widget-table action-table">
		    <div class="widget-header"> <i class="icon-th-list"></i>
		      <h3>Box # {{$key}}</h3>
		    </div>

		    <div class="widget-content">
		    	<div class="table-responsive">
					<table class="table table-striped table-bordered">
						<thead>
							<tr>
								<th>{{ $col_dt_sku }}</th>
								<th>{{ $col_dt_packed_qty }}</th>
							</tr>
						</thead>
						@if( !CommonHelper::arrayHasValue($details) ) 
						<tr class="font-size-13">
							<td colspan="9" style="text-align: center;">{{ $text_empty_results }}</td>
						</tr>
						@else
							@foreach ($details as $key => $detail) 
							<tr>
								<td>{{ $detail->sku }}</td>
								<td>{{ $detail->packed_qty }}</td>
							</tr>
							@endforeach
						@endif
					</table>
				</div>
			</div>
		</div>
	@endforeach
@endforeach

<script type="text/javascript">
$(document).ready(function() {
    
     /**************Ship events***************/

    // Ship letdown
    $('.shipLetdown').click(function() {
    	var answer = confirm('{{ $text_warning }}');
			
		if (answer) {
			var letdown = $(this).data('id');
	    	$('#shipLetdown_' + letdown).submit();
		}
    });


});	
</script>
