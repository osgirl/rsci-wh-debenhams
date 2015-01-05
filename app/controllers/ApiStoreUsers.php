<?php

class ApiStoreUsers extends BaseController {

	// protected static $allowed_roles = array(3,4);
	
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

			if(! CommonHelper::hasValue($credential['username']) ) throw new Exception( 'Username cannot be null!');
			
			$getUser = StoreUsers::checkUser($credential);
			
			if(! empty($getUser) )
			{
				/*if(!in_array(Auth::user()->role_id, self::$allowed_roles)) {
					throw new Exception( 'Account not allowed.');
				}*/
				$userinfo = $getUser->toArray();
				
				$user_detail = array(
								'user_id' 	=> $userinfo['id'],
								'username' 	=> $userinfo['username'],
								'role_id'	=> $userinfo['role_id']
							);
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
				throw new Exception( 'Invalid username!');
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