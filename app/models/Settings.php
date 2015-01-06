<?php

class Settings extends Eloquent {

	protected $table = 'settings';
	
	public static function addSetting($data = array()) {
		DB::table('settings')->insert($data);
	}
	
	public static function updateSetting($id, $data = array()) {
		$query = DB::table('settings')->where('id', '=', $id);
		$query->update($data);	
	}
	
	public static function deleteSetting($data = array()) {
		foreach ($data as $item) {
			$query = DB::table('settings')->where('id', '=', $item);
			$query->update(array('deleted_at' => date('Y-m-d H:i:s')));			
		}
	}
	
	public static function getSettings($data = array()) {
		$query = DB::table('settings')->where(function($query_sub) 
										{
							                $query_sub->where('deleted_at', '=', '0000-00-00 00:00:00')
								   				  	  ->orWhere('deleted_at', '=', NULL);
							            });
		
		if( CommonHelper::hasValue($data['sort']) && CommonHelper::hasValue($data['order']))  {
			if ($data['sort']=='brand') $data['sort'] = 'brand_name';
			
			$query->orderBy($data['sort'], $data['order']);
		}
		
		if( CommonHelper::hasValue($data['limit']) && CommonHelper::hasValue($data['page']))  {
			$query->skip($data['limit'] * ($data['page'] - 1))
		          ->take($data['limit']);
		}
		
		$result = $query->get();
				
		return $result;
	}
	
	public static function getCountSettings($data = array()) {
		$query = DB::table('settings')->where(function($query_sub) 
										{
							                $query_sub->where('deleted_at', '=', '0000-00-00 00:00:00')
								   				  	  ->orWhere('deleted_at', '=', NULL);
							            });
							   								   
		return $query->count();
	}
	
	public static function getSettingsOptions($data = array()) {
		$query = DB::table('settings')->where(function($query_sub) 
										{
							                $query_sub->where('deleted_at', '=', '0000-00-00 00:00:00')
								   				  	  ->orWhere('deleted_at', '=', NULL);
							            });
		$result = $query->get();
				
		return $result;
	}
}