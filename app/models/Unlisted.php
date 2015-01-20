<?php

class Unlisted extends Eloquent {

	protected $table = 'unlisted';

    public static function createUpdate($data = array()) {
        $query = Unlisted::where('sku', '=', $data['sku'])
            ->where('reference_no', '=', $data['po_order_no'])->first();

        if(! is_null($query) ) {
            $unlisted     = $query->first();
            $qty_received = $unlisted['quantity_received'] + $data['quantity_delivered'];

            Unlisted::where('sku', '=', $data['sku'])
                ->where('reference_no', '=', $data['po_order_no'])
                ->update(array('quantity_received' => $qty_received));

        } else {

            $unlisted                    = new Unlisted;
            $unlisted->sku               = $data['sku'];
            $unlisted->reference_no      = $data['po_order_no'];
            $unlisted->quantity_received = $data['quantity_delivered'];
            $unlisted->save();
        }
    }

    public static function getList($data = array(), $getCount = false)
    {
        /*$query = Load::select(DB::raw("wms_load.id, wms_load.load_code, wms_load.is_shipped, group_concat(wms_pallet.store_code SEPARATOR ',') stores"))
            ->join('load_details', 'load_details.load_code', '=', 'load.load_code')
            ->join('pallet', 'pallet.pallet_code', '=', 'load_details.pallet_code')
            ->groupBy('load.load_code');*/

        $query = Unlisted::where('deleted_at', '=', '0000-00-00 00:00:00');

        // echo "<pre>"; print_r($data); die();

        if( CommonHelper::hasValue($data['filter_reference_no']) ) $query->where('reference_no', 'LIKE', '%'. $data['filter_reference_no'] . '%');
        if( CommonHelper::hasValue($data['filter_sku']) ) $query->where('sku', 'LIKE', '%'. $data['filter_sku'] . '%');

        if( CommonHelper::hasValue($data['sort']) && CommonHelper::hasValue($data['order']))  {
            if ($data['sort'] == 'reference_no') $data['sort'] = 'reference_no';
            if ($data['sort'] == 'sku') $data['sort'] = 'sku';

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

}