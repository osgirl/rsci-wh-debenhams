<?php

class ApiStoreReturn extends BaseController {

	// private static $types = array('sku', 'store');

	public function __construct() {
		date_default_timezone_set('Asia/Manila');
	}

	/**
	 * Version 1
	 * Get store return lists grouped according to needed format
	 *
	 * @example www.example.com/api/{version}/store_return/list GET
	 *
	 * @throws Exception error
	 * @return json encoded array of store return lists
	*/
	public static function getStoreReturnList()
	{
		try {
			$bench = new BenchmarkHelper();
			$bench->start(__METHOD__);

			$pilerId      = Authorizer::getResourceOwnerId();
			$pickingLists = StoreReturn::getListByPiler($pilerId);

			DebugHelper::log(__METHOD__, $pickingLists);
			$bench->end();
			return CommonHelper::return_success_message($pickingLists);

		} catch (Exception $e) {
			return CommonHelper::return_fail($e->getMessage());
		}

	}

	/**
	* Gets details of the selected store order number
	*
	* @example www.example.com/api/{version}/store_return/detail/{soNo} GET
	*
	* @param  sku     move document number
	* @return json encoded array of of store return detail
	*/
	public static function getStoreReturnDetail($soNo)
	{
		try {
			if(! CommonHelper::hasValue($soNo) ) throw new Exception( 'Missing parameter store order numbere.');

			$bench = new BenchmarkHelper();
			$bench->start(__METHOD__);

			DB::beginTransaction();

			$storeOrderDetail = StoreReturnDetail::getDetailBySoNo($soNo);

			$bench->end();
			DB::commit();
			return CommonHelper::return_success_message($storeOrderDetail->toArray());
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
	public static function postSaveDetail()
	{

		try{

			CommonHelper::setRequiredFields(array('so_no', 'data', 'slot_code'));

			//parameters
			$soNo   = Request::get('so_no');
			$data    = json_decode(Request::get('data'), true);
			$user_id = Authorizer::getResourceOwnerId();
			$slot_code = Request::get('slot_code');
			DebugHelper::logVar(__METHOD__, print_r($data, true));
			if(empty($data)) throw new Exception("Parameter data does not have a valid format");

			DB::beginTransaction();

			// update picklist, picklist detail, box_detail and audit trail
			$result    = StoreReturnDetail::saveDetail($soNo, $data, $user_id);

			// update status of picklist
			$status_options = Dataset::where("data_code", "=", "SR_STATUS_TYPE")->get()->lists("id", "data_value");
			StoreReturn::updateStatus($soNo, $status_options['done'], $slot_code);

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

	/**
	* Change purchase order status
	*
	* @example  www.example.com/api/{version}/purchase_order/update_status/{po_order_no}
	*
	* @param  po_order_no    int    Purchase order number
	* @param  po_status      int    Purchase order status
	* @return Status
	*/
	public function updateStatus($soNo) {
		try {

			if(! CommonHelper::hasValue($soNo) ) throw new Exception( 'Missing store order number parameter.');
			if(! CommonHelper::hasValue(Request::get('so_status')) ) throw new Exception( 'Missing status parameter.');
			$status_value = Request::get('so_status');
			$status_options = Dataset::where("data_code", "=", "SR_STATUS_TYPE")->get()->lists("id", "data_value");

			if(! CommonHelper::arrayHasValue($status_options[$status_value]) ) throw new Exception( 'Invalid status value.');

			$loggedInUserId = Authorizer::getResourceOwnerId();
			$validateUser = StoreReturn::where('so_no', '=', $soNo)
					->where('assigned_to_user_id', '=', $loggedInUserId)->first();

			if ( empty($validateUser) ) throw new Exception( 'User does not have the rights to access this record.');

			$save = StoreReturn::updateStatus($soNo, $status_options[$status_value]);

			//Audit trail
			$user_id = Authorizer::getResourceOwnerId();
			$date_before = 'Store return no #' . $soNo . ' status was Assigned.';
			$data_after = 'Store return no #' . $soNo . ' status is now ' .$status_options[$status_value]. ' and was changed by Stock Piler #' . $user_id  . '.';
			$arrParams = array(
				'module'		=> Config::get("audit_trail_modules.store_return"),
				'action'		=> Config::get("audit_trail.modify_store_return_status"),
				'reference'		=> 'Store return #' . $soNo,
				'data_before'	=> $date_before,
				'data_after'	=> $data_after,
				'user_id'		=> Authorizer::getResourceOwnerId(),// ResourceServer::getOwnerId(),
				'created_at'	=> date('Y-m-d H:i:s'),
				'updated_at'	=> date('Y-m-d H:i:s')
			);

			if ($save) {
				AuditTrail::addAuditTrail($arrParams);
			}

			DebugHelper::log(__METHOD__, $save);
			return Response::json(array(
				'error' => false,
				'message' => 'Successfully changed status to '.$status_value,
				'result' => $save),
				200
			);

		}catch(Exception $e) {
			Log::error(__METHOD__ .' Something went wrong: '.print_r($e->getMessage(),true));
			return CommonHelper::return_fail($e->getMessage());
		}
	}


}

