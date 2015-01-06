<?php

class ApiSlotMasterList extends BaseController {
	
	/**
	 * Display a upc listing of the resource.
	 *
	 * @return Response
	 */
	public function index() {

		try {
			$slot_list = SlotList::all();
			
			DebugHelper::log(__METHOD__, $slot_list->toArray());
			return Response::json(array(
				'error' => false,
				'message' => 'Success',
				'result' => $slot_list->toArray()),
				200
			);

		}catch(Exception $e) {
			return Response::json(array(
				"error" => true,
				"result" => $e->getMessage()),
				400
			);
		}
	}
	
}