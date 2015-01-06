<?php

class UserRoles extends Eloquent {

	protected $table = 'user_roles';
	
	public static function addUserRole($data = array()) {
		DB::table('user_roles')->insert($data);
	}
	
	public static function updateUserRole($id, $data = array()) {
		$query = DB::table('user_roles')->where('id', '=', $id);
		$query->update($data);	
	}
	
	public static function deleteUserRole($data = array()) {
		foreach ($data as $item) {
			$query = DB::table('user_roles')->where('id', '=', $item);
			$query->update(array('deleted_at' => date('Y-m-d H:i:s')));			
		}
	}
	
	public static function getUserRoles($data = array()) {
		$query = DB::table('user_roles')->where('role_name', '!=', 'superadmin')
										->where(function($query_sub) 
											{
								                $query_sub->where('deleted_at', '=', '0000-00-00 00:00:00')
									   				  	  ->orWhere('deleted_at', '=', NULL);
								            });

		if( CommonHelper::hasValue($data['filter_role_name']) ) $query->where('role_name', 'LIKE', '%'.$data['filter_role_name'].'%');
		
		$query->orderBy($data['sort'], $data['order']);
		
		if( CommonHelper::hasValue($data['limit']) && CommonHelper::hasValue($data['page']))  {
			$query->skip($data['limit'] * ($data['page'] - 1))
		          ->take($data['limit']);
		}
		
		$result = $query->get();
				
		return $result;
	}
	
	public static function getCountUserRoles($data = array()) {
		$query = DB::table('user_roles')->where('role_name', '!=', 'superadmin')
										->where(function($query_sub) 
											{
								                $query_sub->where('deleted_at', '=', '0000-00-00 00:00:00')
									   				  	  ->orWhere('deleted_at', '=', NULL);
								            });
		
		if( CommonHelper::hasValue($data['filter_role_name']) ) $query->where('role_name', 'LIKE', '%'.$data['filter_role_name'].'%');
							   								   
		return $query->count();
	}
	
	public static function getUserRolesOptions() {
		$query = DB::table('user_roles')->where('role_name', '!=', 'superadmin')
										->where(function($query_sub) 
											{
								                $query_sub->where('deleted_at', '=', '0000-00-00 00:00:00')
									   				  	  ->orWhere('deleted_at', '=', NULL);
								            })
										->orderBy('role_name', 'ASC');
		
		$result = $query->get();
				
		return $result;
	}
}