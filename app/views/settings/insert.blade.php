@if( $errors->all() )
    <div class="alert alert-error">
    	<button class="close" data-dismiss="alert" type="button">&times;</button>
    	{{ HTML::ul($errors->all()) }}
    </div>
@endif

<div class="widget">
    <div class="widget-header"> <i class="icon-th-list"></i>
    	<h3>{{ $heading_title_insert }}</h3>
    </div>
    <!-- /widget-header -->
    
    <div class="widget-content">
    	{{ Form::open(array('url'=>'settings/insertData', 'class'=>'form-horizontal', 'id'=>'form-settings', 'role'=>'form', 'method' => 'post')) }}
		<div class="span2">&nbsp;</div>
		<div class="span9">
			<fieldset>
				<div class="control-group">											
					<label class="control-label" for="brand" style="width: 152px;">{{ $entry_brand }}</label>
					<div class="controls">
						{{-- Form::select('brand', $brand_options, Input::old('brand')) --}}
						{{ Form::select('brand', $brand_options, 'family-mart') }}
					</div> <!-- /controls -->				
				</div> <!-- /control-group -->
				
				<div class="control-group">											
					<label class="control-label" for="product_identifier" style="width: 152px;">{{ $entry_product_identifier }}</label>
					<div class="controls">
						@foreach ($product_identifier_options as $key => $val)
						
							@if (Input::old('product_identifier') == $key)
								{{ Form::radio('product_identifier', $key, true) }}&nbsp;&nbsp;{{ $val }}&nbsp;&nbsp;
							@else
								{{ Form::radio('product_identifier', $key, (($key === 'upc') ? true:false )) }}&nbsp;&nbsp;{{ $val }}&nbsp;&nbsp;
							@endif
						@endforeach
					</div> <!-- /controls -->				
				</div> <!-- /control-group -->
				
				<div class="control-group">											
					<label class="control-label" for="product_action" style="width: 152px;">{{ $entry_product_action }}</label>
					<div class="controls">
						@foreach ($product_action_options as $key => $val)
						<?php //print_r($key); ?>
							@if (Input::old('product_action') == $key)
								{{ Form::radio('product_action', $key, true) }}&nbsp;&nbsp;{{ $val }}<br />
							@else
								{{ Form::radio('product_action', $key, (($key === 'upc-detail-page') ? true:false )) }}&nbsp;&nbsp;{{ $val }}<br />
							@endif
						@endforeach
					</div> <!-- /controls -->				
				</div> <!-- /control-group -->
												
				<div class="control-group">											
					<label class="control-label" for=""></label>
					<div class="controls">
						<a class="btn btn-info" id="submitForm">{{ $button_submit }}</a>
						<a class="btn" href="{{ $url_cancel }}">{{ $button_cancel }}</a>
					</div> <!-- /controls -->				
				</div> <!-- /control-group -->
			</fieldset>
        </div>
        {{ Form::hidden('sort', $sort) }}
        {{ Form::hidden('order', $order) }}
        
		{{ Form::close() }}
	</div>
</div>

<script type="text/javascript">
$(document).ready(function() {
    // Submit Form
    $('#submitForm').click(function() {
    	$('#form-settings').submit();
    });
    
    $('#form-settings input').keydown(function(e) {
		if (e.keyCode == 13) {
			$('#form-settings').submit();
		}
	});
});	
</script>