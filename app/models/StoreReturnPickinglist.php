<?php

class StoreReturnPickinglist extends Eloquent {

	protected $table = 'store_return_pickinglist';
	protected $fillable = array('sku', 'doc_no');

	/********************Methods for CMS only**************************/
	public static function getTosubloc()
	{
		$query = DB::select(DB::raw("SELECT wms_store_return_pickinglist.to_store_code, wms_stores.store_name from wms_store_return_pickinglist left join wms_stores on wms_store_return_pickinglist.to_store_code = wms_stores.store_name"));

		return $query;

	}
	public static function getUpdateDateMod($move_doc_number, $ship_date)
	{

		$query = DB::SELECT(DB::raw("UPDATE wms_store_return_pickinglist set ship_date='$ship_date' WHERE move_doc_number='$move_doc_number'"));

	}
	public static function getStorelocation($doc_num)
	{
		$query=DB::table('store_return_pickinglist')
				->select('store_name','store_name as str_nem')
				->join('stores','store_return_pickinglist.from_store_code','=','stores.store_code','LEFT')
				->WHERE('move_doc_number',  $doc_num);

				return $query;
	}
	public static function getPicklistBoxes($doc_num)
	{
		/*$box = StoreReturnPickinglist::select(DB::raw('sum(wms_box_details.moved_qty) as total_qty'),'box_details.box_code',
                            'store_return_pick_details.sku as upc','store_return_pick_details.from_store_code','store_return_pick_details.to_store_code','stores.store_code', 'stores.store_name','store_return_pickinglist.move_doc_number',
                            'product_lists.description','product_lists.dept_code','product_lists.sub_dept','product_lists.class','product_lists.sub_class','box.box_number','box.box_total','store_return_pickinglist.ship_date')
            ->join('store_return_pick_details', 'store_return_pick_details.move_doc_number', '=', 'store_return_pickinglist.move_doc_number')
			->join('box_details', 'store_return_pick_details.id', '=', 'box_details.subloc_transfer_id','LEFT')
			 ->join('box', 'box_details.box_code','=','box.box_code','LEFT')
            ->join('product_lists','product_lists.upc','=','store_return_pick_details.sku','LEFT')

			->join('stores','store_return_pick_details.from_store_code','=','stores.store_code','LEFT')
			->where('store_return_pickinglist.move_doc_number','=', $doc_num)
			->groupBy('box_details.box_code')
			->get();

	 
		return $box;*/


		$query= DB::SELECT(DB::raw("SELECT GROUP_CONCAT(DISTINCT(wms_store_return_pick_details.move_doc_number) SEPARATOR ', ' ) as move_doc_number ,wms_box_details.box_code,sum(wms_box_details.moved_qty) as total_qty, sku, ship_date, from_store_code, to_store_code, wms_stores.store_code, wms_stores.store_name
					from wms_store_return_pickinglist
					INNER JOIN wms_box ON wms_store_return_pickinglist.move_doc_number=wms_box.tl_number
					inner join wms_box_details on wms_box.box_code=wms_box_details.box_code
					INNER JOIN wms_store_return_pick_details on wms_box_details.subloc_transfer_id=wms_store_return_pick_details.id
					left join wms_stores on wms_store_return_pick_details.to_store_code = wms_stores.store_code
					where wms_store_return_pickinglist.move_doc_number='$doc_num'
					GROUP BY wms_box_details.box_code
 
"));


									return $query;

 

	}
	public static function getStockTransferTLNumberPosted($data = array(), $getCount = false)
	{

		$query = DB::table('box')
			->select('store_return_pickinglist.*', 'stores.store_name', 'to_store_code')
			
			->join('store_return_pick_details','store_return_pickinglist.move_doc_number','=','store_return_pick_details.move_doc_number','LEFT')
			->join('stores','store_return_pick_details.from_store_code','=','stores.store_code','LEFT')
			->where('type','=','1')
			->where('pl_status','=','18');

/*$this->data['stores']                 = Store::lists( 'store_name', 'store_code');
 		$this->data['po_info']                 = Store::lists( 'store_name','store_name');*/

		/*if( CommonHelper::hasValue($data['filter_doc_no']) ) $query->where('load_details.move_doc_number', 'LIKE', '%'. $data['filter_doc_no'].'%');*/

	if( CommonHelper::hasValue($data['filter_doc_no']) ) $query->where('store_return_pickinglist.move_doc_number', 'LIKE', '%'. $data['filter_doc_no'] . '%');
	
		if( CommonHelper::hasValue($data['filter_store']) ) $query->where('stores.store_code', '=',  $data['filter_store']);
		if( CommonHelper::hasValue($data['filter_store_name']) ) $query->where('store_return_pick_details.to_store_code', '=',  $data['filter_store_name']);


 	if( CommonHelper::hasValue($data['sort']) && CommonHelper::hasValue($data['order']))  {
            if($data['sort'] == 'doc_no') $data['sort'] = 'store_return_pickinglist.move_doc_number';
            $query->orderBy($data['sort'], $data['order']);
        }


        if( CommonHelper::hasValue($data['limit']) && CommonHelper::hasValue($data['page']) && !$getCount)  {
            $query->skip($data['limit'] * ($data['page'] - 1))
                  ->take($data['limit']);
        }

        $query->groupBy('store_return_pickinglist.move_doc_number');
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
	 	public static function getremovedTL($tlnumber , $loadnumber) {
		$query = DB::select(DB::raw("DELETE FROM wms_load_details  where load_code = '$loadnumber' and move_doc_number = '$tlnumber'"));
 
	}
	public static function getremovedTLUpdate($tlnumber , $loadnumber) {
		$query = DB::select(DB::raw("UPDATE wms_store_return_pickinglist set type='0' where move_doc_number='$tlnumber'"));
 
	}

	public static function assignToTLnumber($tlnumber , $loadnumber) {
	
		$query1 = DB::select(DB::raw("INSERT INTO `wms_load_details`(`id`, `load_code`, `move_doc_number`,`sync_status`, `is_load`, `created_at`, `updated_at`, `jda_sync_date`) VALUES ('','$loadnumber','$tlnumber','','','','','')"));
	}
   public static function assignToTL($tlnumber , $loadnumber) {
		$query = DB::select(DB::raw("UPDATE wms_store_return_pickinglist set type='1' where move_doc_number='$tlnumber'"));
 
	}
	public static function getStocktransferPickingListv2($data= array(), $getCount=false)
	{

 $query = StoreReturnPickinglist::select('store_return_pickinglist.*','store_return_pickinglist.move_doc_number', 'users.firstname','users.lastname','dataset.*','stores.store_name','store_return_pick_details.from_store_code','stores.store_code','store_return_pick_details.to_store_code','dataset.data_value')
 			->join('store_return_pick_details','store_return_pickinglist.move_doc_number','=','store_return_pick_details.move_doc_number','LEFT')	
            ->join('users','store_return_pickinglist.assigned_to_user_id','=','users.id','left')
       		->join('stores','store_return_pick_details.to_store_code','=','stores.store_code','left')
			->join('dataset', 'store_return_pickinglist.pl_status', '=', 'dataset.id');

		if( CommonHelper::hasValue($data['filter_doc_no']) ) $query->where('store_return_pickinglist.move_doc_number', 'LIKE', '%'. $data['filter_doc_no'] . '%');
		if( CommonHelper::hasValue($data['filter_status']) ) $query->where('data_value', '=', $data['filter_status'])->where('data_code', '=', 'PICKLIST_STATUS_TYPE');
		if( CommonHelper::hasValue($data['filter_store']) ) $query->where('stores.store_code', '=',  $data['filter_store']);
		if( CommonHelper::hasValue($data['filter_store_name']) ) $query->where('store_return_pick_details.to_store_code', '=',  $data['filter_store_name']);
		if( CommonHelper::hasValue($data['filter_stock_piler']) ) $query->whereRaw('find_in_set('. $data['filter_stock_piler'] . ',assigned_to_user_id) > 0');
        

		if( CommonHelper::hasValue($data['limit']) && CommonHelper::hasValue($data['page']) && !$getCount)  {
			$query->skip($data['limit'] * ($data['page'] - 1))
		          ->take($data['limit']);
		}

		$query->groupBy('store_return_pickinglist.move_doc_number');
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

	public static function updateStatusstocktransfer()
	{
		$query=DB::table('store_return_pickinglist')
            ->where('store_return_pickinglist.so_status', 22)
            ->where('store_return_pickinglist.assigned_to_user_id','!=', 0)
            ->update(['store_return_pickinglist.so_status' =>'23']);
    }
	public static function getStoreReturnPickandPackTLnumbersync()
    {
         
        $query=DB::table('store_return_pickinglist')
            ->where('store_return_pickinglist.pl_status', 15)
            ->where('store_return_pickinglist.assigned_to_user_id','!=', 0)
            ->update(['store_return_pickinglist.pl_status' =>'16']);
    }
	public static function getStoreReturnPickandPackTLnumbersyncclose($tl_number)
    {
         
        $query=DB::table('store_return_pickinglist')
        	->where('store_return_pickinglist.move_doc_number',$tl_number)
            ->where('store_return_pickinglist.pl_status', 17)
            ->where('store_return_pickinglist.assigned_to_user_id','!=', 0)
            ->update(['store_return_pickinglist.pl_status' =>'18']);
    }
	public static function getInfoBySoNo($data)
	{
		return StoreReturnPickinglist::whereIn('move_doc_number', $data)->get()->toArray();
	}
	public static function assignToStockPilerPickingmodel($docNo = '', $data = array())
	{
		$query = StoreReturnPickinglist::where('move_doc_number', '=', $docNo)->update($data);
	}
	public static function getStocktransferPickReport($data= array(), $getCount=false )
	{
		$query = StoreReturnPickinglist::select('store_return_pickinglist.to_store_code', 'stores.store_name','store_return_pickinglist.to_store_code', 'store_return_pickinglist.assigned_to_user_id','store_return_pickinglist.move_doc_number', 'store_return_pick_details.sku as upc','store_return_pick_details.quantity_to_pick', 'store_return_pick_details.moved_qty', 'users.firstname', 'users.lastname', 'product_lists.sku', 'product_lists.description', 'store_return_pick_details.created_at', DB::raw('wms_store_return_pick_details.moved_qty - wms_store_return_pick_details.quantity_to_pick as variance'))
->join('store_return_pick_details','store_return_pickinglist.move_doc_number','=','store_return_pick_details.move_doc_number','left')
->join('stores','store_return_pickinglist.from_store_code','=','stores.store_code','left')
->join('users','store_return_pickinglist.assigned_to_user_id','=','users.id','left') 
->join('product_lists','store_return_pick_details.sku','=','product_lists.upc','left') 
->where('store_return_pickinglist.pl_status','=',18)
->where('store_return_pick_details.quantity_to_pick','<>','store_return_pick_details.moved_qty') 
->where('store_return_pickinglist.assigned_to_user_id','!=', 0);
 
 if( CommonHelper::hasValue($data['filter_doc_no']) ) $query->where('store_return_pickinglist.move_doc_number', 'LIKE', '%'. $data['filter_doc_no'] . '%');


		if( CommonHelper::hasValue($data['sort']) && CommonHelper::hasValue($data['order']))  {
			if($data['sort'] == 'doc_no') $data['sort'] = 'store_return_pickinglist.move_doc_number';
			$query->orderBy($data['sort'], $data['order']);
		}


		if( CommonHelper::hasValue($data['limit']) && CommonHelper::hasValue($data['page']) && !$getCount)  {
			$query->skip($data['limit'] * ($data['page'] - 1))
		          ->take($data['limit']);
		}

		 
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

}