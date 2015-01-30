<?php


class ApiDepartment extends BaseController {

	public function __construct() {
		date_default_timezone_set('Asia/Manila');
	}

	/**
	* Get Brands
	*
	* @example  www.example.com/api/{version}/department/brands
	* @return array of brands
	*/
	public static function getBrands()
	{
		try {
			$brands = Department::getBrands();
			return CommonHelper::return_success_message($brands);
		} catch (Exception $e) {
			return CommonHelper::return_fail($e->getMessage());
		}

	}

	/**
	* Get Brands
	*
	* @example  www.example.com/api/{version}/department/divisions
	* @return array of brands
	*/
	public static function getDivisions()
	{
		try {
			$divisions = Department::getDivisions();
			return CommonHelper::return_success_message($divisions);
		} catch (Exception $e) {
			return CommonHelper::return_fail($e->getMessage());
		}

	}

}