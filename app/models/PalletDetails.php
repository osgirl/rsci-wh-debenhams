<?php

class PalletDetails extends Eloquent {

	protected $guarded = array(); 
	/**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'pallet_details';

    /**
    * Get pallet per boxcode
    *
    * @param $boxCode 	box_code
    * @return array
    */
    public static function getPallet($boxCode)
    {
    	$query = PalletDetails::where('box_code', '=', $boxCode)->first();

        if($query) return $query->toArray();
    	return false;
    }
}