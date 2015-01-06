<?php

class AuditTrail extends Eloquent {

	protected $table = 'audit_trail';

	public static function addAuditTrail($data = array()) {
		DB::table('audit_trail')->insert($data);
	}
	
	public static function getAuditTrails($data = array()) {
		$query = DB::table('audit_trail')->join('users', 'audit_trail.user_id', '=', 'users.id', 'LEFT');

		if( CommonHelper::hasValue($data['filter_module']) ) {
			$filter_module = Config::get('audit_trail_modules.'.$data['filter_module']);
			// $query->where('module', '=', $data['filter_module']);
			$query->where('module', '=', $filter_module);
		}
		if( CommonHelper::hasValue($data['filter_action']) ) $query->where('action', 'LIKE', '%'.$data['filter_action'].'%');
		if( CommonHelper::hasValue($data['filter_reference']) ) $query->where('reference', 'LIKE', '%'.$data['filter_reference'].'%');
		if( CommonHelper::hasValue($data['filter_user']) ) $query->where('user_id', '=', $data['filter_user']);
		if( CommonHelper::hasValue($data['filter_date_from']) && CommonHelper::hasValue($data['filter_date_to'])) $query->whereBetween('audit_trail.created_at', array($data['filter_date_from'] . ' 00:00:00', $data['filter_date_to'] . ' 23:59:59'));
		
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
		
		$result = $query->get(array(
										'audit_trail.*', 
										'users.username',
										'users.firstname',
										'users.lastname'
									)
								);
				
		return $result;
	}
	
	public static function getCountAuditTrails($data = array()) {
		$query = DB::table('audit_trail')->join('users', 'audit_trail.user_id', '=', 'users.id', 'LEFT');

		if( CommonHelper::hasValue($data['filter_module']) ) $query->where('module', '=', $data['filter_module']);
		if( CommonHelper::hasValue($data['filter_action']) ) $query->where('action', 'LIKE', '%'.$data['filter_action'].'%');
		if( CommonHelper::hasValue($data['filter_reference']) ) $query->where('reference', 'LIKE', '%'.$data['filter_reference'].'%');
		if( CommonHelper::hasValue($data['filter_user']) ) $query->where('user_id', '=', $data['filter_user']);
		if( CommonHelper::hasValue($data['filter_date_from']) && CommonHelper::hasValue($data['filter_date_to'])) $query->whereBetween('audit_trail.created_at', array($data['filter_date_from'] . ' 00:00:00', $data['filter_date_to'] . ' 23:59:59'));
								   								   
		return $query->count();
	}
}