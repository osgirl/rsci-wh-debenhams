<?php

class PicklistDetails extends Eloquent {

	protected $table = 'picklist_details';

	public $timestamps=false;

	/**
	* Get picklist header, either by store or by sku/upc
	*
	* @example  PicklistDetails::getPickListGrouped();
	*
	* @return array of skus merged with stores
	*/
	public static function getPickListGrouped()
	{
		// $pickingListSKU = self::getAllPickingListBySKU();
		$pickingListUPC = self::getAllPickingListByUPC();
		$pickingListStore = self::getAllPickingListByStore();
		$pickingList = array_merge($pickingListUPC, $pickingListStore);

		return $pickingList;

	}

	/**
	* Get picklist Document numbers
	*
	* @example  self::getPicklistDocumentNumbers();
	*
	* @param  $type      	type that the client is requesting
	* @param  $skuOrStore   sku/upc or store
	* @param  $id   		stock piler id
	* @return array of document numbers
	*/
	public static function getPicklistDocumentNumbers($type, $skuOrStore, $id)
	{
		$query = PicklistDetails::select('picklist_details.move_doc_number as move_doc_number')
			->where('move_to_shipping_area', '=', Config::get('letdown_statuses.unmoved'))
			->join('picklist', 'picklist.move_doc_number', '=', 'picklist_details.move_doc_number');
			if($type ==='upc') {
				$query->where('sku', '=', $skuOrStore)
					->where('picklist.type', '=', 'upc');
			} else if ($type ==='store') {
				$query->where('store_code', '=', $skuOrStore)
					->where('picklist.type', '=', 'store');
			} else {
				throw new Exception("Type parameter is not correct");
			}

		$documentNumbers = $query->whereIn('assigned_user_id', array(0, $id))
			->groupBy('picklist_details.move_doc_number')
			->lists('picklist_details.move_doc_number');
		return $documentNumbers;
	}

	/**
	* Check and Assign Picklist sku or store group
	*
	* @example  PicklistDetails::checkAndAssign();
	*
	* @param  $docNos      	Document numbers
	* @param  $type      	type that the client is requesting
	* @param  $skuOrStore   sku/upc or store
	* @param  $id   		stock piler id
	* @return Status
	*/
	public static function checkAndAssign($docNos,$skuOrStore,$type, $id)
	{
		$lockTag = time();
		$picklistAssigned = self::getSumOfAssigned($docNos,$skuOrStore,$type);
		if($picklistAssigned > 0 ) {
			$currentlyAssigned = self::getCurrentlyAssigned($docNos,$skuOrStore,$type, array(0, $id));
			if(in_array(0, $currentlyAssigned)) {
				self::assignPicklist(array_keys($currentlyAssigned), $skuOrStore,$type, $id, $lockTag);
				return array_keys($currentlyAssigned);
			}
			if(in_array($id, $currentlyAssigned)) {
				return array_keys($currentlyAssigned);
			}
			throw new Exception("This upc/store has already been assigned");
		} else {
			self::assignPicklist($docNos, $skuOrStore,$type,  $id, $lockTag);
		}
		return $docNos;
	}

	/**
	* Check if all moved_qty has value per document numbers.
	*
	* @example  PicklistDetails::checkforEmptySkus();
	*
	* @param  $docNos      	Document numbers
	* @param  $skuOrStore   sku/upc or store
	* @return Status
	*/
	public static function checkforEmptySkus($docNos,$skuOrStore, $type)
	{
		$query = PicklistDetails::whereIn('move_doc_number', $docNos);

		if($type == 'upc') $query->where('sku', '=', $skuOrStore);
		else $query->where('store_code', '=', $skuOrStore);

		$status = $query->where('moved_qty', '=', '0')
			  			->lists('moved_qty', 'move_doc_number');

		DebugHelper::log(__METHOD__, $query);
		// if(CommonHelper::arrayHasValue($status)) throw new Exception("There are UPC's not yet packed.", 1);

		return true;
	}

	/**
	* Check only if the user is assigned
	*
	* @example  PicklistDetails::checkOnlyAssigned();
	*
	* @param  $docNos      	Document numbers
	* @param  $type      	type that the client is requesting
	* @param  $skuOrStore   sku/upc or store
	* @param  $id   		stock piler id
	* @return Status
	*/
	public static function checkOnlyAssigned($docNos,$skuOrStore,$type, $id)
	{
		$currentlyAssigned = self::getCurrentlyAssigned($docNos,$skuOrStore,$type, array($id));
		if(in_array($id, $currentlyAssigned)) {
			return array_keys($currentlyAssigned);
		} else {
			throw new Exception("This upc or store has already been assigned");
		}
	}



	/**
	* Get details if the type of request is by sku
	*
	* @example  PicklistDetails::getDetailBySKU
	*
	* @param  Place      Where something interesting takes place
	* @param  integer   How many times something interesting should happen
	* @throws Some_Exception_Class If something interesting cannot happen
	* @return Status
	*/
	public static function getDetailBySKU($sku, $docNos)
	{
		$picklistDetails = PicklistDetails::select(DB::raw("sum(wms_picklist_details.quantity_to_pick) as quantity_to_pick,wms_picklist_details.store_code, wms_stores.store_name "))
			->join('stores', 'stores.store_code', '=', 'picklist_details.store_code')
			->whereIn('move_doc_number', $docNos)
			->where('move_to_shipping_area', '=', Config::get('picking_statuses.unmoved'))
			->where('picklist_details.sku', '=', $sku)
			->groupBy('picklist_details.store_code')
			->get();
		return $picklistDetails;
	}

	public static function getDetailByStore($storeCode, $docNos)
	{
		$picklistDetails = PicklistDetails::select(DB::raw("sum(wms_picklist_details.quantity_to_pick) as quantity_to_pick,wms_picklist_details.sku, wms_product_lists.description"))
			->join('product_lists', 'product_lists.upc', '=', 'picklist_details.sku')
			->whereIn('move_doc_number', $docNos)
			->where('move_to_shipping_area', '=', Config::get('picking_statuses.unmoved'))
			->where('picklist_details.store_code', '=', $storeCode)
			->groupBy('picklist_details.sku')
			->get();
		return $picklistDetails;
	}

	/**
	* Gets details that match the document numbers,store code and sku
	*
	* @example  PicklistDetails::getPicklistDetail()
	*
	* @param  $docNos      Document numbers
	* @param  $storeCode   Store Code
	* @param  $sku  	   SKU/UPC
	* @return picklist details
	*/
	public static function getPicklistDetail($docNos, $storeCode, $sku)
	{
		$picklistDetails = PicklistDetails::whereIn('move_doc_number', $docNos)
			->where('sku', '=', $sku)
			->where('store_code', '=', $storeCode)
			->where('move_to_shipping_area', '=', Config::get('picking_statuses.unmoved'))
			->get();
		DebugHelper::log(__METHOD__, $picklistDetails);
		return $picklistDetails;
	}


	/**
	 * Get all picking list that are grouped by SKU/UPC
	 *
	 * @example  self::getAllPickingListBySKU()
	 *
	 * @return array of upcs
	 */
	private static function getAllPickingListBySKU()
	{
		//bump into upc
		$result = PicklistDetails::select('picklist_details.sku', 'product_lists.description', 'picklist.type')
			->join('product_lists', 'product_lists.upc', '=', 'picklist_details.sku')
			->join('picklist', 'picklist.move_doc_number', '=', 'picklist_details.move_doc_number')
			->where('picklist.type', '=', 'sku')
			->where('move_to_shipping_area', '=', Config::get('picking_statuses.unmoved'))
			->groupBy('picklist_details.sku')
			->get()->toArray();
		return $result;
	}

	/**
	 * Get all picking list that are grouped by SKU/UPC
	 *
	 * @example  self::getAllPickingListByUPC()
	 *
	 * @return array of upcs
	 */
	private static function getAllPickingListByUPC()
	{
		//bump into upc
		$result = PicklistDetails::select('picklist_details.sku', 'product_lists.description', 'picklist.type')
			->join('product_lists', 'product_lists.upc', '=', 'picklist_details.sku')
			->join('picklist', 'picklist.move_doc_number', '=', 'picklist_details.move_doc_number')
			->where('picklist.type', '=', 'upc')
			->where('move_to_shipping_area', '=', Config::get('picking_statuses.unmoved'))
			->groupBy('picklist_details.sku')
			->get()->toArray();
		return $result;
	}

	 /**
	 * Get all picking list that are grouped by Store
	 *
	 * @example  self::getAllPickingListByStore()
	 *
	 * @return array of stores
	 */
	private static function getAllPickingListByStore()
	{
		$result = PicklistDetails::select('stores.store_name', 'picklist_details.store_code','picklist.type')
			->join('stores', 'stores.store_code', '=', 'picklist_details.store_code')
			->join('picklist', 'picklist.move_doc_number', '=', 'picklist_details.move_doc_number')
			->where('picklist.type', '=', 'store')
			->where('move_to_shipping_area', '=', Config::get('picking_statuses.unmoved'))
			->groupBy('picklist_details.store_code')
			->get()->toArray();
		return $result;
	}

	/**
	* Gets sum of assigned ids, used for check if the group of details has someone assigned to it
	*
	* @example  self::getSumOfAssigned();
	*
	* @param  $docNos   	Document number
	* @param  $skuOrStore  	sku/upc or store
	* @param  $type 		type of call the client is asking for
	* @return sum of assigned user id
	*/
	private static function getSumOfAssigned($docNos,$skuOrStore,$type)
	{
		$query = PicklistDetails::select(DB::raw("sum(wms_picklist_details.assigned_user_id) as assigned_user_id"))
			->join('picklist', 'picklist.move_doc_number', '=', 'picklist_details.move_doc_number')
        	->whereIn('picklist_details.move_doc_number', $docNos);
		if($type ==='upc') {
			$query->where('sku', '=', $skuOrStore)
				->where('picklist.type', '=', 'upc');
		} else if ($type ==='store') {
			$query->where('store_code', '=', $skuOrStore)
				->where('picklist.type', '=', 'store');
		} else {
			throw new Exception("Type parameter is not correct");
		}

		$result = $query->pluck('assigned_user_id');
		return $result;
	}

	/**
	* Gets details where the current user has already been assigned
	*
	* @example  self::getCurrentlyAssigned();
	*
	* @param  $docNos   	Document number
	* @param  $skuOrStore  	sku/upc or store
	* @param  $type 		type of call the client is asking for
	* @param  $id       	Stock piler ids
	* @return array of assigned user id and document number
	*/
	private static function getCurrentlyAssigned($docNos,$skuOrStore,$type, $ids)
	{
		$query = PicklistDetails::select('picklist_details.move_doc_number as move_doc_number', 'picklist_details.assigned_user_id as assigned_user_id')
				->whereIn('picklist_details.move_doc_number', $docNos)
				->join('picklist', 'picklist.move_doc_number', '=', 'picklist_details.move_doc_number');
				if($type ==='upc') {
					$query->where('sku', '=', $skuOrStore)
						->where('picklist.type', '=', 'upc');
				} else if ($type ==='store') {
					$query->where('store_code', '=', $skuOrStore)
						->where('picklist.type', '=', 'store');
				} else {
					throw new Exception("Type parameter is not correct");
				}
		$result  = $query->whereIn('assigned_user_id', $ids)
			->lists('assigned_user_id', 'move_doc_number');
		return $result;
	}

	/**
	* Assign to stockpiler
	*
	* @example  self::assignLetdown();
	*
	* @param  $docNos   	Document number
	* @param  $skuOrStore  	sku/upc or store
	* @param  $type 		type of call the client is asking for
	* @param  $id       	Stock piler id
	* @param  $lockTag  Lock tag
	* @return void
	*/
	public static function assignPicklist($docNos, $skuOrStore, $type, $id,$lockTag)
	{
		$query = PicklistDetails::select('picklist_details.updated_at as updated_at')
			->whereIn('picklist_details.move_doc_number', $docNos)
			->join('picklist', 'picklist.move_doc_number', '=', 'picklist_details.move_doc_number');
		if($type ==='upc') {
			$query->where('sku', '=', $skuOrStore)
				->where('picklist.type', '=', 'upc');

		} else if ($type ==='store') {
			$query->where('store_code', '=', $skuOrStore)
			->where('picklist.type', '=', 'store');
		} else {
			throw new Exception("Type parameter is not correct");
		}

		$query->where('assigned_user_id', '=', 0)
			->update(array(
				'lock_tag'						=>	$lockTag,
            	'assigned_user_id' 				=>  $id,
            	'picklist_details.updated_at'	=>	date('Y-m-d H:i:s')));

        return true;
	}

	public static function moveToBox($picklistDetailId, $packedQty) {
		$picklistDetail = PicklistDetails::where('id', '=', $picklistDetailId)
			->first();
		$newPackedQty = intval($picklistDetail->moved_qty) + $packedQty;
		if($picklistDetail->quantity_to_pick < $newPackedQty) {
			throw new Exception("Trying to move quantity greater than quantity to pick");
		}
		PicklistDetails::where('id', '=', $picklistDetailId)
			->update(array('moved_qty'=> $newPackedQty,
				'updated_at'	=>    date('Y-m-d H:i:s')));
		return;
	}

	public static function moveToShippingChangeStatus($docNos,$skuOrStore,$type)
	{
		$query = PicklistDetails::select('picklist_details.updated_at as updated_at')
			->whereIn('picklist_details.move_doc_number', $docNos)
			->join('picklist', 'picklist.move_doc_number', '=', 'picklist_details.move_doc_number');
		if($type ==='upc') {
			$query->where('picklist.type', '=', 'upc')
				->where('sku', '=', $skuOrStore);
		} else if ($type ==='store') {
			$query->where('picklist.type', '=', 'store')
			->where('store_code', '=', $skuOrStore);
		} else {
			throw new Exception("Type parameter is not correct");
		}

		$query->update(array('move_to_shipping_area'	=> Config::get('picking_statuses.moved'),
			'picklist_details.updated_at'	=> date('Y-m-d H:i:s')));

		return true;
	}

	/**********for cms*************/

	public static function getFilteredPicklistDetail($data, $getCount= false)
	{
		$query = PicklistDetails::where('move_doc_number', $data['picklist_doc'])
			->leftJoin('stores', 'stores.store_code', '=', 'picklist_details.store_code')
			->leftJoin('product_lists', 'picklist_details.sku', '=', 'product_lists.upc');

		if( CommonHelper::hasValue($data['filter_sku']) ) $query->where('picklist_details.sku', 'LIKE', '%'.$data['filter_sku'].'%');
		if( CommonHelper::hasValue($data['filter_so']) ) $query->where('so_no', 'LIKE', '%'.$data['filter_so'].'%');
		if( CommonHelper::hasValue($data['filter_from_slot']) ) $query->where('from_slot_code', 'LIKE', '%'.$data['filter_from_slot'].'%');
		// if( CommonHelper::hasValue($data['filter_to_slot']) ) $query->where('to_slot_code', 'LIKE', '%'.$data['filter_to_slot'].'%');
		// if( CommonHelper::hasValue($data['filter_status_detail']) ) $query->where('move_to_shipping_area', '=', $data['filter_status_detail']);

		if( CommonHelper::hasValue($data['sort']) && CommonHelper::hasValue($data['order']))  {
			if ($data['sort']=='sku') $data['sort'] = 'product_lists.sku';
			if ($data['sort']=='upc') $data['sort'] = 'product_lists.upc';
			if ($data['sort']=='so_no') $data['sort'] = 'so_no';
			if ($data['sort']=='from_slot_code') $data['sort'] = 'from_slot_code';
			// if ($data['sort']=='to_slot_code') $data['sort'] = 'to_slot_code';

			$query->orderBy($data['sort'], $data['order']);
		}

		if( CommonHelper::hasValue($data['limit']) && CommonHelper::hasValue($data['page'])  && !$getCount)  {
			$query->skip($data['limit'] * ($data['page'] - 1))
		          ->take($data['limit']);
		}
		$result = $query->get();

		if($getCount) {
			$result = $query->count();
		}

		return $result;

	}

	public static function getLockTags($data = array(), $getCount=false)
	{
		$query = PicklistDetails::select(DB::raw('wms_picklist_details.*, wms_users.*, sum(move_to_shipping_area) as sum_moved , sum(wms_picklist_details.moved_qty) as sum_moved_qty'))
			->join('users', 'users.id', '=', 'picklist_details.assigned_user_id');

		if( CommonHelper::hasValue($data['filter_stock_piler']) ) $query->where('users.id', '=', $data['filter_stock_piler']);
		if( CommonHelper::hasValue($data['filter_doc_no'])) $query->where('picklist_details.move_doc_number', 'LIKE', '%'.$data['filter_doc_no'].'%');
		if( CommonHelper::hasValue($data['filter_sku']) ) $query->where('picklist_details.sku', 'LIKE', '%'. $data['filter_sku'].'%');

		if( CommonHelper::hasValue($data['sort']) && CommonHelper::hasValue($data['order']))  {
			if ($data['sort']=='lock_tag') $data['sort'] = 'picklist_details.lock_tag';

			$query->orderBy($data['sort'], $data['order']);
		}

		if( CommonHelper::hasValue($data['limit']) && CommonHelper::hasValue($data['page'])  && !$getCount)  {
			$query->skip($data['limit'] * ($data['page'] - 1))
		          ->take($data['limit']);
		}

		if($getCount) {
			$result = $query->count(DB::raw('distinct lock_tag'));

		} else {
			$query->groupBy('picklist_details.lock_tag');
			$result = $query->get();
		}

		return $result;
	}

	public static function getLockTagDetails($lockTag)
	{
		$lockTagDetails = PicklistDetails::where('lock_tag', '=', $lockTag)
			->join('stores', 'stores.store_code', '=', 'picklist_details.store_code')
			->join('product_lists','product_lists.upc', '=', 'picklist_details.sku' )
			->get();
		$sumMoved =  PicklistDetails::select(DB::raw('sum(move_to_shipping_area) as sum_moved, sum(wms_picklist_details.moved_qty) as sum_moved_qty'))
			->where('lock_tag', '=', $lockTag)
			->first();


		$result = array('details'	=> $lockTagDetails,
				'sum_moved'	=> $sumMoved['sum_moved'],
				'sum_moved_qty'	=> $sumMoved['sum_moved_qty']);
		return $result;
	}

	public static function unlockTag($lockTags)
	{
		PicklistDetails::whereIn('lock_tag', $lockTags)
			->where('move_to_shipping_area' , '!=', Config::get('picking_statuses.moved'))
			->update(array('assigned_user_id'=> 0,
					'lock_tag'		=> 0,
					'updated_at'	=>	date('Y-m-d H:i:s')));
		return true;
	}

	/**
	* Get Picklist detail by move_doc_number
	*
	* @example  PicklistDetails::getPicklistDetailByDocNo({$docNo})
	*
	* @param  sku      move_doc_nmber
	* @return array of picklist details by move document number
	*/
	public static function getPicklistDetailByDocNo($docNo)
	{
		$picklistDetails = PicklistDetails::select('picklist_details.sku',"product_lists.description", 'store_code', 'move_doc_number', 'from_slot_code', 'quantity_to_pick', 'moved_qty', 'so_no')
			->leftJoin('product_lists' , 'product_lists.upc', '=', 'picklist_details.sku')
			->where('move_doc_number', '=', $docNo)
			->orderBy('from_slot_code', 'asc')
			->get();

		return $picklistDetails;
	}

	/**
	 * Save details by move_doc_number
	 * @param  integer 	$docNo   	Picklist document number
	 * @param  json 	$data    	Details in json format
	 * @param  integer 	$user_id 	id of the user
	 * @return boolean
	 */
	public static function saveDetail($docNo, $data, $user_id)
	{
		$doneId = Dataset::getType(array('data_code' => 'PICKLIST_STATUS_TYPE', 'data_value'=> 'done'))
						->toArray();
		//UPDATE ssi.wms_letdown_details SET moved_qty = 250, to_slot_code = 'PCK00001', move_to_picking_area = 1
		// WHERE from_slot_code = 'CRAC' AND move_doc_number = 8858 AND sku = '2800090900154'
		foreach ($data as $key => $value) {
			foreach ($value as $v) {
				$qtyToMove    = $v['moved_qty'];
				$boxCode      = $v['box_code'];
				$sku          = $v['sku'];
				$fromSlotCode = $v['from_slot_code'];
				$soNo 		  = $v['so_no'];

				$picklistDetail = PicklistDetails::where('from_slot_code', '=', $fromSlotCode)
					->where('move_doc_number', '=', $docNo)
					->where('sku', '=', $sku)
					->where('so_no', '=', $soNo)
					->first();

				$picklistDetail->moved_qty             = ($picklistDetail->moved_qty + $qtyToMove);
				$picklistDetail->updated_at            = date('Y-m-d H:i:s');

				# Save picklist to box
				BoxDetails::moveToBox($picklistDetail->id, $boxCode,$qtyToMove);

				$picklistDetail->save();

				$dataAfter = $qtyToMove .' items of '. $sku . ' was packed to ' . $boxCode . "\n";
				self::postToPicklistToBoxAuditTrail($dataAfter, $docNo);
			}
		}

		return true;
	}

	/**
	* post audit trail when picklist details are moved to boxes
	*
	* @example  self::postToPicklistToBoxAuditTrail();
	*
	* @param  dataAfter     changes that happened to the picklist
	* @param  docNos   		document numbers
	* @return void
	*/
	public static function postToPicklistToBoxAuditTrail($dataAfter, $docNo)
	{
		$arrParams = array(
			'module'		=> Config::get("audit_trail_modules.picking"),
			'action'		=> Config::get("audit_trail.post_picklist_to_box"),
			'reference'		=> "Picklist Document #: " .$docNo,
			'data_before'	=> '',
			'data_after'	=> $dataAfter,
			'user_id'		=> Authorizer::getResourceOwnerId(),
			'created_at'	=> date('Y-m-d H:i:s'),
			'updated_at'	=> date('Y-m-d H:i:s')
		);
		AuditTrail::addAuditTrail($arrParams);
	}

	public static function getPicklistLoad($docNo)
	{
		$query = PicklistDetails::select('load_details.load_code')
			->where('move_doc_number', '=', $docNo)
			->join('box_details', 'box_details.picklist_detail_id', '=', 'picklist_details.id')
			->join('pallet_details', 'pallet_details.box_code', '=', 'box_details.box_code')
			->join('load_details', 'load_details.pallet_code', '=', 'pallet_details.pallet_code')
			->distinct()->get()->toArray();

		return $query;
	}
}
