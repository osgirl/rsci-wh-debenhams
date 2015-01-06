<?php

class StoreController extends BaseController {
	private $data = array();

	protected $layout = "layouts.main";
	
	public function __construct()
    {
    	$this->beforeFilter('csrf', array('on' => 'post'));
    	$this->beforeFilter('auth', array('only' => array('Dashboard')));
    	
    	// Check Permissions
    	if (Session::has('permissions')) {
	    	if (!in_array('CanAccessStoreMasterList', unserialize(Session::get('permissions'))))  {
				return Redirect::to('/');
			} 
    	} else {
			return Redirect::to('users/logout');
		}
    }

	public function showIndex() {
		// Check Permissions
		
    	if (Session::has('permissions')) {
	    	if (!in_array('CanAccessSlotMasterList', unserialize(Session::get('permissions'))))  {
				return Redirect::to('/');
			} 
    	} else {
			return Redirect::to('users/logout');
		}
		
		$this->getList();
	}
	
	public function exportCSV() {
		// Check Permissions
		if (Session::has('permissions')) {
	    	if (!in_array('CanExportSlotMasterList', unserialize(Session::get('permissions'))))  {
				return Redirect::to('stores');
			}
    	} else {
			return Redirect::to('users/logout');
		}
		
		$arrParams = array(
							'filter_store_code'	=> Input::get('filter_store_code', NULL),
							'filter_store_name' => Input::get('filter_store_name', NULL),
							'sort'				=> Input::get('sort', 'store_code'),
							'order'				=> Input::get('order', 'ASC'),
							'page'				=> NULL,
							'limit'				=> NULL
						);
		
		$results = Store::getStoreList($arrParams);

		$output = Lang::get('stores.col_store_name') . ",";
		$output .= Lang::get('stores.col_store_code') . ",";
		$output .= Lang::get('stores.col_store_address') . "\n";
		
	    foreach ($results as $value) {
	    	$exportData = array(
	    						'"' . $value->store_name . '"',
	    						'"' . $value->store_code . '"',
	    						'"' . $value->address1.' '.$value->address2.' '.$value->address3 . '"'
	    					);
	  		
	      	$output .= implode(",", $exportData);
	      	$output .= "\n";
	  	}
	  	
		$headers = array(
			'Content-Type' => 'text/csv',
			'Content-Disposition' => 'attachment; filename="storeList_' . date('Ymd')  . '_' . time() . '.csv"',
		);

		return Response::make(rtrim($output, "\n"), 200, $headers);
	}
	
	protected function getList() {
		$this->data['heading_title'] = Lang::get('stores.heading_title');
		
		$this->data['text_empty_results'] = Lang::get('general.text_empty_results');
		$this->data['text_total'] = Lang::get('general.text_total');
	
		$this->data['label_filter_store_code'] = Lang::get('stores.label_filter_store_code');
		$this->data['label_filter_store_name'] = Lang::get('stores.label_filter_store_name');
	
		$this->data['col_id'] = Lang::get('stores.col_id');
		$this->data['col_store_name'] = Lang::get('stores.col_store_name');
		$this->data['col_store_code'] = Lang::get('stores.col_store_code');
		$this->data['col_store_address'] = Lang::get('stores.col_store_address');

		$this->data['button_search'] = Lang::get('general.button_search');
		$this->data['button_clear'] = Lang::get('general.button_clear');
		$this->data['button_export'] = Lang::get('general.button_export');
		$this->data['button_jda'] = Lang::get('general.button_jda');
		
		// URL
		$this->data['url_export'] = URL::to('stores/export');
				
		// Search Filters
		$filter_store_code = Input::get('filter_store_code', NULL);
		$filter_store_name = Input::get('filter_store_name', NULL);

		$sort = Input::get('sort', 'store_code');
		$order = Input::get('order', 'ASC');
		$page = Input::get('page', 1);
		
		// Data
		$arrParams = array(
							'filter_store_code'	=> $filter_store_code,
							'filter_store_name' => $filter_store_name,
							'sort'				=> $sort,
							'order'				=> $order,
							'page'				=> $page,
							'limit'				=> 30
						);		
		$results = Store::getStoreList($arrParams);
		$results_total = Store::getCountstoreLists($arrParams);
		
		// Pagination
		$this->data['arrFilters'] = array(
										'filter_store_code'	=> $filter_store_code,
										'filter_store_name' => $filter_store_name,
										'sort'				=> $sort,
										'order'				=> $order
									);
		
		$this->data['stores'] = Paginator::make($results, $results_total, 30);
		$this->data['stores_count'] = $results_total;
		
		$this->data['counter'] 	= $this->data['stores']->getFrom();
		
		$this->data['filter_store_code'] = $filter_store_code;
		$this->data['filter_store_name'] = $filter_store_name;
		
		$this->data['sort'] = $sort;
		$this->data['order'] = $order;
		$this->data['page'] = $page;
		
		$url = '?filter_store_code=' . $filter_store_code;
		$url .= '&filter_store_name=' . $filter_store_name;
		$url .= '&page=' . $page;
		
		$order_store_code = ($sort=='store_code' && $order=='ASC') ? 'DESC' : 'ASC';
		$order_store_name = ($sort=='store_name' && $order=='ASC') ? 'DESC' : 'ASC';

		$this->data['sort_store_code'] = URL::to('stores' . $url . '&sort=store_code&order=' . $order_store_code, NULL, FALSE);
		$this->data['sort_store_name'] = URL::to('stores' . $url . '&sort=store_name&order=' . $order_store_name, NULL, FALSE);
		
		// Permissions
		$this->data['permissions'] = unserialize(Session::get('permissions'));
		
		$this->layout->content = View::make('stores.list', $this->data);
	}
}