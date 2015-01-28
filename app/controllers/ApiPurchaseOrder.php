<?php

class ApiPurchaseOrder extends BaseController {


	public function __construct() {
		date_default_timezone_set('Asia/Manila');
	}

	/**
	* Display a prodcut listing of the resource
	*
	* @example  www.example.com/api/{version}/purchase_order/{piler_id}
	*
	* @param  piler_id  int    Stock piler id
	* @throws Some_Exception_Class If something interesting cannot happen
	* @return json encoded paginated purchase order list
	*/
	public function index($piler_id) {

		try {

			if(! CommonHelper::hasValue($piler_id) ) throw new Exception( 'Missing stockpiler id parameter.');

			$arrParams = array('stock_piler_id' => $piler_id);
			$purchaseOrder = PurchaseOrder::getAPIPoLists($arrParams);

			DebugHelper::log(__METHOD__, $purchaseOrder);

			// $items 		= $purchaseOrder;
			// $totalItems = PurchaseOrder::getAPICount($arrParams);
			// $perPage 	= 10;

			// $purchaseOrder = Paginator::make($items, count($items), $perPage);

			return CommonHelper::return_success_message($purchaseOrder);
		}catch(Exception $e) {
			Log::error(__METHOD__ .' Something went wrong: '.print_r($e->getMessage(),true));
			return CommonHelper::return_fail($e->getMessage());
		}
	}

	/**
	* Get Purchase Order Details
	*
	* @example  www.example.com/api/{version}/purchase_order/details/{po_id}
	*
	* @param  po_id    int    Purchase Order Id
	* @return json encoded array of purchase order details
	*/
	public function getDetails($po_id) {
		try {
			if(! CommonHelper::hasValue($po_id) ) throw new Exception( 'Missing purchase order id parameter.');

			$arrParams = array('po_id' => $po_id);
			$po_details = PurchaseOrderDetail::getAPIPoDetail($arrParams);

			DebugHelper::log(__METHOD__, $po_details);
			return CommonHelper::return_success_message($po_details->toArray());
		}catch(Exception $e) {
			Log::error(__METHOD__ .' Something went wrong: '.print_r($e->getMessage(),true));
			return CommonHelper::return_fail($e->getMessage());
		}
	}

	/**
	* Change purchase order status to closed and changes purchase order quantities
	*
	* @example  www.example.com/api/{version}/purchase_order/{po_order_no}
	*
	* @param  po_order_no    int             Purchase order number
	* @param  data           json string     json encoded array of skus and correspoinding moved quantity
	* @param  user_id        int             Stock Piler Id
	* @param  datetime_done  datetime        Date and time the purchase order was done (YYYY-MM-DD HH:MM:SS) (note: this is the time when the PO was marked done in the app, which is not the same with current time when the api call was done)
	* @param  po_id          int             Purchase order id
	* @return Status
	*/
	public function savedReceivedPO($po_order_no) {
		try {
			if(! CommonHelper::hasValue($po_order_no) ) throw new Exception( 'Missing purchase order number parameter.');
			// echo "<pre>"; print_r(json_decode(Request::get('data'), true)); die();
			CommonHelper::setRequiredFields(array('data', 'user_id', 'datetime_done','po_id'));

			$data 		= json_decode(Request::get('data'), true);

			if(empty($data)) {
				throw new Exception("Empty data parameter");
			}

			DebugHelper::log(__METHOD__, $data);
			$po_status 	= "done";
			$date_done 	= Request::get('datetime_done');
			$user_id 	= Request::get('user_id');
			$po_id 		= Request::get('po_id');
			DB::beginTransaction();
			//check if user has the right to this PO
			PurchaseOrder::isPOAssignedToThisUser($user_id, $po_id);


			//save purcase order detail
			foreach($data as $row) {
				$row['po_order_no'] = $po_order_no;
				PurchaseOrderDetail::updateSKUs($row, $po_id); //update po_detail table for the received qty
			}
			self::validatePassedPODetails($po_id, $po_order_no);

			//update po status
			PurchaseOrder::updatePOStatus($po_order_no, $po_status, $date_done); //update po_list status to done

			//add audit trail
			self::savedReceivedPOAuditTrail($po_order_no, $user_id);

			//add transaction for jda syncing
			JdaTransaction::insert(array(
				'module' 		=> Config::get('transactions.module_purchase_order'),
				'jda_action'	=> Config::get('transactions.jda_action_po_receiving'),
				'reference'		=> $po_order_no
			));

			DB::commit();
			return CommonHelper::return_success();
		}catch(Exception $e) {
			DB::rollback();
			Log::error(__METHOD__ .' Something went wrong: '.print_r($e->getMessage(),true));
			return CommonHelper::return_fail($e->getMessage());
		}
	}

	/**
	* Add audit trail for saving received PO
	*
	* @param  po_id    integer  purchase order id
	* @param  user_id  integer  stock piler assigned to the PO
	* @return true
	*/
	private static function savedReceivedPOAuditTrail($po_id, $user_id)
	{
		$data_after = 'Purchase Order #' . $po_id . ' was received by Stock Piler #' . $user_id  . '.';
		$arrParams = array(
			'module'		=> Config::get("audit_trail_modules.purchaseorder"),
			'action'		=> Config::get("audit_trail.save_po"),
			'reference'		=> 'Purchase Order #' . $po_id,
			'data_before'	=> '',
			'data_after'	=> $data_after,
			'user_id'		=> Authorizer::getResourceOwnerId(),// ResourceServer::getOwnerId(),
			'created_at'	=> date('Y-m-d H:i:s'),
			'updated_at'	=> date('Y-m-d H:i:s')
		);
		AuditTrail::addAuditTrail($arrParams);
	}

	private static function validatePassedPODetails($poId, $poNumber)
	{
		$poInfo = PurchaseOrder::getPOInfo($poId);
		if(empty($poInfo)) throw new Exception("Purchase order does not exist");
		if((int)$poInfo->purchase_order_no !== (int)$poNumber) throw new Exception("Passed purchase order number does not match purchase order id.");

	}


	/*********************Unused functions remove******************************/
	//TODO::Why 5? Is this function used? Ask Mobile
	/**
	* Assign Purchase order to admin
	*
	* @example  www.example.com/api/{version}/purchase_order/assign_to_stockpiler/{id}/{clerk_id}/{piler_id}
	*
	* @param  id     	int    Purchase order id
	* @param  clerk_id  int    Document Clerk Id
	* @param  piler_id  int    Stock Piler Id
	* @return Status if fail or successfull
	*/
	/*public function assignToPiler($id, $clerk_id, $piler_id) {
		try {
			if(! CommonHelper::hasValue($id) ) throw new Exception( 'Missing id parameter.');
			if(! CommonHelper::hasValue($clerk_id) ) throw new Exception( 'Missing clerk_id parameter.');
			if(! CommonHelper::hasValue($piler_id) ) throw new Exception( 'Missing stockpiler_id parameter.');

			DB::beginTransaction();
			$params = array(
				"assigned_by"			=> $clerk_id,
				"assigned_to_user_id"	=> $piler_id,
				"updated_at"			=> date('Y-m-d H:i:s'),
				"po_status"				=> 5
			);
			$po = PurchaseOrder::find($id);
			PurchaseOrder::assignToStockPiler($po->purchase_order_no, $params);
			DB::commit();

			DebugHelper::log(__METHOD__, $po->toArray());

			return Response::json(array(
				'error' => false,
				'message' => 'Successfully assigned to piler '. $piler_id),
				200
			);

		}catch(Exception $e) {
			DB::rollback();
			Log::error(__METHOD__ .' Something went wrong: '.print_r($e->getMessage(),true));
			return Response::json(array(
				"error" => true,
				"message" => $e->getMessage()),
				400
			);
		}
	}
*/

		//TODO::Ask mobile if this is used
	/**
	* Change purchase order status
	*
	* @example  www.example.com/api/{version}/purchase_order/update_status/{po_order_no}
	*
	* @param  po_order_no    int    Purchase order number
	* @param  po_status      int    Purchase order status
	* @return Status
	*/
	public function updateStatus($po_order_no) {
		try {

			if(! CommonHelper::hasValue($po_order_no) ) throw new Exception( 'Missing purchase order number parameter.');
			if(! CommonHelper::hasValue(Request::get('po_status')) ) throw new Exception( 'Missing status parameter.');
			$status_value = Request::get('po_status');
			$status_options = Dataset::where("data_code", "=", "PO_STATUS_TYPE")->get()->lists("id", "data_value");

			if(! CommonHelper::arrayHasValue($status_options[$status_value]) ) throw new Exception( 'Invalid status value.');

			$po = PurchaseOrder::where('purchase_order_no', '=', $po_order_no)
					->update(array(
						"po_status" => $status_options[$status_value],
						"updated_at" => date('Y-m-d H:i:s')
					));

			//Audit trail
			$user_id = Authorizer::getResourceOwnerId();
			$date_before = 'PO No #' . $po_order_no . ' status was Assigned.';
			$data_after = 'PO No #' . $po_order_no . ' status is now ' .$status_options[$status_value]. ' and was changed by Stock Piler #' . $user_id  . '.';
			$arrParams = array(
				'module'		=> Config::get("audit_trail_modules.purchaseorder"),
				'action'		=> Config::get("audit_trail.modify_po_status"),
				'reference'		=> 'Purchase Order #' . $po_order_no,
				'data_before'	=> $date_before,
				'data_after'	=> $data_after,
				'user_id'		=> Authorizer::getResourceOwnerId(),// ResourceServer::getOwnerId(),
				'created_at'	=> date('Y-m-d H:i:s'),
				'updated_at'	=> date('Y-m-d H:i:s')
			);
			AuditTrail::addAuditTrail($arrParams);

			DebugHelper::log(__METHOD__, $po);
			return Response::json(array(
				'error' => false,
				'message' => 'Successfully changed status to '.$status_value,
				'result' => $po),
				200
			);

		}catch(Exception $e) {
			Log::error(__METHOD__ .' Something went wrong: '.print_r($e->getMessage(),true));
			return Response::json(array(
				"error" => true,
				"result" => $e->getMessage()),
				400
			);
		}
	}

	/**
	* Update a particular PO Detail
	*
	* @example  www.example.com/sample url
	*
	* @param  po_id    int    Purchase order id
	* @param  sku      string SKU/UPC of product
	* @return
	*/
	/*public function updateDetail($po_id, $sku) {
		try {
			if(! CommonHelper::hasValue($po_id) ) throw new Exception( 'Missing id parameter.');
			if(! CommonHelper::hasValue($sku) ) throw new Exception( 'Missing sku/upc parameter.');
			if(! CommonHelper::numericHasValue(Request::get("quantity_delivered")) ) throw new Exception( 'Missing quantity.');

			$quantity_delivered = Request::get("quantity_delivered");
			DB::beginTransaction();
			$po_detail = DB::table('purchase_order_details')
					->where('po_id', '=', $po_id)
					->where('sku', '=', $sku)
					->update(array(
						"quantity_delivered"=>$quantity_delivered,
						"updated_at" => date('Y-m-d H:i:s')
					));

			DebugHelper::log(__METHOD__, $po_detail->toArray());
			DB::commit();
			return Response::json(array(
				'error' => false,
				'message' => 'Successfull updated sku #'.$sku,
				'result' => $po_detail),
				200
			);

		}catch(Exception $e) {
			DB::rollback();
			Log::error(__METHOD__ .' Something went wrong: '.print_r($e->getMessage(),true));
			return Response::json(array(
				"error" => true,
				"result" => $e->getMessage()),
				400
			);
		}
	}*/

}