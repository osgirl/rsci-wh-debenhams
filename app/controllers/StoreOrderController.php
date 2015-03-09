<?php

class StoreOrderController extends BaseController {
	private $data = array();
	protected $layout = "layouts.main";

	public function __construct() {
    	date_default_timezone_set('Asia/Manila');
		$this->beforeFilter('csrf', array('on' => 'post'));
		$this->beforeFilter('auth', array('only'=> array('Dashboard')));
		$this->apiUrl = Config::get('constant.api_url');
	}

	public function showIndex() {
		// Check Permissions
		if (Session::has('permissions')) {
	    	if (!in_array('CanAccessStoreOrders', unserialize(Session::get('permissions'))))  {
				return Redirect::to('user/profile');
			}
    	} else {
			return Redirect::to('users/logout');
		}

		$this->getList();
	}


	public function exportCSV() {
		// Check Permissions
		if (Session::has('permissions')) {
	    	if (!in_array('CanExportStoreOrders', unserialize(Session::get('permissions'))))  {
				return Redirect::to('store_order' . $this->setURL());
			}
    	} else {
			return Redirect::to('users/logout');
		}
		$this->data = Lang::get('store_order');
		$this->data['so_status_type'] = Dataset::getTypeWithValue("SO_STATUS_TYPE");
		$this->data['text_empty_results'] = Lang::get('general.text_empty_results');
		$arrParams = array(
							'filter_so_no' 			=> Input::get('filter_so_no', NULL),
							'filter_store' 			=> Input::get('filter_store', NULL),
							'filter_order_date' 	=> Input::get('filter_order_date',NULL),
							'filter_status' 		=> Input::get('filter_status', NULL),
							'sort'					=> Input::get('sort', 'so_no'),
							'order'					=> Input::get('order', 'ASC'),
							'page'					=> NULL,
							'limit'					=> NULL
						);

		$results = StoreOrder::getSOList($arrParams);

		$this->data['results'] = $results;

		$pdf = App::make('dompdf');
		$pdf->loadView('store_order.report_list', $this->data)->setPaper('a4')->setOrientation('landscape');
		// return $pdf->stream();
		return $pdf->download('store_order_' . date('Ymd') . '.pdf');
	}

	public function exportDetailsCSV() {
		///Check Permissions
		if (Session::has('permissions')) {
	    	if (!in_array('CanExportStoreOrders', unserialize(Session::get('permissions'))))  {
				return Redirect::to('store_order' . $this->setURL());
			}
    	} else {
			return Redirect::to('users/logout');
		}

		if (StoreOrder::find(Input::get('id', NULL))!=NULL) {
			$so_id = Input::get('id', NULL);
			$this->data = Lang::get('store_order');
			$this->data['so_status_type'] = Dataset::getTypeWithValue("SO_STATUS_TYPE");
			$this->data['text_empty_results'] = Lang::get('general.text_empty_results');
			$arrParams = array(
							'sort'		=> Input::get('sort_detail', 'sku'),
							'order'		=> Input::get('order_detail', 'ASC'),
							'page'		=> NULL,
							'limit'		=> NULL
						);

			$so_info = StoreOrder::getSOInfo($so_id);
			$results = StoreOrderDetail::getSODetails($so_info->so_no, $arrParams);
			$this->data['results'] = $results;

			$pdf = App::make('dompdf');
			$pdf->loadView('store_order.report_detail', $this->data)->setPaper('a4')->setOrientation('landscape');
			// return $pdf->stream();
			return $pdf->download('store_order_detail_' . date('Ymd') . '.pdf');
		}
	}

	public function exportMTSCSV() {
		///Check Permissions
		if (Session::has('permissions')) {
	    	if (!in_array('CanExportStoreOrders', unserialize(Session::get('permissions'))))  {
				return Redirect::to('store_order' . $this->setURL());
			}
    	} else {
			return Redirect::to('users/logout');
		}

		if (StoreOrder::find(Input::get('id', NULL))!=NULL) {
			$so_id = Input::get('id', NULL);
			$this->data = Lang::get('store_order');
			$this->data['so_status_type'] = Dataset::getTypeWithValue("SO_STATUS_TYPE");
			$this->data['text_empty_results'] = Lang::get('general.text_empty_results');
			$arrParams = array(
							'sort'		=> Input::get('sort_detail', 'sku'),
							'order'		=> Input::get('order_detail', 'ASC'),
							'page'		=> NULL,
							'limit'		=> NULL
						);

			$so_info = StoreOrder::getSOInfo($so_id);
			$results = StoreOrderDetail::getMtsDetails($so_info->so_no, $arrParams);
			$this->data['results'] = $results;

			$pdf = App::make('dompdf');
			$pdf->loadView('store_order.report_mts', $this->data)->setPaper('a4')->setOrientation('landscape');
			// return $pdf->stream();
			return $pdf->download('store_order_mts_' . date('Ymd') . '.pdf');
		}
	}

	public function getSODetails() {
		// Check Permissions
		if (Session::has('permissions')) {
	    	if (!in_array('CanAccessStoreOrders', unserialize(Session::get('permissions'))))  {
				return Redirect::to('store_order');
			} elseif (StoreOrder::find(Input::get('id', NULL))==NULL) {
				return Redirect::to('store_order')->with('error', Lang::get('store_order.error_so_details'));
			}
    	} else {
			return Redirect::to('users/logout');
		}

		$this->data['heading_title_so_details'] = Lang::get('store_order.heading_title_so_details');
		$this->data['heading_title_so_contents'] = Lang::get('store_order.heading_title_so_contents');

		$this->data['text_empty_results'] = Lang::get('general.text_empty_results');
		$this->data['text_total'] = Lang::get('general.text_total');
		$this->data['text_select'] = Lang::get('general.text_select');
		$this->data['text_print_manifest'] = Lang::get('store_order.text_print_manifest');
		$this->data['text_closed_so'] = Lang::get('store_order.text_closed_so');
		$this->data['text_warning'] = Lang::get('store_order.text_warning');

		$this->data['label_store_order_no'] = Lang::get('store_order.label_store_order_no');
		$this->data['label_store'] = Lang::get('store_order.label_store');
		$this->data['label_order_date'] = Lang::get('store_order.label_order_date');
		$this->data['label_dispatch_date'] = Lang::get('store_order.label_dispatch_date');
		$this->data['label_status'] = Lang::get('store_order.label_status');
		$this->data['label_app_sync'] = Lang::get('store_order.label_app_sync');

		$this->data['col_id'] = Lang::get('store_order.col_id');
		$this->data['col_upc'] = Lang::get('store_order.col_upc');
		$this->data['col_short_name'] = Lang::get('store_order.col_short_name');
		$this->data['col_ordered_quantity'] = Lang::get('store_order.col_ordered_quantity');
		$this->data['col_delivered_quantity'] = Lang::get('store_order.col_delivered_quantity');
		$this->data['col_picked_quantity'] = Lang::get('store_order.col_picked_quantity');
		$this->data['col_packed_quantity'] = Lang::get('store_order.col_packed_quantity');
		$this->data['col_slot_no'] = Lang::get('store_order.col_slot_no');
		$this->data['col_expiry_date'] = Lang::get('store_order.col_expiry_date');
		$this->data['col_moved_to_picking'] = Lang::get('store_order.col_moved_to_picking');

		$this->data['button_back'] = Lang::get('general.button_back');
		$this->data['button_export'] = Lang::get('general.button_export');
		$this->data['button_print_manifest'] = Lang::get('store_order.button_print_manifest');

		// URL
		$this->data['url_export'] = URL::to('store_order/export_detail');
		$this->data['url_back'] = URL::to('store_order' . $this->setURL());

		// Message
		$this->data['error'] = '';
		if (Session::has('error')) {
			$this->data['error'] = Session::get('error');
		}

		$this->data['success'] = '';
		if (Session::has('success')) {
			$this->data['success'] = Session::get('success');
		}

		// Search Options
		// Search Options
		$this->data['so_status_type'] = Dataset::getTypeWithValue("SO_STATUS_TYPE");
		$store_list 	  			  = StoreOrder::getStoreList();
		if(CommonHelper::arrayHasValue($store_list)) {
			foreach($store_list as $store){
				$this->data['store_list'][$store] = $store;
			}
		}
		else {
			$this->data['store_list'][] = NULL;
		}

		// Search Filters
		$filter_so_no = Input::get('filter_so_no', NULL);
		$filter_store = Input::get('filter_store', NULL);
		$filter_order_date = Input::get('filter_order_date', NULL);
		$filter_status = Input::get('filter_status', NULL);

		$sort = Input::get('sort', 'so_no');
		$order = Input::get('order', 'ASC');
		$page = Input::get('page', 1);

		// Details
		$sort_detail = Input::get('sort_detail', 'sku');
		$order_detail = Input::get('order_detail', 'ASC');
		$page_detail = Input::get('page_detail', 1);

		//Data
		$so_id = Input::get('id', NULL);
		$so_no = Input::get('so_no', NULL);

		$this->data['so_info'] = StoreOrder::getSOInfo($so_id);

		// echo '<pre>'; print_r($this->data['so_info']); exit;
		$arrParams = array(
						'sort'		=> $sort_detail,
						'order'		=> $order_detail,
						'page'		=> $page_detail,
						'limit'		=> 30
					);

		$results 		= StoreOrderDetail::getSODetails($so_no, $arrParams);
		// echo '<pre>'; dd($results);
		$results_total 	= StoreOrderDetail::getCountSODetails($so_no, $arrParams);

		// Pagination
		$this->data['arrFilters'] = array(
									'sort'		=> $sort_detail,
									'order'		=> $order_detail
								);

		$this->data['store_orders'] = Paginator::make($results, $results_total, 30);
		$this->data['store_orders_count'] = $results_total;

		$this->data['counter'] 	= $this->data['store_orders']->getFrom();

		// Main
		$this->data['filter_so_no'] = $filter_so_no;
		$this->data['filter_store'] = $filter_store;
		$this->data['filter_order_date'] = $filter_order_date;
		$this->data['filter_status'] = $filter_status;

		$this->data['sort'] = $sort;
		$this->data['order'] = $order;
		$this->data['page'] = $page;

		// Details
		$this->data['sort_detail'] = $sort_detail;
		$this->data['order_detail'] = $order_detail;
		$this->data['page_detail'] = $page_detail;

		$url = '?filter_so_no=' . $filter_so_no . '&filter_store=' . $filter_store;
		$url .= '&filter_order_date=' . $filter_order_date;
		$url .= '&filter_status=' . $filter_status;
		$url .= '&sort=' . $sort . '&order=' . $order . '&page=' . $page;
		$url .= '&page_detail=' . $page_detail . '&id=' . $so_id . '&so_no=' . $so_no;


		$order_sku = ($sort_detail=='sku' && $order_detail=='ASC') ? 'DESC' : 'ASC';
		$order_short_name = ($sort_detail=='short_name' && $order_detail=='ASC') ? 'DESC' : 'ASC';
		$order_ordered_quantity = ($sort_detail=='ordered_quantity' && $order_detail=='ASC') ? 'DESC' : 'ASC';
		$order_allocated_quantity = ($sort_detail=='allocated_quantity' && $order_detail=='ASC') ? 'DESC' : 'ASC';
		$order_dispatched_quantity = ($sort_detail=='dispatched_quantity' && $order_detail=='ASC') ? 'DESC' : 'ASC';

		$this->data['sort_sku'] = URL::to('store_order/detail' . $url . '&sort_detail=sku&order_detail=' . $order_sku, NULL, FALSE);
		$this->data['sort_short_name'] = URL::to('store_order/detail' . $url . '&sort_detail=short_name&order_detail=' . $order_short_name, NULL, FALSE);
		$this->data['sort_ordered_quantity'] = URL::to('store_order/detail' . $url . '&sort_detail=ordered_quantity&order_detail=' . $order_ordered_quantity, NULL, FALSE);
		$this->data['sort_allocated_quantity'] = URL::to('store_order/detail' . $url . '&sort_detail=allocated_quantity&order_detail=' . $order_allocated_quantity, NULL, FALSE);
		$this->data['sort_dispatched_quantity'] = URL::to('store_order/detail' . $url . '&sort_detail=dispatched_quantity&order_detail=' . $order_dispatched_quantity, NULL, FALSE);

		// Permissions
		$this->data['permissions'] = unserialize(Session::get('permissions'));

		$this->layout->content = View::make('store_order.detail', $this->data);
	}

	public function getMtsDetails() {
		// Check Permissions
		if (Session::has('permissions')) {
	    	if (!in_array('CanAccessStoreOrders', unserialize(Session::get('permissions'))))  {
				return Redirect::to('store_order');
			} elseif (StoreOrder::find(Input::get('id', NULL))==NULL) {
				return Redirect::to('store_order')->with('error', Lang::get('store_order.error_so_details'));
			}
    	} else {
			return Redirect::to('users/logout');
		}

		$this->data = Lang::get('store_order');
		$this->data['text_empty_results'] = Lang::get('general.text_empty_results');
		$this->data['text_total'] = Lang::get('general.text_total');
		$this->data['text_select'] = Lang::get('general.text_select');

		$this->data['button_back'] = Lang::get('general.button_back');
		$this->data['button_export'] = Lang::get('general.button_export');

		// URL
		$this->data['url_mts_export'] = URL::to('store_order/export_mts');
		$this->data['url_back'] = URL::to('store_order' . $this->setURL());

		// Message
		$this->data['error'] = '';
		if (Session::has('error')) {
			$this->data['error'] = Session::get('error');
		}

		$this->data['success'] = '';
		if (Session::has('success')) {
			$this->data['success'] = Session::get('success');
		}

		// Search Options
		// Search Options
		$this->data['so_status_type'] = Dataset::getTypeWithValue("SO_STATUS_TYPE");
		$store_list 	  			  = StoreOrder::getStoreList();
		if(CommonHelper::arrayHasValue($store_list)) {
			foreach($store_list as $store){
				$this->data['store_list'][$store] = $store;
			}
		}
		else {
			$this->data['store_list'][] = NULL;
		}

		// Search Filters
		$filter_so_no = Input::get('filter_so_no', NULL);
		$filter_store = Input::get('filter_store', NULL);
		$filter_order_date = Input::get('filter_order_date', NULL);
		$filter_status = Input::get('filter_status', NULL);

		$sort = Input::get('sort', 'so_no');
		$order = Input::get('order', 'ASC');
		$page = Input::get('page', 1);

		// Details
		$sort_detail = Input::get('sort_detail', 'sku');
		$order_detail = Input::get('order_detail', 'ASC');
		$page_detail = Input::get('page_detail', 1);

		//Data
		$so_id = Input::get('id', NULL);
		$so_no = Input::get('so_no', NULL);

		$this->data['so_info'] = StoreOrder::getSOInfo($so_id);

		// echo '<pre>'; print_r($this->data['so_info']); exit;
		$arrParams = array(
						'sort'		=> $sort_detail,
						'order'		=> $order_detail,
						'page'		=> $page_detail,
						'limit'		=> 30
					);

		$results 		= StoreOrderDetail::getMtsDetails($so_no, $arrParams);
		// echo '<pre>'; dd($results);
		$results_total 	= StoreOrderDetail::getMtsDetails($so_no, $arrParams, TRUE);

		// Pagination
		$this->data['arrFilters'] = array(
									'sort'		=> $sort_detail,
									'order'		=> $order_detail
								);

		$this->data['store_orders'] = Paginator::make($results, $results_total, 30);
		$this->data['store_orders_count'] = $results_total;

		$this->data['counter'] 	= $this->data['store_orders']->getFrom();

		// Main
		$this->data['filter_so_no'] = $filter_so_no;
		$this->data['filter_store'] = $filter_store;
		$this->data['filter_order_date'] = $filter_order_date;
		$this->data['filter_status'] = $filter_status;

		$this->data['sort'] = $sort;
		$this->data['order'] = $order;
		$this->data['page'] = $page;

		// Details
		$this->data['sort_detail'] = $sort_detail;
		$this->data['order_detail'] = $order_detail;
		$this->data['page_detail'] = $page_detail;

		$url = '?filter_so_no=' . $filter_so_no . '&filter_store=' . $filter_store;
		$url .= '&filter_order_date=' . $filter_order_date;
		$url .= '&filter_status=' . $filter_status;
		$url .= '&sort=' . $sort . '&order=' . $order . '&page=' . $page;
		$url .= '&page_detail=' . $page_detail . '&id=' . $so_id . '&so_no=' . $so_no;


		$order_sku = ($sort_detail=='sku' && $order_detail=='ASC') ? 'DESC' : 'ASC';
		$order_short_name = ($sort_detail=='short_name' && $order_detail=='ASC') ? 'DESC' : 'ASC';
		$order_ordered_quantity = ($sort_detail=='ordered_quantity' && $order_detail=='ASC') ? 'DESC' : 'ASC';
		$order_allocated_quantity = ($sort_detail=='allocated_quantity' && $order_detail=='ASC') ? 'DESC' : 'ASC';
		$order_dispatched_quantity = ($sort_detail=='dispatched_quantity' && $order_detail=='ASC') ? 'DESC' : 'ASC';

		$this->data['sort_sku'] = URL::to('store_order/mts_detail' . $url . '&sort_detail=sku&order_detail=' . $order_sku, NULL, FALSE);
		$this->data['sort_short_name'] = URL::to('store_order/mts_detail' . $url . '&sort_detail=short_name&order_detail=' . $order_short_name, NULL, FALSE);
		$this->data['sort_ordered_quantity'] = URL::to('store_order/mts_detail' . $url . '&sort_detail=ordered_quantity&order_detail=' . $order_ordered_quantity, NULL, FALSE);
		$this->data['sort_allocated_quantity'] = URL::to('store_order/mts_detail' . $url . '&sort_detail=allocated_quantity&order_detail=' . $order_allocated_quantity, NULL, FALSE);
		$this->data['sort_dispatched_quantity'] = URL::to('store_order/mts_detail' . $url . '&sort_detail=dispatched_quantity&order_detail=' . $order_dispatched_quantity, NULL, FALSE);

		// Permissions
		$this->data['permissions'] = unserialize(Session::get('permissions'));

		$this->layout->content = View::make('store_order.mts_detail', $this->data);
	}

	protected function getList() {
		$this->data['heading_title'] = Lang::get('store_order.heading_title');

		$this->data['text_empty_results'] = Lang::get('general.text_empty_results');
		$this->data['text_total'] = Lang::get('general.text_total');
		$this->data['text_select'] = Lang::get('general.text_select');
		$this->data['text_closed_so'] = Lang::get('store_order.text_closed_so');
		$this->data['text_warning'] = Lang::get('store_order.text_warning');
		$this->data['text_confirm_assign'] = Lang::get('store_order.text_confirm_assign');

		$this->data['label_store_order_no'] = Lang::get('store_order.label_store_order_no');
		$this->data['label_store'] = Lang::get('store_order.label_store');
		$this->data['label_order_date'] = Lang::get('store_order.label_order_date');
		$this->data['label_status'] = Lang::get('store_order.label_status');

		$this->data['col_id'] = Lang::get('store_order.col_id');
		$this->data['col_so_no'] = Lang::get('store_order.col_so_no');
		$this->data['col_store'] = Lang::get('store_order.col_store');
		$this->data['col_store_name'] = Lang::get('store_order.col_store_name');
		$this->data['col_order_date'] = Lang::get('store_order.col_order_date');
		$this->data['col_status'] = Lang::get('store_order.col_status');
		$this->data['col_load_code'] = Lang::get('store_order.col_load_code');
		$this->data['col_action'] = Lang::get('store_order.col_action');

		$this->data['button_search'] = Lang::get('general.button_search');
		$this->data['button_clear'] = Lang::get('general.button_clear');
		$this->data['button_export'] = Lang::get('general.button_export');
		$this->data['link_view_mts'] = Lang::get('general.link_view_mts');

		// URL
		$this->data['url_export'] = URL::to('store_order/export');
		$this->data['url_detail'] = URL::to('store_order/detail' . $this->setURL());
		$this->data['url_mts_detail'] = URL::to('store_order/mts_detail' . $this->setURL());

		// Message
		$this->data['error'] = '';
		if (Session::has('error')) {
			$this->data['error'] = Session::get('error');
		}

		$this->data['success'] = '';
		if (Session::has('success')) {
			$this->data['success'] = Session::get('success');
		}

		// Search Options
		$this->data['so_status_type'] = Dataset::getTypeWithValue("SO_STATUS_TYPE");
		$store_list 	  			  = StoreOrder::getStoreList();

		if(CommonHelper::arrayHasValue($store_list)) {
			foreach($store_list as $store){
				$this->data['store_list'][$store] = $store;
			}
		}
		else {
			$this->data['store_list'][] = NULL;
		}
		// Search Filters
		$filter_so_no = Input::get('filter_so_no', NULL);
		$filter_store = Input::get('filter_store', NULL);
		$filter_order_date = Input::get('filter_order_date', NULL);
		$filter_status = Input::get('filter_status', NULL);

		$sort = Input::get('sort', 'so_no');
		$order = Input::get('order', 'ASC');
		$page = Input::get('page', 1);

		//Data
		$arrParams = array(
						'filter_so_no' 			=> $filter_so_no,
						'filter_store' 			=> $filter_store,
						'filter_order_date' 	=> $filter_order_date,
						'filter_status' 		=> $filter_status,
						'sort'					=> $sort,
						'order'					=> $order,
						'page'					=> $page,
						'limit'					=> 30
					);

		$results 		= StoreOrder::getSOList($arrParams);
		// echo '<pre>'; dd($results);
		$results_total 	= StoreOrder::getCount($arrParams);

		// Pagination
		$this->data['arrFilters'] = array(
									'filter_so_no' 			=> $filter_so_no,
									'filter_store' 			=> $filter_store,
									'filter_order_date' 	=> $filter_order_date,
									'filter_status' 		=> $filter_status,
									'sort'					=> $sort,
									'order'					=> $order
								);

		$this->data['store_orders'] = Paginator::make($results, $results_total, 30);
		$this->data['store_orders_count'] = $results_total;

		$this->data['counter'] 	= $this->data['store_orders']->getFrom();

		$this->data['filter_so_no'] = $filter_so_no;
		$this->data['filter_store'] = $filter_store;
		$this->data['filter_order_date'] = $filter_order_date;
		$this->data['filter_status'] = $filter_status;

		$this->data['sort'] = $sort;
		$this->data['order'] = $order;
		$this->data['page'] = $page;

		$url = '?filter_so_no=' . $filter_so_no . '&filter_store=' . $filter_store;
		$url .= '&filter_order_date=' . $filter_order_date;
		$url .= '&filter_status=' . $filter_status;
		$url .= '&page=' . $page;

		$order_so_no = ($sort=='so_no' && $order=='ASC') ? 'DESC' : 'ASC';
		$order_store = ($sort=='store' && $order=='ASC') ? 'DESC' : 'ASC';
		$order_order_date = ($sort=='order_date' && $order=='ASC') ? 'DESC' : 'ASC';

		$this->data['sort_so_no'] = URL::to('store_order' . $url . '&sort=so_no&order=' . $order_so_no, NULL, FALSE);
		$this->data['sort_store'] = URL::to('store_order' . $url . '&sort=store&order=' . $order_store, NULL, FALSE);
		$this->data['sort_order_date'] = URL::to('store_order' . $url . '&sort=order_date&order=' . $order_order_date, NULL, FALSE);

		// Permissions
		$this->data['permissions'] = unserialize(Session::get('permissions'));

		$this->layout->content = View::make('store_order.list', $this->data);
	}

	protected function setURL() {
		// Search Filters
		$url = '?filter_so_no=' . Input::get('filter_so_no', NULL);
		$url .= '&filter_store=' . Input::get('filter_store', NULL);
		$url .= '&filter_order_date=' . Input::get('filter_order_date', NULL);
		$url .= '&filter_status=' . Input::get('filter_status', NULL);
		$url .= '&sort=' . Input::get('sort', 'so_no');
		$url .= '&order=' . Input::get('order', 'ASC');
		$url .= '&page=' . Input::get('page', 1);

		return $url;
	}

}
