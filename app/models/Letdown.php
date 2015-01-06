<?php

class Letdown extends Eloquent {

	protected $table = 'letdown';

	/***************************Methods for API only*********************************/
	public static function getList()
	{
		return Letdown::select('move_doc_number')
			->where('lt_status', '=', 0)->get();
	}


	/***************************Methods for CMS only*********************************/

	public static function getLetDownList($data = array(), $getCount = false)
	{
		$query = DB::table('letdown');

		if( CommonHelper::hasValue($data['filter_doc_no']) ) $query->where('letdown.move_doc_number', 'LIKE', '%'. $data['filter_doc_no'] . '%');

		if( CommonHelper::hasValue($data['sort']) && CommonHelper::hasValue($data['order']))  {
			if($data['sort'] == 'doc_no'){
				$data['sort'] = 'letdown.move_doc_number';
			}
			$query->orderBy($data['sort'], $data['order']);
		}


		if( CommonHelper::hasValue($data['limit']) && CommonHelper::hasValue($data['page']) && !$getCount)  {
			$query->skip($data['limit'] * ($data['page'] - 1))
		          ->take($data['limit']);
		}
		$query->groupBy('letdown.move_doc_number');
		$result = $query->get();
		if($getCount) {
			$result = count($result);
		}

		return $result;

	}

	public static function getLetDownInfo($letdown_id = NULL){
		$query = DB::table('letdown')
				->join('letdown_details', 'letdown_details.move_doc_number', '=', 'letdown.move_doc_number','LEFT')
				->where('letdown.id', '=', $letdown_id);

		$result = $query->get();

		return $result[0];
	}

	/**
	* Unassign letdown
	*
	* @param  docNo      string 	document number
	* @return Status
	*/
	public static function unassignLetdown($docNo)
	{
		Letdown::where('move_doc_number', '=', $docNo)
			->update(array(
				'assigned_user_id'	   =>	0,
				'updated_at'		   =>	date('Y-m-d H:i:s')
				));
		return;
	}

	/**
	* Updates lt_status status in letdown main, not in letdown details table
	*
	*/
	public static function updateMoveToPickingHeaderStatus($docNo, $status)
	{
		Letdown::where('move_doc_number', '=', $docNo)
			->update(array(
				'updated_at'		   =>	date('Y-m-d H:i:s'),
				'lt_status' => $status)
				);
	}

}
/*
*/
