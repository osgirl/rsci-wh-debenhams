<?php

class ApiReserveZone extends BaseController {
	
	public function __construct() {
		date_default_timezone_set('Asia/Manila');
	}

	/**
	* Gets upcs/skus of products in the receiving zone.
	*
	* @example  www.example.com/api/{version}/upc
	*
	* @return Status
	*/ 
	public function index() {
		try {
			$upc_list = SkuOnDock::getAll();
			DebugHelper::log(__METHOD__.' Lists: ', $upc_list);

			return CommonHelper::return_success_message($upc_list->toArray());
		}catch(Exception $e) {
			Log::error(__METHOD__ .' Something went wrong: '.print_r($e->getMessage(),true));
			return CommonHelper::return_fail($e->getMessage());
		}
	}

	/**
	* Putaway to reserve zone
	*
	* @example  www.example.com/api/{version}/upc/reserve_zone/
	*
	* @param  slot_id  
	* @return json encoded array of result of moving 
    * 
    * Steps
    * 1 Save transaction of put_away
    * 2 Save slot detail
    */
	public function putToReserve($slot_id) {
		try {
            
			if(! CommonHelper::hasValue($slot_id) ) throw new Exception( 'Missing slot id parameter.');

			CommonHelper::setRequiredFields(array('data', 'created_at', 'user_id'));

			SlotDetails::_isSlotExist($slot_id);

			$user_id 		= Request::get('user_id');
			$created_at 	= Request::get('created_at');
			$data 			= Request::get("data");
			$result 		= array();

			$data = json_decode($data, true);
			if(empty($data)) {
				throw new Exception("You haven't moved any items yet.");
			}
			
			Log::info(__METHOD__ .' Data json decode: '.print_r($data,true));
			
			//get moved_to_reserve id
			$arrParams = array('data_code' => 'ZONE_TYPE', 'data_value'=> 'moved_to_reserve');
			$zone_id = Dataset::getType($arrParams)->toArray();
			
			DB::beginTransaction();
			//1
			$transaction = PutAway::createTransaction($slot_id, $zone_id['id']);

			DebugHelper::log(__METHOD__. ' Transaction: ' , $transaction->id);

			//2 TODO:: move this to another method
	        if( CommonHelper::hasValue($transaction->id) ) 
	        {
				foreach ($data as $key => $values) {
		            $arrParams = array(
		            		'put_away_id'		=> $transaction->id,
		            		'slot_id' 			=> $slot_id,
		            		'assigned_user_id' 	=> $user_id,
		            		'created_at'		=> $created_at,
		            		'sku'				=> $values['sku'],
		            		'quantity'			=> $values['quantity_delivered'],
		            		'expiry_date'		=> $values['expiry_date']
		            	);

		            $result[$values['sku']] = $this->_validateQty($arrParams); 
		        }

		        Log::info(__METHOD__ .' Results : '.print_r($result,true));
		        
	        }
	        self::putToReserveAuditTrail($user_id, $slot_id, $result, $data);
	        DB::commit();
			return CommonHelper::return_success_message($result);
		}catch(Exception $e) {
			DB::rollback();
			Log::error(__METHOD__ .' Something went wrong: '.print_r($e->getMessage(),true));
			return CommonHelper::return_fail($e->getMessage());
		}	
	}

	/**
	* Validate if quantity is valid
	*
	* @example  self::_validateQty({data})
	*
	* @param  data      sku and moved quantity json string
	* @return result    what skus were moved
	*/ 
	private function _validateQty($data = array()) {
		$checkQty = SkuOnDock::_checkQty($data);

		if( ($checkQty['total_qty_remaining'] >= $data['quantity']) && ($checkQty['total_qty_remaining'] != 0) &&  ($data['quantity'] >0)) 
		{
			$result = SlotDetails::insert($data); 
			if( $result ) SkuOnDock::reduceTotalQtyRemaining($data);
		}
		else
		{
			$result = 'Unable to insert sku #: '.$data['sku'];
		}

		return $result;
	}


	/**
	 * Put to reserve audit trail
	 * @param $user_id          integer    user id of the stock piler
	 * @param $slot_code        integer   slot code to move the items
	 * @param $skus_result      array     result of moving (shows whether the items were moved)
	 * @param $skus_original    array     skus array passed by client
	 * @return true
	 */
	private static function putToReserveAuditTrail($user_id, $slot_code, $skus_result, $skus_original)
	{
		//TODO::how do i ensure skus were valid and all moved
		$data_after = "Stock Piler #" . $user_id . ' moving the following items to slot #' . $slot_code . ':</br>';
		
		foreach ($skus_original as $key => $value) {
			if($skus_result[$value['sku']] !== true)
			{
				$data_after .= $skus_result[$value['sku']];
			} else {
				$data_after .= "Able to insert ". $value['quantity_delivered'] . ' of ' .$value['sku'] . ' with expiration date of ' . $value['expiry_date'];
			}
			
			$data_after .= "</br>";
		}

		$arrParams = array(
			'module'		=> Config::get("audit_trail_modules.reservezone"),
			'action'		=> Config::get("audit_trail.put_to_reserve"),
			'reference'		=> "Slot code #" . $slot_code,
			'data_before'	=> '',
			'data_after'	=> $data_after,
			'user_id'		=> $user_id,
			'created_at'	=> date('Y-m-d H:i:s'),
			'updated_at'	=> date('Y-m-d H:i:s')
		);
		AuditTrail::addAuditTrail($arrParams);
	}

	/*********************Unused functions remove******************************/
	/*private function _oldvalidateQty($values = array(), $po_id) {
		$params = array('sku'=>$values['sku'], 
									'po_id'=>$po_id
							);
		$delivered_qty		= PurchaseOrderDetail::getSku($params);
		$total_stored_qty 	= SlotDetails::_checkQuantity($values, $po_id);
		$delivered_qty		= $delivered_qty->quantity_delivered;
		
		//TRUE if delivered_qty is greater or equal the submitted qty
		if( (integer) $delivered_qty >= (integer) $values['quantity'] ) 
		{
			Log::info(__METHOD__ .' Values1 : '. $delivered_qty >= (integer) $values['quantity']);
			$available_qty = $delivered_qty - $total_stored_qty; //sum
			
			//TRUE if available_qty is greater or equal to submitted qty
			if( (integer) $available_qty >= (integer) $values['quantity'] ) {
				Log::info(__METHOD__ .' Values2 : '.$available_qty >= (integer) $values['quantity']);
				$result = SlotDetails::insert($values); 
				
			}
			else {
				Log::info(__METHOD__ .' else 2 : '. $values['sku']);
				$result = 'Unable to insert sku #: '.$values['sku'];
			}
		}
		else 
		{
			Log::info(__METHOD__ .' else 1 : '. $values['sku'] . ' delivered_qty: '.$delivered_qty. 'quantity: '.$values['quantity']);
			$result = 'Unable to insert sku #: '.$values['sku'];
		}

		return $result;
	}*/
}