<?php

class PurchaseOrderDetail extends Eloquent {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'purchase_order_details';
	protected $fillable = array('sku', 'receiver_no');

	public static function getAPIPoDetail($data = array())
	{
		$query = PurchaseOrderDetail::join('purchase_order_lists', 'purchase_order_details.receiver_no', '=', 'purchase_order_lists.receiver_no', 'LEFT')
						->join('product_lists', 'purchase_order_details.sku', '=', 'product_lists.upc', 'LEFT')
						->where('purchase_order_details.receiver_no', '=', $data['receiver_no']);

		$result = $query->get(array(
							"purchase_order_details.*",
							"product_lists.description", "product_lists.short_description"
						)
					);

		return $result;
	}

	public static function updateSKUs($data = array(), $receiver_no)
	{
		// echo '<pre>'; print_r($data); die();
		if(! CommonHelper::hasValue($receiver_no) ) throw new Exception( 'Receiver number is missing from parameter.');
		if(! isset($data['sku'])) throw new Exception( 'Sku is missing from data parameter.');
		if(! isset($data['quantity_delivered'])) throw new Exception( 'Quantity is missing from data parameter.');

		$query = PurchaseOrderDetail::where('sku', '=', $data['sku'])->where('receiver_no', '=', $receiver_no);

		$data['receiver_no'] = $receiver_no;
		$checkSku = PurchaseOrderDetail::isSKUExist($query, $data);

		if ($checkSku) {
			PurchaseOrderDetail::checkIfQtyExceeds($query, $data['quantity_delivered']);
			$array_params = array(
				'quantity_delivered' => $data['quantity_delivered'],
				'expiry_date' => $data['expiry_date'],
				'updated_at' => date('Y-m-d H:i:s')
			);

			$result = $query->update($array_params);
			DebugHelper::log(__METHOD__, $result);
			return $result;
		}
	}

	public static function isSKUExist($query, $data)
	{
		$isExists = $query->first();
		DebugHelper::log(__METHOD__, $isExists);

		// throw new Exception( 'SKU not found in the database.');
		if( is_null($isExists) ) {
			Unlisted::createUpdate($data);

			return false;
		}
		return true;
	}

	public static function checkIfQtyExceeds($query, $qty_delivered)
	{
		$row = $query->first();

		DebugHelper::log(__METHOD__, $row);
		if( $row["quantity_ordered"] < $qty_delivered ) throw new Exception( 'Cannot accept more than the expected quantity.');
		return;
	}

	public static function getPODetails($receiver_no = NULL, $data = array()) {
		$query = DB::table('purchase_order_lists')
					->join('purchase_order_details', 'purchase_order_lists.receiver_no', '=', 'purchase_order_details.receiver_no', 'RIGHT')
					->join('product_lists', 'purchase_order_details.sku', '=', 'product_lists.upc')
					->where('purchase_order_details.receiver_no', '=', $receiver_no);

		if( CommonHelper::hasValue($data['sort']) && CommonHelper::hasValue($data['order']))  {
			if ($data['sort']=='sku') $data['sort'] = 'product_lists.sku';
			if ($data['sort']=='upc') $data['sort'] = 'product_lists.upc';
			if ($data['sort']=='short_name') $data['sort'] = 'product_lists.short_description';
			if ($data['sort']=='expected_quantity') $data['sort'] = 'purchase_order_details.quantity_ordered';
			if ($data['sort']=='received_quantity') $data['sort'] = 'purchase_order_details.quantity_delivered';

			$query->orderBy($data['sort'], $data['order']);
		}

		if( CommonHelper::hasValue($data['limit']) && CommonHelper::hasValue($data['page']))  {
			$query->skip($data['limit'] * ($data['page'] - 1))
		          ->take($data['limit']);
		}

		$result = $query->get();

		return $result;
	}

	public static function getScannedPODetails($receiver_no = NULL) {
		$query = DB::table('purchase_order_lists')
					->join('purchase_order_details', 'purchase_order_lists.receiver_no', '=', 'purchase_order_details.receiver_no', 'RIGHT')
					->join('product_lists', 'purchase_order_details.sku', '=', 'product_lists.upc')
					->where('purchase_order_details.receiver_no', '=', $receiver_no)
					->where('quantity_delivered' , '>', 0);

		$result = $query->get();

		return $result;
	}


	public static function getCountPODetails($receiver_no) {
		$query = DB::table('purchase_order_lists')
					->join('purchase_order_details', 'purchase_order_lists.receiver_no', '=', 'purchase_order_details.receiver_no', 'RIGHT')
					->join('product_lists', 'purchase_order_details.sku', '=', 'product_lists.upc')
					->where('purchase_order_details.receiver_no', '=', $receiver_no);

		return $query->count();
	}

	public static function getUpcWithStatusDone($data = array()) {
		//get status done
		$arrParams = array('data_code' => 'PO_STATUS_TYPE', 'data_value'=> 'done');
		$po_status = Dataset::getType($arrParams)->toArray();

		$query = DB::table('purchase_order_details')->join('purchase_order_lists', 'purchase_order_details.receiver_no', '=', 'purchase_order_lists.receiver_no', 'LEFT')
													->where('purchase_order_lists.po_status', '=', $po_status['id'])
													->where('purchase_order_details.quantity_delivered', '>', 0)
													->where('purchase_order_details.deleted_at', '=', '0000-00-00 00:00:00');

		if( CommonHelper::hasValue($data['limit']) && CommonHelper::hasValue($data['page']))  {
			$query->skip($data['limit'] * ($data['page'] - 1))
		          ->take($data['limit']);
		}

		return $query->get(array(
				'purchase_order_details.*'
			)
		);

	}

	public static function getCountUpcWithStatusDone($data = array()) {
		//get status done
		$arrParams = array('data_code' => 'PO_STATUS_TYPE', 'data_value'=> 'done');
		$po_status = Dataset::getType($arrParams)->toArray();

		$query = DB::table('purchase_order_details')->join('purchase_order_lists', 'purchase_order_details.receiver_no', '=', 'purchase_order_lists.receiver_no', 'LEFT')
													->where('purchase_order_lists.po_status', '=', $po_status['id'])
													->where('purchase_order_details.quantity_delivered', '>', 0)
													->where('purchase_order_details.deleted_at', '=', '0000-00-00 00:00:00');

		if( CommonHelper::hasValue($data['limit']) && CommonHelper::hasValue($data['page']))  {
			$query->skip($data['limit'] * ($data['page'] - 1))
		          ->take($data['limit']);
		}

		return $query->count();
	}

	public static function getSku($data = array()) {
		$query = DB::table('purchase_order_details')->where('sku', '=', $data['sku'])
													->where('receiver_no', '=', $data['receiver_no']);

		return $query->first();
	}

	public static function getPODetailsWithExpiration($data = array(), $getCount = false) {
		$query = DB::table('purchase_order_lists')
					->join('purchase_order_details', 'purchase_order_lists.receiver_no', '=', 'purchase_order_details.receiver_no', 'RIGHT')
					->join('product_lists', 'purchase_order_details.sku', '=', 'product_lists.upc')
					->where('expiry_date', '<>', '0000-00-00 00:00:00');

		if( CommonHelper::hasValue($data['filter_po_no']) ) $query->where('purchase_order_no', 'LIKE', '%'.$data['filter_po_no'].'%');
		if( CommonHelper::hasValue($data['filter_shipment_reference_no']) ) $query->where('shipment_reference_no', 'LIKE', '%'.$data['filter_shipment_reference_no'].'%');

		if( CommonHelper::hasValue($data['sort']) && CommonHelper::hasValue($data['order']))  {
			if ($data['sort']=='sku') $data['sort'] = 'product_lists.sku';
			if ($data['sort']=='upc') $data['sort'] = 'product_lists.upc';
			if ($data['sort']=='short_name') $data['sort'] = 'product_lists.short_description';
			if ($data['sort']=='expected_quantity') $data['sort'] = 'purchase_order_details.quantity_ordered';
			if ($data['sort']=='received_quantity') $data['sort'] = 'purchase_order_details.quantity_delivered';

			$query->orderBy($data['sort'], $data['order']);
		}

		if( CommonHelper::hasValue($data['limit']) && CommonHelper::hasValue($data['page']) && !$getCount)  {
			$query->skip($data['limit'] * ($data['page'] - 1))
		          ->take($data['limit']);
		}

		$result = $query->get();

		if($getCount) {
			$result = $query->count();
		}

		return $result;
	}
}