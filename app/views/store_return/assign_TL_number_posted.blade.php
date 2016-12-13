<div class="control-group">
<div class="control-group">
    <a href="{{ $url_back }}" class="btn btn-info btn-darkblue"> <i class="icon-chevron-left"></i> back to list</a>
    
</div>
<h2><span class="label label-important" style="font-size: 18px; font-weight: normal;">Note: Please use Store filter first before using "Assign Pell no." button</span></h2>
    <div class="controls">
        <div class="accordion" id="accordion2">
          <div class="accordion-group" style="background-color: #FFFFFF;">
           {{ Form::open(array('url'=>'stock_transfer/assignToTLNumber', 'class'=>'form-signin', 'id'=>'form-loadnumber', 'role'=>'form', 'method' => 'get')) }}
            <div class="widget-header"> <i class="icon-th-list"></i>
      <h3> Pell number content</h3>
    </div>
            <div id="collapseOne" class="accordion-body collapse in" style="padding-top: 20px;">
                   
                    <div class="span4">
                     <div>
                             <span class="search-po-left-pane">  Pell number :</span>
                            <span class="search-po-right-pane">{{ Form::text('loadnumber', $loadnumber, array('id' => 'loadnumber', 'readonly' => 'readonly')) }}
                           
                            </span>

                          
                    </div>
                    <div>
                            <span class="search-po-left-pane"> Box no.: </span>
                            <span class="search-po-right-pane"> 
                           {{ Form::text('filter_doc_no', $filter_doc_no, array('class'=>'login', 'placeholder'=>'', 'id'=>"filter_doc_no")) }}
                            </span>
                    </div>
                    <div>
                            <span class="search-po-left-pane"> MTS no.: </span>
                            <span class="search-po-right-pane">  {{ Form::text('filter_doc_no_pick', $filter_doc_no_pick, array('class'=>'login', 'placeholder'=>'', 'id'=>"filter_doc_no_pick")) }}
                           
                            </span>
                    </div>
                    </div>
                    <div class="span5">
                       
                        <div>
                            <span class="search-po-left-pane">{{ $label_store }}</span>
                            <span class="search-po-left-pane">
                                {{ Form::select('filter_store', array('' => $text_select) + $stores, $filter_store, array('class'=>'select-width', 'id'=>"filter_store")) }}
                            </span>
                        </div>
                      
                     <div>
                            <span class="search-po-left-pane"> {{$label_store_to}}</span>
                            <span class="search-po-left-pane">
                                {{ Form::select('filter_store_name', array('' => $text_select) + $po_info, $filter_store_name, array('class'=>'select-width', 'id'=>"filter_store_name")) }}
                            </span>
                        </div>
                    </div>
        
                
                   
                     <div class="span11 control-group collapse-border-top">
                        <a class="btn btn-success btn-darkblue" id="submitForm">Search</a>
                        <a class="btn" id="clearForm">Clear</a>
                    </div>  
          </div>
             {{ Form::hidden('sort', $sort) }}
            {{ Form::hidden('order', $order) }}

            {{ Form::close() }}
        </div>

    </div> <!-- /controls -->
</div> <!-- /control-group -->

 

 
<div class="div-buttons">
          
            <a   role="button" class="btn btn-info btn-darkblue assignTLnumber" title="Assign To LoadNumber" data-toggle="modal">{{$btn_subloc_pell_no}}</a>
    </div>
    
<div class="div-paginate">  
             @if( CommonHelper::arrayHasValue($picklist) )
    <h6 class="paginate">
        <span>{{ $picklist->appends($arrFilters)->links() }}</span>
    </h6>
    @endif
    </div>

<div class="widget widget-table action-table">
    <div class="widget-header"> <i class="icon-th-list"></i>
      <h3> {{$subloc_load_TLposted}}</h3>
       <span class="pagination-totalItems"></span>
    </div>
    <!-- /widget-header -->
    <div class="widget-content">
    	<div class="table-responsive">
			<table class="table table-striped table-bordered">
				<thead>
					<tr>
						 <th style="width: 20px;" class="align-center"><input type="checkbox" id="main-selected"></th>
				 	<th>No.</th>
                    <th> Box no. </th>
						<th> MTS no. </th>
                        <th>  From    </th>
                        <th> To    </th>
					 
			 
						
					</tr>
				</thead>
							
		 	      @if( !CommonHelper::arrayHasValue($picklist)  )
                    <tr class="font-size-13">
                        <td colspan="13" style="text-align: center;">No Results Found</td>
                    </tr>
                @else
                     @foreach( $picklist as $tlnumber )
                        <tr class="font-size-13 tblrow" data-id="{{ $tlnumber['box_code']  }}" 
                        >
                            
                            <td class="align-center">
                           
                                <input type="checkbox" class="checkbox item-selected" name="selected[]" id="selected-{{ $tlnumber['box_code'] }}" value="{{ $tlnumber['box_code'] }}" />
                             
                            </td>
                               <td> {{$counter++}} </td>
                               <td>{{$tlnumber['box_code']}}</td>
                            <td> {{$tlnumber['move_doc_number']}} </td>
                             
                         
                            <td> {{$tlnumber['from_store_code']}} - {{ Store::getStoreName($tlnumber['from_store_code']) }}  </td>
                            <td> {{$tlnumber['to_store_code']}} -  {{ Store::getStoreName($tlnumber['to_store_code']) }}</td>
                        
                                                                                    
                            </td>
                        </tr>
                       @endforeach
				 		@endif
			</table>
		</div>
	</div>
<div class="div-paginate">  
             @if( CommonHelper::arrayHasValue($picklist) )
    <h6 class="paginate">
        <span>{{ $picklist->appends($arrFilters)->links() }}</span>
    </h6>
    @endif
    </div>
 
<script type="text/javascript">

$(document).ready(function()

{

     $('#submitForm').click(function() {
        $('#form-loadnumber').submit();
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
        $('#filter_doc_no, #filter_doc_no_pick, #filter_store_name, #filter_store').val('');
        $('select').val('');
        $('#form-loadnumber').submit();
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
$('.assignTLnumber').click(function() {
    var count = $("[name='selected[]']:checked").length;
   

    if (count>0) {
       /* var answer = confirm('Assign Selected TL number?');*/
        if (count) {
            var boxes = new Array();
            
            $.each($("input[name='selected[]']:checked"), function() {
                boxes.push($(this).val());
               
            });
            $('#box-codes').val(boxes.join(','));
          

          location =  'stocknumbertlnumber?&loadnumber='+ '{{$loadnumber}}'  + '&tlnumber=' + encodeURIComponent(boxes.join(','));

        } else {
            return false;
        }
    } else {
        alert('Please Choose TL number ');
        return false;
    }
    });
 

    $('#form-loadnumber').keydown(function(e) {
        if (e.keyCode == 13) {
            $('#form-loadnumber').submit();
        }
    });
  
 });
</script>