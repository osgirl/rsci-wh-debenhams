<?php

class AuditTrailController extends BaseController {
	private $data = array();

	protected $layout = "layouts.main";
	
	public function __construct()
    {
    	$this->beforeFilter('csrf', array('on' => 'post'));
    	$this->beforeFilter('auth', array('only' => array('Dashboard')));
    	
    	// Check Permissions
    	if (Session::has('permissions')) {
	    	if (!in_array('CanAccessAuditTrail', unserialize(Session::get('permissions'))))  {
				return Redirect::to('/');
			} 
    	} else {
			return Redirect::to('users/logout');
		}
    }

	public function showIndex() {
		// Check Permissions
		if (Session::has('permissions')) {
	    	if (!in_array('CanAccessAuditTrail', unserialize(Session::get('permissions'))))  {
				return Redirect::to('/');
			} 
    	} else {
			return Redirect::to('users/logout');
		}
		
		$this->getList();
	}
	
	public function insertData() {
		$data_before = array('data' => 123);
		$data_after = array('data' => 456);
		
		$arrParams = array(
						'module'		=> 'purchase order',
						'action'		=> 'insert',
						'data_before'	=> json_encode($data_before),
						'data_after'	=> json_encode($data_after),
						'user_id'		=> Auth::user()->id,
						'created_at'	=> date('Y-m-d H:i:s'),
						'updated_at'	=> date('Y-m-d H:i:s')
						);
		AuditTrail::addAuditTrail($arrParams);
				
		$this->layout->content = '';
	}
	
	public function exportCSV() {
		// Check Permissions
		if (Session::has('permissions')) {
	    	if (!in_array('CanExportAuditTrail', unserialize(Session::get('permissions'))))  {
				return Redirect::to('audit_trail');
			} 
    	} else {
			return Redirect::to('users/logout');
		}
		
		$arrParams = array(
							'filter_date_from'	=> Input::get('filter_date_from', NULL),
							'filter_date_to'	=> Input::get('filter_date_to', NULL),
							'filter_module'		=> Input::get('filter_module', NULL),
							'filter_action'		=> Input::get('filter_action', NULL),
							'filter_reference'	=> Input::get('filter_reference', NULL),
							'filter_user'		=> Input::get('filter_user', NULL),
							'sort'				=> Input::get('sort', 'date'),
							'order'				=> Input::get('order', 'DESC'),
							'page'				=> NULL,
							'limit'				=> NULL
						);		
		$results = AuditTrail::getAuditTrails($arrParams);

		$output = Lang::get('audit_trail.col_transaction_date') . ',';
		$output .= Lang::get('audit_trail.col_module') . ',';
		$output .= Lang::get('audit_trail.col_reference') . ',';
		$output .= Lang::get('audit_trail.col_username') . ',';
		$output .= Lang::get('audit_trail.col_action') . ',';
		$output .= Lang::get('audit_trail.col_details') . "\n";
	    
	    foreach ($results as $value) {
	    	$data_after = str_replace('<br />', "\n", $value->data_after);
	    	
	    	$exportData = array(
		    					'"' . $value->created_at . '"', 
		    					'"' . $value->module . '"', 
		    					'"' . $value->reference . '"', 
		    					'"' . $value->username . '"', 
		    					'"' . $value->action . '"', 
		    					'"' . $data_after . '"'
	    					);
	  		
	      	$output .= implode(",", $exportData);
	      	$output .= "\n";
	  	}
	  	
		$headers = array(
			'Content-Type' => 'text/csv',
			'Content-Disposition' => 'attachment; filename="auditTrail_' . date('Ymd')  . '_' . time() . '.csv"',
		);

		return Response::make(rtrim($output, "\n"), 200, $headers);
	}
	
	protected function getList() {
		$this->data['heading_title'] = Lang::get('audit_trail.heading_title');
		
		$this->data['text_empty_results'] = Lang::get('general.text_empty_results');
		$this->data['text_total'] = Lang::get('general.text_total');
	
		$this->data['label_filter_date_from'] = Lang::get('audit_trail.label_filter_date_from');
		$this->data['label_filter_date_to'] = Lang::get('audit_trail.label_filter_date_to');
		$this->data['label_filter_module'] = Lang::get('audit_trail.label_filter_module');
		$this->data['label_filter_action'] = Lang::get('audit_trail.label_filter_action');
		$this->data['label_filter_reference'] = Lang::get('audit_trail.label_filter_reference');
		$this->data['label_filter_user'] = Lang::get('audit_trail.label_filter_user');
	
		$this->data['col_id'] = Lang::get('audit_trail.col_id');
		$this->data['col_transaction_date'] = Lang::get('audit_trail.col_transaction_date');
		$this->data['col_module'] = Lang::get('audit_trail.col_module');
		$this->data['col_reference'] = Lang::get('audit_trail.col_reference');
		$this->data['col_username'] = Lang::get('audit_trail.col_username');
		$this->data['col_action'] = Lang::get('audit_trail.col_action');
		$this->data['col_details'] = Lang::get('audit_trail.col_details');
		
		$this->data['button_search'] = Lang::get('general.button_search');
		$this->data['button_clear'] = Lang::get('general.button_clear');
		$this->data['button_export'] = Lang::get('general.button_export');
		$this->data['button_archive'] = Lang::get('general.button_archive');
		
		// URL
		$this->data['url_export'] = URL::to('audit_trail/export');
		
		// Search Options
		$user_options = array();		
		foreach (User::getUserOptions() as $item) {
			$user_options[$item->id] = $item->username;
		}	
		$this->data['filter_user_options'] = array('' => Lang::get('general.text_select')) + $user_options;
				
		$modules = Config::get('audit_trail_modules');
		unset($modules['product'], $modules['slotmaster']);
		$this->data['filter_module_options'] = array('' => Lang::get('general.text_select')) + $modules;
		// echo '<pre>';dd($this->data['filter_module_options']);
		
		// Search Filters
		$filter_date_from = Input::get('filter_date_from', NULL);
		$filter_date_to = Input::get('filter_date_to', NULL);
		$filter_module = Input::get('filter_module', NULL);
		$filter_action = Input::get('filter_action', NULL);
		$filter_reference = Input::get('filter_reference', NULL);
		$filter_user = Input::get('filter_user', NULL);
		
		$sort = Input::get('sort', 'date');
		$order = Input::get('order', 'DESC');
		$page = Input::get('page', 1);
		
		// Errors
		$this->data['error_date'] = '';
		if (($filter_date_from!='' && $filter_date_to==NULL) || ($filter_date_from==NULL && $filter_date_to!='')) {
			$this->data['error_date'] = Lang::get('audit_trail.error_date');
		}
		
		// Data		
		$arrParams = array(
							'filter_date_from'	=> $filter_date_from,
							'filter_date_to'	=> $filter_date_to,
							'filter_module'		=> $filter_module,
							'filter_action'		=> $filter_action,
							'filter_reference'	=> $filter_reference,
							'filter_user'		=> $filter_user,
							'sort'				=> $sort,
							'order'				=> $order,
							'page'				=> $page,
							'limit'				=> 30
						);		
		$results = AuditTrail::getAuditTrails($arrParams);
		$results_total = AuditTrail::getCountAuditTrails($arrParams);
		
		// Pagination
		$this->data['arrFilters'] = array(
										'filter_date_from'	=> $filter_date_from,
										'filter_date_to'	=> $filter_date_to,
										'filter_module'		=> $filter_module,
										'filter_action'		=> $filter_action,
										'filter_reference'	=> $filter_reference,
										'filter_user'		=> $filter_user,
										'sort'				=> $sort,
										'order'				=> $order
									);
		
		$this->data['audit_trails'] = Paginator::make($results, $results_total, 30);
		$this->data['audit_trails_count'] = $results_total;
		
		$this->data['counter'] 	= $this->data['audit_trails']->getFrom();
		
		$this->data['filter_date_from'] = $filter_date_from;
		$this->data['filter_date_to'] = $filter_date_to;
		$this->data['filter_module'] = $filter_module;
		$this->data['filter_action'] = $filter_action;
		$this->data['filter_reference'] = $filter_reference;
		$this->data['filter_user'] = $filter_user;
		
		$this->data['sort'] = $sort;
		$this->data['order'] = $order;
		$this->data['page'] = $page;
		
		$url = '?filter_date_from=' . $filter_date_from . '&filter_date_to=' . $filter_date_to;
		$url .= '&filter_module=' . $filter_module . '&filter_action=' . $filter_action;
		$url .= '&filter_reference=' . $filter_reference . '&filter_user=' . $filter_user;
		$url .= '&page=' . $page;
		
		$order_audit_id = ($sort=='audit_id' && $order=='ASC') ? 'DESC' : 'ASC';
		$order_date = ($sort=='date' && $order=='ASC') ? 'DESC' : 'ASC';
		$order_module = ($sort=='module' && $order=='ASC') ? 'DESC' : 'ASC';
		$order_reference = ($sort=='reference' && $order=='ASC') ? 'DESC' : 'ASC';
		$order_username = ($sort=='username' && $order=='ASC') ? 'DESC' : 'ASC';
		$order_action = ($sort=='action' && $order=='ASC') ? 'DESC' : 'ASC';
		$order_details = ($sort=='details' && $order=='ASC') ? 'DESC' : 'ASC';
		
		$this->data['sort_audit_id'] = URL::to('audit_trail' . $url . '&sort=audit_id&order=' . $order_audit_id, NULL, FALSE);
		$this->data['sort_date'] = URL::to('audit_trail' . $url . '&sort=date&order=' . $order_date, NULL, FALSE);
		$this->data['sort_module'] = URL::to('audit_trail' . $url . '&sort=module&order=' . $order_module, NULL, FALSE);
		$this->data['sort_reference'] = URL::to('audit_trail' . $url . '&sort=reference&order=' . $order_reference, NULL, FALSE);
		$this->data['sort_username'] = URL::to('audit_trail' . $url . '&sort=username&order=' . $order_username, NULL, FALSE);
		$this->data['sort_action'] = URL::to('audit_trail' . $url . '&sort=action&order=' . $order_action, NULL, FALSE);
		$this->data['sort_details'] = URL::to('audit_trail' . $url . '&sort=details&order=' . $order_details, NULL, FALSE);
		
		// Permissions
		$this->data['permissions'] = unserialize(Session::get('permissions'));
		
		$this->layout->content = View::make('audit_trail.list', $this->data);
	}
	
	protected function moduleOptions() {
		return array(
					'' 					=> 'Please Select',
					'Purchase Order' 	=> 'Purchase Order',
					'JDA Sync' 			=> 'JDA Sync',
					'Mobile Sync' 		=> 'Mobile Sync',
					'Users' 			=> 'Users',
					'User Roles' 		=> 'User Roles'
				);
	}
}