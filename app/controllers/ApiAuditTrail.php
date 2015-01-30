<?php


class ApiAuditTrail extends BaseController {

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
			CommonHelper::setRequiredFields(array('data'));

			$data = json_decode(Request::get('data'), true);

			foreach ($data as $value)
			{
				$auditTrail = new AuditTrail;
				$auditTrail->data_after = $value['data_after'];
				$auditTrail->reference = $value['reference'];
				$auditTrail->action = $value['action'];
				$auditTrail->module = $value['module'];
				$auditTrail->user_id = Authorizer::getResourceOwnerId();
				$auditTrail->save();
			}

			DB::commit();
			return CommonHelper::return_success();

		}catch(Exception $e) {
			Log::error(__METHOD__ .' Something went wrong: '.print_r($e->getMessage(),true));
			DB::rollback();
			return CommonHelper::return_fail($e->getMessage());
		}
	}

}