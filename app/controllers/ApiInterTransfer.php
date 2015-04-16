<?php


class ApiInterTransfer extends BaseController {

	public function __construct() {
		date_default_timezone_set('Asia/Manila');
	}

	/**
	 * Insert record
	 * @param  array 		$data 		array of data
	 * @return boolean
	 */
	public function insertRecord()
	{
		try {
			DB::beginTransaction();
			CommonHelper::setRequiredFields(array('data', 'box_code'));

			$data = json_decode(Request::get('data'), true);
			$box_code = Request::get('box_code');

			foreach ($data as $value)
			{
				$interTransfer              = InterTransfer::firstOrNew(array('box_code'=>$box_code, 'mts_number'=>$value['mts_number']));
				$interTransfer->box_code   = $box_code;
				$interTransfer->mts_number  = $value['mts_number'];
				$interTransfer->no_of_boxes = $value['no_of_boxes'];
				$interTransfer->updated_at  = date('Y-m-d H:i:s');
				$interTransfer->save();

				self::auditTrail($box_code, $value['mts_number'], $value['no_of_boxes']);
			}

			DB::commit();
			return CommonHelper::return_success();

		}catch(Exception $e) {
			Log::error(__METHOD__ .' Something went wrong: '.print_r($e->getMessage(),true));
			DB::rollback();
			return CommonHelper::return_fail($e->getMessage());
		}
	}

	public function auditTrail($box_code, $mts_number, $no_of_boxes)
	{
		//Audit trail
		$user_id              = Authorizer::getResourceOwnerId();
		$data_after 		  = 'Inserted inter transfer with mts_number ' . $mts_number . ' with box total of ' . $no_of_boxes . ' in box: ' . $box_code . ' and has been added by Stock Piler # '. $user_id;

		$arrParams = array(
			'module'		=> Config::get("audit_trail_modules.inter_transfer"),
			'action'		=> Config::get("audit_trail.post_inter_transfer"),
			'reference'		=> 'Box #' . $box_code,
			'data_before'	=> '',
			'data_after'	=> $data_after,
			'user_id'		=> $user_id,
			'created_at'	=> date('Y-m-d H:i:s'),
			'updated_at'	=> date('Y-m-d H:i:s')
		);
		AuditTrail::addAuditTrail($arrParams);
	}

}