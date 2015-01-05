<?php

class ApiUsers extends BaseController {

	protected static $allowed_roles = array(3,4);

	/**
	 * User login
	 *
	 * @param 	$credential	username and password of the user
	 * @return 	Response
	 */
	public function postLogin() {

		try {
			$credential = array();
			$credential['username'] = Request::get('username');
			$credential['password'] = Request::get('password');


			if(! CommonHelper::hasValue($credential['username']) ) throw new Exception( 'Username cannot be null!');
			if(! CommonHelper::hasValue($credential['password']) ) throw new Exception( 'Password cannot be null!');

			if(Auth::attempt($credential))
			{
				$deleted_at = Auth::user()->deleted_at;

				if(!in_array(Auth::user()->role_id, self::$allowed_roles)) {
					throw new Exception( 'Account not allowed.');
				}
				//validate if user account has been deleted
				if( $deleted_at > '0000-00-00 00:00:00' ) throw new Exception( 'Invalid username or password!');

				$user_detail = array(
								'user_id' 	=> Auth::user()->id,
								'username' 	=> Auth::user()->username,
								'barcode'	=> Auth::user()->barcode,
								'role_id'	=> Auth::user()->role_id
							);

				if(Auth::user()->role_id == 4) $user_detail['store_code'] = Auth::user()->store_code;

				DebugHelper::log(__METHOD__ .' User detail ',$user_detail);
				return Response::json(array(
					"error" => false,
					"result" => array("user" => $user_detail),
					"message" => 'Successfully logged in!'),
					200
				);
			}
			else
			{
				throw new Exception( 'Invalid username or password!');
			}

		}catch(Exception $e) {
			return Response::json(array(
				"error" => true,
				"message" => $e->getMessage()),
				400
			);
		}
	}

	/**
	 * Barcode login
	 *
	 * @param 	$credential	username and password of the user
	 * @return 	Response
	 */
	public function postBarcodeLogin() {

		try {
			$employee_code = Request::get('employee_barcode');

			if(! CommonHelper::hasValue($employee_code) ) throw new Exception( 'Missing employee barcode.');

			$result = User::getBarcodeUser($employee_code);
			DebugHelper::logVar(__METHOD__ . 'barcode result: ', print_r($result, true));

			if($result['role_id'] == 3)
			{
				$user = $result->toArray();
				//validate if user account has been deleted

				$user_detail = array(
								'user_id' 	=> $user['id'],
								'username' 	=> $user['username'],
								'barcode'	=> $user['barcode'],
								'role_id'	=> $user['role_id']
							);
				return Response::json(array(
					"error" => false,
					"result" => array("user" => $user_detail),
					"message" => 'Successfully logged in!'),
					200
				);
			}
			else {
				throw new Exception( 'Employee barcode not registered.');
			}

		}catch(Exception $e) {
			return Response::json(array(
				"error" => true,
				"message" => $e->getMessage()),
				400
			);
		}
	}

	/**
	 * Logout current user
	 *
	 * @param
	 * @return Response
	 */
	public function postLogout() {
		Auth::logout();

		return Response::json(array(
			"error" => false,
			"message" => 'Successfully logged out!'),
			200
		);
	}
}