<?php

class SkuOnDock extends Eloquent {

	protected $table = 'sku_on_dock';
	
	public static function insertData($data = array()) {
		$params = array( 
				'sku' => $data['sku'],
				'total_qty_delivered' => $data['quantity_delivered'],
				'total_qty_remaining' => $data['quantity_delivered']
			);

		//check if sku exists
		$isExists = SkuOnDock::where('sku', '=', $data['sku'])->first();		

		if( is_null($isExists) ) SkuOnDock::insert($params);
		else 
		{
			Log::info(__METHOD__ .' isExists values: '.print_r($isExists->toArray(),true));
			$params['old_total_qty_delivered'] = $isExists['total_qty_delivered'];
			$params['old_total_qty_remaining'] = $isExists['total_qty_remaining'];
			SkuOnDock::updateData($params);
		}

		DebugHelper::log(__METHOD__, NULL);
	}

	public static function updateData($data = array()) {
		$total_qty_delivered = SkuOnDock::_checkPoDetailQuantity($data);
		$offset = $total_qty_delivered - $data['old_total_qty_delivered'];
		Log::info(__METHOD__ .' $total_qty_delivered: '.$total_qty_delivered. ' $old_total_qty_delivered: '. $data['old_total_qty_delivered']. ' offset: '. $offset);
		
		if($offset != 0) 
		{
			$total_qty_remaining = $data['old_total_qty_remaining'] + $offset;
		}
		else {
			$total_qty_remaining = $data['old_total_qty_remaining'];	
		}

		$params = array( 
				'sku' 				  => $data['sku'],
				'total_qty_delivered' => $total_qty_delivered,
				'total_qty_remaining' => $total_qty_remaining,
				'updated_at'		  => date('Y-m-d H:i:s')
			);
		
		Log::info(__METHOD__ .' params: '.print_r($params,true));

		$query = SkuOnDock::where('sku', '=', $data['sku'])
						  ->update($params);

		return $query;
	}

	public static function _checkPoDetailQuantity($data = array()) {
		$query = PurchaseOrderDetail::where('sku', '=', $data['sku'])
									->where('deleted_at', '=', '0000-00-00 00:00:00');
		
		$result = $query->sum('quantity_delivered');
		DebugHelper::log(__METHOD__, $result);

		return $result;
	}

	public static function _checkQty($data = array()) {
		$query = SkuOnDock::where('sku', '=', $data['sku'])->first();

		return $query;
	}

	public static function reduceTotalQtyRemaining($data = array()) {
		$total_qty_remaining = SkuOnDock::where('sku', '=', $data['sku'])->first();

		$offset_qty_remaining = $total_qty_remaining['total_qty_remaining'] - $data['quantity'];

		$arrParams = array('total_qty_remaining' => $offset_qty_remaining, 'updated_at' => date('Y-m-d H:i:s'));
		$query = SkuOnDock::where('sku', '=', $data['sku'])
						  ->update($arrParams);

		return $query;
	}

	public static function getAll($data = array()) {
		$query = SkuOnDock::where('sku_on_dock.created_at', '<>', '0000-00-00 00:00:00')
			->join('product_lists', 'product_lists.upc', '=', 'sku_on_dock.sku')
			->where('total_qty_delivered' , '>', 0)
			->where('total_qty_remaining' , '>', 0);
		// return $query->get(array('sku_on_dock.id','total_qty_delivered',
		// 		'total_qty_remaining', 'product_lists.upc', 'product_lists.description'));
		return $query->get(array('sku_on_dock.*', 'product_lists.description'));

	}

	public static function getAllCount($data = array()) {
		$query = SkuOnDock::where('created_at', '<>', '0000-00-00 00:00:00');

		if( CommonHelper::hasValue($data['limit']) && CommonHelper::hasValue($data['page']))  {
			$query->skip($data['limit'] * ($data['page'] - 1))
		          ->take($data['limit']);
		}														

		return $query->count();

	}
}