<?php

class HomeController extends BaseController {
	private $data = array();

	/*
	|--------------------------------------------------------------------------
	| Default Home Controller
	|--------------------------------------------------------------------------
	|
	| You may wish to use controllers instead of, or in addition to, Closure
	| based routes. That's great! Here is an example controller method to
	| get you started. To route to this controller, just add the route:
	|
	|	Route::get('/', 'HomeController@showWelcome');
	|
	*/

	protected $layout = "layouts.main";

	public function __construct() {
		$this->beforeFilter('csrf', array('on' => 'post'));
	}

	public function showIndex() {
		if(Auth::check()) 
		{
			return Redirect::to('purchase_order');
		}
		else {
			$this->data['heading_title_login'] = Lang::get('users.heading_title_login');
			$this->data['heading_subtitle_login'] = Lang::get('users.heading_subtitle_login');
			
			$this->data['entry_username'] = Lang::get('users.entry_username');
			$this->data['entry_password'] = Lang::get('users.entry_password');
			
			$this->data['button_signin'] = Lang::get('general.button_signin');
			
			$this->layout->content = View::make('users.login', $this->data);
			
		}
	}

	/*
	*
	* @return Possible status
	*
	*/
	public static function getStatusValues()
	{	
		$status = Config::get('statuses');
		return Response::json(array(
			'error' => false,
			'message' => $status),
			200
		);
	}
}