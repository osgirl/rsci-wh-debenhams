<?php

class Department extends Eloquent {

	protected $table = 'department';
	
	public static function getDepartments($data = array()) {
		$query = DB::table('department')->where('sub_dept', '=', 0)
										->where('class', '=', 0)
										->where('sub_class', '=', 0)
										->orderBy('dept_code', 'ASC');
		
		$result = $query->get();
		
		return $result;
	}
	
	public static function getSubDepartments($dept_code = NULL) {
		$query = DB::table('department')->where('dept_code', '=', $dept_code)
										->where('sub_dept', '!=', 0)
										->where('class', '=', 0)
										->where('sub_class', '=', 0)
										->orderBy('sub_dept', 'ASC');
		
		$result = $query->get();
				
		return $result;
	}
}