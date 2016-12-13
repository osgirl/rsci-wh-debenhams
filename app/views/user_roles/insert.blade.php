@if( $errors->all() )
    <div class="alert alert-error">
    	<button class="close" data-dismiss="alert" type="button">&times;</button>
    	{{ HTML::ul($errors->all()) }}
    </div>
@endif

<div class="widget">
    <div class="widget-header"> <i class="icon-group"></i>
    	<h3>{{ $heading_title_insert }}</h3>
    </div>
    <!-- /widget-header -->

    <div class="widget-content">
    	{{ Form::open(array('url'=>'user_roles/insertData', 'class'=>'form-horizontal', 'id'=>'form-user-roles', 'role'=>'form', 'method' => 'post')) }}
		<div class="span11">
			<fieldset>
				<div class="control-group">
					<label class="control-label" for="role_name">{{ $entry_role_name }}</label>
					<div class="controls">
						{{ Form::text('role_name', Input::old('role_name')) }}
					</div> <!-- /controls -->
				</div> <!-- /control-group -->

				<div class="control-group">
					<label class="control-label" for="group_access">{{ $entry_permissions }}</label>

				</div> <!-- /control-group -->
			</fieldset>
        </div>
        <div class="span11">
            <table class="table table-condensed table-bordered" style="text-align: center;">
                <thead>
                    <tr>
                        <th class="align-center">{{ $col_module }}</th>
                        <th class="align-center">{{ $col_access }}</th>
                        <th class="align-center">{{ $col_insert }}</th>
                        <th class="align-center">{{ $col_update }}</th>
                        <th class="align-center">{{ $col_delete }}</th>
                        <th class="align-center">{{ $col_export }}</th>
                        <th class="align-center">{{ $col_password }}</th><!-- 
                        <th class="align-center">{{ $col_archive }}</th> -->
                        <th class="align-center">{{ $col_jda }}</th>
                        <th class="align-center">{{ $col_stock_piler }}</th>
                        <th class="align-center">{{ $col_closed_po }}</th>
                        <th class="align-center">{{ $col_closed_so }}</th>
                        <!-- <th class="align-center">{{ $col_generate_letdown }}</th> -->
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="align-center font-12">{{ $module_purchase_orders }}</td>
                        <td class="align-center"><input type="checkbox" class="checkbox" name="permissions[]" value="CanAccessPurchaseOrders" @if(CommonHelper::arrayHasValue(Input::old('permissions')) && CommonHelper::valueInArray('CanAccessPurchaseOrders', Input::old('permissions')) ) checked="checked" @endif /></td>
                        <td class="align-center">--</td>
                        <td class="align-center">--</td> 
                        <td class="align-center"><input type="checkbox" class="checkbox" name="permissions[]" value="CanExportPurchaseOrders" @if(CommonHelper::arrayHasValue(Input::old('permissions')) && CommonHelper::valueInArray('CanExportPurchaseOrders', Input::old('permissions')) ) checked="checked" @endif /></td>
                        <td class="align-center">--</td>
                        <td class="align-center">--</td>
                        <td class="align-center"><input type="checkbox" class="checkbox" name="permissions[]" value="CanSyncPurchaseOrders" @if(CommonHelper::arrayHasValue(Input::old('permissions')) && CommonHelper::valueInArray('CanSyncPurchaseOrders', Input::old('permissions')) ) checked="checked" @endif /></td>
                        <td class="align-center"><input type="checkbox" class="checkbox" name="permissions[]" value="CanAssignPurchaseOrders" @if(CommonHelper::arrayHasValue(Input::old('permissions')) && CommonHelper::valueInArray('CanAssignPurchaseOrders', Input::old('permissions')) ) checked="checked" @endif /></td>
                        <td class="align-center"><input type="checkbox" class="checkbox" name="permissions[]" value="CanClosePurchaseOrders" @if(CommonHelper::arrayHasValue(Input::old('permissions')) && CommonHelper::valueInArray('CanClosePurchaseOrders', Input::old('permissions')) ) checked="checked" @endif /></td>
                        <td class="align-center">--</td>
                    </tr>
                    <!-- <tr>
                        <td class="align-center font-12">{{ $module_picking }}</td>
                        <td class="align-center"><input type="checkbox" class="checkbox" name="permissions[]" value="CanAccessLetdown" @if(CommonHelper::arrayHasValue(Input::old('permissions')) && CommonHelper::valueInArray('CanAccessLetdown', Input::old('permissions')) ) checked="checked" @endif /></td>
                        <td class="align-center">--</td>
                        <td class="align-center">--</td>
                        <td class="align-center">--</td>
                        <td class="align-center"><input type="checkbox" class="checkbox" name="permissions[]" value="CanExportLetdown" @if(CommonHelper::arrayHasValue(Input::old('permissions')) && CommonHelper::valueInArray('CanExportLetdown', Input::old('permissions')) ) checked="checked" @endif /></td>
                        <td class="align-center">--</td>
                        <td class="align-center">--</td>
                        <td class="align-center">--</td>
                        <td class="align-center">--</td>
                        <td class="align-center">--</td>
                        <td class="align-center">--</td>
                    </tr> -->
                    <tr>
                        <td class="align-center font-12">{{ $module_packing }}</td>
                        <td class="align-center"><input type="checkbox" class="checkbox" name="permissions[]" value="CanAccessPacking" @if(CommonHelper::arrayHasValue(Input::old('permissions')) && CommonHelper::valueInArray('CanAccessPacking', Input::old('permissions')) ) checked="checked" @endif /></td>
                        <td class="align-center">--</td>
                        <td class="align-center">--</td> 
                        <td class="align-center"><input type="checkbox" class="checkbox" name="permissions[]" value="CanExportPacking" @if(CommonHelper::arrayHasValue(Input::old('permissions')) && CommonHelper::valueInArray('CanExportPacking', Input::old('permissions')) ) checked="checked" @endif /></td>
                        <td class="align-center">--</td>
                        <td class="align-center">--</td>
                        <td class="align-center">--</td>
                        <td class="align-center"><input type="checkbox" class="checkbox" name="permissions[]" value="CanAssignPacking" @if(CommonHelper::arrayHasValue(Input::old('permissions')) && CommonHelper::valueInArray('CanAssignPacking', Input::old('permissions')) ) checked="checked" @endif /></td>
                        <td class="align-center">--</td>
                        <td class="align-center">--</td>
                    </tr>
              <!--       <tr>
                        <td class="align-center font-12">{{ $module_boxing_loading }}</td>
                        <td class="align-center"><input type="checkbox" class="checkbox" name="permissions[]" value="CanAccessBoxingLoading" @if(CommonHelper::arrayHasValue(Input::old('permissions')) && CommonHelper::valueInArray('CanAccessBoxingLoading', Input::old('permissions')) ) checked="checked" @endif /></td>
                        <td class="align-center">--</td>
                        <td class="align-center">--</td>
                        <td class="align-center">--</td>
                        <td class="align-center"><input type="checkbox" class="checkbox" name="permissions[]" value="CanExportBoxingLoading" @if(CommonHelper::arrayHasValue(Input::old('permissions')) && CommonHelper::valueInArray('CanExportBoxingLoading', Input::old('permissions')) ) checked="checked" @endif /></td>
                        <td class="align-center">--</td>
                        <td class="align-center">--</td>
                        <td class="align-center">--</td>
                        <td class="align-center">--</td>
                        <td class="align-center">--</td>
                        <td class="align-center">--</td>
                    </tr> -->
                    <tr>
                        <td class="align-center font-12">{{ $module_shipping }}</td>
                        <td class="align-center"><input type="checkbox" class="checkbox" name="permissions[]" value="CanAccessShipping" @if(CommonHelper::arrayHasValue(Input::old('permissions')) && CommonHelper::valueInArray('CanAccessShipping', Input::old('permissions')) ) checked="checked" @endif /></td>
                        <td class="align-center">--</td>
                        <td class="align-center">--</td> 
                        <td class="align-center"><input type="checkbox" class="checkbox" name="permissions[]" value="CanExportShipping" @if(CommonHelper::arrayHasValue(Input::old('permissions')) && CommonHelper::valueInArray('CanExportShipping', Input::old('permissions')) ) checked="checked" @endif /></td>
                        <td class="align-center">--</td>
                        <td class="align-center">--</td>
                        <td class="align-center">--</td>
                        <td class="align-center">--</td>
                        <td class="align-center">--</td>
                        <td class="align-center">--</td>
                    </tr>
                    <!-- <tr>
                        <td class="align-center font-12">{{ $module_purchase_order_details }}</td>
                        <td class="align-center"><input type="checkbox" class="checkbox" name="permissions[]" value="CanAccessPurchaseOrderDetails" @if(CommonHelper::arrayHasValue(Input::old('permissions')) && CommonHelper::valueInArray('CanAccessPurchaseOrderDetails', Input::old('permissions')) ) checked="checked" @endif /></td>
                        <td class="align-center">--</td>
                        <td class="align-center">--</td>
                        <td class="align-center">--</td>
                        <td class="align-center"><input type="checkbox" class="checkbox" name="permissions[]" value="CanExportPurchaseOrderDetails" @if(CommonHelper::arrayHasValue(Input::old('permissions')) && CommonHelper::valueInArray('CanExportPurchaseOrderDetails', Input::old('permissions')) ) checked="checked" @endif /></td>
                        <td class="align-center">--</td>
                        <td class="align-center">--</td>
                        <td class="align-center"><input type="checkbox" class="checkbox" name="permissions[]" value="CanSyncPurchaseOrderDetails" @if(CommonHelper::arrayHasValue(Input::old('permissions')) && CommonHelper::valueInArray('CanSyncPurchaseOrderDetails', Input::old('permissions')) ) checked="checked" @endif /></td>
                        <td class="align-center"><input type="checkbox" class="checkbox" name="permissions[]" value="CanAssignPurchaseOrderDetails" @if(CommonHelper::arrayHasValue(Input::old('permissions')) && CommonHelper::valueInArray('CanAssignPurchaseOrderDetails', Input::old('permissions')) ) checked="checked" @endif /></td>
                        <td class="align-center"><input type="checkbox" class="checkbox" name="permissions[]" value="CanClosePurchaseOrderDetails" @if(CommonHelper::arrayHasValue(Input::old('permissions')) && CommonHelper::valueInArray('CanClosePurchaseOrderDetails', Input::old('permissions')) ) checked="checked" @endif /></td>
                        <td class="align-center">--</td>
                        <td class="align-center">--</td>
                    </tr> -->
                    <tr>
                        <td class="align-center font-12">{{ $module_store_orders }}</td>
                        <td class="align-center"><input type="checkbox" class="checkbox" name="permissions[]" value="CanAccessStoreOrders" @if(CommonHelper::arrayHasValue(Input::old('permissions')) && CommonHelper::valueInArray('CanAccessStoreOrders', Input::old('permissions')) ) checked="checked" @endif /></td>
                        <td class="align-center">--</td>
                        <td class="align-center">--</td> 
                        <td class="align-center"><input type="checkbox" class="checkbox" name="permissions[]" value="CanExportStoreOrders" @if(CommonHelper::arrayHasValue(Input::old('permissions')) && CommonHelper::valueInArray('CanExportStoreOrders', Input::old('permissions')) ) checked="checked" @endif /></td>
                        <td class="align-center">--</td>
                        <td class="align-center">--</td>
                        <td class="align-center"><input type="checkbox" class="checkbox" name="permissions[]" value="CanSyncStoreOrders" @if(CommonHelper::arrayHasValue(Input::old('permissions')) && CommonHelper::valueInArray('CanSyncStoreOrders', Input::old('permissions')) ) checked="checked" @endif /></td>
                        <td class="align-center">--</td>
                        <td class="align-center">--</td>
                        <td class="align-center">--</td>
                    </tr>
                  <!--   -->
                    <!-- <tr>
                        <td class="align-center font-12">{{ $module_store_order_details }}</td>
                        <td class="align-center"><input type="checkbox" class="checkbox" name="permissions[]" value="CanAccessStoreOrderDetails" @if(CommonHelper::arrayHasValue(Input::old('permissions')) && CommonHelper::valueInArray('CanAccessStoreOrderDetails', Input::old('permissions')) ) checked="checked" @endif /></td>
                        <td class="align-center">--</td>
                        <td class="align-center">--</td>
                        <td class="align-center">--</td>
                        <td class="align-center"><input type="checkbox" class="checkbox" name="permissions[]" value="CanExportStoreOrderDetails" @if(CommonHelper::arrayHasValue(Input::old('permissions')) && CommonHelper::valueInArray('CanExportStoreOrderDetails', Input::old('permissions')) ) checked="checked" @endif /></td>
                        <td class="align-center">--</td>
                        <td class="align-center">--</td>
                        <td class="align-center"><input type="checkbox" class="checkbox" name="permissions[]" value="CanSyncStoreOrderDetails" @if(CommonHelper::arrayHasValue(Input::old('permissions')) && CommonHelper::valueInArray('CanSyncStoreOrderDetails', Input::old('permissions')) ) checked="checked" @endif /></td>
                        <td class="align-center">
                        <td class="align-center">--</td>
                        <td class="align-center"><input type="checkbox" class="checkbox" name="permissions[]" value="CanCloseStoreOrderDetails" @if(CommonHelper::arrayHasValue(Input::old('permissions')) && CommonHelper::valueInArray('CanCloseStoreOrderDetails', Input::old('permissions')) ) checked="checked" @endif /></td>
                        <td class="align-center"><input type="checkbox" class="checkbox" name="permissions[]" value="CanGenerateLetdownStoreOrderDetails" @if(CommonHelper::arrayHasValue(Input::old('permissions')) && CommonHelper::valueInArray('CanGenerateLetdownStoreOrderDetails', Input::old('permissions')) ) checked="checked" @endif /></td>
                    </tr> -->
                    <!-- <tr>
                        <td class="align-center font-12">{{ $module_inventory }}</td>
                        <td class="align-center"><input type="checkbox" class="checkbox" name="permissions[]" value="CanAccessInventory" @if(CommonHelper::arrayHasValue(Input::old('permissions')) && CommonHelper::valueInArray('CanAccessInventory', Input::old('permissions')) ) checked="checked" @endif /></td>
                        <td class="align-center">--</td>
                        <td class="align-center">--</td>
                        <td class="align-center">--</td>
                        <td class="align-center"><input type="checkbox" class="checkbox" name="permissions[]" value="CanExportInventory" @if(CommonHelper::arrayHasValue(Input::old('permissions')) && CommonHelper::valueInArray('CanExportInventory', Input::old('permissions')) ) checked="checked" @endif /></td>
                        <td class="align-center">--</td>
                        <td class="align-center">--</td>
                        <td class="align-center"><input type="checkbox" class="checkbox" name="permissions[]" value="CanSyncInventory" @if(CommonHelper::arrayHasValue(Input::old('permissions')) && CommonHelper::valueInArray('CanSyncInventory', Input::old('permissions')) ) checked="checked" @endif /></td>
                        <td class="align-center">--</td>
                        <td class="align-center">--</td>
                        <td class="align-center">--</td>
                        <td class="align-center">--</td>
                    </tr>
                    <tr>
                        <td class="align-center font-12">{{ $module_inventory_details }}</td>
                        <td class="align-center"><input type="checkbox" class="checkbox" name="permissions[]" value="CanAccessInventoryDetails" @if(CommonHelper::arrayHasValue(Input::old('permissions')) && CommonHelper::valueInArray('CanAccessInventoryDetails', Input::old('permissions')) ) checked="checked" @endif /></td>
                        <td class="align-center">--</td>
                        <td class="align-center">--</td>
                        <td class="align-center">--</td>
                        <td class="align-center"><input type="checkbox" class="checkbox" name="permissions[]" value="CanExportInventoryDetails" @if(CommonHelper::arrayHasValue(Input::old('permissions')) && CommonHelper::valueInArray('CanExportInventoryDetails', Input::old('permissions')) ) checked="checked" @endif /></td>
                        <td class="align-center">--</td>
                        <td class="align-center">--</td>
                        <td class="align-center"><input type="checkbox" class="checkbox" name="permissions[]" value="CanSyncInventoryDetails" @if(CommonHelper::arrayHasValue(Input::old('permissions')) && CommonHelper::valueInArray('CanSyncInventoryDetails', Input::old('permissions')) ) checked="checked" @endif /></td>
                        <td class="align-center">--</td>
                        <td class="align-center">--</td>
                        <td class="align-center">--</td>
                        <td class="align-center">--</td>
                    </tr> -->
                    <tr>
                        <td class="align-center font-12">{{ $module_product_master_list }}</td>
                        <td class="align-center"><input type="checkbox" class="checkbox" name="permissions[]" value="CanAccessProductMasterList" @if(CommonHelper::arrayHasValue(Input::old('permissions')) && CommonHelper::valueInArray('CanAccessProductMasterList', Input::old('permissions')) ) checked="checked" @endif /></td>
                        <td class="align-center">--</td>
                        <td class="align-center">--</td>
                        <td class="align-center">--</td>
                        <td class="align-center"><input type="checkbox" class="checkbox" name="permissions[]" value="CanExportProductMasterList" @if(CommonHelper::arrayHasValue(Input::old('permissions')) && CommonHelper::valueInArray('CanExportProductMasterList', Input::old('permissions')) ) checked="checked" @endif /></td> 
                        <td class="align-center">--</td>
                        <td class="align-center">--</td>
                        <td class="align-center">--</td>
                        <td class="align-center">--</td>
                        <td class="align-center">--</td>
                    </tr>
                    <tr>
                        <td class="align-center font-12">{{ $module_store_master_list }}</td>
                        <td class="align-center"><input type="checkbox" class="checkbox" name="permissions[]" value="CanAccessStoreMasterList" @if(CommonHelper::arrayHasValue(Input::old('permissions')) && CommonHelper::valueInArray('CanAccessStoreMasterList', Input::old('permissions')) ) checked="checked" @endif /></td>
                        <td class="align-center">--</td>
                        <td class="align-center">--</td>
                        <td class="align-center">--</td>
                        <td class="align-center"><input type="checkbox" class="checkbox" name="permissions[]" value="CanExportStoreMasterList" @if(CommonHelper::arrayHasValue(Input::old('permissions')) && CommonHelper::valueInArray('CanExportStoreMasterList', Input::old('permissions')) ) checked="checked" @endif /></td>
                        <td class="align-center">--</td>
                        <td class="align-center">--</td> 
                        <td class="align-center">--</td>
                        <td class="align-center">--</td>
                        <td class="align-center">--</td>
                    </tr>
                <!--     <tr>
                        <td class="align-center font-12">{{ $module_slot_master_list }}</td>
                        <td class="align-center"><input type="checkbox" class="checkbox" name="permissions[]" value="CanAccessSlotMasterList" @if(CommonHelper::arrayHasValue(Input::old('permissions')) && CommonHelper::valueInArray('CanAccessSlotMasterList', Input::old('permissions')) ) checked="checked" @endif /></td>
                        <td class="align-center">--</td>
                        <td class="align-center">--</td>
                        <td class="align-center">--</td>
                        <td class="align-center"><input type="checkbox" class="checkbox" name="permissions[]" value="CanExportSlotMasterList" @if(CommonHelper::arrayHasValue(Input::old('permissions')) && CommonHelper::valueInArray('CanExportSlotMasterList', Input::old('permissions')) ) checked="checked" @endif /></td>
                        <td class="align-center">--</td>
                        <td class="align-center">--</td>
                        <td class="align-center">--</td>
                        <td class="align-center">--</td>
                        <td class="align-center">--</td>
                        <td class="align-center">--</td>
                    </tr> -->
          <!--           <tr>
                        <td class="align-center font-12">{{ $module_vendor_master_list }}</td>
                        <td class="align-center"><input type="checkbox" class="checkbox" name="permissions[]" value="CanAccessSlotMasterList" @if(CommonHelper::arrayHasValue(Input::old('permissions')) && CommonHelper::valueInArray('CanAccessVendorMasterList', Input::old('permissions')) ) checked="checked" @endif /></td>
                        <td class="align-center">--</td>
                        <td class="align-center">--</td>
                        <td class="align-center">--</td>
                        <td class="align-center"><input type="checkbox" class="checkbox" name="permissions[]" value="CanExportSlotMasterList" @if(CommonHelper::arrayHasValue(Input::old('permissions')) && CommonHelper::valueInArray('CanExportVendorMasterList', Input::old('permissions')) ) checked="checked" @endif /></td>
                        <td class="align-center">--</td>
                        <td class="align-center">--</td>
                        <td class="align-center">--</td>
                        <td class="align-center">--</td>
                        <td class="align-center">--</td>
                        <td class="align-center">--</td>
                    </tr> -->
              <!--       <tr>
                        <td class="align-center font-12">{{ $module_unlisted }}</td>
                        <td class="align-center"><input type="checkbox" class="checkbox" name="permissions[]" value="CanAccessUnlisted" @if(CommonHelper::arrayHasValue(Input::old('permissions')) && CommonHelper::valueInArray('CanAccessUnlisted', Input::old('permissions')) ) checked="checked" @endif /></td>
                        <td class="align-center">--</td>
                        <td class="align-center">--</td>
                        <td class="align-center">--</td>
                        <td class="align-center"><input type="checkbox" class="checkbox" name="permissions[]" value="CanExportUnlisted" @if(CommonHelper::arrayHasValue(Input::old('permissions')) && CommonHelper::valueInArray('CanExportUnlisted', Input::old('permissions')) ) checked="checked" @endif /></td>
                        <td class="align-center">--</td>
                        <td class="align-center">--</td>
                        <td class="align-center">--</td>
                        <td class="align-center">--</td>
                        <td class="align-center">--</td>
                        <td class="align-center">--</td>
                    </tr> -->

            <!--         <tr>
                        <td class="align-center font-12">{{ $module_expiry_items }}</td>
                        <td class="align-center"><input type="checkbox" class="checkbox" name="permissions[]" value="CanAccessExpiryItems" @if(CommonHelper::arrayHasValue(Input::old('permissions')) && CommonHelper::valueInArray('CanAccessExpiryItems', Input::old('permissions')) ) checked="checked" @endif /></td>
                        <td class="align-center">--</td>
                        <td class="align-center">--</td>
                        <td class="align-center">--</td>
                        <td class="align-center"><input type="checkbox" class="checkbox" name="permissions[]" value="CanExportExpiryItems" @if(CommonHelper::arrayHasValue(Input::old('permissions')) && CommonHelper::valueInArray('CanExportExpiryItems', Input::old('permissions')) ) checked="checked" @endif /></td>
                        <td class="align-center">--</td>
                        <td class="align-center">--</td>
                        <td class="align-center">--</td>
                        <td class="align-center">--</td>
                        <td class="align-center">--</td>
                        <td class="align-center">--</td>
                    </tr> -->
                    <tr>
                        <td class="align-center font-12">{{ $module_users }}</td>
                        <td class="align-center"><input type="checkbox" class="checkbox" name="permissions[]" value="CanAccessUsers" @if(CommonHelper::arrayHasValue(Input::old('permissions')) && CommonHelper::valueInArray('CanAccessUsers', Input::old('permissions')) ) checked="checked" @endif /></td>
                        <td class="align-center"><input type="checkbox" class="checkbox" name="permissions[]" value="CanInsertUsers" @if(CommonHelper::arrayHasValue(Input::old('permissions')) && CommonHelper::valueInArray('CanInsertUsers', Input::old('permissions')) ) checked="checked" @endif /></td>
                        <td class="align-center"><input type="checkbox" class="checkbox" name="permissions[]" value="CanUpdateUsers" @if(CommonHelper::arrayHasValue(Input::old('permissions')) && CommonHelper::valueInArray('CanUpdateUsers', Input::old('permissions')) ) checked="checked" @endif /></td>
                        <td class="align-center"><input type="checkbox" class="checkbox" name="permissions[]" value="CanDeleteUsers" @if(CommonHelper::arrayHasValue(Input::old('permissions')) && CommonHelper::valueInArray('CanDeleteUsers', Input::old('permissions')) ) checked="checked" @endif /></td>
                        <td class="align-center"><input type="checkbox" class="checkbox" name="permissions[]" value="CanExportUsers" @if(CommonHelper::arrayHasValue(Input::old('permissions')) && CommonHelper::valueInArray('CanExportUsers', Input::old('permissions')) ) checked="checked" @endif /></td>
                        <td class="align-center"><input type="checkbox" class="checkbox" name="permissions[]" value="CanChangePasswordUsers" @if(CommonHelper::arrayHasValue(Input::old('permissions')) && CommonHelper::valueInArray('CanChangePasswordUsers', Input::old('permissions')) ) checked="checked" @endif /></td>
                        <td class="align-center">--</td>
                        <td class="align-center">--</td> 
                        <td class="align-center">--</td>
                        <td class="align-center">--</td>
                    </tr>
                    <tr>
                        <td class="align-center font-12">{{ $module_user_roles }}</td>
                        <td class="align-center"><input type="checkbox" class="checkbox" name="permissions[]" value="CanAccessUserRoles" @if(CommonHelper::arrayHasValue(Input::old('permissions')) && CommonHelper::valueInArray('CanAccessUserRoles', Input::old('permissions')) ) checked="checked" @endif /></td>
                        <td class="align-center"><input type="checkbox" class="checkbox" name="permissions[]" value="CanInsertUserRoles" @if(CommonHelper::arrayHasValue(Input::old('permissions')) && CommonHelper::valueInArray('CanInsertUserRoles', Input::old('permissions')) ) checked="checked" @endif /></td>
                        <td class="align-center"><input type="checkbox" class="checkbox" name="permissions[]" value="CanUpdateUserRoles" @if(CommonHelper::arrayHasValue(Input::old('permissions')) && CommonHelper::valueInArray('CanUpdateUserRoles', Input::old('permissions')) ) checked="checked" @endif /></td>
                        <td class="align-center"><input type="checkbox" class="checkbox" name="permissions[]" value="CanDeleteUserRoles" @if(CommonHelper::arrayHasValue(Input::old('permissions')) && CommonHelper::valueInArray('CanDeleteUserRoles', Input::old('permissions')) ) checked="checked" @endif /></td>
                        <td class="align-center">--</td>
                        <td class="align-center">--</td>
                        <td class="align-center">--</td>
                        <td class="align-center">--</td>
                        <td class="align-center">--</td> 
                        <td class="align-center">--</td>
                    </tr>
                    <tr>
                        <td class="align-center font-12">{{ $module_audit_trail }}</td>
                        <td class="align-center"><input type="checkbox" class="checkbox" name="permissions[]" value="CanAccessAuditTrail" @if(CommonHelper::arrayHasValue(Input::old('permissions')) && CommonHelper::valueInArray('CanAccessAuditTrail', Input::old('permissions')) ) checked="checked" @endif /></td>
                        <td class="align-center">--</td>
                        <td class="align-center">--</td>
                        <td class="align-center">--</td>
                        <td class="align-center"><input type="checkbox" class="checkbox" name="permissions[]" value="CanExportAuditTrail" @if(CommonHelper::arrayHasValue(Input::old('permissions')) && CommonHelper::valueInArray('CanExportAuditTrail', Input::old('permissions')) ) checked="checked" @endif /></td>
                        <td class="align-center">--</td>
                        <td class="align-center"><input type="checkbox" class="checkbox" name="permissions[]" value="CanArchiveAuditTrail" @if(CommonHelper::arrayHasValue(Input::old('permissions')) && CommonHelper::valueInArray('CanArchiveAuditTrail', Input::old('permissions')) ) checked="checked" @endif /></td>
                        <td class="align-center">--</td>
                        <td class="align-center">--</td> 
                        <td class="align-center">--</td>
                    </tr>
                    <!-- <tr>
                        <td class="align-center font-12">{{ $module_settings }}</td>
                        <td class="align-center"><input type="checkbox" class="checkbox" name="permissions[]" value="CanAccessSettings" @if(CommonHelper::arrayHasValue(Input::old('permissions')) && CommonHelper::valueInArray('CanAccessSettings', Input::old('permissions')) ) checked="checked" @endif /></td>
                        <td class="align-center"><input type="checkbox" class="checkbox" name="permissions[]" value="CanInsertSettings" @if(CommonHelper::arrayHasValue(Input::old('permissions')) && CommonHelper::valueInArray('CanInsertSettings', Input::old('permissions')) ) checked="checked" @endif /></td>
                        <td class="align-center"><input type="checkbox" class="checkbox" name="permissions[]" value="CanUpdateSettings" @if(CommonHelper::arrayHasValue(Input::old('permissions')) && CommonHelper::valueInArray('CanUpdateSettings', Input::old('permissions')) ) checked="checked" @endif /></td>
                        <td class="align-center"><input type="checkbox" class="checkbox" name="permissions[]" value="CanDeleteSettings" @if(CommonHelper::arrayHasValue(Input::old('permissions')) && CommonHelper::valueInArray('CanDeleteSettings', Input::old('permissions')) ) checked="checked" @endif /></td>
                        <td class="align-center">--</td>
                        <td class="align-center">--</td>
                        <td class="align-center">--</td>
                        <td class="align-center">--</td>
                        <td class="align-center">--</td>
                        <td class="align-center">--</td>
                        <td class="align-center">--</td>
                    </tr> -->
                    <!-- <tr>
                        <td class="align-center font-12">{{ $module_stock_piler }}</td>
                        <td class="align-center"><input type="checkbox" class="checkbox" name="permissions[]" value="CanAccessStockPiler" @if(CommonHelper::arrayHasValue(Input::old('permissions')) && CommonHelper::valueInArray('CanAccessStockPiler', Input::old('permissions')) ) checked="checked" @endif /></td>
                        <td class="align-center">--</td>
                        <td class="align-center">--</td>
                        <td class="align-center">--</td>
                        <td class="align-center">--</td>
                        <td class="align-center">--</td>
                        <td class="align-center">--</td>
                        <td class="align-center">--</td>
                        <td class="align-center">--</td>
                        <td class="align-center">--</td>
                        <td class="align-center">--</td>
                        <td class="align-center">--</td>
                    </tr> -->
                </tbody>
            </table>

            <a class="btn btn-info" id="submitForm">{{ $button_submit }}</a>
            <a class="btn" href="{{ $url_cancel }}">{{ $button_cancel }}</a>
        </div> <!-- /controls -->
        {{ Form::hidden('filter_role_name', $filter_role_name) }}
        {{ Form::hidden('sort', $sort) }}
        {{ Form::hidden('order', $order) }}

		{{ Form::close() }}
	</div>
</div>

<script type="text/javascript">
$(document).ready(function() {
    // Submit Form
    $('#submitForm').click(function() {
    	$('#form-user-roles').submit();
    });

    $('#form-user-roles input').keydown(function(e) {
		if (e.keyCode == 13) {
			$('#form-user-roles').submit();
		}
	});
});
</script>