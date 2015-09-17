<?php

class Pallet extends Eloquent {

	protected $guarded = array(); 
	/**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'pallet';

    public static function getOrCreatePallete($storeCode, $loadCode)
    {
    	$pallete = Pallet::join('load_details','load_details.pallet_code', '=', 'pallet.pallet_code' )
    		->where('pallet.store_code', '=', $storeCode)
    		->where('load_details.load_code', '=', $loadCode)
    		->first();
    		
    	if($pallete == null) {
    		$palleteMax =  Pallet::select(DB::raw('max(id) as max_created, max(pallet_code) as pallete_code'))->first()->toArray();
			;
			
			if($palleteMax['max_created'] === null) {
				$palleteCode = 'PT0000001';
			} else {
				$palleteCode = substr($palleteMax['pallete_code'], -7);
				$palleteCode = (int) $palleteCode + 1;
				$palleteCode = 'PT' . sprintf("%07s", (int)$palleteCode);
			}

    		Pallet::create(array('pallet_code'	=> $palleteCode,
    				'store_code'	=> $storeCode));
    		$pallete = Pallet::where('pallet_code','=', $palleteCode)->first();
    		
    		LoadDetails::create(array('load_code'	=> $loadCode,
    				'pallet_code'	=> $palleteCode));

    	} 

    	return $pallete->toArray();
    }
}