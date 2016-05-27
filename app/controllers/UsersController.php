<?php

class UsersController extends BaseController {
	private $data = array();

	protected $layout = "layouts.main";
	protected $apiUrl;
	protected $allowedUserRoles = array('1', '2', '5');

	public function __construct() {
    	date_default_timezone_set('Asia/Manila');
		$this->beforeFilter('csrf', array('on' => 'post'));
		$this->beforeFilter('auth', array('only'=> array('Dashboard')));
		$this->apiUrl = Config::get('constant.api_url');
	}

	public function showIndex() {
		// Check Permissions
		if (Session::has('permissions')) {
	    	if (!in_array('CanAccessUsers', unserialize(Session::get('permissions'))))  {
				return Redirect::to('/');
			}
    	} else {
			return Redirect::to('users/logout');
		}

		$this->getList();
	}

	public function getLogin() {
		$this->data['heading_title_login'] = Lang::get('users.heading_title_login');
		$this->data['heading_subtitle_login'] = Lang::get('users.heading_subtitle_login');

		$this->data['entry_username'] = Lang::get('users.entry_username');
		$this->data['entry_password'] = Lang::get('users.entry_password');

		$this->data['button_signin'] = Lang::get('general.button_signin');

		$this->layout->content = View::make('users.login', $this->data);
	}


	public function validateUser(){
		$username = $request->get('username');
      	$password = $request->get('password');

      if($username  == '' && $password == '') {
       return response()->json([
                              "message" => "Fields are all required",
                              "error"   => true],405);
      }
      if (Auth::attempt(array('username' => $username, 'password' => $password)))
      {

        if(in_array(Auth::user()->role_id, array(0))) {
          return response()->json(['message' => 'Account not allowed'], 405);
        }
        $user_detail = array('id' => Auth::user()->id,
                             'username' => Auth::user()->username,
                             'firstname' => Auth::user()->firstname,
                             'lastname'  => Auth::user()->lastname,
                             'location'  => Auth::user()->location);
          return response()->json([
                                  'message'  => 'Successfully login!',
                                  'result' => $user_detail
                                  ],200);
      }
      else
      {
          return response()->json([
                                  'message' => 'Invalid credentials.Please try again',
                                  'error'   => true
                                  ], 404);
      }
    


	}
	public function postSignin() {
		$username = Input::get('username');
		$password = Input::get('password');

		if (Auth::attempt(array('username'=>$username, 'password'=>$password)))
		{
			$userRole = UserRoles::find(Auth::user()->role_id);
			$userRoleId = Auth::user()->role_id;

			// if(!in_array(Auth::user()->role_id, $this->allowedUserRoles) || $userRole->deleted_at > '0000-00-00 00:00:00' || Auth::user()->deleted_at > '0000-00-00 00:00:00')
			if ($userRole->deleted_at > '0000-00-00 00:00:00' || Auth::user()->deleted_at > '0000-00-00 00:00:00')
			{
				Auth::logout();
				return Redirect::to('users/login')
		        ->with('message', Lang::get('users.error_login_stock_piler'))
		        ->withInput();
			}
			else {
				Session::put('permissions', serialize(json_decode($userRole->permissions)));
				return Redirect::to('/')->with('success', Lang::get('users.text_success_login'));
			}

		}

	    return Redirect::to('users/login')
	        ->with('message', Lang::get('users.error_login'))
	        ->withInput();
	}

	public function getLogout() {
		Auth::logout();
		Session::forget('permissions');

		return Redirect::to('/')->with('message', Lang::get('users.text_success_logout'));
	}

	public function updateProfileForm() {
		$this->data['heading_title_profile'] = Lang::get('users.heading_title_profile');

		$this->data['entry_username'] = Lang::get('users.entry_username');
		$this->data['entry_barcode'] = Lang::get('users.entry_barcode');
		$this->data['entry_firstname'] = Lang::get('users.entry_firstname');
		$this->data['entry_lastname'] = Lang::get('users.entry_lastname');
		$this->data['entry_user_role'] = Lang::get('users.entry_user_role');
		$this->data['entry_brand'] = Lang::get('users.entry_brand');

		$this->data['button_submit'] = Lang::get('general.button_submit');
		$brands = array('' => Lang::get('general.text_select'));
		foreach (Brands::getBrandsOption() as $item) {
			$brands[$item->id] = $item->brand_name;
		}
		$this->data['brand_options'] = $brands;
		// Options
		$user_roles = array('' => Lang::get('general.text_select'));
		foreach (UserRoles::getUserRolesOptions() as $item) {
			$user_roles[$item->id] = $item->role_name;
		}

		$this->data['user_role_options'] = $user_roles;

		$this->data['success'] = '';
		if (Session::has('success')) {
			$this->data['success'] = Session::get('success');
		}

		// Data
		$this->data['user'] = User::find(Auth::user()->id);

		$this->layout->content = View::make('users.profile', $this->data);
	}

	public function updateProfilePasswordForm() {
		$this->data['heading_title_password'] = Lang::get('users.heading_title_password');

		$this->data['entry_password'] = Lang::get('users.entry_password');
		$this->data['entry_confirm_password'] = Lang::get('users.entry_confirm_password');

		$this->data['button_submit'] = Lang::get('general.button_submit');

		$this->data['success'] = '';
		if (Session::has('success')) {
			$this->data['success'] = Session::get('success');
		}

		// Data
		$this->data['user'] = User::find(Auth::user()->id);

		$this->layout->content = View::make('users.change_password', $this->data);
	}

	/*public function exportCSV() {
		// Check Permissions
		if (Session::has('permissions')) {
	    	if (!in_array('CanExportUsers', unserialize(Session::get('permissions'))))  {
				return Redirect::to('user' . $this->setURL());
			}
    	} else {
			return Redirect::to('users/logout');
		}

		$arrParams = array(
							'filter_username'	=> Input::get('filter_username', NULL),
							'filter_barcode'	=> Input::get('filter_barcode', NULL),
							'filter_user_role'	=> Input::get('filter_user_role', NULL),
							'sort'				=> Input::get('sort', 'username'),
							'order'				=> Input::get('order', 'ASC'),
							'page'				=> NULL,
							'limit'				=> NULL
						);
		$results = User::getUsers($arrParams);

		$output = Lang::get('users.col_username') . ',';
		$output .= Lang::get('users.col_barcode') . ',';
		$output .= Lang::get('users.col_name') . ',';
		$output .= Lang::get('users.col_user_role') . ',';
		// $output .= Lang::get('users.col_brand') . ',';
		$output .= Lang::get('users.col_date') . "\n";

	    foreach ($results as $value) {
	    	$exportData = array(
	    						'"' . $value->username . '"',
	    						'"' . $value->barcode . '"',
	    						'"' . $value->name . '"',
	    						'"' . $value->role_name . '"',
	    						// '"' . $value->brand_name . '"',
	    						'"' . date('M d, Y', strtotime($value->created_at)) . '"'
	    					);

	      	$output .= implode(",", $exportData);
	      	$output .= "\n";
	  	}

		$headers = array(
			'Content-Type' => 'text/csv',
			'Content-Disposition' => 'attachment; filename="users_' . date('Ymd')  . '_' . time() . '.csv"',
		);

		return Response::make(rtrim($output, "\n"), 200, $headers);
	}*/

	public function exportCSV() {
		// Check Permissions
		if (Session::has('permissions')) {
	    	if (!in_array('CanExportUsers', unserialize(Session::get('permissions'))))  {
				return Redirect::to('user' . $this->setURL());
			}
    	} else {
			return Redirect::to('users/logout');
		}

		$this->data['col_username'] = Lang::get('users.col_username');
		$this->data['col_barcode'] = Lang::get('users.col_barcode');
		$this->data['col_name'] = Lang::get('users.col_name');
		$this->data['col_user_role'] = Lang::get('users.col_user_role');
		$this->data['col_brand'] = Lang::get('users.col_brand');
		$this->data['col_date'] = Lang::get('users.col_date');
		$this->data['text_empty_results'] = Lang::get('general.text_empty_results');

		$arrParams = array(
							'filter_username'	=> Input::get('filter_username', NULL),
							'filter_barcode'	=> Input::get('filter_barcode', NULL),
							'filter_user_role'	=> Input::get('filter_user_role', NULL),
							'sort'				=> Input::get('sort', 'username'),
							'order'				=> Input::get('order', 'ASC'),
							'page'				=> NULL,
							'limit'				=> NULL
						);
		$results = User::getUsers($arrParams);

		$this->data['results'] = $results;

		$pdf = App::make('dompdf');
		$pdf->loadView('users.report_list', $this->data)->setPaper('a4')->setOrientation('landscape');
		// return $pdf->stream();
		return $pdf->download('users_' . date('Ymd') . '.pdf');
	}

	public function insertDataForm() {
		// Check Permissions
		if (Session::has('permissions')) {
	    	if (!in_array('CanInsertUsers', unserialize(Session::get('permissions'))))  {
				return Redirect::to('user' . $this->setURL());
			}
    	} else {
			return Redirect::to('users/logout');
		}

		$this->data['heading_title_insert'] = Lang::get('users.heading_title_insert');

		$this->data['entry_username'] = Lang::get('users.entry_username');
		$this->data['entry_password'] = Lang::get('users.entry_password');
		$this->data['entry_confirm_password'] = Lang::get('users.entry_confirm_password');
		$this->data['entry_barcode'] = Lang::get('users.entry_barcode');
		$this->data['entry_firstname'] = Lang::get('users.entry_firstname');
		$this->data['entry_lastname'] = Lang::get('users.entry_lastname');
		$this->data['entry_user_role'] = Lang::get('users.entry_user_role');
		$this->data['entry_brand'] = Lang::get('users.entry_brand');

		$this->data['button_submit'] = Lang::get('general.button_submit');
		$this->data['button_cancel'] = Lang::get('general.button_cancel');

		// URL
		$this->data['url_cancel'] = URL::to('user' . $this->setURL());

		// Search Filters
		$this->data['filter_username'] = Input::get('filter_username');
		$this->data['filter_barcode'] = Input::get('filter_barcode');
		$this->data['filter_user_role'] = Input::get('filter_user_role');

		$this->data['sort'] = Input::get('sort', 'username');
		$this->data['order'] = Input::get('order', 'ASC');

		// Options
		$user_roles = array('' => Lang::get('general.text_select'));
		foreach (UserRoles::getUserRolesOptions() as $item) {
			$user_roles[$item->id] = $item->role_name;
		}

		$this->data['user_role_options'] = $user_roles;

		$brands = array('' => Lang::get('general.text_select'));
		foreach (Brands::getBrandsOption() as $item) {
			$brands[$item->id] = $item->brand_name;
		}

		$this->data['brand_options'] = $brands;


		$this->layout->content = View::make('users.insert', $this->data);
	}

	public function insertData() {
		// Check Permissions
		if (Session::has('permissions')) {
	    	if (!in_array('CanInsertUsers', unserialize(Session::get('permissions'))))  {
				return Redirect::to('user' . $this->setURL());
			}
    	} else {
			return Redirect::to('users/logout');
		}

		$rules = array(
						'username'				=> 'required|unique:users,username,NULL,id,deleted_at,0000-00-00 00:00:00',
						'password'				=> 'required|alpha_num|between:6,12|confirmed',
					    'password_confirmation'	=> 'required|alpha_num|between:6,12',
						'firstname'				=> 'required|min:2',
					    'lastname'				=> 'required|min:2',
					    'barcode'				=> 'unique:users,barcode,NULL,id,deleted_at,0000-00-00 00:00:00',
					    'role_id'				=> 'required',
					    'brand'					=> 'required'
					);
		$validator = Validator::make(Input::all(), $rules);

		if ($validator->fails()) {
			return Redirect::to('user/insert' . $this->setURL())
				->withErrors($validator)
				->withInput();
		} else {
			$arrParams = array(
							'username'		=> Input::get('username'),
							'password'		=> Hash::make(Input::get('password')),
							'firstname'		=> Input::get('firstname'),
							'lastname'		=> Input::get('lastname'),
							'barcode'		=> ( Input::get('role_id') == 2 ) ? '' : Input::get('barcode'), //if admin no barcode must empty
							'role_id'		=> Input::get('role_id'),
							'brand_id'			=> Input::get('brand'),
							'deleted_at'	=> '0000-00-00 00:00:00',
							'created_at'	=> date('Y-m-d H:i:s'),
							'updated_at'	=> date('Y-m-d H:i:s')
							);

			User::addUser($arrParams);

			// AuditTrail
			$role = UserRoles::find(Input::get('role_id'));
			$brand = Brands::find(Input::get('brand'));

			$data_before = '';
			$data_after = 'Username: ' . Input::get('username') . '<br />' .
						  'First Name: ' . Input::get('firstname') . '<br />' .
						  'Last Name: ' . Input::get('lastname') . '<br />' .
						  'Barcode: ' . Input::get('barcode') . '<br />' .
						  'Role: ' . $role->role_name . '<br />' .
						  'Brand: ' . $brand->brand_name;

			$arrParams = array(
							'module'		=> 'Users',
							'action'		=> 'Added New User',
							'reference'		=> Input::get('username'),
							'data_before'	=> $data_before,
							'data_after'	=> $data_after,
							'user_id'		=> Auth::user()->id,
							'created_at'	=> date('Y-m-d H:i:s'),
							'updated_at'	=> date('Y-m-d H:i:s')
							);
			AuditTrail::addAuditTrail($arrParams);
			// AuditTrail

			return Redirect::to('user'. $this->setURL())->with('success', Lang::get('users.text_success_insert'));
		}
	}

	public function updateDataForm() {
		// Check Permissions
		if (Session::has('permissions')) {
	    	if (!in_array('CanUpdateUsers', unserialize(Session::get('permissions'))) || Input::get('id')=='1' || User::find(Input::get('id'))==NULL) {
				return Redirect::to('user' . $this->setURL());
			}
    	} else {
			return Redirect::to('users/logout');
		}

		$this->data['heading_title_update'] = Lang::get('users.heading_title_update');

		$this->data['entry_username'] = Lang::get('users.entry_username');
		$this->data['entry_barcode'] = Lang::get('users.entry_barcode');
		$this->data['entry_firstname'] = Lang::get('users.entry_firstname');
		$this->data['entry_lastname'] = Lang::get('users.entry_lastname');
		$this->data['entry_user_role'] = Lang::get('users.entry_user_role');
		$this->data['entry_brand'] = Lang::get('users.entry_brand');

		$this->data['button_submit'] = Lang::get('general.button_submit');
		$this->data['button_cancel'] = Lang::get('general.button_cancel');

		// URL
		$this->data['url_cancel'] = URL::to('user' . $this->setURL());

		// Search Filters
		$this->data['filter_username'] = Input::get('filter_username');
		$this->data['filter_barcode'] = Input::get('filter_barcode');
		$this->data['filter_user_role'] = Input::get('filter_user_role');

		$this->data['sort'] = Input::get('sort', 'username');
		$this->data['order'] = Input::get('order', 'ASC');

		// Options
		$user_roles = array('' => Lang::get('general.text_select'));
		foreach (UserRoles::getUserRolesOptions() as $item) {
			$user_roles[$item->id] = $item->role_name;
		}

		$this->data['user_role_options'] = $user_roles;

		$brands = array('' => Lang::get('general.text_select'));
		foreach (Brands::getBrandsOption() as $item) {
			$brands[$item->id] = $item->brand_name;
		}

		$this->data['brand_options'] = $brands;

		// Data
		$this->data['user'] = User::find(Input::get('id'));

		$this->layout->content = View::make('users.update', $this->data);
	}

	public function updateData() {
		// Check Permissions
		if (Session::has('permissions')) {
	    	if ((!in_array('CanUpdateUsers', unserialize(Session::get('permissions'))) || Input::get('id')=='1' || User::find(Input::get('id'))==NULL) && Input::get('mode') == 'user') {
				return Redirect::to('user' . $this->setURL());
			}
    	} else {
			return Redirect::to('users/logout');
		}
		// echo "<pre>"; print_r(Input::all()); die();
		$rules = array(
						'firstname'				=> 'required|min:2',
					    'lastname'				=> 'required|min:2',
					    'barcode'				=> 'unique:users,barcode,' . Input::get('id') . ',id,deleted_at,0000-00-00 00:00:00',
					    'role_id'				=> 'required',
					    'brand'					=> 'required'
					);
		$validator = Validator::make(Input::all(), $rules);

		if ($validator->fails()) {
			$route = (Input::get('mode') == 'user') ? 'user/update'  . $this->setURL() .'&id=' . Input::get('id') : 'user/profile';

			return Redirect::to($route)->withErrors($validator)->withInput();
		} else {
			// AuditTrail
			$user = User::find(Input::get('id'));

			$role_name = '';
			if (UserRoles::find($user->role_id)!=NULL) {
				$role = UserRoles::find($user->role_id);
				$role_name = $role->role_name;
			}

			$brand_name = '';
			if (Brands::find($user->brand_id)!=NULL) {
				$brand = Brands::find($user->brand_id);
				$brand_name = $brand->brand_name;
			}

			$data_before = 'Username: ' . $user->username . '<br />' .
						   'First Name: ' . $user->firstname . '<br />' .
						   'Last Name: ' . $user->lastname . '<br />' .
						   'Barcode: ' . $user->barcode . '<br />' .
						   'Role: ' . $role_name . '<br />' .
						   'Brand: ' . $brand_name;
			// AuditTrail

			// Update User Details
			$arrParams = array(
							'firstname'		=> Input::get('firstname'),
							'lastname'		=> Input::get('lastname'),
							'barcode'		=> ( Input::get('role_id') == 2 ) ? '' : Input::get('barcode'), //if admin no barcode must empty
							'role_id'		=> Input::get('role_id'),
							'brand_id'			=> Input::get('brand'),
							'updated_at'	=> date('Y-m-d H:i:s')
							);
			User::updateUser(Input::get('id'), $arrParams);

			// AuditTrail
			$role = UserRoles::find(Input::get('role_id'));
			$brand = Brands::find(Input::get('brand'));

			$data_after = 'Username: ' . Input::get('username') . '<br />' .
						  'First Name: ' . Input::get('firstname') . '<br />' .
						  'Last Name: ' . Input::get('lastname') . '<br />' .
						  'Barcode: ' . Input::get('barcode') . '<br />' .
						  'Role: ' . $role_name . '<br />' .
						  'Brand: ' . $brand_name;

			$arrParams = array(
							'module'		=> 'Users',
							'action'		=> 'Modified User',
							'reference'		=> Input::get('username'),
							'data_before'	=> $data_before,
							'data_after'	=> $data_after,
							'user_id'		=> Auth::user()->id,
							'created_at'	=> date('Y-m-d H:i:s'),
							'updated_at'	=> date('Y-m-d H:i:s')
							);
			AuditTrail::addAuditTrail($arrParams);
			// AuditTrail

			$route = (Input::get('mode') == 'user') ? 'user' . $this->setURL() : 'user/profile';

			return Redirect::to($route)->with('success', Lang::get('users.text_success_update'));
		}
	}

	public function updatePasswordForm() {
		// Check Permissions
		if (Session::has('permissions')) {
	    	if (!in_array('CanChangePasswordUsers', unserialize(Session::get('permissions'))) || Input::get('id')=='1' || User::find(Input::get('id'))==NULL) {
				return Redirect::to('user' . $this->setURL());
			}
    	} else {
			return Redirect::to('users/logout');
		}

		$this->data['heading_title_password'] = Lang::get('users.heading_title_password');

		$this->data['entry_password'] = Lang::get('users.entry_password');
		$this->data['entry_confirm_password'] = Lang::get('users.entry_confirm_password');

		$this->data['button_submit'] = Lang::get('general.button_submit');
		$this->data['button_cancel'] = Lang::get('general.button_cancel');

		// URL
		$this->data['url_cancel'] = URL::to('user' . $this->setURL());

		// Search Filters
		$this->data['filter_username'] = Input::get('filter_username');
		$this->data['filter_barcode'] = Input::get('filter_barcode');
		$this->data['filter_user_role'] = Input::get('filter_user_role');

		$this->data['sort'] = Input::get('sort', 'username');
		$this->data['order'] = Input::get('order', 'ASC');

		// Data
		$this->data['user'] = User::find(Input::get('id'));

		$this->layout->content = View::make('users.password', $this->data);
	}

	public function updatePassword() {
		// Check Permissions
		if (Session::has('permissions')) {
	    	if ((!in_array('CanChangePasswordUsers', unserialize(Session::get('permissions'))) || Input::get('id')=='1' || User::find(Input::get('id'))==NULL) && Input::get('mode') == 'user') {
				return Redirect::to('user' . $this->setURL());
			}
    	} else {
			return Redirect::to('users/logout');
		}

		$rules = array(
						'password'				=> 'required|alpha_num|between:6,12|confirmed',
					    'password_confirmation'	=> 'required|alpha_num|between:6,12',
					);
		$validator = Validator::make(Input::all(), $rules);

		if ($validator->fails()) {
			$route = (Input::get('mode') == 'user') ? 'user/password' . $this->setURL() .'&id=' . Input::get('id') : 'user/change_password';

			return Redirect::to($route)->withErrors($validator)->withInput();
		} else {
			$arrParams = array(
							'password'		=> Hash::make(Input::get('password')),
							'updated_at'	=> date('Y-m-d H:i:s')
							);
			User::updateUser(Input::get('id'), $arrParams);

			// AuditTrail
			$user = User::find(Input::get('id'));

			$data_before = '';
			$data_after = 'Set New Password';

			$arrParams = array(
							'module'		=> 'Users',
							'action'		=> 'Changed Password',
							'reference'		=> $user->username,
							'data_before'	=> $data_before,
							'data_after'	=> $data_after,
							'user_id'		=> Auth::user()->id,
							'created_at'	=> date('Y-m-d H:i:s'),
							'updated_at'	=> date('Y-m-d H:i:s')
							);
			AuditTrail::addAuditTrail($arrParams);
			// AuditTrail

			$route = (Input::get('mode') == 'user') ? 'user' . $this->setURL() : 'user/change_password';

			return Redirect::to($route)->with('success', Lang::get('users.text_success_password'));
		}
	}

	public function deleteData() {
		// Check Permissions
		if (Session::has('permissions')) {
	    	if (!in_array('CanDeleteUsers', unserialize(Session::get('permissions')))) {
				return Redirect::to('user' . $this->setURL());
			}
    	} else {
			return Redirect::to('users/logout');
		}

		$rules = array(
						'selected'	=> 'required'
					);
		$validator = Validator::make(Input::all(), $rules);

		if ($validator->fails()) {
			return Redirect::to('user');
		} else {
			User::deleteUser(Input::get('selected'));

			// AuditTrail
			$arrDeleted = array();
			foreach (Input::get('selected') as $id) {
				$info = User::find($id);
				$arrDeleted[] = $info->username;
			}

			$data_before = '';
			$data_after = 'Deleted: ' . implode(', ', $arrDeleted);

			$arrParams = array(
							'module'		=> 'Users',
							'action'		=> 'Deleted Users',
							'reference'		=> implode(', ', $arrDeleted),
							'data_before'	=> $data_before,
							'data_after'	=> $data_after,
							'user_id'		=> Auth::user()->id,
							'created_at'	=> date('Y-m-d H:i:s'),
							'updated_at'	=> date('Y-m-d H:i:s')
							);
			AuditTrail::addAuditTrail($arrParams);
			// AuditTrail

			return Redirect::to('user' . $this->setURL())->with('success', Lang::get('users.text_success_delete'));
		}
	}

	protected function getList() {
		$this->data['heading_title'] = Lang::get('users.heading_title');

		$this->data['text_empty_results'] = Lang::get('general.text_empty_results');
		$this->data['text_total'] = Lang::get('general.text_total');
		$this->data['text_confirm'] = Lang::get('general.text_confirm');

		$this->data['label_filter_username'] = Lang::get('users.label_filter_username');
		$this->data['label_filter_barcode'] = Lang::get('users.label_filter_barcode');
		$this->data['label_filter_user_role'] = Lang::get('users.label_filter_user_role');

		$this->data['col_id'] = Lang::get('users.col_id');
		$this->data['col_username'] = Lang::get('users.col_username');
		$this->data['col_barcode'] = Lang::get('users.col_barcode');
		$this->data['col_name'] = Lang::get('users.col_name');
		$this->data['col_user_role'] = Lang::get('users.col_user_role');
		$this->data['col_brand'] = Lang::get('users.col_brand');
		$this->data['col_date'] = Lang::get('users.col_date');
		$this->data['col_action'] = Lang::get('users.col_action');

		$this->data['button_search'] = Lang::get('general.button_search');
		$this->data['button_clear'] = Lang::get('general.button_clear');
		$this->data['button_export'] = Lang::get('general.button_export');
		$this->data['button_insert'] = Lang::get('users.button_insert');
		$this->data['button_delete'] = Lang::get('users.button_delete');

		$this->data['link_edit'] = Lang::get('general.link_edit');
		$this->data['link_change_password'] = Lang::get('general.link_change_password');

		$this->data['error_delete'] = Lang::get('general.error_delete');

		// URL
		$url = $this->setURL();
		$this->data['url_insert'] = URL::to('user/insert' . $url);
		$this->data['url_update'] = URL::to('user/update' . $url);
		$this->data['url_password'] = URL::to('user/password' . $url);
		$this->data['url_export'] = URL::to('user/export' . $url);

		// Messages
		$this->data['error'] = '';

		$this->data['success'] = '';
		if (Session::has('success')) {
			$this->data['success'] = Session::get('success');
		}

		// Search Options
		$user_roles = array();
		foreach (UserRoles::getUserRolesOptions() as $item) {
			$user_roles[$item->id] = $item->role_name;
		}

		$this->data['filter_user_role_options'] = array('' => Lang::get('general.text_select')) + $user_roles;

		// Search Filters
		$filter_username = Input::get('filter_username', NULL);
		$filter_barcode = Input::get('filter_barcode', NULL);
		$filter_user_role = Input::get('filter_user_role', NULL);

		// Data
		$sort = Input::get('sort', 'username');
		$order = Input::get('order', 'ASC');
		$page = Input::get('page', 1);

		$arrParams = array(
							'filter_username'	=> $filter_username,
							'filter_barcode'	=> $filter_barcode,
							'filter_user_role'	=> $filter_user_role,
							'sort'				=> $sort,
							'order'				=> $order,
							'page'				=> $page,
							'limit'				=> 30
						);
		$results = User::getUsers($arrParams);
		$results_total = User::getUsers($arrParams,true);

		// Pagination
		$this->data['arrFilters'] = array(
										'filter_username'	=> $filter_username,
										'filter_barcode'	=> $filter_barcode,
										'filter_user_role'	=> $filter_user_role,
										'sort'				=> $sort,
										'order'				=> $order
									);

		$this->data['users'] = Paginator::make($results, $results_total, 30);
		$this->data['users_count'] = $results_total;

		$this->data['counter'] 	= $this->data['users']->getFrom();

		$this->data['filter_username'] = $filter_username;
		$this->data['filter_barcode'] = $filter_barcode;
		$this->data['filter_user_role'] = $filter_user_role;

		$this->data['sort'] = $sort;
		$this->data['order'] = $order;
		$this->data['page'] = $page;

		$url = '?filter_username=' . $filter_username . '&filter_barcode=' . $filter_barcode;
		$url .= '&filter_user_role=' . $filter_user_role;
		$url .= '&page=' . $page;

		$order_id = ($sort=='id' && $order=='ASC') ? 'DESC' : 'ASC';
		$order_username = ($sort=='username' && $order=='ASC') ? 'DESC' : 'ASC';
		$order_barcode = ($sort=='barcode' && $order=='ASC') ? 'DESC' : 'ASC';
		$order_name = ($sort=='name' && $order=='ASC') ? 'DESC' : 'ASC';
		$order_role = ($sort=='role' && $order=='ASC') ? 'DESC' : 'ASC';
		$order_brand = ($sort=='brand' && $order=='ASC') ? 'DESC' : 'ASC';
		$order_date = ($sort=='date' && $order=='ASC') ? 'DESC' : 'ASC';

		$this->data['sort_id'] = URL::to('user' . $url . '&sort=id&order=' . $order_id, NULL, FALSE);
		$this->data['sort_username'] = URL::to('user' . $url . '&sort=username&order=' . $order_username, NULL, FALSE);
		$this->data['sort_barcode'] = URL::to('user' . $url . '&sort=barcode&order=' . $order_barcode, NULL, FALSE);
		$this->data['sort_name'] = URL::to('user' . $url . '&sort=name&order=' . $order_name, NULL, FALSE);
		$this->data['sort_role'] = URL::to('user' . $url . '&sort=role&order=' . $order_role, NULL, FALSE);
		$this->data['sort_brand'] = URL::to('user' . $url . '&sort=brand&order=' . $order_brand, NULL, FALSE);
		$this->data['sort_date'] = URL::to('user' . $url . '&sort=date&order=' . $order_date, NULL, FALSE);

		// Permissions
		$this->data['permissions'] = unserialize(Session::get('permissions'));

		$this->layout->content = View::make('users.list', $this->data);
	}

	protected function setURL() {
		// Search Filters
		$url = '?filter_username=' . Input::get('filter_username', NULL);
		$url .= '&filter_barcode=' . Input::get('filter_barcode', NULL);
		$url .= '&filter_user_role=' . Input::get('filter_user_role', NULL);
		$url .= '&sort=' . Input::get('sort', 'username');
		$url .= '&order=' . Input::get('order', 'ASC');
		$url .= '&page=' . Input::get('page', 1);

		return $url;
	}
}