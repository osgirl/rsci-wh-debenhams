<?php

class Brands extends Eloquent {

	protected $table = 'brands';

	public static function getBrandsOption($data = array()) {
		$query = Brands::where(function($query_sub) {
	                $query_sub->where('deleted_at', '=', '0000-00-00 00:00:00')
		   				  	  ->orWhere('deleted_at', '=', NULL);
	            });
		$result = $query->get();

		return $result;
	}

}