<?php

class ApiLetdown extends BaseController {


	public function __construct() {
		date_default_timezone_set('Asia/Manila');
	}

	/**
	* Get Letdown lists
	*
	* @example www.example.com/api/{version}/letdown/list GET
	*
	* @throws Exception error
	* @return json encoded array of letdown lists by upc
	*/
	public static function getLetDownLists()
	{
		try {
			$bench = new BenchmarkHelper();
			$bench->start(__METHOD__);
			$letdownLists = LetdownDetails::getLetDownLists();
			DebugHelper::log(__METHOD__, $letdownLists);
			$bench->end();
			return CommonHelper::return_success_message($letdownLists->toArray());
		} catch (Exception $e) {
			return CommonHelper::return_fail($e->getMessage());
		}

	}

	/**
	* Gets details of the selected upc
	*
	* @example www.example.com/api/{version}/letdown/list/detail/{sku} GET
	*
	* @param  sku     sku/upc of product
	* @return json encoded array of of letdown detail
	*/
	public static function getLetdownDetail($sku)
	{
		try {
			$bench = new BenchmarkHelper();
			$bench->start(__METHOD__);
			//get document numbers
			$docNos = self::getDocumentNumbers($sku);
			DB::beginTransaction();
			//assign to the document number details then return new document numbers
			$docNos = LetdownDetails::checkAndAssign($docNos,$sku, ResourceServer::getOwnerId());
			//get details by sku
			$letdownDetails = LetdownDetails::getLetDownDetailBySKU($sku, $docNos);
			DebugHelper::log(__METHOD__);
			$bench->end();
			DB::commit();
			$result = array('doc_nos'	=> $docNos,
					'details'	=> $letdownDetails->toArray());
			return CommonHelper::return_success_message($result);
		} catch (Exception $e) {
			DB::rollback();
			return CommonHelper::return_fail($e->getMessage());
		}
	}

	/**
	* Change letdown quantity, moves a certain product from reserve zone to picking zone
	*
	* @example www.example.com/api/{version}/letdown/detail POST
	* @param  doc_nos          document number
	* @param  moved_qty       the quantity moved from reserved to picking
	* @param  sku_or_store	  sku or store that has products moved(store) or are moved(sku)
	* @return status ok if moved
	*/
	public static function postLetdownDetail()
	{
		try{

			CommonHelper::setRequiredFields(array('doc_nos', 'sku', 'slot'));

			//parameters
			$slot = Request::get('slot');
			$movedQty = (int) Request::get('moved_qty', 0);
			$sku = Request::get('sku');
			$docNos = json_decode(Request::get('doc_nos'));
			if(empty($docNos)) {
				throw new Exception("Parameter doc_nos does not have a valid format");
			}
			DB::beginTransaction();
			//assign letdown
			$docNos = LetdownDetails::checkAndAssign($docNos,$sku, ResourceServer::getOwnerId());
			//get letdown details
			$letDownDetails = LetdownDetails::getLetdownDetail($docNos,$sku, $slot, ResourceServer::getOwnerId());
			//set moved quantity of the details and return total to letdown
			$letdownTotalToLetdown = self::moveLetdownDetail($letDownDetails, $movedQty);
			//check if moved quanty is less than needed quantity
			self::checkMovedQty($letdownTotalToLetdown, $movedQty);
			self::postLetdownDetailAuditTrail($sku, $movedQty, $docNos);
			DebugHelper::log(__METHOD__, $letDownDetails);
			DB::commit();
			return Response::json(array(
				'error' => false,
				'message' => 'Success'),
				200
			);
		} catch (Exception $e) {
			DB::rollback();
			return CommonHelper::return_fail($e->getMessage());
		}
	}

	/**
	* Gets document numbers for the sku that are not yet moved
	*
	* @example  self::getDocumentNumbers
	*
	* @param  sku      sku/upc of product
	* @return array of document numbers
	*/
	public static function getDocumentNumbers($sku)
	{
		$docNos = LetdownDetails::getLetdownDocumentNumbers($sku, ResourceServer::getOwnerId());
		if(count($docNos) < 1) throw new Exception("This upc has already been assigned");
		return $docNos;
	}

	/**
	* Update letdown details with the moved quanity distributed among details
	*
	* @example  self::moveLetdownDetail()
	*
	* @param  letDownDetails    retrieved letdown details
	* @param  movedQty   		quantity to move passed by client
	* @return void
	*/
	public static function moveLetdownDetail($letDownDetails, $movedQty)
	{
		$letdownTotalToLetdown = 0;
		foreach ($letDownDetails as $key => $letDownDetail) {
			$qtyToMove = 0;
			if((int)$letDownDetail->quantity_to_letdown <= $movedQty) {
				$qtyToMove = (int) $letDownDetail->quantity_to_letdown;
				$movedQty = $movedQty - (int) $letDownDetail->quantity_to_letdown;
			} else {
				$qtyToMove = $movedQty;
				$movedQty = 0;
			}
			if($qtyToMove >= 0) {
				LetdownDetails::moveToPicking($letDownDetail->id, $qtyToMove);
			}
			$letdownTotalToLetdown +=  $letDownDetail->quantity_to_letdown;
		}
		return $letdownTotalToLetdown;
	}


	/**
	* Check overall moved quantity of letdown against the needed quantity
	*
	* @example  self::checkMovedQty()
	*
	* @param  $letdownTotalToLetdown      total required by the details
	* @param  $moved_qty   		 		  quantity passed by client
	* @throws error if $letdownTotalToLetdown is less than movedQty
	* @return void
	*/
	private static function checkMovedQty($letdownTotalToLetdown = 0, $moved_qty)
	{
		if($letdownTotalToLetdown < $moved_qty) {
			throw new Exception("Quantity to letdown is less than passed quantity you are trying to move.");
		}
	}


	/**
	 * Post audit trail of letdown
	 *
	 * @example  self::postLetdownDetailAuditTrail()
	 *
	 * @param  $sku        sku or upc
	 * @param  $movedQty   How many times something interesting should happen
	 * @param  $docNos     array of document numbers
	 * @return void
	 */
	public static function postLetdownDetailAuditTrail($sku, $movedQty, $docNos)
	{
		$dataAfter = $movedQty . ' '. $sku . ' from the letdown documents #' . implode(',', $docNos) . ' was moved to picking zone.' ;
		$arrParams = array(
			'module'		=> Config::get("audit_trail_modules.letdown"),
			'action'		=> Config::get("audit_trail.post_letdown"),
			'reference'		=> "Letdown Document Number: " .implode(',', $docNos),
			'data_before'	=> '',
			'data_after'	=> $dataAfter,
			'user_id'		=> ResourceServer::getOwnerId(),
			'created_at'	=> date('Y-m-d H:i:s'),
			'updated_at'	=> date('Y-m-d H:i:s')
		);
		AuditTrail::addAuditTrail($arrParams);
	}

	/**
	* Get Letdown lists version 2
	*
	* @example www.example.com/api/{version}/letdown/list GET
	*
	* @throws Exception error
	* @return json encoded array of letdown lists
	*/
	public static function getLetDownListsv2()
	{
		try {
			$bench = new BenchmarkHelper();
			$bench->start(__METHOD__);

			$letdownLists = Letdown::getList();
			DebugHelper::log(__METHOD__, $letdownLists);
			$bench->end();
			return CommonHelper::return_success_message($letdownLists->toArray());
		} catch (Exception $e) {
			return CommonHelper::return_fail($e->getMessage());
		}
	}

	/**
	* Gets details of the selected move_doc_number
	*
	* @example www.example.com/api/{version}/letdown/detail/{move_doc_number} GET
	*
	* @param  sku     move document number
	* @return json encoded array of of letdown detail
	*/
	public static function getLetdownDetailv2($move_doc_number)
	{
		try {
			/*if ( empty($move_doc_number) ) {
				throw new Exception("Missing parameter move_doc_number.");
			}*/

			$bench = new BenchmarkHelper();
			$bench->start(__METHOD__);

			DB::beginTransaction();

			//assign letdown
			LetdownDetails::checkAndAssignv2($move_doc_number, Authorizer::getResourceOwnerId());
			$letdownDetail = LetdownDetails::getLetDownDetailByDocNo($move_doc_number);

			$bench->end();
			DB::commit();
			return CommonHelper::return_success_message($letdownDetail->toArray());
		} catch (Exception $e) {
			DB::rollback();
			return CommonHelper::return_fail($e->getMessage());
		}
	}

	/**
	* Change letdown quantity, moves a certain product from reserve zone to picking zone
	*
	* @example www.example.com/api/{version}/letdown/detail POST
	* @param  doc_no          document number
	* @param  data       e.g format: {"CRAC":[{"moved_qty":500,"sku":2800090900154,"from_slot_code":"CRAC"},{"moved_qty":500,"sku":2800091900153,"from_slot_code":"CRAC"}]}
	* @return status ok if moved
	*/
	public static function postLetdownDetailv2()
	{

		try{

			CommonHelper::setRequiredFields(array('doc_no', 'data'));

			//parameters
			$docNo   = Request::get('doc_no');
			$data    = json_decode(Request::get('data'), true);
			$user_id = Authorizer::getResourceOwnerId();
			DebugHelper::logVar(__METHOD__, print_r($data, true));
			if(empty($data)) throw new Exception("Parameter data does not have a valid format");

			DB::beginTransaction();
			//assign letdown
			// $docNos = LetdownDetails::checkAndAssign($docNos,$sku, ResourceServer::getOwnerId());

			$result = LetdownDetails::saveDetail($docNo, $data, $user_id);

			// self::postLetdownDetailAuditTrail($sku, $movedQty, $docNos);
			DB::commit();

			return Response::json(array(
				'error'   => false,
				'message' => 'Success'),
				200
			);
		} catch (Exception $e) {
			DB::rollback();
			return CommonHelper::return_fail($e->getMessage());
		}
	}
}

