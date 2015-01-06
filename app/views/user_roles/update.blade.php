@if( $errors->all() )
    <div class="alert alert-error">
    	<button class="close" data-dismiss="alert" type="button">&times;</button>
    	{{ HTML::ul($errors->all()) }}
    </div>
@endif

<div class="widget">
    <div class="widget-header"> <i class="icon-group"></i>
    	<h3>{{ $heading_title_update }}</h3>
    </div>
    <!-- /widget-header -->
    
    <div class="widget-content">
		{{ Form::model($user_role, array('url'=>'user_roles/updateData', 'class'=>'form-horizontal', 'id'=>'form-user-roles', 'role'=>'form', 'method' => 'post')) }}
		<div class="span11">
			<fieldset>
				<div class="control-group">											
					<label class="control-label" for="role_name">{{ $entry_role_name }}</label>
					<div class="controls">
						{{ Form::text('role_name', null) }}
					</div> <!-- /controls -->				
				</div> <!-- /control-group -->
								
				<div class="control-group">											
					<label class="control-label" for="group_access">{{ $entry_permissions }}</label>
					<div class="controls">
						<table class="table table-condensed table-bordered" style="text-align: center;">
							<thead>
								<tr>
									<th class="align-center">{{ $col_module }}</th>
									<th class="align-center">{{ $col_access }}</th>
									<th class="align-center">{{ $col_insert }}</th>
                                    <th class="align-center">{{ $col_update }}</th>
                                    <th class="align-center">{{ $col_delete }}</th>
									<th class="align-center">{{ $col_export }}</th>
									<th class="align-center">{{ $col_password }}</th>
									<th class="align-center">{{ $col_archive }}</th>
									<th class="align-center">{{ $col_jda }}</th>
									<th class="align-center">{{ $col_stock_piler }}</th>
									<th class="align-center">{{ $col_closed_po }}</th>
									<th class="align-center">{{ $col_closed_so }}</th>
                                    <th class="align-center">{{ $col_generate_letdown }}</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td class="align-center font-12">{{ $module_purchase_orders }}</td>
									<td class="align-center">
										@if((CommonHelper::arrayHasValue(Input::old('permissions')) && CommonHelper::valueInArray('CanAccessPurchaseOrders', Input::old('permissions'))) || CommonHelper::valueInArray('CanAccessPurchaseOrders', json_decode($user_role->permissions)))
											<input type="checkbox" class="checkbox" name="permissions[]" value="CanAccessPurchaseOrders" checked="checked" />
										@else
											<input type="checkbox" class="checkbox" name="permissions[]" value="CanAccessPurchaseOrders" />
										@endif
									</td>
									<td class="align-center">--</td>
									<td class="align-center">--</td>
									<td class="align-center">--</td>
									<td class="align-center">
										@if((CommonHelper::arrayHasValue(Input::old('permissions')) && CommonHelper::valueInArray('CanExportPurchaseOrders', Input::old('permissions'))) || CommonHelper::valueInArray('CanExportPurchaseOrders', json_decode($user_role->permissions)))
											<input type="checkbox" class="checkbox" name="permissions[]" value="CanExportPurchaseOrders" checked="checked" />
										@else
											<input type="checkbox" class="checkbox" name="permissions[]" value="CanExportPurchaseOrders" />
										@endif
									</td>
									<td class="align-center">--</td>
									<td class="align-center">--</td>
                                	<td class="align-center">
										@if((CommonHelper::arrayHasValue(Input::old('permissions')) && CommonHelper::valueInArray('CanSyncPurchaseOrders', Input::old('permissions'))) || CommonHelper::valueInArray('CanSyncPurchaseOrders', json_decode($user_role->permissions)))
											<input type="checkbox" class="checkbox" name="permissions[]" value="CanSyncPurchaseOrders" checked="checked" />
										@else
											<input type="checkbox" class="checkbox" name="permissions[]" value="CanSyncPurchaseOrders" />
										@endif
									</td>
									<td class="align-center">
										@if((CommonHelper::arrayHasValue(Input::old('permissions')) && CommonHelper::valueInArray('CanAssignPurchaseOrders', Input::old('permissions'))) || CommonHelper::valueInArray('CanAssignPurchaseOrders', json_decode($user_role->permissions)))
											<input type="checkbox" class="checkbox" name="permissions[]" value="CanAssignPurchaseOrders" checked="checked" />
										@else
											<input type="checkbox" class="checkbox" name="permissions[]" value="CanAssignPurchaseOrders" />
										@endif
									</td>
									<td class="align-center">
										@if((CommonHelper::arrayHasValue(Input::old('permissions')) && CommonHelper::valueInArray('CanClosePurchaseOrders', Input::old('permissions'))) || CommonHelper::valueInArray('CanClosePurchaseOrders', json_decode($user_role->permissions)))
											<input type="checkbox" class="checkbox" name="permissions[]" value="CanClosePurchaseOrders" checked="checked" />
										@else
											<input type="checkbox" class="checkbox" name="permissions[]" value="CanClosePurchaseOrders" />
										@endif
									</td>
									<td class="align-center">--</td>
									<td class="align-center">--</td>
                                </tr>

                                <tr>
									<td class="align-center font-12">{{ $module_purchase_order_details }}</td>
									<td class="align-center">
										@if((CommonHelper::arrayHasValue(Input::old('permissions')) && CommonHelper::valueInArray('CanAccessPurchaseOrderDetails', Input::old('permissions'))) || CommonHelper::valueInArray('CanAccessPurchaseOrderDetails', json_decode($user_role->permissions)))
											<input type="checkbox" class="checkbox" name="permissions[]" value="CanAccessPurchaseOrderDetails" checked="checked" />
										@else
											<input type="checkbox" class="checkbox" name="permissions[]" value="CanAccessPurchaseOrderDetails" />
										@endif
									</td>
									<td class="align-center">--</td>
									<td class="align-center">--</td>
									<td class="align-center">--</td>
									<td class="align-center">
										@if((CommonHelper::arrayHasValue(Input::old('permissions')) && CommonHelper::valueInArray('CanExportPurchaseOrderDetails', Input::old('permissions'))) || CommonHelper::valueInArray('CanExportPurchaseOrderDetails', json_decode($user_role->permissions)))
											<input type="checkbox" class="checkbox" name="permissions[]" value="CanExportPurchaseOrderDetails" checked="checked" />
										@else
											<input type="checkbox" class="checkbox" name="permissions[]" value="CanExportPurchaseOrderDetails" />
										@endif
									</td>
									<td class="align-center">--</td>
									<td class="align-center">--</td>
                                	<td class="align-center">
										@if((CommonHelper::arrayHasValue(Input::old('permissions')) && CommonHelper::valueInArray('CanSyncPurchaseOrderDetails', Input::old('permissions'))) || CommonHelper::valueInArray('CanSyncPurchaseOrderDetails', json_decode($user_role->permissions)))
											<input type="checkbox" class="checkbox" name="permissions[]" value="CanSyncPurchaseOrderDetails" checked="checked" />
										@else
											<input type="checkbox" class="checkbox" name="permissions[]" value="CanSyncPurchaseOrderDetails" />
										@endif
									</td>
									<td class="align-center">
										@if((CommonHelper::arrayHasValue(Input::old('permissions')) && CommonHelper::valueInArray('CanAssignPurchaseOrderDetails', Input::old('permissions'))) || CommonHelper::valueInArray('CanAssignPurchaseOrderDetails', json_decode($user_role->permissions)))
											<input type="checkbox" class="checkbox" name="permissions[]" value="CanAssignPurchaseOrderDetails" checked="checked" />
										@else
											<input type="checkbox" class="checkbox" name="permissions[]" value="CanAssignPurchaseOrderDetails" />
										@endif
									</td>
									<td class="align-center">
										@if((CommonHelper::arrayHasValue(Input::old('permissions')) && CommonHelper::valueInArray('CanClosePurchaseOrderDetails', Input::old('permissions'))) || CommonHelper::valueInArray('CanClosePurchaseOrderDetails', json_decode($user_role->permissions)))
											<input type="checkbox" class="checkbox" name="permissions[]" value="CanClosePurchaseOrderDetails" checked="checked" />
										@else
											<input type="checkbox" class="checkbox" name="permissions[]" value="CanClosePurchaseOrderDetails" />
										@endif
									</td>
									<td class="align-center">--</td>
									<td class="align-center">--</td>
                                </tr>
                                
                                <tr>
									<td class="align-center font-12">{{ $module_store_orders }}</td>
									<td class="align-center">
										@if((CommonHelper::arrayHasValue(Input::old('permissions')) && CommonHelper::valueInArray('CanAccessStoreOrders', Input::old('permissions'))) || CommonHelper::valueInArray('CanAccessStoreOrders', json_decode($user_role->permissions)))
											<input type="checkbox" class="checkbox" name="permissions[]" value="CanAccessStoreOrders" checked="checked" />
										@else
											<input type="checkbox" class="checkbox" name="permissions[]" value="CanAccessStoreOrders" />
										@endif
									</td>
									<td class="align-center">--</td>
									<td class="align-center">--</td>
									<td class="align-center">--</td>
									<td class="align-center">
										@if((CommonHelper::arrayHasValue(Input::old('permissions')) && CommonHelper::valueInArray('CanExportStoreOrders', Input::old('permissions'))) || CommonHelper::valueInArray('CanExportStoreOrders', json_decode($user_role->permissions)))
											<input type="checkbox" class="checkbox" name="permissions[]" value="CanExportStoreOrders" checked="checked" />
										@else
											<input type="checkbox" class="checkbox" name="permissions[]" value="CanExportStoreOrders" />
										@endif
									</td>
									<td class="align-center">--</td>
									<td class="align-center">--</td>
                                	<td class="align-center">
										@if((CommonHelper::arrayHasValue(Input::old('permissions')) && CommonHelper::valueInArray('CanSyncStoreOrders', Input::old('permissions'))) || CommonHelper::valueInArray('CanSyncStoreOrders', json_decode($user_role->permissions)))
											<input type="checkbox" class="checkbox" name="permissions[]" value="CanSyncStoreOrders" checked="checked" />
										@else
											<input type="checkbox" class="checkbox" name="permissions[]" value="CanSyncStoreOrders" />
										@endif
									</td>
									<td class="align-center">--</td>
									<td class="align-center">--</td>
									<td class="align-center">
										@if((CommonHelper::arrayHasValue(Input::old('permissions')) && CommonHelper::valueInArray('CanCloseStoreOrders', Input::old('permissions'))) || CommonHelper::valueInArray('CanCloseStoreOrders', json_decode($user_role->permissions)))
											<input type="checkbox" class="checkbox" name="permissions[]" value="CanCloseStoreOrders" checked="checked" />
										@else
											<input type="checkbox" class="checkbox" name="permissions[]" value="CanCloseStoreOrders" />
										@endif
									</td>
									<td class="align-center">
										@if((CommonHelper::arrayHasValue(Input::old('permissions')) && CommonHelper::valueInArray('CanGenerateLetdownStoreOrders', Input::old('permissions'))) || CommonHelper::valueInArray('CanGenerateLetdownStoreOrders', json_decode($user_role->permissions)))
											<input type="checkbox" class="checkbox" name="permissions[]" value="CanGenerateLetdownStoreOrders" checked="checked" />
										@else
											<input type="checkbox" class="checkbox" name="permissions[]" value="CanGenerateLetdownStoreOrders" />
										@endif
									</td>
                                </tr>

                                <tr>
									<td class="align-center font-12">{{ $module_store_order_details }}</td>
									<td class="align-center">
										@if((CommonHelper::arrayHasValue(Input::old('permissions')) && CommonHelper::valueInArray('CanAccessStoreOrderDetails', Input::old('permissions'))) || CommonHelper::valueInArray('CanAccessStoreOrderDetails', json_decode($user_role->permissions)))
											<input type="checkbox" class="checkbox" name="permissions[]" value="CanAccessStoreOrderDetails" checked="checked" />
										@else
											<input type="checkbox" class="checkbox" name="permissions[]" value="CanAccessStoreOrderDetails" />
										@endif
									</td>
									<td class="align-center">--</td>
									<td class="align-center">--</td>
									<td class="align-center">--</td>
									<td class="align-center">
										@if((CommonHelper::arrayHasValue(Input::old('permissions')) && CommonHelper::valueInArray('CanExportStoreOrderDetails', Input::old('permissions'))) || CommonHelper::valueInArray('CanExportStoreOrderDetails', json_decode($user_role->permissions)))
											<input type="checkbox" class="checkbox" name="permissions[]" value="CanExportStoreOrderDetails" checked="checked" />
										@else
											<input type="checkbox" class="checkbox" name="permissions[]" value="CanExportStoreOrderDetails" />
										@endif
									</td>
									<td class="align-center">--</td>
									<td class="align-center">--</td>
                                	<td class="align-center">
										@if((CommonHelper::arrayHasValue(Input::old('permissions')) && CommonHelper::valueInArray('CanSyncStoreOrderDetails', Input::old('permissions'))) || CommonHelper::valueInArray('CanSyncStoreOrderDetails', json_decode($user_role->permissions)))
											<input type="checkbox" class="checkbox" name="permissions[]" value="CanSyncStoreOrderDetails" checked="checked" />
										@else
											<input type="checkbox" class="checkbox" name="permissions[]" value="CanSyncStoreOrderDetails" />
										@endif
									</td>
									<td class="align-center">--</td>
									<td class="align-center">--</td>
									<td class="align-center">
										@if((CommonHelper::arrayHasValue(Input::old('permissions')) && CommonHelper::valueInArray('CanCloseStoreOrderDetails', Input::old('permissions'))) || CommonHelper::valueInArray('CanCloseStoreOrderDetails', json_decode($user_role->permissions)))
											<input type="checkbox" class="checkbox" name="permissions[]" value="CanCloseStoreOrderDetails" checked="checked" />
										@else
											<input type="checkbox" class="checkbox" name="permissions[]" value="CanCloseStoreOrderDetails" />
										@endif
									</td>
									<td class="align-center">
										@if((CommonHelper::arrayHasValue(Input::old('permissions')) && CommonHelper::valueInArray('CanGenerateLetdownStoreOrderDetails', Input::old('permissions'))) || CommonHelper::valueInArray('CanGenerateLetdownStoreOrderDetails', json_decode($user_role->permissions)))
											<input type="checkbox" class="checkbox" name="permissions[]" value="CanGenerateLetdownStoreOrderDetails" checked="checked" />
										@else
											<input type="checkbox" class="checkbox" name="permissions[]" value="CanGenerateLetdownStoreOrderDetails" />
										@endif
									</td>
                                </tr>

                                <tr>
									<td class="align-center font-12">{{ $module_inventory }}</td>
									<td class="align-center">
										@if((CommonHelper::arrayHasValue(Input::old('permissions')) && CommonHelper::valueInArray('CanAccessInventory', Input::old('permissions'))) || CommonHelper::valueInArray('CanAccessInventory', json_decode($user_role->permissions)))
											<input type="checkbox" class="checkbox" name="permissions[]" value="CanAccessInventory" checked="checked" />
										@else
											<input type="checkbox" class="checkbox" name="permissions[]" value="CanAccessInventory" />
										@endif
									</td>
									<td class="align-center">--</td>
									<td class="align-center">--</td>
									<td class="align-center">--</td>
									<td class="align-center">
										@if((CommonHelper::arrayHasValue(Input::old('permissions')) && CommonHelper::valueInArray('CanExportInventory', Input::old('permissions'))) || CommonHelper::valueInArray('CanExportInventory', json_decode($user_role->permissions)))
											<input type="checkbox" class="checkbox" name="permissions[]" value="CanExportInventory" checked="checked" />
										@else
											<input type="checkbox" class="checkbox" name="permissions[]" value="CanExportInventory" />
										@endif
									</td>
									<td class="align-center">--</td>
									<td class="align-center">--</td>
                                	<td class="align-center">
										@if((CommonHelper::arrayHasValue(Input::old('permissions')) && CommonHelper::valueInArray('CanSyncInventory', Input::old('permissions'))) || CommonHelper::valueInArray('CanSyncInventory', json_decode($user_role->permissions)))
											<input type="checkbox" class="checkbox" name="permissions[]" value="CanSyncInventory" checked="checked" />
										@else
											<input type="checkbox" class="checkbox" name="permissions[]" value="CanSyncInventory" />
										@endif
									</td>
									<td class="align-center">--</td>
									<td class="align-center">--</td>
									<td class="align-center">--</td>
									<td class="align-center">--</td>
                                </tr>
                                
                                <tr>
									<td class="align-center font-12">{{ $module_inventory_details }}</td>
									<td class="align-center">
										@if((CommonHelper::arrayHasValue(Input::old('permissions')) && CommonHelper::valueInArray('CanAccessInventoryDetails', Input::old('permissions'))) || CommonHelper::valueInArray('CanAccessInventoryDetails', json_decode($user_role->permissions)))
											<input type="checkbox" class="checkbox" name="permissions[]" value="CanAccessInventoryDetails" checked="checked" />
										@else
											<input type="checkbox" class="checkbox" name="permissions[]" value="CanAccessInventoryDetails" />
										@endif
									</td>
									<td class="align-center">--</td>
									<td class="align-center">--</td>
									<td class="align-center">--</td>
									<td class="align-center">
										@if((CommonHelper::arrayHasValue(Input::old('permissions')) && CommonHelper::valueInArray('CanExportInventoryDetails', Input::old('permissions'))) || CommonHelper::valueInArray('CanExportInventoryDetails', json_decode($user_role->permissions)))
											<input type="checkbox" class="checkbox" name="permissions[]" value="CanExportInventoryDetails" checked="checked" />
										@else
											<input type="checkbox" class="checkbox" name="permissions[]" value="CanExportInventoryDetails" />
										@endif
									</td>
									<td class="align-center">--</td>
									<td class="align-center">--</td>
                                	<td class="align-center">
										@if((CommonHelper::arrayHasValue(Input::old('permissions')) && CommonHelper::valueInArray('CanSyncInventoryDetails', Input::old('permissions'))) || CommonHelper::valueInArray('CanSyncInventoryDetails', json_decode($user_role->permissions)))
											<input type="checkbox" class="checkbox" name="permissions[]" value="CanSyncInventoryDetails" checked="checked" />
										@else
											<input type="checkbox" class="checkbox" name="permissions[]" value="CanSyncInventoryDetails" />
										@endif
									</td>
									<td class="align-center">--</td>
									<td class="align-center">--</td>
									<td class="align-center">--</td>
									<td class="align-center">--</td>
                                </tr>
                                
                                <tr>
									<td class="align-center font-12">{{ $module_product_master_list }}</td>
									<td class="align-center">
										@if((CommonHelper::arrayHasValue(Input::old('permissions')) && CommonHelper::valueInArray('CanAccessProductMasterList', Input::old('permissions'))) || CommonHelper::valueInArray('CanAccessProductMasterList', json_decode($user_role->permissions)))
											<input type="checkbox" class="checkbox" name="permissions[]" value="CanAccessProductMasterList" checked="checked" />
										@else
											<input type="checkbox" class="checkbox" name="permissions[]" value="CanAccessProductMasterList" />
										@endif
									</td>
									<td class="align-center">--</td>
									<td class="align-center">--</td>
									<td class="align-center">--</td>
									<td class="align-center">
										@if((CommonHelper::arrayHasValue(Input::old('permissions')) && CommonHelper::valueInArray('CanExportProductMasterList', Input::old('permissions'))) || CommonHelper::valueInArray('CanExportProductMasterList', json_decode($user_role->permissions)))
											<input type="checkbox" class="checkbox" name="permissions[]" value="CanExportProductMasterList" checked="checked" />
										@else
											<input type="checkbox" class="checkbox" name="permissions[]" value="CanExportProductMasterList" />
										@endif
									</td>
									<td class="align-center">--</td>
									<td class="align-center">--</td>
                                	<td class="align-center">--</td>
									<td class="align-center">--</td>
									<td class="align-center">--</td>
									<td class="align-center">--</td>
									<td class="align-center">--</td>
                                </tr>
                                
                                <tr>
									<td class="align-center font-12">{{ $module_slot_master_list }}</td>
									<td class="align-center">
										@if((CommonHelper::arrayHasValue(Input::old('permissions')) && CommonHelper::valueInArray('CanAccessSlotMasterList', Input::old('permissions'))) || CommonHelper::valueInArray('CanAccessSlotMasterList', json_decode($user_role->permissions)))
											<input type="checkbox" class="checkbox" name="permissions[]" value="CanAccessSlotMasterList" checked="checked" />
										@else
											<input type="checkbox" class="checkbox" name="permissions[]" value="CanAccessSlotMasterList" />
										@endif
									</td>
									<td class="align-center">--</td>
									<td class="align-center">--</td>
									<td class="align-center">--</td>
									<td class="align-center">
										@if((CommonHelper::arrayHasValue(Input::old('permissions')) && CommonHelper::valueInArray('CanExportSlotMasterList', Input::old('permissions'))) || CommonHelper::valueInArray('CanExportSlotMasterList', json_decode($user_role->permissions)))
											<input type="checkbox" class="checkbox" name="permissions[]" value="CanExportSlotMasterList" checked="checked" />
										@else
											<input type="checkbox" class="checkbox" name="permissions[]" value="CanExportSlotMasterList" />
										@endif
									</td>
									<td class="align-center">--</td>
									<td class="align-center">--</td>
                                	<td class="align-center">--</td>
									<td class="align-center">--</td>
									<td class="align-center">--</td>
									<td class="align-center">--</td>
									<td class="align-center">--</td>
                                </tr>
								
								<tr>
									<td class="align-center font-12">{{ $module_vendor_master_list }}</td>
									<td class="align-center">
										@if((CommonHelper::arrayHasValue(Input::old('permissions')) && CommonHelper::valueInArray('CanAccessSlotMasterList', Input::old('permissions'))) || CommonHelper::valueInArray('CanAccessVendorMasterList', json_decode($user_role->permissions)))
											<input type="checkbox" class="checkbox" name="permissions[]" value="CanAccessVendorMasterList" checked="checked" />
										@else
											<input type="checkbox" class="checkbox" name="permissions[]" value="CanAccessVendorMasterList" />
										@endif
									</td>
									<td class="align-center">--</td>
									<td class="align-center">--</td>
									<td class="align-center">--</td>
									<td class="align-center">
										@if((CommonHelper::arrayHasValue(Input::old('permissions')) && CommonHelper::valueInArray('CanExportVendorMasterList', Input::old('permissions'))) || CommonHelper::valueInArray('CanExportVendorMasterList', json_decode($user_role->permissions)))
											<input type="checkbox" class="checkbox" name="permissions[]" value="CanExportVendorMasterList" checked="checked" />
										@else
											<input type="checkbox" class="checkbox" name="permissions[]" value="CanExportVendorMasterList" />
										@endif
									</td>
									<td class="align-center">--</td>
									<td class="align-center">--</td>
                                	<td class="align-center">--</td>
									<td class="align-center">--</td>
									<td class="align-center">--</td>
									<td class="align-center">--</td>
									<td class="align-center">--</td>
                                </tr>

								<tr>
									<td class="align-center font-12">{{ $module_users }}</td>
									<td class="align-center">
										@if((CommonHelper::arrayHasValue(Input::old('permissions')) && CommonHelper::valueInArray('CanAccessUsers', Input::old('permissions'))) || CommonHelper::valueInArray('CanAccessUsers', json_decode($user_role->permissions)))
											<input type="checkbox" class="checkbox" name="permissions[]" value="CanAccessUsers" checked="checked" />
										@else
											<input type="checkbox" class="checkbox" name="permissions[]" value="CanAccessUsers" />
										@endif
									</td>
									<td class="align-center">
										@if((CommonHelper::arrayHasValue(Input::old('permissions')) && CommonHelper::valueInArray('CanInsertUsers', Input::old('permissions'))) || CommonHelper::valueInArray('CanInsertUsers', json_decode($user_role->permissions)))
											<input type="checkbox" class="checkbox" name="permissions[]" value="CanInsertUsers" checked="checked" />
										@else
											<input type="checkbox" class="checkbox" name="permissions[]" value="CanInsertUsers" />
										@endif
									</td>
									<td class="align-center">
										@if((CommonHelper::arrayHasValue(Input::old('permissions')) && CommonHelper::valueInArray('CanUpdateUsers', Input::old('permissions'))) || CommonHelper::valueInArray('CanUpdateUsers', json_decode($user_role->permissions)))
											<input type="checkbox" class="checkbox" name="permissions[]" value="CanUpdateUsers" checked="checked" />
										@else
											<input type="checkbox" class="checkbox" name="permissions[]" value="CanUpdateUsers" />
										@endif
									</td>
									<td class="align-center">
										@if((CommonHelper::arrayHasValue(Input::old('permissions')) && CommonHelper::valueInArray('CanDeleteUsers', Input::old('permissions'))) || CommonHelper::valueInArray('CanDeleteUsers', json_decode($user_role->permissions)))
											<input type="checkbox" class="checkbox" name="permissions[]" value="CanDeleteUsers" checked="checked" />
										@else
											<input type="checkbox" class="checkbox" name="permissions[]" value="CanDeleteUsers" />
										@endif
									</td>
									<td class="align-center">
										@if((CommonHelper::arrayHasValue(Input::old('permissions')) && CommonHelper::valueInArray('CanExportUsers', Input::old('permissions'))) || CommonHelper::valueInArray('CanExportUsers', json_decode($user_role->permissions)))
											<input type="checkbox" class="checkbox" name="permissions[]" value="CanExportUsers" checked="checked" />
										@else
											<input type="checkbox" class="checkbox" name="permissions[]" value="CanExportUsers" />
										@endif
									</td>
									<td class="align-center">
										@if((CommonHelper::arrayHasValue(Input::old('permissions')) && CommonHelper::valueInArray('CanChangePasswordUsers', Input::old('permissions'))) || CommonHelper::valueInArray('CanChangePasswordUsers', json_decode($user_role->permissions)))
											<input type="checkbox" class="checkbox" name="permissions[]" value="CanChangePasswordUsers" checked="checked" />
										@else
											<input type="checkbox" class="checkbox" name="permissions[]" value="CanChangePasswordUsers" />
										@endif
									</td>
									<td class="align-center">--</td>
                                	<td class="align-center">--</td>
                                	<td class="align-center">--</td>
                                	<td class="align-center">--</td>
                                	<td class="align-center">--</td>
									<td class="align-center">--</td>
                                </tr>
							
								<tr>
									<td class="align-center font-12">{{ $module_user_roles }}</td>
									<td class="align-center">
										@if((CommonHelper::arrayHasValue(Input::old('permissions')) && CommonHelper::valueInArray('CanAccessUserRoles', Input::old('permissions'))) || CommonHelper::valueInArray('CanAccessUserRoles', json_decode($user_role->permissions)))
											<input type="checkbox" class="checkbox" name="permissions[]" value="CanAccessUserRoles" checked="checked" />
										@else
											<input type="checkbox" class="checkbox" name="permissions[]" value="CanAccessUserRoles" />
										@endif
									</td>
									<td class="align-center">
										@if((CommonHelper::arrayHasValue(Input::old('permissions')) && CommonHelper::valueInArray('CanInsertUserRoles', Input::old('permissions'))) || CommonHelper::valueInArray('CanInsertUserRoles', json_decode($user_role->permissions)))
											<input type="checkbox" class="checkbox" name="permissions[]" value="CanInsertUserRoles" checked="checked" />
										@else
											<input type="checkbox" class="checkbox" name="permissions[]" value="CanInsertUserRoles" />
										@endif
									</td>
									<td class="align-center">
										@if((CommonHelper::arrayHasValue(Input::old('permissions')) && CommonHelper::valueInArray('CanUpdateUserRoles', Input::old('permissions'))) || CommonHelper::valueInArray('CanUpdateUserRoles', json_decode($user_role->permissions)))
											<input type="checkbox" class="checkbox" name="permissions[]" value="CanUpdateUserRoles" checked="checked" />
										@else
											<input type="checkbox" class="checkbox" name="permissions[]" value="CanUpdateUserRoles" />
										@endif
									</td>
									<td class="align-center">
										@if((CommonHelper::arrayHasValue(Input::old('permissions')) && CommonHelper::valueInArray('CanDeleteUserRoles', Input::old('permissions'))) || CommonHelper::valueInArray('CanDeleteUserRoles', json_decode($user_role->permissions)))
											<input type="checkbox" class="checkbox" name="permissions[]" value="CanDeleteUserRoles" checked="checked" />
										@else
											<input type="checkbox" class="checkbox" name="permissions[]" value="CanDeleteUserRoles" />
										@endif
									</td>
									<td class="align-center">--</td>
									<td class="align-center">--</td>
                                	<td class="align-center">--</td>
                                	<td class="align-center">--</td>
                                	<td class="align-center">--</td>
                                	<td class="align-center">--</td>
                                	<td class="align-center">--</td>
									<td class="align-center">--</td>
                                </tr>
                                
                                <tr>
									<td class="align-center font-12">{{ $module_audit_trail }}</td>
									<td class="align-center">
										@if((CommonHelper::arrayHasValue(Input::old('permissions')) && CommonHelper::valueInArray('CanAccessAuditTrail', Input::old('permissions'))) || CommonHelper::valueInArray('CanAccessAuditTrail', json_decode($user_role->permissions)))
											<input type="checkbox" class="checkbox" name="permissions[]" value="CanAccessAuditTrail" checked="checked" />
										@else
											<input type="checkbox" class="checkbox" name="permissions[]" value="CanAccessAuditTrail" />
										@endif
									</td>
									<td class="align-center">--</td>
									<td class="align-center">--</td>
									<td class="align-center">--</td>
									<td class="align-center">
										@if((CommonHelper::arrayHasValue(Input::old('permissions')) && CommonHelper::valueInArray('CanExportAuditTrail', Input::old('permissions'))) || CommonHelper::valueInArray('CanExportAuditTrail', json_decode($user_role->permissions)))
											<input type="checkbox" class="checkbox" name="permissions[]" value="CanExportAuditTrail" checked="checked" />
										@else
											<input type="checkbox" class="checkbox" name="permissions[]" value="CanExportAuditTrail" />
										@endif
									</td>
									<td class="align-center">--</td>
									<td class="align-center">
										@if((CommonHelper::arrayHasValue(Input::old('permissions')) && CommonHelper::valueInArray('CanArchiveAuditTrail', Input::old('permissions'))) || CommonHelper::valueInArray('CanArchiveAuditTrail', json_decode($user_role->permissions)))
											<input type="checkbox" class="checkbox" name="permissions[]" value="CanArchiveAuditTrail" checked="checked" />
										@else
											<input type="checkbox" class="checkbox" name="permissions[]" value="CanArchiveAuditTrail" />
										@endif
									</td>
                                	<td class="align-center">--</td>
									<td class="align-center">--</td>
									<td class="align-center">--</td>
									<td class="align-center">--</td>
									<td class="align-center">--</td>
                                </tr>
                                
                                <tr>
									<td class="align-center font-12">{{ $module_settings }}</td>
									<td class="align-center">
										@if((CommonHelper::arrayHasValue(Input::old('permissions')) && CommonHelper::valueInArray('CanAccessSettings', Input::old('permissions'))) || CommonHelper::valueInArray('CanAccessSettings', json_decode($user_role->permissions)))
											<input type="checkbox" class="checkbox" name="permissions[]" value="CanAccessSettings" checked="checked" />
										@else
											<input type="checkbox" class="checkbox" name="permissions[]" value="CanAccessSettings" />
										@endif
									</td>
									<td class="align-center">
										@if((CommonHelper::arrayHasValue(Input::old('permissions')) && CommonHelper::valueInArray('CanInsertSettings', Input::old('permissions'))) || CommonHelper::valueInArray('CanInsertSettings', json_decode($user_role->permissions)))
											<input type="checkbox" class="checkbox" name="permissions[]" value="CanInsertSettings" checked="checked" />
										@else
											<input type="checkbox" class="checkbox" name="permissions[]" value="CanInsertSettings" />
										@endif
									</td>
									<td class="align-center">
										@if((CommonHelper::arrayHasValue(Input::old('permissions')) && CommonHelper::valueInArray('CanUpdateSettings', Input::old('permissions'))) || CommonHelper::valueInArray('CanUpdateSettings', json_decode($user_role->permissions)))
											<input type="checkbox" class="checkbox" name="permissions[]" value="CanUpdateSettings" checked="checked" />
										@else
											<input type="checkbox" class="checkbox" name="permissions[]" value="CanUpdateSettings" />
										@endif
									</td>
									<td class="align-center">
										@if((CommonHelper::arrayHasValue(Input::old('permissions')) && CommonHelper::valueInArray('CanDeleteSettings', Input::old('permissions'))) || CommonHelper::valueInArray('CanDeleteSettings', json_decode($user_role->permissions)))
											<input type="checkbox" class="checkbox" name="permissions[]" value="CanDeleteSettings" checked="checked" />
										@else
											<input type="checkbox" class="checkbox" name="permissions[]" value="CanDeleteSettings" />
										@endif
									</td>
									<td class="align-center">--</td>
									<td class="align-center">--</td>
                                	<td class="align-center">--</td>
                                	<td class="align-center">--</td>
                                	<td class="align-center">--</td>
                                	<td class="align-center">--</td>
                                	<td class="align-center">--</td>
									<td class="align-center">--</td>
                                </tr>
                                
                                <tr>
									<td class="align-center font-12">{{ $module_stock_piler }}</td>
									<td class="align-center">
										@if((CommonHelper::arrayHasValue(Input::old('permissions')) && CommonHelper::valueInArray('CanAccessStockPiler', Input::old('permissions'))) || CommonHelper::valueInArray('CanAccessStockPiler', json_decode($user_role->permissions)))
											<input type="checkbox" class="checkbox" name="permissions[]" value="CanAccessStockPiler" checked="checked" />
										@else
											<input type="checkbox" class="checkbox" name="permissions[]" value="CanAccessStockPiler" />
										@endif
									</td>
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
                                </tr>
							</tbody>							
						</table>
						
						<a class="btn btn-info" id="submitForm">{{ $button_submit }}</a>
						<a class="btn" href="{{ $url_cancel }}">{{ $button_cancel }}</a>
					</div> <!-- /controls -->				
				</div> <!-- /control-group -->
			</fieldset>
        </div>
        {{ Form::hidden('role_id', $user_role->id) }}
        {{ Form::hidden('filter_role_name', $filter_role_name) }}
        {{ Form::hidden('sort', $sort) }}
        {{ Form::hidden('order', $order) }}
        {{ Form::hidden('page', $page) }}
        
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