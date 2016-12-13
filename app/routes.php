<?php
ini_set('max_execution_time', 0);
/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

Route::get('/', 'HomeController@showIndex');
Route::controller('users', 'UsersController');

Route::group(array('prefix'=>'api'), function()
{
	//Route::controller('user', 'ApiUsers');
	Route::get('user', 'ApiUsers@postLogin');
});

//stores api
Route::group(array('prefix'=>'api/v1'), function()
{
	Route::controller('store_user', 'ApiStoreUsers');
	Route::get('store_receive', 'ApiStoreSO@getSO');
	Route::get('store_receive/detail/{so_no}', 'ApiStoreSO@getSoDetails');
	Route::post('store_receive/accept/{so_no}', 'ApiStoreSO@postAcceptSo');

});

//end stores api

Route::group(array("before"=>"auth.basic"), function()
{
	route::get('barcodesasdf', 'LoadController@exportCSVbarcode');
	Route::get('load/shipping', 'shippingController@index');
    Route::get('load/assigned', 'shippingController@assignPilerForm');
    Route::post('shipping/assigned_to', 'shippingController@assignToPiler');

	Route::get('load/boxnumber', [
    	'as' => 'load/boxnumber', 
    	'uses'=> 'BoxController@getlist12' ]);

	Route::get('load/loadnumbersync', [
    	'as' => 'load/loadnumbersync', 
    	'uses'=> 'BoxController@loadnumbersync' ]);

	Route::get('load/loadnumbersyncstock', [
    	'as' => 'load/loadnumbersyncstock', 
    	'uses'=> 'BoxController@loadnumbersyncstock' ]);

	Route::get('load/loadnumbersyncstock', [
    	'as' => 'load/loadnumbersyncstock', 
    	'uses'=> 'BoxController@getloadnumbersyncstock' ]);

	Route::get('load/shipLoad',[
		'as' => 'load/shipLoad',
		'uses'=> 'BoxController@shippedload' ]);

	Route::get('load/shipLoadstock',[
		'as' => 'load/shipLoadstock',
		'uses'=> 'BoxController@shippedloadstock' ]);

	Route::get('load/loadnumber', [
    	'as' => 'load/loadnumber', 
    	'uses'=> 'BoxController@loadnumber'
    	]);
 
	Route::get('load/removed', [
    	'as' => 'load/removed', 
    	'uses'=> 'BoxController@getremoved'
    	]);
	Route::get('load/boxdetails',[
		'as' 	=> 'load/boxdetails',
		'uses'	=> 'BoxController@getBoxDetails']);

    Route::get('load/load_details','BoxController@index');
    Route::get('load/box_content', 'BoxController@getBoxDetails');
    Route::post('box/new/load', 'BoxController@generateLoadCode');
    Route::get('stock/new/load', 'BoxController@generateLoadCodestock');
  //Route::get('box_list_details/{id}', array('as' => 'box_list_details', 'uses' => 'BoxController@getListBox'));

	Route::get('purchase_order', 'PurchaseOrderController@showIndex');
	Route::get('purchase_order/discrepansy', 'PurchaseOrderController@getlist1');
	Route::post('purchase_order/assign_to_piler', 'PurchaseOrderController@assignToStockPiler');
	Route::post('purchase_order/close_po', 'PurchaseOrderController@closePO');

	Route::post('purchase_order/partialreceivebtn', [
		'as' 		=> 'purchase_order/partialreceivebtn',
		'uses' 		=> 'PurchaseorderController@PartialReceive', ]);

	Route::get('purchase_order/export', 'PurchaseOrderController@exportCSV');

	/*route::get('purchase_order/partial_received',[
		'as'	=> 'purchase_order/partial_received',
		'uses'  => 'PurchaseorderController@getPartialReceiveButton',]);*/

	route::get('purchase_order/export_excel_file',[
		'as'	=> 'purchase_order/export_excel_file',
		'uses'	=> 'PurchaseorderController@exportCSVexcelfile',]);

	Route::get('purchase_order/export_detail', 'PurchaseOrderController@exportDetailsCSV');
	Route::get('purchase_order/export_backorder', 'PurchaseOrderController@exportBackorder');
	Route::post('purchase_order/reopen', 'PurchaseOrderController@reopen');
	Route::get('purchase_order/assign', 'PurchaseOrderController@assignPilerForm');
	
	Route::get('purchase_order/pulljda', 'PurchaseOrderController@pullJDA');

	Route::get('purchase_order/get_division', 'PurchaseOrderController@getDivisionv2');

	Route::get('purchase_order/division', 'PurchaseOrderController@showdivision');
	Route::get('purchase_order/discrepansy', 'PurchaseorderController@discrepansy');
	Route::get('purchase_order/updateqty', 'PurchaseOrderController@updateqty');

	Route::get('purchase_order/shipment_input',[
		'as'	=> 'purchase_order/shipment_input',
		'uses'	=> 'PurchaseorderController@getShipmentInput']);

	Route::get('purchase_order/detail', 'PurchaseOrderController@getPODetails');
	Route::get('purchase_order/sync_to_mobile', 'PurchaseOrderController@synctomobile');

/*	Route::get('purchase_order/pullPOPartialReceive', [
		'as'  => 'purchase_order/pullPOPartialReceive',
		'uses' => 'PurchaseorderController@pullPODemo' ]);
*/
	Route::get('purchase_order/sync','PurchaseOrderController@synctdivision');

	Route::get('purchase_order/sync_to_mobile_division', 'PurchaseOrderController@synctomobiledivision');
	
	/*================================================================*/
/*******************************stock transfer module************************/

	Route::get('store_return/detail', 'StoreReturnController@getSODetails');
	Route::get('store_return/assign', 'StoreReturnController@assignPilerForm');

	Route::get('stocktransfer/assignpicking', 'StoreReturnController@assignPilerFormpicking');

	Route::post('stock_transfer/stocktransferpicking',[
		'as' => 'stock_transfer/stocktransferpicking',
		'uses' => 'StoreReturnController@assignToStockPilerPicking']);
	
	Route::get('stock_transfer/discrepansymts',[
		'as'	=> 'stock_transfer/discrepansymts',
		'uses'	=> 'StocktransferController@getdiscrepancymts']);

	Route::get('stock_transfer/discrepansypick',[
		'as'	=> 'stock_transfer/discrepansypcik',
		'uses'	=> 'StocktransferController@getdiscrepancypick']);

	route::get('stock_transfer/discrepansyPdffile',[
		'as' 	=> 'stock_transfer/discrepansyPdffile',
		'uses'	=> 'StoreReturnController@exportCSVMTSdicrepancy']);

	route::get('stock_transfer/discrepansyExcelfile',[
		'as' 	=> 'stock_transfer/discrepansyExcelfile',
		'uses'	=> 'StoreReturnController@getexportCVSmtsdiscrepancyexelfile']);


	Route::get('store_return/export', 'StoreReturnController@exportCSV');
	Route::get('store_return/export_detail', 'StoreReturnController@exportDetailsCSV');
	Route::post('store_return/close', 'StoreReturnController@closeStoreReturn');

	Route::get('stocktransfer/stocktranferload',[ 
		'as' => 'stocktransfer/stocktranferload',
		'uses' => 'shippingController@getStockStransferLoad']);

														Route::get('stock_transfer/assignToTLNumber',[
																'as'	=> 'stock_transfer/assignToTLNumber',
																'uses'	=> 'StocktransferController@getStockTLnumberPosted']);

	Route::get('stock_transfer/TLnumbersync', [
    	'as' => 'stock_transfer/TLnumbersync', 
    	'uses'=> 'StocktransferController@StoreReturnTLnumbersync' ]);

	Route::get('store_return/pickingstock', [
		'as' 	=> 'store_return/pickingstock',
		'uses'	=> 'StocktransferController@getUpdateDate']);
	
/***********asdfasdf dd*******/
	Route::get('stocktransfer/assignedstockload',[
		'as'	=> 'stocktransfer/assignedstockload',
		'uses'	=> 'StocktransferController@getstockloadassign']);

	Route::post('stocktransfer/assignedstockloadpost',[
		'as'	=> 'stocktransfer/assignedstockloadpost',
		'uses'	=> 'StocktransferController@getstockloadassignpost']);
/**********asdfasdfasf  d d d dd********/
	Route::get('stock_transfer/removed', [
    	'as' => 'stock_transfer/removed', 
    	'uses'=> 'StocktransferController@getremoved']); 

	Route::get('stock_transfer/PickingTLnumbersync', [
    	'as' => 'stock_transfer/PickingTLnumbersync', 
    	'uses'=> 'StocktransferController@StoreReturnPickingandPackTLnumbersync' ]);

													Route::get('stock_transfer/assignPostedTLnumberStockTransfer',[
														'as'	=> 'stock_transfer/assignPostedTLnumberStockTransfer',
														'uses'	=> 'StocktransferController@getStockTransferLoadnumberAssign']);

	route::get('stock_transfer/exportCSV',[
		'as' => 'stock_transfer/exportCSV',
		'uses'=> 'StocktransferController@getMTSCSV']);

	route::get('stock_transfer/exportCSVunlisted',[
		'as' => 'stock_transfer/exportCSVunlisted',
		'uses'=> 'StocktransferController@getCSVUnlistedReportMTS']);

	Route::get ('stock_transfer/stocknumbertlnumber',[
		'as'	=> 'stock_transfer/stocknumbertlnumber',
		'uses'	=> 'StocktransferController@getlist1']);

	Route::get('stock_transfer/exportCSVpickingreport',[
		'as' => 'stock_transfer/exportCSVpickingreport',
		'uses'=> 'StocktransferController@getCSVPickingReport']);
	
	route::get('stock_transfer/export_excel_file',[
		'as'	=> 'stock_transfer/export_excel_file',
		'uses'	=> 'StocktransferController@exportCSVasdf2fsdf']);
	
	Route::get('stock_transfer/closemtsnumber', [
    	'as' => 'stock_transfer/closemtsnumber', 
    	'uses'=> 'StocktransferController@closePickliststockreceiving']);

	Route::get('stock_transfer/closetlnumberpick', [
    	'as' => 'stock_transfer/closetlnumberpick', 
    	'uses'=> 'StocktransferController@closePickliststockpicking' ]);

	Route::get('stock_transfer/MTSReceiving','StocktransferController@getSOList');

	route::get('stock_transfer/mts_discrepansy',[
		'as'	=> 'stock_transfer/mts_discrepansy',
		'uses'	=> 'StocktransferController@getdiscrepancymts']);

	Route::post('store_return/assign_to_piler', 'StoreReturnController@assignToStockPiler');

	Route::get('stocktransfer/mts_transfer', [
		'as' => 'stocktransfer/mts_transfer',
		'uses'=> 'StocktransferController@getlist' ]);
	 
	Route::get('stocktransfer/PickAndPackStore', [
		'as' => 'stocktransfer/PickAndPackStore',
		'uses'=> 'StocktransferController@PickAndPackStore' ]);

	Route::get('stocktransfer/MTSpickdetails', [
		'as' => 'stocktransfer/MTSpickdetails',
		'uses'=> 'StocktransferController@getMTSpickpackdetails' ]);
 	 
	Route::get('stock_transfer/assign',[
		'as' => 'stock_transfer/assign',
		'uses' => 'StocktransferController@StockTransferpiler' ]);
 	 

	 Route::get('store_return/mts_receiving_detail',[
	 	'as' => 'store_return/mts_receiving_detail',
	 	'uses' =>'StocktransferController@getMtsRecevingDetail' ]);

	 Route::post('mtsload/new/loadcode',[   // stock transfer load generate controller //
	 	'as' 	=> 'mtsload/new/loadcode',
	 	'uses'  => 'StocktransferController@getMTSGenerateLoadCode' ]);

	/*================================================================*/
/*******************************stock transfer module************************/

Route::get('reverse_logistic/exportCSV', [
	'as' 	=> 'reverse_logistic/exportCSV',
	'uses'	=> 'ReverseLogisticController@exportDetailsCSV']);

Route::get('reverse_logistic/exportCSVexcelfile',[
	'as'	=> 'reverse_logistic/exportCSVexcelfile',
	'uses'	=> 'ReverseLogisticController@exportCSVexcelfile']);

 Route::get('reverse_logistic/exportCSVunlisted', [
	'as' 	=> 'reverse_logistic/exportCSVunlisted',
	'uses'	=> 'ReverseLogisticController@exportReverseUnlisted']);

Route::get('reverse_logistic/reverse_list',[
	'as' =>'reverse_logistic/reverse_list',
	'uses'=>'ReverseLogisticController@getreverselist']);

Route::get('reverse_logistic/discrepansy',[
	'as' =>'reverse_logistic/discrepansy',
	'uses'=>'ReverseLogisticController@getdiscrepancy']);

Route::get('reverse_logistic/TLnumbersync', [
    	'as' => 'reverse_logistic/TLnumbersync', 
    	'uses'=> 'ReverseLogisticController@ReverseTLnumbersync' ]);

Route::get('reverse_logistic/closetlnumberReverse', [
    	'as' => 'reverse_logistic/closetlnumberReverse', 
    	'uses'=> 'ReverseLogisticController@closeReverseStatus' ]);

Route::get('reverse_logistic/assign',[
	'as' => 'reverse_logistic/assign',
	'uses'=> 'ReverseLogisticController@assignPilerFormReverse' ]);

Route::get('reverse_logistic/detail',[
	'as' => 'reverse_logistic/detail',
	'uses' => 'ReverseLogisticController@getSODetails' ]);

Route::post('reverse_logistic/assign_2_piler',[
	'as'	=> 'reverse_logistic/assign_2_piler',
	'uses'	=> 'ReverseLogisticController@assignToStockPilerReversepost' ]);

Route::get('reverse_logistic/export',[
	'as' => 'reverse_logistic/export',
	'uses'=> 'ReverseLogisticController@exportCSV' ]);

Route::get('reverse_logistic/export_detail',[
	'as' => 'reverse_logistic/export_detail',
	'uses'=> 'ReverseLogisticController@exportDetailsCSV' ]);

	Route::get('box/list', 'BoxController@index');
	Route::get('box/detail', 'BoxController@getBoxDetails');
	Route::get('box/create', 'BoxController@createBox');
	Route::post('box/create', 'BoxController@postCreateBox');
	Route::get('box/update', 'BoxController@updateBox');
	Route::post('box/update', 'BoxController@postUpdateBox');
	Route::get('box/export', 'BoxController@exportBoxes');
	Route::get('box/export_detail', 'BoxController@exportDetailsCSV');
	Route::get('box/delete', 'BoxController@deleteBoxes');
	Route::post('box/load', 'BoxController@loadBoxes');
	
    Route::get('box/assign', 'BoxController@assignPilerForm');
    Route::post('box/assign_to_piler', 'BoxController@assignToStockPiler');

	Route::get('letdown', 'LetDownController@showIndex');
	Route::get('letdown/detail', 'LetDownController@getLetDownDetails');
	Route::get('letdown/export', 'LetDownController@exportCSV');
	Route::get('letdown/export_detail', 'LetDownController@exportDetailsCSV');
	Route::post('letdown/close_letdown', 'LetDownController@closeLetdown');
	Route::get('letdown/locktags', 'LetDownController@getLockTagList');
	Route::get('letdown/locktags_detail', 'LetDownController@getLockTagDetail');
	Route::post('letdown/unlock', 'LetDownController@unlockLetdownTag');

	Route::get('picking/TLnumbersync', [
    	'as' => 'picking/TLnumbersync', 
    	'uses'=> 'PicklistController@TLnumbersync'
    	]);
	Route::get('picking/list', 'PicklistController@showIndex');

	Route::get('picking/updatedate', [
		'as' 	=> 'picking/updatedate',
		'uses'	=> 'PicklistController@getUpdateDate']);

	Route::get('picking/detail', 'PicklistController@getPicklistDetails');

	Route::get('picking/discrepansy',[
		'as'	=> 'picking/discrepansy',
		'uses'	=> 'PicklistController@getdiscrepancy']);

	Route::get('picking/export_excel_file',[
		'as'	=> 'picking/export_excel_file',
		'uses'	=> 'PicklistController@exportCSVasdf2fsdf']);

	Route::get('picking/export', 'PicklistController@exportPickListVarianceCSV');
	Route::get('picking/export_detail', 'PicklistController@exportDetailCSV');
	Route::get('picking/update', 'PicklistController@updatePicklist');
	Route::get('picking/locktags', 'PicklistController@getLockTagList');
	Route::get('picking/locktags_detail', 'PicklistController@getLockTagDetail');
	Route::post('picking/unlock', 'PicklistController@unlockPicklistTag');
	Route::post('picking/change_to_store', 'PicklistController@changeToStore');
	Route::post('picking/new/load', 'PicklistController@generateLoadCode');
	Route::post('picking/load', 'PicklistController@loadPicklistDocuments');
	Route::get('picking/assign', 'PicklistController@assignPilerForm');
	Route::post('picking/assign_to_piler', 'PicklistController@assignToStockPiler');
	Route::post('picking/close', 'PicklistController@closePicklist');
    Route::get('picking/printboxlabel/{doc_num}', 'PicklistController@printBoxLabel');
    Route::get('picking/printboxlabelstock/{doc_num}', 'StocktransferController@printBoxLabelstock');

	Route::get('inventory', 'InventoryController@showIndex');
	Route::get('inventory/export', 'InventoryController@exportCSV');
	Route::get('inventory/detail', 'InventoryController@getDetails');
	Route::get('inventory/export_detail', 'InventoryController@exportDetailsCSV');


	Route::get('load/barcodes', 'LoadController@getbarcode');


	Route::get('products', 'ProductListController@showIndex');
	Route::get('products/export', 'ProductListController@exportCSV');
	Route::get('products/department', 'ProductListController@getSubDepartments');

	/*Route::get('slots', 'SlotListController@showIndex');*/
	Route::get('slots/export', 'SlotListController@exportCSV');
		
	Route::get('stores', 'StoreController@showIndex');
	Route::get('stores/export', 'StoreController@exportCSV');

	Route::get('vendors', 'VendorController@showIndex');
	Route::get('vendors/export', 'VendorController@exportCSV');

	Route::get('user', 'UsersController@showIndex');
	Route::get('user/insert', 'UsersController@insertDataForm');
	Route::post('user/insertData', 'UsersController@insertData');
	Route::get('user/update', 'UsersController@updateDataForm');
	Route::post('user/updateData', 'UsersController@updateData');
	Route::get('user/password', 'UsersController@updatePasswordForm');
	Route::post('user/updatePassword', 'UsersController@updatePassword');
	Route::post('user/delete', 'UsersController@deleteData');
	Route::get('user/export', 'UsersController@exportCSV');

	Route::get('user/profile', 'UsersController@updateProfileForm');
	Route::get('user/change_password', 'UsersController@updateProfilePasswordForm');

	Route::get('user_roles', 'UserRolesController@showIndex');
	Route::get('user_roles/insert', 'UserRolesController@insertDataForm');
	Route::post('user_roles/insertData', 'UserRolesController@insertData');
	Route::get('user_roles/update', 'UserRolesController@updateDataForm');
	Route::post('user_roles/updateData', 'UserRolesController@updateData');
	Route::post('user_roles/delete', 'UserRolesController@deleteData');

	Route::get('audit_trail', 'AuditTrailController@showIndex');
	Route::get('audit_trail/insert', 'AuditTrailController@insertData');
	Route::get('audit_trail/export', 'AuditTrailController@exportCSV');
	Route::get('audit_trail/archive_logs', 'AuditTrailController@archive');

	Route::get('settings', 'SettingsController@showIndex');
	Route::get('settings/insert', 'SettingsController@insertDataForm');
	Route::post('settings/insertData', 'SettingsController@insertData');
	Route::get('settings/update', 'SettingsController@updateDataForm');
	Route::post('settings/updateData', 'SettingsController@updateData');
	Route::post('settings/delete', 'SettingsController@deleteData');

	Route::get('load/list', 'LoadController@showIndex');
	Route::get('load/export', 'LoadController@exportCSV');
	Route::post('load/ship', 'LoadController@shipLoad');
	Route::get('load/print/{loadCode}', 'LoadController@printLoad');
	Route::get('load/print/update/{loadCode}', 'LoadController@updatePrintLoad');
    Route::get('load/printpacklist/{loadCode}', 'LoadController@printPackingList');
    Route::get('load/printpackliststock/{loadCode}', 'LoadController@printPackingListstock');
    Route::get('load/printpacklist/update/{loadCode}', 'LoadController@updatePrintPackingList');
    Route::get('load/printloadingsheet/{loadCode}', 'LoadController@printLoadingSheet');
    Route::get('load/printloadingsheetstock/{loadCode}', 'LoadController@printLoadingSheetstock');

	Route::get('purchase_order/unlisted', 'PurchaseorderController@exportDetailsCSV');
	Route::get('unlisted/export', 'UnlistedController@exportCSV');

	Route::get('expiry_items', 'ExpiryItemsController@showIndex');
	Route::get('expiry_items/export', 'ExpiryItemsController@exportCSV');
});
Route::group(array('prefix'=>'api/v1'), function()
{
	Route::post('oauth/access_token', function()
	{
	    // return AuthorizationServer::performAccessTokenFlow();
	    return Response::json(Authorizer::issueAccessToken());
	});
});
//Route::group(array('prefix'=>'api/v1', 'before'=>'oauth|auth.piler'), function()
Route::group(array('prefix'=>'api'), function()
{
	Route::get('RPoList/{piler_id}','ApiPurchaseOrder@RPolist');
	Route::get('RPoListDetailUpdate/{receiver_no}/{division}/{upc}/{quantity_delivered}','ApiPurchaseOrder@RPoListDetailUpdate');
	Route::get('RPoListDetail/{receiver_no}/{division_id}','ApiPurchaseOrder@RPolistDetail');
	Route::get('RPoUpdatestatus/{receiver_no}/{division_id}','ApiPurchaseOrder@UpdateApiRPoSlot');
	Route::get('RPoListDetailUpdate/{receiver_no}/{division}/{upc}/{rqty}','ApiPurchaseOrder@RPoListDetailUpdate');
	Route::get('products', 'ApiProductList@index');
	Route::get('products/upc_exist', 'ApiProductList@checkUpc');
	//purchase order apis
	Route::get('purchase_order/{piler_id}', 'ApiPurchaseOrder@index');
	Route::post('purchase_order/{po_order_no}', 'ApiPurchaseOrder@savedReceivedPO');
	Route::get('purchase_order/details/{receiver_no}', 'ApiPurchaseOrder@getDetails');
	Route::post('purchase_order/change_status/{po_order_no}', 'ApiPurchaseOrder@updateStatus');
	Route::post('purchase_order/not_in_po/{po_order_no}', 'ApiPurchaseOrder@notInPo');
	Route::post('purchase_order/unlisted/{po_order_no}', 'ApiPurchaseOrder@unlisted');

	//reserved zone
	Route::get('upc', 'ApiReserveZone@index');
	Route::post('upc/reserve_zone/{slot_id}', 'ApiReserveZone@putToReserve');
	//slot master
	Route::get('slots/list', 'ApiSlotMasterList@index');

	//letdown api
	Route::get('letdown/list', 'ApiLetdown@getLetDownLists');
	Route::get('letdown/list/detail/{sku}', 'ApiLetdown@getLetdownDetail');
	Route::post('letdown/detail', 'ApiLetdown@postLetdownDetail');

	//picklist
	Route::get('picking/list', 'ApiPicklist@getPickingLists');
	Route::get('picking/detail/{sku_or_store}', 'ApiPicklist@getPickingDetail');
	Route::post('picking/detail', 'ApiBox@postToPicklistToBox');
	Route::post('picking/done/{sku_or_store}', 'ApiPicklist@postDone');


	//boxing
	Route::get('boxes/{store_code}', 'ApiBox@getBoxesByStore');
	Route::post('boxes/create', 'ApiBox@postCreateBox');
	Route::post('boxes/load', 'ApiLoads@loadBoxes');
	Route::get('boxes', 'ApiBox@getAllBoxes');

	//load
	Route::post('loads/create', 'ApiLoads@generateLoadCode');
	Route::get('loads/list', 'ApiLoads@getList');

	//get status types
	Route::get('status/values', 'HomeController@getStatusValues');

	//department
	Route::get('department/brands', 'ApiDepartment@getBrands');
	Route::get('department/divisions', 'ApiDepartment@getDivisions');

	//audit trail
	Route::post('audittrail/insert', 'ApiAuditTrail@insertRecord');

	//slots
	Route::get('slot/is_exist', 'ApiSlots@getIsSlotExist');

	//store return
	Route::get('store_return/list', 'ApiStoreReturn@getStoreReturnList');
	Route::get('store_return/details/{soNo}', 'ApiStoreReturn@getStoreReturnDetail');
	Route::post('store_return/save', 'ApiStoreReturn@postSaveDetail');
	Route::post('store_return/change_status/{soNo}', 'ApiStoreReturn@updateStatus');
	Route::post('store_return/not_in_transfer/{soNo}', 'ApiStoreReturn@notInTransfer');

	Route::post('inter_transfer/add', 'ApiInterTransfer@insertRecord');

	//manual move
	Route::get('manual_move/get_info', 'ApiManualMove@getInfo');
	Route::post('manual_move/save', 'ApiManualMove@insertRecord');
});

Route::group(array('prefix'=>'api/v1', 'before'=>'oauth'), function()
{
	//store order api
	Route::get('store_order/loads/{store_code}', 'ApiStoreOrder@getLoads');
	Route::get('store_order/product/list/{store_code}', 'ApiStoreOrder@getProductList');
	Route::post('store_order/receive', 'ApiStoreOrder@postReceive');
	Route::post('store_order/close', 'ApiStoreOrder@closeStoreOrders');
});

Route::group(array('prefix'=>'api/v2', 'before'=>'oauth|auth.piler'), function()
{
	Route::get('letdown/list', 'ApiLetdown@getLetDownListsv2');
	Route::get('letdown/details/{move_doc_number}', 'ApiLetdown@getLetdownDetailv2');
	Route::post('letdown/save', 'ApiLetdown@postLetdownDetailv2');

	Route::get('picking/list', 'ApiPicklist@getPickingListsv2');
	Route::get('picking/details/{move_doc_number}', 'ApiPicklist@getPickingDetailv2');
	Route::post('picking/save', 'ApiPicklist@postPickingDetailv2');
	Route::post('picking/change_status/{docNo}', 'ApiPicklist@updateStatus');

    Route::get('boxes/{store_code}/{userid}', 'ApiBox@getBoxesByStoreUserId'); // with username
    Route::post('boxes/createbyuserid', 'ApiBox@postCreateBoxByUserId'); // with username
});
