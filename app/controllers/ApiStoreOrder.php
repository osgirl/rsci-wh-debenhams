<?php

class ApiStoreOrder extends BaseController {
	
	public function __construct() {
		date_default_timezone_set('Asia/Manila');
	}

	/**
	* Gets upcs for the store using store order list
	*
	* @example  www.example.com/api/{version}/store_order/list
	*
	* @return json encoded array of upcs
	*/ 
	public static function getProductList($storeCode)
	{
		try {
			$load_code = Request::get('load_code');
			if( isset($load_code) == FALSE ) throw new Exception( 'Missing load code parameter.');
			if(! CommonHelper::hasValue($storeCode) ) throw new Exception( 'Missing store code parameter.');

			$params = array('storeCode' => $storeCode, 'loadCode' => Request::get('load_code'));
			$upcs = StoreOrderDetail::getProductList($params);
			DebugHelper::log(__METHOD__, $upcs);
			return Response::json(array(
				'error' => false,
				'message' => 'Success',
				'result'	=> $upcs),
				200
			);
		} catch (Exception $e) {
			Log::error(__METHOD__ .' Something went wrong: '.print_r($e->getMessage(),true));
			return Response::json(array(
				"error" => true,
				"result" => $e->getMessage()),
				400
			);
		}
	}

	/**
	* Gets upcs for the store using store order list
	*
	* @example  www.example.com/api/{version}/store_order/loads
	*
	* @return json encoded array of loads
	*/ 
	public static function getLoads($storeCode)
	{
		try {
			if(! CommonHelper::hasValue($storeCode) ) throw new Exception( 'Missing store code parameter.');
			$loads = StoreOrder::getLoadList($storeCode);
			
			DebugHelper::log(__METHOD__, $loads);
			return Response::json(array(
				'error' => false,
				'message' => 'Success',
				'result'	=> $loads->toArray()),
				200
			);
		} catch (Exception $e) {
			Log::error(__METHOD__ .' Something went wrong: '.print_r($e->getMessage(),true));
			return Response::json(array(
				"error" => true,
				"result" => $e->getMessage()),
				400
			);
		}
	}

	/**
	* Post store receiving data
	*
	* @example  www.example.com/api/{version}/store_order/receive
	*
	* @param  sku         sku/upc of product
	* @param  load_code   load code of the product
	* @param  store_code  store code of the product
	* @param  received_qty  store code of the product
	* @throws Some_Exception_Class If something interesting cannot happen
	* @return Status
	*/ 
	public static function postReceive()
	{
		try {
			CommonHelper::setRequiredFields(array('sku', 'load_code', 'store_code', 'received_qty'));

			$sku = Request::get('sku');
			$loadCode = Request::get('load_code');
			$storeCode = Request::get('store_code');
			$receivedQty = $receivedQtyOrig = Request::get('received_qty');
			
			$storeOrderDetails = StoreOrderDetail::getStoreOrderDetail($storeCode, $loadCode, $sku);
			$totalToReceive = 0;
			foreach ($storeOrderDetails as $detail) {
				$qtyToMove = 0;
				
				if((int)$detail->ordered_qty <= $receivedQty) {
					$qtyToMove = (int) $detail->ordered_qty;
					$movedQty = $receivedQty - (int) $detail->ordered_qty;
				} else {
					$qtyToMove = $receivedQty;
					$movedQty = 0; 
				}
				if($qtyToMove > 0) {
					StoreOrderDetail::receiveSo($detail->id, $qtyToMove);
				}
				$totalToReceive +=  $detail->ordered_qty;
			}
			self::checkMovedQty($totalToReceive, $receivedQtyOrig);
			return Response::json(array(
				'error' => false,
				'message' => 'Success'),
				200
			);
		} catch (Exception $e) {
			return Response::json(array(
				"error" => true,
				"result" => $e->getMessage()),
				400
			);
		}
	}

	/**
	* Close store order status
	*
	* @example  www.example.com/api/{version}/store_order/close
	*
	* @param  load_code     load code to change status
	* @return Status
	*/ 
	public static function closeStoreOrders()
	{
		try {
			CommonHelper::setRequiredFields(array('load_code', 'store_code'));

			$loadCode = Request::get('load_code');
			$storeCode = Request::get('store_code');

			StoreOrder::closeStoreOrdersByLoad($loadCode, $storeCode);
			return Response::json(array(
				'error' => false,
				'message' => 'Success'),
				200
			);
		} catch (Exception $e) {
			return Response::json(array(
				"error" => true,
				"result" => $e->getMessage()),
				400
			);
		}
	}


	public static function checkMovedQty($orderedQty, $receivedQty)
	{
		if($orderedQty< $receivedQty) {
			throw new Exception("Received quantity is greater than ordered quantity.");
		}
	}
	
}