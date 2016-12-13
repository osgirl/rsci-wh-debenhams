
<div class="control-group">
<div class="control-group">
    <a href="{{ $url_back }}" class="btn btn-info btn-darkblue"> <i class="icon-chevron-left"></i> back to list</a>
    
</div>
<h2><span class="label label-important" style="font-size: 18px; font-weight: normal;">Note: Please use Store filter first before using "Assign Pell no." button</span></h2>
    <div class="controls">
        <div class="accordion" id="accordion2">
          <div class="accordion-group" style="background-color: #FFFFFF;">
            {{ Form::open(array('url'=>'load/loadnumber', 'class'=>'form-signin', 'id'=>'form-loadnumber', 'role'=>'form', 'method' => 'get')) }}
            <div class="widget-header"> <i class="icon-th-list"></i>
      <h3>{{$col_pell_no}}</h3>
    </div>
            <div id="collapseOne" class="accordion-body collapse in" style="padding-top: 20px;">
                   
                    <div class="span4">
                     <div>
                             <span class="search-po-left-pane"> {{$col_pell_no_label}}</span>
                            <span class="search-po-right-pane">{{ Form::text('loadnumber', $loadnumber, array('id' => 'loadnumber', 'readonly' => 'readonly')) }}
                           
                            </span>

                          
                    </div>
                    <div>
                            <span class="search-po-left-pane"> MTS no.: </span>
                            <span class="search-po-right-pane"> 
                           {{ Form::text('filter_doc_no', $filter_doc_no, array('class'=>'login', 'placeholder'=>'', 'id'=>"filter_doc_no")) }}
                            </span>
                    </div>
                    <div>
                            <span class="search-po-left-pane"> Box no.: </span>
                            <span class="search-po-right-pane"> 
                           {{ Form::text('filter_box_code', $filter_box_code, array('class'=>'login', 'placeholder'=>'', 'id'=>"filter_box_code")) }}
                            </span>
                    </div>
                    </div>
                    <div class="span4">
                      <div>
                            <span class="search-po-left-pane">{{$col_store_name}} :</span>
                            <span class="search-po-right-pane"> {{ Form::select('filter_store', array('' => $text_select) + $stores, $filter_store, array('class'=>'select-width', 'id'=>"filter_store")) }}
                              
                            </span>
                        </div>
                            
                    <!--
                        <div>
                            <span class="search-po-left-pane">Ship By :</span>
                            <div class="search-po-right-pane input-append date">
                             
                                <span class="add-on"><i class="icon-th"></i></span>
                            </div>
                        </div>
                        -->
                    </div>
                    <div class="span3">
                    <!--  <div>
                            <span class="search-po-right-pane">Load #:</span>
                            <span class="search-po-right-pane">{{ Form::text('loadnumber', $loadnumber, array('id' => 'loadnumber', 'readonly' => 'readonly')) }}
                           
                            </span>
                        </div> -->

                  <!--   <div class="span11 control-group collapse-border-top">
                        <a class="btn btn-success btn-darkblue" id="submitForm">Search</a>
                        <a class="btn" id="clearForm">Clear</a>
                    </div> -->
                      
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
          
            @if ( CommonHelper::valueInArray('CanSyncPurchaseOrders', $permissions) )
            <a   role="button" class="btn btn-info btn-darkblue assignTLnumber" title="Assign To Pell number" data-toggle="modal">{{$btn_rem_box}}</a>
            @endif
    </div>


<div class="clear">
    <div class="div-paginate">
   
            <h6 class="paginate">
                <span>&nbsp;</span>
            </h6>
       
    </div>
    <div class="div-buttons">
       
    </div>
</div>
<div class="widget widget-table action-table">
    <div class="widget-header"> <i class="icon-th-list"></i>
     <h3>Box number list</h3>
      <span class="pagination-totalItems"></span>
    </div>

    <div class="widget-content">
        <div class="table-responsive">
            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th style="width: 20px;" class="align-center"><input type="checkbox" id="main-selected"></th>
                        <th>NO.</th>
                        <th>  Box no. </th>
                        <th> MTS no. </th>
                        <th> Store Name </th>
                        <th>Store address</th>
                  
                      
                    </tr>
                </thead>
               @if( !CommonHelper::arrayHasValue($picklist)  )
                    <tr class="font-size-13">
                        <td colspan="13" style="text-align: center;">No Results Found</td>
                    </tr>
                @else
                    @foreach( $picklist as $tlnumber )
                        <tr class="font-size-13 tblrow" data-id="{{ $tlnumber['box_code'] }}"
                    
                        >
                            
                            <td class="align-center">
                           
                                <input type="checkbox" class="checkbox item-selected" name="selected[]" id="selected-{{ $tlnumber['box_code'] }}" value="{{ $tlnumber['box_code'] }}" />
                             
                            </td>
                            
                            <td>{{ $counter++ }}</td>
                             
                            <td>{{ $tlnumber['box_code'] }} </td>
                            <td>{{$tlnumber['MASTER_EDU']}}</td>

                            <td> {{$tlnumber['store_code']}} - {{ Store::getStoreName($tlnumber['store_code']) }}</td>
                            <td> {{$tlnumber['address1']}}</td>
                             
                          <!--   <td class="align-center">

                                    &nbsp;&nbsp;<a style="width: 70px;" disabled="disabled" class="btn btn-success ">{{ $button_close_picklist }}</a> -->
                                
                            

                               
                            </td>

                                
                            </td>
                        </tr>
                    @endforeach
               @endif
                   <!--          <td class="align-center">
                           
                                    &nbsp;&nbsp;<a style="width: 70px;" disabled="disabled" class="btn btn-info"> </a>  
                                  
                                        &nbsp;&nbsp;<a style="width: 70px;" class="btn btn-success closePicklist" data-id=" "> </a> 
                                     
                               
                                &nbsp;&nbsp;<a style="width: 70px;" disabled="disabled" class="btn btn-danger"> </a>
                                
                                    &nbsp;&nbsp;<a style="width: 70px;" disabled="disabled" class="btn"> </a>
                                
                               
                            </td> -->
               
            </table>
        </div>
    </div>  
</div>

<div id="add-load-modal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="load-boxes-modal-label" aria-hidden="true">
     <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title">New Load Code</h4>
      </div>
      <div class="modal-body">
   
        <span id="load-code-created"></span>
      </div>
      <div class="modal-footer">
          <button type="button" class="btn btn-default" id="close-add-load" >Close</button>
      </div>
    </div><!-- /.modal-content -->
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
        $('#filter_doc_no, #filter_status, #filter_store, #filter_transfer_no, #filter_action_date, #filter_doc_no, #filter_box_code').val('');
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
        var answer = confirm('Assign Selected Box number?');
        if (answer) {
            var boxes = new Array();
            
            $.each($("input[name='selected[]']:checked"), function() {
                boxes.push($(this).val());
               
            });
            $('#box-codes').val(boxes.join(','));
          

        location =  'boxnumber?&loadnumber='+ '{{$loadnumber}}'  + '&tlnumber=' + encodeURIComponent(boxes.join(','));

        } else {
            return false;
        }
    } else {
        alert('Please Choose Box number ');
        return false;
    }
    });
$('#submitForm').click(function() {
        $('#form-loadnumber').submit();
    });

    $('#form-loadnumber').keydown(function(e) {
        if (e.keyCode == 13) {
            $('#form-loadnumber').submit();
        }
    });
  
 });
</script>