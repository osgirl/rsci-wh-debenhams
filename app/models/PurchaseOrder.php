<?php

class PurchaseOrder extends Eloquent {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */


	protected $table = 'purchase_order_lists';

	public static function getAPIPoLists($data = array()) {
		$arrParams = array('data_code' => 'PO_STATUS_TYPE', 'data_value'=> 'closed');
		$piler_id  = (int) $data['stock_piler_id'];
		$po_status = Dataset::getType($arrParams)->toArray();

	$query = DB::select(DB::raw("SELECT wms_purchase_order_lists.*, wms_vendors.vendor_name,wms_purchase_order_details.division as 'division',wms_purchase_order_details.assigned_to_user_id as 'assigned_to_user_id' FROM wms_purchase_order_lists LEFT join wms_vendors on wms_purchase_order_lists.vendor_id = wms_vendors.id Left join wms_purchase_order_details on wms_purchase_order_lists.receiver_no=wms_purchase_order_details.receiver_no WHERE find_in_set($piler_id,wms_purchase_order_details.assigned_to_user_id) > 0 group by wms_purchase_order_lists.purchase_order_no"));
	/*
		$query = DB::select(DB::raw("SELECT wms_purchase_order_lists.vendor_id,wms_purchase_order_lists.receiver_no,wms_purchase_order_lists.purchase_order_no,wms_purchase_order_lists.destination,wms_purchase_order_lists.carton_id,wms_purchase_order_lists.total_qty,wms_purchase_order_lists.back_order,wms_purchase_order_lists.shipment_reference_no,wms_purchase_order_lists.container_id,wms_purchase_order_lists.invoice_no,wms_purchase_order_lists.invoice_amount,wms_purchase_order_lists.slot_code,wms_purchase_order_lists.delivery_date,wms_purchase_order_lists.datetime_done,wms_purchase_order_lists.latest_mobile_sync_date,wms_purchase_order_lists.created_at,wms_purchase_order_lists.updated_at,wms_purchase_order_lists.deleted_at, wms_vendors.vendor_name,wms_purchase_order_details.division as 'division',wms_purchase_order_details.assigned_to_user_id as 'assigned_to_user_id',wms_purchase_order_lists.id as 'id',wms_purchase_order_details.po_status FROM wms_purchase_order_lists LEFT join wms_vendors on wms_purchase_order_lists.vendor_id = wms_vendors.id Left join wms_purchase_order_details on wms_purchase_order_lists.receiver_no=wms_purchase_order_details.receiver_no WHERE find_in_set(34,wms_purchase_order_details.assigned_to_user_id) > 0 group by wms_purchase_order_lists.purchase_order_no"));
	*/
		return $query;
	}

	public static function getAPICount($data = array()) {
		$arrParams = array('data_code' => 'PO_STATUS_TYPE', 'data_value'=> 'closed');
		$piler_id  = (int) $data['stock_piler_id'];
		$po_status = Dataset::getType($arrParams)->toArray();

		// $query = PurchaseOrder::where('assigned_to_user_id', '=', $data['stock_piler_id'])
		// 			->where("po_status", "<>", $po_status['id']);
		$query = DB::select(DB::raw("SELECT wms_purchase_order_lists.*, wms_vendors.vendor_name,wms_purchase_order_details.division as 'division',wms_purchase_order_details.assigned_to_user_id as 'assigned_to_user_id' FROM wms_purchase_order_lists LEFT join wms_vendors on wms_purchase_order_lists.vendor_id = wms_vendors.id Left join wms_purchase_order_details on wms_purchase_order_lists.receiver_no=wms_purchase_order_details.receiver_no WHERE find_in_set($piler_id,wms_purchase_order_details.assigned_to_user_id) > 0 group by wms_purchase_order_lists.purchase_order_no"));

		return count($query);
	}

	public static function getPOInfo($receiver_no = NULL) {
		$query = DB::table('purchase_order_lists')
					// ->join('users', 'purchase_order_lists.assigned_to_user_id', '=', 'users.id', 'LEFT')
					->join('dataset', 'purchase_order_lists.po_status', '=', 'dataset.id', 'LEFT')
					->join('vendors', 'purchase_order_lists.vendor_id', '=', 'vendors.id', 'LEFT')
					->where('purchase_order_lists.receiver_no', '=', $receiver_no);

		$result = $query->get(array(
									'purchase_order_lists.*',
									'vendors.vendor_name',
									'dataset.data_display'
									// 'users.firstname',
									// 'users.lastname'
								)
							);

		// get the multiple stock piler fullname
		foreach ($result as $key => $po) {
			$assignedToUserId       = explode(',', $po->assigned_to_user_id);
			$getUsers               = User::getUsersFullname($assignedToUserId);
			$result[$key]->fullname = implode(', ', array_map(function ($entry) { return $entry['name']; }, $getUsers));
		}

		return $result[0];
	}

	/**
	* Gets PO lists with filters and ordering
	*
	* @example  PurchaseOrder::getPoLists({data});
	*
	* @param  data      array parameters passed by cms
	* @return array of po
	* Use :  cms
	*/

/*
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
*/
	public static function getPoListsdivision($data = array(), $getCount = FALSE) {
		$query = PurchaseOrder::getPOQuerydivision($data);
		// echo "<pre>"; print_r($data); die();

		if( CommonHelper::hasValue($data['limit']) && CommonHelper::hasValue($data['page']) && !$getCount)  {
			$query->skip($data['limit'] * ($data['page'] - 1))
		          ->take($data['limit']);
		}

		$result = $query->get(array(
									'purchase_order_lists.*',
									'vendors.vendor_name',
									'division',
									'dataset.data_display'
									// 'users.firstname',
									// 'users.lastname'
								)
							);

		DebugHelper::log(__METHOD__, $result);
		// get the multiple stock piler fullname
		foreach ($result as $key => $po) {
			$assignedToUserId       = explode(',', $po->assigned_to_user_id);
			$getUsers               = User::getUsersFullname($assignedToUserId);
			$result[$key]->fullname = implode(', ', array_map(function ($entry) { return $entry['name']; }, $getUsers));
		}

		if($getCount) return count($result);

		return $result;
	}

  public static function UpdateRPoListDetailUpdate($receiver_no,$division_id,$upc,$rqty) {
        $query = DB::select(DB::raw("update wms_purchase_order_details set quantity_delivered='$rqty',po_status=4 where receiver_no='$receiver_no' and dept_number='$division_id' and upc='$upc'"));
        return $query;
    }
	public static function getPOQuerydivision($data = array())
	{
		$query = DB::table('purchase_order_lists')
					
			->select('*',DB::raw('sum(quantity_ordered) as quantity_ordered1','dept_number'),DB::raw('sum(quantity_delivered) as quantity_delivered1'))
		 	 	// ->join('users', 'purchase_order_lists.assigned_to_user_id', 'IN', 'users.id', 'LEFT')
			->join('purchase_order_details', 'purchase_order_lists.receiver_no', '=', 'purchase_order_details.receiver_no', 'LEFT')
			 
			->join('dataset', 'purchase_order_details.po_status', '=', 'dataset.id', 'LEFT') 
			->where('purchase_order_details.receiver_no','=', $data['receiver_no'])
			->groupBy('purchase_order_details.dept_number');

		

		if( CommonHelper::hasValue($data['filter_po_no']) ) $query->where('purchase_order_no', 'LIKE', '%'.$data['filter_po_no'].'%');
		if( CommonHelper::hasValue($data['filter_receiver_no']) ) $query->where('purchase_order_lists.receiver_no', '=', $data['filter_receiver_no']);
		if( CommonHelper::hasValue($data['filter_entry_date']) ) $query->where('purchase_order_lists.created_at', 'LIKE', '%'.$data['filter_entry_date'].'%');
	 
		if( CommonHelper::hasValue($data['filter_status']) && $data['filter_status'] !== 'default' ) $query->where('purchase_order_lists.po_status', '=', $data['filter_status']);
		if( CommonHelper::hasValue($data['filter_back_order']) ) $query->where('back_order', '=', $data['filter_back_order']);
		if( CommonHelper::hasValue($data['filter_brand']) ) $query->where('dept_code', '=', $data['filter_brand']);
		if( CommonHelper::hasValue($data['filter_division']) ) $query->where('division', '=', $data['filter_division']);
		if( CommonHelper::hasValue($data['filter_shipment_reference_no']) ) $query->where('shipment_reference_no', '=', $data['filter_shipment_reference_no']);

		if( CommonHelper::hasValue($data['sort']) && CommonHelper::hasValue($data['order']))  {
			if ($data['sort']=='po_no') $data['sort'] = 'division';
			if ($data['sort']=='receiver_no') $data['sort'] = 'purchase_order_lists.receiver_no';
			if ($data['sort']=='entry_date') $data['sort'] = 'purchase_order_lists.created_at';

			$query->orderBy($data['sort'], $data['order']);
		}

		//if( !empty($data['filter_back_order_only']) ) $query->where('back_order', '<>', 0);

		DebugHelper::log(__METHOD__, $query);
		return $query;
	}
public static function getPOQuerydiscrepancy( $getCount = true, $data = array())
	{
		$query = DB::table('purchase_order_lists')
					->select('purchase_order_lists.purchase_order_no','purchase_order_lists.receiver_no', 'purchase_order_details.quantity_ordered','purchase_order_details.quantity_delivered','users.firstname', 'users.lastname', 'purchase_order_details.division', 'purchase_order_details.upc','purchase_order_lists.purchase_order_no','purchase_order_lists.shipment_reference_no', 'product_lists.sku','product_lists.description as shortname','purchase_order_details.created_at')
	->join('purchase_order_details','purchase_order_lists.receiver_no', '=', 'purchase_order_details.receiver_no','left')
    ->join('product_lists','purchase_order_details.upc','=','product_lists.upc','LEFT')
    ->join('users','purchase_order_details.assigned_to_user_id','=','users.id','LEFT') 
	->where('purchase_order_details.quantity_delivered','<>','purchase_order_details.quantity_ordered') 
	->WHERE('purchase_order_details.assigned_to_user_id','!=', 0)
	->where('purchase_order_details.po_status','=',  5);
 
 		if( CommonHelper::hasValue($data['filter_po_no']) ) $query->where('purchase_order_no', 'LIKE', '%'.$data['filter_po_no'].'%');
		if( CommonHelper::hasValue($data['filter_shipment_reference_no']) ) $query->where('purchase_order_lists.shipment_reference_no', 'LIKE', '%'.$data['filter_shipment_reference_no'].'%');
	 
		if( CommonHelper::hasValue($data['filter_entry_date']) ) $query->where('purchase_order_lists.entry_date', '=' ,$data['filter_entry_date'] );

		 $query->groupBy('purchase_order_no');
		$result = $query->get();
        DebugHelper::log(__METHOD__, $result);

		// get the multiple stock piler fullname
		 

		if($getCount) return count($result);
		return $result;

		return $query;
	}
public static function getPOQueryUnlistedReport($data = array())
	{
		$query = DB::select(DB::raw("SELECT COALESCE(wms_product_lists.sku,'No Available') as sku, COALESCE(wms_purchase_order_details.upc, 'No Available') as upc, COALESCE(wms_purchase_order_details.division, 'No Available') as division, COALESCE(wms_purchase_order_lists.purchase_order_no, 'No Available') as purchase_order_no, COALESCE(wms_product_lists.description, 'No Available') as description,
wms_users.firstname, wms_users.lastname, quantity_delivered, wms_purchase_order_details.created_at 

FROM `wms_purchase_order_lists`
LEFT JOIN wms_purchase_order_details on wms_purchase_order_details.receiver_no = wms_purchase_order_lists.receiver_no
left join wms_users on wms_purchase_order_details.assigned_to_user_id = wms_users.id
left JOIN wms_product_lists on wms_purchase_order_details.upc = wms_product_lists.upc
WHERE 
quantity_ordered = 0 and wms_purchase_order_details.assigned_to_user_id != 0 and wms_purchase_order_lists.po_status = 5 and  wms_product_lists.sku is NULL"));
 
		return $query;
	}
	public static function getPoListsdiv($data = array(), $getCount = FALSE) {

		
		$query = PurchaseOrder::getPOQuerydivision($data);
		// echo "<pre>"; print_r($data); die();

		if( CommonHelper::hasValue($data['limit']) && CommonHelper::hasValue($data['page']) && !$getCount)  {
			$query->skip($data['limit'] * ($data['page'] - 1))
		          ->take($data['limit']);
		}

		$result = $query->get(array(
									'purchase_order_lists.*',
									'vendors.vendor_name',
									'division',
									'dataset.data_display'
									// 'users.firstname',
									// 'users.lastname'
								)
							);

		DebugHelper::log(__METHOD__, $result);
		// get the multiple stock piler fullname
		foreach ($result as $key => $po) {
			$assignedToUserId       = explode(',', $po->assigned_to_user_id);
			$getUsers               = User::getUsersFullname($assignedToUserId);
			$result[$key]->fullname = implode(', ', array_map(function ($entry) { return $entry['name']; }, $getUsers));
		}

		if($getCount) return count($result);

		return $result;
	}

	public static function getPOInfodiv($receiver_no = NULL) {
		$query = DB::table('purchase_order_lists')
					 
->select('*',DB::raw('sum(wms_purchase_order_details.quantity_ordered) as total_qty','dept_number'))
					// ->join('users', 'purchase_order_lists.assigned_to_user_id', '=', 'users.id', 'LEFT')
					->join('dataset', 'purchase_order_lists.po_status', '=', 'dataset.id', 'LEFT')
					->join('vendors', 'purchase_order_lists.vendor_id', '=', 'vendors.id', 'LEFT')
					->join('purchase_order_details', 'purchase_order_lists.receiver_no','=','purchase_order_details.receiver_no','left')
					->where('purchase_order_lists.receiver_no', '=', $receiver_no);

		$result = $query->get(array(
									'purchase_order_lists.*',
									'vendors.vendor_name',
									'dataset.data_display'
									// 'users.firstname',
									// 'users.lastname'
								)
							);

		// get the multiple stock piler fullname
		foreach ($result as $key => $po) {
			$assignedToUserId       = explode(',', $po->assigned_to_user_id);
			$getUsers               = User::getUsersFullname($assignedToUserId);
			$result[$key]->fullname = implode(', ', array_map(function ($entry) { return $entry['name']; }, $getUsers));
		}

		return $result[0];
	}
	public static function getPullPODemo()
	{
		$query=DB::SELECT(DB::RAW("INSERT INTO `wms_purchase_order_lists`(`id`, `assigned_by`, `assigned_to_user_id`, `vendor_id`, `receiver_no`, `purchase_order_no`, `invoice_no`, `total_qty`, `destination`, `shipment_reference_no`, `entry_date`, `po_status`, `delivery_date`, `datetime_done`, `latest_mobile_sync_date`, `created_at`, `updated_at`, `deleted_at`) VALUES ('','','','','16550','10864','123456','10','','0688123','2016-02-29','1','','','','','','')"));

		$query=DB::SELECT(DB::RAW("INSERT INTO `wms_purchase_order_details`(`id`, `sku`, `upc`, `receiver_no`, `dept_number`, `brand`, `division`, `quantity_ordered`, `unit_price`, `quantity_delivered`, `expiry_date`, `assigned_by`, `assigned_to_user_id`, `po_status`, `created_at`, `updated_at`, `deleted_at`) VALUES ('','4296422','2200842964227','16550', '12','','Mens Accessories','5','','','','','','1','','',''),  ('','7996248',  '2200879962487',     '16550', '10','','Gifts and Seasonal','5','','','','','','1','','','')"));


		return $query;
	}
 
	public static function getsynctomobile()
	{
		$query=DB::table('purchase_order_lists')
            ->where('po_status', 1)
            ->orwhere('po_status', 2)
            ->update(['latest_mobile_sync_date' => date('Y-m-d H:i:s')]);

		$query=DB::table('purchase_order_details')
            ->where('po_status', 2)
            ->update(['po_status' =>'3']);
	}
	public static function getsynctomobiledivision( )
	{
		$query=DB::table('purchase_order_lists')
		 
            ->where('po_status', 1)
            ->orwhere('po_status', 2)
            ->update(['latest_mobile_sync_date' => date('Y-m-d H:i:s')]);

		$query=DB::table('purchase_order_details')
			 
            ->where('po_status', 2)
            ->update(['po_status' =>'3']);

           $query=DB::table('purchase_order_lists')
           		->select('purchase_order_lists.*','purchase_order_details.*')
           		->join('purchase_order_details','purchase_order_lists.receiver_no','=','purchase_order_lists.receiver_no','left');
	}
	public static function getPartialReceiveStatus($receiver_no, $division_id)
	{
		 
		$query=DB::table('purchase_order_details') 
            ->where('purchase_order_details.receiver_no', $receiver_no)
            ->where('purchase_order_details.dept_number', $division_id)

            ->update(['purchase_order_details.po_status' =>'3']);
	}
	public static function getPartialReceiveStatusbtn($purchase_order_no, $receiver_no)
	{
		$query=DB::table('purchase_order_lists')
			->WHERE('purchase_order_no', '=',$purchase_order_no)
			->where('receiver_no', '=', $receiver_no)
			 
			->update(['po_status' => '6']);
	}
	
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

	public static function getPoLists($data = array(), $getCount = FALSE) {
		$query = PurchaseOrder::getPOQuery($data);
		// echo "<pre>"; print_r($data); die();

		if( CommonHelper::hasValue($data['limit']) && CommonHelper::hasValue($data['page']) && !$getCount)  {
			$query->skip($data['limit'] * ($data['page'] - 1))
		          ->take($data['limit']);
		}

		$result = $query->get(array(
									'purchase_order_lists.*',  
									'dataset.data_display'
									// 'users.firstname',
									// 'users.lastname'
								)
							);

		DebugHelper::log(__METHOD__, $result);
		// get the multiple stock piler fullname
		foreach ($result as $key => $po) {
			$assignedToUserId       = explode(',', $po->assigned_to_user_id);
			$getUsers               = User::getUsersFullname($assignedToUserId);
			$result[$key]->fullname = implode(', ', array_map(function ($entry) { return $entry['name']; }, $getUsers));
		}

		if($getCount) return count($result);

		return $result;
	}
	public static function getPoLists1($receiver_no, $po_no, $data = array(), $getCount = FALSE) {
		//$query = PurchaseOrder::getPOQuery($data);
		// echo "<pre>"; print_r($data); die();
	/*	$query=DB::table('purchase_order_details')
			->select('purchase_order_details.upc')
			->where('purchase_order_details.receiver_no', $receiver_no);*/

			$query = DB::table('purchase_order_details')
			->select('product_lists.sku', 'purchase_order_details.upc', 'quantity_ordered', 'purchase_order_details.quantity_delivered as qty', 'dept_number', 'purchase_order_no', 'product_lists.short_description','purchase_order_details.receiver_no', 'purchase_order_lists.shipment_reference_no', 'purchase_order_lists.invoice_no', 'product_lists.dept_code')
				->join('purchase_order_lists','purchase_order_details.receiver_no','=','purchase_order_lists.receiver_no','LEFT')
				->join('product_lists','purchase_order_details.upc','=','product_lists.upc','LEFT')
				->where('purchase_order_no','=', $po_no)
				->where('purchase_order_details.receiver_no','=',$receiver_no)
					->get();

			 
 
		return $query;
 
	}

	/**
	* Get PO query from the passed parameter from the cms
	*
	* @example  PurchaseOrder::getCount({data});
	*
	* @param  data      array parameters passed by cms
	* @return Count of all po with the passed filters
	* Use :  cms
	*/
	public static function getCount($data = array()) {
		$query = PurchaseOrder::getPOQuery($data);
		return $query->count();
	}

	public static function getCount1($data = array()) {
		$query = PurchaseOrder::getPOQuery1($data);
		return $query->count();
	}

	/**
	* Get PO query from the passed parameter from the cms
	*
	* @example  PurchaseOrder::getPOQuery({data});
	*
	* @param  data      array parameters passed by cms
	* @return Laravel query object
	* Use :  cms
	*/


	public static function getMShipmentRef($receiver_no, $purchase_order_no, $shipment_ref)
	{
		$query = DB::SELECT(DB::RAW("UPDATE wms_purchase_order_lists set shipment_reference_no='$shipment_ref' where receiver_no='$receiver_no' and purchase_order_no='$purchase_order_no'"));
 
	}
	public static function getPOQuery($data = array())
	{
		$query = DB::table('purchase_order_lists')
					->select('purchase_order_lists.*','purchase_order_lists.shipment_reference_no','purchase_order_lists.invoice_no','purchase_order_lists.purchase_order_no','dataset.data_display','purchase_order_lists.po_status')
					->join('dataset', 'purchase_order_lists.po_status', '=', 'dataset.id', 'LEFT');

	
	 	if( CommonHelper::hasValue($data['filter_po_no']) ) $query->where('purchase_order_no', 'LIKE', '%'.$data['filter_po_no'].'%');
		if( CommonHelper::hasValue($data['filter_shipment_reference_no']) ) $query->where('purchase_order_lists.shipment_reference_no', 'LIKE', '%'.$data['filter_shipment_reference_no'].'%');
	 	if( CommonHelper::hasValue($data['filter_invoice_no']) ) $query->where('purchase_order_lists.invoice_no', 'LIKE', '%'.$data['filter_invoice_no'].'%');
		if( CommonHelper::hasValue($data['filter_entry_date']) ) $query->where('purchase_order_lists.entry_date', '=' ,$data['filter_entry_date'] );

		if( CommonHelper::hasValue($data['filter_status']) && $data['filter_status'] !== 'default' ) $query->where('purchase_order_lists.po_status', '=', $data['filter_status']);
 

		if( CommonHelper::hasValue($data['sort']) && CommonHelper::hasValue($data['order']))  {
			if ($data['sort']=='po_no') $data['sort'] = 'purchase_order_no'; 
			if ($data['sort']=='entry_date') $data['sort'] = 'purchase_order_lists.created_at';

			$query->orderBy($data['sort'], $data['order']);
		}

		 
		DebugHelper::log(__METHOD__, $query);
		return $query;
	}

	public static function getJDAquery($receiver_no, $invoice_no)
	{
		$query = DB::table('purchase_order_lists')
			->SELECT('purchase_order_lists.receiver_no','purchase_order_lists.purchase_order_no','purchase_order_lists.invoice_no','product_lists.sku')
			->join('purchase_order_details','purchase_order_lists.receiver_no','=','purchase_order_details.receiver_no','left')
			->join('product_lists','purchase_order_details.upc','=','product_lists.upc','left')
			->where('purchase_order_details.receiver_no','=', $receiver_no)
			->where('purchase_order_lists.invoice_no','=', $invoice_no);
	}
	

	public static function assignToStockPiler($purchase_order_no = '',$receiver_num = '', $data = array()) {
		$query = DB::table('purchase_order_details')
		->where('dept_number', '=', $purchase_order_no)
		->where('receiver_no', '=' , $receiver_num)
		->update($data);

	}
	public static function getPOnumber($receiver_num, $data = array()) {
		$query = DB::table('purchase_order_lists')
			->SELECT('purchase_order_lists.*','purchase_order_details.*')
			->join('purchase_order_details','purchase_order_lists.receiver_no','=','purchase_order_details.receiver_no','left')
			->where('purchase_order_lists.receiver_no','=',$receiver_num);

		return $query;

	}

	public static function updatepoliststatus($receiver_num) 
	{
		$query = DB::table('purchase_order_details')->where('receiver_no', '=' , $receiver_num)->where('po_status', '=' ,'1')->count('id');
		$asd=$query;
		 if($asd==0)$query1 = DB::table('purchase_order_lists')->where('receiver_no','=',$receiver_num)->update(array("po_status"=> 2));
	}


	public static function isPOAssignedToThisUser($user_id, $receiver_no) {
		$query = PurchaseOrder::where('receiver_no', '=', $receiver_no)
								->whereRaw('find_in_set('. $user_id . ',assigned_to_user_id) > 0');
									// ->where('assigned_to_user_id', '=', $user_id);

		$isExists = $query->first();
		DebugHelper::log(__METHOD__, $isExists);

		if( is_null($isExists) ) throw new Exception( 'User doesnt have the right access to this PO.');
		return;
	}

	public static function updatePO($receiver_no, $po_order_no, $po_status, $date_done, $slot_code) {
		$status_value = $po_status;
		$status_options = Dataset::where("data_code", "=", "PO_STATUS_TYPE")->get()->lists("id", "data_value");

		if(! CommonHelper::arrayHasValue($status_options[$status_value]) ) throw new Exception( 'Invalid status value.');

		$params = array(
					 
					"po_status" => $status_options[$status_value],
					"datetime_done" => $date_done,
					'latest_mobile_sync_date' => date('Y-m-d H:i:s')
				);

		if( !empty($slot_code) ) $params['slot_code'] = $slot_code;

		$result = PurchaseOrder::where('receiver_no', '=', $receiver_no)
									 
				->update($params);

		DebugHelper::log(__METHOD__, $result);

		return $result;
	}

	public static function reopenPO($data = array()) {
		if (! empty($data) ) {
			$query         = PurchaseOrder::where('purchase_order_no', '=', $data['po_order_no']);
			$getReceiverNo = $query->select('receiver_no')->first()->toArray();
			$receiverNo    = $getReceiverNo['receiver_no'];

			$query->update(array(
					"assigned_by"=> 0,
					"assigned_to_user_id" => 0,
					"po_status" => 1,
					"datetime_done" => '0000-00-00 00:00:00',
					'updated_at' => date('Y-m-d H:i:s')
				));

			PurchaseOrderDetail::where('receiver_no', '=', $receiverNo)
				->update(array(
					"quantity_delivered" => 0,
					'updated_at' => date('Y-m-d H:i:s')
				));

			Unlisted::deleteByReference($data['po_order_no']);

			return true;
		} else {
			return false;
		}
	}

	public static function getPOInfoByPoNos($dept_code,$receiver_no) {

		return PurchaseOrderDetail::select('division')->whereIn('dept_number', $dept_code)
		->whereIn('receiver_no', $receiver_no)
		->get()
		->toArray();

	}

	public static function getPOInfoByReceiverNo($receiver_no) {
		return PurchaseOrder::where('receiver_no', '=', $receiver_no)->first();
	}

	public static function generateShipmentReferenceNo() {

	}

}