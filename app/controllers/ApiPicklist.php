<?php

class ApiPicklist extends BaseController {
	
	// private static $types = array('sku', 'store');
	private static $types = array('upc', 'store');

	public function __construct() {
		date_default_timezone_set('Asia/Manila');
	}

	/**
	* Get Picklist lists grouped according to needed format
	*
	* @example www.example.com/api/{version}/picking/list GET
	*
	* @throws Exception error
	* @return json encoded array of picking lists
	*/ 
	public static function getPickingLists()
	{
		try {
			$pickingLists = PicklistDetails::getPickListGrouped();
			return CommonHelper::return_success_message($pickingLists);
		} catch (Exception $e) {
			return CommonHelper::return_fail($e->getMessage());
		}
		
	}

	/**
	* Gets details of the selected sku or store
	*
	* @example www.example.com/api/{version}/picking/detail/{sku_or_store} GET
	*
	* @param  $skuOrStore     sku/upc of product or storeCode of store
	* @param  $type           type the client needs
	* @return json encoded array of of letdown detail
	*/ 
	public static function getPickingDetail($skuOrStore)
	{
		try {
			if(! CommonHelper::hasValue(Request::get('type')) ) throw new Exception( 'Missing type type parameter.');
			DB::beginTransaction();
			$type = Request::get('type');
			$docNos = self::getDocumentNumbers($type, $skuOrStore, ResourceServer::getOwnerId());
			$result = self::getDocNosAndDetails($docNos,$skuOrStore, ResourceServer::getOwnerId(), $type);
			DB::commit();
			return CommonHelper::return_success_message($result);
		} catch (Exception $e) {
			DB::rollback();
			return CommonHelper::return_fail($e->getMessage());
		}
	}


	/**
	* Post done the upc or store that waas clicked in the header
	*
	* @example  www.example.com/api/{version}/picking/done/{sku_or_store}
	*
	* @param  skuOrStore      sku/upc or store code of what picking header was done
	* @param  docNos          passed document numbers at click of the picking header
	* @param  $type           type of picking header
	* @return Status
	*/ 
	public static function postDone($skuOrStore)
	{
		try {
			DB::beginTransaction();
			CommonHelper::setRequiredFields(array('doc_nos', 'type'));
			$docNos = json_decode(Request::get('doc_nos'));
			$type = Request::get('type');
			if(empty($docNos)) throw new Exception("Parameter doc_nos does not have a valid format");
			
			if(in_array($type, self::$types)) {
				PicklistDetails::checkforEmptySkus($docNos,$skuOrStore, $type);
				// dd($stats);
				$docNos = PicklistDetails::checkOnlyAssigned($docNos,$skuOrStore,$type, ResourceServer::getOwnerId());
				PicklistDetails::moveToShippingChangeStatus($docNos,$skuOrStore,$type);
			} else {
				throw new Exception("Type parameter is not correct");
			}
			self::postDoneAuditTrail($docNos, $skuOrStore, $type);
			DB::commit();
			return CommonHelper::return_success();
		} catch (Exception $e) {
			DB::rollback();
			return CommonHelper::return_fail($e->getMessage());
		}
	}

	/**
	* Gets all document numbers of passed sku or store
	*
	* @example self::getDocumentNumbers();
	*
	* @param  $type         type of what the client needs
	* @param  $skuOrStore   sku or store
	* @param  $id   		stock piler id
	* @return document numbers
	*/ 
	private static function getDocumentNumbers($type, $skuOrStore, $id)
	{
		$docNos = PicklistDetails::getPicklistDocumentNumbers($type, $skuOrStore, $id);
		if(count($docNos) < 1) throw new Exception("This store or upc has already been assigned");
		return $docNos;
	}

	/**
	* Gets assigned or newly assigned doc numbers and details of the passed sku or store
	*
	* @example  self::getDocNosAndDetails()
	*
	* @param  $docNos      	Document numbers
	* @param  $type         type of what the client needs
	* @param  $skuOrStore   sku or store
	* @param  $id   		stock piler id
	* @return Status
	*/ 
	private static function getDocNosAndDetails($docNos,$skuOrStore, $id, $type)
	{
		if($type == 'upc') {
			$docNos = PicklistDetails::checkAndAssign($docNos,$skuOrStore,'upc', $id);
			$pickingDetail = PicklistDetails::getDetailBySKU($skuOrStore, $docNos);
		} else if ($type == 'store') {
			$docNos =PicklistDetails::checkAndAssign($docNos,$skuOrStore,'store', $id);
			$pickingDetail = PicklistDetails::getDetailByStore($skuOrStore, $docNos);
		} else {
			throw new Exception("Type parameter is not correct");
		}
		$result = array('doc_nos'	=> $docNos,
				'details'	=> $pickingDetail->toArray());
		return $result;
	}

	/**
	* Audit trail of posting the picking header as done
	*
	* @example  self::postDoneAuditTrail({param})
	*
	* @param  docNos      		passed document numbers at click of the picking header
	* @param  skuOrStore   		sku/upc or store code of what picking header was done
	* @param  type   			type of picking header
	* @return 
	*/ 
	private static function postDoneAuditTrail($docNos, $skuOrStore, $type)
	{
		if($type == 'upc') {
			$dataAfter = 'Item ' . $skuOrStore . ' from Picklist Documents # ' .implode(',', $docNos) . ' change status as done.';
		} else if($type == 'store') {
			$dataAfter = 'Items from store #' . $skuOrStore . ' from Picklist Documents # ' .implode(',', $docNos) . ' change status as done';
		} 
		$arrParams = array(
			'module'		=> Config::get("audit_trail_modules.picking"),
			'action'		=> Config::get("audit_trail.post_picklist_to_box"),
			'reference'		=> "Picklist Document #: " .implode(',', $docNos),
			'data_before'	=> '',
			'data_after'	=> $dataAfter,
			'user_id'		=> ResourceServer::getOwnerId(),
			'created_at'	=> date('Y-m-d H:i:s'),
			'updated_at'	=> date('Y-m-d H:i:s')
		);
		AuditTrail::addAuditTrail($arrParams);
	}

}

