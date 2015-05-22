<?php


class ApiManualMove extends BaseController {

	public function __construct() {
		date_default_timezone_set('Asia/Manila');
	}

	/**
	 * Insert record
	 * @param  array 		$data 		array of data
	 * @return boolean
	 */
	public function insertRecord() {
		try {
			DB::beginTransaction();
			CommonHelper::setRequiredFields(array('from_slot', 'data', 'to_slot'));

			$fromSlot = Request::get('from_slot');
			$toSlot = Request::get('to_slot');
			$data = json_decode(Request::get('data'), true);

			foreach ($data as $value)
			{
				$manualMove = new ManualMove;
				$manualMove->from_slot = $fromSlot;
				$manualMove->upc = $value['upc'];
				$manualMove->quantity = $value['quantity'];
				$manualMove->to_slot = $toSlot;
				$manualMove->sync_by = Authorizer::getResourceOwnerId();
				$saved = $manualMove->save();

				if($saved){
					$manual_move  = "classes/manual_move.php {$manualMove->id}";
					CommonHelper::execInBackground($manual_move,'manual_move');
				}
				//audit trail
				self::auditTrail($fromSlot, $value, $toSlot);
			}


			DB::commit();
			return CommonHelper::return_success();

		}catch(Exception $e) {
			Log::error(__METHOD__ .' Something went wrong: '.print_r($e->getMessage(),true));
			DB::rollback();
			return CommonHelper::return_fail($e->getMessage());
		}
	}

	public function auditTrail($fromSlot, $data, $toSlot)
	{
		//Audit trail
		$user_id              = Authorizer::getResourceOwnerId();
		// $data_after 		  = 'Inserted manual move with mts_number ' . $mts_number . ' with box total of ' . $no_of_boxes . ' in box: ' . $box_code . ' and has been added by Stock Piler # '. $user_id;
		$data_after 		  = 'Moved upc: ' . $data['upc'] . ' with quantity of ' . $data['quantity'] . ' from slot ' . $fromSlot . ' to ' . $toSlot;

		$arrParams = array(
			'module'		=> Config::get("audit_trail_modules.manual_move"),
			'action'		=> Config::get("audit_trail.save_manual_move"),
			'reference'		=> 'Upc #' . $data['upc'],
			'data_before'	=> '',
			'data_after'	=> $data_after,
			'user_id'		=> $user_id,
			'created_at'	=> date('Y-m-d H:i:s'),
			'updated_at'	=> date('Y-m-d H:i:s')
		);
		AuditTrail::addAuditTrail($arrParams);
	}

	/**
	* Get Brands
	*
	* @example  www.example.com/api/{version}/department/brands
	* @return array of brands
	*/
	public static function getInfo()
	{
		try {
			CommonHelper::setRequiredFields(array('from_slot', 'upc',));

			$fromSlot = Request::get('from_slot');
			$upc = Request::get('upc');

			$info = ManualMove::getDB2Info($fromSlot, $upc);
			return CommonHelper::return_success_message($info);
		} catch (Exception $e) {
			return CommonHelper::return_fail($e->getMessage());
		}

	}

}