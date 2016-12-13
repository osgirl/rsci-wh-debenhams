
<div class="control-group">
    <div class="controls">
        <div class="accordion" id="accordion2">
          <div class="accordion-group" style="background-color: #FFFFFF;">
           {{ Form::open(array('url'=>'stock_transfer/MTSReceiving', 'class'=>'form-signin', 'id'=>'form-MTSReceiving', 'role'=>'form', 'method' => 'get')) }} 
     
            <div id="collapseOne" class="accordion-body collapse in" style="padding-top: 20px;">
                   
                    <div class="span4">
                     <div>
                            <span class="search-po-left-pane"> MTS number:</span>
                            <span class="search-po-right-pane"> {{ Form::text('filter_doc_no', $filter_doc_no, array('class'=>'login', 'placeholder'=>'', 'id'=>"filter_doc_no")) }}
                            </span>
                    </div>
                <div>
                            <span class="search-po-left-pane"> Date Entry:</span>
                            <div class="search-po-right-pane input-append date">
                                {{ Form::text('filter_date_entry', $filter_date_entry, array('class'=>'span2', 'id'=>"filter_date_entry", 'readonly'=>'readonly')) }}
                                <span class="add-on"><i class="icon-th"></i></span>
                            </div>
                        </div>
                    </div>
                    <div class="span4">
                    
                         <div>
                            <span class="search-po-left-pane">{{ $label_store }}</span>
                            <span class="search-po-right-pane">
                                {{ Form::select('filter_store', array('' => $text_select) + $stores, $filter_store, array('class'=>'select-width', 'id'=>"filter_store")) }}
                            </span>
                        </div>
                         <div>
                            <span class="search-po-left-pane"> {{$label_store_to}}</span>
                            <span class="search-po-right-pane">
                                {{ Form::select('filter_store_name', array('' => $text_select) + $po_info, $filter_store_name, array('class'=>'select-width', 'id'=>"filter_store_name")) }}
                            </span>
                        </div>

                    </div>
                    <div class="span4">
                      <div>
                            <span class="search-po-left-pane">Piler name :</span>
                            <span class="search-po-right-pane">
                                {{ Form::select('filter_stock_piler', array('' => $text_select) + $stock_piler_list, $filter_stock_piler, array('class'=>'select-width', 'id'=>"filter_stock_piler")) }}
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

<div class="div-buttons">
       
            <a   role="button" class="btn btn-info btn-darkblue  assignReverse"  > {{$button_assign_to_stock_piler}}</a> 
             <a   role="button" class="btn btn-info btn-darkblue" href={{URL::to('stock_transfer/TLnumbersync')}}> Sync to Mobile</a> &nbsp;&nbsp;
            <!--  <a href="{{$url_export}}" class="btn btn-info btn-darkblue">Report</a> -->
   
 
        <div class="div-buttons btn-group ">
                        <button type="button" class="btn btn-info btn-darkblue " data-toggle="dropdown">Report <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu"> 
                          <li><a href={{url::to('stock_transfer/discrepansymts')}}>Over/Short Report</a></li>
                       <!--     <li><a href={{URL::to('stock_transfer/exportCSVunlisted')}}>Unlisted Report</a></li>  -->
                        </ul>
                    </div>  
           
     </div>

<div class="widget widget-table action-table">
    <div class="widget-header"> <i class="icon-th-list"></i>
      <h3>{{ $heading_title_stock }}</h3>
      <span class="pagination-totalItems">{{ $text_total }} {{ $picklist_count }}</span>
    </div>
   
    <div class="widget-content">
        <div class="table-responsive">
            <table class="table table-striped table-bordered">
                <thead >
                    
                    <th style="width: 20px;" class="align-center"><input type="checkbox" id="main-selected" /></th>
                     
                    <th>{{ $col_no }}</th>
                    <th style="width: 20px;"> MTS no.</th>
                    <th  >date created </th>
                    <th  >from  </th>
                    <th  >To  </th>
                    <th  >Piler Name</th>
                    
                    <th  >&nbsp;&nbsp;{{ $col_action }}</th>
                </thead>
                @if( !CommonHelper::arrayHasValue($stocktranferLIST) )
                    <tr class="font-size-13">
                        <td colspan="10" style="text-align: center;">{{ $text_empty_results }}</td>
                    </tr>
                @else
                    @foreach( $stocktranferLIST as $value )
                        <tr class="font-size-13 tblrow" data-id="{{ $value['so_no'] }}"
                        @if ( $value['data_display'] === 'Done' && ($value['quantity_to_pick'] != $value['moved_qty']) )
                            style="background-color:#F29F9F"
                        @endif
                        >
                            
                            <td class="align-center">
                                 @if( $value['so_status'] < 21)
                                <input type="checkbox" class="checkbox item-selected" name="selected[]" id="selected-{{ $value['so_no'] }}" value="{{ $value['so_no'] }}" />
                                @endif
                            </td>
                           
                            <td>{{ $counter++ }}</td> 
                            <td>
                              
                                <a href="{{ URL::to('store_return/mts_receiving_detail?picklistDoc='.$value['so_no']).'&filter_entry_date='.date('M d, Y', strtotime($value['date_entry'])).'&filter_store='.$value['store_name'] .'&filter_stock_piler='.$value['firstname'].' '. $value['lastname']}}">{{ $value['so_no'] }}
                               
                            </td>

                            <td>{{ date("M d, Y", strtotime($value['date_entry'])) }} </td>
                        
                            <td >{{ Store::getStoreName($value['from_store_code']) }} </td> 

                            <td >{{ Store::getStoreName($value['to_store_code']) }} </td> 
                            <td>{{ $value['firstname'].' '. $value['lastname'] }}</td> 
                            <td>                            
                            @if($value['so_status'] === 23)
                                    <a style="width: 80px;" disabled="disabled" class="btn btn-info">{{ $text_posted }}</a>  
                                     
                                @elseif ( $value['so_status'] === 22)
                                
                                        <a style="width: 80px;" class="btn btn-success" data-id="{{ $value['so_no'] }}" href={{url::to('stock_transfer/closemtsnumber?tl_number='. $value['so_no'])}}>{{ $button_close_picklist }}</a> 
                                      
                                @elseif ( $value['so_status'] === 20 )

                                <a style="width: 80px;" disabled="disabled" class="btn btn-danger">Assigned</a>

                                @elseif ( $value['so_status'] === 21 )

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

    @if( CommonHelper::arrayHasValue($stocktranferLIST) )
    <h6 class="paginate">
        <span>{{ $stocktranferLIST->appends($arrFilters)->links() }}</span>
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
        $('#filter_doc_no, #filter_date_entry, #filter_supplier, #filter_shipment_reference_no').val('');
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
   

    if (count>0) {
        /*var answer = confirm('Assign Selected MTS Number?');*/
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