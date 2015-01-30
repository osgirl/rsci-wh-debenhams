<?php

class Department extends Eloquent {

	protected $table = 'department';

	public static function getDepartments($data = array()) {
		$query = Department::where('sub_dept', '=', 0)
										->where('class', '=', 0)
										->where('sub_class', '=', 0)
										->orderBy('dept_code', 'ASC');

		$result = $query->get();

		return $result;
	}

	public static function getSubDepartments($dept_code = NULL) {
		$query = Department::where('dept_code', '=', $dept_code)
										->where('sub_dept', '!=', 0)
										->where('class', '=', 0)
										->where('sub_class', '=', 0)
										->orderBy('sub_dept', 'ASC');

		$result = $query->get();

		return $result;
	}

	public static function getBrands() {
		return Department::where('dept_code', '<>', 0)
			->where('sub_dept', '=', 0)
			->where('class', '=', 0)
			->where('sub_class', '=', 0)
			->get(array('id', 'description'))
			->toArray();
	}

	public static function getDivisions() {
		return Department::where('dept_code', '<>', 0)
			->where('sub_dept', '<>', 0)
			->where('class', '=', 0)
			->where('sub_class', '=', 0)
			->get(array('id', 'description'))
			->toArray();
	}
}