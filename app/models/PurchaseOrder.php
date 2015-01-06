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
		$po_status = Dataset::getType($arrParams)->toArray();
		
		$query = PurchaseOrder::where('assigned_to_user_id', '=', $data['stock_piler_id'])
					->join('vendors', 'purchase_order_lists.vendor_id', '=', 'vendors.id', 'LEFT')
					->where("po_status", "<>", $po_status['id']);

		return $query->get(array(
				"purchase_order_lists.*",
				"vendors.vendor_name"
			)
		);
	}

	public static function getAPICount($data = array()) {
		$arrParams = array('data_code' => 'PO_STATUS_TYPE', 'data_value'=> 'closed');
		$po_status = Dataset::getType($arrParams)->toArray();
		
		$query = PurchaseOrder::where('assigned_to_user_id', '=', $data['stock_piler_id'])
					->where("po_status", "<>", $po_status['id']);

		return $query->count();
	}
	
	public static function getPOInfo($po_id = NULL) {
		$query = DB::table('purchase_order_lists')
					->join('users', 'purchase_order_lists.assigned_to_user_id', '=', 'users.id', 'LEFT')
					->join('dataset', 'purchase_order_lists.po_status', '=', 'dataset.id', 'LEFT')
					->join('vendors', 'purchase_order_lists.vendor_id', '=', 'vendors.id', 'LEFT')
					->where('purchase_order_lists.id', '=', $po_id);

		$result = $query->get(array(
									'purchase_order_lists.*', 
									'vendors.vendor_name',
									'dataset.data_display',
									'users.firstname', 
									'users.lastname'
								)
							);
		
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
									'dataset.data_display',
									'users.firstname', 
									'users.lastname'
								)
							);
		
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
					->join('users', 'purchase_order_lists.assigned_to_user_id', '=', 'users.id', 'LEFT')
					->join('dataset', 'purchase_order_lists.po_status', '=', 'dataset.id', 'LEFT')
					->join('vendors', 'purchase_order_lists.vendor_id', '=', 'vendors.id', 'LEFT');

		if( CommonHelper::hasValue($data['filter_po_no']) ) $query->where('purchase_order_no', 'LIKE', '%'.$data['filter_po_no'].'%');
		if( CommonHelper::hasValue($data['filter_receiver_no']) ) $query->where('receiver_no', 'LIKE', '%'.$data['filter_receiver_no'].'%');
		if( CommonHelper::hasValue($data['filter_supplier']) ) $query->where('vendors.vendor_name', 'LIKE', '%'.$data['filter_supplier'].'%');
		if( CommonHelper::hasValue($data['filter_entry_date']) ) $query->where('purchase_order_lists.created_at', 'LIKE', '%'.$data['filter_entry_date'].'%');
		if( CommonHelper::hasValue($data['filter_stock_piler']) ) $query->where('assigned_to_user_id', '=', $data['filter_stock_piler']);
		if( CommonHelper::hasValue($data['filter_status']) && $data['filter_status'] !== 'default' ) $query->where('po_status', '=', $data['filter_status']);
		return $query;
	}
	
	public static function assignToStockPiler($purchase_order_no = '', $data = array()) {
		$query = DB::table('purchase_order_lists')
							->where('purchase_order_no', '=', $purchase_order_no)->update($data);
	}

	public static function isPOAssignedToThisUser($user_id, $po_id) {
		$query = PurchaseOrder::where('id', '=', $po_id)
									->where('assigned_to_user_id', '=', $user_id);

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

}