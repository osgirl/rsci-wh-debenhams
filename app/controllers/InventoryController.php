<?php

class InventoryController extends BaseController {
	private $data = array();

	protected $layout = "layouts.main";
	
	public function __construct()
    {
    	$this->beforeFilter('csrf', array('on' => 'post'));
    	$this->beforeFilter('auth', array('only' => array('Dashboard')));
    	
    	// Check Permissions
    	if (Session::has('permissions')) {
	    	if (!in_array('CanAccessInventory', unserialize(Session::get('permissions'))))  {
				return Redirect::to('/');
			} 
    	} else {
			return Redirect::to('users/logout');
		}
    }

	public function showIndex() {
		// Check Permissions
    	if (Session::has('permissions')) {
	    	if (!in_array('CanAccessInventory', unserialize(Session::get('permissions'))))  {
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
	    	if (!in_array('CanExportInventory', unserialize(Session::get('permissions'))))  {
				return Redirect::to('inventory');
			} 
    	} else {
			return Redirect::to('users/logout');
		}
		
		$arrParams = array(
							'filter_prod_sku'	=> Input::get('filter_prod_sku', NULL),
							'filter_prod_upc'	=> Input::get('filter_prod_upc', NULL),
							'filter_date_from'	=> Input::get('filter_date_from', NULL),
							'filter_date_to'	=> Input::get('filter_date_to', NULL),
							'filter_slot_no'	=> Input::get('filter_slot_no', NULL),
							'sort'				=> Input::get('sort', 'slot_no'),
							'order'				=> Input::get('order', 'ASC'),
							'page'				=> NULL,
							'limit'				=> NULL
						);		
		$results = Inventory::getInventoryMain($arrParams);

	    $output = Lang::get('inventory.col_slot_no') . ',';
	    $output .= Lang::get('inventory.col_prod_sku') . ',';
		$output .= Lang::get('inventory.col_prod_upc') . ',';
		$output .= Lang::get('inventory.col_prod_short_name') . ',';
		$output .= Lang::get('inventory.col_total_quantity') . ',';
		$output .= Lang::get('inventory.col_earliest_expiry_date') . "\n";
		
	    foreach ($results as $value) {
	    	$exportData = array(
	    						'"' . $value->slot_id . '"', 
	    						'"' . $value->sku . '"', 
	    						'"' . $value->upc . '"', 
	    						'"' . $value->short_description . '"', 
	    						'"' . $value->total_qty . '"', 
	    						'"' . date('M d, Y', strtotime($value->early_expiry)) . '"'
	    					);
	  		
	      	$output .= implode(",", $exportData);
	      	$output .= "\n";
	  	}
	  	
		$headers = array(
			'Content-Type' => 'text/csv',
			'Content-Disposition' => 'attachment; filename="inventory_' . date('Ymd')  . '_' . time() . '.csv"',
		);

		return Response::make(rtrim($output, "\n"), 200, $headers);
	}
	
	public function exportDetailsCSV() {
		// Check Permissions
		if (Session::has('permissions')) {
	    	if (!in_array('CanExportInventoryDetails', unserialize(Session::get('permissions'))))  {
				return Redirect::to('inventory');
			} 
    	} else {
			return Redirect::to('users/logout');
		}
		
		$arrParams = array(
							'slot'		=> Input::get('slot', NULL),
							'sku'		=> Input::get('sku', NULL),
							'sort'		=> Input::get('sort', 'expiry_date'),
							'order'		=> Input::get('order', 'ASC'),
							'page'		=> NULL,
							'limit'		=> NULL
						);		
		$results = Inventory::getInventory($arrParams);

	    $output = Lang::get('inventory.col_slot_no') . ',';
	    $output .= Lang::get('inventory.col_prod_sku') . ',';
		$output .= Lang::get('inventory.col_prod_upc') . ',';
		$output .= Lang::get('inventory.col_prod_short_name') . ',';
		$output .= Lang::get('inventory.col_quantity') . ',';
		$output .= Lang::get('inventory.col_expiry_date') . "\n";
		
	    foreach ($results as $value) {
	    	$exportData = array(
	    						'"' . $value->slot_id . '"', 
	    						'"' . $value->sku . '"', 
	    						'"' . $value->upc . '"', 
	    						'"' . $value->short_description . '"', 
	    						'"' . $value->quantity . '"', 
	    						'"' . date('M d Y', strtotime($value->expiry_date)) . '"'
	    					);
	  		
	      	$output .= implode(",", $exportData);
	      	$output .= "\n";
	  	}
	  	
		$headers = array(
			'Content-Type' => 'text/csv',
			'Content-Disposition' => 'attachment; filename="inventoryDetails_' . $value->slot_id . '_' . date('Ymd')  . '_' . time() . '.csv"',
		);

		return Response::make(rtrim($output, "\n"), 200, $headers);
	}
	
	protected function getList() {
		$this->data['heading_title'] = Lang::get('inventory.heading_title');
		
		$this->data['text_empty_results'] = Lang::get('general.text_empty_results');
		$this->data['text_total'] = Lang::get('general.text_total');
		$this->data['text_view_details'] = Lang::get('inventory.text_view_details');
	
		$this->data['label_filter_prod_sku'] = Lang::get('inventory.label_filter_prod_sku');
		$this->data['label_filter_prod_upc'] = Lang::get('inventory.label_filter_prod_upc');
		$this->data['label_filter_date_from'] = Lang::get('inventory.label_filter_date_from');
		$this->data['label_filter_date_to'] = Lang::get('inventory.label_filter_date_to');
		$this->data['label_filter_slot_no'] = Lang::get('inventory.label_filter_slot_no');
	
		$this->data['col_id'] = Lang::get('inventory.col_id');
		$this->data['col_slot_no'] = Lang::get('inventory.col_slot_no');
		$this->data['col_prod_sku'] = Lang::get('inventory.col_prod_sku');
		$this->data['col_prod_upc'] = Lang::get('inventory.col_prod_upc');
		$this->data['col_prod_short_name'] = Lang::get('inventory.col_prod_short_name');
		$this->data['col_total_quantity'] = Lang::get('inventory.col_total_quantity');
		$this->data['col_earliest_expiry_date'] = Lang::get('inventory.col_earliest_expiry_date');
		
		$this->data['button_search'] = Lang::get('general.button_search');
		$this->data['button_clear'] = Lang::get('general.button_clear');
		$this->data['button_export'] = Lang::get('general.button_export');
		$this->data['button_jda'] = Lang::get('general.button_jda');
		
		// URL
		$this->data['url_export'] = URL::to('inventory/export');
		$this->data['url_details'] = URL::to('inventory/detail' . $this->setURL(true));
				
		// Search Filters
		$filter_prod_sku = Input::get('filter_prod_sku', NULL);
		$filter_prod_upc = Input::get('filter_prod_upc', NULL);
		$filter_date_from = Input::get('filter_date_from', NULL);
		$filter_date_to = Input::get('filter_date_to', NULL);
		$filter_slot_no = Input::get('filter_slot_no', NULL);
		
		$sort = Input::get('sort', 'slot_no');
		$order = Input::get('order', 'ASC');
		$page = Input::get('page', 1);
				
		// Data
		$arrParams = array(
							'filter_prod_sku'	=> $filter_prod_sku,
							'filter_prod_upc'	=> $filter_prod_upc,
							'filter_date_from'	=> $filter_date_from,
							'filter_date_to'	=> $filter_date_to,
							'filter_slot_no'	=> $filter_slot_no,
							'sort'				=> $sort,
							'order'				=> $order,
							'page'				=> $page,
							'limit'				=> 30
						);		
		 $results = Inventory::getInventoryMain($arrParams);
		 $results_total = Inventory::getInventoryMain($arrParams, TRUE);
		//if you want to see slot use this
		//$results = SlotDetails::getSlotDetailsMain($arrParams);
		//$results_total = count(SlotDetails::getCountSlotDetailsMain($arrParams));
		
		// Pagination
		$this->data['arrFilters'] = array(
										'filter_prod_sku'	=> $filter_prod_sku,
										'filter_prod_upc'	=> $filter_prod_upc,
										'filter_date_from'	=> $filter_date_from,
										'filter_date_to'	=> $filter_date_to,
										'filter_slot_no'	=> $filter_slot_no,
										'sort'				=> $sort,
										'order'				=> $order
									);
		
		$this->data['inventory'] = Paginator::make($results, $results_total, 30);
		$this->data['inventory_count'] = $results_total;
		
		$this->data['counter'] 	= $this->data['inventory']->getFrom();
		
		$this->data['filter_prod_sku'] = $filter_prod_sku;
		$this->data['filter_prod_upc'] = $filter_prod_upc;
		$this->data['filter_date_from'] = $filter_date_from;
		$this->data['filter_date_to'] = $filter_date_to;
		$this->data['filter_slot_no'] = $filter_slot_no;
		
		$this->data['sort'] = $sort;
		$this->data['order'] = $order;
		$this->data['page'] = $page;
		
		$url = '?filter_prod_sku=' . $filter_prod_sku . '&filter_prod_upc=' . $filter_prod_upc;
		$url .= '&filter_date_from=' . $filter_date_from . '&filter_date_to=' . $filter_date_to;
		$url .= '&filter_slot_no=' . $filter_slot_no;
		$url .= '&page=' . $page;
		
		$order_id = ($sort=='id' && $order=='ASC') ? 'DESC' : 'ASC';
		$order_slot_no = ($sort=='slot_no' && $order=='ASC') ? 'DESC' : 'ASC';
		$order_sku = ($sort=='sku' && $order=='ASC') ? 'DESC' : 'ASC';
		$order_upc = ($sort=='upc' && $order=='ASC') ? 'DESC' : 'ASC';
		$order_short_name = ($sort=='short_name' && $order=='ASC') ? 'DESC' : 'ASC';
		$order_quantity = ($sort=='quantity' && $order=='ASC') ? 'DESC' : 'ASC';
		$order_expiry_date = ($sort=='created_at' && $order=='ASC') ? 'DESC' : 'ASC';
		
		$this->data['sort_id'] = URL::to('inventory' . $url . '&sort=id&order=' . $order_id, NULL, FALSE);
		$this->data['sort_slot_no'] = URL::to('inventory' . $url . '&sort=slot_no&order=' . $order_slot_no, NULL, FALSE);
		$this->data['sort_sku'] = URL::to('inventory' . $url . '&sort=sku&order=' . $order_sku, NULL, FALSE);
		$this->data['sort_upc'] = URL::to('inventory' . $url . '&sort=upc&order=' . $order_upc, NULL, FALSE);
		$this->data['sort_short_name'] = URL::to('inventory' . $url . '&sort=short_name&order=' . $order_short_name, NULL, FALSE);
		$this->data['sort_quantity'] = URL::to('inventory' . $url . '&sort=quantity&order=' . $order_quantity, NULL, FALSE);
		$this->data['sort_expiry_date'] = URL::to('inventory' . $url . '&sort=created_at&order=' . $order_expiry_date, NULL, FALSE);
		
		// Permissions
		$this->data['permissions'] = unserialize(Session::get('permissions'));
		
		$this->layout->content = View::make('inventory.list', $this->data);
	}
	
	protected function getDetails() {
		$this->data['heading_title_details'] = Lang::get('inventory.heading_title_details');
		
		$this->data['text_empty_results'] = Lang::get('general.text_empty_results');
		$this->data['text_total'] = Lang::get('general.text_total');
	
		$this->data['label_filter_slot_no'] = Lang::get('inventory.label_filter_slot_no');
	
		$this->data['col_id'] = Lang::get('inventory.col_id');
		$this->data['col_prod_sku'] = Lang::get('inventory.col_prod_sku');
		$this->data['col_prod_upc'] = Lang::get('inventory.col_prod_upc');
		$this->data['col_prod_short_name'] = Lang::get('inventory.col_prod_short_name');
		$this->data['col_quantity'] = Lang::get('inventory.col_quantity');
		$this->data['col_expiry_date'] = Lang::get('inventory.col_expiry_date');
		
		$this->data['button_search'] = Lang::get('general.button_search');
		$this->data['button_clear'] = Lang::get('general.button_clear');
		$this->data['button_export'] = Lang::get('general.button_export');
		$this->data['button_jda'] = Lang::get('general.button_jda');
		$this->data['button_back'] = Lang::get('general.button_back');
		
		// URL
		$this->data['url_export'] = URL::to('inventory/export_detail');
		$this->data['url_back'] = URL::to('inventory' . $this->setURL(false, true));
				
		// Search Filters
		$slot = Input::get('slot', NULL);
		$sku = Input::get('sku', NULL);
		
		// $sort = Input::get('sort', 'expiry_date');
		$sort = Input::get('sort', NULL);
		$order = Input::get('order', 'ASC');
		$page = Input::get('page', 1);
				
		// Data
		$arrParams = array(
							'slot'		=> $slot,
							'sku'		=> $sku,
							'sort'		=> $sort,
							'order'		=> $order,
							'page'		=> $page,
							'limit'		=> 30
						);		
		// $results = Inventory::getInventory($arrParams);
		// $results_total = Inventory::getCountInventory($arrParams);
		$results = SlotDetails::getSlotDetails($arrParams);
		$results_total = SlotDetails::getCountSlotDetails($arrParams);
		
		// Pagination
		$this->data['arrFilters'] = array(
										'slot'				=> $slot,
										'sku'				=> $sku,
										'sort'				=> $sort,
										'order'				=> $order,
										'filter_prod_sku'	=> Input::get('filter_prod_sku', NULL),
										'filter_prod_upc'	=> Input::get('filter_prod_upc', NULL),
										'filter_date_from'	=> Input::get('filter_date_from', NULL),
										'filter_date_to'	=> Input::get('filter_date_to', NULL),
										'page_back'			=>  Input::get('page_back', 1),
										'sort_back'			=>  Input::get('sort_back', 1),
										'order_back'			=>  Input::get('order_back', 1)
									);

		$this->data['inventory'] = Paginator::make($results, $results_total, 30);
		$this->data['inventory_count'] = $results_total;
		
		$this->data['counter'] 	= $this->data['inventory']->getFrom();
		
		$this->data['slot'] = $slot;
		$this->data['sku'] = $sku;
		
		$this->data['sort'] = $sort;
		$this->data['order'] = $order;
		$this->data['page'] = $page;
		
		$url = $this->setURL(true). 'slot=' . $slot . '&sku=' . $sku . '&page=' . $page;
		
		$order_id = ($sort=='id' && $order=='ASC') ? 'DESC' : 'ASC';
		$order_sku = ($sort=='sku' && $order=='ASC') ? 'DESC' : 'ASC';
		$order_upc = ($sort=='upc' && $order=='ASC') ? 'DESC' : 'ASC';
		$order_short_name = ($sort=='short_name' && $order=='ASC') ? 'DESC' : 'ASC';
		$order_quantity = ($sort=='quantity' && $order=='ASC') ? 'DESC' : 'ASC';
		$order_expiry_date = ($sort=='expiry_date' && $order=='ASC') ? 'DESC' : 'ASC';
		
		$this->data['sort_id'] = URL::to('inventory/detail' . $url . '&sort=id&order=' . $order_id, NULL, FALSE);
		$this->data['sort_sku'] = URL::to('inventory/detail' . $url . '&sort=sku&order=' . $order_sku, NULL, FALSE);
		$this->data['sort_upc'] = URL::to('inventory/detail' . $url . '&sort=upc&order=' . $order_upc, NULL, FALSE);
		$this->data['sort_short_name'] = URL::to('inventory/detail' . $url . '&sort=short_name&order=' . $order_short_name, NULL, FALSE);
		$this->data['sort_quantity'] = URL::to('inventory/detail' . $url . '&sort=quantity&order=' . $order_quantity, NULL, FALSE);
		$this->data['sort_expiry_date'] = URL::to('inventory/detail' . $url . '&sort=expiry_date&order=' . $order_expiry_date, NULL, FALSE);
		
		// Permissions
		$this->data['permissions'] = unserialize(Session::get('permissions'));
		
		$this->layout->content = View::make('inventory.detail', $this->data);
	}
	
	protected function setURL($forDetail = false, $forBackToList = false) {
		// Search Filters
		$url = '?filter_prod_sku=' . Input::get('filter_prod_sku', NULL);	
		$url .= '&filter_prod_upc=' . Input::get('filter_prod_upc', NULL);
		$url .= '&filter_date_from=' . Input::get('filter_date_from', NULL);
		$url .= '&filter_date_to=' . Input::get('filter_date_to', NULL);
		$url .= '&slot=' . Input::get('slot', NULL);
		if($forDetail) {
			$url .= '&sort_back=' . Input::get('sort', 'sku');
			$url .= '&order_back=' . Input::get('order', 'ASC');
			$url .= '&page_back=' . Input::get('page', 1);
		} else {
			if($forBackToList) {
				$url .= '&sort=' . Input::get('sort_back', 'sku');
				$url .= '&order=' . Input::get('order_back', 'ASC');
				$url .= '&page=' . Input::get('page_back', 1);
			} else {	
				$url .= '&sort=' . Input::get('sort', 'sku');
				$url .= '&order=' . Input::get('order', 'ASC');
				$url .= '&page=' . Input::get('page', 1);
			}
		}
		
		
		return $url;
	}

}