<?php

class BoxDetails extends Eloquent {

	protected $guarded = array();
	/**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'box_details';

    public $timestamps = false;

	protected $fillable = array('picklist_detail_id', 'box_code', 'moved_qty', 'created_at', "updated_at");

	/*
	*
	*  add or update box manifest detail
	*
	*/
	public static function moveToBox($picklistDetailId, $boxCode,$qtyToMove)
	{
		//check if box exists in header
		$boxDetail = BoxDetails::where('picklist_detail_id', '=', $picklistDetailId)
			->where('box_code', '=', $boxCode)
			->first();

		if(count($boxDetail) === 0) {
			self::addBoxManifestDetail($picklistDetailId, $boxCode,$qtyToMove);
		} else {
			//TODO::checking if shipped, need load here
			/*if((int)$boxDetail->is_shipped == 1) {
				throw new Exception("You are trying to use a shipped box.");
			}*/
			self::updateBoxManifestDetail($picklistDetailId, $boxCode, intval($boxDetail->moved_qty) + $qtyToMove);
		}

	}

	/*
	*
	*  create news box manifest detail
	*
	*/
	public static function addBoxManifestDetail($picklistDetailId,$boxCode,$qtyToMove)
	{
		BoxDetails::create(array(
			"picklist_detail_id"		=> intval($picklistDetailId),
			"box_code"					=> $boxCode,
			"moved_qty"					=> $qtyToMove,
			"created_at"				=> date('Y-m-d H:i:s'),
			"updated_at"				=> date('Y-m-d H:i:s')
		));

		return;
	}

	/*
	*
	*  updateBoxManifestDetail
	*
	*/

	public static function updateBoxManifestDetail($picklistDetailId,$boxCode,$qtyToMove)
	{
		BoxDetails::where('picklist_detail_id', '=', $picklistDetailId)
			->where('box_code', '=', $boxCode)
			->update(array("moved_qty"		=> $qtyToMove,
				"updated_at"				=> date('Y-m-d H:i:s')));
		return;
	}

	public static function getBoxesByPicklistDetail($picklistDocNo)
    {
        $boxes = BoxDetails::join('picklist_details', 'picklist_details.id', '=', 'box_details.picklist_detail_id')
        	->where('picklist_details.move_doc_number', '=', $picklistDocNo)
        	->groupBy('box_details.box_code')
        	->lists('box_code');

        return $boxes;
    }


    /**************** For CMS only******************/

    /******************Methods for CMS only*************************/

	public static function getBoxDetails($box_code,$data = array(), $getCount = false)
	{

		$query = DB::table('box_details')
					->select('picklist_details.sku', 'box_details.moved_qty', 'box_details.box_code', 'product_lists.short_description')
					->join('box', 'box_details.box_code', '=', 'box.box_code', 'LEFT')
					->join('stores', 'stores.store_code', '=', 'box.store_code', 'LEFT')
					->join('picklist_details', 'picklist_details.id', '=', 'box_details.picklist_detail_id', 'LEFT')
					->join('product_lists', 'picklist_details.sku', '=', 'product_lists.upc')
					->where('box_details.box_code', '=', $box_code);

		if( CommonHelper::hasValue($data['filter_sku']) ) $query->where('picklist_details.sku', 'LIKE', '%'.$data['filter_sku'].'%');
		if( CommonHelper::hasValue($data['filter_store'])) $query->where('stores.store_name', 'LIKE', '%'.$data['filter_store'].'%');
		if( CommonHelper::hasValue($data['filter_box_code']) ) $query->where('box.box_code', 'LIKE', '%'.$data['filter_box_code'].'%');

		if( CommonHelper::hasValue($data['sort']) && CommonHelper::hasValue($data['order']))  {
			if ($data['sort']=='box_code') $data['sort'] = 'box_details.box_code';
			if ($data['sort']=='store') $data['sort'] = 'stores.store_name';
			if ($data['sort']=='date_created') $data['sort'] = 'box.created_at';


			$query->orderBy($data['sort'], $data['order']);
		}

		if( CommonHelper::hasValue($data['limit']) && CommonHelper::hasValue($data['page'])  && !$getCount)  {
			$query->skip($data['limit'] * ($data['page'] - 1))
		          ->take($data['limit']);
		}
		$result = $query->get();
		DebugHelper::log(__METHOD__, $result);
		if($getCount) {
			$result = $query->count();
		}



		return $result;
	}

	public static function isBoxEmpty($boxCode)
	{
		$query = BoxDetails::where('box_code', '=', $boxCode)->get();

		return ( ($query->isEmpty()) ? false : true );
	}

    public static function getBoxDetailCount($boxCode)
    {
    	$boxDetailCount = BoxDetails::where('box_code', '=', $boxCode)
    		->count();
    	return $boxDetailCount;
    }

    public static function getTotalMovedQty($boxCode)
    {
    	$boxMovedQty = BoxDetails::select(DB::raw('SUM(moved_qty) moved_qty'))
    		->where('box_code', '=', $boxCode)
    		->first()
    		->toArray();
    	return $boxMovedQty;
    }
    /*
    SELECT box.box_code, box.in_use FROM `wms_box_details` box_details
LEFT JOIN wms_picklist_details picklist_details ON picklist_details.id = box_details.picklist_detail_id
LEFT JOIN wms_box box ON box.box_code = box_details.box_code
WHERE move_doc_number = 447 AND box.in_use = 0
GROUP BY box.box_code
    */
    public static function checkBoxesPerDocNo($docNo)
    {
        $query = BoxDetails::where('move_doc_number', '=', $docNo)
        	->where('box.in_use', '=', 0)
        	->leftJoin('picklist_details', 'picklist_details.id', '=', 'box_details.picklist_detail_id')
        	->leftJoin('box', 'box.box_code', '=', 'box_details.box_code')
        	->groupBy('box.box_code')
        	->get();
        DebugHelper::log(__METHOD__, $query);
        return $query;
    }

    /**
    * Get the unique box_Code per picklist document number
    *
    * @param $docNo 	picklist document number
    * @return array
    */
    public static function getUniqueBoxPerDocNo($docNo)
    {
    	/*SELECT DISTINCT pl.move_doc_number, b.store_code, MIN(bd.box_code) box_code
					FROM wms_box_details bd
					INNER JOIN wms_box b ON b.box_code = bd.box_code
					INNER JOIN wms_picklist_details pd ON bd.picklist_detail_id = pd.id
                    INNER JOIN wms_picklist pl ON pl.move_doc_number = pd.move_doc_number
					WHERE bd.sync_status = 0 AND pl_status = 2
					GROUP BY pl.move_doc_number
					ORDER BY pl.move_doc_number, sequence_no ASC*/

		$arrParams = array('data_code' => 'PICKLIST_STATUS_TYPE', 'data_value'=> 'done');
		$status = Dataset::getType($arrParams)->toArray();
		$query = BoxDetails::select(DB::raw('DISTINCT wms_picklist.move_doc_number, wms_box.store_code, MIN(wms_box_details.box_code) box_code'))
			->join('box', 'box.box_code', '=', 'box_details.box_code')
			->join('picklist_details', 'box_details.picklist_detail_id', '=', 'picklist_details.id')
			->join('picklist', 'picklist.move_doc_number', '=', 'picklist_details.move_doc_number')
			->where('pl_status', '=', $status['id'])
			->where('picklist.move_doc_number', '=', $docNo)
			->groupBy('picklist.move_doc_number')
			->orderBy('picklist.move_doc_number', 'sequence_no')
			->first()->toArray();

		// DebugHelper::log(__METHOD__, $query);

		return $query;
    }

    public static function getAllBoxes()
    {
        $query = BoxDetails::select('box.box_code', 'store_name', 'picklist_details.created_at as order_date')
        	->where('box.in_use', '=', 0)
        	->leftJoin('picklist_details', 'picklist_details.id', '=', 'box_details.picklist_detail_id')
        	// ->leftJoin('store_order', 'picklist_details.so_no', '=', 'store_order.so_no')
        	->leftJoin('box', 'box.box_code', '=', 'box_details.box_code')
        	->join('stores', 'stores.store_code', '=', 'box.store_code', 'LEFT')
        	->groupBy('box.box_code')
        	->get();
        DebugHelper::log(__METHOD__, $query);
        return $query;
    }
}