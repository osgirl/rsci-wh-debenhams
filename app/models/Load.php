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


    public static function getLoadNumbersync()
    {
         
        $query=DB::table('load')
            ->join('load_details','load.load_code','=','load_details.load_code','RIGHT')
            ->where('load.data_value', 0)
            ->where('load.assigned_to_user_id','!=', 0)
            ->update(['load.data_value' =>'1']);
    }
     public static function getLoadNumbersyncstockmodel()
    {
         
        $query=DB::table('load')
            ->join('load_details','load.load_code','=','load_details.load_code','RIGHT')
            ->where('load.data_value', 0)
            ->where('load.assigned_to_user_id','!=', 0)
            ->update(['load.data_value' =>'1']);
    }
    public static function getLoadShipped($loadnumber)
    {     
        $query=DB::table('load')
            ->where('load.data_value', 1)
            ->where('load.is_shipped', 1)
            ->where('load.assigned_to_user_id','!=', 0)
            ->where('load.load_code',"$loadnumber")
            ->update(['load.is_shipped' =>'2']);

    }
    public static function shippedloadstock($loadnumber)
    {     
        $query=DB::table('load')
            ->where('load.data_value', 1)
            ->where('load.is_shipped', 1)
            ->where('load.assigned_to_user_id','!=', 0)
            ->where('load.load_code',"$loadnumber")
            ->update(['load.is_shipped' =>'2']);

    }
    public static function getloadtagging($taggingload)
    {

        $query =DB::table('load')
            ->WHERE('load.load_code', $taggingload)
            ->update(['load.tagging_load' => '2']);
    }
/*    public static function getpostedtoBoxOrder($doc_num)
    {
        $query=DB::select(DB::raw("INSERT INTO wms_store_detail_box (move_doc_number, box_code, upc, quantity_packed,  box_status, quantity_pick) 
            SELECT wms_box.move_doc_number, wms_box.box_code, wms_picklist_details.sku, wms_box_details.moved_qty, wms_box.boxstatus_unload, '0' 
            from wms_box
            LEFT join wms_box_details on wms_box.box_code = wms_box_details.box_code
            left join wms_picklist_details on wms_box_details.picklist_detail_id = wms_picklist_details.id
            where wms_box.move_doc_number='$doc_num'"));


    }*/
    public static function getInsertToSelect($loadnumber)
    {     
        $query = DB::select(DB::raw("INSERT INTO wms_store_order (load_code, so_no, store_code, delivery_date)
            SELECT wms_load_details.load_code, wms_picklist_details.move_doc_number, wms_picklist_details.store_code, wms_load.ship_at
            FROM wms_load_details
            left join wms_picklist_details on wms_load_details.move_doc_number = wms_picklist_details.move_doc_number
            left join wms_load on wms_load_details.load_code = wms_load.load_code
            where wms_load_details.load_code = '$loadnumber' "));
}
    public static function getSOboxstatus($loadnumber)
    {     
    
        $query = DB::select(DB::raw("INSERT INTO wms_store_detail_box (move_doc_number, box_code, upc, quantity_packed,  box_status, quantity_pick)
      
      SELECT  wms_picklist_details.move_doc_number,  wms_box_details.box_code, wms_picklist_details.sku, wms_box_details.moved_qty,  wms_box.boxstatus_unload as box_status , '0'
FROM wms_load_details
left join wms_picklist_details on wms_load_details.move_doc_number = wms_picklist_details.move_doc_number
left join wms_load on wms_load_details.load_code = wms_load.load_code
LEFT JOIN wms_box_details on wms_picklist_details.id = wms_box_details.picklist_detail_id
left join wms_box on wms_box_details.box_code = wms_box.box_code
where wms_load_details.load_code = '$loadnumber' GROUP by wms_box.box_code "));
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
    
    public static function getLoadDataValue($loadnumber) {
 
        $query = DB::SELECT(DB::raw("SELECT data_value from wms_load WHERE load_code='$loadnumber'"));
     

        return $query;
    }


    public static function getLoadList($load_code, $data = array(), $getCount = false)
    {
        $query = DB::table('load_details')
        ->select('load_details.move_doc_number', 'box_details.box_code', 'load_details.load_code','picklist.pl_status','picklist_details.store_code', 'stores.store_name','load.data_value', 'stores.address1')
        ->join('load','load_details.load_code','=','load.load_code','LEFT')
        ->join('picklist','load_details.move_doc_number', '=','picklist.move_doc_number','LEFT')
        ->join('picklist_details','load_details.move_doc_number','=','picklist_details.move_doc_number','LEFT')
        ->join('stores','picklist_details.store_code','=','stores.store_code','LEFT')
        ->join ('box_details','picklist_details.id','=','box_details.picklist_detail_id','LEFT')
         ->where('picklist.type', '=', '1')
         ->where('load_details.load_code', $load_code);
             

 
        if( CommonHelper::hasValue($data['filter_load_code']) ) $query->where('load.load_code', 'LIKE', '%'. $data['filter_load_code'] . '%'); 

        if( CommonHelper::hasValue($data['filter_doc_no']) ) $query->where('picklist_details.move_doc_number', 'LIKE', '%'. $data['filter_doc_no'] . '%');

        if( CommonHelper::hasValue($data['filter_store']) ) $query->where('picklist_details.store_code', '=',  $data['filter_store']);
         if( CommonHelper::hasValue($data['filter_data_value']) ) $query->where('load.data_value', 'LIKE', '%'. $data['filter_data_value'].   '%'); 

       if( CommonHelper::hasValue($data['limit']) && CommonHelper::hasValue($data['page']))  {
            $query->skip($data['limit'] * ($data['page'] - 1))
                  ->take($data['limit']);
        }

        $query->groupBy('picklist_details.move_doc_number');
        $result = $query->get();

        return $result;
    //    GROUP_CONCAT(DISTINCT(wms_picklist_details.move_doc_number) SEPARATOR ', ' ) as MASTER_EDU

    }
     public static function getLoadList2($load_code, $data = array(), $getCount = false)
    {
        $query = DB::table('load_details')
        ->select('load_details.box_number','load.*',DB::raw('GROUP_CONCAT(DISTINCT(wms_picklist_details.move_doc_number) SEPARATOR ", "  )  as MASTER_EDU'),'stores.store_code','store_name','stores.address1')
        ->join('load','load_details.load_code','=','load.load_code','left')
        ->join('box','load_details.box_number','=','box.box_code','left')
         ->join('picklist_details','box.move_doc_number','=','picklist_details.move_doc_number','LEFT')
        ->join('stores','box.store_code','=','stores.store_code','LEFT')
         ->where('load_details.load_code', $load_code)
         ->groupBy('box.box_code');
             
        if( CommonHelper::hasValue($data['filter_box_code']) ) $query->where('load_details.box_number', 'LIKE', '%'. $data['filter_box_code'] . '%' );
        if( CommonHelper::hasValue($data['filter_doc_no']) ) $query->where('picklist_details.move_doc_number', 'LIKE', '%'. $data['filter_doc_no'] . '%' ); 

    
       if( CommonHelper::hasValue($data['limit']) && CommonHelper::hasValue($data['page']))  {
            $query->skip($data['limit'] * ($data['page'] - 1))
                  ->take($data['limit']);
        }

      
        $result = $query->get();

        return $result;
        

    }
    public static function getExportLoadList($loadnumber, $data=array()) {
 
        $query = DB::table('load')
        ->select('load.load_code','firstname','lastname','picklist_details.move_doc_number','box.box_code','box.moved_qty','load.updated_at')
        ->join('load_details','load.load_code','=','load_details.load_code','LEFT')
        ->join('picklist_details','load_details.move_doc_number','=','picklist_details.move_doc_number','LEFT')
        ->join('box_details','picklist_details.id','=','box_details.picklist_detail_id','LEFT')
        ->join('users','assigned_to_user_id','=','users.id','Left')
        ->WHERE('load_code', $loadnumber);
     
     

        return $query;
    }

    public static function getlist($data = array(), $getCount = false)
    {
        $query = DB::table('load')
        ->select('load.*','firstname','lastname','load.ship_at','load.assigned_by')
        ->join('users','assigned_to_user_id','=','users.id','Left')
        ->WHERE('tagging_load' ,'=', '1')
        ->orderBy('load_code', 'DESC');

 
        
        CommonHelper::filternator($query,$data,2,$getCount);
        if( CommonHelper::hasValue($data['filter_load_code']) ) $query->where('load_code', 'LIKE', '%'.$data['filter_load_code'].'%');
      if( CommonHelper::hasValue($data['filter_entry_date']) ) $query->where('load.ship_at', 'LIKE','%'.$data['filter_entry_date']. '%');

        $result = $query->get();
        if($getCount) {
            $result = count($result);
        }
        return $result;

    }
       public static function getliststock($data = array(), $getCount = false)
    {
        $query = DB::table('load')
        ->select('load.*','firstname','lastname','load.ship_at')
        ->join('users','assigned_to_user_id','=','users.id','Left')
        ->WHERE('tagging_load' ,'=', '2')
        ->orderBy('load_code', 'DESC');
        
        CommonHelper::filternator($query,$data,2,$getCount);
           if( CommonHelper::hasValue($data['filter_load_code']) ) $query->where('load_code', 'LIKE', '%'.$data['filter_load_code'].'%');
          if( CommonHelper::hasValue($data['filter_entry_date']) ) $query->where('load.ship_at', 'LIKE','%'.$data['filter_entry_date']. '%');

     

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

    public static function getLoadnumberOpen() {
        $query = DB::table('load')
        ->where('data_value', '=', '0')
        ->orderBy('load_code', 'ASC');

        $result = $query->get();

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

/*public static function getPackingDetailsasdfasdf($loadCode)
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

*/
public static function getPackingDetails($loadCode)
    {
        // get load date
        

            $rs = DB::SELECT(DB::raw("SELECT wms_load_details.load_code, wms_picklist_details.sku, wms_picklist_details.move_doc_number, wms_box_details.moved_qty, wms_box_details.box_code, wms_product_lists.description
FROM wms_load_details
LEFT join wms_box on wms_load_details.box_number = wms_box.box_code
INNER join wms_box_details on wms_box.box_code = wms_box_details.box_code
LEFT join wms_picklist_details on wms_picklist_details.id = wms_box_details.picklist_detail_id
left join wms_product_lists on wms_picklist_details.sku = wms_product_lists.upc 
where wms_load_details.load_code='$loadCode'   ORDER BY wms_picklist_details.move_doc_number, wms_box_details.box_code ASC  "));


        return $rs;
    }
public static function getPackingDetailsstock($loadCode)
    {
        // get load date
          /*  $rs = DB::SELECT(DB::raw("SELECT wms_box_details.box_code, wms_store_return_pick_details.move_doc_number, wms_store_return_pick_details.sku, wms_box_details.moved_qty
from `wms_load_details` 
LEFT join wms_box on wms_load_details.box_number = wms_box.box_code
INNER JOIN wms_box_details on wms_box.box_code = wms_box_details.box_code
LEFT JOIN wms_store_return_pick_details on wms_box_details.subloc_transfer_id = wms_store_return_pick_details.id
where `wms_load_details`.`load_code` = '$loadCode'  ORDER BY wms_load_details.move_doc_number, wms_box_details.box_code ASC  
"));*/

$rescue = DB::SELECT(DB::RAW("SELECT wms_box_details.box_code, wms_store_return_pick_details.move_doc_number, wms_store_return_pick_details.sku, wms_box_details.moved_qty, description
from `wms_load_details` 
LEFT join wms_box on wms_load_details.box_number = wms_box.box_code
INNER JOIN wms_box_details on wms_box.box_code = wms_box_details.box_code
LEFT JOIN wms_store_return_pick_details on wms_box_details.subloc_transfer_id = wms_store_return_pick_details.id
LEFT join wms_product_lists on wms_store_return_pick_details.sku = wms_product_lists.upc 
where `wms_load_details`.`load_code` = '$loadCode' ORDER BY wms_store_return_pick_details.move_doc_number, wms_box_details.box_code ASC "));
        return $rescue;
    }
    public static function getStoreLocation ($loadCode)
    {
            
     

            $query= DB::table('load_details')
                ->SELECT( 'store_return_pick_details.to_store_code','store_return_pick_details.from_store_code')
          
                ->JOIN('box','load_details.box_number','=','box.box_code','LEFT')
                ->join('box_details','box.box_code' ,'=','box_details.box_code','LEFT')
                ->join('store_return_pick_details','box_details.subloc_transfer_id','=','store_return_pick_details.id','left')
                ->WHERE('load_details.load_code', '=', $loadCode)
                ->first();
        

            return $query;
    }
    public static function getStoreLocationwarehouse ($loadCode)
    {
            
     

            $query= DB::table('load_details')
                ->SELECT( 'picklist_details.store_code', 'load_details.*')
          
                ->JOIN('box','load_details.box_number','=','box.box_code','LEFT') 
                ->join('picklist_details','box.move_doc_number','=','picklist_details.move_doc_number','left')
                ->WHERE('load_details.load_code', '=', $loadCode)
                ->first();
        

            return $query;
    }
    public static function getLoadingDetails($loadCode)
    {
        // get load date
         /*$query = DB::SELECT(DB::raw("SELECT GROUP_CONCAT(DISTINCT(wms_load_details.move_doc_number) SEPARATOR ',' ) as move_doc_number,wms_box_details.box_code, sum(wms_box_details.moved_qty) as total_qty
FROM wms_load_details
left join wms_box on wms_load_details.move_doc_number = wms_box.move_doc_number
left join wms_box_details on wms_box.box_code = wms_box_details.box_code
WHERE load_code='$loadCode'
GROUP BY wms_box_details.box_code;"));

        return $query;
*/
        $box= DB::SELECT(DB::raw("SELECT wms_load_details.load_code, GROUP_CONCAT(DISTINCT(wms_picklist_details.move_doc_number) SEPARATOR ',' ) as move_doc_number, wms_box_details.box_code, sum(wms_box_details.moved_qty) as total_qty 
FROM wms_load_details
LEFT join wms_box_details on wms_load_details.box_number = wms_box_details.box_code
LEFT join wms_picklist_details on wms_picklist_details.id = wms_box_details.picklist_detail_id
WHERE wms_load_details.load_code='$loadCode'
 GROUP by wms_box_details.box_code"));

                                    return $box;
    }
    public static function getLoadingDetailsstock($loadCode)
    {
        // get load date
         /*$query = DB::SELECT(DB::raw("SELECT GROUP_CONCAT(DISTINCT(wms_load_details.move_doc_number) SEPARATOR ',' ) as move_doc_number,wms_box_details.box_code, sum(wms_box_details.moved_qty) as total_qty
FROM wms_load_details
left join wms_box on wms_load_details.move_doc_number = wms_box.move_doc_number
left join wms_box_details on wms_box.box_code = wms_box_details.box_code
WHERE load_code='$loadCode'
GROUP BY wms_box_details.box_code;"));

        return $query;
*/
        $box= DB::SELECT(DB::raw("SELECT wms_load_details.load_code, GROUP_CONCAT(DISTINCT(wms_store_return_pick_details.move_doc_number) SEPARATOR ',' ) as move_doc_number, wms_box_details.box_code, sum(wms_box_details.moved_qty) as total_qty 
FROM wms_load_details
LEFT join wms_box on wms_box.box_code = wms_load_details.box_number
INNER join wms_box_details on wms_box.box_code = wms_box_details.box_code
LEFT JOIN wms_store_return_pick_details on wms_box_details.subloc_transfer_id = wms_store_return_pick_details.id
WHERE wms_load_details.load_code='$loadCode'
 GROUP by wms_box_details.box_code"));

                                    return $box;
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