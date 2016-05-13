<?php

class StoreOrder extends Eloquent {

	protected $table = 'store_order';

/*************All functions used specifically only for API***************/
	/**
	* Gets products for the store using store order list
	*
	* @example  StoreOrder::getLoadList();
	* @param   storeCode    store code
	*
	* @return upcs
	*/ 
	public static function getLoadList($storeCode)
	{
		/*$loads =  DB::select(DB::raw("select wso.load_code,(SELECT group_concat( concat( so_no ) SEPARATOR ', ') FROM wms_store_order  where load_code = wso.load_code AND store_code = {$storeCode} AND so_status = ".Config::get('so_statuses.done')." ) so_no  , substring('test') as test
			from wms_store_order wso  
			where store_code = {$storeCode} AND so_status = ".Config::get('so_statuses.done')." AND load_code != '0'
			group by load_code" ));
		*/	

		$loads= StoreOrder::select(DB::raw('load_code,group_concat(RTRIM(so_no)) as so_no'))
			->where('store_code', '=', $storeCode)
			->where('so_status', '=', Config::get("so_statuses.done"))
			->where('load_code', '!=', '0') 
			->groupBy('load_code')
			->get();
		if($loads->isEmpty()) throw new Exception("This store does not have delivery loads");
		
		return $loads;
	}

	public static function closeStoreOrdersByLoad($loadCode, $storeCode)
	{
		StoreOrder::where('load_code', '=', $loadCode)
			->where('store_code', '=', $storeCode)
			->update(array(
				'so_status'	=> Config::get('so_statuses.closed'),
				'updated_at' => date('Y-m-d H:i:s'),
				'delivery_date' => date('Y-m-d H:i:s')
				));
		return true;
	} 
	
/*************All functions used specifically only for CMS***************/

	public static function getSOInfo($so_id = NULL) {
		$query = DB::table('store_order')
					->join('dataset', 'store_order.so_status', '=', 'dataset.id', 'LEFT')
				
					->where('store_order.id', '=', $so_id);

		$result = $query->get(array(
									'store_order.*', 
									'dataset.data_display',
								)
							);
		
		return $result[0];
	}

	public static function getSOList($data = array())
	{
		$query = DB::table('store_order')
				    ->join('stores', 'store_order.store_code', '=', 'stores.store_code', 'LEFT');

		if( CommonHelper::hasValue($data['filter_so_no']) ) $query->where('so_no', 'LIKE', '%'.$data['filter_so_no'].'%');
		if( CommonHelper::hasValue($data['filter_store']) ) $query->where('store_order.store_code', 'LIKE', $data['filter_store']);
		if( CommonHelper::hasValue($data['filter_order_date']) ) $query->where('order_date', 'LIKE', '%'.$data['filter_order_date'].'%');
		if( CommonHelper::hasValue($data['filter_status']) ) $query->where('so_status', 'LIKE', '%'.$data['filter_status'].'%');

		if( CommonHelper::hasValue($data['sort']) && CommonHelper::hasValue($data['order']))  {
			if ($data['sort']=='so_no') $data['sort'] = 'so_no';
			if ($data['sort']=='store') $data['sort'] = 'store_name';
			if ($data['sort']=='order_date') $data['sort'] = 'order_date';
			
			$query->orderBy($data['sort'], $data['order']);
		}

		if( CommonHelper::hasValue($data['limit']) && CommonHelper::hasValue($data['page']))  {
			$query->skip($data['limit'] * ($data['page'] - 1))
		          ->take($data['limit']);
		}

		$storeOrders = $query->get(array(
									'store_order.*',
									'stores.store_name'
								)
							);
		DebugHelper::log(__METHOD__, $storeOrders);
		return $storeOrders;
	}

	public static function getCount($data = array()){
		$query = DB::table('store_order')
				    ->join('stores', 'store_order.store_code', '=', 'stores.store_code', 'LEFT');

		if( CommonHelper::hasValue($data['filter_so_no']) ) $query->where('so_no', 'LIKE', '%'.$data['filter_so_no'].'%');
		if( CommonHelper::hasValue($data['filter_store']) ) $query->where('store_order.store_code', 'LIKE', $data['filter_store']);
		if( CommonHelper::hasValue($data['filter_order_date']) ) $query->where('order_date', 'LIKE', '%'.$data['filter_order_date'].'%');
		if( CommonHelper::hasValue($data['filter_status']) ) $query->where('so_status', 'LIKE', '%'.$data['filter_status'].'%');
		
		return $query->count();
	}


	public static function getStoreList(){
		$storeList = DB::table('store_order')->lists('store_code');
		
		return $storeList;
	}

	public static function updateLoadCode($soNumbers, $loadCode)
	{
		// StoreOrder::where('so_no', '=', $soNumber)
		$update = StoreOrder::whereIn('so_no', $soNumbers)
			->update(array(
				'load_code'		=> $loadCode,
				'so_status'		=> Config::get('so_statuses.done'),
				'updated_at'	=> date('Y-m-d H:i:s')));

		return $update;
	}


	/***********************Unused functions *****************************************/

	/*
	*
	*  change letdown status to 1 so that it will be queried 
	*
	*/
/*	public static function closeLtStatus($soNos)
	{
		StoreOrder::whereIn('so_no', $soNos)
			->update(array('lt_status_closed'	=> 1,
				'updated_at'	=> date('Y-m-d H:i:s')
				));
		return;
	}

	//make this dry
	public static function updateStoreOrder($so_no)
	{
		StoreOrder::where('so_no',"=", $so_no)
            ->update(array(
            	'so_status'		=> Config::get('statuses.done'),
            	'datetime_done' => date('Y-m-d H:i:s'),
            	'updated_at' => date('Y-m-d H:i:s')));
		
		return;
	}
*/
	/**
	* Check if the the letdown document is assigned 
	*
	*/ 
/*	public static function checkAndAssign($storesOrderNos, $id)
	{
		$storeOrders = StoreOrder::whereIn('so_no',$storesOrderNos)
			->where('order_date' , '<=', date('Y-m-d H:i:s'))
			->lists('assigned_user_id');
		if($storeOrders === null) {
			throw new Exception("The store you are accessing does not exist or does not have an existing store order.");
		}

		if(in_array(0, $storeOrders)) {
			StoreOrder::assignStore($storesOrderNos, $id);
		} else {
			if(!in_array($id, $storeOrders)) {
				throw new Exception("This Store  has already been assigned");
			} 
		}
		return true;
	}*/
	
	/**
	* Gets stores with items in the picking area that are approved
	*
	*/ 
/*	public static function getStoresWithOrders()
	{
		//$storeCodes = StoreOrder::select('store_order.id', 'store_order.store_code', 'store_order.so_status', 'store_order.assigned_user_id', 'store_order.lt_status_closed','store_order.order_date', 'stores.store_name', 'letdown_details.move_doc_number')
		$storeCodes = StoreOrder::select('store_order.id', 'store_order.store_code', 'store_order.so_status', 'store_order.assigned_user_id', 'store_order.lt_status_closed','store_order.order_date', 'stores.store_name')
			//->leftJoin('letdown_details', 'letdown_details.so_no', '=', 'store_order.so_no')
			->leftJoin('stores', 'store_order.store_code', '=', 'stores.store_code')
			->whereIn('store_order.so_status' ,array(1,2))
			->where('store_order.lt_status_closed' , '=', 1)
			->groupBy('store_order.store_code')
			//->groupBy( 'letdown_details.move_doc_number', 'store_order.store_code')
			->get();
		return $storeCodes;
	}
*/
	/*
	*
	*  Get store order Ids of a given store in a given date in list form
	*
	*/
/*	public static function getStoreOrderNumbers($storeCode, $docNo = null)
	{	
		$storesOrderIds = StoreOrder::where('store_order.store_code', '=', $storeCode)
				->where('store_order.order_date' , '<=', date('Y-m-d H:i:s'));

		if($docNo !== null) {
			$storesOrderIds->select('store_order.so_no as so_no')
				->join('letdown_details', 'letdown_details.so_no', '=', 'store_order.so_no')
				->where('letdown_details.move_doc_number', '=' ,$docNo)
				->groupBy('store_order.so_no');
		}
		return $storesOrderIds->lists('so_no');
	}
*/


	/*
	*
	*  Assign store/store orders, placed this here because it is actually accessing store orders, not stores but displayes as stores in client
	*
	*/
/*	public static function assignStore($storesOrderNos, $id)
	{
		StoreOrder::whereIn('so_no',$storesOrderNos)
            ->update(array('assigned_user_id' => $id));
        return true;
	}*/


	/*
	
	public static function checkStoreOrderStatus($so_no)
	{
		//TODO not final
		$storeOrder = StoreOrder::where('so_no', '=', $so_no)
			->where('so_status' , '!=', Config::get('statuses.open'))
			->get()
			->toArray();

		DebugHelper::log(__METHOD__, $storeOrder);
		if(count($storeOrder) > 0) {
			throw new Exception("This store order is already being processed or has already been processed");

		}
	}

	

	public static function getStoreOrders()
	{
		$storeOrders = StoreOrder::select('id','so_no', 'store_name', 'so_status')
			->whereIn('so_status', array(Config::get('statuses.open'), Config::get('statuses.inProcess'), Config::get('statuses.done')))
			->where('order_date' , '<=', date('Y-m-d H:i:s'))
			->paginate(30);
		return $storeOrders;
	}
*/
}
