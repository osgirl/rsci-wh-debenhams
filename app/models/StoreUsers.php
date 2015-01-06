<?php

class StoreUsers extends Eloquent {

	protected $guarded = array(); 
	/**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'store_users';

    public static function checkUser($data = array()) {
    	$username = $data['username'];
    	$query = StoreUsers::where('username','=', $username);
    	//

    	$result = $query->first();
    	DebugHelper::log(__METHOD__, $result);
    	return $result;
    }
}