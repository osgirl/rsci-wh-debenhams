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

	public function RPolist($piler_id)
    {
    	try {
			$polist = Box::GetApiRPoList($piler_id);

			return Response::json(array('result' => $polist),200);
			//return $polist;
		}
		catch(Exception $e) 
		{
			return Response::json(array(
				"error" => true,
				"result" => $e->getMessage()
				),400
			);
		}

    }
	public static function getBoxesByStore($storeCode)
	{
		try {
			//$boxes = Box::getBoxes($storeCode);
            $user_id = Authorizer::getResourceOwnerId();
            $boxes = Box::getBoxesUserId($storeCode,$user_id);
			return CommonHelper::return_success_message($boxes);
		} catch (Exception $e) {
			return CommonHelper::return_fail($e->getMessage());
		}

	}

    /**
     * Get boxes by store
     *
     * @example  www.example.com/api/{version}/boxes/{store_code}/{user_name}
     * @return boxes
     */
    public static function getBoxesByStoreUserId($storeCode,$userid)
    {
        try {
            $boxes = Box::getBoxesUserId($storeCode,$userid);
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

	/**
	* Create box in series
	*
	* @example  self::postToPicklistToBoxAuditTrail();
	*
	* @param  dataAfter     changes that happened to the picklist
	* @param  docNos   		document numbers
	* @return void
	*/
	public static function postCreateBox()
	{
		try {

			CommonHelper::setRequiredFields(array('store'));

			DB::beginTransaction();

			$storeCode = Request::get('store');
            $user_id = Authorizer::getResourceOwnerId();
			$numberOfBoxes = 1;

			if(strlen($storeCode) == 1) $newStoreCodeFormat = "000{$storeCode}";
			else if(strlen($storeCode) == 2) $newStoreCodeFormat = "00{$storeCode}";
			else if(strlen($storeCode) == 3) $newStoreCodeFormat = "0{$storeCode}";
			else if(strlen($storeCode) == 4) $newStoreCodeFormat = "{$storeCode}";
			else throw new Exception("Invalid store");

			#check if a record exist in that store
			$box = Box::where('box_code', 'LIKE', "{$newStoreCodeFormat}%")->max('box_code');
			#if result is empty follow the format
			if($box == null) $box = $newStoreCodeFormat."00000";
			#if exists get the latest then increment box
			$formattedBoxCode = array();
			$containerBox = array(); //use for audit trail
			foreach(range(1, $numberOfBoxes) as $number) {
				$boxCode = substr($box, -5);
				$boxCode = (int) $boxCode + $number;
				$formattedBoxCode[$number]['box_code'] = $newStoreCodeFormat . sprintf("%05s", (int)$boxCode);
				$formattedBoxCode[$number]['store_code'] = $storeCode;
				$formattedBoxCode[$number]['created_at'] = date('Y-m-d H:i:s');
                $formattedBoxCode[$number]['userid'] = $user_id;
				$containerBox[] = $newStoreCodeFormat . sprintf("%05s", (int)$boxCode);
			}

			Box::insert($formattedBoxCode);

			$storeName = Store::getStoreName($storeCode);
			$boxCodeInString = implode(',', $containerBox);

			self::postCreateBoxAuditTrail($boxCodeInString, $storeName);
			DB::commit();
			return CommonHelper::return_success_message($containerBox[0]);
		} catch (Exception $e) {
			DB::rollback();
			return CommonHelper::return_fail($e->getMessage());
		}
	}

    /**
     * Create box in series by username
     *
     * @example  self::postCreateBoxByUserId();
     *
     * @param  store     store code
     * @param  username  user name
     * @return status is success/error
     */
    public static function postCreateBoxByUserId()
    {
        try {

            CommonHelper::setRequiredFields(array('store'));
            CommonHelper::setRequiredFields(array('userid'));

            DB::beginTransaction();

            $storeCode = Request::get('store');
            // $userid = Request::get('userid');
            $userid = Auth::user()->id;
            $numberOfBoxes = 1;

            if(strlen($storeCode) == 1) $newStoreCodeFormat = "000{$storeCode}";
            else if(strlen($storeCode) == 2) $newStoreCodeFormat = "00{$storeCode}";
            else if(strlen($storeCode) == 3) $newStoreCodeFormat = "0{$storeCode}";
            else if(strlen($storeCode) == 4) $newStoreCodeFormat = "{$storeCode}";
            else throw new Exception("Invalid store");

            #check if a record exist in that store
            $box = Box::where('box_code', 'LIKE', "{$newStoreCodeFormat}%")->max('box_code');
            #if result is empty follow the format
            if($box == null) $box = $newStoreCodeFormat."00000";
            #if exists get the latest then increment box
            $formattedBoxCode = array();
            $containerBox = array(); //use for audit trail
            foreach(range(1, $numberOfBoxes) as $number) {
                $boxCode = substr($box, -5);
                $boxCode = (int) $boxCode + $number;
                $formattedBoxCode[$number]['box_code'] = $newStoreCodeFormat . sprintf("%05s", (int)$boxCode);
                $formattedBoxCode[$number]['store_code'] = $storeCode;
                $formattedBoxCode[$number]['created_at'] = date('Y-m-d H:i:s');
                $formattedBoxCode[$number]['userid'] = $userid;
                $containerBox[] = $newStoreCodeFormat . sprintf("%05s", (int)$boxCode);
            }

            Box::insert($formattedBoxCode);

            $storeName = Store::getStoreName($storeCode);
            $boxCodeInString = implode(',', $containerBox);

            self::postCreateBoxAuditTrail($boxCodeInString, $storeName);
            DB::commit();
            return CommonHelper::return_success_message($containerBox[0]);
        } catch (Exception $e) {
            DB::rollback();
            return CommonHelper::return_fail($e->getMessage());
        }
    }

	private static function postCreateBoxAuditTrail($boxCode, $storeName)
	{
		$user_id = Authorizer::getResourceOwnerId();
		$userInfo = User::find($user_id);
		$dataBefore = '';
		$dataAfter = 'User '. $userInfo->username . ' created a box with code ' . $boxCode. ' for ' . $storeName;

		$arrParams = array(
						'module'		=> Config::get('audit_trail_modules.boxing'),
						'action'		=> Config::get('audit_trail.create_box'),
						'reference'		=> 'Box code # ' . $boxCode,
						'data_before'	=> $dataBefore,
						'data_after'	=> $dataAfter,
						'user_id'		=> $user_id,
						'created_at'	=> date('Y-m-d H:i:s'),
						'updated_at'	=> date('Y-m-d H:i:s')
						);
		AuditTrail::addAuditTrail($arrParams);
	}

	/**
	* Get boxes that are not empty
	*
	* @example  www.example.com/api/{version}/boxes/all
	* @return boxes
	*/
	public static function getAllBoxes()
	{
		try {
			$boxes = BoxDetails::getAllBoxes();
			return CommonHelper::return_success_message($boxes);
		} catch (Exception $e) {
			return CommonHelper::return_fail($e->getMessage());
		}

	}

}