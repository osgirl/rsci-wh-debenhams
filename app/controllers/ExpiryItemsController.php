<?php

class ExpiryItemsController extends BaseController {
	private $data = array();

	protected $layout = "layouts.main";

	public function __construct()
    {
    	date_default_timezone_set('Asia/Manila');
    	$this->beforeFilter('csrf', array('on' => 'post'));
    	$this->beforeFilter('auth', array('only' => array('Dashboard')));

    	// Check Permissions
    	if (Session::has('permissions')) {
	    	if (!in_array('CanAccessExpiryItems', unserialize(Session::get('permissions'))))  {
				return Redirect::to('/');
			}
    	} else {
			return Redirect::to('users/logout');
		}
    }

	public function showIndex() {
		// Check Permissions
    	if (Session::has('permissions')) {
	    	if (!in_array('CanAccessExpiryItems', unserialize(Session::get('permissions'))))  {
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
	    	if (!in_array('CanExportExpiryItems', unserialize(Session::get('permissions'))))  {
				return Redirect::to('slots');
			}
    	} else {
			return Redirect::to('users/logout');
		}
		$this->data = Lang::get('expiry_items');
		$this->data['text_empty_results'] = Lang::get('general.text_empty_results');

		$arrParams = array(
							'filter_shipment_reference_no'	=> Input::get('filter_shipment_reference_no', NULL),
							'filter_po_no'	=> Input::get('filter_po_no', NULL),
							'sort'				=> Input::get('sort', 'sku'),
							'order'				=> Input::get('order', 'ASC'),
							'page'				=> NULL,
							'limit'				=> NULL
						);
		$results = PurchaseOrderDetail::getPODetailsWithExpiration($arrParams);
		$this->data['results'] = $results;

		$pdf = App::make('dompdf');
		$pdf->loadView('expiry_items.report_list', $this->data)->setPaper('a4')->setOrientation('landscape');
		// return $pdf->stream();
		return $pdf->download('expiry_items_' . date('Ymd') . '.pdf');
	}

	protected function getList() {
		$this->data = Lang::get('expiry_items');

		$this->data['text_empty_results'] = Lang::get('general.text_empty_results');
		$this->data['text_total'] = Lang::get('general.text_total');

		$this->data['button_search'] = Lang::get('general.button_search');
		$this->data['button_clear'] = Lang::get('general.button_clear');
		$this->data['button_export'] = Lang::get('general.button_export');
		$this->data['button_jda'] = Lang::get('general.button_jda');

		// URL
		$this->data['url_export'] = URL::to('expiry_items/export' . $this->setURL());

		// Search Filters
		$filter_po_no = Input::get('filter_po_no', NULL);
		$filter_shipment_reference_no = Input::get('filter_shipment_reference_no', NULL);

		$sort = Input::get('sort', 'purchase_order_lists.receiver_no');
		$order = Input::get('order', 'ASC');
		$page = Input::get('page', 1);

		// Data
		$arrParams = array(
							'filter_shipment_reference_no'	=> $filter_shipment_reference_no,
							'filter_po_no'	=> $filter_po_no,
							'sort'				=> $sort,
							'order'				=> $order,
							'page'				=> $page,
							'limit'				=> 30
						);
		$results = PurchaseOrderDetail::getPODetailsWithExpiration($arrParams);
		$results_total = PurchaseOrderDetail::getPODetailsWithExpiration($arrParams, TRUE);

		// Pagination
		$this->data['arrFilters'] = array(
										'filter_shipment_reference_no'	=> $filter_shipment_reference_no,
										'filter_po_no'	=> $filter_po_no,
										'sort'				=> $sort,
										'order'				=> $order
									);

		$this->data['expiry_items'] = Paginator::make($results, $results_total, 30);
		$this->data['expiry_items_count'] = $results_total;

		$this->data['counter'] 	= $this->data['expiry_items']->getFrom();

		$this->data['filter_po_no'] = $filter_po_no;
		$this->data['filter_shipment_reference_no'] = $filter_shipment_reference_no;
		// $this->data['po_info'] = PurchaseOrder::getPOInfo($receiver_no);

		$this->data['sort'] = $sort;
		$this->data['order'] = $order;
		$this->data['page'] = $page;

		$url = '?filter_po_no=' . $filter_po_no . '&filter_shipment_reference_no=' . $filter_shipment_reference_no;
		$url .= '&page=' . $page;

		$order_purchase_order_no = ($sort=='purchase_order_no' && $order=='ASC') ? 'DESC' : 'ASC';

		$this->data['sort_po_no'] = URL::to('expiry_items' . $url . '&sort=filter_po_no&order=' . $order_purchase_order_no, NULL, FALSE);

		// Permissions
		$this->data['permissions'] = unserialize(Session::get('permissions'));

		$this->layout->content = View::make('expiry_items.list', $this->data);
	}

	protected function setURL() {
		// Search Filters
		// http://local.ccri.com/picking/list?filter_doc_no=&filter_status=&filter_store=26&sort=doc_no&order=ASC
		$url = '?filter_po_no=' . Input::get('filter_po_no', NULL);
		$url .= '&filter_shipment_reference_no=' . Input::get('filter_shipment_reference_no', NULL);
		$url .= '&sort=' . Input::get('sort', 'sku');
		$url .= '&order=' . Input::get('order', 'ASC');
		$url .= '&page=' . Input::get('page', 1);

		return $url;
	}
}