<div class="control-group">
    
    <a class="btn btn-info btn-darkblue" href="{{url('purchase_order/division?filter_po_no='.$filter_po_no.'&filter_stock_piler='.$filter_stock_piler.'&division='.$division.'&receiver_no='.$receiver_no.'&filter_shipment_reference_no='.$filter_shipment_reference_no. '&total_qty='.$total_qty)}}"><i class="icon-chevron-left"></i> {{ $button_back }}</a>
  
</div>
 
 {{ Form::open(array('url'=>'purchase_order/detail', 'id'=>"form-assign", 'class'=>'form-horizontal', 'style' => 'margin: 0px;', 'role'=>'form')) }}

<div class="widget widget-table action-table">
    <div class="widget-header"> <i class="icon-th-list"></i>
      <h3>{{ $heading_title_po_details }}</h3>
    </div>
    <!-- /widget-header -->
    <div class="widget-content">
       <div class="row-fluid stats-box">
            <div class="span4">
                <div>
                    <span class="left-pane">{{ $label_purchase_no }}</span>
                    <span class="right-pane">   {{ Form::text('filter_po_no', $filter_po_no, array('readonly' => 'readonly')) }}</span>
                </div>
                
            </div>

            <div class="span4">
               
                
          </div>

          <div class="span4">
                
            <div>
                    <span class="left-pane">PilerName :</span>
                    <span class="right-pane">  {{ Form::text('filter_stock_piler', $filter_stock_piler, array('readonly' => 'readonly')) }}</span>
                </div>
                
          </div>

          <div class="span4">
                
               <div>
                    <span class="left-pane">Division :</span>
                    <span class="right-pane">  {{ Form::text('division', $division, array('readonly' => 'readonly')) }}</span>
                </div>
          </div>
       </div>
     </div>
</div>

<div class="clear">
    <div class="div-paginate">
        @if(CommonHelper::arrayHasValue($purchase_orders) )
            <h6 class="paginate">
                <span>{{ $purchase_orders->appends($arrFilters)->links() }}&nbsp;</span>
            </h6>
        @else
            &nbsp;
        @endif
    </div>
</div>

<div class="widget widget-table action-table">
    <div class="widget-header"> <i class="icon-th-list"></i>
      <h3>{{ $heading_title_po_contents }}</h3>
      <span class="pagination-totalItems">{{ $text_total }} {{ $purchase_orders_count }}</span>
    </div>
    <!-- /widget-header -->
    <div class="widget-content">
        <div class="table-responsive" onkeypress="return isNumber(event)">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>{{ $col_id }}</th>
                        <th><a href="{{ $sort_sku }}" class="@if( $sort_detail=='sku' ) {{ $order_detail }} @endif">{{ $col_sku }}</a></th>
                        <th><a href="{{ $sort_upc }}" class="@if( $sort_detail=='upc' ) {{ $order_detail }} @endif">{{ $col_upc }}</a></th>
                        <th> {{ $col_short_name }}</a></th>

                        <th>{{$col_expected_quantity}}</th>
                      
                        <th>{{$col_received_quantity}}</th>
                        <th> VARIANCE </th>
                        <th> REMARKS </th>
                    </tr>
                </thead>
                @if( !CommonHelper::arrayHasValue($purchase_orders) )
                <tr class="font-size-13">
                    <td colspan="7" class="align-center">{{ $text_empty_results }}</td>
                </tr>
                @else
                    @foreach( $purchase_orders as $po )
                    <tr class="font-size-13"
                    @if ( $po->quantity_ordered !== $po->quantity_delivered )
                        style="background-color:#F29F9F"
                    @endif
                    >
                        <td>{{ $counter++ }}</td>
                        <td>{{ $po->sku  }}</td>
                        <td>{{ $po->upc }}</td>
                        <td>{{ $po->description }}</td>
                        <td>{{ $po->quantity_ordered }}</td> 
                          

               
                        <td >     
                    
                                @if($po->po_status == 4 )                     
                         {{ Form::open(array('url'=>'purchase_order/updateqty', 'class'=>'form-signin', 'id'=>'form-purchase-order', 'role'=>'form', 'method' => 'get')) }}   
                       {{ Form::hidden('upc', $po->upc) }}
                        {{ Form::hidden('receiver_no',  Input::get('receiver_no', NULL)) }}
                        {{ Form::hidden('division_id', $po->dept_number) }}
                        {{ Form::hidden('filter_po_no', $filter_po_no) }}
                        {{ Form::hidden('filter_stock_piler', $filter_stock_piler) }}
                        {{ Form::hidden('filter_shipment_reference_no', $filter_shipment_reference_no) }} 
                        {{ Form::hidden('total_qty', $total_qty) }} 
                        {{ Form::hidden('division', $division) }}

                        {{ Form::text('quantity', $po->quantity_delivered, array('class'=>'form-signin', 'placeholder'=>'', 'id'=>"readonly")) }}
                        {{ Form::close() }}
                                @else 
                                <input style="text-align: center;" type="" name="" disabled value="{{ $po->quantity_delivered }} ">
                                @endif
                      

                        </td>
                        


                        <td>{{ $po->quantity_delivered - $po->quantity_ordered  }}</td>
                        <td>    
                        @if($po->quantity_ordered  == '0' && $po->sku == '')
                                    Not in PO and MasterList!
                                
                        @elseif ($po->quantity_ordered  == '0' )
                                    Not in PO
                        @elseif ( $po->sku == '' )
                                    Not in MasterList!
                        @else
                              
                                    
                                     
                        @endif</td>
                    </tr>
                    @endforeach
                @endif
            </table>
        </div>
    </div>

    @if( CommonHelper::arrayHasValue($purchase_orders) )
    <h6 class="paginate">
        <span>{{ $purchase_orders->appends($arrFilters)->links() }}</span>
    </h6>
    @endif

  
    </div>

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
                {{ Form::label('invoice_no',$label_invoice_number, array("style" => "margin-right:10px","class" => "col-sm-2 control-label"))}}
                <div class="col-sm-10">
                    {{ Form::text('invoice_no', '', array('id'=>'invoiceNoInput','class'=> "form-control"))}}
                </div>
            </div>
            <br/>
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

    <!-- end of modal for close po-->

    <!-- /widget-content -->
</div>

<script type="text/javascript">
$(document).ready(function() {

$('#form-purchase-order').keydown(function(e) {
        if (e.keyCode == 13) {
            $('#form-purchase-order').submit();
        }
    });
 
    // Submit Form
    $('#submitForm').click(function() {
        $('#form-purchase-order').submit();
    });

    });
</script>