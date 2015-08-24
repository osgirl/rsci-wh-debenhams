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
    public static function getBoxesWithFilters($data= array())
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

        $query = Box::select('box_details.picklist_detail_id', 'box.box_code', 'box.id', 'box.store_code', 'box.in_use', 'box.created_at', 'stores.store_name', 'picklist.pl_status')
            ->join('stores', 'stores.store_code', '=', 'box.store_code')
            ->leftJoin('box_details', 'box_details.box_code', '=', 'box.box_code')
            ->join('picklist_details', 'picklist_details.id', '=', 'box_details.picklist_detail_id')
            ->join('picklist', 'picklist.move_doc_number', '=', 'picklist_details.move_doc_number');

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

        if( CommonHelper::hasValue($data['limit']) && CommonHelper::hasValue($data['page']) )  {
            $query->skip($data['limit'] * ($data['page'] - 1))
                  ->take($data['limit']);
        }
        $query->groupBy('box.box_code');

        $result = $query->get();


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
        return $result;
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