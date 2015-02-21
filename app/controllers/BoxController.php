<?php

class BoxController extends BaseController {
	private $data = array();
	protected $layout = "layouts.main";
	private $user;

	public function __construct() {
		$this->beforeFilter('csrf', array('on' => 'post'));
		$this->beforeFilter('auth', array('only'=> array('Dashboard')));
		$this->apiUrl = Config::get('constant.api_url');
		$this->user = User::find(Auth::user()->id);
		date_default_timezone_set('Asia/Manila');
	}

	public function index()
	{
		if (Session::has('permissions')) {
	    	if (!in_array('CanAccessBoxingLoading', unserialize(Session::get('permissions')))){
	    			return Redirect::to('user/profile');
			}
    	} else {
			return Redirect::to('users/logout');
		}

		$this->getList();
	}

	public function getList()
	{

		$this->data['heading_title'] = Lang::get('box.heading_title');

		$this->data['entry_load'] = Lang::get('box.entry_load');
		$this->data['entry_load_create'] = Lang::get('box.entry_load_create');

		$this->data['text_empty_results'] 	= Lang::get('general.text_empty_results');
		$this->data['text_total'] 			= Lang::get('general.text_total');
		$this->data['text_select'] 			= Lang::get('general.text_select');
		$this->data['text_confirm_delete'] 	= Lang::get('box.text_confirm_delete');
		$this->data['text_confirm_delete_single'] 	= Lang::get('box.text_confirm_delete_single');
		$this->data['text_confirm_load'] 	= Lang::get('box.text_confirm_load');

		$this->data['button_create_box'] 	= Lang::get('box.button_create_box');
		$this->data['button_export_box'] 	= Lang::get('box.button_export_box');
		$this->data['button_delete_box'] 	= Lang::get('box.button_delete_box');
		$this->data['button_load'] 			= Lang::get('box.button_load');
		$this->data['button_add_store'] = Lang::get('box.button_add_store');

		$this->data['label_store'] 		= Lang::get('box.label_store');
		$this->data['label_box_code'] 	= Lang::get('box.label_box_code');
		$this->data['label_load_code'] = Lang::get('box.label_load_code');
		// $this->data['label_doc_no'] = Lang::get('box.label_doc_no');

		$this->data['col_id'] 			= Lang::get('box.col_id');
		$this->data['col_store'] 		= Lang::get('box.col_store');
		$this->data['col_box_code'] 	= Lang::get('box.col_box_code');
		$this->data['col_date_created'] = Lang::get('box.col_date_created');
		$this->data['col_action'] 		= Lang::get('box.col_action');

		$this->data['button_search'] 	= Lang::get('general.button_search');
		$this->data['button_clear'] 	= Lang::get('general.button_clear');

		$this->data['error_delete'] 	= Lang::get('box.error_delete');
		$this->data['error_load'] 		= Lang::get('box.error_load');
		$this->data['error_load_no_load_code'] = Lang::get('box.error_load_no_load_code');
		$this->data['load_codes']		= $this->getLoadCodes();


		// Message
		$this->data['error'] = '';
		if (Session::has('error')) $this->data['error'] = Session::get('error');

		$this->data['success'] = '';
		if (Session::has('success')) $this->data['success'] = Session::get('success');

		$filter_store 		= Input::get('filter_store', NULL);
		$filter_box_code	= Input::get('filter_box_code', NULL);

		$sort = Input::get('sort', 'box_code');
		$order = Input::get('order', 'ASC');
		$page = Input::get('page', 1);


		$arrParams = array(
						'filter_store' 			=> $filter_store,
						'filter_box_code' 		=> $filter_box_code,
						'sort'					=> $sort,
						'order'					=> $order,
						'page'					=> $page,
						'limit'					=> 30
					);

		$results 		= Box::getBoxesWithFilters($arrParams)->toArray();
		// echo '<pre>'; dd($results);
		// echo '<pre>';
		// print_r($results);
		$results_total 	= Box::getBoxesCount($arrParams);



		$this->data['arrFilters'] = array(
									'filter_store' 			=> $filter_store,
									'filter_box_code' 		=> $filter_box_code,
									'sort'					=> $sort,
									'order'					=> $order
								);


		$this->data['boxes'] = Paginator::make($results, $results_total, 30);
		$this->data['boxes_count'] = $results_total;

		$this->data['counter'] 	= $this->data['boxes']->getFrom();

		$this->data['filter_store'] = $filter_store;
		$this->data['filter_box_code'] = $filter_box_code;

		$this->data['url_add_box'] = URL::to('box/create' . $this->setURL());
		$this->data['url_update_box'] =  URL::to('box/update' . $this->setURL());
		$this->data['url_delete_box'] =  URL::to('box/delete' . $this->setURL());
		$this->data['url_export_box'] =  URL::to('box/export' . $this->setURL());
		$this->data['url_detail']   = URL::to('box/detail' . $this->setURL(TRUE));
		$this->data['url_load']	= URL::to('box/load');
		$this->data['url_generate_load_code']	= URL::to('box/new/load');

		$this->data['sort'] = $sort;
		$this->data['order'] = $order;
		$this->data['page'] = $page;

		$url = '?filter_store=' . $filter_store . '&filter_box_code=' . $filter_box_code;
		$url .= '&page=' . $page;

		$order_store = ($sort=='store' && $order=='ASC') ? 'DESC' : 'ASC';
		$order_box_code = ($sort=='box_code' && $order=='ASC') ? 'DESC' : 'ASC';
		$order_date_created = ($sort=='date_created' && $order=='ASC') ? 'DESC' : 'ASC';

		$this->data['sort_store'] = URL::to('box/list' . $url . '&sort=store&order=' . $order_store, NULL, FALSE);
		$this->data['sort_box_code'] = URL::to('box/list' . $url . '&sort=box_code&order=' . $order_box_code, NULL, FALSE);
		$this->data['sort_date_created'] = URL::to('box/list' . $url . '&sort=date_created&order=' . $order_date_created, NULL, FALSE);

		$this->data['permissions'] = unserialize(Session::get('permissions'));

		$this->layout->content = View::make('box.list', $this->data);

	}

	public function getBoxDetails() {
		// Check Permissions

		$this->data = Lang::get('box');
		$this->data['text_empty_results'] = Lang::get('general.text_empty_results');
		$this->data['text_total'] = Lang::get('general.text_total');
		$this->data['button_jda'] = Lang::get('general.button_jda');
		$this->data['button_export'] = Lang::get('general.button_export');
		$this->data['button_cancel'] = Lang::get('general.button_cancel');
		$this->data['button_search'] = Lang::get('general.button_search');
		$this->data['button_clear'] = Lang::get('general.button_clear');
		// URL
		$this->data['url_export'] = URL::to('box/export_detail');
		$this->data['url_back'] = URL::to('box/list' . $this->setURL());

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
		$filter_sku = Input::get('filter_sku', NULL);
		$filter_store = Input::get('filter_store', NULL);

		$filter_box_code = Input::get('filter_box_code', NULL);
		$sort_back = Input::get('sort_back', 'sku');
		$order_back = Input::get('order_back', 'ASC');
		$page_back = Input::get('page_back', 1);

		// Details
		$sort_detail = Input::get('sort', 'sku');
		$order_detail = Input::get('order', 'ASC');
		$page_detail = Input::get('page', 1);

		//Data
		$box_id = Input::get('id', NULL);
		// $this->data['letdown_info'] = Letdown::getLetDownInfo($letdown_id);
		$box_code = Input::get('box_code', NULL);

		$arrParams = array(
						'sort'		=> $sort_detail,
						'order'		=> $order_detail,
						'page'		=> $page_detail,
						'filter_sku' => $filter_sku,
						'filter_store' => $filter_store,
						'filter_box_code'	=> $filter_box_code,
						'limit'		=> 30
					);
		// dd($box_code);
		$results 		= BoxDetails::getBoxDetails($box_code, $arrParams);
		DebugHelper::log(__METHOD__, $results);
		$results_total 	= BoxDetails::getBoxDetails($box_code, $arrParams, true);
		// Pagination
		$this->data['arrFilters'] = array(
									'filter_box_code' => $filter_box_code,
									'page_back'		=> $page_back,
									'sort_back'		=> $sort_back,
									'order_back'	=> $order_back,
									'filter_sku'	=> $filter_sku,
									'filter_store' 	=> $filter_store,
									// 'filter_slot'	=> $filter_slot,
									'sort'			=> $sort_detail,
									'order'			=> $order_detail,
									'box_code'		=> $box_code,
									'id'			=> $box_id
								);

		$this->data['boxes'] = Paginator::make($results, $results_total, 30);
		$this->data['boxes_count'] = $results_total;

		$this->data['counter'] 	= $this->data['boxes']->getFrom();
		$this->data['box_id'] = $box_id;
		$this->data['box_code'] = $box_code;
		// Main
		$this->data['filter_sku'] = $filter_sku;
		$this->data['filter_store'] = $filter_store;
		$this->data['filter_box_code'] = $filter_box_code;

		$this->data['sort'] = $sort_detail;
		$this->data['order'] = $order_detail;
		$this->data['page'] = $page_detail;

		// Details
		$this->data['sort_back'] 	= $sort_back;
		$this->data['order_back'] 	= $order_back;
		$this->data['page_back'] 	= $page_back;

		$url = '?filter_sku=' . $filter_sku . '&filter_store=' . $filter_store . '&filter_box_code='. $filter_box_code;
		$url .= '&page_back=' . $page_back . '&sort_back=' . $sort_back . '&order_back=' . $order_back .'&id=' . $box_id . '&box_code= '.$box_code ;


		$order_sku = ($sort_detail=='sku' && $order_detail=='ASC') ? 'DESC' : 'ASC';
		$order_store = ($sort_detail=='store' && $order_detail=='ASC') ? 'DESC' : 'ASC';
		$order_box_code = ($sort_detail=='box_code' && $order_detail=='ASC') ? 'DESC' : 'ASC';


		$this->data['sort_sku'] = URL::to('box/detail' . $url . '&sort=sku&order=' . $order_sku, NULL, FALSE);
		$this->data['sort_box_code'] = URL::to('box/detail' . $url . '&sort=box_code&order=' . $order_box_code, NULL, FALSE);
		$this->data['sort_store'] = URL::to('box/detail' . $url . '&sort=store&order=' . $order_store, NULL, FALSE);

		// Permissions
		$this->data['permissions'] = unserialize(Session::get('permissions'));
		$this->data['url_detail'] = URL::to('box/detail');

		$this->layout->content = View::make('box.detail', $this->data);
	}

	public function loadBoxes()
	{
		try {
			$data = Input::all();
			if(!isset($data['box_lists'])) throw new Exception("Box empty.");
			$boxLists = explode(',', $data['box_lists']);
			$loadCode = $data['load_codes'];

			DB::beginTransaction();

			foreach ($boxLists as $boxCode)
			{
				//get boxes info
				$boxInfo = Box::getBoxList($boxCode);
				if(empty($boxInfo)) throw new Exception("Box code does not exist");
				$soNos = array_unique(explode(',', $boxInfo['so_no'])); //remove duplicate so_no
				StoreOrder::updateLoadCode($soNos, $loadCode);

				$pallete = Pallet::getOrCreatePallete($boxInfo['store_code'], $loadCode);
				PalletDetails::create(array(
					'box_code' 		=> $boxCode, //$boxInfo['box_code'],
					'pallet_code'	=> $pallete['pallet_code']
					));
				$useBox = Box::updateBox(array(
					"box_code"	=> $boxInfo['box_code'],
					"store"		=> $boxInfo['store_code'],
					"in_use"	=> Config::get('box_statuses.in_use')
					));
			}
			self::loadBoxesAuditTrail($boxLists, $loadCode);
			DB::commit();

			return Redirect::to('box/list'. $this->setURL())->with('message', Lang::get('box.text_success_load'));
		} catch (Exception $e) {
			DB::rollback();
			return Redirect::to('box/list'. $this->setURL())->withErrors(Lang::get('box.text_fail_load'));
		}
	}

	protected function createJdaTransaction($data)
	{
		$picklistParams = array(
			'module' 		=> Config::get('transactions.module_picklist'),
			'jda_action'	=> Config::get('transactions.jda_action_picklist'),
			'reference'		=> $data['move_doc_number']
		);
		//create jda transaction for picklist closing
		JdaTransaction::insert($picklistParams);
		Log::info(__METHOD__ .' dump: '.print_r($data['move_doc_number'],true));
		$getUniqueBox = BoxDetails::getUniqueBoxPerDocNo($data['move_doc_number']);
		Log::info(__METHOD__ .' dump: '.print_r($getUniqueBox,true));
		$boxParams = array(
			'module' 		=> Config::get('transactions.module_box'),
			'jda_action'	=> Config::get('transactions.jda_action_box'),
			'reference'		=> $getUniqueBox['box_code']
		);
		//create jda transaction for box header
		$boxResp = JdaTransaction::insert($boxParams);

		if(is_array($getUniqueBox)) {

			$getPallet = PalletDetails::getPallet($getUniqueBox['box_code']);
			$palletParams = array(
				'module' 		=> Config::get('transactions.module_pallet'),
				'jda_action'	=> Config::get('transactions.jda_action_pallet'),
				'reference'		=> $getPallet['pallet_code']
			);
			//create jda transaction for pallet header
			$palletResp = JdaTransaction::insert($palletParams);
		}

		$getLoad = LoadDetails::getLoad($getPallet['pallet_code']);
		$loadParams = array(
			'module' 		=> Config::get('transactions.module_load'),
			'jda_action'	=> Config::get('transactions.jda_action_load'),
			'reference'		=> $getLoad['load_code']
		);
		//create jda transaction for load header
		$loadResp = JdaTransaction::insert($loadParams);

		$palletizeBoxParams = array(
			'module' 		=> Config::get('transactions.module_palletize_box'),
			'jda_action'	=> Config::get('transactions.jda_action_palletize_box'),
			'reference'		=> $getPallet['pallet_code']
		);
		//create jda transaction for pallete to box
		$palletBoxResp = JdaTransaction::insert($palletizeBoxParams);


		$loadingParams = array(
			'module' 		=> Config::get('transactions.module_loading'),
			'jda_action'	=> Config::get('transactions.jda_action_loading'),
			'reference'		=> $getLoad['load_code']
		);
		//create jda transaction for loading
		$insertLoad 		= JdaTransaction::insert($loadingParams);

		$docNo 				= $data['move_doc_number'];
		$boxNo 				= $getUniqueBox['box_code'];
		$palletNo 			= $getPallet['pallet_code'];
		$loadNo 			= $getLoad['load_code'];
	}

	protected function getLoadCodes()
	{
		$loadCodes = Load::getLoadCodes();
		return $loadCodes;
	}

	/**
	* Generate Load Code
	*
	* @example  www.example.com/picking/new/load
	*
	* @return load code
	*/
	public function generateLoadCode()
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
	}

	public function createBox()
	{
		self::checkPermissions('CanAccessBoxingLoading');
		$this->data['heading_title_add'] = Lang::get('box.heading_title_add');

		$this->data['entry_store'] = Lang::get('box.entry_store');
		$this->data['entry_box_code'] = Lang::get('box.entry_box_code');

		$this->data['text_confirm_create'] = Lang::get('box.text_confirm_create');
		$this->data['text_confirm_cancel'] = Lang::get('box.text_confirm_cancel');

		$this->data['button_submit'] = Lang::get('box.button_submit');
		$this->data['button_cancel'] = Lang::get('box.button_cancel');
		$this->data['button_back'] = Lang::get('box.button_back');

		$this->data['error_required_fields'] = Lang::get('box.error_required_fields');

		$this->data['url_back'] = URL::to('box/list' . $this->setURL());
		$this->data['url_create'] = 'box/create' . $this->setURL();

		$stores = Store::lists( 'store_name', 'store_code');
		$this->data['stores'] = $stores;

		$this->data['filter_store'] 	= Input::get('filter_store', NULL);
		$this->data['filter_box_code']	= Input::get('filter_box_code', NULL);

		$this->data['sort']  = Input::get('sort', 'store');
		$this->data['order'] = Input::get('order', 'ASC');
		$this->data['page']  = Input::get('page', 1);

		$this->data['error'] = '';
		if (Session::has('error')) $this->data['error'] = Session::get('error');

		$this->data['permissions'] = unserialize(Session::get('permissions'));

		$this->layout->content = View::make('box.create', $this->data);
	}

	public function updateBox()
	{
		self::checkPermissions('CanAccessBoxingLoading');
		$boxCode = Input::get('box_code');

		$this->data['heading_title_update'] = Lang::get('box.heading_title_update');

		$this->data['entry_store'] = Lang::get('box.entry_store');
		$this->data['entry_box_code'] = Lang::get('box.entry_box_code');

		$this->data['text_confirm_update'] = Lang::get('box.text_confirm_update');
		$this->data['text_confirm_cancel'] = Lang::get('box.text_confirm_cancel');

		$this->data['button_submit'] = Lang::get('box.button_submit');
		$this->data['button_cancel'] = Lang::get('box.button_cancel');
		$this->data['button_back'] = Lang::get('box.button_back');

		$this->data['error_required_fields'] = Lang::get('box.error_required_fields');

		$this->data['url_back'] = URL::to('box/list' . $this->setURL());
		$this->data['url_update'] = 'box/update'. $this->setURL();

		$stores = Store::lists( 'store_name', 'store_code');
		$this->data['stores'] = $stores;

		$this->data['box_details'] = Box::where('box_code', '=', $boxCode)->first();

		if($this->data['box_details'] === null) {
			return Redirect::to('box/list');
		}

		$this->data['filter_store'] 	= Input::get('filter_store', NULL);
		$this->data['filter_box_code']	= Input::get('filter_box_code', NULL);

		$this->data['sort']  = Input::get('sort', 'store');
		$this->data['order'] = Input::get('order', 'ASC');
		$this->data['page']  = Input::get('page', 1);

		$this->data['error'] = '';
		if (Session::has('error')) $this->data['error'] = Session::get('error');

		$this->data['permissions'] = unserialize(Session::get('permissions'));

		$this->layout->content = View::make('box.update', $this->data);
	}

	public function exportDetailsCSV() {
		//TODO
		///Check Permissions
		$box_code = Input::get('box_code', NULL);
		$this->data = Lang::get('box');
		$this->data['text_empty_results'] 	= Lang::get('general.text_empty_results');
		$arrParams = array(
						'sort'			=> Input::get('sort', 'sku'),
						'order'			=> Input::get('order', 'ASC'),
						'filter_sku' 	=> NULL,
						'filter_store' 	=> NULL,
						'filter_box_code' => NULL,
						'filter_status' => NULL,
						'page'			=> NULL,
						'limit'			=> NULL
					);

		// $ld_info = Letdown::getLetDownInfo($ld_id);
		$results = BoxDetails::getBoxDetails($box_code, $arrParams);

			$this->data['results'] = $results;

		$pdf = App::make('dompdf');
		$pdf->loadView('box.report_detail', $this->data)->setPaper('a4')->setOrientation('landscape');
		// return $pdf->stream();
		return $pdf->download('box_detail_' . date('Ymd') . '.pdf');
	}

	public function exportBoxes()
	{
		self::checkPermissions('CanExportBoxingLoading');
		$this->data = Lang::get('box');
		$this->data['text_empty_results'] 	= Lang::get('general.text_empty_results');
		$arrParams = array(
						'filter_store' 			=> Input::get('filter_store', NULL),
						'filter_box_code' 		=> Input::get('filter_box_code', NULL),
						'sort'					=> Input::get('sort', 'store'),
						'order'					=> Input::get('order', 'ASC'),
						'page'					=> NULL,
						'limit'					=> NULL
					);

		$results 		= Box::getBoxesWithFilters($arrParams)->toArray();
		$this->data['results'] = $results;

		$pdf = App::make('dompdf');
		$pdf->loadView('box.report_list', $this->data)->setPaper('a4')->setOrientation('landscape');
		// return $pdf->stream();
		return $pdf->download('box_' . date('Ymd') . '.pdf');
	}

	public function deleteBoxes()
	{
		self::checkPermissions('CanAccessBoxingLoading');
		try {
			$data = Input::all();
			DB::beginTransaction();
			$boxCodes = explode(",", Input::get('box_codes'));
			foreach ($boxCodes as $boxCode) {
				Box::deleteByBoxCode($boxCode);
			}
			self::deleteBoxesAuditTrail(Input::get('box_codes'));
			DB::commit();
			return Redirect::to(URL::to('box/list' . $this->setURL() ))->with("message", Lang::get('box.text_success_delete'));
		} catch (Exception $e) {
			DB::rollback();
			return Redirect::to(URL::to('box/list' . $this->setURL() ))->withErrors(Lang::get('box.text_fail_delete'));
		}
	}

	public function postUpdateBox()
	{
		self::checkPermissions('CanAccessBoxingLoading');
		try {
			$input = Input::all();
			$input['in_use'] = Config::get('box_statuses.not_in_use');
			DB::beginTransaction();
			$boxDetails = BoxDetails::getBoxDetailCount($input['box_code']);
			if($boxDetails > 0) throw new Exception("Box details already exists");
			Box::updateBox($input);
			$storeName = Store::getStoreName($input['store']);
			self::postUpdateBoxAuditTrail($input['box_code'], $storeName);
			DB::commit();
			return Redirect::to(URL::to('box/update' . $this->setURL(). '&box_code=' . $input['box_code']))->with('message', Lang::get('box.text_success_update'));
		} catch (Exception $e) {
			DB::rollback();
			return Redirect::to(URL::to('box/update' . $this->setURL() . '&box_code=' . $input['box_code']))->withErrors(Lang::get('box.text_fail_update') . ': ' . $input['box_code']);
		}

	}

	public function postCreateBox()
	{
		self::checkPermissions('CanAccessBoxingLoading');
		try {
			#box format: [{storecode}-00001] (eg 0005-00001)
			$input = Input::all();

			DB::beginTransaction();
			if(! is_numeric($input['box_range']) || (int) $input['box_range'] == 0 || $input['box_range'] < 0 ) throw new Exception ('Invalid input.');

			$storeCode = $input['store'];
			$numberOfBoxes = (int)$input['box_range'];

			if(strlen($storeCode) == 1) $newStoreCodeFormat = "000{$storeCode}-";
			else if(strlen($storeCode) == 2) $newStoreCodeFormat = "00{$storeCode}-";
			else if(strlen($storeCode) == 3) $newStoreCodeFormat = "0{$storeCode}-";
			else if(strlen($storeCode) == 4) $newStoreCodeFormat = "{$storeCode}-";
			else throw new Exception("Invalid store");

			#check if a record exist in that store
			$box = Box::where('box_code', 'LIKE', "{$newStoreCodeFormat}%")->max('box_code');
			#if result is empty follow the format
			if($box == null) $box = $newStoreCodeFormat."00000";
			#if exists get the latest then increment box
			$formattedBoxCode = array();
			$containerBox = array(); //use for audit trail
			foreach(range(1, $numberOfBoxes) as $number) {
				$boxCode = substr($box, -5);
				$boxCode = (int) $boxCode + $number;
				$formattedBoxCode[$number]['box_code'] = $newStoreCodeFormat . sprintf("%05s", (int)$boxCode);
				$formattedBoxCode[$number]['store_code'] = $storeCode;
				$formattedBoxCode[$number]['created_at'] = date('Y-m-d H:i:s');
				$containerBox[] = $newStoreCodeFormat . sprintf("%05s", (int)$boxCode);
			}

			Box::insert($formattedBoxCode);

			$storeName = Store::getStoreName($storeCode);
			$boxCodeInString = implode(',', $containerBox);

			self::postCreateBoxAuditTrail($boxCodeInString, $storeName);
			DB::commit();
			return Redirect::to(URL::to('box/create' . $this->setURL()))->with('message', Lang::get('box.text_success_create'));
		} catch (Exception $e) {
			DB::rollback();
			// return Redirect::to(URL::to('box/create' . $this->setURL()))->withErrors(Lang::get('box.text_fail_create') . ': ' . $input['store']);
			return Redirect::to(URL::to('box/create' . $this->setURL()))->withErrors($e->getMessage());
		}

	}


	protected function checkPermissions($permission)
	{
		if (Session::has('permissions')) {
	    	if (!in_array($permission, unserialize(Session::get('permissions'))))  {
				return Redirect::to('box/list' . $this->setURL());
			}
    	} else {
			return Redirect::to('users/logout');
		}
	}


	protected function setURL($detail = FALSE) {
		// Search Filters
		if(!$detail) {
			$url = '?filter_store=' . Input::get('filter_store', NULL);
			$url .= '&filter_box_code=' . Input::get('filter_box_code', NULL);
			$url .= '&sort=' . Input::get('sort', 'store');
			$url .= '&order=' . Input::get('order', 'ASC');
			$url .= '&page=' . Input::get('page', 1);
		}
		else {
			$url = '?filter_box_code=' . Input::get('filter_box_code', NULL);
			// $url .= '&filter_sku=' . Input::get('filter_sku', NULL);
			$url .= '&box_code=' . Input::get('box_code', 'NULL');
			$url .= '&sort=' . Input::get('sort', 'box_code');
			$url .= '&order=' . Input::get('order', 'ASC');
			$url .= '&page=' . Input::get('page', 1);
		}

		return $url;
	}

	protected function postCreateBoxAuditTrail($boxCode, $storeName)
	{
		$dataBefore = '';
		$dataAfter = 'User '. $this->user->username . ' created a box with code ' . $boxCode. ' for ' . $storeName;

		$arrParams = array(
						'module'		=> Config::get('audit_trail_modules.boxing'),
						'action'		=> Config::get('audit_trail.create_box'),
						'reference'		=> 'Box code # ' . $boxCode,
						'data_before'	=> $dataBefore,
						'data_after'	=> $dataAfter,
						'user_id'		=> Auth::user()->id,
						'created_at'	=> date('Y-m-d H:i:s'),
						'updated_at'	=> date('Y-m-d H:i:s')
						);
		AuditTrail::addAuditTrail($arrParams);
	}

	protected function postUpdateBoxAuditTrail($boxCode, $storeName)
	{
		$user = User::find(Auth::user()->id);
		$dataBefore = '';
		$dataAfter = 'User '. $this->user->username . ' reassigned box with code ' . $boxCode. ' to ' . $storeName;

		$arrParams = array(
						'module'		=> Config::get('audit_trail_modules.boxing'),
						'action'		=> Config::get('audit_trail.update_box'),
						'reference'		=> $boxCode,
						'data_before'	=> $dataBefore,
						'data_after'	=> $dataAfter,
						'user_id'		=> Auth::user()->id,
						'created_at'	=> date('Y-m-d H:i:s'),
						'updated_at'	=> date('Y-m-d H:i:s')
						);
		AuditTrail::addAuditTrail($arrParams);
	}

	protected function deleteBoxesAuditTrail($boxCode)
	{

		$dataBefore = '';
		$dataAfter = 'User '. $this->user->username . ' deleted box/es with code ' . $boxCode . '.';

		$arrParams = array(
						'module'		=> Config::get('audit_trail_modules.boxing'),
						'action'		=> Config::get('audit_trail.delete_box'),
						'reference'		=> $boxCode,
						'data_before'	=> $dataBefore,
						'data_after'	=> $dataAfter,
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
	private function generateLoadCodeAuditTrail($loadCode)
	{
		$data_after = 'Load code # '.$loadCode . ' generated by ' . Auth::user()->username;
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
	}

	/**
	* Audit trail for picklist loading
	*
	* @example  self::loadBoxesAuditTrail()
	*
	* @param  $boxCodes 	box codes
	* @param  $loadCode 		load code
	* @return void
	*/
	private function loadBoxesAuditTrail($boxCodes, $loadCode)
	{
		$boxCodes = implode(',', $boxCodes);
		$data_after = 'Box code # '.$boxCodes . '  loaded to Load # ' . $loadCode .' by '. Auth::user()->username;
		$arrParams = array(
			'module'		=> Config::get("audit_trail_modules.boxing"),
			'action'		=> Config::get("audit_trail.box_load"),
			'reference'		=> 'Box code # ' . $boxCodes,
			'data_before'	=> '',
			'data_after'	=> $data_after,
			'user_id'		=> Auth::user()->id,
			'created_at'	=> date('Y-m-d H:i:s'),
			'updated_at'	=> date('Y-m-d H:i:s')
		);
		AuditTrail::addAuditTrail($arrParams);
	}
}