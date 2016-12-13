<?php

class LoadDetails extends Eloquent {

	protected $guarded = array(); 
	/**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'load_details';

    /**
    * Get load_code per pallet
    *
    * @param $palletCode   pallet_code
    * @return array
    */
    public static function getStocktransferLoadList ($loadnumber,$data = array(), $getCount = false)
    {
        $query = DB::table('load_details')
        ->select('load.*','load_details.box_number','store_return_pick_details.*',DB::raw('GROUP_CONCAT(DISTINCT(wms_store_return_pick_details.move_doc_number) SEPARATOR ", "  )  as MASTER_EDU'),'stores.store_code','store_name','stores.address1')
        ->join('load','load_details.load_code','=','load.load_code','left')
        ->join('box','load_details.box_number','=','box.box_code','left')
         ->join('store_return_pick_details','box.tl_number','=','store_return_pick_details.move_doc_number','LEFT')
        ->join('stores','box.store_code','=','stores.store_code','LEFT')
         ->where('load_details.load_code', $loadnumber)
         ->groupBy('box.box_code');

        /*$query = DB::table('load_details')
                    ->SELECT('load_details.load_code', 'load_details.move_doc_number', 'store_return_pickinglist.*', 'stores.store_name', 'store_return_pick_details.to_store_code','load.data_value')
                    ->join('load', 'load_details.load_code','=','load.load_code','left')
                    ->JOIN('store_return_pickinglist','load_details.move_doc_number','=','store_return_pickinglist.move_doc_number','left')
                    ->join('store_return_pick_details','store_return_pickinglist.move_doc_number','=','store_return_pick_details.move_doc_number','left')
                    ->join('stores','store_return_pick_details.from_store_code','=','stores.store_code','left')
                    ->where('store_return_pickinglist.type','=','1')
                    ->where('load_details.load_code', $loadnumber);*/
    if( CommonHelper::hasValue($data['filter_box_code']) ) $query->where('load_details.box_number', 'LIKE', '%'. $data['filter_box_code']. '%');

    if( CommonHelper::hasValue($data['filter_doc_no']) ) $query->where('store_return_pick_details.move_doc_number', 'LIKE', '%'. $data['filter_doc_no']. '%');

    if( CommonHelper::hasValue($data['filter_store_name']) ) $query->where('store_return_pick_details.to_store_code', 'LIKE', '%'. $data['filter_store_name']. '%');
      if( CommonHelper::hasValue($data['filter_store']) ) $query->where('store_return_pick_details.from_store_code', 'LIKE', '%'. $data['filter_store']. '%');
        
    if( CommonHelper::hasValue($data['sort']) && CommonHelper::hasValue($data['order']))  {
            if($data['sort'] == 'doc_no') $data['sort'] = 'store_return_pick_details.move_doc_number';
            $query->orderBy($data['sort'], $data['order']);
        }


        if( CommonHelper::hasValue($data['limit']) && CommonHelper::hasValue($data['page']) && !$getCount)  {
            $query->skip($data['limit'] * ($data['page'] - 1))
                  ->take($data['limit']);
        }

      
        $result = $query->get();
        DebugHelper::log(__METHOD__, $result);

        // get the multiple stock piler fullname
       

        if($getCount) return count($result);
        return $result;
    }
    public static function getLoad($palletCode)
    {
        $query = LoadDetails::where('pallet_code', '=', $palletCode)
            ->first()->toArray();

        return $query;
    }
}
