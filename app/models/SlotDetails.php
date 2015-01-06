<?php

class SlotDetails extends Eloquent {

	protected $table = 'slot_details';

	public static function getSlotDetailsMain($data = array()) {
		$query = DB::table('slot_details')
						->select(DB::raw('wms_slot_details.slot_id, wms_slot_details.sku, wms_product_lists.upc, wms_product_lists.short_description, SUM(wms_slot_details.quantity) as total_qty, MIN(wms_slot_details.expiry_date) as early_expiry'))
						->join('product_lists', 'slot_details.sku', '=', 'product_lists.upc', 'LEFT');

		if( CommonHelper::hasValue($data['filter_slot_no']) ) $query->where('slot_id', 'LIKE', '%'.$data['filter_slot_no'].'%');
		if( CommonHelper::hasValue($data['filter_prod_sku']) ) $query->where('slot_details.sku', 'LIKE', '%'.$data['filter_prod_sku'].'%');
		if( CommonHelper::hasValue($data['filter_prod_upc']) ) $query->where('product_lists.upc', 'LIKE', '%'.$data['filter_prod_upc'].'%');
		if( CommonHelper::hasValue($data['filter_date_from']) && CommonHelper::hasValue($data['filter_date_to'])) $query->whereBetween('expiry_date', array($data['filter_date_from'] . ' 00:00:00', $data['filter_date_to'] . ' 23:59:59'));
		
		if( CommonHelper::hasValue($data['sort']) && CommonHelper::hasValue($data['order']))  {
			if ($data['sort']=='slot_no') $data['sort'] = 'slot_id';
			if ($data['sort']=='sku') $data['sort'] = 'slot_details.sku';
			if ($data['sort']=='upc') $data['sort'] = 'product_lists.upc';
			if ($data['sort']=='quantity') $data['sort'] = 'total_qty';
			if ($data['sort']=='expiry_date') $data['sort'] = 'early_expiry';
			if ($data['sort']=='short_name') $data['sort'] = 'product_lists.short_description';
			
			$query->orderBy($data['sort'], $data['order']);
		}
		
		if( CommonHelper::hasValue($data['limit']) && CommonHelper::hasValue($data['page']))  {
			$query->skip($data['limit'] * ($data['page'] - 1))
		          ->take($data['limit']);
		}
		
		$result = $query->groupBy('slot_details.slot_id', 'slot_details.sku')
						->get();
			
		return $result;
	}
	
	public static function getCountSlotDetailsMain($data = array()) {
		$query = DB::table('slot_details')
						->select(DB::raw('wms_slot_details.slot_id, wms_slot_details.sku, wms_product_lists.upc, wms_product_lists.short_description, SUM(wms_slot_details.quantity) as total_qty, MIN(wms_slot_details.expiry_date) as early_expiry'))
						->join('product_lists', 'slot_details.sku', '=', 'product_lists.upc', 'LEFT');

		if( CommonHelper::hasValue($data['filter_slot_no']) ) $query->where('slot_id', 'LIKE', '%'.$data['filter_slot_no'].'%');
		if( CommonHelper::hasValue($data['filter_prod_sku']) ) $query->where('slot_details.sku', 'LIKE', '%'.$data['filter_prod_sku'].'%');
		if( CommonHelper::hasValue($data['filter_prod_upc']) ) $query->where('product_lists.upc', 'LIKE', '%'.$data['filter_prod_upc'].'%');
		if( CommonHelper::hasValue($data['filter_date_from']) && CommonHelper::hasValue($data['filter_date_to'])) $query->whereBetween('expiry_date', array($data['filter_date_from'] . ' 00:00:00', $data['filter_date_to'] . ' 23:59:59'));
				
		$result = $query->groupBy('slot_details.slot_id', 'slot_details.sku')
						->get();
			
		return $result;
	}
	
	public static function getSlotDetails($data = array()) {
		$query = DB::table('slot_details')->join('product_lists', 'slot_details.sku', '=', 'product_lists.upc', 'LEFT')
										  ->where('slot_details.slot_id', '=', $data['slot'])
										  ->where('slot_details.sku', '=', $data['sku']);

		if( CommonHelper::hasValue($data['sort']) && CommonHelper::hasValue($data['order']))  {
			if ($data['sort']=='sku') $data['sort'] = 'slot_details.sku';
			if ($data['sort']=='upc') $data['sort'] = 'product_lists.upc';
			if ($data['sort']=='short_name') $data['sort'] = 'product_lists.short_description';
			
			$query->orderBy($data['sort'], $data['order']);
		}
		
		if( CommonHelper::hasValue($data['limit']) && CommonHelper::hasValue($data['page']))  {
			$query->skip($data['limit'] * ($data['page'] - 1))
		          ->take($data['limit']);
		}
		
		$result = $query->get();
				
		return $result;
	}
	
	public static function getCountSlotDetails($data = array()) {
		$query = DB::table('slot_details')->join('product_lists', 'slot_details.sku', '=', 'product_lists.upc', 'LEFT')
										  ->where('slot_details.slot_id', '=', $data['slot'])
										  ->where('slot_details.sku', '=', $data['sku']);
			   								   
		return $query->count();
	}

	public static function insertData($data = array(), $po_id = NULL) {
		SlotDetails::insert($data); 
	}

	/**
	* Check if slot exists
	*
	* @example  SlotDetails::_isSlotExist({slot_id})
	*
	* @param  slot_id   slot id
	* @throws Exception Slot id does not exist
	* @return Status
	*/ 
	public static function _isSlotExist($slot_id) {
		$query = SlotList::where('slot_code', '=', $slot_id)->first();

		if(! $query) throw new Exception( 'Slot id does not exist.');
	}

	public static function _checkQuantity($data = array(), $po_id) {
		$query = SlotDetails::join('purchase_order_details', 'slot_details.sku', '=', 'purchase_order_details.sku', 'LEFT')
							->where('slot_details.sku', '=', $data['sku'])
							->where('purchase_order_details.po_id', '=', $po_id);
		
		$result = $query->sum('quantity');
		DebugHelper::log(__METHOD__, $result);

		return $result;
	}
}