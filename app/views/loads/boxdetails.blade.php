 <div class="control-group">
    <a href="{{ $url_back }}" class="btn btn-info btn-darkblue"> <i class="icon-chevron-left"></i> back to list</a>
    
</div>
            {{ Form::open(array('url'=>'load/boxdetails', 'class'=>'form-signin', 'id'=>'form-boxdetails', 'role'=>'form', 'method' => 'get')) }}
            <div class="widget-header"> <i class="icon-th-list"></i>
      <h3>{{$header_pell_details}}</h3>
     </div>

    <!-- /widget-header -->
	<div class="widget-content">
	   <div class="row-fluid stats-box">
	      	<div class="span4">
	        	<div>
		        	<span class="left-pane">{{$col_pell_no_label}}</span>
		        	<span class="left-pane">{{ Form::text('loadnumber', $loadnumber, array('id' => 'loadnumber', 'readonly' => 'readonly')) }}</span>

		        </div>
               
                <div>
                    <span class="left-pane"> Box no. : </span>
                    <span class="left-pane">{{ Form::text('filter_box_code', $filter_box_code, array('class'=>'login', 'placeholder'=>'', 'id'=>"filter_box_code")) }}</span>
                </div>
                <div>
                    <span class="left-pane"> MTS no. : </span>
                    <span class="left-pane">{{ Form::text('filter_doc_no', $filter_doc_no, array('class'=>'login', 'placeholder'=>'', 'id'=>"filter_doc_no")) }}</span>
                </div>

	   </div>
	   <div class="span4">
	        	
                 <div>
                    <span class="left-pane"> {{$entry_stock_piler}}</span>
                    <span class="left-pane">{{ Form::text('pilername', $pilername, array('id' => 'pilername', 'readonly' => 'readonly')) }}</span>
                </div>
                  <div>
                    <span class="left-pane"> Store Names:</span>
                    <span class="left-pane">    {{ Form::select('filter_store', array('' => $text_select) + $stores, $filter_store, array('class'=>'select-width', 'id'=>"filter_store")) }}</span>
                </div>
	   </div>
      
     </div>
</div>
   
  <div class="span11 control-group collapse-border-top">
                        <a class="btn btn-success btn-darkblue" id="submitForm">Search</a>
                        <a class="btn" id="clearForm">Clear</a>
        </div> 
       

                        @if ( CommonHelper::valueInArray('CanSyncPurchaseOrders',  $permissions) || CommonHelper::valueInArray('CanSyncPurchaseOrders', $permission ))
          <div class="div-buttons">
            <a   role="button" class="btn btn-info btn-darkblue removebutton" title="Its allow you to remove box no" data-toggle="modal">{{$btn_rem_box}}</a></div>
                        @endif
        <!--   <div class="div-buttons">
              <a   role="button" class="btn btn-info btn-darkblue" disabled title="Assign To Picker" data-toggle="modal">Remove TL number</a></div> -->
          
<div class="clear">
    <div class="div-paginate">
        @if(CommonHelper::arrayHasValue($boxesdetails) )
            <h6 class="paginate">
                <span>{{ $boxesdetails->appends($arrFilters)->links() }}&nbsp;</span>
            </h6>
        @else
            &nbsp;
        @endif
    </div>
 

<div class="widget widget-table action-table">
    <div class="widget-header"> <i class="icon-th-list"></i>
      <h3>{{$pell_no_header_contnt}}</h3>
       <span class="pagination-totalItems"> </span>
    </div>
    <!-- /widget-header -->
    <div class="widget-content">
    	<div class="table-responsive">
			<table class="table table-striped table-bordered">
				<thead>
					<tr>
						 <th style="width: 20px;" class="align-center"><input type="checkbox" id="main-selected"></th>
				 	<th>No.</th>
						<th> Box number </th>
                        <th>  MTS no. </th>
                        <th>  store name    </th>
                        <th>  store address    </th>
					 
			 
						
					</tr>
				</thead>
							
		 	    @if( !CommonHelper::arrayHasValue($boxesdetails)  )
					<tr class="font-size-13">
						<td colspan="13" style="text-align: center;">No Results Found</td>
					</tr>
			 
                @else
					@foreach( $boxesdetails as $body)
				        <tr class="font-size-13 tblrow" data-id="{{ $body->box_number  }}">
                            <td class="align-center">
                             @if($body->data_value != 1)
                                <input type="checkbox" class="checkbox item-selected" name="selected[]" id="selected-{{ $body->box_number  }}" value="{{ $body->box_number }}" />
                            @endif
                            </td>
                            
                            <td>{{ $counter++ }}</td>
                             
                            <td>{{ $body->box_number }} </td>
                            <td>{{ $body->MASTER_EDU }} </td>

                            <td> {{ $body->store_name }}</td>
                            <td> {{ $body->address1 }}</td>
                        
                                                                                    
                            </td>
                        </tr>
                       
				@endforeach
				@endif			
			</table>
		</div>
	</div>

<script type="text/javascript">

$(document).ready(function()

{

     $('#submitForm').click(function() {
        $('#form-boxdetails').submit();
    });
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

 $('#clearForm').click(function() {
        $('#filter_doc_no, #filter_box_code').val('');
        $('select').val('');
        $('#form-boxdetails').submit();
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
$('.removebutton').click(function() {
    var count = $("[name='selected[]']:checked").length;
   

    if (count>0) {
      /*  var answer = confirm('Do you want to removed this TL number?');*/
        if (count) {
            var boxes = new Array();
            
            $.each($("input[name='selected[]']:checked"), function() {
                boxes.push($(this).val());
               
            });
            $('#box-codes').val(boxes.join(','));
          

         location =  'removed?&loadnumber='+ '{{$loadnumber}}'  + '&tlnumber=' + encodeURIComponent(boxes.join(','));
        } else {
            return false;
        }
    } else {
        alert('Please Choose Box number');
        return false;
    }
    });
$('#submitForm').click(function() {
        $('#form-boxdetails').submit();
    });

    $('#form-boxdetails').keydown(function(e) {
        if (e.keyCode == 13) {
            $('#form-boxdetails').submit();
        }
    });
  
 });
</script>
