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
<h2><span class="label label-important" style="font-size: 15px; font-weight: normal;">Notes: Please use filter first before using the Report button.</span></h2>
	<div class="controls">
		<div class="accordion" id="accordion2">
          <div class="accordion-group" style="background-color: #FFFFFF;">
            {{ Form::open(array('url'=>'purchase_order/discrepansy', 'class'=>'form-signin', 'id'=>'form-purchase-order', 'role'=>'form', 'method' => 'get')) }}
            <div id="collapseOne" class="accordion-body collapse in" style="padding-top: 20px;">
	                <div class="span4">
			        	<div>
				        	<span class="search-po-left-pane">{{ $label_purchase_no }}</span>
				        	<span class="search-po-right-pane">
				        		{{ Form::text('filter_po_no', $filter_po_no, array('class'=>'login', 'placeholder'=>'', 'id'=>"filter_po_no")) }}
				        	</span>
				        </div>
	 
			      	</div>
			      	<div class="span4">
			      		<!-- <div>
				        	<span class="search-po-left-pane">{{ $label_entry_date }}</span>
				        	<div class="search-po-right-pane input-append date">
								{{ Form::text('filter_entry_date', $filter_entry_date, array('class'=>'span2', 'id'=>"filter_entry_date", 'readonly'=>'readonly')) }}
								<span class="add-on"><i class="icon-th"></i></span>
				        	</div>
				        </div> -->
	 

			      	</div>
			    
			      	<div class="span3">
			    
			      	</div>
			      	<div class="span11 control-group collapse-border-top">
			      		<a class="btn btn-success btn-darkblue" id="submitForm">{{ $button_search }}</a>
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
		@if(CommonHelper::arrayHasValue($po_discrepancy) )
		    <h6 class="paginate">
				<span>{{ $po_discrepancy->appends($arrFilters)->links() }}&nbsp;</span>
			</h6>
		@else
			&nbsp;
		@endif
	</div>
 
				<th>
					
			      	</div>
			      	<div class="btn-group div-buttons">
				        <button type="button" class="btn btn-info btn-darkblue dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Report <span class="caret"></span>
				        </button>
				        <ul class="dropdown-menu">
				          <li><a id="exportlistpdf">Export pdf</a></li>
					          <li><a id="exportList">Export excel</a></li>
				        </ul>
			      	</div>
			     </th>
			</tr>
		</table>
	</div>
	
</div>

<div class="widget widget-table action-table">
    <div class="widget-header"> <i class="icon-th-list"></i>
      <h3>{{ $heading_title_discrp }}</h3>
      <span class="pagination-totalItems">{{ $text_total }} {{ $purchase_orders_count }}</span>
    </div>
    <!-- /widget-header -->
    <div class="widget-content">
    	<div class="table-responsive">
			<table class="table table-striped table-bordered">
				<thead>
				<tr> 
					<th rowspan="2">{{ $col_id }}</th>
					<th rowspan="2">Dept</th>
					<th rowspan="2">Style No. </th>
					<th rowspan="2">SKU</th>
						<th rowspan="2">UPC</th>
					<th  style="text-align: center" colspan="2">Quantity</th>
					<th style="text-align: center" rowspan="2">Discrepancy <br>(Short/Over)</th>
					<th rowspan="2">Invoice No</th>
					<th rowspan="2">Remarks</th>
				</tr>
					<tr>
					 
						<th>Advised Per RA </th>
						<th> Actual Receipt</th>
						 
			 
						 
					</tr>
				</thead>
				@if( !CommonHelper::arrayHasValue($po_discrepancy) )
				<tr class="font-size-13">
					<td colspan="13" style="text-align: center;">{{ $text_empty_results }}</td>
				</tr>
				@else
					@foreach( $po_discrepancy as $po )
					<tr  style="text-align: center" class="font-size-13 tblrow" data-id="{{ $po->purchase_order_no }}">
				 
						<td style="text-align: center">{{ $counter++ }}</td>
						<td style="text-align: center">{{$po->dept_number}} </td>
						<td style="text-align: center">{{$po->short_description}}</td>
						<td style="text-align: center">{{$po->sku}}</td>
						<td style="text-align: center">{{$po->upc}}</td>
						<td style="text-align: center"> {{$po->quantity_ordered}}</td> 
						<td style="text-align: center">{{$po->quantity_delivered}}</td> 
						<td style="text-align: center">{{ $po->quantity_delivered- $po->quantity_ordered }}</td>
						<td style="text-align: center"></td> 
						<td style="text-align: center" ></td> 
					</tr>
					@endforeach
				@endif
			</table>
		</div>
	</div>

	@if( CommonHelper::arrayHasValue($po_discrepancy) )
    <h6 class="paginate">
		<span>{{ $po_discrepancy->appends($arrFilters)->links() }}</span>
	</h6>
	@endif

	<!-- Button to trigger modal -->
	<!-- Modal -->
 
 
 
	<!-- /widget-content -->
 

<!--modal for close po-->
<div id="closePoModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="closePoModalLabel" aria-hidden="true">
	 <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title">{{$entry_invoice}}</h4>
      </div>
      <div class="modal-body">
        {{ Form::open(array('role'=> 'form', "class"=> "form-horizontal"))}}
        <div class="form-group">
        	{{ Form::label('invoice_no',$label_invoice_number, array("style" => "margin-right:10px", "class" => "col-sm-2 control-label"))}}
        	<div class="col-sm-10">
        		{{ Form::text('invoice_no', '', array('required', 'id'=>'invoiceNoInput','class'=> "form-control"))}}
        	</div>
		</div>
		</br>
		<div class="form-group">
        	{{ Form::label('invoice_amount',$label_invoice_amount, array("style" => "margin-right:10px","class" => "col-sm-2 control-label"))}}
        	<div class="col-sm-10">
        		{{ Form::text('invoice_amount', '', array('required', 'id'=>'invoiceAmountInput','class'=> "form-control"))}}
        	</div>
		</div>
        {{ Form::close()}}
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" id="closePOModalButton">Close PO</button>
      </div>
    </div><!-- /.modal-content -->
</div>

<!-- endi of modal for close po-->

<script type="text/javascript">
$(document).ready(function() {
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

		$('.remove-piler-btn').click(function(e) {
			console.log('ee');
			$(this).parent().parent().remove();
		});

	});



    $('.date').datepicker({
      format: 'yyyy-mm-dd'
    });

    // Assign PO
    $('.assignPO').click(function() {
    	var count = $("[name='selected[]']:checked").length;

		if (count>0) {
			var answer = confirm('{{ $text_confirm_assign }}')

			if (answer) {
				var purchase_no = new Array();
				$.each($("input[name='selected[]']:checked"), function() {
					purchase_no.push($(this).val());
				});

    			$('#po_no').val(purchase_no.join(','));

    			// http://local.ccri.com/purchase_order/assign
    			location = "{{ $url_assign }}" + '&po_no=' + encodeURIComponent(purchase_no.join(','));
			} else {
				return false;
			}
		} else {
			alert('{{ $error_assign }}');
			return false;
		}
    });

    // Close PO
    /*$('.closePO').click(function() {
    	var purchase_no = $(this).data('id');

    	$("#closePoModal").modal('show');
    	$("#closePoModal").attr('data-id', purchase_no);

    });

    $('#closePOModalButton').click(function(){
    	var purchase_no = $("#closePoModal").data('id');
    	if(($('#invoiceNoInput').val() == '') || ($('#invoiceAmountInput').val() == '')) {
    		alert('Please input required fields.');
    	} else {
    		if(!isNaN($('#invoiceAmountInput').val())) {
    			if(purchase_no == '') {
		    		alert('Error, you have not chosen a PO to close.');
		    	} else {
		    		$('#closePO_' + purchase_no + ' input[name="invoice_no"]').val($('#invoiceNoInput').val());
		    		$('#closePO_' + purchase_no + ' input[name="invoice_amount"]').val($('#invoiceAmountInput').val());
		    		$('#closePO_' + purchase_no).submit();
		    	}
    		} else {
    			alert('Invoice amount should be a valid number.');
    		}
    	}
    });*/

	$('.closePO').click(function() {
    	var purchase_no = $(this).data('id');

    	var answer = confirm('Are you sure you want to close this PO?');
   		if (answer) {
	    	$('#closePO_' + purchase_no).submit();
    	} else {
			return false;
		}

    });

    //clear data id of closePOModal
    $('#closePoModal .close').click(function(){
    	$("#closePoModal").attr('data-id', '');
    });


    // Submit Assign PO
    $('#btn-assign').click(function() {
    	stockpiler = $('select[name=\'stock_piler\']').val();

    	if (stockpiler == undefined) {
    		alert('{{ $error_assign_po }}');
    		return false;
    	} else {
    		$('#form-assign').submit();
    	}
    });

    // Submit Form
    $('#submitForm').click(function() {
    	$('#form-purchase-order').submit();
    });

    $('#form-purchase-order input').keydown(function(e) {
		if (e.keyCode == 13) {
			$('#form-purchase-order').submit();
		}
	});

    // Clear Form
    $('#clearForm').click(function() {
    	$('#filter_po_no, #filter_entry_date, #filter_shipment_reference_no').val('');
		$('#filter_back_order').val('');

		$('select').val('');
		$('#form-purchase-order').submit();
    });

	// Export List
    $('#exportList').click(function() {
    	url = '';

		var filter_po_no = $('#filter_po_no').val();
		url += '?filter_po_no=' + encodeURIComponent(filter_po_no);

 

      	location = "{{ $url_export }}" + url;
    });



	// Export List
    $('#exportlistpdf').click(function() {
    	url = '';

		var filter_po_no = $('#filter_po_no').val();
		url += '?filter_po_no=' + encodeURIComponent(filter_po_no);
 

	 
	 

      	location = "{{ $url_exportpdf }}" + url;
    });

    // Select
    $('.tblrow').click(function() {
    	var rowid = $(this).data('id');

    	if ($('#selected-' + rowid).length>0) {
	    	if ($('#selected-' + rowid).is(':checked')) {
	    		$('#selected-' + rowid).prop('checked', false);
	    		$(this).children('td').removeClass('tblrow-active');
	    	} else {
	    		$('#selected-' + rowid).prop('checked', true);
	    		$(this).children('td').addClass('tblrow-active');
	    	}
    	} else {
    		$(this).children('td').removeClass('tblrow-active');
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


   	$('#reopen').click(function() {
   		var answer = confirm('{{ $text_confirm_reopen }}')
   		if (answer) {
	    	var docNo = $(this).data('id');
   			// alert(docNo);
	    	$("#reopenPoNo").val(docNo);
	    	$('#reopenForm').submit();
    	} else {
			return false;
		}
    
    });
	dataVal = { brand : $('#filter_brand').val()};
	getDivision(dataVal);

	$('#filter_brand').click(function() {
		var value = $(this).val();
		var dataVal = { brand : value };

		getDivision(dataVal);

        return false;
	});

	function getDivision(dataVal) {
		var select = $("#filter_division");
		select.html('');
		if (dataVal.brand == '') {
			select.append('<option value="" selected="selected">Please Select</option>');
		} else {
			select.append('<option value="" selected="selected">Please Select</option>');
			$.ajax({
	            url: 'purchase_order/get_division',
	            type: 'GET',
	            cache: false,
	            data: dataVal,
	            dataType: 'json',
	            success: function(result) {
	            	// select.html('');
	            	$.each(result, function(key, val) {
	            		select.append('<option value="' + key + '">' + val + '</option>');
	            	})
	            },
	            error: function(xhr, textstatus, errorthrown){
	            	return false;
	            },
	            complete: function() {
	            	return false;
	            }

	        });
		}
	}
});
</script>