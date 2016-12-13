<?php


class ReverseLogistic extends Eloquent {

	protected $table = 'reverse_logistic';

	 public static function getReverseTLnumbercclose($tl_number)
    {
         
        $query=DB::table('reverse_logistic')
        	->where('reverse_logistic.move_doc_number',$tl_number)
            ->where('reverse_logistic.so_status', 22)
            ->where('reverse_logistic.assigned_to_user_id','!=', 0)
            ->update(['reverse_logistic.so_status' =>'23']);
    }
    public static function getRLVarianceReport($data= array(), $getCount=false)
    {
    	$query = ReverseLogistic::select('reverse_logistic.move_doc_number', 'stores.store_name', 'reverse_logistic_det.upc','reverse_logistic_det.created_at', 'users.firstname', 'users.lastname', 'product_lists.sku','reverse_logistic_det.delivered_qty','reverse_logistic.updated_at','reverse_logistic_det.moved_qty','product_lists.description',DB::raw('CONCAT(firstname, " ", lastname) as fullname, (moved_qty - delivered_qty) as variance'))
->JOIN('reverse_logistic_det','reverse_logistic.move_doc_number','=','reverse_logistic_det.move_doc_number','LEFT')
->JOIN('users','reverse_logistic.assigned_to_user_id','=','users.id','LEFT')
->JOIN('stores','reverse_logistic.from_store_code','=','stores.store_code','LEFT')
->JOIN('product_lists','reverse_logistic_det.upc','=','product_lists.upc','LEFT')
->where('reverse_logistic.so_status','=',23)
->where('reverse_logistic.assigned_to_user_id','!=', 0)
->where('reverse_logistic_det.delivered_qty','<>','reverse_logistic_det.moved_qty');

	if( CommonHelper::hasValue($data['filter_doc_no']) ) $query->where('reverse_logistic.move_doc_number', 'LIKE', '%'. $data['filter_doc_no'] . '%');

	 

	if( CommonHelper::hasValue($data['filter_entry_date']) ) $query->where('reverse_logistic.updated_at', 'LIKE', '%'. $data['filter_entry_date'] . '%');

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
    public static function getRLUnlistedReport()
    {
    	$query = DB::select(DB::raw("SELECT wms_reverse_logistic.move_doc_number,wms_reverse_logistic_det.upc, wms_stores.store_name,COALESCE(wms_product_lists.sku, 'No Available') as sku, COALESCE(wms_product_lists.description, 'No Available') as description, wms_users.firstname, wms_users.lastname, wms_reverse_logistic_det.created_at, wms_reverse_logistic_det.moved_qty from  wms_reverse_logistic_det left  join wms_reverse_logistic on wms_reverse_logistic_det.move_doc_number = wms_reverse_logistic.move_doc_number LEFT JOIN wms_stores on wms_reverse_logistic.from_store_code = wms_stores.store_code LEFT JOIN wms_product_lists on wms_reverse_logistic_det.upc = wms_product_lists.upc LEFT JOIN wms_users on wms_reverse_logistic.assigned_to_user_id = wms_users.id where wms_reverse_logistic_det.delivered_qty=0 and wms_reverse_logistic.assigned_to_user_id != 0 and wms_reverse_logistic.so_status=23 and wms_product_lists.id is null"));

    	return $query;
    }
    public static function getReverseTLnumbersync()
    {
         
        $query=DB::table('reverse_logistic')
            ->where('reverse_logistic.so_status', 20)
            ->where('reverse_logistic.assigned_to_user_id','!=', 0)
            ->update(['reverse_logistic.so_status' =>'21']);
    }
	 public static function getReverseLogisticList($data= array(), $getCount=false)
    {
      
		// echo "<pre>"; print_r($data); die();
    	  $query = ReverseLogistic::select('reverse_logistic.created_at','reverse_logistic.move_doc_number','reverse_logistic.so_status','reverse_logistic.updated_at','users.firstname','users.lastname','dataset.data_display','dataset.data_value','dataset.data_display','stores.store_name')
    	  ->join('stores','reverse_logistic.from_store_code','=','stores.store_code','left')
    	  ->join('users','reverse_logistic.assigned_to_user_id','=','users.id','left')
    	  ->join('dataset','reverse_logistic.so_status','=','dataset.id','left');

		if( CommonHelper::hasValue($data['filter_doc_no']) ) $query->where('reverse_logistic.move_doc_number', 'LIKE', '%'. $data['filter_doc_no'] . '%');
		if( CommonHelper::hasValue($data['filter_entry_date']) ) $query->where('reverse_logistic.created_at',  'LIKE', '%'. $data['filter_entry_date'] . '%');
 
		if( CommonHelper::hasValue($data['filter_store']) ) $query->where('reverse_logistic.from_store_code',  'LIKE', '%'. $data['filter_store'] . '%');
		if( CommonHelper::hasValue($data['filter_stock_piler']) ) $query->whereRaw('find_in_set('. $data['filter_stock_piler'] . ',assigned_to_user_id) > 0'); 
        
		if( CommonHelper::hasValue($data['sort']) && CommonHelper::hasValue($data['order']))  {
			if($data['sort'] == 'doc_no') $data['sort'] = 'reverse_logistic.move_doc_number';
			if($data['sort'] == 'doc_no') $data['sort'] = 'reverse_logistic.created_at';
			$query->orderBy($data['sort'], $data['order']);
		}


		if( CommonHelper::hasValue($data['limit']) && CommonHelper::hasValue($data['page']) && !$getCount)  {
			$query->skip($data['limit'] * ($data['page'] - 1))
		          ->take($data['limit']);
		}

		$query->groupBy('reverse_logistic.move_doc_number');
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

    public static function getInfoBySoNo($data)
	{
		return ReverseLogistic::whereIn('move_doc_number', $data)->get()->toArray();
	}
	public static function assignToStockPilerReverse($soNo = '', $data = array())
	{
		$query = ReverseLogistic::where('move_doc_number', '=', $soNo)->update($data);
	}
}


	
