<?php

class ReverseLogisticDetails extends Eloquent {

	protected $table = 'reverse_logistic_det';
	protected $fillable = array('sku', 'so_no');

	 
	 public static function getReversedetails($data, $getCount= false)
	{
		$query = ReverselogisticDetails::where('move_doc_number', $data['picklist_doc'])
			->select(DB::raw('convert(wms_product_lists.sku, decimal(20)) as sku'),'reverse_logistic_det.upc','product_lists.description','reverse_logistic_det.delivered_qty as quantity_to_pick','reverse_logistic_det.moved_qty' )
		 
			->Join('product_lists', 'reverse_logistic_det.upc', '=', 'product_lists.upc','left');

		if( CommonHelper::hasValue($data['filter_sku']) ) $query->where('reverse_logistic_det.upc', 'LIKE', '%'.$data['filter_sku'].'%');
		if( CommonHelper::hasValue($data['filter_so']) ) $query->where('product_lists.sku', 'LIKE', '%'.$data['filter_so'].'%');

		if( CommonHelper::hasValue($data['sort']) && CommonHelper::hasValue($data['order']))  {
			if ($data['sort']=='sku') $data['sort'] = 'reverse_logistic_det.upc';
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