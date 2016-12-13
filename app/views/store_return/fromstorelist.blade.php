
<div class="control-group">
    <div class="controls">
        <div class="accordion" id="accordion2">
          <div class="accordion-group" style="background-color: #FFFFFF;">
          
            <div class="widget-header">  
     
    </div>
            <div id="collapseOne" class="accordion-body collapse in" style="padding-top: 20px;">
                   
                    <div class="span4">
                     <div>
                            <span class="search-po-left-pane"> MTS number:</span>
                            <span class="search-po-right-pane">  {{ Form::text('filter_so_no', $filter_so_no, array('class'=>'login', 'placeholder'=>'', 'id'=>"filter_so_no")) }}
                            </span>
                    </div>
               
                    </div>
                    <div class="span4">
                      <div>
                            <span class="search-po-left-pane">Status : </span>
                            <span class="search-po-right-pane">  {{ Form::select('filter_status', array('default' => $text_select) + $po_status_type, $filter_status, array('class'=>'select-width', 'id'=>"filter_status")) }}
                              
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
          
   
            <a   role="button" class="btn btn-info btn-darkblue  assignTLnumber" title="Its allow you to assigned from store"  >Assign To Store</a> 
    </div>
    <div class="div-buttons">
          
   
            <a   role="button" class="btn btn-info btn-darkblue  assignTLnumber" title="Its allow you to assigned from store"  >Assign StockPiler</a> &nbsp;&nbsp;
    </div>
<div class="div-buttons"> 
  <a   role="button" class="btn btn-info btn-darkblue  assignTLnumber" title="Its allow you to assigned from store"  >Remove  Store </a> &nbsp;&nbsp;
          </div>
            <a   role="button" class="btn btn-info btn-darkblue  assignTLnumber" title="Its allow you to assigned from store"  >Sync to Mobile</a> 
<div class="clear">
    <div class="div-paginate">
   
            <h6 class="paginate">
                <span>&nbsp;</span>
            </h6>
       
    </div>
   
        
          
   
    
    
</div>
<div class="widget widget-table action-table">
    <div class="widget-header"> <i class="icon-th-list"></i>
     <h3>MTS List</h3>
      <span class="pagination-totalItems"></span>
    </div>

    <div class="widget-content">
        <div class="table-responsive">
            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th style="width: 20px;" class="align-center"><input type="checkbox" id="main-selected"></th>
                        <th>NO.</th>
                        <th>  MTS Number </th>
                        <th> From Store </th>
                     
                       <th> To Store</th> 
                        <th> Piler Name</th> 
                        <th> Status</th>
                      
                    </tr>
                </thead>
           
                        <tr class="font-size-13 tblrow" data-id=" ">
                      
                            
                            <td class="align-center">
                           
                                <input type="checkbox" class="checkbox item-selected" name="selected[]" id="selected- " value=" " />
                             
                            </td>
                            
                            <td> </td>
                             
                            <td> </td>

                            <td>   </td>
                             <td >    </td>
                             <td >    </td>
                            <td >    </td>

                            
                        </tr>
               
               
            </table>
        </div>
    </div>  
</div>

<!-- <div id="add-load-modal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="load-boxes-modal-label" aria-hidden="true">
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
    </div>  
</div>
-->
   