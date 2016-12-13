<?php

class Picklist extends Eloquent {

	protected $table = 'picklist';

	/*****CMS Functions*****/
	public static function getPickList($picklistDocNo)
	{
		$picklist = Picklist::where('picklist.move_doc_number', '=', $picklistDocNo)
		->join('picklist_details', 'picklist.move_doc_number','=', 'picklist.move_doc_number')
		->where('picklist_details.move_doc_number', '=', $picklistDocNo)
			->first()->toArray();
		return $picklist;
	}
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

	public static  function getPRINTMTS($picklist_doc)
	{
			$query = DB::select(DB::raw("SELECT SUM(wms_box_details.moved_qty) as total_qty, wms_stores.store_name, wms_stores.store_code, wms_box.move_doc_number as picklist_doc
FROM wms_box
LEFT JOIN wms_stores on wms_box.store_code = wms_stores.store_code
LEFT JOIN wms_box_details on wms_box.box_code = wms_box_details.box_code
LEFT join wms_picklist_details on wms_box_details.picklist_detail_id = wms_picklist_details.id
WHERE wms_box.move_doc_number='$picklist_doc' AND wms_picklist_details.move_doc_number='$picklist_doc' GROUP BY wms_box.box_code"));
 
		return $query;
	}
	public static function getPRINTMTSasdf($picklist_doc)
	{
		$query = DB::select(DB::raw("SELECT sum(wms_box_details.moved_qty) as total_qty, wms_picklist.move_doc_number, wms_box.box_code, wms_stores.store_name, wms_stores.store_code
				from wms_picklist 
				left join wms_box on wms_picklist.move_doc_number = wms_box.move_doc_number 
				LEFT JOIN wms_box_details on wms_box.box_code = wms_box_details.box_code 
                LEFT JOIN wms_picklist_details on wms_picklist.move_doc_number = wms_picklist_details.move_doc_number
                LEFT JOIN wms_stores on wms_picklist_details.store_code = wms_stores.store_code
				where wms_picklist.move_doc_number='$picklist_doc'
                group BY wms_box.box_code"));


		return $query;
	}
	public static function getTLnumbersync()
    {
         
        $query=DB::table('Picklist')
            ->where('picklist.pl_status', 15)
            ->where('Picklist.assigned_to_user_id','!=', 0)
            ->update(['picklist.pl_status' =>'16']);
    }
	public static function getpostedtoStore($doc_num, $boxcode)
	{
		$query=DB::select(DB::raw("INSERT INTO wms_store_order_detail (so_no, sku, ordered_qty, packed_qty, delivered_qty) 
			SELECT wms_picklist_details.move_doc_number, wms_picklist_details.sku, wms_picklist_details.quantity_to_pick as ordered_qty, wms_picklist_details.moved_qty, '0' 
			from wms_picklist_details
			where wms_picklist_details.move_doc_number='$doc_num'"));


	}
	public static function getUpdateDateMod($move_doc_number, $ship_date)
	{

		$query = DB::SELECT(DB::raw("UPDATE wms_picklist set ship_date='$ship_date' WHERE move_doc_number='$move_doc_number'"));

	}
	/*public static function getpostedtoBoxOrder($doc_num)
	{
		$query=DB::select(DB::raw("INSERT INTO wms_store_detail_box (move_doc_number, box_code, upc, quantity_packed,  box_status, quantity_pick) 
			SELECT wms_box.move_doc_number, wms_box.box_code, wms_picklist_details.sku, wms_box_details.moved_qty, wms_box.boxstatus_unload, '0' 
			from wms_box
			inner join wms_box_details on wms_box.box_code = wms_box_details.box_code
			left join wms_picklist_details on wms_box_details.picklist_detail_id = wms_picklist_details.id
			where wms_box.move_doc_number='$doc_num'"));


	}*/
	 
	public static function getPickingListvVariance($data= array(), $getCount=false)
	{
		 


		$query = Picklist::SELECT('picklist.move_doc_number', 'picklist.pl_status','store_name','picklist_details.sku as upc', 'picklist_details.store_code', 'product_lists.sku', 'product_lists.description','from_slot_code', 'users.firstname','users.lastname','picklist_details.quantity_to_pick','picklist_details.sku as upc','picklist.updated_at', 'picklist_details.created_at','picklist_details.moved_qty','picklist_details.quantity_to_pick',DB::raw('CONCAT(firstname, " ", lastname) as fullname, (moved_qty - quantity_to_pick) as variance'))
->join('picklist_details','picklist_details.move_doc_number','=','picklist.move_doc_number','LEFT')
->JOIN('product_lists','picklist_details.sku','=','product_lists.upc','LEFT')
->JOIN('users','picklist.assigned_to_user_id','=','users.id','LEFT')
->JOIN('stores','picklist_details.store_code','=','stores.store_code','LEFT')
->WHERE('picklist.assigned_to_user_id', '!=', '0')
->where('picklist.pl_status','=' ,'18')
->where('picklist_details.quantity_to_pick','<>','picklist_details.moved_qty');

	if( CommonHelper::hasValue($data['filter_doc_no']) ) $query->where('picklist.move_doc_number', 'LIKE', '%'. $data['filter_doc_no'] . '%');

	 

	if( CommonHelper::hasValue($data['filter_entry_date']) ) $query->where('picklist.updated_at', 'LIKE', '%'. $data['filter_entry_date'] . '%');

	
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
	public static function getPickingListv2($data= array(), $getCount=false)
	{
		// $query = Picklist::select(DB::raw('wms_picklist.*, sum(wms_picklist_details.move_to_shipping_area) as sum_moved, sum(wms_picklist_details.assigned_user_id) as sum_assigned, store_code' ))

		$query = Picklist::select('picklist.*','division.*','picklist_details.*','dataset.*','picklist_details.updated_at as action_date')
            ->join('picklist_details', 'picklist_details.move_doc_number', '=', 'picklist.move_doc_number')
          	->join('division','picklist_details.division','=','division.id', 'LEFT')
			->join('dataset', 'picklist.pl_status', '=', 'dataset.id');

		if( CommonHelper::hasValue($data['filter_doc_no']) ) $query->where('picklist.move_doc_number', 'LIKE', '%'. $data['filter_doc_no'] . '%');
		if( CommonHelper::hasValue($data['filter_type']) ) $query->where('type', '=',  $data['filter_type']);
		if( CommonHelper::hasValue($data['filter_status']) ) $query->where('data_value', '=', $data['filter_status'])->where('data_code', '=', 'PICKLIST_STATUS_TYPE');
		if( CommonHelper::hasValue($data['filter_store']) ) $query->where('store_code', '=',  $data['filter_store']);
		/*if( CommonHelper::hasValue($data['filter_stock_piler']) ) $query->whereRaw('find_in_set('. $data['filter_stock_piler'] . ',assigned_to_user_id) > 0'); */
        


        
		if( CommonHelper::hasValue($data['sort']) && CommonHelper::hasValue($data['order']))  {
			if($data['sort'] == 'doc_no') $data['sort'] = 'picklist.move_doc_number';
			$query->orderBy($data['sort'], $data['order']);
		}


		if( CommonHelper::hasValue($data['limit']) && CommonHelper::hasValue($data['page']) && !$getCount)  {
			$query->skip($data['limit'] * ($data['page'] - 1))
		          ->take($data['limit']);
		}

		$query->groupBy('picklist.move_doc_number');
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

	public static function getPickingList($picklist_doc, $data= array(), $getCount=false)
	{
		$query = Picklist::select(DB::raw('SUM(wms_box_details.moved_qty) as total_qty, wms_stores.store_name, wms_stores.store_code, wms_box.move_doc_number as picklist_doc' ))
			->join('stores','box.store_code','=','stores.store_code','LEFT')
			->join('box_details','box.box_code','=','box_details.box_code','LEFT')
			->join('picklist_details','box_details.picklist_detail_id','=','picklist_details.id','left')
			->where('box.move_doc_number', $picklist_doc)
			->WHERE('picklist_details',$picklist_doc)
			->groupBy('box.box_code');
/*
		if( CommonHelper::hasValue($data['filter_doc_no']) ) $query->where('picklist.move_doc_number', 'LIKE', '%'. $data['filter_doc_no'] . '%');
		if( CommonHelper::hasValue($data['filter_type']) ) $query->where('type', '=',  $data['filter_type']);
		if( CommonHelper::hasValue($data['filter_status']) ) $query->where('pl_status', '=', $data['filter_status']);

		if( CommonHelper::hasValue($data['sort']) && CommonHelper::hasValue($data['order']))  {
			if($data['sort'] == 'doc_no') $data['sort'] = 'picklist.move_doc_number';
			$query->orderBy($data['sort'], $data['order']);
		}


		if( CommonHelper::hasValue($data['limit']) && CommonHelper::hasValue($data['page']) && !$getCount)  {
			$query->skip($data['limit'] * ($data['page'] - 1))
		          ->take($data['limit']);
		}
		$query->groupBy('picklist.move_doc_number');
		$result = $query->get();*/

		return $query;
	}

	public static function getPickingListCount($data)
	{
		$query = Picklist::select('*');
		if( CommonHelper::hasValue($data['filter_doc_no']) ) $query->where('move_doc_number', 'LIKE', '%'. $data['filter_doc_no'] . '%');
		if( CommonHelper::hasValue($data['filter_type']) ) $query->where('type', '=',  $data['filter_type']);
		if( CommonHelper::hasValue($data['filter_status']) ) $query->where('pl_status', '=', $data['filter_status']);

		$result = $query->count();

		return $result;

	}

	

	public static function changeToStore($docNo)
	{
		$picklistDetails = Picklist::select(DB::raw('sum(wms_picklist_details.move_to_shipping_area) as sum_moved, sum(wms_picklist_details.assigned_user_id) as sum_assigned'))
			->join('picklist_details', 'picklist_details.move_doc_number', '=', 'picklist.move_doc_number')
			// ->whereI('picklist.move_doc_number', '=',$docNo)
			->whereIn('picklist.move_doc_number', $docNo)
			->groupBy('picklist.move_doc_number')
			->first();
		DebugHelper::log(__METHOD__, $picklistDetails);
		if(count($picklistDetails) === 0) throw new Exception("Document number does not exists");
		if($picklistDetails['sum_moved'] > 0 || $picklistDetails['sum_assigned'] > 0) {
			throw new Exception("This picklist cannot be changed to type store");
		}
		// Picklist::where('move_doc_number', '=', $docNo)
		Picklist::whereIn('move_doc_number', $docNo)
			->update(array(
				'type'		=> 'store',
				'updated_at'=>	date('Y-m-d H:i:s')));
		return;
	}
  	public static function assignToLoadnumber($box_code = '', $data = array())
    {
        $query = picklist::where('pl_status', '=', $box_code)->update($data);
        DebugHelper::log(__METHOD__, $query);
        }


   public static function assignToTL($tlnumber , $loadnumber) {
		$query = DB::select(DB::raw("UPDATE wms_box set assign_to_load='1' where move_doc_number='$tlnumber'"));
 
	}
	
	public static function getremovedTL($tlnumber , $loadnumber) {
		$query = DB::select(DB::raw("DELETE FROM wms_load_details  where load_code = '$loadnumber' and move_doc_number = '$tlnumber'"));
 
	}
	public static function getremovedTLUpdate($tlnumber , $loadnumber) {
		$query = DB::select(DB::raw("UPDATE wms_picklist set type='0' where move_doc_number='$tlnumber'"));
 
	}
     
     public static function assignToTLnumber($tlnumber , $loadnumber) {
	
		$query1 = DB::select(DB::raw("INSERT INTO `wms_load_details`(`id`, `load_code`, `move_doc_number`, `sync_status`, `is_load`, `created_at`, `updated_at`, `jda_sync_date`) VALUES ('','$loadnumber','$tlnumber','','','','','')"));
	}
	public static function getInfoByDocNos($data)
	{
		return Picklist::whereIn('move_doc_number', $data)->get()->toArray();
	}
	public static function getTlnumberPosted($data)
	{
		return Picklist::where('move_doc_number', $data)->update('pl_status','=','19')->get()->toArray();
	}
 

	public static function assignToStockPiler($docNo = '', $data = array())
	{
		$query = Picklist::where('move_doc_number', '=', $docNo)->update($data);
	}

	/***************************Methods for API only*********************************/
	public static function getListByPiler($pilerId)
	{
		return Picklist::whereRaw('find_in_set('. $pilerId . ',assigned_to_user_id) > 0')
			->where('data_code', '=', 'PICKLIST_STATUS_TYPE')
			->where('data_value', '<>', 'closed')
			->join('picklist_details', 'picklist.move_doc_number', '=', 'picklist_details.move_doc_number')
			->join('stores', 'stores.store_code', '=', 'picklist_details.store_code')
			->join('dataset', 'picklist.pl_status', '=', 'dataset.id')
			->groupBy('picklist.move_doc_number')
			->get(array('picklist_details.so_no as transfer_no','picklist.move_doc_number','stores.store_name', 'picklist_details.store_code', 'data_value as status'))
			->toArray();
	}

	public static function updateStatusToMoved($docNo)
	{
		return Picklist::where('move_doc_number','=', $docNo)
					->update(array('pl_status' =>  Config::get('picking_statuses.moved')));
	}

	public static function updateStatus($docNo, $plStatus)
	{
		return Picklist::where('move_doc_number', '=', $docNo)
					->update(array(
						"pl_status" => $plStatus,
						"updated_at" => date('Y-m-d H:i:s')
					));
	}

	public static function getassigntlnumber($docNo)
	{
		return Picklist::where('move_doc_number', '=', $docNo)
					->update(array(
						"pl_status" => '19',
						"updated_at" => date('Y-m-d H:i:s')
					));
	}


	public static function getPicklistWithoutDiscrepancies()
	{
		$status_options = Dataset::where("data_code", "=", "PICKLIST_STATUS_TYPE")->get()->lists("id", "data_value");

		$query = Picklist::join('picklist_details', 'picklist_details.move_doc_number', '=', 'picklist.move_doc_number')
			->where('pl_status', '=', $status_options['done'])
			// ->where('quantity_to_pick', '=', 'moved_qty')
			// ->groupBy('picklist.move_doc_number')
			->get()->toArray();


		echo "<pre>"; print_r($query); die();
	}

	public static function getPicklistBoxes($doc_num){
		/*$box = Picklist::select(DB::raw('sum(wms_box_details.moved_qty) as total_qty'),'box_details.box_code',
                            'picklist_details.sku as upc','picklist_details.store_code','picklist_details.so_no','picklist_details.store_code','stores.store_name', 'picklist.move_doc_number',
                            'product_lists.description','product_lists.dept_code','product_lists.sub_dept','product_lists.class','product_lists.sub_class','box.box_number','box.box_total','picklist.created_at')
            ->join('picklist_details', 'picklist_details.move_doc_number', '=', 'picklist.move_doc_number')
           
			->join('box_details', 'picklist_details.id', '=', 'box_details.picklist_detail_id')
			 ->join('box', 'box_details.box_code','=','box.box_code','LEFT')
            ->join('product_lists','product_lists.upc','=','picklist_details.sku','LEFT')

			->join('stores','picklist_details.store_code','=','stores.store_code','LEFT')
			->where('picklist.move_doc_number','=', $doc_num)
			->groupBy('box_details.box_code')
			->get();*/

				$box= DB::SELECT(DB::raw("SELECT GROUP_CONCAT(DISTINCT(wms_picklist_details.move_doc_number) SEPARATOR ', ' ) as MASTER_EDU,wms_box_details.box_code,sum(wms_box_details.moved_qty) as qty, ship_date, wms_stores.store_code, wms_stores.store_name
					from wms_picklist
					INNER JOIN wms_box ON wms_picklist.move_doc_number=wms_box.move_doc_number
					inner join wms_box_details on wms_box.box_code=wms_box_details.box_code
					INNER JOIN wms_picklist_details on wms_box_details.picklist_detail_id=wms_picklist_details.id
					left join wms_stores on wms_picklist_details.store_code = wms_stores.store_code
					where wms_picklist.move_doc_number='$doc_num'
					GROUP BY wms_box_details.box_code"));
									return $box;
	}
}
