<?php

class StoreSO extends Eloquent {

	protected $table = 'store_so';

	public static function getOpenSo($data = array()) {
		$store_code = $data['store_code'];
		$query = StoreSO::where('store_code', '=', $store_code)
				->where('so_status', '=','0');

		$result = $query->get(array('so_no', 'so_status', 'delivery_date'));
		DebugHelper::log(__METHOD__, $result);
		return $result;
	}

	public static function updateSoStatus($data = array()) {
		$so_no = $data['so_no'];
		$assigned_user_id = $data['assigned_user_id'];

		$result = StoreSO::where('so_no', '=', $so_no)
				->update(array(
					"assigned_user_id"=> $assigned_user_id,
					"so_status" => 1,
					'updated_at' => date('Y-m-d H:i:s')
				));

		DebugHelper::log(__METHOD__, $result);

		return $result;
	}
}
