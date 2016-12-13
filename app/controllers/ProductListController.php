<?php

class ProductListController extends BaseController {
	private $data = array();

	protected $layout = "layouts.main";

	public function __construct()
    {
    	date_default_timezone_set('Asia/Manila');
    	$this->beforeFilter('csrf', array('on' => 'post'));
    	$this->beforeFilter('auth', array('only' => array('Dashboard')));

    	// Check Permissions
    	if (Session::has('permissions')) {
	    	if (!in_array('CanAccessProductMasterList', unserialize(Session::get('permissions'))))  {
				return Redirect::to('/');
			}
    	} else {
			return Redirect::to('users/logout');
		}
    }

	public function showIndex() {
		// Check Permissions
    	if (Session::has('permissions')) {
	    	if (!in_array('CanAccessProductMasterList', unserialize(Session::get('permissions'))))  {
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
	    	if (!in_array('CanExportProductMasterList', unserialize(Session::get('permissions'))))  {
				return Redirect::to('products');
			}
    	} else {
			return Redirect::to('users/logout');
		}

		$arrParams = array(
							'filter_prod_sku'			=> Input::get('filter_prod_sku', NULL),
							'filter_prod_upc'			=> Input::get('filter_prod_upc', NULL),
							'filter_prod_full_name'		=> Input::get('filter_prod_full_name', NULL),
							'filter_prod_short_name'	=> Input::get('filter_prod_short_name', NULL),
							'filter_dept_no'			=> Input::get('filter_dept_no', NULL),
							'filter_sub_dept_no'		=> Input::get('filter_sub_dept_no', NULL),
							'sort'						=> Input::get('sort', 'sku'),
							'order'						=> Input::get('order', 'ASC'),
							'page'						=> NULL,
							'limit'						=> NULL
						);
		$results = ProductList::getProductLists($arrParams);
	    $output = Lang::get('product_list.col_prod_sku') . ',';
		$output .= Lang::get('product_list.col_prod_upc') . ',';
		$output .= Lang::get('product_list.col_prod_full_name') . ',';
		$output .= Lang::get('product_list.col_prod_short_name') . ',';
		$output .= Lang::get('product_list.col_department') . ',';
		$output .= Lang::get('product_list.col_sub_department') . "\n";

	    foreach ($results as $value) {
	    	$exportData = array(
	    						'"' . $value->sku . '"',
	    						'"' . $value->upc . '"',
	    						'"' . $value->description . '"',
	    						'"' . $value->short_description . '"',
	    						'"' . $value->dept_code . ' - ' . $value->dept_name . '"',
	    						'"' . $value->sub_dept . ' - ' . $value->sub_dept_name . '"'
	    					);

	      	$output .= implode(",", $exportData);
	      	$output .= "\n";
	  	}

		$headers = array(
			'Content-Type' => 'text/csv',
			'Content-Disposition' => 'attachment; filename="productList_' . date('Ymd')  . '_' . time() . '.csv"',
		);

		return Response::make(rtrim($output, "\n"), 200, $headers);
	}*/

	public function exportCSV() {
		// Check Permissions
		if (Session::has('permissions')) {
	    	if (!in_array('CanExportProductMasterList', unserialize(Session::get('permissions'))))  {
				return Redirect::to('products');
			}
    	} else {
			return Redirect::to('users/logout');
		}
		$this->data = Lang::get('product_list');
		$this->data['text_empty_results'] = Lang::get('general.text_empty_results');
		$arrParams = array(
							'filter_prod_sku'			=> Input::get('filter_prod_sku', NULL),
							'filter_prod_upc'			=> Input::get('filter_prod_upc', NULL),
							'filter_prod_full_name'		=> Input::get('filter_prod_full_name', NULL),
							'filter_prod_short_name'	=> Input::get('filter_prod_short_name', NULL),
							'filter_dept_no'			=> Input::get('filter_dept_no', NULL),
							'filter_sub_dept_no'		=> Input::get('filter_sub_dept_no', NULL),
							'sort'						=> Input::get('sort', 'sku'),
							'order'						=> Input::get('order', 'ASC'),
							'page'						=> NULL,
							'limit'						=> NULL
						);
		$results = ProductList::getProductLists($arrParams);
	 
	    $this->data['results'] = $results;

		$pdf = App::make('dompdf');
		$pdf->loadView('products.report_list', $this->data)->setPaper('a4')->setOrientation('landscape');
		// return $pdf->stream();
		return $pdf->download('products_' . date('Ymd') . '.pdf');
	}

	public function getSubDepartments() {
		$filter_dept_no = Input::get('filter_dept_no', NULL);

		$sub_departments = array();
		foreach (Department::getSubDepartments($filter_dept_no) as $item) {
			$sub_departments[$item->sub_dept] = $item->sub_dept . ' - ' . $item->description;
		}
		$filter_sub_department_options = $sub_departments;

		return Response::json($filter_sub_department_options);
	}

	protected function getList() {
		$this->data['heading_title'] = Lang::get('product_list.heading_title');

		$this->data['text_empty_results'] = Lang::get('general.text_empty_results');
		$this->data['text_total'] = Lang::get('general.text_total');

		$this->data['label_filter_prod_sku'] = Lang::get('product_list.label_filter_prod_sku');
		$this->data['label_filter_prod_upc'] = Lang::get('product_list.label_filter_prod_upc');
		$this->data['label_filter_prod_full_name'] = Lang::get('product_list.label_filter_prod_full_name');
		$this->data['label_filter_prod_short_name'] = Lang::get('product_list.label_filter_prod_short_name');
		$this->data['label_filter_dept_name'] = Lang::get('product_list.label_filter_dept_name');
		$this->data['label_filter_sub_dept_name'] = Lang::get('product_list.label_filter_sub_dept_name');

		$this->data['col_id'] = Lang::get('product_list.col_id');
		$this->data['col_prod_sku'] = Lang::get('product_list.col_prod_sku');
		$this->data['col_prod_upc'] = Lang::get('product_list.col_prod_upc');
		$this->data['col_prod_full_name'] = Lang::get('product_list.col_prod_full_name');
		$this->data['col_prod_short_name'] = Lang::get('product_list.col_prod_short_name');
		$this->data['col_department'] = Lang::get('product_list.col_department');
		$this->data['col_sub_department'] = Lang::get('product_list.col_sub_department');

		$this->data['button_search'] = Lang::get('general.button_search');
		$this->data['button_clear'] = Lang::get('general.button_clear');
		$this->data['button_export'] = Lang::get('general.button_export');
		$this->data['button_jda'] = Lang::get('general.button_jda');

		// URL
		$this->data['url_export'] = URL::to('products/export');
		$this->data['url_department'] = URL::to('products/department');

		// Search Filters
		$filter_prod_sku = Input::get('filter_prod_sku', NULL);
		$filter_prod_upc = Input::get('filter_prod_upc', NULL);
		$filter_prod_full_name = Input::get('filter_prod_full_name', NULL);
		$filter_prod_short_name = Input::get('filter_prod_short_name', NULL);
		$filter_dept_no = Input::get('filter_dept_no', NULL);
		$filter_sub_dept_no = Input::get('filter_sub_dept_no', NULL);

		// Search Options
		$departments = array();
		foreach (Department::getDepartments() as $item) {
			$departments[$item->dept_code] = $item->dept_code . ' - ' . $item->description;
		}
		$this->data['filter_department_options'] = array('' => Lang::get('general.text_select')) + $departments;


		$sub_departments = array();
		foreach (Department::getSubDepartments($filter_dept_no) as $item) {
			$sub_departments[$item->sub_dept] = $item->sub_dept . ' - ' . $item->description;
		}
		$this->data['filter_sub_department_options'] = array('' => Lang::get('general.text_select')) + $sub_departments;


		// Data
		$page = Input::get('page', 1);
		$sort = Input::get('sort', 'sku');
		$order = Input::get('order', 'ASC');

		$arrParams = array(
							'filter_prod_sku'			=> $filter_prod_sku,
							'filter_prod_upc'			=> $filter_prod_upc,
							'filter_prod_full_name'		=> $filter_prod_full_name,
							'filter_prod_short_name'	=> $filter_prod_short_name,
							'filter_dept_no'			=> $filter_dept_no,
							'filter_sub_dept_no'		=> $filter_sub_dept_no,
							'sort'						=> $sort,
							'order'						=> $order,
							'page'						=> $page,
							'limit'						=> 30
						);
		$results = ProductList::getProductLists($arrParams);
		DebugHelper::log(__METHOD__, $results);
		$results_total = ProductList::getCountProductLists($arrParams);
		DebugHelper::log(__METHOD__, $results_total);
		// Pagination
		$this->data['arrFilters'] = array(
										'filter_prod_sku'			=> $filter_prod_sku,
										'filter_prod_upc'			=> $filter_prod_upc,
										'filter_prod_full_name'		=> $filter_prod_full_name,
										'filter_prod_short_name'	=> $filter_prod_short_name,
										'filter_dept_no'			=> $filter_dept_no,
										'filter_sub_dept_no'		=> $filter_sub_dept_no,
										'sort'						=> $sort,
										'order'						=> $order
									);

		$this->data['products'] = Paginator::make($results, $results_total, 30);
		$this->data['products_count'] = $results_total;

		$this->data['counter'] 	= $this->data['products']->getFrom();

		$this->data['filter_prod_sku'] = $filter_prod_sku;
		$this->data['filter_prod_upc'] = $filter_prod_upc;
		$this->data['filter_prod_full_name'] = $filter_prod_full_name;
		$this->data['filter_prod_short_name'] = $filter_prod_short_name;
		$this->data['filter_dept_no'] = $filter_dept_no;
		$this->data['filter_sub_dept_no'] = $filter_sub_dept_no;

		$this->data['sort'] = $sort;
		$this->data['order'] = $order;
		$this->data['page'] = $page;

		$url = '?filter_prod_sku=' . $filter_prod_sku . '&filter_prod_upc=' . $filter_prod_upc;
		$url .= '&filter_prod_full_name=' . $filter_prod_full_name . '&filter_prod_short_name=' . $filter_prod_short_name;
		$url .= '&filter_dept_no=' . $filter_dept_no . '&filter_sub_dept_no=' . $filter_sub_dept_no;
		$url .= '&page=' . $page;

		$order_id = ($sort=='id' && $order=='ASC') ? 'DESC' : 'ASC';
		$order_sku = ($sort=='sku' && $order=='ASC') ? 'DESC' : 'ASC';
		$order_upc = ($sort=='upc' && $order=='ASC') ? 'DESC' : 'ASC';
		$order_full_name = ($sort=='full_name' && $order=='ASC') ? 'DESC' : 'ASC';
		$order_short_name = ($sort=='short_name' && $order=='ASC') ? 'DESC' : 'ASC';
		$order_dept = ($sort=='dept' && $order=='ASC') ? 'DESC' : 'ASC';
		$order_sub_dept = ($sort=='sub_dept' && $order=='ASC') ? 'DESC' : 'ASC';

		$this->data['sort_id'] = URL::to('products' . $url . '&sort=id&order=' . $order_id, NULL, FALSE);
		$this->data['sort_sku'] = URL::to('products' . $url . '&sort=sku&order=' . $order_sku, NULL, FALSE);
		$this->data['sort_upc'] = URL::to('products' . $url . '&sort=upc&order=' . $order_upc, NULL, FALSE);
		$this->data['sort_full_name'] = URL::to('products' . $url . '&sort=full_name&order=' . $order_full_name, NULL, FALSE);
		$this->data['sort_short_name'] = URL::to('products' . $url . '&sort=short_name&order=' . $order_short_name, NULL, FALSE);
		$this->data['sort_dept'] = URL::to('products' . $url . '&sort=dept&order=' . $order_dept, NULL, FALSE);
		$this->data['sort_sub_dept'] = URL::to('products' . $url . '&sort=sub_dept&order=' . $order_sub_dept, NULL, FALSE);

		// Permissions
		$this->data['permissions'] = unserialize(Session::get('permissions'));

		$this->layout->content = View::make('products.list', $this->data);
	}
}