<?php

class PutAway extends Eloquent {

	protected $guarded = array(); 
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'putaway';

	/**
	* Create transaction in putaway table
	*
	* @example  PutAway::createTransaction()
	*
	* @return Status
	*/ 
	public static function createTransaction($slot_id, $zone_id)
	{
		$transaction = PutAway::create(array(
				'slot_id' 		=> $slot_id,
				'zone_id' 		=> $zone_id,
				'created_at'	=> date('Y-m-d H:i:s'),
				'updated_at'	=> date('Y-m-d H:i:s')
				));
		return $transaction;

	}

}