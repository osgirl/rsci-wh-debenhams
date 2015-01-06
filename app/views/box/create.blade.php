@if( $errors->all() )
    <div class="alert alert-error">
    	<button class="close" data-dismiss="alert" type="button">&times;</button>
    	{{ HTML::ul($errors->all()) }}
    </div>
@endif

<div class="control-group">
	<a href="{{ $url_back }}" class="btn btn-info"> <i class="icon-chevron-left"></i> {{ $button_back }}</a>
</div>

<div class="widget">
    <div class="widget-header"> <i class="icon-user"></i>
    	<h3>{{ $heading_title_add }}</h3>
    </div>
    <!-- /widget-header -->
    
    <div class="widget-content">
    	{{ Form::open(array('url'=>$url_create, 'class'=>'form-horizontal',  'autocomplete'=>'off', 'id'=>'form-box-create', 'role'=>'form', 'method' => 'post')) }}
		<div class="span3">&nbsp;</div>
		<div class="span7">
			<fieldset>

				<div class="control-group">											
					<label class="control-label" for="role_id">{{ $entry_store }}</label>
					<div class="controls">
						{{ Form::select('store', $stores, Input::old('store')) }}
					</div> <!-- /controls -->				
				</div> <!-- /control-group -->


				<!-- <div class="control-group">											
					<label class="control-label" for="username">{{-- $entry_box_code --}}</label>
					<div class="controls">
						{{-- Form::text('box_code', Input::old('box_code'), array('maxlength'=>'9')) --}}
					</div> 
				</div> --> <!-- /control-group -->

				<div class="control-group">											
					<label class="control-label" for="username"> No. of boxes: </label>
					<div class="controls">
						{{ Form::text('box_range', NULL, array('maxlength'=>'3', 'id'=>'box_range')) }}
					</div> 
				</div>

				<div class="control-group">											
					<label class="control-label" for=""></label>
					<div class="controls">
						<a class="btn btn-info" id="submitForm">{{ $button_submit }}</a>
						<a class="btn" id="cancel-create-box" href="{{ $url_back }}">{{ $button_cancel }}</a>
					</div> <!-- /controls -->				
				</div> <!-- /control-group -->
			</fieldset>
        </div>
        <div class="span2">&nbsp;</div>
        {{ Form::hidden('filter_store', $filter_store) }}
        {{ Form::hidden('filter_box_code', $filter_box_code) }}
        {{ Form::hidden('sort', $sort) }}
        {{ Form::hidden('order', $order) }}
        {{ Form::hidden('page', $page) }}
        
		{{ Form::close() }}
	</div>
</div>

<script type="text/javascript">
$(document).ready(function() {

    // Create Box
    $('#submitForm').click(function() {
    	if($('#box_range').val() == '') {
    		alert('Please enter the number of boxes.');
    		return false;
    	}
    	else {

	    	if(($("#form-box-create select[name='store']").val() === '') || ($("#form-box-create input[name='box_range']").val() == '')) {
	    		alert('{{$error_required_fields}}');
	    	} else {
	    		var answer = confirm('{{ $text_confirm_create }}')
				if (answer) {
					$('#form-box-create').submit();
				} else {
					return false;
				}
	    	}

	    	return false;
    	}
    	
    });

    // Cancel
    $('#cancel-create-box').click(function() {
    	var answer = confirm('{{ $text_confirm_cancel }}')
		if (answer) {
			return true;
		} else {
			return false;
		}
    });

});

</script>