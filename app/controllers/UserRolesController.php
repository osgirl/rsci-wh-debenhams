<?php

class UserRolesController extends BaseController {
	private $data = array();

	protected $layout = "layouts.main";

	public function __construct()
    {
    	$this->beforeFilter('csrf', array('on' => 'post'));
    	$this->beforeFilter('auth', array('only' => array('Dashboard')));

		// Check Permissions
		if (Session::has('permissions')) {
	    	if (!in_array('CanAccessUserRoles', unserialize(Session::get('permissions'))))  {
				return Redirect::to('/');
			}
    	} else {
			return Redirect::to('users/logout');
		}
    }

	public function showIndex() {
		// Check Permissions
		if (Session::has('permissions')) {
	    	if (!in_array('CanAccessUserRoles', unserialize(Session::get('permissions'))))  {
				return Redirect::to('/');
			}
    	} else {
			return Redirect::to('users/logout');
		}

		$this->getList();
	}

	public function insertDataForm() {
		// Check Permissions
		if (Session::has('permissions')) {
	    	if (!in_array('CanInsertUserRoles', unserialize(Session::get('permissions'))))  {
				return Redirect::to('user_roles' . $this->setURL());
			}
    	} else {
			return Redirect::to('users/logout');
		}

		$this->data = Lang::get('user_roles');
		$this->data['button_submit'] = Lang::get('general.button_submit');
		$this->data['button_cancel'] = Lang::get('general.button_cancel');

		// URL
		$this->data['url_cancel'] = URL::to('user_roles' . $this->setURL());

		// Search Filters
		$this->data['filter_role_name'] = Input::get('filter_role_name');

		$this->data['sort'] = Input::get('sort', 'role_name');
		$this->data['order'] = Input::get('order', 'ASC');

		$this->layout->content = View::make('user_roles.insert', $this->data);
	}

	public function insertData() {
		// Check Permissions
		if (Session::has('permissions')) {
	    	if (!in_array('CanInsertUserRoles', unserialize(Session::get('permissions'))))  {
				return Redirect::to('user_roles' . $this->setURL());
			}
    	} else {
			return Redirect::to('users/logout');
		}

		$rules = array(
						'role_name'		=> 'required|unique:user_roles,role_name,NULL,id,deleted_at,0000-00-00 00:00:00',
						'permissions'	=> 'required'
					);
		$validator = Validator::make(Input::all(), $rules);

		if ($validator->fails()) {
			return Redirect::to('user_roles/insert' . $this->setURL())
				->withErrors($validator)
				->withInput();
		} else {
			$arrParams = array(
							'role_name'		=> Input::get('role_name'),
							'permissions'	=> json_encode(Input::get('permissions')),
							'created_at'	=> date('Y-m-d H:i:s'),
							'updated_at'	=> date('Y-m-d H:i:s')
							);
			UserRoles::addUserRole($arrParams);

			// AuditTrail
			$data_before = '';
			$data_after = 'Role Name: ' . Input::get('role_name') . '<br />' .
						  'Permissions: ' . implode(', ', Input::get('permissions'));

			$arrParams = array(
							'module'		=> 'User Roles',
							'action'		=> 'Added New User Role',
							'reference'		=> Input::get('role_name'),
							'data_before'	=> $data_before,
							'data_after'	=> $data_after,
							'user_id'		=> Auth::user()->id,
							'created_at'	=> date('Y-m-d H:i:s'),
							'updated_at'	=> date('Y-m-d H:i:s')
							);
			AuditTrail::addAuditTrail($arrParams);
			// AuditTrail

			return Redirect::to('user_roles'. $this->setURL())->with('success', Lang::get('user_roles.text_success_insert'));
		}
	}

	public function updateDataForm() {
		// Check Permissions
		if (Session::has('permissions')) {
	    	if (!in_array('CanUpdateUserRoles', unserialize(Session::get('permissions'))) || Input::get('id')=='1' || UserRoles::find(Input::get('id'))==NULL) {
				return Redirect::to('user_roles' . $this->setURL());
			}
    	} else {
			return Redirect::to('users/logout');
		}

		$this->data = Lang::get('user_roles');

		$this->data['button_submit'] = Lang::get('general.button_submit');
		$this->data['button_cancel'] = Lang::get('general.button_cancel');

		// URL
		$this->data['url_cancel'] = URL::to('user_roles' . $this->setURL());

		// Search Filters
		$this->data['filter_role_name'] = Input::get('filter_role_name');

		$this->data['sort'] = Input::get('sort', 'role_name');
		$this->data['order'] = Input::get('order', 'ASC');
		$this->data['page'] = Input::get('page', 1);

		// Data
		$this->data['user_role'] = UserRoles::find(Input::get('id'));
		// echo "<pre>"; print_r($this->data['user_role']); die();

		$this->layout->content = View::make('user_roles.update', $this->data);
	}

	public function updateData() {
		// Check Permissions
		if (Session::has('permissions')) {
	    	if (!in_array('CanUpdateUserRoles', unserialize(Session::get('permissions'))) || Input::get('role_id')=='1' || UserRoles::find(Input::get('role_id'))==NULL) {
				return Redirect::to('user_roles' . $this->setURL());
			}
    	} else {
			return Redirect::to('users/logout');
		}

		$rules = array(
						'role_name'		=> 'required|unique:user_roles,role_name,' . Input::get('role_id') . ',id,deleted_at,0000-00-00 00:00:00',
						'permissions'	=> 'required'
					);
		$validator = Validator::make(Input::all(), $rules);

		if ($validator->fails()) {
			return Redirect::to('user_roles/update' . $this->setURL() .'&id=' . Input::get('role_id'))
				->withErrors($validator)
				->withInput();
		} else {
			// AuditTrail
			$role = UserRoles::find(Input::get('role_id'));
			$data_before = 'Role Name: ' . $role->role_name . '<br />' .
						   'Permissions: ' . implode(', ', json_decode($role->permissions));
			// AuditTrail

			// Update User Role Details
			$arrParams = array(
							'role_name'		=> Input::get('role_name'),
							'permissions'	=> json_encode(Input::get('permissions')),
							'updated_at'	=> date('Y-m-d H:i:s')
							);
			UserRoles::updateUserRole(Input::get('role_id'), $arrParams);

			// AuditTrail
			$data_after = 'Role Name: ' . Input::get('role_name') . '<br />' .
						  'Permissions: ' . implode(', ', Input::get('permissions'));

			$arrParams = array(
							'module'		=> 'User Roles',
							'action'		=> 'Modified User Role',
							'reference'		=> Input::get('role_name'),
							'data_before'	=> $data_before,
							'data_after'	=> $data_after,
							'user_id'		=> Auth::user()->id,
							'created_at'	=> date('Y-m-d H:i:s'),
							'updated_at'	=> date('Y-m-d H:i:s')
							);
			AuditTrail::addAuditTrail($arrParams);
			// AuditTrail

			return Redirect::to('user_roles' . $this->setURL())->with('success', Lang::get('user_roles.text_success_update'));
		}
	}

	public function deleteData() {
		// Check Permissions
		if (Session::has('permissions')) {
	    	if (!in_array('CanDeleteUserRoles', unserialize(Session::get('permissions'))))  {
				return Redirect::to('user_roles' . $this->setURL());
			}
    	} else {
			return Redirect::to('users/logout');
		}

		$rules = array(
						'selected'	=> 'required'
					);
		$validator = Validator::make(Input::all(), $rules);

		if ($validator->fails()) {
			return Redirect::to('user_roles');
		} else {
			UserRoles::deleteUserRole(Input::get('selected'));

			// AuditTrail
			$arrDeleted = array();
			foreach (Input::get('selected') as $id) {
				$info = UserRoles::find($id);
				$arrDeleted[] = $info->role_name;
			}

			$data_before = '';
			$data_after = 'Deleted: ' . implode(', ', $arrDeleted);

			$arrParams = array(
							'module'		=> 'User Roles',
							'action'		=> 'Deleted User Roles',
							'reference'		=> implode(', ', $arrDeleted),
							'data_before'	=> $data_before,
							'data_after'	=> $data_after,
							'user_id'		=> Auth::user()->id,
							'created_at'	=> date('Y-m-d H:i:s'),
							'updated_at'	=> date('Y-m-d H:i:s')
							);
			AuditTrail::addAuditTrail($arrParams);
			// AuditTrail

			return Redirect::to('user_roles' . $this->setURL())->with('success', Lang::get('user_roles.text_success_delete'));
		}
	}

	protected function getList() {
		$this->data['heading_title'] = Lang::get('user_roles.heading_title');

		$this->data['text_empty_results'] = Lang::get('general.text_empty_results');
		$this->data['text_total'] = Lang::get('general.text_total');
		$this->data['text_confirm'] = Lang::get('general.text_confirm');

		$this->data['label_filter_role_name'] = Lang::get('user_roles.label_filter_role_name');

		$this->data['col_id'] = Lang::get('user_roles.col_id');
		$this->data['col_role_name'] = Lang::get('user_roles.col_role_name');
		$this->data['col_action'] = Lang::get('user_roles.col_action');

		$this->data['button_search'] = Lang::get('general.button_search');
		$this->data['button_clear'] = Lang::get('general.button_clear');
		$this->data['button_insert'] = Lang::get('user_roles.button_insert');
		$this->data['button_delete'] = Lang::get('user_roles.button_delete');

		$this->data['link_edit'] = Lang::get('general.link_edit');

		$this->data['error_delete'] = Lang::get('general.error_delete');

		// URL
		$url = $this->setURL();
		$this->data['url_insert'] = URL::to('user_roles/insert' . $url);
		$this->data['url_update'] = URL::to('user_roles/update' . $url);

		// Messages
		$this->data['error'] = '';

		$this->data['success'] = '';
		if (Session::has('success')) {
			$this->data['success'] = Session::get('success');
		}

		// Search Filters
		$filter_role_name = Input::get('filter_role_name', NULL);

		// Data
		$page = Input::get('page', 1);
		$sort = Input::get('sort', 'role_name');
		$order = Input::get('order', 'ASC');

		$arrParams = array(
							'filter_role_name'	=> $filter_role_name,
							'sort'				=> $sort,
							'order'				=> $order,
							'page'				=> $page,
							'limit'				=> 30
						);
		$results = UserRoles::getUserRoles($arrParams);
		$results_total = UserRoles::getCountUserRoles($arrParams);

		// Pagination
		$this->data['arrFilters'] = array(
										'filter_role_name'	=> $filter_role_name,
										'sort'				=> $sort,
										'order'				=> $order
									);

		$this->data['user_roles'] = Paginator::make($results, $results_total, 30);
		$this->data['user_roles_count'] = $results_total;

		$this->data['counter'] 	= $this->data['user_roles']->getFrom();

		$this->data['filter_role_name'] = $filter_role_name;

		$this->data['sort'] = $sort;
		$this->data['order'] = $order;
		$this->data['page'] = $page;

		$url = '?filter_role_name=' . $filter_role_name . '&page=' . $page;

		$order_id = ($sort=='id' && $order=='ASC') ? 'DESC' : 'ASC';
		$order_role_name = ($sort=='role_name' && $order=='ASC') ? 'DESC' : 'ASC';

		$this->data['sort_id'] = URL::to('user_roles' . $url . '&sort=id&order=' . $order_id, NULL, FALSE);
		$this->data['sort_role_name'] = URL::to('user_roles' . $url . '&sort=role_name&order=' . $order_role_name, NULL, FALSE);

		// Permissions
		$this->data['permissions'] = unserialize(Session::get('permissions'));

		$this->layout->content = View::make('user_roles.list', $this->data);
	}

	protected function setURL() {
		// Search Filters
		$url = '?filter_role_name=' . Input::get('filter_role_name', NULL);
		$url .= '&sort=' . Input::get('sort', 'role_name');
		$url .= '&order=' . Input::get('order', 'ASC');
		$url .= '&page=' . Input::get('page', 1);

		return $url;
	}
}