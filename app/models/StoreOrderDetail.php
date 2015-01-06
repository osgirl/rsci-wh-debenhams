<?php

class StoreOrderDetail extends Eloquent {

	protected $table = 'store_order_detail';


	/**
	* Gets products for the store using store order list
	*
	* @example  StoreOrderDetail::getProductList();
	* @param   storeCode    store code
	*
	* @return upcs
	*/ 
	public static function getProductList($data)
	{
		/*$upcs =  DB::select(DB::raw("select stores.store_name, sku, SUM(ordered_qty) ordered_qty, SUM(delivered_qty) delivered_qty
			from wms_store_order so
			right join wms_store_order_detail so_detail on so.so_no = so_detail.so_no
			inner join wms_stores stores on stores.store_code = so.store_code
			where so.store_code = {$data['storeCode']} and so_status = ".Config::get('so_statuses.done')." and load_code = '{$data['loadCode']}'
			group by sku, load_code, so.store_code" ));*/
		
		$upcs =  DB::select(DB::raw("select stores.store_name, so_detail.sku, SUM(ordered_qty) ordered_qty, SUM(delivered_qty) delivered_qty, description
			from wms_store_order so
			right join wms_store_order_detail so_detail on so.so_no = so_detail.so_no
			inner join wms_product_lists product_lists on product_lists.upc = so_detail.sku
			inner join wms_stores stores on stores.store_code = so.store_code
			where so.store_code = {$data['storeCode']} and so_status = ".Config::get('so_statuses.done')." and load_code = '{$data['loadCode']}'
			group by so_detail.sku, load_code, so.store_code" ));
		return $upcs;
	}

	public static function getStoreOrderDetail($storeCode, $loadCode, $sku)
	{
		$storeOrderDetails = StoreOrderDetail::select('store_order_detail.id', 'store_order_detail.so_no','store_order_detail.sku', 'ordered_qty', 'packed_qty', 'delivered_qty', 'store_order_detail.created_at', 'store_order.store_code', 'store_order.load_code', 'store_order.so_status', 'store_order.assigned_user_id' )
			->join('store_order', 'store_order.so_no', '=', 'store_order_detail.so_no')
			->where('sku', '=', $sku)
			->where('store_order.load_code', '=', $loadCode)
			->where('store_order.store_code', '=', $storeCode)
			->where('store_order.so_status', '=',Config::get('so_statuses.done') )
			->get();
		
		return $storeOrderDetails;
	}

	public static function receiveSo($sodId, $qtyToReceive)
	{
		$storeOrderDetail = StoreOrderDetail::where('id', '=', $sodId)
			->get()->first();

		$newQtyToReceive = intval($storeOrderDetail->delivered_qty) + $qtyToReceive; 
		if((int)$storeOrderDetail->ordered_qty < $newQtyToReceive) {
			throw new Exception("Received quantity is greater than ordered quantity.");
		}
		StoreOrderDetail::where('id', '=', $sodId)
			->update(array(
				'delivered_qty'	=> $newQtyToReceive,
				'updated_at'	=> date('Y-m-d H:i:s')
			));
		return true;
	}

	/********************Methods for CMS only**************************/


	public static function getSODetails($so_no,$data = array()){
		$query = DB::table('store_order_detail')
					->join('product_lists', 'store_order_detail.sku', '=', 'product_lists.upc')
					->where('store_order_detail.so_no', '=', $so_no);

		if( CommonHelper::hasValue($data['sort']) && CommonHelper::hasValue($data['order']))  {
			if ($data['sort']=='sku') $data['sort'] = 'product_lists.upc';
			if ($data['sort']=='short_name') $data['sort'] = 'product_lists.short_description';
			if ($data['sort']=='ordered_quantity') $data['sort'] = 'store_order_detail.ordered_qty';
			if ($data['sort']=='delivered_quantity') $data['sort'] = 'store_order_detail.delivered_qty';
			
			$query->orderBy($data['sort'], $data['order']);
		}

		if( CommonHelper::hasValue($data['limit']) && CommonHelper::hasValue($data['page']))  {
			$query->skip($data['limit'] * ($data['page'] - 1))
		          ->take($data['limit']);
		}

		$result = $query->get();
		
		return $result;
	}

	public static function getCountSODetails($so_no) {
		$storeOrderDetail = StoreOrderDetail::select('store_order_detail.*', 'product_lists.description')
			->where('so_no', '=', $so_no)
			->join('product_lists', 'store_order_detail.sku', '=', 'product_lists.upc')
			->get();
		
		return $storeOrderDetail->count();			
	}

	public static function getMtsDetails($so_no,$data = array(), $getCount = FALSE){
		/*
		SELECT store_order.so_no, store_order.store_code, store_order_detail.sku, box_details.picklist_detail_id, box_details.box_code,store_order_detail.ordered_qty,  box_details.moved_qty, picklist_details.sequence_no, store_order.load_code
		FROM `wms_store_order_detail` store_order_detail
		LEFT JOIN wms_store_order store_order ON store_order_detail.so_no = store_order.so_no
		INNER JOIN wms_picklist_details picklist_details ON picklist_details.so_no = store_order.so_no AND store_order_detail.sku = picklist_details.sku
		RIGHT JOIN wms_box_details box_details ON box_details.picklist_detail_id = picklist_details.id
		WHERE store_order.so_no = 10708
		ORDER BY sequence_no, store_order_detail.sku ASC
		*/
		$query = DB::table('store_order_detail')
					->join('product_lists', 'store_order_detail.sku', '=', 'product_lists.upc')
					->leftJoin('store_order', 'store_order_detail.so_no', '=', 'store_order.so_no')
					->join('picklist_details',function($join)
					{
						$join->on('picklist_details.so_no', '=', 'store_order.so_no')
							 ->on('store_order_detail.sku', '=', 'picklist_details.sku');
					})
					->join('box_details', 'box_details.picklist_detail_id', '=', 'picklist_details.id', 'RIGHT')
					->where('store_order_detail.so_no', '=', $so_no);

		if( CommonHelper::hasValue($data['sort']) && CommonHelper::hasValue($data['order']))  {
			if ($data['sort']=='sku') $data['sort'] = 'product_lists.upc';
			if ($data['sort']=='short_name') $data['sort'] = 'product_lists.short_description';
			if ($data['sort']=='ordered_quantity') $data['sort'] = 'store_order_detail.ordered_qty';
			if ($data['sort']=='delivered_quantity') $data['sort'] = 'store_order_detail.delivered_qty';
			
			$query->orderBy('box_details.box_code', 'ASC')
				  ->orderBy($data['sort'], $data['order']);
		}

		if( CommonHelper::hasValue($data['limit']) && CommonHelper::hasValue($data['page']))  {
			$query->skip($data['limit'] * ($data['page'] - 1))
		          ->take($data['limit']);
		}

		if($getCount) $result = $query->count();
		else $result = $query->get(
			array('store_order.so_no', 'store_order.store_code', 
					'product_lists.sku', 'product_lists.upc', 'product_lists.description', 
					'box_details.picklist_detail_id', 'box_details.box_code',
					'store_order_detail.ordered_qty', 'box_details.moved_qty', 'store_order_detail.delivered_qty',
					'picklist_details.sequence_no', 'store_order.load_code'));
		
		DebugHelper::log(__METHOD__, $result);
		return $result;
	}

}