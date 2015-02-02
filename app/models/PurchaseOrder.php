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


		$query = DB::select(DB::raw("SELECT wms_purchase_order_lists.*, wms_vendors.vendor_name FROM wms_purchase_order_lists LEFT join wms_vendors on wms_purchase_order_lists.vendor_id = wms_vendors.id WHERE find_in_set($piler_id,assigned_to_user_id) > 0"));

		return $query;
	}

	public static function getAPICount($data = array()) {
		$arrParams = array('data_code' => 'PO_STATUS_TYPE', 'data_value'=> 'closed');
		$piler_id  = (int) $data['stock_piler_id'];
		$po_status = Dataset::getType($arrParams)->toArray();

		// $query = PurchaseOrder::where('assigned_to_user_id', '=', $data['stock_piler_id'])
		// 			->where("po_status", "<>", $po_status['id']);
		$query = DB::select(DB::raw("SELECT wms_purchase_order_lists.*, wms_vendors.vendor_name FROM wms_purchase_order_lists LEFT join wms_vendors on wms_purchase_order_lists.vendor_id = wms_vendors.id WHERE find_in_set($piler_id,assigned_to_user_id) > 0"));

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
	public static function getPoLists($data = array()) {
		$query = PurchaseOrder::getPOQuery($data);
		// echo "<pre>"; print_r($data); die();
		if( CommonHelper::hasValue($data['sort']) && CommonHelper::hasValue($data['order']))  {
			if ($data['sort']=='po_no') $data['sort'] = 'purchase_order_no';
			if ($data['sort']=='receiver_no') $data['sort'] = 'receiver_no';
			if ($data['sort']=='entry_date') $data['sort'] = 'purchase_order_lists.created_at';

			$query->orderBy($data['sort'], $data['order']);
		}

		if( CommonHelper::hasValue($data['limit']) && CommonHelper::hasValue($data['page']))  {
			$query->skip($data['limit'] * ($data['page'] - 1))
		          ->take($data['limit']);
		}

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

		return $result;
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

	/**
	* Get PO query from the passed parameter from the cms
	*
	* @example  PurchaseOrder::getPOQuery({data});
	*
	* @param  data      array parameters passed by cms
	* @return Laravel query object
	* Use :  cms
	*/
	public static function getPOQuery($data = array())
	{
		$query = DB::table('purchase_order_lists')
					// ->join('users', 'purchase_order_lists.assigned_to_user_id', 'IN', 'users.id', 'LEFT')
					->join('dataset', 'purchase_order_lists.po_status', '=', 'dataset.id', 'LEFT')
					->join('vendors', 'purchase_order_lists.vendor_id', '=', 'vendors.id', 'LEFT');

		if( CommonHelper::hasValue($data['filter_po_no']) ) $query->where('purchase_order_no', 'LIKE', '%'.$data['filter_po_no'].'%');
		if( CommonHelper::hasValue($data['filter_receiver_no']) ) $query->where('receiver_no', '=', $data['filter_receiver_no']);
		// if( CommonHelper::hasValue($data['filter_supplier']) ) $query->where('vendors.vendor_name', 'LIKE', '%'.$data['filter_supplier'].'%');
		if( CommonHelper::hasValue($data['filter_entry_date']) ) $query->where('purchase_order_lists.created_at', 'LIKE', '%'.$data['filter_entry_date'].'%');
		// if( CommonHelper::hasValue($data['filter_stock_piler']) ) $query->where('assigned_to_user_id', '=', $data['filter_stock_piler']);
		if( CommonHelper::hasValue($data['filter_stock_piler']) ) $query->whereRaw('find_in_set('. $data['filter_stock_piler'] . ',assigned_to_user_id) > 0');
		if( CommonHelper::hasValue($data['filter_status']) && $data['filter_status'] !== 'default' ) $query->where('po_status', '=', $data['filter_status']);
		DebugHelper::log(__METHOD__, $query);
		return $query;
	}

	public static function assignToStockPiler($purchase_order_no = '', $data = array()) {
		$query = DB::table('purchase_order_lists')->where('purchase_order_no', '=', $purchase_order_no)->update($data);
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

	public static function updatePOStatus($po_order_no, $po_status, $date_done, $invoice_no='', $invoice_amount=0) {
		$status_value = $po_status;
		$status_options = Dataset::where("data_code", "=", "PO_STATUS_TYPE")->get()->lists("id", "data_value");

		if(! CommonHelper::arrayHasValue($status_options[$status_value]) ) throw new Exception( 'Invalid status value.');

		$result = PurchaseOrder::where('purchase_order_no', '=', $po_order_no)
				->update(array(
					"invoice_amount"=> $invoice_amount,
					"invoice_no" => $invoice_no,
					"po_status" => $status_options[$status_value],
					"datetime_done" => $date_done,
					'latest_mobile_sync_date' => date('Y-m-d H:i:s')
				));

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

	public static function getPOInfoByPoNos($data) {

		return PurchaseOrder::whereIn('purchase_order_no', $data)->get()->toArray();

	}

	public static function getPOInfoByReceiverNo($receiver_no) {
		return PurchaseOrder::where('receiver_no', '=', $receiver_no)->first();
	}

}