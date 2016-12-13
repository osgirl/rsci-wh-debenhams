<?php

class StoreReturnPickingdetail extends Eloquent {

	protected $table = 'store_return_pick_details';
	protected $fillable = array('sku', 'so_no');


	public static function getFilteredPicklistDetailStock($data, $getCount= false)
	{
		$query = StoreReturnPickingdetail::where('move_doc_number', $data['picklist_doc'])
			->select(DB::raw('convert(wms_product_lists.sku, decimal(20)) as sku,convert(wms_store_return_pick_details.sku, decimal(20,0)) as upc'),'product_lists.description','store_return_pick_details.quantity_to_pick','store_return_pick_details.moved_qty') 
			->Join('product_lists', 'store_return_pick_details.sku', '=', 'product_lists.upc','left');

		if( CommonHelper::hasValue($data['filter_sku']) ) $query->where('store_return_pick_details.sku', 'LIKE', '%'.$data['filter_sku'].'%');
		if( CommonHelper::hasValue($data['filter_so']) ) $query->where('product_lists.sku', 'LIKE', '%'.$data['filter_so'].'%');
		 
		 

		if( CommonHelper::hasValue($data['sort']) && CommonHelper::hasValue($data['order']))  {
			if ($data['sort']=='sku') $data['sort'] = 'product_lists.sku';
			if ($data['sort']=='upc') $data['sort'] = 'product_lists.upc'; 

			$query->orderBy($data['sort'], $data['order']);
		}

		if( CommonHelper::hasValue($data['limit']) && CommonHelper::hasValue($data['page'])  && !$getCount)  {
			$query->skip($data['limit'] * ($data['page'] - 1))
		          ->take($data['limit']);
		}
		$result = $query->get();

		if($getCount) {
			$result = $query->count();
		}

		return $result;

	}
	



}