<?php

class ApiSlots extends BaseController {


	public function __construct() {
		date_default_timezone_set('Asia/Manila');
	}

	/**
	* Get boxes by store
	*
	* @example  www.example.com/api/{version}/slot/is_exist
	* @return boxes
	*/
	public static function getIsSlotExist()
	{
		try {
			CommonHelper::setRequiredFields(array('slot_code'));

			$slotCode = Request::get('slot_code');
			$slot     = SlotList::isSlotExist($slotCode);

			return CommonHelper::return_success_message($slot);
		} catch (Exception $e) {
			return CommonHelper::return_fail($e->getMessage());
		}

	}


}