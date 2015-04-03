<?php

class InterTransfer extends Eloquent {

	protected $table = 'inter_transfer';
	protected $fillable = array('load_code', 'mts_number');

	public static function addRecord($data = array()) {
		InterTransfer::insert($data);
	}
}