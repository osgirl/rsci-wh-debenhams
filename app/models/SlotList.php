<?php

class SlotList extends Eloquent {

	protected $table = 'slot_lists';

	public static function getSlotLists($data = array()) {
		$query = DB::table('slot_lists');

		if( CommonHelper::hasValue($data['filter_slot_no']) ) $query->where('slot_code', 'LIKE', '%'.$data['filter_slot_no'].'%');

		if( CommonHelper::hasValue($data['sort']) && CommonHelper::hasValue($data['order']))  {
			if ($data['sort']=='date') $data['sort'] = 'created_at';
			if ($data['sort']=='username') $data['sort'] = 'users.username';
			if ($data['sort']=='details') $data['sort'] = 'data_before';

			$query->orderBy($data['sort'], $data['order']);
		}

		if( CommonHelper::hasValue($data['limit']) && CommonHelper::hasValue($data['page']))  {
			$query->skip($data['limit'] * ($data['page'] - 1))
		          ->take($data['limit']);
		}

		$result = $query->get();

		return $result;
	}

	public static function getCountSlotLists($data = array()) {
		$query = DB::table('slot_lists');

		if( CommonHelper::hasValue($data['filter_slot_no']) ) $query->where('slot_code', 'LIKE', '%'.$data['filter_slot_no'].'%');

		return $query->count();
	}

	public static function isSlotExist($slotCode) {
		$query = SlotList::where('slot_code', '=', $slotCode)->first();
		// print_r($query);
		if (! empty($query) ) return $query['slot_code'];

		return false;
	}
}