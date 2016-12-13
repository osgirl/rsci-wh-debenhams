 <div class="control-group">
    <div class="controls">
        <div class="accordion" id="accordion2">
          <div class="accordion-group" style="background-color: #FFFFFF;">
           {{ Form::open(array('url'=>'reverse_logistic/reverse_list', 'class'=>'form-signin', 'id'=>'form-MTSReceiving', 'role'=>'form', 'method' => 'get')) }} 
     
            <div id="collapseOne" class="accordion-body collapse in" style="padding-top: 20px;">
                   
                    <div class="span4">
                     <div>
                            <span class="search-po-left-pane"> MTS no.:</span>
                            <span class="search-po-right-pane"> {{ Form::text('filter_doc_no', $filter_doc_no, array('class'=>'login', 'placeholder'=>'', 'id'=>"filter_doc_no")) }}
                            </span>
                    </div>
                  <div>
                            <span class="search-po-left-pane">Picker :</span>
                            <span class="search-po-right-pane">
                                {{ Form::select('filter_stock_piler', array('' => $text_select) + $stock_piler_list, $filter_stock_piler, array('class'=>'select-width', 'id'=>"filter_stock_piler")) }}
                            </span>
                        </div>
                    </div>
                    <div class="span4">
                     <div>
                            <span class="search-po-left-pane"> Date Entry:</span>
                            <div class="search-po-right-pane input-append date">
                                {{ Form::text('filter_entry_date', $filter_entry_date, array('class'=>'span2', 'id'=>"filter_entry_date", 'readonly'=>'readonly')) }}
                                <span class="add-on"><i class="icon-th"></i></span>
                            </div>
                        </div>
                        
                        <div>
                            <span class="search-po-left-pane">{{ $label_store }}</span>
                            <span class="search-po-right-pane">
                                {{ Form::select('filter_store', array('' => $text_select) + $stores, $filter_store, array('class'=>'select-width', 'id'=>"filter_store")) }}
                            </span>
                        </div>
                    </div>
                    
                   
                     <div class="span11 control-group collapse-border-top">
                        <a class="btn btn-success btn-darkblue" id="submitForm">Search</a>
                        <a class="btn" id="clearForm">Clear</a>
                    </div>  
          </div>
              
        </div>

    </div> <!-- /controls -->
</div> <!-- /control-group -->

<div class="div-buttons btn-group">
  <button type="button" class="btn btn-info btn-darkblue " data-toggle="dropdown" href={{$url_export}}>Report 
  <span class="caret"></span></button>
        <ul class="dropdown-menu">
       <li><a href="{{url('reverse_logistic/discrepansy')}}">Overage/Shortage Report</a></li>
                          <!--  <li><a href={{$url_export}}>Overage/Shortage Report</a></li>  -->
        </ul>
</div>  
  
 <div class="div-buttons">
       
 <a  role="button" class="btn btn-info btn-darkblue  assignReverse">{{$button_assign_to_stock_piler}}</a> 
 <a role="button" class="btn btn-info btn-darkblue" href={{URL::TO('reverse_logistic/TLnumbersync')}}> Sync to Mobile</a>&nbsp;&nbsp;
    </div>
 
<div class="clear">
<div class="div-paginate">  
             @if( CommonHelper::arrayHasValue($reverselogisticlist) )
    <h6 class="paginate">
        <span>{{ $reverselogisticlist->appends($arrFilters)->links() }}</span>
    </h6>
    @endif
    </div>
   
</div>
           
       
           
     

<div class="widget widget-table action-table">
    <div class="widget-header"> <i class="icon-th-list"></i>
      <h3> Return to Warehouse list</h3>
      <span class="pagination-totalItems">{{ $text_total }} {{ $picklist_count }}</span>
    </div>
   
    <div class="widget-content">
        <div class="table-responsive">
            <table class="table table-striped table-bordered">
                <thead >
                    
                    <th style="width: 20px;" class="align-center"><input type="checkbox" id="main-selected" /></th>
                     
                    <th>{{ $col_no }}</th>
                    <th style="width: 20px;"> MTS no. </th>
                    
                    <th  >STORE</th>
                    <th  >Piler Name</th>
                    <th  >date created </th>
                    <th  >&nbsp;&nbsp;{{ $col_action }}</th>
                </thead>
                @if( !CommonHelper::arrayHasValue($reverselogisticlist) )
                    <tr class="font-size-13">
                        <td colspan="10" style="text-align: center;"> No Result Found</td>
                    </tr>
                @else
                    @foreach( $reverselogisticlist as $value )
                        <tr class="font-size-13 tblrow" data-id="{{ $value['move_doc_number'] }}"
                       
                        >
                            
                            <td class="align-center">
                                 @if( $value['so_status'] < 21)
                                <input type="checkbox" class="checkbox item-selected" name="selected[]" id="selected-{{ $value['move_doc_number'] }}" value="{{ $value['move_doc_number'] }}" />
                                @endif
                            </td>
                           
                            <td>{{ $counter++ }}</td> 
                            <td>
                              
                                <a href="{{url::to('reverse_logistic/detail?picklist_doc='.$value['move_doc_number'].'&filter_stock_piler='.$value['firstname'].' '.$value['lastname'].'&filter_created_at='.date('M d, Y', strtotime($value['created_at'])))}}">{{ $value['move_doc_number'] }}
                               
                            </td>

                            <td>{{ $value['store_name'] }} </td>
                        
                            
                            <td> {{ $value['firstname'].' '.$value['lastname']  }}</td> 
                            <td > {{ date("M d, Y", strtotime($value['created_at'])) }} </td> 
                            <td>                            
                            @if($value['data_display'] === 'Posted')
                                    <a style="width: 80px;" disabled="disabled" class="btn btn-info">{{ $text_posted }}</a>  
                                @elseif ( $value['data_display'] === 'Done' )
                                
                                <a style="width: 80px;" class="btn btn-success closePicklist" data-id="{{ $value['move_doc_number'] }}" href={{URL::TO('reverse_logistic/closetlnumberReverse?tl_number='.$value['move_doc_number'])}}>{{ $button_close_picklist }}</a> 
                                     
                                @elseif ( $value['data_display'] === 'Assigned' )

                                <a style="width: 80px;" disabled="disabled" class="btn btn-danger">Assigned</a>

                                @elseif ( $value['data_display'] === 'In Process' )

                                <a style="width: 80px;" disabled="disabled" class="btn btn-danger">In Process</a>
                                @else
                                    <a style="width: 80px;" disabled="disabled" class="btn">{{ $button_close_picklist }}</a>
                                
                                @endif
                                
                                 
                            </td>
                        </tr>
                    @endforeach
                @endif
            </table>
        </div>
    </div>

    @if( CommonHelper::arrayHasValue($reverselogisticlist) )
    <h6 class="paginate">
        <span>{{ $reverselogisticlist->appends($arrFilters)->links() }}</span>
    </h6>
    @endif


</div>
 <script type="text/javascript">
$(document).ready(function() {

    //clear data id of closePOModal
    $('#closePoModal .close').click(function(){
        $("#closePoModal").attr('data-id', '');
    });
$('#form-MTSReceiving').keydown(function(e) {
        if (e.keyCode == 13) {
            $('#form-MTSReceiving').submit();
        }
    });
 
    // Submit Form
    $('#submitForm').click(function() {
        $('#form-MTSReceiving').submit();
    });

    $('#form-MTSReceiving input').keydown(function(e) {
        if (e.keyCode == 13) {
            $('#form-MTSReceiving').submit();
        }
    });

    // Clear Form
    $('#clearForm').click(function() {
        $('#filter_doc_no, #filter_entry_date, #filter_supplier, #filter_shipment_reference_no').val('');
        $('#filter_created_at, #filter_back_order').val('');

        $('select').val('');
        $('#form-MTSReceiving').submit();
    });

    // Export List
 

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
 $('.assignReverse').click(function() {
    var count = $("[name='selected[]']:checked").length;
   

    if (count>0) {/*
        var answer = confirm('Assign Selected MTS Number?');*/
        if (count) {
            var boxes = new Array();
            
            $.each($("input[name='selected[]']:checked"), function() {
                boxes.push($(this).val());
               
            });
            $('#box-codes').val(boxes.join(','));
          

        location =  'assign?'  + '&so_no=' + encodeURIComponent(boxes.join(','));

        } else {
            return false;
        }
    } else {
        alert('Please Choose MTS Number!!! ');
        return false;
    }
    });
   $('.date').datepicker({
      format: 'yyyy-mm-dd'
    });
 
});
</script>