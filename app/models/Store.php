<?php

class Store extends Eloquent {

	protected $table = 'stores';

	public static function getStoreList($data = array()) {
		$query = DB::table('stores');

		if( CommonHelper::hasValue($data['filter_store_code']) ) $query->where('store_code', 'LIKE', '%'.$data['filter_store_code'].'%');
		if( CommonHelper::hasValue($data['filter_store_name']) ) $query->where('store_name', 'LIKE', '%'.$data['filter_store_name'].'%');
		

		if( CommonHelper::hasValue($data['sort']) && CommonHelper::hasValue($data['order']))  {
			$query->orderBy($data['sort'], $data['order']);
		}
		
		if( CommonHelper::hasValue($data['limit']) && CommonHelper::hasValue($data['page']))  {
			$query->skip($data['limit'] * ($data['page'] - 1))
		          ->take($data['limit']);
		}
		
		$result = $query->get();
				
		return $result;
	}

	public static function getCountstoreLists($data = array())
	{
		$query = DB::table('stores');

		if( CommonHelper::hasValue($data['filter_store_code']) ) $query->where('store_code', 'LIKE', '%'.$data['filter_store_code'].'%');
		if( CommonHelper::hasValue($data['filter_store_name']) ) $query->where('store_name', 'LIKE', '%'.$data['filter_store_name'].'%');
		$result = $query->count();

		return $result;
	}

	public static function getStoreName($storeCode)
	{
		$storeName = Store::where('store_code', '=', $storeCode)
			->pluck('store_name');
			
		return $storeName;
	}
	
}