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
	public function insertRecord() {
		try {
			DB::beginTransaction();
			CommonHelper::setRequiredFields(array('data', 'load_code'));

			$data = json_decode(Request::get('data'), true);
			$load_code = Request::get('load_code');

			foreach ($data as $value)
			{
				$interTransfer              = InterTransfer::firstOrNew(array('load_code'=>$load_code, 'mts_number'=>$value['mts_number']));
				$interTransfer->load_code   = $load_code;
				$interTransfer->mts_number  = $value['mts_number'];
				$interTransfer->no_of_boxes = $value['no_of_boxes'];
				$interTransfer->updated_at  = date('Y-m-d H:i:s');
				$interTransfer->save();
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