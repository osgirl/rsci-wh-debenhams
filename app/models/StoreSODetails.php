<?php

class StoreSODetails extends Eloquent {

	protected $table = 'store_so_details';

	public static function updateDeliveredQty($data = array(), $so_no) 
	{
		if(! CommonHelper::hasValue($so_no) ) throw new Exception( 'So no is missing from parameter.');
		if(! CommonHelper::hasValue($data['sku']) ) throw new Exception( 'Sku is missing from data parameter.');

		$query = StoreSODetails::where('sku', '=', $data['sku'])->where('so_no', '=', $so_no);

		StoreSODetails::isSKUExist($query);
		StoreSODetails::checkIfQtyExceeds($query, $data['delivered_qty']);

		$array_params = array(
			'delivered_qty' => $data['delivered_qty'],
			'updated_at' => date('Y-m-d H:i:s')
		);

		$result = $query->update($array_params);
		DebugHelper::log(__METHOD__, $result);


		return $result;
	}

	public static function isSKUExist($query) 
	{
		$isExists = $query->first();
		DebugHelper::log(__METHOD__, $isExists);

		if( is_null($isExists) ) throw new Exception( 'SKU not found in the database.');
		return;
	}

	public static function checkIfQtyExceeds($query, $qty_delivered) 
	{
		$row = $query->first();
		DebugHelper::log(__METHOD__, $row);
		if(! empty($row) ) {
			$row = $row->toArray();
			if( $row["ordered_qty"] < $qty_delivered ) throw new Exception( "SKU: {$row['sku']} cannot accept more than the expected quantity.");
		}
		return;
	}

	public static function getSoDetail($data = array())
	{
		$so_no = $data['so_no'];
		$query = StoreSODetails::join('store_so', 'store_so.so_no', '=', 'store_so_details.so_no', 'LEFT')
						->where('store_so.so_no', '=', $so_no)
						->where('so_status', '=', 0);

		$result = $query->get(array("store_so_details.*"));
		DebugHelper::log(__METHOD__, $result);
		return $result;
	}
}
