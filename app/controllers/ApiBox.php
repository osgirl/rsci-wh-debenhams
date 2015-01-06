<?php 


class ApiBox extends BaseController {

	public function __construct() {
		date_default_timezone_set('Asia/Manila');
	}

	/**
	* Get boxes by store
	*
	* @example  www.example.com/api/{version}/boxes/{store_code}
	* @return boxes
	*/ 
	public static function getBoxesByStore($storeCode)
	{
		try {
			$boxes = Box::getBoxes($storeCode);
			return CommonHelper::return_success_message($boxes);
		} catch (Exception $e) {
			return CommonHelper::return_fail($e->getMessage());
		}
		
	}

	/**
	* Post picking detail
	*
	* @example  www.example.com/api/{version}/picking/detail
	*
	* @param  data      	box code with quantity to move array
	* @param  sku       	sku of what the client is trying to move
	* @param  doc_nos   	document numbers involved in the transaction
	* @param  store_code    store code
	* @return void
	*/ 
	public static function postToPicklistToBox()
	{
		try {
			CommonHelper::setRequiredFields(array('data', 'sku', 'doc_nos', 'store_code'));
			$data = json_decode(Request::get('data'));
			$docNos = json_decode(Request::get('doc_nos'));
			$sku = Request::get('sku');
			$storeCode = Request::get('store_code');
			self::checkDataAndDocNosFormat($data, $docNos);
			DB::beginTransaction();
			$picklistDetail = self::getPicklistDetail($docNos, $storeCode, $sku);
			$dataAfter = self::moveQuantityToBox($picklistDetail,$data, $storeCode, $sku);
			self::postToPicklistToBoxAuditTrail($dataAfter, $docNos);
			DB::commit();
			return Response::json(array(
				'error' => false,
				'message' => 'Success'),
				200
			);
		} catch (Exception $e) {
			DB::rollback();
			return Response::json(array(
				"error" => true,
				"result" => $e->getMessage()),
				400
			);
		}

	}

	/**
	* Move quantity to box, and change moved_qty in picking detail
	*
	* @example  self::moveQuantityToBox
	*
	* @param  picklistDetail      picklist details
	* @param  data   			  box code 
	* @param  storeCode           store code
	* @param  sku                 sku/up
	* @return data that will be logged in audi trail
	*/ 
	private static function moveQuantityToBox($picklistDetail,$data, $storeCode, $sku)
	{
		$picklistDetail = $picklistDetail->toArray();
		// print_r($data);
		// $countPicklistDetail = count($picklistDetail);
		// $countData = count($data);
		$picklistDetailCounter = ( (count($picklistDetail) > count($data)) ? count($picklistDetail) : count($data) );
		// print_r($picklistDetailCounter);
		$dataCounter = 0;
		$distributedQty = 0;
		$dataAfter = "";
		for ($i = 0; $i < $picklistDetailCounter; $i++) {
			if($distributedQty === 0) {
				//check data of boxes if box exists, if it does, set it as the box and distributed quantity
				if(array_key_exists($dataCounter,$data)) {
					self::checkBoxData($data[$dataCounter], $storeCode);
					$distributedQty = $data[$dataCounter]->qty_packed;
					$dataCounter++;
					if($i > 0) $i--;
				} else {
					 break;
				}
			}
			// dd();
			$qtyToMove = 0;
			//if the needed quantity of the picklist detail is less than the distributed quantity, set it as the qty to move
			if((int)$picklistDetail[$i]['quantity_to_pick'] <= $distributedQty) { 
				$qtyToMove = (int) $picklistDetail[$i]['quantity_to_pick'];
				$distributedQty = $distributedQty - $picklistDetail[$i]['quantity_to_pick'];
				$picklistDetail[$i]['quantity_to_pick'] -= $qtyToMove;
			} else { 
				$qtyToMove = $distributedQty;
				$distributedQty = 0; 
				$picklistDetail[$i]['quantity_to_pick'] -= $qtyToMove;
			}
			if($qtyToMove > 0) {
				PicklistDetails::moveToBox($picklistDetail[$i]['id'], $qtyToMove);
				BoxDetails::moveToBox($picklistDetail[$i]['id'], $data[$dataCounter-1]->box_code,$qtyToMove);
				$dataAfter = $qtyToMove .' items of '. $sku . ' was packed to ' . $data[$dataCounter-1]->box_code . "\n";
			}
		}	
		if($distributedQty > 0) {
			throw new Exception("Cannot move quantity greater than required");
		}
		// print_r($dataAfter);
		// dd();
		return $dataAfter;
	}

	/**
	* Get picklist detail with checking
	*
	* @example  self::getPicklistDetail()
	*
	* @param  doc_nos   	document numbers involved in the transaction
	* @param  storeCode           store code
	* @param  sku                 sku/up
	* @return picklistDetail
	*/ 
	private static function getPicklistDetail($docNos, $storeCode, $sku)
	{
		$picklistDetail = PicklistDetails::getPicklistDetail($docNos, $storeCode, $sku);
		if(count($picklistDetail)=== 0) throw new Exception('The SKU for that store and letdown documents does not exist.');
		return $picklistDetail;
	}

	/**
	* check if passed data and document numbers are in a correct format
	*
	* @example  self::checkDataAndDocNosFormat()
	*
	* @param  data   	box code with quantity to move
	* @param  docNos    document numbers
	* @throws error if wrong format was passed
	* @return void
	*/ 
	private static function checkDataAndDocNosFormat($data, $docNos)
	{
		if(empty($data)) throw new Exception("Parameter doc_nos does not have a valid format [{'box_code':'930213', 'qty_packed': 20}]");
		/*else {
			$arr = array();
			foreach($data as $value) {
				if( $value->qty_packed == 0 ) throw new Exception("There are UPC's not yet packed.", 1);
			}
		}*/
		if(empty($docNos)) throw new Exception("Parameter doc_nos does not have a valid format");
	}

	/**
	* Check box data
	*
	* @example  self::checkBoxData({params})
	*
	* @param  boxData    box code and quantity packed ex: {'box_code': '', 'qty_packed': ''}
	* @param  storeCode  store code 
	* @return Status if data passed is valid
	*/ 
	private static function checkBoxData($boxData, $storeCode)
	{
		//check data passed
		if($boxData->box_code === '') throw new Exception("Box code value is missing in data.");
		if($boxData->qty_packed === '') throw new Exception("Quantity packed value is missing in data.");
		//check if valid box is passed
		$box = Box::getBox($boxData->box_code, $storeCode);
		if(count($box) === 0) throw new Exception("Box code does not exist for that store.");
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
	public static function postToPicklistToBoxAuditTrail($dataAfter, $docNos)
	{
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