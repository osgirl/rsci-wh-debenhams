<?php

class Box extends Eloquent {

	protected $guarded = array();

    /**
    * Get single box
    *
    * @example Box::getBox();
    *
    * @param  boxCode   box code
    * @param  storeCode store code
    * @return Status
    */

     public static function GetApiRPoList($piler_id) 
    {

            $query = DB::select(DB::raw("SELECT purchase_order_no,wms_purchase_order_lists.receiver_no,wms_purchase_order_details.dept_number as division_id,division,wms_purchase_order_details.po_status from wms_purchase_order_lists 
                inner JOIN wms_purchase_order_details on wms_purchase_order_lists.receiver_no=wms_purchase_order_details.receiver_no where wms_purchase_order_details.assigned_to_user_id='$piler_id' and wms_purchase_order_details.po_status=3 group by wms_purchase_order_lists.receiver_no,dept_number"));
            
        return $query;
    }
    public static function getBox($boxCode, $storeCode)
    {
        $box = Box::where('box_code','=',$boxCode)
            ->where('store_code', '=', $storeCode)
            ->get();
        return $box;
    }

	/**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'box';

    public static function getBoxes($storeCode)
    {
    	$boxes = Box::where('store_code', '=', $storeCode)
    		->where('in_use', '=', Config::get('box_statuses.not_in_use'))
    		->lists('box_code');
        DebugHelper::log(__METHOD__, $boxes);

        return $boxes;
    }

    public static function getBoxesUserId($storeCode,$userid)
    {
        $boxes = Box::where('store_code', '=', $storeCode)
            ->where('userid', '=', $userid)
            ->where('in_use', '=', Config::get('box_statuses.not_in_use'))
            ->lists('box_code');
        DebugHelper::log(__METHOD__, $boxes);

        return $boxes;
    }


    /****************Methods for CMS********************/
    /**
    * Gets boxes with filters and order
    *
    * @example  Box::getBoxesWithFilter({params})
    *
    * @return boxes
    */
    public static function getBoxesWithFilters($data= array(), $getCount = FALSE)
    {
        /*$query = Box::select('box_details.picklist_detail_id', 'box.box_code', 'box.id', 'box.store_code', 'box.in_use', 'box.created_at', 'stores.store_name')
            ->join('stores', 'stores.store_code', '=', 'box.store_code')
            ->leftJoin('box_details', 'box_details.box_code', '=', 'box.box_code');

        //8-24-15
        $query = Box::select('box_details.picklist_detail_id', 'box.box_code', 'box.id', 'box.store_code', 'box.in_use', 'box.created_at', 'stores.store_name', 'picklist.pl_status')
            ->join('stores', 'stores.store_code', '=', 'box.store_code')
            ->leftJoin('picklist_details', 'picklist_details.store_code', '=', 'box.store_code')
            ->leftJoin('picklist', function($join)
            {
                $join->on('picklist.move_doc_number', '=', 'picklist_details.move_doc_number');

            })
            ->leftJoin('box_details', 'box_details.box_code', '=', 'box.box_code');
            */

        $query = Box::select('box_details.picklist_detail_id', 'box.box_code', 'box.id', 'box.store_code', 'box.in_use', 'box.created_at', 'stores.store_name', 'picklist.pl_status', 'box.userid', 'users.username','box.tl_number','picklist.move_doc_number')
            ->join('stores', 'stores.store_code', '=', 'box.store_code')
            
            ->leftJoin('box_details', 'box_details.box_code', '=', 'box.box_code')
            ->leftJoin('picklist_details', 'picklist_details.id', '=', 'box_details.picklist_detail_id')
            ->leftJoin('picklist', 'picklist.move_doc_number', '=', 'picklist_details.move_doc_number')
            ->leftJoin('users', 'users.id', '=', 'box.userid')
            ->where ('box.tl_number','=', $data['load_code']);

        if( CommonHelper::hasValue($data['sort']) && CommonHelper::hasValue($data['order']))  {
            if ($data['sort']=='store') $data['sort'] = 'box.store_code';
            if ($data['sort']=='box_code') $data['sort'] = 'box.box_code';
            if ($data['sort']=='date_created') $data['sort'] = 'box.created_at';

            // die($data['order']);

            $query->orderBy($data['sort'], $data['order']);
        }
     
        if( CommonHelper::hasValue($data['filter_store']) ) {
            $query->where('stores.store_name', 'LIKE', '%'. $data['filter_store']. '%');
        }
        if( CommonHelper::hasValue($data['filter_box_code']) ) {
            $query->where('box.box_code', 'LIKE', '%'.$data['filter_box_code']. '%');
        }
        if( CommonHelper::hasValue($data['filter_stock_piler']) ) {
            $query->where('users.id', 'LIKE', $data['filter_stock_piler'] );
        }

        if( CommonHelper::hasValue($data['limit']) && CommonHelper::hasValue($data['page']) )  {
            $query->skip($data['limit'] * ($data['page'] - 1))
                  ->take($data['limit']);
        }
        $query->groupBy('box.box_code');


        $result = $query->get();

        if($getCount) return count($result);
        DebugHelper::log(__METHOD__, $result);

        return $result;
    }
public static function getBoxesWithFilters1($data= array(), $getCount = FALSE)
    {
         

        $query = Box::select('box_details.picklist_detail_id', 'box.box_code', 'box.id', 'box.store_code', 'box.in_use', 'box.created_at', 'stores.store_name', 'picklist.pl_status', 'box.userid', 'users.username','box.tl_number','picklist.move_doc_number')
            ->join('stores', 'stores.store_code', '=', 'box.store_code')
            
            ->leftJoin('box_details', 'box_details.box_code', '=', 'box.box_code')
            ->leftJoin('picklist_details', 'picklist_details.id', '=', 'box_details.picklist_detail_id')
            ->leftJoin('picklist', 'picklist.move_doc_number', '=', 'picklist_details.move_doc_number')
            ->leftJoin('users', 'users.id', '=', 'box.userid')
            ->where ('box.tl_number','=', $data['load_code']);

        if( CommonHelper::hasValue($data['sort']) && CommonHelper::hasValue($data['order']))  {
            if ($data['sort']=='store') $data['sort'] = 'box.store_code';
            if ($data['sort']=='box_code') $data['sort'] = 'box.box_code';
            if ($data['sort']=='date_created') $data['sort'] = 'box.created_at';

            // die($data['order']);

            $query->orderBy($data['sort'], $data['order']);
        }
     
        if( CommonHelper::hasValue($data['filter_store']) ) {
            $query->where('stores.store_name', 'LIKE', '%'. $data['filter_store']. '%');
        }
        if( CommonHelper::hasValue($data['filter_box_code']) ) {
            $query->where('box.box_code', 'LIKE', '%'.$data['filter_box_code']. '%');
        }
        if( CommonHelper::hasValue($data['filter_stock_piler']) ) {
            $query->where('users.id', 'LIKE', $data['filter_stock_piler'] );
        }

        if( CommonHelper::hasValue($data['limit']) && CommonHelper::hasValue($data['page']) )  {
            $query->skip($data['limit'] * ($data['page'] - 1))
                  ->take($data['limit']);
        }
        $query->groupBy('box.box_code');


        $result = $query->get();

        if($getCount) return count($result);
        DebugHelper::log(__METHOD__, $result);

        return $result;
    }

    public static function getboxcontent($id)
    {
        $query= DB::table('box', 'load')
        ->leftJoin('load','box.tl_number','=','load.load_code')
        ->leftJoin('users','users.id','=','assigned_to_user_id')
        ->leftJoin('stores', 'box.store_code','=','stores.store_code')
        ->where('box_code','=',$id)
        ->first();
        return $query;
    }
    public static function getboxcontentstock($id)
    {
        $query= DB::table('box', 'load')
        ->leftJoin('load','box.tl_number','=','load.load_code')
        ->leftJoin('users','users.id','=','assigned_to_user_id')
        ->leftJoin('stores', 'box.store_code','=','stores.store_code')
        ->where('box_code','=',$id)
        ->first();
        return $query;
    }
    public static function getremovedTL($tlnumber , $loadnumber) {
        $query = DB::select(DB::raw("DELETE FROM wms_load_details  where load_code = '$loadnumber' and box_number = '$tlnumber'"));
 
    }
    public static function getremovedTLUpdate($tlnumber , $loadnumber) {
        $query = DB::select(DB::raw("UPDATE wms_box set assign_to_load='0' where box_code='$tlnumber'"));
 
    }
    public static function getremovedTLstock($tlnumber , $loadnumber) {
        $query = DB::select(DB::raw("DELETE FROM wms_load_details  where load_code = '$loadnumber' and box_number = '$tlnumber'"));
 
    }
    public static function  getremovedTLUpdatestock($tlnumber , $loadnumber) {
        $query = DB::select(DB::raw("UPDATE wms_box set assign_to_load='0' where box_code='$tlnumber'"));
 
    }
    public static function assignToTL($tlnumber , $loadnumber) {
        $query = DB::select(DB::raw("UPDATE wms_box set assign_to_load='1' where box_code='$tlnumber'"));
 
    }
     public static function assignToTLnumberbox($tlnumber , $loadnumber) {
    
        $query1 = DB::select(DB::raw("INSERT INTO `wms_load_details`(`id`, `load_code`, `box_number`, `sync_status`, `is_load`, `created_at`, `updated_at`, `jda_sync_date`) VALUES ('','$loadnumber','$tlnumber','','','','','')"));
    }
    public static function assignToTLstock($tlnumber , $loadnumber) {
        $query = DB::select(DB::raw("UPDATE wms_box set assign_to_load='1' where box_code='$tlnumber'"));
 
    }
     public static function assignToTLnumberboxstock($tlnumber , $loadnumber) {
    
        $query1 = DB::select(DB::raw("INSERT INTO `wms_load_details`(`id`, `load_code`, `box_number`, `sync_status`, `is_load`, `created_at`, `updated_at`, `jda_sync_date`) VALUES ('','$loadnumber','$tlnumber','','','','','')"));
    }

    public static function getLoadNumberstock ($data = array(), $getCount=false)
    {
          $query  = Box::select('box.*','stores.*','store_return_pick_details.*',DB::raw('GROUP_CONCAT(DISTINCT(wms_store_return_pick_details.move_doc_number) SEPARATOR ", "  )  as MASTER_EDU'))
                        ->join('store_return_pick_details','box.tl_number','=','store_return_pick_details.move_doc_number','LEFT')
                        ->join('stores','store_return_pick_details.to_store_code','=','stores.store_code','LEFT')
                        ->where('box.assign_to_load','=',  0)
                        ->where('box.in_use','=', 0)
                        ->where('box.move_doc_number','=', "")
                        ->groupBy('box_code');
         
         if( CommonHelper::hasValue($data['filter_store']) ) $query->where('store_return_pick_details.from_store_code', 'LIKE', '%'. $data['filter_store']. '%'); 

         if( CommonHelper::hasValue($data['filter_store_name']) ) $query->where('store_return_pick_details.to_store_code', 'LIKE', '%'. $data['filter_store_name']. '%');
         if( CommonHelper::hasValue($data['filter_doc_no']) ) $query->where('box.box_code', 'LIKE', '%'. $data['filter_doc_no']. '%');
        if( CommonHelper::hasValue($data['filter_doc_no_pick']) ) $query->where('store_return_pick_details.move_doc_number', 'LIKE', '%'. $data['filter_doc_no_pick']. '%');
           

            if( CommonHelper::hasValue($data['limit']) && CommonHelper::hasValue($data['page']) && !$getCount)  {
            $query->skip($data['limit'] * ($data['page'] - 1))
                  ->take($data['limit']);
        }

    
        $result = $query->get();
        DebugHelper::log(__METHOD__, $result);

        // get the multiple stock piler fullname
        foreach ($result as $key => $picklist) {
            $assignedToUserId       = explode(',', $picklist->assigned_to_user_id);
            $getUsers               = User::getUsersFullname($assignedToUserId);
            $result[$key]->fullname = implode(', ', array_map(function ($entry) { return $entry['name']; }, $getUsers));
        }

        if($getCount) return count($result);
        return $result;
    }

 public static function getLoadNumber(  $data= array(), $getCount=false)
    {
        // $query = Picklist::select(DB::raw('wms_picklist.*, sum(wms_picklist_details.move_to_shipping_area) as sum_moved, sum(wms_picklist_details.assigned_user_id) as sum_assigned, store_code' ))
        $query  = Box::select('box.*','stores.*',DB::raw('GROUP_CONCAT(DISTINCT(wms_picklist.move_doc_number) SEPARATOR ", "  )  as MASTER_EDU'))

                        ->join('picklist_details','box.move_doc_number','=','picklist_details.move_doc_number','LEFT')
                        ->join('picklist','picklist_details.move_doc_number','=','picklist.move_doc_number','LEFT')
                        ->join('stores','picklist_details.store_code','=','stores.store_code','LEFT')
                        ->where('assign_to_load','=', 0)
                        ->where('box.in_use','=',0)
                        ->where('box.tl_number','=',"")
                        ->groupBy('box_code');
         
         if( CommonHelper::hasValue($data['filter_store']) ) $query->where('picklist_details.store_code', 'LIKE', '%'. $data['filter_store']. '%');
         if( CommonHelper::hasValue($data['filter_doc_no']) ) $query->where('picklist_details.move_doc_number', 'LIKE', '%'. $data['filter_doc_no']. '%');

         if( CommonHelper::hasValue($data['filter_box_code']) ) $query->where('picklist_details.move_doc_number', 'LIKE', '%'. $data['filter_box_code']. '%');
       /* $query = Box::select('picklist.*','picklist.type','division.*','picklist_details.store_code','load.data_value' ,'box.move_doc_number as movedoc','picklist_details.updated_at as action_date')
        ->join('box','Picklist.move_doc_number','=','box.move_doc_number','LEFT')
            ->join('picklist_details', 'picklist_details.move_doc_number', '=', 'picklist.move_doc_number')
            ->join('division','picklist_details.division','=','division.id', 'LEFT')
            ->join('load','picklist.pl_status','=','load.data_value','LEFT')
            ->where('picklist.type', '=','0')
            ->where('picklist.pl_status','=','18');*/

       /* if( CommonHelper::hasValue($data['filter_doc_no']) ) $query->where('Picklist.move_doc_number', 'LIKE', '%'. $data['filter_doc_no'] . '%');
        if( CommonHelper::hasValue($data['filter_type']) ) $query->where('type', '=',  $data['filter_type']);
        if( CommonHelper::hasValue($data['filter_status']) ) $query->where('data_value', '=', $data['filter_status'])->where('data_code', '=', 'PICKLIST_STATUS_TYPE');
        if( CommonHelper::hasValue($data['filter_store']) ) $query->where('picklist_details.store_code', 'LIKE', '%'. $data['filter_store']. '%');
        if( CommonHelper::hasValue($data['filter_stock_piler']) ) $query->whereRaw('find_in_set('. $data['filter_stock_piler'] . ',assigned_to_user_id) > 0');
        if( CommonHelper::hasValue($data['filter_transfer_no']) ) $query->where('picklist_details.so_no', 'LIKE', '%'. $data['filter_transfer_no'] . '%');
        if( CommonHelper::hasValue($data['filter_action_date']) ) $query->whereBetween('picklist_details.updated_at', array($data['filter_action_date'] . ' 00:00:00', $data['filter_action_date'] . ' 23:59:59'));*/

      


        if( CommonHelper::hasValue($data['limit']) && CommonHelper::hasValue($data['page']) && !$getCount)  {
            $query->skip($data['limit'] * ($data['page'] - 1))
                  ->take($data['limit']);
        }

    
        $result = $query->get();
        DebugHelper::log(__METHOD__, $result);

        // get the multiple stock piler fullname
        foreach ($result as $key => $picklist) {
            $assignedToUserId       = explode(',', $picklist->assigned_to_user_id);
            $getUsers               = User::getUsersFullname($assignedToUserId);
            $result[$key]->fullname = implode(', ', array_map(function ($entry) { return $entry['name']; }, $getUsers));
        }

        if($getCount) return count($result);
        return $result;
    }

    public static function getBoxesCount($data= array())
    {

        $query = Box::select(DB::raw('count(distinct wms_box.box_code) as count'))
            ->join('stores', 'stores.store_code', '=', 'box.store_code')
            ->leftJoin('box_details', 'box_details.box_code', '=', 'box.box_code');

        if( CommonHelper::hasValue($data['sort']) && CommonHelper::hasValue($data['order']))  {
            if ($data['sort']=='store') $data['sort'] = 'box.store_code';
            if ($data['sort']=='box_code') $data['sort'] = 'box.box_code';
            if ($data['sort']=='date_created') $data['sort'] = 'box.created_at';

            $query->orderBy($data['sort'], $data['order']);
        }
        if( CommonHelper::hasValue($data['filter_store']) ) {
            $query->where('stores.store_name', 'LIKE', '%'. $data['filter_store']. '%');
        }
        if( CommonHelper::hasValue($data['filter_box_code']) ) {
            $query->where('box.box_code', 'LIKE', '%'.$data['filter_box_code']. '%');
        }
        $result = $query->pluck('count');
        

        return count($result);
    }

    /**
    * Updates Box
    *
    * @example  Box::updateBox({array})
    *
    * @return Status
    */
    public static function updateBox($data=array())
    {
        $test = Box::where('box_code', '=', $data['box_code'])
            ->update(array(
                    'store_code'    => $data['store'],
                    'in_use'        => $data['in_use'],
                    'updated_at'    => date('Y-m-d H:i:s')
                    ));

        return true;
    }

    public static function deleteByBoxCode($boxCode)
    {
        Box::where('box_code' ,'=', $boxCode)
            ->delete();
        return;
    }

    public static function getBoxList($boxCode)
    {
        // $boxlist = Box::where('box.box_code', '=', $boxCode)
        $boxlist = Box::select(DB::raw("wms_box.box_code, wms_box.store_code, picklist_detail_id, wms_box_details.moved_qty, move_doc_number,GROUP_CONCAT(RTRIM(so_no)) so_no"))
        ->join('box_details', 'box_details.box_code','=', 'box.box_code')
        ->join('picklist_details', 'box_details.picklist_detail_id','=', 'picklist_details.id', 'LEFT')
        ->where('box.box_code', '=', $boxCode)
        // ->groupBy('box.box_code')
        ->first()->toArray();
        // DebugHelper::log(__METHOD__, $boxlist);
        return $boxlist;
    }

    public static function getInfoByBoxNos($data)
    {
        return Box::whereIn('box_code', $data)->get()->toArray();
    }

    public static function assignToStockPiler($Box_code = '', $data = array())
    {
        $query = Box::where('box_code', '=', $Box_code)->update($data);
        DebugHelper::log(__METHOD__, $query);
    }
    public static function assignToTLnumber($Box_code = '', $data = array())
    {
        $query = picklist::where('move_doc_number', '=', $Box_code)->update($data);
        DebugHelper::log(__METHOD__, $query);
    }
/**
    public static function getDetailsBox()
    {

            $query = DB::table('wms_box_details')
            ->where('box_code', '=', $id);
            return $query;
    }
**/


    //TODO :: remove if not in use
    /*********************Functions from Box detail**************************/

    //protected $table = 'box_manifest';

    /*
    *
    *  Gets boxes of the store by store code / store code
    *
    */
/*    public static function getBoxesOfStoreByStoreCode($storeCode)
    {
        $boxes = BoxManifest::select('id', 'store_code', 'is_shipped')
            ->where('is_shipped', '=', 0)
            ->where('store_code','=', $storeCode)
            ->get();
        return $boxes;
    }*/

    /*
    *
    *  create boxes for a store of a specific store order
    *  note store code is the same is store code
    *
    */
/*    public static function createBox($storeCode)
    {
        BoxManifest::insert(
            array('store_code' => $storeCode)
        );
        return;
    }*/

    /*
    *
    *  Updates status of boxes
    *
    */
/*    public static function changeStatusOfBoxes($boxIds, $status)
    {
        BoxManifest::whereIn('id', $boxIds)
            ->update(array(
                    'is_shipped'    => $status,
                    'updated_at'    => date('Y-m-d H:i:s')
                    ));
        return ;
    }*/
}