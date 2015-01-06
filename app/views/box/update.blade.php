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
    	<h3>{{ $heading_title_update }}</h3>
    </div>
    <!-- /widget-header -->
    
    <div class="widget-content">
    	{{ Form::open(array('url'=>$url_update, 'class'=>'form-horizontal',  'autocomplete'=>'off', 'id'=>'form-box-update', 'role'=>'form', 'method' => 'post')) }}
		<div class="span3">&nbsp;</div>
		<div class="span7">
			<fieldset>

				<div class="control-group">											
					<label class="control-label" for="role_id">{{ $entry_store }}</label>
					<div class="controls">
						{{ Form::select('store', $stores, $box_details->store_code) }}
					</div> <!-- /controls -->				
				</div> <!-- /control-group -->


				<div class="control-group">											
					<label class="control-label" for="username">{{ $entry_box_code }}</label>
					<div class="controls">
						{{ Form::text('box_code', $box_details->box_code, array('disabled'=>'true')) }}
					</div> <!-- /controls -->				
				</div> <!-- /control-group -->

				<div class="control-group">											
					<label class="control-label" for=""></label>
					<div class="controls">
						<a class="btn btn-info" id="submitForm">{{ $button_submit }}</a>
						<a class="btn" id="cancel-update-box" href="{{ $url_back }}">{{ $button_cancel }}</a>
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
        {{ Form::hidden('box_code', $box_details->box_code) }}
        {{ Form::hidden('box_id', $box_details->id) }}
        
		{{ Form::close() }}
	</div>
</div>

<script type="text/javascript">
$(document).ready(function() {

    // Update Box
    $('#submitForm').click(function() {

    	if(($("#form-box-update select[name='store']").val() === '') || ($("#form-box-update input[name='box_code']").val() == '')) {
    		alert('{{$error_required_fields}}');
    	} else {
    		var answer = confirm('{{ $text_confirm_update }}')
			if (answer) {
				$('#form-box-update').submit();
			} else {
				return false;
			}
    	}

    	return false;
    	
    });

    // Cancel
    $('#cancel-update-box').click(function() {
    	var answer = confirm('{{ $text_confirm_cancel }}')
		if (answer) {
			return true;
		} else {
			return false;
		}
    });

});

</script>