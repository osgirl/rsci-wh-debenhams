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




public static function assignToStockPiler($Box_code = '', $data = array())
    {
        $query = load::where('load_code', '=', $Box_code)->update($data);
        DebugHelper::log(__METHOD__, $query);

        


        
    }




    public static function getLoads()
    {
        return Load::where('is_shipped', '=', 0)->get(array('id','load_code'));
    }

    public static function getLoadList($data = array(), $getCount = false)
    {
        $query = Load::select(DB::raw("wms_load.id, wms_load.load_code, wms_load.is_shipped, group_concat(DISTINCT wms_pallet.store_code SEPARATOR ',') stores,group_concat(DISTINCT wms_picklist.pl_status SEPARATOR ',') pl_status"))
            ->join('load_details', 'load_details.load_code', '=', 'load.load_code')
            ->join('pallet', 'pallet.pallet_code', '=', 'load_details.pallet_code')
            ->join('pallet_details','load_details.pallet_code','=','pallet_details.pallet_code','RIGHT')
            ->join('box_details','box_details.box_code','=','pallet_details.box_code','LEFT')
            ->join('picklist_details','picklist_details.id','=','box_details.picklist_detail_id','LEFT')
            ->join('picklist','picklist.move_doc_number','=','picklist_details.move_doc_number','LEFT')
            ->where('load.load_code', '!=', '')
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

        // foreach ($result as $load) {
        //     $load->open_pl_status = Load::getOpenPicklist($load->move_doc_number);
        // }

        if($getCount) {
            $result = count($result);
        }
        DebugHelper::log(__METHOD__, $result);
        return $result;

    }

    public static function getlist($data = array(), $getCount = false)
    {
        $query = DB::table('load')
        ->select('load.*','firstname','lastname')
        ->join('users','assigned_to_user_id','=','users.id','Left');
     

        CommonHelper::filternator($query,$data,2,$getCount);
        if( CommonHelper::hasValue($data['filter_ship_at']) ) $query->where('ship_at', 'LIKE', '%'.$data['filter_ship_at'].'%');
        $result = $query->get();
        if($getCount) {
            $result = count($result);
        }
        return $result;

    }
 
    public static function getInfoLoad($data)
    {
        return Load::whereIn('load_code', $data)->get()->toArray();
    }

    public static function getOpenPicklist($move_doc_number) {
        $sql = "SELECT count(WHMOVE) num_open
                FROM WHSMVH
                WHERE WHMVST = '1' AND WHMOVE IN ({$move_doc_number})";
                //WHERE POMRCH.POSTAT = 3 AND POMRCH.POLOC = 7000

        $db2 = new DB2Helper;
        $result = $db2->get($sql);

        $db2->close();

        return $result[0]['NUM_OPEN'];
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

    public static function getLoadDetails($loadCode)
    {
        // get load date
        $rs = DB::table('load')
                    ->select(DB::raw("date_format(created_at,'%m/%d/%y') as load_date "))
                    ->where('load_code', '=', $loadCode)
                    ->first();
        $data['load_date'] = $rs->load_date;

        // get box codes and details based on pallet code
            $rs = DB::table('pallet_details')
                        ->select('box_code')
                        ->join('load_details','load_details.pallet_code','=','pallet_details.pallet_code','RIGHT')
                        ->where('load_code', '=', $loadCode)
                        ->get();
        foreach($rs as $val){
            $box =  DB::table('box_details')
                    // ->select(DB::raw('SUM(wms_picklist_details.moved_qty) as moved_qty'),
                    ->select(DB::raw('SUM(wms_box_details.moved_qty) as moved_qty'),
                            'picklist_details.sku as upc','picklist_details.created_at as order_date','picklist_details.store_code','picklist_details.so_no','picklist_details.store_code',
                            'product_lists.description')
                    ->join('picklist_details','picklist_details.id','=','box_details.picklist_detail_id','LEFT')
                    ->join('product_lists','product_lists.upc','=','picklist_details.sku','LEFT')
                    ->groupBy('picklist_details.sku')
                    ->where('box_details.box_code','=', $val->box_code)
                    ->get();



            if(!empty($box)){$counter=count($box);
                for($i=0;$i<$counter;$i++){
                    $data['StoreOrder'][$box[$i]->so_no]['store_code'] = $box[$i]->store_code;
                    $data['StoreOrder'][$box[$i]->so_no]['items'][$val->box_code] = $box;
                    $data['StoreOrder'][$box[$i]->so_no]['order_date'] = $box[$i]->order_date;
                }
                $data['StoreOrder'][$box[0]->so_no]['store_code'] = $box[0]->store_code;
                $data['StoreOrder'][$box[0]->so_no]['items'][$val->box_code] = $box;
                $data['StoreOrder'][$box[0]->so_no]['order_date'] = $box[0]->order_date;
            }
        }
        // echo '<pre>'; dd($data);
            foreach ($data['StoreOrder'] as $soNo => $value) {
                $store = DB::table('stores')
                    ->select('store_name')
                    ->where('store_code','=', $value['store_code'])
                    ->first();
                $data['StoreOrder'][$soNo]['store_name'] = $store->store_name;
                $mts_comments = DB::table('store_order')->select('comments')->where('so_no',$soNo)->first();
                $data['StoreOrder'][$soNo]['comments'] = $mts_comments->comments;
                // get so date created
                // echo '<pre>'; dd($soNo);
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


public static function getCommentsByLoadCode($loadCode)
{
   // get comments from wms_store_order by load_code
//    $rs = DB::table('store_order')
//                ->select('comments')
//                ->where('load_code', '=', $loadCode)
//                ->first();
//    $data['comments'] = $rs->comments;


    $rs = DB::table('store_order')->select('comments')->where('load_code',$loadCode)->first();

    //$data = 'shit!';
    $data['comments'] = $rs->comments;


    return $data;
}

public static function getPackingDetails($loadCode)
    {
        // get load date
        $rs = DB::table('load')
                    ->select(DB::raw("date_format(created_at,'%m/%d/%y') as load_date "),DB::raw("date_format(updated_at,'%m/%d/%y') as ship_date"),'is_shipped')
                    ->where('load_code', '=', $loadCode)
                    ->first();
        $data['load_date'] = $rs->load_date;
        $data['ship_date'] = $rs->ship_date;
        $data['is_shipped'] = $rs->is_shipped;
        // get box codes and details based on pallet code
            $rs = DB::table('pallet_details')
                        ->select('box_code')
                        ->join('load_details','load_details.pallet_code','=','pallet_details.pallet_code','RIGHT')
                        ->where('load_code', '=', $loadCode)
                        ->get();
        foreach($rs as $val){
            $box =  DB::table('box_details')
                    ->select('box_details.moved_qty',
                            'picklist_details.sku as upc','picklist_details.store_code','picklist_details.so_no','picklist_details.store_code',
                            'product_lists.description','product_lists.dept_code','product_lists.sub_dept','product_lists.class','product_lists.sub_class')
                    ->join('picklist_details','picklist_details.id','=','box_details.picklist_detail_id','LEFT')
                    ->join('product_lists','product_lists.upc','=','picklist_details.sku','LEFT')
                    ->where('box_details.box_code','=', $val->box_code)
                    ->get();

            if(!empty($box)){
                $counter=count($box);
                for($i=0;$i<$counter;$i++){
                    $res= Department::getBrand($box[$i]->dept_code,0,0,0);
                    try{
                        $data['StoreCode'][$box[$i]->store_code]['StoreOrder'][$box[$i]->so_no]['brand'] = $res[0]['description'];
                    }
                    catch(Exception $e){
                        continue;
                    }
                    $data['StoreCode'][$box[$i]->store_code]['StoreOrder'][$box[$i]->so_no]['items'][$val->box_code] = $box;
                    if(!array_key_exists('InterTransfer', $data['StoreCode'][$box[$i]->store_code]))
                        $data['StoreCode'][$box[$i]->store_code]['InterTransfer']=array();
                }
            }

            $rs = DB::table('inter_transfer')
                ->select('mts_number','no_of_boxes','box.store_code')
                ->join('box','box.box_code','=','inter_transfer.box_code')
                ->where('inter_transfer.box_code', '=', $val->box_code)
                ->get();

            if(!empty($rs)){
                $counter=count($rs);
                for($i=0;$i<$counter;$i++){
                    $data['StoreCode'][$rs[$i]->store_code]['InterTransfer'][$rs[$i]->mts_number]['items'][$val->box_code] = $rs;
                }
            }



        // echo '<pre>'; dd($data);
            foreach ($data['StoreCode'] as $storeCode => $value) {
                $store = DB::table('stores')
                    ->select('store_name')
                    ->where('store_code','=', $storeCode)
                    ->first();
                $data['StoreCode'][$storeCode]['store_name'] = $store->store_name;
            }
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

    public static function getLoadingDetails($loadCode)
    {
        // get load date
        $rs = DB::table('load')
                    ->select(DB::raw("date_format(created_at,'%m/%d/%y') as load_date "),DB::raw("date_format(updated_at,'%m/%d/%y') as ship_date"),'is_shipped')
                    ->where('load_code', '=', $loadCode)
                    ->first();
        $data['load_date'] = $rs->load_date;
        $data['ship_date'] = $rs->ship_date;
        $data['is_shipped'] = $rs->is_shipped;
        // get box codes and details based on pallet code
            $rs = DB::table('pallet_details')
                        ->select('box_code')
                        ->join('load_details','load_details.pallet_code','=','pallet_details.pallet_code','RIGHT')
                        ->where('load_code', '=', $loadCode)
                        ->get();
        foreach($rs as $val){
            $box =  DB::table('box_details')
                    ->select('box_details.moved_qty',
                            'picklist_details.sku as upc','picklist_details.store_code','picklist_details.so_no','picklist_details.store_code',
                            'product_lists.description','product_lists.dept_code','product_lists.sub_dept','product_lists.class','product_lists.sub_class')
                    ->join('picklist_details','picklist_details.id','=','box_details.picklist_detail_id','LEFT')
                    ->join('product_lists','product_lists.upc','=','picklist_details.sku','LEFT')
                    ->where('box_details.box_code','=', $val->box_code)
                    ->get();

            if(!empty($box)){
                $counter=count($box);
                for($i=0;$i<$counter;$i++){
                    $res= Department::getBrand($box[$i]->dept_code,0,0,0);
                    try{
                        $data['brand'] = $res[0]['description'];
                    }
                    catch(Exception $e){
                        continue;
                    }
                    $data['StoreOrder'][$box[$i]->so_no]['items'][$val->box_code] = $box;
                    if(!array_key_exists('InterTransfer', $data))
                        $data['InterTransfer']=array();
                }
            }

            $rs = DB::table('inter_transfer')
                ->select('mts_number','no_of_boxes','box.store_code')
                ->join('box','box.box_code','=','inter_transfer.box_code')
                ->where('inter_transfer.box_code', '=', $val->box_code)
                ->get();

            if(!empty($rs)){
                $counter=count($rs);
                for($i=0;$i<$counter;$i++){
                    $data['InterTransfer'][$rs[$i]->mts_number]['items'][$val->box_code] = $rs;
                }
            }

        }

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
public static function getPOInfodiv($receiver_no = NULL) {
        $query = DB::table('purchase_order_lists')
                    // ->join('users', 'purchase_order_lists.assigned_to_user_id', '=', 'users.id', 'LEFT')
                    ->join('dataset', 'purchase_order_lists.po_status', '=', 'dataset.id', 'LEFT')
                    ->join('vendors', 'purchase_order_lists.vendor_id', '=', 'vendors.id', 'LEFT')
                    ->where('purchase_order_lists.receiver_no', '=', $receiver_no);

        $result = $query->get(array(
                                    'purchase_order_lists.*',
                                    'vendors.vendor_name',
                                    'dataset.data_display'
                                    // 'users.firstname',
                                    // 'users.lastname'
                                )
                            );

        // get the multiple stock piler fullname
        foreach ($result as $key => $lo) {
            $assignedToUserId       = explode(',', $lo->assigned_to_user_id);
            $getUsers               = User::getUsersFullname($assignedToUserId);
            $result[$key]->fullname = implode(', ', array_map(function ($entry) { return $entry['name']; }, $getUsers));
        }

        return $result[0];
}
}