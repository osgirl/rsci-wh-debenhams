<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::group(array('prefix'=>'api'), function()
{
	/////////////////////////////////////////////

Route::get('users', 'ControllerApiName@postLogin');
//////////////////////////////////////////////
	//Route::post('validateUser', 'ControllerApiName@validateUser');
	Route::get('name','ControllerApiName@showName');
	Route::get('RPoList/{piler_id}','ControllerApiName@RPolist');
	Route::get('RPoListDetail/{receiver_no}/{division_id}','ControllerApiName@RPolistDetail');
	Route::get('RPoUpdatestatus/{receiver_no}/{division_id}','ControllerApiName@UpdateApiRPoSlot');	
	Route::get('RPoListDetailUpdate/{receiver_no}/{division}/{upc}/{rqty}/{slot}','ControllerApiName@UpdateRPoListDetail');

	Route::get('UserLogin/{username}/{password}','ControllerApiName@UserLogin');

	Route::get('getquery/{query}','ControllerApiName@getquery');


	Route::get('RPoListDetailAdd/{receiver_no}/{division}/{sku}/{upc}/{rqty}/{userid}/{slot}/{division_name}','ControllerApiName@RPoListDetailAdd');
	

	Route::get('PTLList/{piler_id}','ControllerApiName@PTLList');
	Route::get('PTLListDetail/{moved_doc}','ControllerApiName@PTLListDetail');
	Route::get('PTLListDetailUpdate/{picking_id}/{upc}/{rcv_qty}','ControllerApiName@PTLListDetailUpdate');
	Route::get('PTLListUpdate/{moved_doc}','ControllerApiName@PTLListUpdate');
	Route::get('PTLGetBoxCode/{store_id}','ControllerApiName@PTLGetBoxCode');
	Route::get('PSTTLGetBoxCode/{store_id}','ControllerApiName@PSTTLGetBoxCode');
	route::get('AddBoxDetail/{picklist_detail_id}/{box_code}/{moved_qty}','ControllerApiName@AddBoxDetail');
	Route::get('PTLBoxUpdate/{box_code}/{store_id}/{move_doc}/{number}/{total}','ControllerApiName@PTLBoxUpdate');
	Route::get('PTLBoxValidate/{box_code}','ControllerApiName@PTLBoxValidate');
	Route::get('PTLGetLastBoxCode/{store_id}/{move_doc}','ControllerApiName@PTLGetLastBoxCode'); 
	Route::get('PTLGetLastBoxCode1/{store_id}','ControllerApiName@PTLGetLastBoxCode1');
	Route::get('PTLNewBoxCode/{box_code}/{store_id}/{move_doc}/{number}/{total}','ControllerApiName@PTLNewBoxCode');
	Route::get('LBList/{piler_id}','ControllerApiName@LBlist');
	Route::get('STLBList/{piler_id}','ControllerApiName@STLBlist');
	Route::get('LBListDetail/{load_code}', 'ControllerApiName@LBListdetails');
	Route::get('STLBListDetail/{load_code}', 'ControllerApiName@STLBListdetails');
	Route::get('STLBListDetailBox/{move_doc}', 'ControllerApiName@STLBListdetailsBox');
	Route::get('UpdateSTLoadingBoxStatus/{move_doc}/{boxcode}/{status}', 'ControllerApiName@UpdateSTLoadingBoxStatus');
	Route::get('PSTTLGetLastBoxCode/{store_id}/{move_doc}','ControllerApiName@PSTTLGetLastBoxCode');

	 Route::get('LBListDetailBox/{move_doc}', 'ControllerApiName@LBListDetailbox');
	 Route::get('UpdateLoadingStatus/{load_code}/{date}', 'ControllerApiName@UpdateLoadingStatus');
	Route::get('PSTTLNewBoxCode/{box_code}/{store_id}/{move_doc}/{number}/{total}','ControllerApiName@PSTTLNewBoxCode');


	/* ******************************* Store receiving Api************************************/
	Route::get('PStoreList/{piler_id}', 'ControllerApiName@PStoreList');
	Route::get('PStoreListDetail/{box_code}', 'ControllerApiName@PStoreListDetail');
	Route::get('PStoreListDetailUpc/{moved_doc}/{box_code}', 'ControllerApiName@PStoreListDetailUpc');
	Route::get('PStoreListDetailContent/{boxcode}/{move_doc}', 'ControllerApiName@PStoreListDetailContent');
	Route::get('UpdateLoadingBoxStatus/{move_doc}/{boxcode}/{status}', 'ControllerApiName@UpdateLoadingBoxStatus');
	Route::get('UpdateStoreOrderUpc/{move_doc}/{upc}/{rcv_qty}', 'ControllerApiName@UpdateStoreOrderUpc');
	Route::get('UpdateStoreOrderBox/{move_doc}/{box_code}/{upc}/{rcv_qty}', 'ControllerApiName@UpdateStoreOrderBox');
 
	Route::get('UpdateStoreOrderStatus/{move_doc}', 'ControllerApiName@UpdateStoreOrderStatus');
 
	/* ******************************* Store receiving Api************************************/
	Route::get('RSTList/{piler_id}', 'ControllerApiName@RSTList');
	Route::get('RSTListDetail/{mts_no}', 'ControllerApiName@RSTListDetail');
	Route::get('PSTList/{piler_id}', 'ControllerApiName@PSTList');
	Route::get('PSTListDetail/{mts_no}', 'ControllerApiName@PSTListDetail');
	Route::get('RRLList/{piler_id}', 'ControllerApiName@RRLList');


	/* ******************************* Stock Transfer MTS Receiving************************************/

	Route::get('RSTListDetailUpdate/{mts_no}/{upc}/{rqty}','ControllerApiName@RSTListDetailUpdate');
	Route::get('RSTListDetailAdd/{mts_no}/{upc}/{rqty}','ControllerApiName@RSTListDetailAdd');
	Route::get('RSTListUpdateStatus/{mts_no}','ControllerApiName@RSTListUpdateStatus');

	/* *********************************subloc picking **************************************/
	Route::get('PSTTLListDetailUpdate/{picking_id}/{upc}/{rcv_qty}','ControllerApiName@PSTTLListDetailUpdate');
	Route::get('PSTTLListUpdate/{moved_doc}','ControllerApiName@PSTTLListUpdate');
	route::get('AddSTBoxDetail/{picklist_detail_id}/{box_code}/{moved_qty}','ControllerApiName@AddSTBoxDetail');
	Route::get('PSTTLBoxUpdate/{box_code}/{store_id}/{move_doc}/{number}/{total}','ControllerApiName@PSTTLBoxUpdate');



	/* ******************************* Reverse Logistic Receiving************************************/


	Route::get('RRLListDetailUpdate/{mts_no}/{upc}/{rqty}','ControllerApiName@RRLListDetailUpdate');
	Route::get('RRLListDetailAdd/{mts_no}/{upc}/{rqty}','ControllerApiName@RRLListDetailAdd');
	Route::get('RRLListDetail/{mts_no}', 'ControllerApiName@RRLListDetail'); 
	Route::get('RRLListUpdateStatus/{mts_no}','ControllerApiName@RRLListUpdateStatus');
	Route::get('login/{username}/{password}','ControllerApiUser@validateUser');

	/**************************Loading Module **************************/


	Route::get('NewLoadingList/{piler_id}','ControllerApiName@NewLoadingList');
	Route::get('NewLoadingListDetail/{load_code}','ControllerApiName@NewLoadingListDetails');
	Route::get('UpdateNewLoadingBoxStatus/{load_code}/{box_code}/{status}','ControllerApiName@UpdateNewLoadingBoxStatus');
 /*************************Subloc Loading Module ********************/


	Route::get('NewLoadingSTList/{piler_id}','ControllerApiName@NewLoadingSTList');
	Route::get('NewLoadingSTListDetail/{load_code}','ControllerApiName@NewLoadingSTListDetails');
	Route::get('UpdateNewLoadingSTBoxStatus/{load_code}/{box_code}/{status}','ControllerApiName@UpdateNewLoadingSTBoxStatus');
	Route::get('UpdateLoadingSTStatus/{load_code}/{date}', 'ControllerApiName@UpdateLoadingSTStatus');

/********************************************************/
			//verify user//permission at mobile using quantity or add upc

	Route::get('VerifyUser/{username}/{password}','ControllerApiUser@getVerifyValidateUser');
	Route::get('SlotCode','ControllerApiName@getSlotCodeList');



  //NewLoadingList
});