@if( $errors->all() )
    <div class="alert alert-error">
    	<button class="close" data-dismiss="alert" type="button">&times;</button>
    	{{ HTML::ul($errors->all()) }}
    </div>
@endif

<div class="widget">
    <div class="widget-header"> <i class="icon-th-list"></i>
    	<h3>{{$button_assign_to_stock_piler}}</h3>
    </div>
    <!-- /widget-header -->

    <div class="widget-content">
   {{ Form::open(array('url'=>'reverse_logistic/assign_2_piler', 'id'=>"form-assign", 'class'=>'form-horizontal', 'style' => 'margin: 0px;', 'role'=>'form')) }}


		<!-- <div class="span3">&nbsp;</div> -->
		<div class="span3">&nbsp;</div>
		<div class="span7 add-piler-wrapper">
			<div class="control-group">
				<label class="control-label">MTS no. :</label>
				<div class="controls">
				{{ Form::text('so_no', $so_no, array('id' => 'so_no', 'readonly' => 'readonly')) }}
				</div> <!-- /controls -->
			</div> <!-- /control-group -->
		@if(count($params) > 1)
	
			<div class="control-group">
				<label class="control-label"> MTS no. :</label>
				<div class="controls">
			 {{ Form::select('stock_piler[]', $stock_piler_list, '', array('id' => 'stock_piler_select') ) }}
				<!-- <a class="add-piler-btn"><i class="icon-plus-sign" style="font-size: 1.5em;"></i></a> -->
				</div> <!-- /controls -->
			</div> <!-- /control-group -->
	  		@else
                  <?php
                   $pilers = explode(',',$info[0]['assigned_to_user_id']);
                   ?>
             @foreach($pilers as $key => $piler)
	
			<div class="control-group">
				<label class="control-label"> {{ $entry_stock_piler }}</label>
				<div class="controls">
				 {{ Form::select('stock_piler[]', $stock_piler_list, $piler, array('id' => 'stock_piler_select') ) }}
               @if($key == 0)      
								<!-- <a class="add-piler-btn"><i class="icon-plus-sign" style="font-size: 1.5em;"></i></a> -->
			  @else
					<a class="remove-piler-btn" style="margin-left: 3px;"><i class="icon-minus-sign" style="font-size: 1.5em; color:#CB1212;"></i></a>
			 @endif
				</div> <!-- /controls -->
			</div> <!-- /control-group -->
		    @endforeach
            @endif
        </div>
        <!-- <div class="span10">&nbsp;</div> -->
        <div class="span3">&nbsp;</div>
        <div class="span7">
        	<div class="control-group">
				<label class="control-label" for=""></label>
				<div class="controls">
					<a class="btn btn-info" id="btn-assign"> Assign </a>
					<a class="btn" href="{{$url_back}}">Cancel</a>
				</div> <!-- /controls -->
			</div> <!-- /control-group -->
		</div>
       
	</div>
</div>
<script type="text/javascript">
$(document).ready(function() {
	function removeStockPiler() {
		$('.remove-piler-btn').click(function(e) {
			console.log('ee');
			$(this).parent().parent().remove();
		});
	}

	removeStockPiler();
    // Submit Form
    $('.add-piler-btn').unbind('click').click(function(e) {
		// $('.piler-block').clone().appendTo(".add-piler-wrapper");
		var html = '';
		html += '<div class="control-group piler-block">'
					+ '<label class="control-label" for="stock_piler">{{ $entry_stock_piler }}</label>'
						+ '<div class="controls">'
							+ '{{ Form::select('stock_piler[]', $stock_piler_list, '') }}'
							+ '<a class="remove-piler-btn" style="margin-left: 3px;"><i class="icon-minus-sign" style="font-size: 1.5em; color:#CB1212;"></i></a>'
						+ '</div>'
				+ '</div>';
		$(".add-piler-wrapper").append(html);

		removeStockPiler()

	});

	// Submit Assign
    $('#btn-assign').click(function() {
    	// stockpiler = $('select[name=\'stock_piler\']').val();
    	var stockpiler = $('#stock_piler_select').val();
    	console.log(stockpiler);

    	if (stockpiler == '') {
    		alert('{{ $error_assign }}');
    		return false;
    	} else {
    		$('#form-assign').submit();
    	}
    });
});
</script>