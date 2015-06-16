<?php

class ProductList extends Eloquent {

	protected $table = 'product_lists';

	public static function getProductLists($data = array()) {
		$sql_1 = '(SELECT d1.description FROM wms_department d1 WHERE wms_product_lists.dept_code=d1.dept_code AND d1.sub_dept=0 AND d1.class=0 and d1.sub_class=0) AS dept_name';
		$sql_2 = '(SELECT d2.description FROM wms_department d2 WHERE wms_product_lists.dept_code=d2.dept_code AND wms_product_lists.sub_dept=d2.sub_dept AND d2.class=0 and d2.sub_class=0) AS sub_dept_name';

		$query = DB::table('product_lists')->select(DB::raw('convert(wms_product_lists.sku, decimal(15,0)) as sku,convert(wms_product_lists.upc, decimal(20,0)) as upc,wms_product_lists.description,wms_product_lists.short_description,wms_product_lists.dept_code,wms_product_lists.sub_dept, ' . $sql_1 . ', ' . $sql_2));

		if( CommonHelper::hasValue($data['filter_prod_sku']) ) $query->where('sku', 'LIKE', '%'.$data['filter_prod_sku'].'%');
		if( CommonHelper::hasValue($data['filter_prod_upc']) ) $query->where('upc', 'LIKE', '%'.$data['filter_prod_upc'].'%');
		if( CommonHelper::hasValue($data['filter_prod_full_name']) ) $query->where('description', 'LIKE', '%'.$data['filter_prod_full_name'].'%');
		if( CommonHelper::hasValue($data['filter_prod_short_name']) ) $query->where('short_description', 'LIKE', '%'.$data['filter_prod_short_name'].'%');
		if( CommonHelper::hasValue($data['filter_dept_no']) ) $query->where('dept_code', 'LIKE', '%'.$data['filter_dept_no'].'%');
		if( CommonHelper::hasValue($data['filter_sub_dept_no']) ) $query->where('sub_dept', 'LIKE', '%'.$data['filter_sub_dept_no'].'%');

		if( CommonHelper::hasValue($data['sort']) && CommonHelper::hasValue($data['order']))  {
			if ($data['sort']=='full_name') $data['sort'] = 'description';
			if ($data['sort']=='short_name') $data['sort'] = 'short_description';
			if ($data['sort']=='dept') $data['sort'] = 'dept_code';
			if ($data['sort']=='sub_dept') $data['sort'] = 'sub_dept';

			$query->orderBy($data['sort'], $data['order']);
		}

		if( CommonHelper::hasValue($data['limit']) && CommonHelper::hasValue($data['page']))  {
			$query->skip($data['limit'] * ($data['page'] - 1))
		          ->take($data['limit']);
		}

		$result = $query->get();

		return $result;
	}

	public static function getCountProductLists($data = array()) {
		$sql_1 = '(SELECT d1.description FROM wms_department d1 WHERE wms_product_lists.dept_code=d1.dept_code AND d1.sub_dept=0 AND d1.class=0 and d1.sub_class=0) AS dept_name';
		$sql_2 = '(SELECT d2.description FROM wms_department d2 WHERE wms_product_lists.dept_code=d2.dept_code AND wms_product_lists.sub_dept=d2.sub_dept AND d2.class=0 and d2.sub_class=0) AS sub_dept_name';

		$query = DB::table('product_lists')->select(DB::raw('wms_product_lists.*, ' . $sql_1 . ', ' . $sql_2));

		if( CommonHelper::hasValue($data['filter_prod_sku']) ) $query->where('sku', 'LIKE', '%'.$data['filter_prod_sku'].'%');
		if( CommonHelper::hasValue($data['filter_prod_upc']) ) $query->where('upc', 'LIKE', '%'.$data['filter_prod_upc'].'%');
		if( CommonHelper::hasValue($data['filter_prod_full_name']) ) $query->where('description', 'LIKE', '%'.$data['filter_prod_full_name'].'%');
		if( CommonHelper::hasValue($data['filter_prod_short_name']) ) $query->where('short_description', 'LIKE', '%'.$data['filter_prod_short_name'].'%');
		if( CommonHelper::hasValue($data['filter_dept_no']) ) $query->where('dept_code', 'LIKE', '%'.$data['filter_dept_no'].'%');
		if( CommonHelper::hasValue($data['filter_sub_dept_no']) ) $query->where('sub_dept', 'LIKE', '%'.$data['filter_sub_dept_no'].'%');

		return $query->count();
	}

	public static function checkIfUpcExist($upc) {
		$query = ProductList::where('upc', '=', $upc);

		if( $query->first() ) return true;
		else throw new Exception( 'Upc does not exist.');
	}

	public static function getSkuNo($upc) {
		$query = ProductList::select('sku')->where('upc', '=', $upc);

		if( $query->first() ) return $query->first()->sku;
		else throw new Exception( 'Upc does not exist.');
	}
}