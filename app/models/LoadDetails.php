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
    public static function getLoad($palletCode)
    {
        $query = LoadDetails::where('pallet_code', '=', $palletCode)
            ->first()->toArray();

        return $query;
    }
}