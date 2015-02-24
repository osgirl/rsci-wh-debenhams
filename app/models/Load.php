<?php

class Load extends Eloquent {

	protected $guarded = array();
	/**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'load';
    /*****For cms*****/
    public static function getLoadCodes()
    {
    	$loadCodes = Load::where('is_shipped', '=', 0)->lists('load_code', 'load_code');
    	return $loadCodes;
    }

    public static function getLoads()
    {
        return Load::where('is_shipped', '=', 0)->get(array('id','load_code'));
    }

    public static function getLoadList($data = array(), $getCount = false)
    {
        $query = Load::select(DB::raw("wms_load.id, wms_load.load_code, wms_load.is_shipped, group_concat(wms_pallet.store_code SEPARATOR ',') stores"))
            ->join('load_details', 'load_details.load_code', '=', 'load.load_code')
            ->join('pallet', 'pallet.pallet_code', '=', 'load_details.pallet_code')
            ->groupBy('load.load_code');

        if( CommonHelper::hasValue($data['filter_load_code']) ) $query->where('load.load_code', 'LIKE', '%'. $data['filter_load_code'] . '%');

        if( CommonHelper::hasValue($data['sort']) && CommonHelper::hasValue($data['order']))  {
            if ($data['sort'] == 'load_code') $data['sort'] = 'load.load_code';
            if ($data['sort'] == 'status') $data['sort'] = 'is_shipped';

            $query->orderBy($data['sort'], $data['order']);
        }


        if( CommonHelper::hasValue($data['limit']) && CommonHelper::hasValue($data['page']) && !$getCount)  {
            $query->skip($data['limit'] * ($data['page'] - 1))
                  ->take($data['limit']);
        }

        $result = $query->get();
        if($getCount) {
            $result = count($result);
        }
        DebugHelper::log(__METHOD__, $result);
        return $result;

    }

    public static function shipLoad($data = array())
    {

        $result = Load::where('load_code', '=', $data['load_code'])
                ->update(array(
                    "is_shipped" => 1,
                    'updated_at' => date('Y-m-d H:i:s')
                ));

        return $result;
    }

    public static function getLoadDetails($loadCode, $boxCode=null)
    {
        // get load date
        $rs = DB::table('load')
                    ->select(DB::raw("date_format(created_at,'%m/%d/%y') as load_date "))
                    ->where('load_code', '=', $loadCode)
                    ->first();
        $data['load_date'] = $rs->load_date;

        // get box codes and details based on pallet code
        if($boxCode==null){
            $rs = DB::table('pallet_details')
                        ->select('box_code')
                        ->join('load_details','load_details.pallet_code','=','pallet_details.pallet_code','RIGHT')
                        ->where('load_code', '=', $loadCode)
                        ->get();
        }
        else{
            $rs = DB::table('pallet_details')
                        ->select('box_code')
                        ->join('load_details','load_details.pallet_code','=','pallet_details.pallet_code','RIGHT')
                        ->where('load_code', '=', $loadCode)
                        ->where('box_code', '=', $boxCode)
                        ->get();
        }
        foreach($rs as $val){
            $box =  DB::table('box_details')
                    ->select('box_details.moved_qty',
                            'picklist_details.sku as upc','picklist_details.store_code','picklist_details.so_no','picklist_details.store_code',
                            'product_lists.description')
                    ->join('picklist_details','picklist_details.id','=','box_details.picklist_detail_id','LEFT')
                    ->join('product_lists','product_lists.upc','=','picklist_details.sku','LEFT')
                    ->where('box_details.box_code','=', $val->box_code)
                    ->get();



            if(!empty($box)){
                $data['StoreOrder'][$box[0]->so_no]['store_code'] = $box[0]->store_code;
                $data['StoreOrder'][$box[0]->so_no]['items'][$val->box_code] = $box;
            }
        }
        // echo '<pre>'; dd($data);
        foreach ($data['StoreOrder'] as $soNo => $value) {
            $store = DB::table('stores')
                ->select('store_name')
                ->where('store_code','=', $value['store_code'])
                ->first();
            $data['StoreOrder'][$soNo]['store_name'] = $store->store_name;

            // get so date created
            // echo '<pre>'; dd($soNo);
            $so = DB::table('store_order')
                ->select(DB::raw("date_format(order_date,'%m/%d/%y') as order_date "))
                ->where('so_no','=',$soNo)
                ->first();
            $data['StoreOrder'][$soNo]['order_date'] = $so->order_date;
        }
        // echo '<pre>'; dd($data);
        // $data['StoreOrder'][]
        // arrange array based on store order
        /*foreach($data['StoreOrder'] as $soNo => &$val){
                // get store name
                $store = DB::table('stores')->select('store_name')->where('store_code',$val['store_code'])->first();
                $val['store_name'] = $store->store_name;

                // get so date created
                $so = DB::table('store_order')->select(DB::raw("date_format(order_date,'%m/%d/%y') as order_date "))->where('so_no',$soNo)->first();
                $val['order_date'] = $so->order_date;
        }*/

        return $data;
    }


    /*public static function getCountLoadList($data = array(), $getCount = false)
    {
        $query = DB::table('loads');

        if( CommonHelper::hasValue($data['filter_load_code']) ) $query->where('load_code', 'LIKE', '%'. $data['filter_load_code'] . '%');

        if( CommonHelper::hasValue($data['sort']) && CommonHelper::hasValue($data['order']))  {
            if($data['sort'] == 'load_code'){
                $data['sort'] = 'filter_load_code';
            }
            $query->orderBy($data['sort'], $data['order']);
        }


        if( CommonHelper::hasValue($data['limit']) && CommonHelper::hasValue($data['page']) && !$getCount)  {
            $query->skip($data['limit'] * ($data['page'] - 1))
                  ->take($data['limit']);
        }
        $result = $query->get();
        if($getCount) {
            $result = count($result);
        }

        return $result;

    }*/

}