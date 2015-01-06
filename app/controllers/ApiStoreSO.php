<?php

class ApiStoreSO extends BaseController {

	// protected static $allowed_roles = array(3,4);
	
	/**
	* Gets Store store_orders
	*
	* @example  www.example.com/api/v1/store_receive/
	*
	* @param  
	* @return store_orders @array
	*/ 
	public static function getSO()
	{
		try {
			if(! CommonHelper::hasValue(Request::get('store_code')) ) throw new Exception( 'Missing store code parameter.');

			$store_code = Request::get('store_code');
			$params = array('store_code' => $store_code);

			$getOpenSO = StoreSO::getOpenSo($params);
			
			if(! empty($getOpenSO) )
			{
				$result = $getOpenSO->toArray();
			}
			else {
				$result = 'No open SO to receive';
			}

			return Response::json(array(
				'error' => false,
				'message' => 'Success',
				'result' => $result),
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
	* Gets Store store_orders
	*
	* @example  www.example.com/api/v1/store_receive/detail/{so_no}
	*
	* @param  store_order
	* @return store_order_detail @array
	*/ 
	public static function getSoDetails($so_no)
	{
		try {
			if(! CommonHelper::hasValue($so_no) ) throw new Exception( 'Missing store order parameter.');

			$arrParams = array('so_no' => $so_no);
			$so_details = StoreSODetails::getSoDetail($arrParams);

			DebugHelper::log(__METHOD__, $so_details);
			return Response::json(array(
				'error' => false,
				'message' => 'Success',
				'result' => $so_details->toArray()),
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
	* Accept qty delivered per store_order
	*
	* @example  www.example.com/api/v1/store_receive/accept/{storeCode}
	*
	* @param  store_code    store code
	* @return Status
	*/ 
	public function postAcceptSo($so_no) {

		try {
			if(! CommonHelper::hasValue($so_no) ) throw new Exception( 'Missing store order number parameter.');
			if(! CommonHelper::hasValue(Request::get('data')) ) throw new Exception( 'Missing data parameter.');
			if(! CommonHelper::hasValue(Request::get('user_id')) ) throw new Exception( 'Missing user id parameter.');

			$data = json_decode(Request::get('data'), true);
			$user_id = Request::get('user_id');

			if(empty($data)) throw new Exception("Empty data parameter");
		
			//save store order detail
			foreach($data as $row) {
				StoreSODetails::updateDeliveredQty($row, $so_no);
			}
			//update po status
			$params = array('so_no'=>$so_no, 'assigned_user_id'=>$user_id);
			StoreSO::updateSoStatus($params); //update status to close
			
			return Response::json(array(
				'error' => false,
				'message' => 'Success'),
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
}