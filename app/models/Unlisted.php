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

}