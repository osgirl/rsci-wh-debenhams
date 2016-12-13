<?php

class StoreReturn extends Eloquent {

	protected $table = 'store_return';

	public static function getSOList($data = array())
	{
		// print_r($data); die();
		$query = StoreReturn::select('store_return.*','stores.store_name','dataset.data_code', 'dataset.data_value', 'dataset.data_display')
			->join('stores', 'store_return.from_store_code', '=', 'stores.store_code', 'LEFT')
			->join('dataset', 'so_status', '=', 'dataset.id');

		if( CommonHelper::hasValue($data['filter_so_no']) ) $query->where('so_no', 'LIKE', '%'.$data['filter_so_no'].'%');
		if( CommonHelper::hasValue($data['filter_store']) ) $query->where('store_return.from_store_code', 'LIKE', $data['filter_store']);
		if( CommonHelper::hasValue($data['filter_created_at']) ) $query->where('store_return.created_at', 'LIKE', '%'.$data['filter_created_at'].'%');
		if( CommonHelper::hasValue($data['filter_status']) ) $query->where('data_value', 'LIKE', '%'.$data['filter_status'].'%');

		if( CommonHelper::hasValue($data['sort']) && CommonHelper::hasValue($data['order']))  {
			if ($data['sort']=='so_no') $data['sort'] = 'so_no';
			if ($data['sort']=='store') $data['sort'] = 'store_name';
			if ($data['sort']=='created_at') $data['sort'] = 'store_return.created_at';

			$query->orderBy($data['sort'], $data['order']);
		}

		if( CommonHelper::hasValue($data['limit']) && CommonHelper::hasValue($data['page']))  {
			$query->skip($data['limit'] * ($data['page'] - 1))
		          ->take($data['limit']);
		}

		$result = $query->get();

		// get the multiple stock piler fullname
		foreach ($result as $key => $picklist) {
			$assignedToUserId       = explode(',', $picklist->assigned_to_user_id);
			$getUsers               = User::getUsersFullname($assignedToUserId);
			$result[$key]->fullname = implode(', ', array_map(function ($entry) { return $entry['name']; }, $getUsers));
		}

		DebugHelper::log(__METHOD__, $result);
		return $result;
	}
	public static function getSOListReport($data= array(), $getCount=false)
	{
	

 	$query = DB::table('store_return_detail')
 				->SELECT('store_return.so_no','store_name','to_store_code','store_return_detail.sku as upc','product_lists.sku','description as short_name','users.firstname','users.lastname','store_return_detail.created_at','received_qty','delivered_qty',DB::raw('CONCAT(firstname, " ", lastname) AS fullname, (received_qty - delivered_qty) as variance'))
 ->join('store_return','store_return_detail.so_no','=','store_return.so_no','LEFT')
->join('product_lists','store_return_detail.sku','=','product_lists.upc','LEFT')
->JOIN('stores','store_return.from_store_code','=','stores.store_code','LEFT')
->JOIN('users','store_return.assigned_to_user_id','=','users.id','LEFT')
->where('store_return.so_status','=',23) 
->where('store_return_detail.delivered_qty','<>','store_return_detail.received_qty')
->where('store_return.assigned_to_user_id','!=', 0);

if( CommonHelper::hasValue($data['filter_doc_no']) ) $query->where('store_return.so_no', 'LIKE', '%'. $data['filter_doc_no'] . '%');

	 
		if( CommonHelper::hasValue($data['limit']) && CommonHelper::hasValue($data['page']))  {
			$query->skip($data['limit'] * ($data['page'] - 1))
		          ->take($data['limit']);
		}

		$result = $query->get();

		// get the multiple stock piler fullname
	 

		DebugHelper::log(__METHOD__, $result);
		return $result;

		
	}
	public static function getStocktransferPickUnlistedReport()
		{

			$query = DB::SELECT(DB::RAW("SELECT COALESCE(wms_product_lists.sku,'No Available') as sku, COALESCE(wms_store_return_detail.sku, 'No Available') as upc, COALESCE(wms_product_lists.description, 'No Available') as description,
wms_users.firstname, wms_users.lastname, delivered_qty, wms_store_return_detail.received_qty, wms_store_return_detail.created_at, wms_stores.store_name, wms_store_return.so_no
FROM `wms_store_return`
LEFT JOIN wms_store_return_detail on wms_store_return_detail.so_no = wms_store_return.so_no
left join wms_users on wms_store_return.assigned_to_user_id = wms_users.id
left JOIN wms_product_lists on wms_store_return_detail.sku = wms_product_lists.upc
left join wms_stores on wms_store_return.from_store_code = wms_stores.store_code
WHERE 
delivered_qty = 0 and wms_store_return.assigned_to_user_id != 0 and wms_store_return.so_status = 23 and wms_product_lists.id is NULL"));

			return $query;
		}	
	public static function getStoreReturnTLnumbersync()
    {
         
        $query=DB::table('store_return')
            ->where('store_return.so_status', 20)
            ->where('store_return.assigned_to_user_id','!=', 0)
            ->update(['store_return.so_status' =>'21']);
    }
	
	public static function updateStatusstocktransfer($tl_number)
	{
		$query=DB::table('store_return')
			->where('store_return.so_no',$tl_number)
            ->where('store_return.so_status', 22)
            ->where('store_return.assigned_to_user_id','!=', 0)
            ->update(['store_return.so_status' =>'23']);
    }
	
	public static function getCount($data = array()){
		$query = StoreReturn::join('stores', 'store_return.from_store_code', '=', 'stores.store_code', 'LEFT')
			->join('dataset', 'so_status', '=', 'dataset.id');

		if( CommonHelper::hasValue($data['filter_so_no']) ) $query->where('so_no', 'LIKE', '%'.$data['filter_so_no'].'%');
		if( CommonHelper::hasValue($data['filter_store']) ) $query->where('store_return.store_code', 'LIKE', $data['filter_store']);
		if( CommonHelper::hasValue($data['filter_created_at']) ) $query->where('store_return.created_at', 'LIKE', '%'.$data['filter_created_at'].'%');
		if( CommonHelper::hasValue($data['filter_status']) ) $query->where('data_value', 'LIKE', '%'.$data['filter_status'].'%');

		return $query->count();
	}

	 public static function getStockTransferList($data= array(), $getCount=false)
    {
      
		// echo "<pre>"; print_r($data); die();
    	  $query = StoreReturn::select('store_return.to_store_code','store_return.from_store_code','store_return.date_entry','store_return.so_no','store_return.so_status','store_return.updated_at','dataset.*','stores.store_name','users.firstname','users.lastname','store_return_detail.delivered_qty as quantity_to_pick','store_return_detail.received_qty as moved_qty')
    	  	->join('store_return_detail','store_return.so_no','=','store_return_detail.so_no', 'LEFT')
        	->join('users','store_return.assigned_to_user_id','=','users.id','LEFT')
           ->join('stores', 'store_return.from_store_code','=','stores.store_code','LEFT')
			->join('dataset', 'store_return.so_status', '=', 'dataset.id');

		if( CommonHelper::hasValue($data['filter_doc_no']) ) $query->where('store_return.so_no', 'LIKE', '%'. $data['filter_doc_no'] . '%');
	 
 		if( CommonHelper::hasValue($data['filter_date_entry']) ) $query->where('store_return.date_entry',  'LIKE', '%'. $data['filter_date_entry'] . '%');
		if( CommonHelper::hasValue($data['filter_store']) ) $query->where('from_store_code', '=',  $data['filter_store']);
		if( CommonHelper::hasValue($data['filter_store_name']) ) $query->where('store_return.to_store_code', '=',  $data['filter_store_name']);
		if( CommonHelper::hasValue($data['filter_stock_piler']) ) $query->whereRaw('find_in_set('. $data['filter_stock_piler'] . ',assigned_to_user_id) > 0'); 

        
		if( CommonHelper::hasValue($data['sort']) && CommonHelper::hasValue($data['order']))  { 
			$query->orderBy($data['sort'], $data['order']);
		}


		if( CommonHelper::hasValue($data['limit']) && CommonHelper::hasValue($data['page']) && !$getCount)  {
			$query->skip($data['limit'] * ($data['page'] - 1))
		          ->take($data['limit']);
		}

		$query->groupBy('store_return.so_no');
		$result = $query->get();
        DebugHelper::log(__METHOD__, $result);

		// get the multiple stock piler fullname
		foreach ($result as $key => $picklist) {
			$assignedToUserId       = explode(',', $picklist->assigned_to_user_id);
			$getUsers               = User::getUsersFullname($assignedToUserId);
			$result[$key]->fullname = implode(', ', array_map(function ($entry) { return $entry['name']; }, $getUsers));
		}

		if($getCount) return count($result);
		return $result;
	 

    }
    public static function assignToStockPiler123($docNo = '', $data = array())
	{
		$query = StoreReturn::where('so_no', '=', $docNo)->update($data);
	}
   

	public static function getSOInfo($so_id = NULL) {
		$query = StoreReturn::join('dataset', 'store_return.so_status', '=', 'dataset.id', 'LEFT')
					->where('store_return.id', '=', $so_id);

		$result = $query->get(array(
									'store_return.*',
									'dataset.data_display',
								)
							);

		return $result[0];
	}

	public static function getInfoBySoNo($data)
	{
		return StoreReturn::whereIn('so_no', $data)->get()->toArray();
	}

	public static function assignToStockPiler($soNo = '', $data = array())
	{
		$query = StoreReturn::where('so_no', '=', $soNo)->update($data);
	}
	public static function getSOTLNumber($data)
	{
		return StoreReturn::whereIn('so_no', $data)->get()->toArray();
	}

	public static function getStoreList(){
		$storeList = StoreReturn::lists('store_code');				
		return $storeList;
	}

	/***********************00000*Methods for API only*00000*******************************/
	public static function getListByPiler($pilerId)
	{
		return StoreReturn::whereRaw('find_in_set('. $pilerId . ',assigned_to_user_id) > 0')
			->where('data_code', '=', 'SR_STATUS_TYPE')
			->where('data_value', '<>', 'closed')
			->join('store_return_detail', 'store_return.so_no', '=', 'store_return_detail.so_no')
			->join('stores', 'stores.store_code', '=', 'store_return.store_code')
			->join('dataset', 'store_return.so_status', '=', 'dataset.id')
			->groupBy('store_return.so_no')
			->get(array('store_return.so_no','stores.store_name', 'store_return.store_code', 'data_value as status', 'slot_code'))
			->toArray();
	}

	public static function updateStatus($soNo, $soStatus, $slot_code = '')
	{
		$params = array();
		$params = array( "so_status" => $soStatus, "updated_at" => date('Y-m-d H:i:s'));
		if ( !empty($slot_code) ) $params['slot_code'] = $slot_code;

		return StoreReturn::where('so_no', '=', $soNo)->update($params);
	}
}