<?php

class Picklist extends Eloquent {

	protected $table = 'picklist';

	/*****CMS Functions*****/
	public static function getPickList($picklistDocNo)
	{
		$picklist = Picklist::where('picklist.move_doc_number', '=', $picklistDocNo)
		->join('picklist_details', 'picklist.move_doc_number','=', 'picklist.move_doc_number')
		->where('picklist_details.move_doc_number', '=', $picklistDocNo)
			->first()->toArray();
		return $picklist;
	}

	public static function getPickingList($data= array(), $getCount=false)
	{
		$query = Picklist::select(DB::raw('wms_picklist.*, sum(wms_picklist_details.move_to_shipping_area) as sum_moved, sum(wms_picklist_details.assigned_user_id) as sum_assigned, store_code' ))
			->join('picklist_details', 'picklist_details.move_doc_number', '=', 'picklist.move_doc_number');

		if( CommonHelper::hasValue($data['filter_doc_no']) ) $query->where('picklist.move_doc_number', 'LIKE', '%'. $data['filter_doc_no'] . '%');
		if( CommonHelper::hasValue($data['filter_type']) ) $query->where('type', '=',  $data['filter_type']);
		if( CommonHelper::hasValue($data['filter_status']) ) $query->where('pl_status', '=', $data['filter_status']);

		if( CommonHelper::hasValue($data['sort']) && CommonHelper::hasValue($data['order']))  {
			if($data['sort'] == 'doc_no') $data['sort'] = 'picklist.move_doc_number';
			$query->orderBy($data['sort'], $data['order']);
		}

		
		if( CommonHelper::hasValue($data['limit']) && CommonHelper::hasValue($data['page']) && !$getCount)  {
			$query->skip($data['limit'] * ($data['page'] - 1))
		          ->take($data['limit']);
		}
		$query->groupBy('picklist.move_doc_number');
		$result = $query->get();

		return $result;
	}

	public static function getPickingListCount($data)
	{
		$query = Picklist::select('*');
		if( CommonHelper::hasValue($data['filter_doc_no']) ) $query->where('move_doc_number', 'LIKE', '%'. $data['filter_doc_no'] . '%');
		if( CommonHelper::hasValue($data['filter_type']) ) $query->where('type', '=',  $data['filter_type']);
		if( CommonHelper::hasValue($data['filter_status']) ) $query->where('pl_status', '=', $data['filter_status']);
		
		$result = $query->count();

		return $result;
		
	}

	public static function changeToStore($docNo)
	{
		$picklistDetails = Picklist::select(DB::raw('sum(wms_picklist_details.move_to_shipping_area) as sum_moved, sum(wms_picklist_details.assigned_user_id) as sum_assigned'))
			->join('picklist_details', 'picklist_details.move_doc_number', '=', 'picklist.move_doc_number')
			// ->whereI('picklist.move_doc_number', '=',$docNo)
			->whereIn('picklist.move_doc_number', $docNo)
			->groupBy('picklist.move_doc_number')
			->first();
		DebugHelper::log(__METHOD__, $picklistDetails);
		if(count($picklistDetails) === 0) throw new Exception("Document number does not exists");
		if($picklistDetails['sum_moved'] > 0 || $picklistDetails['sum_assigned'] > 0) {
			throw new Exception("This picklist cannot be changed to type store");
		}	
		// Picklist::where('move_doc_number', '=', $docNo)
		Picklist::whereIn('move_doc_number', $docNo)
			->update(array(
				'type'		=> 'store',
				'updated_at'=>	date('Y-m-d H:i:s')));
		return;
	}
	
}
