<?php

class ApiLoads extends BaseController {


	public function __construct() {
		date_default_timezone_set('Asia/Manila');
	}

	/**
	* Generate Load Code
	*
	* @example  www.example.com/api/{version}/boxes/create/load
	*
	* @return load code
	*/
	public function generateLoadCode()
	{
		try {
			DB::beginTransaction();

			$loadMax =  Load::select(DB::raw('max(id) as max_created, max(load_code) as load_code'))->first()->toArray();

			if($loadMax['max_created'] === null) {
				$loadCode = 'LD0000001';
			} else {
				$loadCode = substr($loadMax['load_code'], -7);
				$loadCode = (int) $loadCode + 1;
				$loadCode = 'LD' . sprintf("%07s", (int)$loadCode);
			}

			Load::create(array('load_code'	=> $loadCode));

			self::generateLoadCodeAuditTrail($loadCode);

			DB::commit();
			return CommonHelper::return_success_message($loadCode);
		} catch (Exception $e) {
			DB::rollback();
			return CommonHelper::return_fail($e->getMessage());
		}
	}

	/**
	* Audit trail for generating load code
	*
	* @example  self::generateLoadCodeAuditTrail()
	*
	* @param  $loadCodeload code
	* @return void
	*/
	private function generateLoadCodeAuditTrail($loadCode)
	{
		$user_id = Authorizer::getResourceOwnerId();
		$userInfo = User::find($user_id);
		$data_after = 'Load code # '.$loadCode . ' generated by ' . $userInfo->username;
		$arrParams = array(
			'module'		=> Config::get("audit_trail_modules.picking"),
			'action'		=> Config::get("audit_trail.generate_load_code"),
			'reference'		=> 'Load code # ' . $loadCode,
			'data_before'	=> '',
			'data_after'	=> $data_after,
			'user_id'		=> $user_id,
			'created_at'	=> date('Y-m-d H:i:s'),
			'updated_at'	=> date('Y-m-d H:i:s')
		);
		AuditTrail::addAuditTrail($arrParams);
	}

	public function getList() {
		try {
			$loads = Load::getLoads();

			return CommonHelper::return_success_message($loads);
		} catch (Exception $e) {
			return CommonHelper::return_fail($e->getMessage());
		}
	}

	public function loadBoxes()
	{
		try {

			CommonHelper::setRequiredFields(array('data', 'load_code'));

			$boxLists = json_decode(Request::get('data'));
			$loadCode = Request::get('load_code');

			DB::beginTransaction();
			// print_r($boxLists); die();
			foreach ($boxLists as $boxCode)
			{
				$boxCode = $boxCode->box_code;
				//get boxes info
				$boxInfo = Box::getBoxList($boxCode);
				if(empty($boxInfo)) throw new Exception("Box code does not exist");
				$soNos = array_unique(explode(',', $boxInfo['so_no'])); //remove duplicate so_no
				StoreOrder::updateLoadCode($soNos, $loadCode);

				$pallete = Pallet::getOrCreatePallete($boxInfo['store_code'], $loadCode);
				PalletDetails::create(array(
					'box_code' 		=> $boxCode, //$boxInfo['box_code'],
					'pallet_code'	=> $pallete['pallet_code']
					));
				$useBox = Box::updateBox(array(
					"box_code"	=> $boxInfo['box_code'],
					"store"		=> $boxInfo['store_code'],
					"in_use"	=> Config::get('box_statuses.in_use')
					));
			}
			self::loadBoxesAuditTrail($boxLists, $loadCode);
			DB::commit();

			return CommonHelper::return_success();
		} catch (Exception $e) {
			DB::rollback();
			return CommonHelper::return_fail($e->getMessage());
		}
	}

	/**
	* Audit trail for picklist loading
	*
	* @example  self::loadBoxesAuditTrail()
	*
	* @param  $boxCodes 	box codes
	* @param  $loadCode 		load code
	* @return void
	*/
	private function loadBoxesAuditTrail($boxCodes, $loadCode)
	{
		$user_id = Authorizer::getResourceOwnerId();
		$userInfo = User::find($user_id);
		$boxCodes = implode(',', $boxCodes);
		$data_after = 'Box code # '.$boxCodes . '  loaded to Load # ' . $loadCode .' by '. $userInfo->username;
		$arrParams = array(
			'module'		=> Config::get("audit_trail_modules.boxing"),
			'action'		=> Config::get("audit_trail.box_load"),
			'reference'		=> 'Box code # ' . $boxCodes,
			'data_before'	=> '',
			'data_after'	=> $data_after,
			'user_id'		=> $user_id,
			'created_at'	=> date('Y-m-d H:i:s'),
			'updated_at'	=> date('Y-m-d H:i:s')
		);
		AuditTrail::addAuditTrail($arrParams);
	}
}