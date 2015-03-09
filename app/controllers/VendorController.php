<?php

class VendorController extends BaseController {
	private $data = array();

	protected $layout = "layouts.main";

	public function __construct()
    {
    	date_default_timezone_set('Asia/Manila');
    	$this->beforeFilter('csrf', array('on' => 'post'));
    	$this->beforeFilter('auth', array('only' => array('Dashboard')));

    	// Check Permissions
    	if (Session::has('permissions')) {
	    	if (!in_array('CanAccessVendorMasterList', unserialize(Session::get('permissions'))))  {
				return Redirect::to('/');
			}
    	} else {
			return Redirect::to('users/logout');
		}
    }

	public function showIndex() {
		// Check Permissions
    	if (Session::has('permissions')) {
	    	if (!in_array('CanAccessVendorMasterList', unserialize(Session::get('permissions'))))  {
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
	    	if (!in_array('CanExportVendorMasterList', unserialize(Session::get('permissions'))))  {
				return Redirect::to('vendors' . $this->setURL());
			}
    	} else {
			return Redirect::to('users/logout');
		}
		$this->data = Lang::get('vendors');
		$this->data['text_empty_results'] = Lang::get('general.text_empty_results');
		$arrParams = array(
							'filter_vendor_no'	=> Input::get('filter_vendor_no', NULL),
							'filter_vendor_name'=> Input::get('filter_vendor_name', NULL),
							'sort'				=> Input::get('sort', 'vendor_code'),
							'order'				=> Input::get('order', 'ASC'),
							'page'				=> NULL,
							'limit'				=> NULL
						);

		$results = Vendors::getVendorLists($arrParams);

		$this->data['results'] = $results;

		$pdf = App::make('dompdf');
		$pdf->loadView('vendors.report_list', $this->data)->setPaper('a4')->setOrientation('landscape');
		// return $pdf->stream();
		return $pdf->download('vendors_' . date('Ymd') . '.pdf');
	}

	protected function getList() {
		$this->data['heading_title'] = Lang::get('vendors.heading_title');

		$this->data['text_empty_results'] = Lang::get('general.text_empty_results');
		$this->data['text_total'] = Lang::get('general.text_total');

		$this->data['label_filter_vendor_no'] = Lang::get('vendors.label_filter_vendor_no');
		$this->data['label_filter_vendor_name'] = Lang::get('vendors.label_filter_vendor_name');

		$this->data['col_id'] = Lang::get('vendors.col_id');
		$this->data['col_vendor_name'] = Lang::get('vendors.col_vendor_name');
		$this->data['col_vendor_no'] = Lang::get('vendors.col_vendor_no');

		$this->data['button_search'] = Lang::get('general.button_search');
		$this->data['button_clear'] = Lang::get('general.button_clear');
		$this->data['button_export'] = Lang::get('general.button_export');
		$this->data['button_jda'] = Lang::get('general.button_jda');

		// URL
		$this->data['url_export'] = URL::to('vendors/export');

		// Search Filters
		$filter_vendor_no = Input::get('filter_vendor_no', NULL);
		$filter_vendor_name = Input::get('filter_vendor_name', NULL);

		$sort = Input::get('sort', 'vendor_code');
		$order = Input::get('order', 'ASC');
		$page = Input::get('page', 1);

		// Data
		$arrParams = array(
							'filter_vendor_no'	=> $filter_vendor_no,
							'filter_vendor_name'=> $filter_vendor_name,
							'sort'				=> $sort,
							'order'				=> $order,
							'page'				=> $page,
							'limit'				=> 30
						);
		$results = Vendors::getVendorLists($arrParams);
		$results_total = Vendors::getVendorLists($arrParams, true);

		// Pagination
		$this->data['arrFilters'] = array(
										'filter_vendor_no'	=> $filter_vendor_no,
										'filter_vendor_name'=> $filter_vendor_name,
										'sort'				=> $sort,
										'order'				=> $order
									);

		$this->data['vendors'] = Paginator::make($results, $results_total, 30);
		$this->data['vendors_count'] = $results_total;

		$this->data['counter'] 	= $this->data['vendors']->getFrom();

		$this->data['filter_vendor_no'] = $filter_vendor_no;
		$this->data['filter_vendor_name'] = $filter_vendor_name;


		$this->data['sort'] = $sort;
		$this->data['order'] = $order;
		$this->data['page'] = $page;

		$url = '?filter_vendor_no=' . $filter_vendor_no;
		$url .= '&filter_vendor_name=' . $filter_vendor_name;
		$url .= '&page=' . $page;

		$order_id = ($sort=='id' && $order=='ASC') ? 'DESC' : 'ASC';
		$order_vendor_no = ($sort=='vendor_code' && $order=='ASC') ? 'DESC' : 'ASC';
		$order_vendor_name = ($sort=='vendor_name' && $order=='ASC') ? 'DESC' : 'ASC';

		$this->data['sort_id'] = URL::to('vendors' . $url . '&sort=id&order=' . $order_id, NULL, FALSE);
		$this->data['sort_vendor_no'] = URL::to('vendors' . $url . '&sort=vendor_code&order=' . $order_vendor_no, NULL, FALSE);
		$this->data['sort_vendor_name'] = URL::to('vendors' . $url . '&sort=vendor_name&order=' . $order_vendor_name, NULL, FALSE);

		// Permissions
		$this->data['permissions'] = unserialize(Session::get('permissions'));

		$this->layout->content = View::make('vendors.list', $this->data);
	}
}