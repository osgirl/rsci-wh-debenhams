<?php

class Inventory extends Eloquent {

	protected $guarded = array(); 
	/**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'inventory';

    public static function getInventoryMain($data = array(), $getCount = FALSE) {
		$query = DB::table('inventory')
						->select(DB::raw('wms_inventory.slot_id, wms_inventory.sku, wms_product_lists.sku upc, wms_product_lists.short_description, SUM(wms_inventory.quantity_on_hand) as total_qty, MIN(wms_inventory.created_at) as early_expiry')) 
						->join('product_lists', 'inventory.sku', '=', 'product_lists.upc', 'LEFT');

		if( CommonHelper::hasValue($data['filter_slot_no']) ) $query->where('slot_id', 'LIKE', '%'.$data['filter_slot_no'].'%');
		if( CommonHelper::hasValue($data['filter_prod_sku']) ) $query->where('product_lists.sku', 'LIKE', '%'.$data['filter_prod_sku'].'%');
		if( CommonHelper::hasValue($data['filter_prod_upc']) ) $query->where('product_lists.upc', 'LIKE', '%'.$data['filter_prod_upc'].'%');
		if( CommonHelper::hasValue($data['filter_date_from']) && CommonHelper::hasValue($data['filter_date_to'])) $query->whereBetween('inventory.created_at', array($data['filter_date_from'] . ' 00:00:00', $data['filter_date_to'] . ' 23:59:59'));
		
		if( CommonHelper::hasValue($data['sort']) && CommonHelper::hasValue($data['order']))  {
			if ($data['sort']=='slot_no') $data['sort'] = 'slot_id';
			if ($data['sort']=='sku') $data['sort'] = 'inventory.sku';
			if ($data['sort']=='upc') $data['sort'] = 'product_lists.upc';
			if ($data['sort']=='quantity') $data['sort'] = 'total_qty';
			if ($data['sort']=='quantity') $data['sort'] = 'total_qty';
			if ($data['sort']=='created_at') $data['sort'] = 'inventory.created_at';
			// if ($data['sort']=='expiry_date') $data['sort'] = 'created_at';
			if ($data['sort']=='short_name') $data['sort'] = 'product_lists.short_description';
			
			$query->orderBy($data['sort'], $data['order']);
		}
		
		if( CommonHelper::hasValue($data['limit']) && CommonHelper::hasValue($data['page']) && !$getCount)  {
			$query->skip($data['limit'] * ($data['page'] - 1))
		          ->take($data['limit']);
		}
		
		$result = $query->groupBy('inventory.slot_id', 'inventory.sku')->get();
		if($getCount) {
			$result = count($result);
		}

		DebugHelper::log(__METHOD__, $result);
		return $result;
	}
	
	public static function getInventory($data = array()) {
		$query = DB::table('inventory')->join('product_lists', 'inventory.sku', '=', 'product_lists.upc', 'LEFT')
										  ->where('inventory.slot_id', '=', $data['slot'])
										  ->where('inventory.sku', '=', $data['sku']);

		if( CommonHelper::hasValue($data['sort']) && CommonHelper::hasValue($data['order']))  {
			if ($data['sort']=='sku') $data['sort'] = 'inventory.sku';
			if ($data['sort']=='upc') $data['sort'] = 'product_lists.upc';
			if ($data['sort']=='short_name') $data['sort'] = 'product_lists.short_description';
			
			$query->orderBy($data['sort'], $data['order']);
		}
		
		if( CommonHelper::hasValue($data['limit']) && CommonHelper::hasValue($data['page']))  {
			$query->skip($data['limit'] * ($data['page'] - 1))
		          ->take($data['limit']);
		}
		
		$result = $query->get();
		DebugHelper::log(__METHOD__, $result);
		return $result;
	}
	
	public static function getCountInventory($data = array()) {
		$query = DB::table('inventory')->join('product_lists', 'inventory.sku', '=', 'product_lists.upc', 'LEFT')
										  ->where('inventory.slot_id', '=', $data['slot'])
										  ->where('inventory.sku', '=', $data['sku']);
			   								   
		return $query->count();
	}

}