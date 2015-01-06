<?php

class Dataset extends Eloquent {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'dataset';

	public static function getTypeInList($type, $includeAdmin = FALSE) {
		$query = Dataset::where('data_code', '=', $type);
		
		if(! $includeAdmin ) $query->where('data_value', '<>', 'super_admin');
		
		$result = $query->lists('data_display', 'id');

		return $result;
	}

	public static function getType($data = array()) {
		$query = Dataset::where('data_code', '=', $data['data_code'])
						->where('data_value', '=', $data['data_value']);

		return $query->firstOrFail();
	}

	/*
	*
	*  Gets data set with value not id
	*  
	*  @param type 	string    type of dataset
	*
	*/
	public static function getTypeWithValue($type)
	{
		$query = Dataset::where('data_code', '=', $type);
		$result = $query->lists('data_display', 'data_value');

		return $result;
	}

}