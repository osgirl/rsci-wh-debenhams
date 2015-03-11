<?php

class UnlistedController extends BaseController {
	private $data = array();

	public $layout = "layouts.main";

	public function __construct()
    {
    	date_default_timezone_set('Asia/Manila');
    	$this->beforeFilter('csrf', array('on' => 'post'));
    	$this->beforeFilter('auth', array('only' => array('Dashboard')));

    	// Check Permissions
    	if (Session::has('permissions')) {
	    	if (!in_array('CanAccessUnlisted', unserialize(Session::get('permissions'))))  {
				return Redirect::to('/');
			}
    	} else {
			return Redirect::to('users/logout');
		}
    }

	public function showIndex() {
		// Check Permissions

    	if (Session::has('permissions')) {
	    	if (!in_array('CanAccessUnlisted', unserialize(Session::get('permissions'))))  {
				return Redirect::to('/');
			}
    	} else {
			return Redirect::to('users/logout');
		}

		$this->getList();
	}

	/*public function exportCSV() {
		// Check Permissions
		if (Session::has('permissions')) {
	    	if (!in_array('CanExportUnlisted', unserialize(Session::get('permissions'))))  {
				return Redirect::to('unlisted');
			}
    	} else {
			return Redirect::to('users/logout');
		}

		$arrParams = array(
							'filter_reference_no'	=> Input::get('filter_reference_no', NULL),
							'filter_sku' 		=> Input::get('filter_sku', NULL),
							'sort'				=> Input::get('sort', 'reference_no'),
							'order'				=> Input::get('order', 'ASC'),
							'page'				=> NULL,
							'limit'				=> NULL
						);

		$results = Unlisted::getList($arrParams);
		$output = Lang::get('unlisted.col_reference') . ",";
		$output .= Lang::get('unlisted.col_sku') . ",";
		$output .= Lang::get('unlisted.col_quantity_received') . "\n";

	    foreach ($results as $value) {
	    	$exportData = array(
	    						'"' . $value->reference_no . '"',
	    						'"' . $value->sku . '"',
	    						'"' . $value->quantity_received . '"'
	    					);

	      	$output .= implode(",", $exportData);
	      	$output .= "\n";
	  	}

		$headers = array(
			'Content-Type' => 'text/csv',
			'Content-Disposition' => 'attachment; filename="unlisted_' . date('Ymd')  . '_' . time() . '.csv"',
		);

		return Response::make(rtrim($output, "\n"), 200, $headers);
	}*/

	public function exportCSV() {
		// Check Permissions
		if (Session::has('permissions')) {
	    	if (!in_array('CanExportUnlisted', unserialize(Session::get('permissions'))))  {
				return Redirect::to('unlisted');
			}
    	} else {
			return Redirect::to('users/logout');
		}
		$this->data = Lang::get('unlisted');
		$arrParams = array(
							'filter_reference_no'	=> Input::get('filter_reference_no', NULL),
							'filter_sku' 		=> Input::get('filter_sku', NULL),
							'sort'				=> Input::get('sort', 'reference_no'),
							'order'				=> Input::get('order', 'ASC'),
							'page'				=> NULL,
							'limit'				=> NULL
						);

		$results = Unlisted::getList($arrParams);
		$this->data['results'] = $results;

		$pdf = App::make('dompdf');
		$pdf->loadView('unlisted.report_list', $this->data)->setPaper('a4')->setOrientation('landscape');
		// return $pdf->stream();
		return $pdf->download('unlisted_' . date('Ymd') . '.pdf');
	}

	protected function getList()
	{
		$this->data = Lang::get('unlisted');

		// URL
		$this->data['url_export'] = URL::to('unlisted/export');

		// Search Filters
		$filter_sku = Input::get('filter_sku', NULL);
		$filter_reference_no = Input::get('filter_reference_no', NULL);

		$sort = Input::get('sort', 'reference_no');
		$order = Input::get('order', 'ASC');
		$page = Input::get('page', 1);

		// Data
		$arrParams = array(
							'filter_reference_no'	=> $filter_reference_no,
							'filter_sku'		=> $filter_sku,
							'sort'				=> $sort,
							'order'				=> $order,
							'page'				=> $page,
							'limit'				=> 30
						);
		$results = Unlisted::getList($arrParams)->toArray();
		// echo '<pre>'; dd($results);
		$results_total = count($results);

		// Pagination
		$this->data['arrFilters'] = array(
										'filter_reference_no'	=> $filter_reference_no,
										'filter_sku'		=> $filter_sku,
										'sort'				=> $sort,
										'order'				=> $order
									);

		$this->data['unlisted'] = Paginator::make($results, $results_total, 30);
		$this->data['unlisted_count'] = $results_total;

		$this->data['counter'] 	= $this->data['unlisted']->getFrom();

		$this->data['filter_reference_no'] = $filter_reference_no;
		$this->data['filter_sku'] = $filter_sku;

		$this->data['sort'] = $sort;
		$this->data['order'] = $order;
		$this->data['page'] = $page;

		$url = '?filter_reference_no=' . $filter_reference_no;
		$url .= '&filter_sku=' . $filter_sku;
		$url .= '&page=' . $page;

		$order_reference = ($sort=='reference_no' && $order=='ASC') ? 'DESC' : 'ASC';
		$order_sku = ($sort=='sku' && $order=='ASC') ? 'DESC' : 'ASC';

		$this->data['sort_reference'] = URL::to('unlisted' . $url . '&sort=reference_no&order=' . $order_reference, NULL, FALSE);
		$this->data['sort_sku'] = URL::to('unlisted' . $url . '&sort=sku&order=' . $order_sku, NULL, FALSE);

		// Permissions
		$this->data['permissions'] = unserialize(Session::get('permissions'));

		$this->layout->content = View::make('unlisted.list', $this->data);
	}

	protected function setURL($forDetail = false, $forBackToList = false) {
		// Search Filters
		$url = '&filter_reference_no=' . Input::get('filter_reference_no', NULL);
		$url .= '&filter_sku=' . Input::get('filter_sku', NULL);
		if($forDetail) {
			$url .= '&sort_back=' . Input::get('sort', 'reference_no');
			$url .= '&order_back=' . Input::get('order', 'ASC');
			$url .= '&page_back=' . Input::get('page', 1);
		} else {
			if($forBackToList == true) {
				$url .= '&sort=' . Input::get('sort_back', 'reference_no');
				$url .= '&order=' . Input::get('order_back', 'ASC');
				$url .= '&page=' . Input::get('page_back', 1);
			} else {
				$url .= '&sort=' . Input::get('sort', 'reference_no');
				$url .= '&order=' . Input::get('order', 'ASC');
				$url .= '&page=' . Input::get('page', 1);
			}
		}
		return $url;
	}

	protected function uirrGeneration() {
		$uirr             = Dataset::firstOrNew(array('data_code'=>'UIRR_FORMAT'));
		$uirrNo           = sprintf("%07s", (int)$uirr->data_value + 1);
		$uirr->data_value = $uirrNo;
		$uirr->updated_at = date('Y-m-d H:i:s');
		$uirr->save();
		$uirr_code        = Dataset::find($uirr->id);

		return $uirrNo;
	}
}
