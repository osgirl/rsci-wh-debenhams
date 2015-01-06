<?php

class Vendors extends Eloquent {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'vendors';

	public static function getVendorLists($data = array(), $getCount=false) {
		$query = DB::table('vendors');

		if( CommonHelper::hasValue($data['filter_vendor_no']) ) $query->where('vendor_code', 'LIKE', '%'.$data['filter_vendor_no'].'%');
		if( CommonHelper::hasValue($data['filter_vendor_name']) ) $query->where('vendor_name', 'LIKE', '%'.$data['filter_vendor_name'].'%');
		
		if( CommonHelper::hasValue($data['sort']) && CommonHelper::hasValue($data['order']))  {
			if ($data['sort']=='date') $data['sort'] = 'created_at';
			
			$query->orderBy($data['sort'], $data['order']);
		}
		
		if( CommonHelper::hasValue($data['limit']) && CommonHelper::hasValue($data['page']) && $getCount == false)  {
			$query->skip($data['limit'] * ($data['page'] - 1))
		          ->take($data['limit']);
		}
		
		if($getCount) {
			$result = $query->count();
		} else{
			$result = $query->get();
		}
		
				
		return $result;
	}

}