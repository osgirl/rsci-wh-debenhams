<?php

class PicklistController extends BaseController {

	protected $layout = "layouts.main";

	private $types = array('upc'=> 'UPC','store'=>'Store' );

	public function __construct() {
		$this->beforeFilter('csrf', array('on' => 'post'));
		$this->beforeFilter('auth', array('only'=> array('Dashboard')));
		$this->apiUrl = Config::get('constant.api_url');
		date_default_timezone_set('Asia/Manila');

		$docNo        = Input::get("doc_no");
		$status       = 'posted'; // closed
		$date_updated = date('Y-m-d H:i:s');

		$status_options = Dataset::where("data_code", "=", "PICKLIST_STATUS_TYPE")->get()->lists("id", "data_value");

		print_r($status_options['closed']);
	}

	/**
	* Shows List of Picklist
	*
	* @example  www.example.com/picklist
	*
	* @return View of Picklist
	*/
	public function showIndex() {
		// Check Permissions
		if (Session::has('permissions')) {
	    	if (!in_array('CanAccessPacking', unserialize(Session::get('permissions'))))  {
	    		return Redirect::to('purchase_order');
			}
    	} else {
			return Redirect::to('users/logout');
		}

		$this->getList();
	}

	/*public function exportCSV()
	{
		$this->checkPermissions('CanExportPickingDocuments');
		$arrParams = array(
							'filter_type' 		=> Input::get('filter_type', NULL),
							'filter_doc_no' 	=> Input::get('filter_doc_no', NULL),
							'filter_status' 		=> Input::get('filter_status', NULL),
							'sort'					=> Input::get('sort', 'doc_no'),
							'order'					=> Input::get('order', 'ASC'),
							'page'					=> NULL,
							'limit'					=> NULL
						);
		$results = Picklist::getPickingList($arrParams);

		$output = Lang::get('picking.col_id'). ',';
		$output .= Lang::get('picking.col_type'). ',';
		$output .= Lang::get('picking.col_doc_no'). ',';
		$output .= Lang::get('picking.col_status'). "\n";

		$pl_status_type = Dataset::getTypeWithValue("PICKLIST_STATUS_TYPE");

		foreach ($results as $key => $value) {

	    	$exportData = array(
	    						'"' . $value->id . '"',
	    						'"' . $value->type . '"',
	    						'"' . $value->move_doc_number . '"',
	    						'"' . $pl_status_type[$value->pl_status] . '"'
	    					);

	      	$output .= implode(",", $exportData);
	      	$output .= "\n";
	  	}

	  	$headers = array(
			'Content-Type' => 'text/csv',
			'Content-Disposition' => 'attachment; filename="picklist_' . date('Ymd')  . '_' . time() . '.csv"',
		);

		return Response::make(rtrim($output, "\n"), 200, $headers);

	}*/

	public function exportCSV()
	{
		$this->checkPermissions('CanExportPacking');
		$this->data = Lang::get('picking');
		$arrParams = array(
							'filter_type'   => Input::get('filter_type', NULL),
							'filter_doc_no' => Input::get('filter_doc_no', NULL),
							'filter_status' => Input::get('filter_status', NULL),
							'filter_store' 	=> Input::get('filter_store', NULL),
							'filter_stock_piler' 	=> Input::get('filter_stock_piler', NULL),
							'sort'          => Input::get('sort', 'doc_no'),
							'order'         => Input::get('order', 'ASC'),
							'page'          => NULL,
							'limit'         => NULL
						);

		$results = Picklist::getPickingListv2($arrParams)->toArray();
		$this->data['results'] = $results;

		$pdf = App::make('dompdf');
		$pdf->loadView('picking.report_list', $this->data)->setPaper('a4')->setOrientation('landscape');
		// return $pdf->stream();
		return $pdf->download('picking_' . date('Ymd') . '.pdf');
	}

	public function exportDetailCSV()
	{
		$this->checkPermissions('CanExportPacking');

		if (Picklist::where('move_doc_number'== Input::get('picklist_doc', NULL))!=NULL) {
			$this->data = Lang::get('picking');
			$this->data['text_empty_results']     = Lang::get('general.text_empty_results');
			$arrParams = array(
						'filter_sku'			=> Input::get('filter_sku', NULL),
						'filter_upc'			=> Input::get('filter_upc', NULL),
						'filter_so'				=> Input::get('filter_so', NULL),
						'filter_from_slot'		=> Input::get('filter_from_slot', NULL),
						'sort'					=> Input::get('sort', 'sku'),
						'order'					=> Input::get('order', 'ASC'),
						'page'					=> NULL,
						'picklist_doc'			=> Input::get('picklist_doc', NULL),
						'limit'					=> NULL
					);
			$results = PicklistDetails::getFilteredPicklistDetail($arrParams);

			$this->data['results'] = $results;

			$pdf = App::make('dompdf');
			$pdf->loadView('picking.report_detail', $this->data)->setPaper('a4')->setOrientation('landscape');
			// return $pdf->stream();
			return $pdf->download('picking_detail_' . date('Ymd') . '.pdf');
		}
		return;
	}

	public function getList()
	{
		$this->data                           = Lang::get('picking');
		$this->data['text_empty_results']     = Lang::get('general.text_empty_results');
		$this->data['text_total']             = Lang::get('general.text_total');
		$this->data['text_select']            = Lang::get('general.text_select');
		$this->data['button_search']          = Lang::get('general.button_search');
		$this->data['button_clear']           = Lang::get('general.button_clear');
		$this->data['button_export']          = Lang::get('general.button_export');
		$this->data['url_detail']             = URL::to('picking/detail' . $this->setURL(true));
		$this->data['url_lock_tags']          = URL::to('picking/locktags');
		$this->data['url_export']             = URL::to('picking/export'. $this->setURL(true));
		$this->data['url_change_to_store']    = URL::to('picking/change_to_store');
		$this->data['url_generate_load_code'] = URL::to('picking/new/load');
		$this->data['url_assign']             = URL::to('picking/assign');
		$this->data['stores']                 = Store::lists( 'store_name', 'store_code');
		// $this->data['url_load']	= URL::to('picking/load');

		// Message
		$this->data['error'] = '';
		if (Session::has('error')) {
			$this->data['error'] = Session::get('error');
		}

		$this->data['success'] = '';
		if (Session::has('success')) {
			$this->data['success'] = Session::get('success');
		}

		$this->data['pl_status_type'] = Dataset::getTypeWithValue("PICKLIST_STATUS_TYPE");
		$this->data['pl_type'] = $this->types;
		$this->data['load_codes']	= $this->getLoadCodes();
		$this->data['stock_piler_list'] = $this->getStockPilers();

		// Search Filters
		$filter_type = Input::get('filter_type', NULL);
		$filter_doc_no = Input::get('filter_doc_no', NULL);
		$filter_status = Input::get('filter_status', NULL);
		$filter_store = Input::get('filter_store', NULL);
		$filter_stock_piler = Input::get('filter_stock_piler', NULL);

		$sort = Input::get('sort', 'doc_no');
		$order = Input::get('order', 'ASC');
		$page = Input::get('page', 1);

		//Data
		$arrParams = array(
						'filter_type' 			=> $filter_type,
						'filter_doc_no' 		=> $filter_doc_no,
						'filter_status' 		=> $filter_status,
						'filter_store' 			=> $filter_store,
						'filter_stock_piler' 	=> $filter_stock_piler,
						'sort'					=> $sort,
						'order'					=> $order,
						'page'					=> $page,
						'limit'					=> 30
					);

		$results 		= Picklist::getPickingListv2($arrParams)->toArray();
		$results_total 		= count($results);
		// $results_total 	= Picklist::getPickingListCount($arrParams);

		// Pagination
		$this->data['arrFilters'] = array(
									'filter_type' 			=> $filter_type,
									'filter_doc_no' 		=> $filter_doc_no,
									'filter_status' 		=> $filter_status,
									'filter_store' 			=> $filter_store,
									'filter_stock_piler' 	=> $filter_stock_piler,
									'sort'					=> $sort,
									'order'					=> $order
								);

		$this->data['picklist'] = Paginator::make($results, $results_total, 30);
		$this->data['picklist_count'] = $results_total;
		$this->data['counter'] 	= $this->data['picklist']->getFrom();
		$this->data['filter_type'] = $filter_type;
		$this->data['filter_doc_no'] = $filter_doc_no;
		$this->data['filter_status'] = $filter_status;
		$this->data['filter_store'] = $filter_store;
		$this->data['filter_stock_piler'] = $filter_stock_piler;
		$this->data['sort'] = $sort;
		$this->data['order'] = $order;
		$this->data['page'] = $page;

		$url = '?filter_type=' . $filter_type . '&filter_doc_no=' . $filter_doc_no;
		$url .= '&filter_status=' . $filter_status . '&filter_store=' . $filter_store;
		$url .= '&filter_stock_piler=' . $filter_stock_piler;
		$url .= '&page=' . $page;

		$order_doc_no = ($sort=='doc_no' && $order=='ASC') ? 'DESC' : 'ASC';

		$this->data['sort_doc_no'] = URL::to('picking/list' . $url . '&sort=doc_no&order=' . $order_doc_no, NULL, FALSE);

		// Permissions
		$this->data['permissions'] = unserialize(Session::get('permissions'));

		$this->layout->content = View::make('picking.list', $this->data);
	}

	public function getPicklistDetails()
	{
		$this->checkPermissions('CanAccessPacking');
		$picklistDoc = Input::get('picklist_doc', NULL);

		if($picklistDoc == NULL) return Redirect::to('picking/list')->withError(Lang::get('picking.error_not_exist'));

		$this->data                       = Lang::get('picking');
		$this->data['text_empty_results'] = Lang::get('general.text_empty_results');
		$this->data['text_total']         = Lang::get('general.text_total');
		$this->data['text_select']        = Lang::get('general.text_select');
		$this->data['button_back']        = Lang::get('general.button_back');
		$this->data['button_search']      = Lang::get('general.button_search');
		$this->data['button_clear']       = Lang::get('general.button_clear');
		$this->data['url_back']           = URL::to('picking/list' . $this->setURL(false, true));
		$this->data['url_detail']         = URL::to('picking/detail');
		$this->data['pick_status_type']   = Dataset::getTypeWithValue("PICKLIST_STATUS_TYPE");
		//added this because there is not closed in the detail
		unset($this->data['pick_status_type'][2]);

		// Message
		$this->data['error'] = '';
		if (Session::has('error')) {
			$this->data['error'] = Session::get('error');
		}

		$this->data['success'] = '';
		if (Session::has('success')) {
			$this->data['success'] = Session::get('success');
		}

		// Search Filters
		$filter_type          = Input::get('filter_type', NULL);
		$filter_doc_no        = Input::get('filter_doc_no', NULL);
		$filter_status        = Input::get('filter_status', NULL);
		$filter_sku           = Input::get('filter_sku', NULL);
		$filter_upc           = Input::get('filter_upc', NULL);
		$filter_so            = Input::get('filter_so', NULL);
		$filter_from_slot     = Input::get('filter_from_slot', NULL);
		// $filter_to_slot    = Input::get('filter_to_slot', NULL);
		// $filter_status_detail = Input::get('filter_status_detail', NULL);

		//for back
		$sort_back  = Input::get('sort_back', 'doc_no');
		$order_back = Input::get('order_back', 'ASC');
		$page_back  = Input::get('page_back', 1);

		// Details
		$sort_detail  = Input::get('sort', 'sku');
		$order_detail = Input::get('order', 'ASC');
		$page_detail  = Input::get('page', 1);

		$arrParams = array(
						'filter_sku'			=> $filter_sku,
						'filter_upc'			=> $filter_upc,
						'filter_so'				=> $filter_so,
						'filter_from_slot'		=> $filter_from_slot,
						// 'filter_to_slot'		=> $filter_to_slot,
						// 'filter_status_detail'	=> $filter_status_detail,
						'sort'					=> $sort_detail,
						'order'					=> $order_detail,
						'page'					=> $page_detail,
						'picklist_doc'			=> $picklistDoc,
						'limit'					=> 30
					);
		$results 		= PicklistDetails::getFilteredPicklistDetail($arrParams);
		$results_total 	= PicklistDetails::getFilteredPicklistDetail($arrParams, true);
		// echo "<pre>"; print_r($results);die();

		// Pagination
		$this->data['arrFilters'] = array(
									'filter_sku'			=> $filter_sku,
									'filter_upc'			=> $filter_upc,
									'filter_so'				=> $filter_so,
									'filter_from_slot'		=> $filter_from_slot,
									// 'filter_to_slot'		=> $filter_to_slot,
									// 'filter_status_detail'	=> $filter_status_detail,
									'filter_type'			=> $filter_type,
									'filter_doc_no'			=> $filter_doc_no,
									'filter_status'		=> $filter_status,
									'sort_back'				=> $sort_back,
									'order_back'			=> $order_back,
									'page_back'				=> $page_back,
									'sort'					=> $sort_detail,
									'order'					=> $order_detail,
									'picklist_doc'			=> $picklistDoc
								);

		$this->data['picklist_detail']       = Paginator::make($results->toArray(), $results_total, 30);
		$this->data['picklist_detail_count'] = $results_total;
		$this->data['counter']               = $this->data['picklist_detail']->getFrom();
		$this->data['picklist_doc']          = $picklistDoc;
		$this->data['filter_type']           = $filter_type;
		$this->data['filter_doc_no']         = $filter_doc_no;
		$this->data['filter_status']         = $filter_status;
		$this->data['filter_sku']            = $filter_sku;
		$this->data['filter_upc']            = $filter_upc;
		$this->data['filter_so']             = $filter_so;
		$this->data['filter_from_slot']      = $filter_from_slot;
		// $this->data['filter_status_detail']  = $filter_status_detail;
		$this->data['sort_back']             = $sort_back;
		$this->data['order_back']            = $order_back;
		$this->data['page_back']             = $page_back;

		// Details
		$this->data['sort']  = $sort_detail;
		$this->data['order'] = $order_detail;
		$this->data['page']  = $page_detail;

		$url = '?filter_sku=' . $filter_sku . '&filter_upc=' . $filter_upc . '&filter_so=' . $filter_so;
		$url .= '&filter_from_slot=' . $filter_from_slot;
		$url .= '&page_back=' . $page_back . '&order_back=' . $order_back . '&sort_back=' . $sort_back;
		$url .= '&picklist_doc=' . $picklistDoc . '&page=' . $page_detail;

		$this->data['url_export_detail'] =  URL::to('picking/export_detail' . $url);

		$order_sku = ($sort_detail=='sku' && $order_detail=='ASC') ? 'DESC' : 'ASC';
		$order_upc = ($sort_detail=='upc' && $order_detail=='ASC') ? 'DESC' : 'ASC';
		$order_so_no = ($sort_detail=='so_no' && $order_detail=='ASC') ? 'DESC' : 'ASC';
		$order_from_slot_code = ($sort_detail=='from_slot_code' && $order_detail=='ASC') ? 'DESC' : 'ASC';
		// $order_to_slot_code = ($sort_detail=='to_slot_code' && $order_detail=='ASC') ? 'DESC' : 'ASC';


		$this->data['sort_sku'] = URL::to('picking/detail' . $url . '&sort=sku&order=' . $order_sku, NULL, FALSE);
		$this->data['sort_upc'] = URL::to('picking/detail' . $url . '&sort=upc&order=' . $order_upc, NULL, FALSE);
		$this->data['sort_so_no'] = URL::to('picking/detail' . $url . '&sort=so_no&order=' . $order_so_no, NULL, FALSE);
		$this->data['sort_from_slot_code'] = URL::to('picking/detail' . $url . '&sort=from_slot_code&order=' . $order_from_slot_code, NULL, FALSE);
		// $this->data['sort_to_slot_code'] = URL::to('picking/detail' . $url . '&sort=to_slot_code&order=' . $order_to_slot_code, NULL, FALSE);

		$this->data['permissions'] = unserialize(Session::get('permissions'));

		$this->layout->content = View::make('picking.detail', $this->data);

	}

	public function getLockTagList()
	{
		$this->checkPermissions('CanViewPickingLockTags');

		$this->data['heading_title_picking_lock_tags'] = Lang::get('picking.heading_title_picking_lock_tags');

		$this->data['text_empty_results'] = Lang::get('general.text_empty_results');
		$this->data['text_total'] = Lang::get('general.text_total');
		$this->data['text_select'] = Lang::get('general.text_select');
		$this->data['text_warning_unlock'] = Lang::get('picking.text_warning_unlock');
		$this->data['text_warning_unlock_single'] = Lang::get('picking.text_warning_unlock_single');

		$this->data['label_stock_piler'] = Lang::get('picking.label_stock_piler');
		$this->data['label_doc_no'] = Lang::get('picking.label_doc_no');
		$this->data['label_sku'] = Lang::get('picking.label_sku');

		$this->data['col_time_locked'] = Lang::get('picking.col_time_locked');
		$this->data['col_stock_piler'] = Lang::get('picking.col_stock_piler');
		$this->data['col_action'] = Lang::get('picking.col_action');

		$this->data['button_search'] = Lang::get('general.button_search');
		$this->data['button_clear'] = Lang::get('general.button_clear');
		$this->data['button_to_picking'] = Lang::get('picking.button_to_picking');
		$this->data['button_unlock_tags'] = Lang::get('picking.button_unlock_tags');
		$this->data['button_unlock_tag'] = Lang::get('picking.button_unlock_tag');


		$this->data['url_to_picking'] = URL::to('picking/list');
		$this->data['url_lock_detail'] = URL::to('picking/locktags_detail'. $this->setURLLock(true));
		$this->data['url_unlock']= 	URL::to('picking/unlock');

		// Message
		$this->data['error'] = '';
		if (Session::has('error')) {
			$this->data['error'] = Session::get('error');
		}

		$this->data['success'] = '';
		if (Session::has('success')) {
			$this->data['success'] = Session::get('success');
		}

		$this->data['stock_piler_list'] = $this->getStockPilers();

		$this->data['error_no_lock_tag'] = Lang::get('picking.error_no_lock_tag');

		// Search Filters
		$filter_stock_piler = Input::get('filter_stock_piler', NULL);
		$filter_doc_no = Input::get('filter_doc_no', NULL);
		$filter_sku = Input::get('filter_sku', NULL);

		$sort = Input::get('sort', 'lock_tag');
		$order = Input::get('order', 'ASC');
		$page = Input::get('page', 1);

		//Data
		$arrParams = array(
						'filter_stock_piler' 	=> $filter_stock_piler,
						'filter_doc_no' 		=> $filter_doc_no,
						'filter_sku' 			=> $filter_sku,
						'sort'					=> $sort,
						'order'					=> $order,
						'page'					=> $page,
						'limit'					=> 30
					);
		$results 		= PicklistDetails::getLockTags($arrParams)->toArray();
		$results_total 	= PicklistDetails::getLockTags($arrParams, true);

		// Pagination
		$this->data['arrFilters'] = array(
									'filter_stock_piler' 	=> $filter_stock_piler,
									'filter_doc_no' 		=> $filter_doc_no,
									'filter_sku' 			=> $filter_sku,
									'sort'					=> $sort,
									'order'					=> $order
								);

		$this->data['lock_tag'] = Paginator::make($results, $results_total, 30);
		$this->data['lock_tag_count'] = $results_total;

		$this->data['counter'] 	= $this->data['lock_tag']->getFrom();


		$this->data['filter_stock_piler'] = $filter_stock_piler;
		$this->data['filter_doc_no'] = $filter_doc_no;
		$this->data['filter_sku'] = $filter_sku;

		$this->data['sort'] = $sort;
		$this->data['order'] = $order;
		$this->data['page'] = $page;

		$url = '?filter_stock_piler=' . $filter_stock_piler . '&filter_doc_no=' .$filter_doc_no . '&filter_sku=' . $filter_sku. '&page=' . $page ;
		$order_lock_tag = ($sort=='lock_tag' && $order=='ASC') ? 'DESC' : 'ASC';

		$this->data['sort_lock_tag'] = URL::to('picking/locktags' . $url . '&sort=lock_tag&order=' . $order_lock_tag, NULL, FALSE);
		// Permissions
		$this->data['permissions'] = unserialize(Session::get('permissions'));

		$this->layout->content = View::make('picking.locklist', $this->data);

	}

	public function getLockTagDetail()
	{
		$this->checkPermissions('CanViewPickingLockTags', false);

		$this->data['heading_title_picking_lock_tags'] = Lang::get('picking.heading_title_picking_lock_tags');

		$this->data['text_empty_results'] = Lang::get('general.text_empty_results');
		$this->data['text_total'] = Lang::get('general.text_total');
		$this->data['text_select'] = Lang::get('general.text_select');
		$this->data['text_warning_unlock_single'] = Lang::get('picking.text_warning_unlock_single');

		$this->data['col_doc_number'] = Lang::get('picking.col_doc_number');
		$this->data['col_upc'] = Lang::get('picking.col_upc');
		$this->data['col_product_name'] = Lang::get('picking.col_product_name');
		$this->data['col_store_code'] = Lang::get('picking.col_store_code');
		$this->data['col_store'] = Lang::get('picking.col_store');

		$this->data['button_back'] = Lang::get('picking.button_back_lock_tags');
		$this->data['button_unlock_tag'] = Lang::get('picking.button_unlock_tag');

		$this->data['url_back']= 	URL::to('picking/locktags'. $this->setURLLock(false, true));
		$this->data['url_unlock']= 	URL::to('picking/unlock');


		// Message
		$this->data['error'] = '';
		if (Session::has('error')) {
			$this->data['error'] = Session::get('error');
		}

		$this->data['success'] = '';
		if (Session::has('success')) {
			$this->data['success'] = Session::get('success');
		}

		$lockTag = Input::get('lock_tag', NULL);
		$this->data['lock_tag'] = $lockTag;

		$this->data['filter_stock_piler'] = Input::get('filter_stock_piler', NULL);
		$this->data['filter_doc_no'] = Input::get('filter_doc_no', NULL);
		$this->data['filter_sku'] = Input::get('filter_sku', NULL);

		$this->data['sort_back'] = Input::get('sort_back', 'lock_tag');
		$this->data['order_back'] = Input::get('order_back', 'ASC');
		$this->data['page_back'] = Input::get('page_back', 1);

		$results = PicklistDetails::getLockTagDetails($lockTag);
		$resultsTotal = count($results['details']); // since there is no pagination

		$this->data['lock_tag_details'] = $results['details'];
		$this->data['sum_moved']= $results['sum_moved'];
		$this->data['sum_moved_qty']= $results['sum_moved_qty'];
		$this->data['lock_tag_details_count'] = $resultsTotal;
		// Permissions
		$this->data['permissions'] = unserialize(Session::get('permissions'));

		$this->layout->content = View::make('picking.locklist_details', $this->data);
	}

	/**
	* Unlock picklist lock tags
	*
	* @example  www.example.com/picking/unlock
	*
	* @param  lock_tag  lock tag
	* @return void
	*/
	public function unlockPicklistTag()
	{
		try {
			$data = Input::all();
			if(!isset($data['lock_tag'])) throw new Exception("Lock tag empty.");
			$lockTags = explode(',',$data['lock_tag']);
			if(empty($lockTags)) throw new Exception("Lock tag empty.");
			DB::beginTransaction();
			PicklistDetails::unlockTag($lockTags);
			self::unlockPicklistTagAuditTrail($lockTags);
			DB::commit();
			return Redirect::to('picking/locktags'. $this->setURLLock())->with('message', Lang::get('picking.text_success_unlock'));
		} catch (Exception $e) {
			DB::rollback();
			return Redirect::to('picking/locktags'. $this->setURLLock())->withErrors(Lang::get('picking.text_fail_unlock'));
		}

	}

	/**
	* Change picklist to store
	*
	* @example  www.example.com/picking/change_to_store
	*
	* @param  picklist_doc_no      Picklist document number
	* @return void
	*/
	public function changeToStore()
	{
		try {
			$data = Input::all();
			DB::beginTransaction();
			if(!isset($data['picklist_doc_no'])) throw new Exception("Document number empty.");
			$docNo = explode(',', $data['picklist_doc_no']);
			Picklist::changeToStore($docNo);
			self::changeToStoreAuditTrail($docNo);
			DB::commit();
			return Redirect::to('picking/list'. $this->setURL())->with('message', Lang::get('picking.text_success_change'));
		} catch (Exception $e) {
			DB::rollback();
			return Redirect::to('picking/list'. $this->setURL())->withErrors(Lang::get('picking.text_fail_change'));
		}

	}

	public function loadPicklistDocuments()
	{
		try {
			$data = Input::all();

			if(!isset($data['picklist_docs'])) throw new Exception("Document number empty.");
			$picklistDocs = explode(',', $data['picklist_docs']);
			$loadCode =$data['load_codes'];
			DB::beginTransaction();
			foreach ($picklistDocs as $picklist) {

				//get boxes for the picklist document
				$picklistInfo = Picklist::getPickList($picklist);
				if(empty($picklistInfo)) throw new Exception("Picklist does not exist");
				StoreOrder::updateLoadCode($picklistInfo['so_no'], $loadCode);

				$pallete = Pallet::getOrCreatePallete($picklistInfo['store_code'], $loadCode);
				$boxes = BoxDetails::getBoxesByPicklistDetail($picklist);
				DebugHelper::logVar(__METHOD__, $boxes);
				foreach ($boxes as $box) {
					PalletDetails::create(array(
						'box_code' 		=> $box,
						'pallet_code'	=> $pallete['pallet_code']
						));
					Box::updateBox(array(
						"box_code"	=> $box,
						"store"		=> $picklistInfo['store_code'],
						"in_use"	=> Config::get('box_statuses.in_use')
						));
				}
				Picklist::where('move_doc_number', '=', $picklist)
					->update(array(
						'pl_status'	=> Config::get('picking_statuses.closed'),
						'updated_at' => date('Y-m-d H:i:s')));
			}
			self::loadPicklistDocumentsAuditTrail($picklistDocs, $loadCode);
			DB::commit();

			return Redirect::to('picking/list'. $this->setURL())->with('message', Lang::get('picking.text_success_load'));
		} catch (Exception $e) {
			DB::rollback();
			return Redirect::to('picking/list'. $this->setURL())->withErrors(Lang::get('picking.text_fail_load'));
		}
	}

	/**
	* Generate Load Code
	*
	* @example  www.example.com/picking/new/load
	*
	* @return load code
	*/
	/*public function generateLoadCode()
	{
		$loadMax =  Load::select(DB::raw('max(id) as max_created, max(load_code) as load_code'))->first()->toArray();
		;

		if($loadMax['max_created'] === null) {
			$loadCode = 'LD0000001';
		} else {
			$loadCode = substr($loadMax['load_code'], -7);
			$loadCode = (int) $loadCode + 1;
			$loadCode = 'LD' . sprintf("%07s", (int)$loadCode);
		}

		Load::create(array(
			'load_code'	=> $loadCode)
			);
		$load = Load::where('load_code', '=',$loadCode)->first()->toArray();
		self::generateLoadCodeAuditTrail($loadCode);
		echo json_encode($load);
		die();
	}*/

	protected function getLoadCodes()
	{
		$loadCodes = Load::getLoadCodes();
		return $loadCodes;
	}

	protected function checkPermissions($permission)
	{
		if (Session::has('permissions')) {
	    	if (!in_array($permission, unserialize(Session::get('permissions'))))  {
	    		return Redirect::to('picking/list');
			}
    	} else {
			return Redirect::to('users/logout');
		}
	}

	protected function setURL($forDetail = false, $forBackToList = false) {
		// Search Filters
		// http://local.ccri.com/picking/list?filter_doc_no=&filter_status=&filter_store=26&sort=doc_no&order=ASC
		$url = '?filter_type=' . Input::get('filter_type', NULL);
		$url .= '&filter_doc_no=' . Input::get('filter_doc_no', NULL);
		$url .= '&filter_status=' . Input::get('filter_status', NULL);
		$url .= '&filter_store=' . Input::get('filter_store', NULL);
		$url .= '&filter_stock_piler=' . Input::get('filter_stock_piler', NULL);
		// $url .= '&filter_sku=' . Input::get('filter_sku', NULL);
		// $url .= '&filter_upc=' . Input::get('filter_upc', NULL);
		if($forDetail) {
			$url .= '&sort_back=' . Input::get('sort', 'doc_no');
			$url .= '&order_back=' . Input::get('order', 'ASC');
			$url .= '&page_back=' . Input::get('page', 1);
		} else {
			if($forBackToList == true) {
				$url .= '&sort=' . Input::get('sort_back', 'doc_no');
				$url .= '&order=' . Input::get('order_back', 'ASC');
				$url .= '&page=' . Input::get('page_back', 1);
			} else {
				$url .= '&sort=' . Input::get('sort', 'doc_no');
				$url .= '&order=' . Input::get('order', 'ASC');
				$url .= '&page=' . Input::get('page', 1);
			}
		}
		return $url;
	}

	protected function setURLLock($forDetail = false, $forBackToList = false) {
		// Search Filters
		$url = '?filter_stock_piler=' . Input::get('filter_stock_piler', NULL);
		$url .= '&filter_doc_no=' . Input::get('filter_doc_no', NULL);
		$url .= '&filter_sku=' . Input::get('filter_sku', NULL);

		if($forDetail) {
			$url .= '&page_back=' . Input::get('page', 1);
			$url .= '&sort_back=' . Input::get('sort', 'lock_tag');
			$url .= '&order_back=' . Input::get('order', 'ASC');
		} else {
			if($forBackToList == true) {
				$url .= '&page=' . Input::get('page_back', 1);
				$url .= '&sort=' . Input::get('sort_back', 'lock_tag');
				$url .= '&order=' . Input::get('order_back', 'ASC');
			} else {
				$url .= '&page=' . Input::get('page', 1);
				$url .= '&sort=' . Input::get('sort', 'lock_tag');
				$url .= '&order=' . Input::get('order', 'ASC');
			}
		}

		return $url;
	}

	/**
	* Gets stock piler for drop down
	*
	* @example  $this->getStockPilers();
	*
	* @return array of stock piler and drop down initial text;
	*/
	private function getStockPilers()
	{
		$stock_pilers = array();
		foreach (User::getStockPilerOptions() as $item) {
			$stock_pilers[$item->id] = $item->firstname . ' ' . $item->lastname;
		}
		return array('' => Lang::get('general.text_select')) + $stock_pilers;
	}

	/**
	* Audit trail for picklist
	*
	* @example  self::unlockPicklistTagAuditTrail
	*
	* @param  $lockTags lock tags
	* @return void
	*/
	private function unlockPicklistTagAuditTrail($lockTags)
	{
		$lockTags = implode(',', $lockTags);
		$data_after = 'Locktags# '.$lockTags . ' unlocked by' . Auth::user()->username;
		$arrParams = array(
			'module'		=> Config::get("audit_trail_modules.picking"),
			'action'		=> Config::get("audit_trail.unlock_picklist_tag"),
			'reference'		=> 'Locktags # ' . $lockTags,
			'data_before'	=> '',
			'data_after'	=> $data_after,
			'user_id'		=> Auth::user()->id,
			'created_at'	=> date('Y-m-d H:i:s'),
			'updated_at'	=> date('Y-m-d H:i:s')
		);
		AuditTrail::addAuditTrail($arrParams);
	}

	/**
	* Audit trail for generating load code
	*
	* @example  self::generateLoadCodeAuditTrail()
	*
	* @param  $loadCodeload code
	* @return void
	*/
	/*private function generateLoadCodeAuditTrail($loadCode)
	{
		$data_after = 'Load code # '.$loadCode . ' generated by' . Auth::user()->username;
		$arrParams = array(
			'module'		=> Config::get("audit_trail_modules.picking"),
			'action'		=> Config::get("audit_trail.generate_load_code"),
			'reference'		=> 'Load code # ' . $loadCode,
			'data_before'	=> '',
			'data_after'	=> $data_after,
			'user_id'		=> Auth::user()->id,
			'created_at'	=> date('Y-m-d H:i:s'),
			'updated_at'	=> date('Y-m-d H:i:s')
		);
		AuditTrail::addAuditTrail($arrParams);
	}*/


	/**
	* Audit trail for picklist change type to store
	*
	* @example  self::changeToStoreAuditTrail()
	*
	* @param  $picklistDocNo picklist document number
	* @return void
	*/
	private function changeToStoreAuditTrail($picklistDocNo)
	{
		$picklistDocNo = implode(',', $picklistDocNo);
		$data_after = 'Picklist document # '.$picklistDocNo . '  change to type store by ' . Auth::user()->username;
		$arrParams = array(
			'module'		=> Config::get("audit_trail_modules.picking"),
			'action'		=> Config::get("audit_trail.picklist_change_to_store"),
			'reference'		=> 'Picklist document # ' . $picklistDocNo,
			'data_before'	=> '',
			'data_after'	=> $data_after,
			'user_id'		=> Auth::user()->id,
			'created_at'	=> date('Y-m-d H:i:s'),
			'updated_at'	=> date('Y-m-d H:i:s')
		);
		AuditTrail::addAuditTrail($arrParams);
	}

	/**
	* Audit trail for picklist loading
	*
	* @example  self::loadPicklistDocumentsAuditTrail()
	*
	* @param  $picklistDocNos 	picklist document numbers
	* @param  $loadCode 		load code
	* @return void
	*/
	/*private function loadPicklistDocumentsAuditTrail($picklistDocNos, $loadCode)
	{
		$picklistDocNos = implode(',', $picklistDocNos);
		$data_after = 'Picklist document # '.$picklistDocNos . '  loaded to Load # ' . $loadCode .' by '. Auth::user()->username;
		$arrParams = array(
			'module'		=> Config::get("audit_trail_modules.picking"),
			'action'		=> Config::get("audit_trail.picklist_load"),
			'reference'		=> 'Picklist documents # ' . $picklistDocNos,
			'data_before'	=> '',
			'data_after'	=> $data_after,
			'user_id'		=> Auth::user()->id,
			'created_at'	=> date('Y-m-d H:i:s'),
			'updated_at'	=> date('Y-m-d H:i:s')
		);
		AuditTrail::addAuditTrail($arrParams);
	}*/

	public function assignPilerForm() {
		if (Session::has('permissions')) {
	    	if (!in_array('CanAssignPacking', unserialize(Session::get('permissions'))))  {
	    		return Redirect::to('purchase_order');
			}
    	} else {
			return Redirect::to('users/logout');
		}
		$this->data                     = Lang::get('picking');
		$this->data['doc_no']           = Input::get('doc_no');

		$this->data['stock_piler_list'] = $this->getStockPilers();
		$this->data['button_assign']    = Lang::get('general.button_assign');
		$this->data['button_cancel']    = Lang::get('general.button_cancel');
		$this->data['url_back']         = URL::to('picking/list');
		$this->data['params']           = explode(',', Input::get('doc_no'));
		$this->data['info']             = Picklist::getInfoByDocNos($this->data['params']);

		$this->layout->content          = View::make('picking.assign_piler_form', $this->data);
	}

	/**
	* Assign stock piler to purchase order
	*
	* @example  www.example.com/purchase_order/assign_to_piler
	*
	* @param  po_no         int    Purchase order number
	* @param  stock_piler   int    Stock piler id
	* @return Status
	*/
	public function assignToStockPiler() {
		// Check Permissions
		$pilers = implode(',' , Input::get('stock_piler'));


		//get moved_to_reserve id
		$arrParams = array('data_code' => 'PICKLIST_STATUS_TYPE', 'data_value'=> 'assigned');
		$picklistStatus = Dataset::getType($arrParams)->toArray();

		$arrDocNo = explode(',', Input::get("doc_no"));

		foreach ($arrDocNo as $docNo) {
			$arrParams = array(
								'assigned_by' 			=> Auth::user()->id,
								'assigned_to_user_id' 	=> $pilers, //Input::get('stock_piler'),
								'pl_status' 			=> $picklistStatus['id'], //assigned
								'updated_at' 			=> date('Y-m-d H:i:s')
							);
			Picklist::assignToStockPiler($docNo, $arrParams);

			// AuditTrail
			$users = User::getUsersFullname(Input::get('stock_piler'));

			$fullname = implode(', ', array_map(function ($entry) { return $entry['name']; }, $users));

			$data_before = '';
			$data_after = 'Picklist Doc No: ' . $docNo . ' assigned to ' . $fullname;

			$arrParams = array(
							'module'		=> Config::get('audit_trail_modules.picking'),
							'action'		=> Config::get('audit_trail.assign_picklist'),
							'reference'		=> $docNo,
							'data_before'	=> $data_before,
							'data_after'	=> $data_after,
							'user_id'		=> Auth::user()->id,
							'created_at'	=> date('Y-m-d H:i:s'),
							'updated_at'	=> date('Y-m-d H:i:s')
							);
			AuditTrail::addAuditTrail($arrParams);
			// AuditTrail
		}


		return Redirect::to('picking/list' . $this->setURL())->with('message', Lang::get('picking.text_success_assign'));

	}

	public function closePicklist()
	{
		// Check Permissions
		/*if (Session::has('permissions')) {
	    	if (!in_array('CanClosePurchaseOrders', unserialize(Session::get('permissions'))) || !in_array('CanClosePurchaseOrderDetails', unserialize(Session::get('permissions'))))  {
				return Redirect::to('user/profile');
			}
    	} else {
			return Redirect::to('users/logout');
		}*/

		$docNo        = Input::get("doc_no");
		$status       = 'posted'; // closed
		$date_updated = date('Y-m-d H:i:s');

		$status_options = Dataset::where("data_code", "=", "PICKLIST_STATUS_TYPE")->get()->lists("id", "data_value");

		/*$picklist = Picklist::where('move_doc_number', '=', $docNo)
				->update(array(
					"pl_status" => $status_options[$status_value],
					"updated_at" => date('Y-m-d H:i:s')
				));*/

		$picklist = Picklist::updateStatus($docNo, $status_options['closed']);

		// for jda transaction TODOS
		/*$closePicklist = BoxDetails::checkBoxesPerDocNo($boxInfo['move_doc_number']);

		if (! $closePicklist->isEmpty() || $useBox)
		{
			$isSuccess = Picklist::where('move_doc_number', '=', $boxInfo['move_doc_number'])
				->update(array(
					'pl_status'	=> Config::get('picking_statuses.closed'),
					'updated_at' => date('Y-m-d H:i:s')));
			DebugHelper::log(__METHOD__, $isSuccess);
			if($isSuccess) self::createJdaTransaction($boxInfo);
		}*/

		// AuditTrail
		$user = User::find(Auth::user()->id);

		$data_before = '';
		$data_after = 'Picklist Document No: ' . $docNo . ' posted by ' . $user->username;

		$arrParams = array(
						'module'		=> Config::get("audit_trail_modules.picking"),
						'action'		=> Config::get("audit_trail.modify_picklist_status"),
						'reference'		=> $docNo,
						'data_before'	=> $data_before,
						'data_after'	=> $data_after,
						'user_id'		=> Auth::user()->id,
						'created_at'	=> date('Y-m-d H:i:s'),
						'updated_at'	=> date('Y-m-d H:i:s')
						);
		AuditTrail::addAuditTrail($arrParams);
		// AuditTrail

		// Add transaction for jda syncing
		$isSuccess = JdaTransaction::insert(array(
			'module' 		=> Config::get('transactions.module_purchase_order'),
			'jda_action'	=> Config::get('transactions.jda_action_po_closing'),
			'reference'		=> $docNo
		));

		// run daemon command: php app/cron/jda/classes/receive_po.php
		/*if( $isSuccess )
		{
			$daemonReceivingClosingPo = "classes/receive_po.php {$purchase_order_no}";
			CommonHelper::execInBackground($daemonReceivingClosingPo);
		}*/

		return Redirect::to('picking/list' . $this->setURL())->with('message', Lang::get('picking.text_success_posted'));
	}
}
