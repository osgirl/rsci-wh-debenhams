<?php

class SlotListController extends BaseController {
	private $data = array();

	protected $layout = "layouts.main";

	public function __construct()
    {
    	date_default_timezone_set('Asia/Manila');
    	$this->beforeFilter('csrf', array('on' => 'post'));
    	$this->beforeFilter('auth', array('only' => array('Dashboard')));

    	// Check Permissions
    	if (Session::has('permissions')) {
	    	if (!in_array('CanAccessSlotMasterList', unserialize(Session::get('permissions'))))  {
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
				return Redirect::to('slots');
			}
    	} else {
			return Redirect::to('users/logout');
		}
		$this->data = Lang::get('slot_list');
		$this->data['text_empty_results'] = Lang::get('general.text_empty_results');
		$arrParams = array(
							'filter_slot_no'	=> Input::get('filter_slot_no', NULL),
							'sort'				=> Input::get('sort', 'slot_code'),
							'order'				=> Input::get('order', 'ASC'),
							'page'				=> NULL,
							'limit'				=> NULL
						);
		$results = SlotList::getSlotLists($arrParams);

		$this->data['results'] = $results;

		$pdf = App::make('dompdf');
		$pdf->loadView('slots.report_list', $this->data)->setPaper('a4')->setOrientation('landscape');
		// return $pdf->stream();
		return $pdf->download('slots_' . date('Ymd') . '.pdf');
	}

	/*public function exportCSV() {
		// Check Permissions
		if (Session::has('permissions')) {
	    	if (!in_array('CanExportSlotMasterList', unserialize(Session::get('permissions'))))  {
				return Redirect::to('slots');
			}
    	} else {
			return Redirect::to('users/logout');
		}
		$this->data = Lang::get('slot_list');
		$this->data['text_empty_results'] = Lang::get('general.text_empty_results');
		$arrParams = array(
							'filter_slot_no'	=> Input::get('filter_slot_no', NULL),
							'sort'				=> Input::get('sort', 'slot_code'),
							'order'				=> Input::get('order', 'ASC'),
							'page'				=> NULL,
							'limit'				=> NULL
						);
		$results = SlotList::getSlotLists($arrParams);

		$this->data['results'] = $results;

		$pdf = App::make('dompdf');
		$pdf->loadView('slots.report_list', $this->data)->setPaper('a4')->setOrientation('landscape');
		// return $pdf->stream();
		return $pdf->download('slots_' . date('Ymd') . '.pdf');
	}*/

	protected function getList() {
		$this->data['heading_title'] = Lang::get('slot_list.heading_title');

		$this->data['text_empty_results'] = Lang::get('general.text_empty_results');
		$this->data['text_total'] = Lang::get('general.text_total');

		$this->data['label_filter_slot_no'] = Lang::get('slot_list.label_filter_slot_no');

		$this->data['col_id'] = Lang::get('slot_list.col_id');
		$this->data['col_slot_no'] = Lang::get('slot_list.col_slot_no');
		$this->data['col_store_no'] = Lang::get('slot_list.col_store_no');
		$this->data['col_zone_no'] = Lang::get('slot_list.col_zone_no');

		$this->data['button_search'] = Lang::get('general.button_search');
		$this->data['button_clear'] = Lang::get('general.button_clear');
		$this->data['button_export'] = Lang::get('general.button_export');
		$this->data['button_jda'] = Lang::get('general.button_jda');

		// URL
		$this->data['url_export'] = URL::to('slots/export');

		// Search Filters
		$filter_slot_no = Input::get('filter_slot_no', NULL);

		$sort = Input::get('sort', 'slot_code');
		$order = Input::get('order', 'ASC');
		$page = Input::get('page', 1);

		// Data
		$arrParams = array(
							'filter_slot_no'	=> $filter_slot_no,
							'sort'				=> $sort,
							'order'				=> $order,
							'page'				=> $page,
							'limit'				=> 30
						);
		$results = SlotList::getSlotLists($arrParams);
		$results_total = SlotList::getCountSlotLists($arrParams);

		// Pagination
		$this->data['arrFilters'] = array(
										'filter_slot_no'	=> $filter_slot_no,
										'sort'				=> $sort,
										'order'				=> $order
									);

		$this->data['slots'] = Paginator::make($results, $results_total, 30);
		$this->data['slots_count'] = $results_total;

		$this->data['counter'] 	= $this->data['slots']->getFrom();

		$this->data['filter_slot_no'] = $filter_slot_no;

		$this->data['sort'] = $sort;
		$this->data['order'] = $order;
		$this->data['page'] = $page;

		$url = '?filter_slot_no=' . $filter_slot_no;
		$url .= '&page=' . $page;

		$order_id = ($sort=='id' && $order=='ASC') ? 'DESC' : 'ASC';
		$order_slot_no = ($sort=='slot_code' && $order=='ASC') ? 'DESC' : 'ASC';

		$this->data['sort_id'] = URL::to('slots' . $url . '&sort=id&order=' . $order_id, NULL, FALSE);
		$this->data['sort_slot_no'] = URL::to('slots' . $url . '&sort=slot_code&order=' . $order_slot_no, NULL, FALSE);

		// Permissions
		$this->data['permissions'] = unserialize(Session::get('permissions'));

		$this->layout->content = View::make('slots.list', $this->data);
	}
}