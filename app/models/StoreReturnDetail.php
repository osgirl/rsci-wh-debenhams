<?php

class StoreReturnDetail extends Eloquent {

	protected $table = 'store_return_detail';

	/********************Methods for CMS only**************************/


	public static function getSODetails($so_no,$data = array()){
		$query =  StoreReturnDetail::join('product_lists', 'store_return_detail.sku', '=', 'product_lists.upc')
					->join('store_return', 'store_return.so_no', '=', 'store_return_detail.so_no', 'LEFT')
					// ->join('dataset', 'store_return.so_status', '=', 'dataset.id');
					->where('store_return_detail.so_no', '=', $so_no);

		if( CommonHelper::hasValue($data['sort']) && CommonHelper::hasValue($data['order']))  {
			if ($data['sort']=='sku') $data['sort'] = 'product_lists.upc';
			if ($data['sort']=='short_name') $data['sort'] = 'product_lists.short_description';
			if ($data['sort']=='ordered_quantity') $data['sort'] = 'store_return_detail.ordered_qty';
			if ($data['sort']=='delivered_quantity') $data['sort'] = 'store_return_detail.delivered_qty';

			$query->orderBy($data['sort'], $data['order']);
		}

		if( CommonHelper::hasValue($data['limit']) && CommonHelper::hasValue($data['page']))  {
			$query->skip($data['limit'] * ($data['page'] - 1))
		          ->take($data['limit']);
		}

		$result = $query->get();

		return $result;
	}

	public static function getCountSODetails($so_no) {
		$storeOrderDetail = StoreReturnDetail::select('store_return_detail.*', 'product_lists.description')
			->join('product_lists', 'store_return_detail.sku', '=', 'product_lists.upc')
			// ->join('store_return', 'store_return.so_no', '=', 'store_return_detail.so_no', 'LEFT')
			->where('so_no', '=', $so_no)
			->get();

		return $storeOrderDetail->count();
	}

	/**
	* Get store return detail by store order number
	*
	* @example  PicklistDetails::getDetailByDocNo({$docNo})
	*
	* @param  sku      move_doc_nmber
	* @return array of picklist details by move document number
	*/
	public static function getDetailBySoNo($soNo)
	{
		$query = StoreReturnDetail::select('store_return_detail.sku',"product_lists.description", 'so_no', 'delivered_qty', 'received_qty', 'so_no')
			->leftJoin('product_lists' , 'product_lists.upc', '=', 'store_return_detail.sku')
			->where('so_no', '=', $soNo)
			->orderBy('product_lists.sku', 'asc')
			->get();

		return $query;
	}

	/**
	 * Save details by move_doc_number
	 * @param  integer 	$docNo   	Picklist document number
	 * @param  json 	$data    	Details in json format
	 * @param  integer 	$user_id 	id of the user
	 * @return boolean
	 */
	public static function saveDetail($soNo, $data, $user_id)
	{
		$doneId = Dataset::getType(array('data_code' => 'SR_STATUS_TYPE', 'data_value'=> 'done'))
						->toArray();
		//UPDATE ssi.wms_letdown_details SET moved_qty = 250, to_slot_code = 'PCK00001', move_to_picking_area = 1
		// WHERE from_slot_code = 'CRAC' AND move_doc_number = 8858 AND sku = '2800090900154'
		foreach ($data as $key => $v) {
			// print_r($value); die();
			// foreach ($value as $v) {
				$receivedQty = $v['received_qty'];
				$sku         = $v['sku'];
				// $slot_code   = $v['slot_code'];

				$detail = StoreReturnDetail::where('so_no', '=', $soNo)
					->where('sku', '=', $sku)
					->first();

				$detail->received_qty          = ($detail->received_qty + $receivedQty);
				$detail->updated_at            = date('Y-m-d H:i:s');

				$detail->save();

				$dataAfter = $receivedQty .' items of '. $sku . ' was received';
				self::saveAuditTrail($dataAfter, $soNo);
			// }
		}

		return true;
	}

	/**
	* post audit trail when store return details was done
	*
	* @example  self::saveAuditTrail();
	*
	* @param  dataAfter     changes that happened to the store reutnr details
	* @param  soNo   		store order #
	* @return void
	*/
	public static function saveAuditTrail($dataAfter, $soNo)
	{
		$arrParams = array(
			'module'		=> Config::get("audit_trail_modules.store_return"),
			'action'		=> Config::get("audit_trail.done_store_return"),
			'reference'		=> "Store Return #: " .$soNo,
			'data_before'	=> '',
			'data_after'	=> $dataAfter,
			'user_id'		=> Authorizer::getResourceOwnerId(),
			'created_at'	=> date('Y-m-d H:i:s'),
			'updated_at'	=> date('Y-m-d H:i:s')
		);
		AuditTrail::addAuditTrail($arrParams);
	}
}