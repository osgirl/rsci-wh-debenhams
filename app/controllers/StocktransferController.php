<?php

class StocktransferController extends \BaseController {


	private $data = array();
	protected $layout = "layouts.main";
 

	public function __construct() {
		$this->beforeFilter('csrf', array('on' => 'post'));
		$this->beforeFilter('auth', array('only'=> array('Dashboard')));
		$this->apiUrl = Config::get('constant.api_url');
		date_default_timezone_set('Asia/Manila');

		// $receiving = "classes/receive_po.php 20283";
		// CommonHelper::execInBackground($receiving);
	}

	public function getUpdateDate()
	{
	 
		$move_doc_number 	    = Input::get('move_doc_number', NULL);
	 	$ship_date 				= Input::get('filter_date_entry', null);
		
		StoreReturnPickinglist::getUpdateDateMod($move_doc_number, $ship_date);
		return Redirect::to('stocktransfer/PickAndPackStore?&move_doc_number='.$move_doc_number)->with('message','Ship Date Successfully Update!');
	}

	public function exportCSVasdf2fsdf()
	{ 
 

		$arrParams = array(
							'filter_entry_date' 		=> Input::get('filter_entry_date', NULL),
							'filter_doc_no' 	=> Input::get('filter_doc_no', NULL),
							'filter_status' 		=> Input::get('filter_status', NULL),
							'sort'					=> Input::get('sort', 'doc_no'),
							'order'					=> Input::get('order', 'ASC'),
							'page'					=> NULL,
							'limit'					=> NULL
						);
		$results = StoreReturnPickinglist::getStocktransferPickReport($arrParams);

  
	 
		$output = Lang::get('picking.col_doc_no'). ',';
		$output .= Lang::get('picking.col_from_slot_code'). ',';
		$output .= Lang::get('picking.col_to_slot_code'). ',';
		$output .= Lang::get('picking.col_sku'). ',';
		$output .= Lang::get('picking.col_upc'). ',';
		$output .= Lang::get('picking.col_shrt_nm'). ','; 
		$output .= Lang::get('picking.col_qty_to_pick'). ',';
		$output .= Lang::get('picking.col_stock_piler'). ',';
		$output .= Lang::get('picking.col_entry_date'). ',';
	 
		$output .= Lang::get('picking.col_var'). "\n";

		 

		foreach ($results as $key => $value) {

	    	$exportData = array(
	    						 
	    						'"' . $value->move_doc_number . '"',
	    						'"' . $value->store_name . '"',
	    						'"' . $value->to_store_code . '"',
	    						'"' . $value->sku . '"',
	    						'"' . $value->upc . '"', 
	    						'"' . $value->description . '"', 
	    						'"' . $value->quantity_to_pick . '"',
	    						'"' . $value->firstname . ' '. $value->lastname .'"',
	    						'"' .date("M d, Y", strtotime($value->created_at)). '"',
	    					 
	    						'"' . $value->variance . '"'
	    					 
	    					);

	      	$output .= implode(",", $exportData);
	      	$output .= "\n";

	       
	  	}

	  	$headers = array(
			'Content-Type' => 'text/csv',
			'Content-Disposition' => 'attachment; filename="picklist_' . date('Ymd')  . '_' . time() . '.csv"',
		);

		return Response::make(rtrim($output, "\n"), 200, $headers);

	}
	public function getstockloadassign() {

	 
 		// Search Filters
	    $filter_load_code 			= Input::get('filter_load_code', NULL);
	    $filter_assigned_to_user_id = Input::get('filter_assigned_to_user_id', NULL);
	    $filter_ship_at 	= Input::get('filter_ship_at', NULL);
	    //$filter_store = Input::get('filter_store', NULL);
	    //$filter_stock_piler = Input::get('filter_stock_piler', NULL);

	    $sort = Input::get('sort', 'load_code');
	    $order = Input::get('order', 'DESC');
	    $page = Input::get('page', 1);

	    $this->data                     = Lang::get('box');
	    $this->data['load_code']           = Input::get('load_code');

	 	$this->data['params']           = explode(',', Input::get('load_code'));
	    $this->data['info']             = Load::getInfoLoad($this->data['params']);

	    $this->data['stock_piler_list'] = $this->getStockPilers();

	    
	    $this->data['filter_assigned_to_user_id'] = $filter_assigned_to_user_id;
	    $this->data['sort'] = $sort;
	    $this->data['order'] = $order;
	    $this->data['page'] = $page;

	    $this->data['url_back']         = URL::to('stocktransfer/stocktranferload'). $this->setURL();
 
	    $this->layout->content  = View::make('store_return.assign_load_number', $this->data);
	}

	public static function getstockloadassignpost()
	{

	$pilers = implode(',' , Input::get('stock_piler'));
    
        $arrBoxCode = explode(',', Input::get("load_code"));
 
        foreach ($arrBoxCode as $codes) {
            $arrParams = array(
                'assigned_by' 			=> Auth::user()->id,
				'assigned_to_user_id' 	=> $pilers, //Input::get('stock_piler'),
				'updated_at' 			=> date('Y-m-d H:i:s')
            );
            
            load::assignToStockPiler($codes, $arrParams);
 
        }
        return Redirect::to('stocktransfer/stocktranferload')->with('message','Successfully assigned the Load!');
    }
 
	public function getMTSGenerateLoadCode()
	{

		$loadMax =  StoreReturnload::select(DB::raw('max(id) as max_created, max(load_code) as load_code'))->first()->toArray();

		if($loadMax['max_created'] === null) {
			$loadCode = 'LD0000001';
		} else {
			$loadCode = substr($loadMax['load_code'], -7);
			$loadCode = (int) $loadCode + 1;
			$loadCode = 'LD' . sprintf("%07s", (int)$loadCode);
		}

		StoreReturnload::create(array(
			'load_code'	=> $loadCode)
			);
		$load = StoreReturnload::where('load_code', '=',$loadCode)->first()->toArray();
		 
		echo json_encode($load);
		die();
	}
	public function getMtsRecevingDetail()
	{

		$so_no 	=Input::get('so_no',null);
		$picklistDoc = Input::get('picklistDoc', NULL);

		$this->data 				= lang::get('store_return');
		$mts_number = Input::get('mts_number', NULL);
 		$this->data['text_total']         = Lang::get('general.text_total');
	 	$this->data['url_back']           = URL::to('stock_transfer/MTSReceiving');

		// Search Filters
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
		$filter_entry_date 		= Input::get('filter_entry_date', null);
		$filter_from_slot     = Input::get('filter_from_slot', NULL);
		$filter_store     		= Input::get('filter_store', NULL);
		$filter_stock_piler     = Input::get('filter_stock_piler', NULL);
		// $filter_to_slot    = Input::get('filter_to_slot', NULL);
		// $filter_status_detail = Input::get('filter_status_detail', NULL);

		//for back
		$sort_back  = Input::get('sort_back' );
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
						'filter_entry_date'		=> $filter_entry_date,
						// 'filter_to_slot'		=> $filter_to_slot,
						// 'filter_status_detail'	=> $filter_status_detail,
						'sort'					=> $sort_detail,
						'order'					=> $order_detail,
						'page'					=> $page_detail,
						'picklist_doc'			=> $picklistDoc,
						'limit'					=> 30
					);
		$results 		= StoreReturnDetail::getFilteredPicklistDetailasdf($picklistDoc,  $arrParams)->toArray();
		$results_total 	= StoreReturnDetail::getFilteredPicklistDetailasdf($picklistDoc, $arrParams, true);
		// echo "<pre>"; print_r($results);die();

		// Pagination
		$this->data['arrFilters'] = array(
									// 'filter_to_slot'		=> $filter_to_slot,
									// 'filter_status_detail'	=> $filter_status_detail,
									'filter_type'			=> $filter_type,
									'filter_doc_no'			=> $filter_doc_no,
									'filter_status'			=> $filter_status,
									'filter_store'			=> $filter_store,
									'filter_entry_date'		=> $filter_entry_date,
									'filter_stock_piler'	=> $filter_stock_piler,
									'sort_back'				=> $sort_back,
									'order_back'			=> $order_back,
									'page_back'				=> $page_back,
									'picklist_doc'			=> $picklistDoc,
									'filter_sku'			=> $filter_sku,
									'filter_upc'			=> $filter_upc,
									'filter_so'				=> $filter_so,
									'filter_from_slot'		=> $filter_from_slot,
									'sort'					=> $sort_detail,
									'order'					=> $order_detail
								);

 
		$this->data['stock_pick_detail']       = Paginator::make($results, $results_total, 30);
		$this->data['picklist_detail_count'] = $results_total;
		$this->data['counter']               = $this->data['stock_pick_detail']->getFrom();
		$this->data['picklistDoc']          = $picklistDoc;
		$this->data['filter_type']           = $filter_type;
		$this->data['filter_doc_no']         = $filter_doc_no;
		$this->data['filter_status']         = $filter_status;
		$this->data['filter_entry_date']		= $filter_entry_date;
		$this->data['filter_sku']            = $filter_sku;
		$this->data['filter_upc']            = $filter_upc;
		$this->data['filter_so']             = $filter_so;
		$this->data['filter_from_slot']      = $filter_from_slot;
		$this->data['filter_store']      = $filter_store;
		$this->data['filter_stock_piler']      = $filter_stock_piler;
		 
		$this->data['sort_back']             = $sort_back;
		$this->data['order_back']            = $order_back;
		$this->data['page_back']             = $page_back;

		 
		$this->data['sort']  = $sort_detail;
		$this->data['order'] = $order_detail;
		$this->data['page']  = $page_detail;

		$url = '?filter_sku=' . $filter_sku . '&filter_upc=' . $filter_upc . '&filter_so=' . $filter_so;
		$url .= '&filter_from_slot=' . $filter_from_slot . '&picklist_doc=' . $picklistDoc.'&filter_entry_date='.$filter_entry_date;
		$url .= '&page=' . $page_detail;

		 
		$order_sku = ($sort_detail=='sku' && $order_detail=='ASC') ? 'DESC' : 'ASC';
		$order_upc = ($sort_detail=='upc' && $order_detail=='ASC') ? 'DESC' : 'ASC';
		$order_so_no = ($sort_detail=='so_no' && $order_detail=='ASC') ? 'DESC' : 'ASC';
	 
		$this->layout->content = View::make('store_return.mts_receiving_detail', $this->data);
	}
 public function getList() {
		 

		$this->data = Lang::get('store_return');

		$this->data['text_empty_results'] = Lang::get('general.text_empty_results');
		$this->data['text_total'] = Lang::get('general.text_total');
		$this->data['text_select'] = Lang::get('general.text_select');
		$this->data['button_search'] = Lang::get('general.button_search');
		$this->data['button_clear'] = Lang::get('general.button_clear');
		$this->data['button_export'] = Lang::get('general.button_export');
		// URL
		$this->data['url_assign'] = URL::to('store_return/fromstore'. $this->setURL());
		$this->data['url_export'] = URL::to('store_return/export');
		$this->data['url_detail'] = URL::to('store_return/detail' . $this->setURL(true));
		$this->data['url_mts_receiving_detail']  =URL::to('store_return/mts_receiving_detail'.$this->setURL(true));
	 
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
		$store_list 	  			  = StoreReturn::getStoreList();

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
		$filter_created_at = Input::get('filter_created_at', NULL);
		$filter_status = Input::get('filter_status', NULL);
		$mts_number 	= Input::get('mts_number', null);
		$sort = Input::get('sort', 'so_no');
		$order = Input::get('order', 'ASC');
		$page = Input::get('page', 1);

		//Data
		$arrParams = array(
						'filter_so_no' 			=> $filter_so_no,
						'filter_store' 			=> $filter_store,
						'filter_created_at' 	=> $filter_created_at,
						'filter_status' 		=> $filter_status,
						'sort'					=> $sort,
						'order'					=> $order,
						'page'					=> $page,
						'limit'					=> 30
					);

		$results 		= StoreReturn::getSOList($arrParams);
		foreach ($results as $result) {
			$arrParams = array(
							'filter_so_no' 			=> $filter_so_no,
							'filter_store' 			=> $filter_store,
							'filter_created_at' 	=> $filter_created_at,
							'filter_status' 		=> $filter_status,
							'sort'					=> $sort,
							'order'					=> $order,
							'page'					=> $page,
							'limit'					=> 0
						);
			$details= StoreReturnDetail::getSODetails($result['so_no'], $arrParams)->toArray();
			foreach($details as $detail){
				if($detail['received_qty'] != $detail['delivered_qty'] ){
					$result->discrepancy=1;
					break;	
				}
			}
		}
		$results = $results->toArray();
		$results_total 	= StoreReturn::getCount($arrParams);

		// Pagination
		$this->data['arrFilters'] = array(
									'filter_so_no' 			=> $filter_so_no,
									'filter_store' 			=> $filter_store,
									'filter_created_at' 	=> $filter_created_at,
									'filter_status' 		=> $filter_status,
									'sort'					=> $sort,
									'order'					=> $order
								);

		$this->data['store_return'] = Paginator::make($results, $results_total, 30);
		$this->data['store_return_count'] = $results_total;

		$this->data['counter'] 	= $this->data['store_return']->getFrom();
		$this->data['so_status_type'] = Dataset::getTypeWithValue("SR_STATUS_TYPE");
		// print_r($results); die();
		$this->data['filter_so_no'] = $filter_so_no;
		$this->data['filter_store'] = $filter_store;
		$this->data['filter_created_at'] = $filter_created_at;
		$this->data['filter_status'] = $filter_status;
		$this->data['mts_number'] 	= $mts_number;


		$this->data['sort'] = $sort;
		$this->data['order'] = $order;
		$this->data['page'] = $page;

		$url = '?filter_so_no=' . $filter_so_no . '&filter_store=' . $filter_store;
		$url .= '&filter_created_at=' . $filter_created_at;
		$url .= '&filter_status=' . $filter_status;
		$url .= '&page=' . $page;

		$order_so_no = ($sort=='so_no' && $order=='ASC') ? 'DESC' : 'ASC';
		$order_store = ($sort=='store' && $order=='ASC') ? 'DESC' : 'ASC';
		$order_created_at = ($sort=='created_at' && $order=='ASC') ? 'DESC' : 'ASC';

		$this->data['sort_so_no'] = URL::to('store_return' . $url . '&sort=so_no&order=' . $order_so_no, NULL, FALSE);
		$this->data['sort_store'] = URL::to('store_return' . $url . '&sort=store&order=' . $order_store, NULL, FALSE);
		$this->data['sort_created_at'] = URL::to('store_return' . $url . '&sort=created_at&order=' . $order_created_at, NULL, FALSE);


		$this->layout->content = View::make('store_return/stocktransfer', $this->data);
	}
	public function getFromStorelist()
	{
		$filter_status      = Input::get('filter_status', NULL);
		$filter_so_no		= Input::get('filter_so_no', null);
		$this->data['text_total']         = Lang::get('general.text_total');
		$this->data['text_select']        = Lang::get('general.text_select');
		$this->data['po_status_type']   = Dataset::getTypeInList("PO_STATUS_TYPE");
 		 

 		 $this->data['filter_status']         = $filter_status;
 		 $this->data['filter_so_no']	 	= $filter_so_no;
		$this->layout->content = View::make('store_return/fromstorelist', $this->data);
	
	}

	 public function StockTransferpiler() {
		 
		// Search Filters
		$filter_so_no = Input::get('filter_so_no', NULL);
		$filter_store_name = Input::get('filter_store_name', NULL);
		$filter_created_at = Input::get('filter_created_at', NULL);
		$filter_status = Input::get('filter_status', NULL);

		$sort = Input::get('sort', 'so_no');
		$order = Input::get('order', 'ASC');
		$page = Input::get('page', 1);

		$this->data                    = Lang::get('store_return');
		$this->data['so_no']           = Input::get('so_no');

		$this->data['filter_so_no'] = $filter_so_no;
		$this->data['filter_store_name'] = $filter_store_name;
		$this->data['filter_created_at'] = $filter_created_at;
		$this->data['filter_status'] = $filter_status;

		$this->data['sort'] = $sort;
		$this->data['order'] = $order;
		$this->data['page'] = $page;

		$this->data['stock_piler_list'] = $this->getStockPilers();
		$this->data['button_assign']    = Lang::get('general.button_assign');
		$this->data['button_cancel']    = Lang::get('general.button_cancel');
		$this->data['url_back']         = URL::to('stock_transfer/MTSReceiving'). $this->setURL();
		$this->data['params']           = explode(',', Input::get('so_no'));
		$this->data['info']             = StoreReturn::getInfoBySoNo($this->data['params']);

		$this->layout->content    = View::make('store_return.assignToPiler', $this->data);
	}
	private function getStockPilers()
	{
		$stock_pilers = array();
		foreach (User::getStockPilerOptions() as $item) {
			$stock_pilers[$item->id] = $item->firstname . ' ' . $item->lastname;
		}
		return array('' => Lang::get('general.text_select')) + $stock_pilers;
	}
	public function getSOList()
	{

		$this->data = Lang::get('store_return');
		$mts_number 	= Input::get('mts_number', null);
		$this->data['text_total']         = Lang::get('general.text_total');
		$this->data['url_detail']		= URL::to('store_return/mts_receiving_detail'.$this->setURL(true));
		$this->data['url_assign'] = URL::to('store_return/assign');
 	 	$this->data['url_export'] = URL::to('stock_transfer/discrepansymts');

		$this->data['text_select']            = Lang::get('general.text_select');

		$this->data['stores']                 = Store::lists( 'store_name', 'store_code');
		$this->data['url_export']			= URL::to('stock_transfer/discrepansymts');
 		$this->data['po_info']                 = Store::lists( 'store_name','store_name');
 		
		$this->data['stock_piler_list'] = $this->getStockPilers();
		// Search filters
		$filter_type = Input::get('filter_type', NULL);
		$filter_doc_no = Input::get('filter_doc_no', NULL);
		$filter_date_entry 	= Input::get('filter_date_entry',null);
		$filter_status = Input::get('filter_status', NULL);
		$filter_store = Input::get('filter_store', NULL);
		$filter_stock_piler = Input::get('filter_stock_piler', NULL);
		$filter_store_name 	= Input::get('filter_store_name', null);

        $filter_transfer_no = Input::get('filter_transfer_no', NULL); 
		$sort = Input::get('sort');
		$order = Input::get('order', 'ASC');
		$page = Input::get('page', 1);

		//Data
		$arrParams = array(
						'filter_type' 			=> $filter_type,
						'filter_doc_no' 		=> $filter_doc_no,
						'filter_status' 		=> $filter_status,
						'filter_store' 			=> $filter_store,
						'filter_stock_piler' 	=> $filter_stock_piler,
                        'filter_transfer_no' 	=> $filter_transfer_no,
                        'filter_date_entry' 	=> $filter_date_entry,
                        'filter_store_name'		=> $filter_store_name,
						'sort'					=> $sort,
						'order'					=> $order,
						'page'					=> $page,
						'limit'					=> 30
					);

 		  
 		$results 		= StoreReturn::getStockTransferList($arrParams)->toArray();
		$results_total 	= StoreReturn::getStockTransferList($arrParams, TRUE);
	 
	 

		$this->data['arrFilters'] = array(
									'filter_type' 			=> $filter_type,
									'filter_doc_no' 		=> $filter_doc_no,
									'filter_status' 		=> $filter_status,
									'filter_store' 			=> $filter_store,
									'filter_stock_piler' 	=> $filter_stock_piler,
                                    'filter_transfer_no' 	=> $filter_transfer_no,
                                    'filter_date_entry' 	=> $filter_date_entry,
									'sort'					=> $sort,
									'order'					=> $order
								);

		$this->data['stocktranferLIST'] = Paginator::make($results, $results_total, 30);
		$this->data['picklist_count'] = $results_total;
		$this->data['counter'] 	= $this->data['stocktranferLIST']->getFrom();
		$this->data['filter_type'] = $filter_type;
		$this->data['filter_doc_no'] = $filter_doc_no;
		$this->data['filter_status'] = $filter_status;
		$this->data['filter_store'] = $filter_store;
		$this->data['filter_stock_piler'] = $filter_stock_piler;
        $this->data['filter_transfer_no'] = $filter_transfer_no;
        $this->data['filter_date_entry'] = $filter_date_entry;
        $this->data['filter_store_name'] 	= $filter_store_name;
		$this->data['sort'] = $sort;
		$this->data['order'] = $order;
		$this->data['page'] = $page;

		$url = '?filter_type=' . $filter_type . '&filter_doc_no=' . $filter_doc_no;
		$url .= '&filter_status=' . $filter_status . '&filter_store=' . $filter_store;
		$url .= '&filter_stock_piler=' . $filter_stock_piler;
        $url .= '&filter_transfer_no=' . $filter_transfer_no;
        $url .= '&filter_store_name=' . $filter_store_name;
        $url .= '&filter_date_entry=' . $filter_date_entry;
		$url .= '&page=' . $page;

		$this->layout->content = View::make('store_return/stocktransfer', $this->data);
	
	}
	public function getdiscrepancymts()
	{
		$this->data = Lang::get('store_return');
	 
	 	$this->data['url_exportpdf']		= URL::to('stock_transfer/discrepansyPdffile');
	 	$this->data['url_exportexcel']		= URL::to('stock_transfer/discrepansyExcelfile');
		// Search filters
		$filter_type = Input::get('filter_type', NULL);
		$filter_doc_no = Input::get('filter_doc_no', NULL);
		$filter_date_entry 	= Input::get('filter_date_entry',null);
		$filter_status = Input::get('filter_status', NULL);
		$filter_store = Input::get('filter_store', NULL);
		$filter_stock_piler = Input::get('filter_stock_piler', NULL);
		$filter_store_name 	= Input::get('filter_store_name', null);

        $filter_transfer_no = Input::get('filter_transfer_no', NULL); 
		$sort = Input::get('sort');
		$order = Input::get('order', 'ASC');
		$page = Input::get('page', 1);

		//Data
		$arrParams = array(
						'filter_type' 			=> $filter_type,
						'filter_doc_no' 		=> $filter_doc_no,
						'filter_status' 		=> $filter_status,
						'filter_store' 			=> $filter_store,
						'filter_stock_piler' 	=> $filter_stock_piler,
                        'filter_transfer_no' 	=> $filter_transfer_no,
                        'filter_date_entry' 	=> $filter_date_entry,
                        'filter_store_name'		=> $filter_store_name,
						'sort'					=> $sort,
						'order'					=> $order,
						'page'					=> $page,
						'limit'					=> 30
					);

 		  
 		$results 		= StoreReturn::getSOListReport($arrParams);
		$results_total 	= StoreReturn::getSOListReport($arrParams, TRUE);
	 
	 

		$this->data['arrFilters'] = array(
									'filter_type' 			=> $filter_type,
									'filter_doc_no' 		=> $filter_doc_no,
									'filter_status' 		=> $filter_status,
									'filter_store' 			=> $filter_store,
									'filter_stock_piler' 	=> $filter_stock_piler,
                                    'filter_transfer_no' 	=> $filter_transfer_no,
                                    'filter_date_entry' 	=> $filter_date_entry,
									'sort'					=> $sort,
									'order'					=> $order
								);

		$this->data['stocktranferdisc'] = Paginator::make($results, $results_total, 30);
		$this->data['picklist_count'] = $results_total;
		$this->data['counter'] 	= $this->data['stocktranferdisc']->getFrom();
		$this->data['filter_type'] = $filter_type;
		$this->data['filter_doc_no'] = $filter_doc_no;
		$this->data['filter_status'] = $filter_status;
		$this->data['filter_store'] = $filter_store;
		$this->data['filter_stock_piler'] = $filter_stock_piler;
        $this->data['filter_transfer_no'] = $filter_transfer_no;
        $this->data['filter_date_entry'] = $filter_date_entry;
        $this->data['filter_store_name'] 	= $filter_store_name;
		$this->data['sort'] = $sort;
		$this->data['order'] = $order;
		$this->data['page'] = $page;

		$url = '?filter_type=' . $filter_type . '&filter_doc_no=' . $filter_doc_no;
		$url .= '&filter_status=' . $filter_status . '&filter_store=' . $filter_store;
		$url .= '&filter_stock_piler=' . $filter_stock_piler;
        $url .= '&filter_transfer_no=' . $filter_transfer_no;
        $url .= '&filter_store_name=' . $filter_store_name;
        $url .= '&filter_date_entry=' . $filter_date_entry;
		$url .= '&page=' . $page;

		$this->layout->content = View::make('store_return/discrepancymts', $this->data);
	}
	public function getdiscrepancypick()
	{
		$this->data = Lang::get('store_return');
	 
	 	$this->data['url_exportpdf']		= URL::to('stock_transfer/exportCSVpickingreport');
	 	$this->data['url_exportexcel']		= URL::to('stock_transfer/export_excel_file');

		// Search filters
		$filter_type = Input::get('filter_type', NULL);
		$filter_doc_no = Input::get('filter_doc_no', NULL);
		$filter_date_entry 	= Input::get('filter_date_entry',null);
		$filter_status = Input::get('filter_status', NULL);
		$filter_store = Input::get('filter_store', NULL);
		$filter_stock_piler = Input::get('filter_stock_piler', NULL);
		$filter_store_name 	= Input::get('filter_store_name', null);

        $filter_transfer_no = Input::get('filter_transfer_no', NULL); 
		$sort = Input::get('sort');
		$order = Input::get('order', 'ASC');
		$page = Input::get('page', 1);

		//Data
		$arrParams = array(
						'filter_type' 			=> $filter_type,
						'filter_doc_no' 		=> $filter_doc_no,
						'filter_status' 		=> $filter_status,
						'filter_store' 			=> $filter_store,
						'filter_stock_piler' 	=> $filter_stock_piler,
                        'filter_transfer_no' 	=> $filter_transfer_no,
                        'filter_date_entry' 	=> $filter_date_entry,
                        'filter_store_name'		=> $filter_store_name,
						'sort'					=> $sort,
						'order'					=> $order,
						'page'					=> $page,
						'limit'					=> 30
					);

 		  
 		$results 		= StoreReturnPickinglist::getStocktransferPickReport($arrParams)->toArray();
		$results_total 	= StoreReturnPickinglist::getStocktransferPickReport($arrParams, TRUE);
	 
	 

		$this->data['arrFilters'] = array(
									'filter_type' 			=> $filter_type,
									'filter_doc_no' 		=> $filter_doc_no,
									'filter_status' 		=> $filter_status,
									'filter_store' 			=> $filter_store,
									'filter_stock_piler' 	=> $filter_stock_piler,
                                    'filter_transfer_no' 	=> $filter_transfer_no,
                                    'filter_date_entry' 	=> $filter_date_entry,
									'sort'					=> $sort,
									'order'					=> $order
								);

		$this->data['asdfdiscrepancy'] = Paginator::make($results, $results_total, 30);
		$this->data['picklist_count'] = $results_total;
		$this->data['counter'] 	= $this->data['asdfdiscrepancy']->getFrom();
		$this->data['filter_type'] = $filter_type;
		$this->data['filter_doc_no'] = $filter_doc_no;
		$this->data['filter_status'] = $filter_status;
		$this->data['filter_store'] = $filter_store;
		$this->data['filter_stock_piler'] = $filter_stock_piler;
        $this->data['filter_transfer_no'] = $filter_transfer_no;
        $this->data['filter_date_entry'] = $filter_date_entry;
        $this->data['filter_store_name'] 	= $filter_store_name;
		$this->data['sort'] = $sort;
		$this->data['order'] = $order;
		$this->data['page'] = $page;

		$url = '?filter_type=' . $filter_type . '&filter_doc_no=' . $filter_doc_no;
		$url .= '&filter_status=' . $filter_status . '&filter_store=' . $filter_store;
		$url .= '&filter_stock_piler=' . $filter_stock_piler;
        $url .= '&filter_transfer_no=' . $filter_transfer_no;
        $url .= '&filter_store_name=' . $filter_store_name;
        $url .= '&filter_date_entry=' . $filter_date_entry;
		$url .= '&page=' . $page;

		$this->layout->content = View::make('store_return/pick_discrepancy', $this->data);
	}
	public function getStockTLnumberPosted()
	{
		$this->data['error'] = '';
		if (Session::has('error')) {
			$this->data['error'] = Session::get('error');
		}

		$this->data['success'] = '';
		if (Session::has('success')) {
			$this->data['success'] = Session::get('success');
		}
		$this->data 						= lang::get('store_return');
		$this->data['text_select']				=Lang::get('general.text_select');
 
		$this->data['stores']                 = Store::lists( 'store_name', 'store_code');
 		$this->data['po_info']                 = Store::lists( 'store_name','store_code');
 		$this->data['url_back']					= URL::to('stocktransfer/stocktranferload'. $this->setURL(true));

		$filter_type = Input::get('filter_type', NULL);
		$filter_doc_no = Input::get('filter_doc_no', NULL);
		$filter_status = Input::get('filter_status', NULL);
		$filter_store_name = Input::get('filter_store_name', NULL);
		$filter_store = Input::get('filter_store', NULL);
		$filter_stock_piler = Input::get('filter_stock_piler', NULL);
		$filter_doc_no_pick	= Input::get('filter_doc_no_pick', null);
        $filter_transfer_no = Input::get('filter_transfer_no', NULL);
        $filter_action_date = Input::get('filter_action_date', NULL);
        
 
		$loadnumber  = Input::get('loadnumber', null);
		$pilername	= Input::get('pilername', Null);
		$created_at	= Input::get('created_at', Null);
	 

		$sort = Input::get('sort', 'doc_no');
		$order = Input::get('order', 'ASC');
		$page = Input::get('page', 1);


		//Data
		$arrParams = array(
						'filter_type' 			=> $filter_type,
						'filter_doc_no' 		=> $filter_doc_no,
						'filter_status' 		=> $filter_status,
						'filter_store' 			=> $filter_store,
						'filter_store_name' 	=> $filter_store_name,
						'filter_doc_no_pick'	=>	$filter_doc_no_pick,
						'filter_stock_piler' 	=> $filter_stock_piler,
                        'filter_transfer_no' 	=> $filter_transfer_no,
                        'filter_action_date' 	=> $filter_action_date,
						'sort'					=> $sort,
						'order'					=> $order,
						'page'					=> $page,
						'limit'					=> 30
					);

		$results 		= Box::getLoadNumberstock(  $arrParams)->toArray();
		$results_total 	= Box::getLoadNumberstock(  $arrParams, TRUE);

		// Pagination
		$this->data['arrFilters'] = array(
									'filter_type' 			=> $filter_type,
									'filter_doc_no' 		=> $filter_doc_no,
									'filter_status' 		=> $filter_status,
									'filter_store_name' 		=> $filter_store_name,
									'filter_doc_no_pick'	=> $filter_doc_no_pick,
									'filter_store' 			=> $filter_store,
									'filter_stock_piler' 	=> $filter_stock_piler,
                                    'filter_transfer_no' 	=> $filter_transfer_no,
                                    'filter_action_date' 	=> $filter_action_date,
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
		$this->data['filter_doc_no_pick'] = $filter_doc_no_pick;
		$this->data['filter_store_name']	= $filter_store_name;
		
		$this->data['filter_stock_piler'] = $filter_stock_piler;
        $this->data['filter_transfer_no'] = $filter_transfer_no;
        $this->data['filter_action_date'] = $filter_action_date;
 		$this->data['loadnumber']		=$loadnumber;

     
		$this->data['sort'] = $sort;
		$this->data['order'] = $order;
		$this->data['page'] = $page;
 
		$this->data['pilername']	= $pilername;
		$this->data['loadnumber'] = $loadnumber;
		$this->data['created_at'] =$created_at;

		$url = '?filter_type=' . $filter_type . '&filter_doc_no=' . $filter_doc_no;
		$url .= '&filter_status=' . $filter_status . '&filter_store=' . $filter_store. '&filter_store_name='.$filter_store_name;
		$url .= '&filter_stock_piler=' . $filter_stock_piler;
        $url .= '&filter_transfer_no=' . $filter_transfer_no;
        $url .= '&filter_action_date=' . $filter_action_date;
		$url .= '&page=' . $page;

		$this->layout->content = View::make('store_return/assign_TL_number_posted', $this->data);
	
	}
	public function StoreReturnTLnumbersync()
	{
			StoreReturn::getStoreReturnTLnumbersync();
		return Redirect::to('stock_transfer/MTSReceiving'.$this->setURL())->with('message','Sync To Mobile Successfully');
	}
	public function StoreReturnPickingandPackTLnumbersync()
	{
			StoreReturnPickinglist::getStoreReturnPickandPackTLnumbersync();
		return Redirect::to('stocktransfer/PickAndPackStore'.$this->setURL())->with('message','Sync To Mobile Successfully');
	}
	 
	 public function closePickliststockreceiving()
	{
		 $tl_number 	=Input::get('tl_number', null);
		$picklist = StoreReturn::updateStatusstocktransfer($tl_number);
		 

		return Redirect::to('stock_transfer/MTSReceiving' . $this->setURL())->with('message', Lang::get('store_return.text_success_postedstocktransfer'));
	}
	 public function closePickliststockpicking()
	{
		 $tl_number			= Input::get('tl_number',null);
		$picklist = StoreReturnPickinglist::getStoreReturnPickandPackTLnumbersyncclose($tl_number);
		 

		return Redirect::to('stocktransfer/PickAndPackStore' . $this->setURL())->with('message', Lang::get('store_return.text_success_postedstocktransferasdf'));
	}
	public function getCSVUnlistedReportMTS()
	{
			$filter_so_no = Input::get('filter_so_no', NULL);
			$filter_store_name = Input::get('filter_store_name', NULL);
			$filter_created_at = Input::get('filter_created_at', NULL);
			$filter_status = Input::get('filter_status', NULL);

			//for back
			$sort_back  = Input::get('sort_back', 'so_no');
			$order_back = Input::get('order_back', 'ASC');
			$page_back  = Input::get('page_back', 1);

			// Details
			$sort_detail  = Input::get('sort', 'sku');
			$order_detail = Input::get('order', 'ASC');
			$page_detail  = Input::get('page', 1);

			//Data
			$so_id = Input::get('id', NULL);
			$so_no = Input::get('so_no', NULL);


			$this->data = Lang::get('store_return'); 

			$arrParams = array(
					'id'             	=> $so_id,
					'sort'              => $sort_detail,
					'order'             => $order_detail,
					'page'              => $page_detail,
					'so_no'             => $so_no,
					'filter_so_no'      => $filter_so_no,
				 
					'filter_created_at' => $filter_created_at,
					'filter_status'     => $filter_status,
					'limit' => NULL
				);
 
			$results = StoreReturn::getStocktransferPickUnlistedReport($arrParams);
			$this->data['results'] = $results;

			 
			$pdf = App::make('dompdf');
			$pdf->loadView('store_return.report_mts_unlisted', $this->data)->setPaper('a4')->setOrientation('landscape');
			// return $pdf->stream();
			return $pdf->download('stock_transfer_mts_unlisted_' . date('Ymd') . '.pdf');
	}

	 public function assignStockTransferStoreForm ()
	
	{
		 
	  	$mts_number 			= Input::get('mts_number', null);
	  	 
  		$filter_load_code 			= Input::get('filter_load_code', NULL);
	    $filter_assigned_to_user_id = Input::get('filter_assigned_to_user_id', NULL);
	    $filter_ship_at 	= Input::get('filter_ship_at', NULL);
	    

	    $sort = Input::get('sort', 'load_code');
	    $order = Input::get('order', 'DESC');
	    $page = Input::get('page', 1);

	    $this->data                     = Lang::get('box');
	    $this->data['load_code']           = Input::get('load_code');

	 	$this->data['params']           = explode(',', Input::get('load_code'));
	    $this->data['info']             = Load::getInfoLoad($this->data['params']);

	    $this->data['stock_piler_list'] = $this->getStockPilers();

	    
	    $this->data['filter_assigned_to_user_id'] = $filter_assigned_to_user_id;
	    $this->data['sort'] = $sort;
	    $this->data['order'] = $order;
	    $this->data['page'] = $page;

      

        $this->data['mts_number'] = $mts_number;


       //$this->layout->content  = View::make('loads.shipping_assign_piler', $this->data);
 

       // $this->layout->content          = View::make('box.assign_piler_form', $this->data);
		
		//return Redirect::to('store_return/fromstore');
		$this->layout->content = View::make('store_return/fromstore', $this->data);
		 
	}
	public function getlist1()
	{

		 $arrPO = explode(',', Input::get("tlnumber"));
		 $loadnumber 		= Input::get('loadnumber', null);
		 $tlnumber 			= Input::get('tlnumber', null);
		

			foreach ($arrPO as $assignTL) {

			Box::assignToTLstock($assignTL, $loadnumber);
			Box::assignToTLnumberboxstock($assignTL, $loadnumber);


		/*	$users = User::getUsersFullname(Input::get('stock_piler')); 

			$fullname = implode(', ', array_map(function ($entry) { return $entry['name']; }, $users));
*/
			$data_before = '';
			$data_after ='Box no. : ' . $tlnumber . ', Assigned to Pell no. : ' . $loadnumber;

			$arrParams = array(
							'module'		=> Config::get("audit_trail_modules.subloc_loading"),
							'action'		=> Config::get('audit_trail.assign_load'),
							'reference'		=> 'Box no. : '. $tlnumber,
							'data_before'	=> $data_before,
							'data_after'	=> $data_after,
							'user_id'		=> Auth::user()->id,
							'created_at'	=> date('Y-m-d H:i:s'),
							'updated_at'	=> date('Y-m-d H:i:s')
							);
			AuditTrail::addAuditTrail($arrParams);
 			}

			return Redirect::to('stocktransfer/stocktranferload'. $this->setURL())->with('message', "Succefully Assigned in Load Number!");

	}
	public function getStockTransferLoadnumberAssign()
	{
		 
		$this->data = Lang::get('store_return');
		$this->data['text_empty_results'] = Lang::get('general.text_empty_results');
		$this->data['text_total'] = Lang::get('general.text_total');
		$this->data['text_select'] = Lang::get('general.text_select');

		$this->data['button_jda'] = Lang::get('general.button_jda');
		$this->data['button_export'] = Lang::get('general.button_export');
		$this->data['button_cancel'] = Lang::get('general.button_cancel');
		$this->data['button_search'] = Lang::get('general.button_search');
		$this->data['button_clear'] = Lang::get('general.button_clear');
		// URL
		$this->data['url_export'] = URL::to('box/export_detail');
		$this->data['url_back'] =  URL::to('stocktransfer/stocktranferload'. $this->setURL(true));

		$this->data['stores']                 = Store::lists( 'store_name', 'store_code');
 		$this->data['po_info']                 = Store::lists( 'store_name','store_code');
		// Message
		$this->data['error'] = '';
		if (Session::has('error')) {
			$this->data['error'] = Session::get('error');
		}

		$this->data['success'] = '';
		if (Session::has('success')) {
			$this->data['success'] = Session::get('success');
		}

		$box_code = Input::get('box_code', NULL);
		$this->data['Contentbox'] = box::getboxcontent($box_code);
		// Search Filters
		$filter_sku = Input::get('filter_sku', NULL);
		$filter_store = Input::get('filter_store', NULL);
		$filter_doc_no = Input::get('filter_doc_no', null);
		$filter_box_code	= Input::get('filter_box_code', null);
		$filter_store_name  = Input::get('filter_store_name', NULL);

		$load_code 	= Input::get('load_code', null);

		$filter_load_code = Input::get('filter_load_code', NULL);
		$sort_back = Input::get('sort_back' );
		$order_back = Input::get('order_back', 'ASC');
		$page_back = Input::get('page_back', 1);

		// Details
		$sort_detail = Input::get('sort' );
		$order_detail = Input::get('order', 'ASC');
		$page_detail = Input::get('page', 1);

		//Data
		$box_id = Input::get('id', NULL);
		// $this->data['letdown_info'] = Letdown::getLetDownInfo($letdown_id);
		$box_code = Input::get('box_code', NULL);

		$loadnumber 	= Input::get('loadnumber', null);
		$pilername		= Input::get('pilername', null);
		$data_value		=Input::get('data_value', null);

		$arrParams = array(
						'sort'				=> $sort_detail,
						'order'				=> $order_detail,
						'page'				=> $page_detail,
						'filter_sku' 		=> $filter_sku,
						'filter_store' 		=> $filter_store,
						'filter_store_name'	=> $filter_store_name,
						'filter_box_code'	=> $filter_box_code,
						'filter_doc_no'		=> $filter_doc_no,
						'filter_load_code'	=> $filter_load_code,
						'limit'				=> 30
					);
		// dd($box_code);
		$results 		= LoadDetails::getStocktransferLoadList($loadnumber, $arrParams);
	//	DebugHelper::log(__METHOD__, $results);
		$results_total 	= LoadDetails::getStocktransferLoadList($loadnumber, $arrParams, true); 
		// Pagination
		$this->data['arrFilters'] = array(
									'filter_store' 	=> $filter_store,
									'filter_load_code' => $filter_load_code,
									'filter_store_name' => $filter_store_name,
									'filter_doc_no'		=> $filter_doc_no,
									'filter_box_code'	=> $filter_box_code,
									'sort_back'		=> $sort_back,
									'order_back'	=> $order_back,
									'page_back'		=> $page_back,
									'filter_sku'	=> $filter_sku,
									// 'filter_slot'	=> $filter_slot,
									'sort'			=> $sort_detail,
									'order'			=> $order_detail,
								 
									'id'			=> $box_id
								);
		 
		$this->data['boxesdetails'] = Paginator::make($results, $results_total, 30);
		$this->data['boxes_count'] = $results_total;

		$this->data['counter'] 	= $this->data['boxesdetails']->getFrom();
		$this->data['box_id'] = $box_id;
		$this->data['box_code'] = $box_code;
		// Main
		//$this->data['load_code'] = $load_code;

		$this->data['filter_sku'] = $filter_sku;
		$this->data['filter_store'] = $filter_store;
		$this->data['filter_doc_no']	= $filter_doc_no;
		$this->data['filter_box_code']	= $filter_box_code;
		$this->data['filter_load_code'] = $filter_load_code;
		$this->data['filter_store_name'] = $filter_store_name;
		
		$this->data['sort'] = $sort_detail;
		$this->data['order'] = $order_detail;
		$this->data['page'] = $page_detail;

		$this->data['loadnumber']		= $loadnumber;
		$this->data['pilername'] 		= $pilername;
		$this->data['data_value'] 		=$data_value;
		
		$this->data['box_code'] 	=$box_code;
		// Details
		$this->data['sort_back'] 	= $sort_back;
		$this->data['order_back'] 	= $order_back;
		$this->data['page_back'] 	= $page_back;
 
		$this->layout->content = View::make('store_return.openTL', $this->data);
	}
	public function getremoved()

	{

		 $arrPO = explode(',', Input::get("tlnumber"));
		 $loadnumber 		= Input::get('loadnumber', null);
		 $tlnumber 			= Input::get('tlnumber', null);
		
	 

			foreach ($arrPO as $assignTL) {
		 

			Box::getremovedTLUpdatestock($assignTL, $loadnumber);
			Box::getremovedTLstock($assignTL, $loadnumber);
 			
 			$data_before = '';
            $data_after = 'Box number : ' . $tlnumber .', Pell number : '. $loadnumber; // ', assign by : ' . Auth::user()->username;

            $arrParams = array(
                'module'		=> Config::get('audit_trail_modules.subloc_loading'),
                'action'		=> Config::get('audit_trail.assign_remove'),
                'reference'		=> 'Remove Box no. : '. $tlnumber. ', ',
                'data_before'	=> $data_before,
                'data_after'	=> $data_after,
                'user_id'		=> Auth::user()->id,
                'created_at'	=> date('Y-m-d H:i:s'),
                'updated_at'	=> date('Y-m-d H:i:s')
               
            );

        }
            AuditTrail::addAuditTrail($arrParams);
			return Redirect::to('stocktransfer/stocktranferload'. $this->setURL())->with('message', "Succefully Remove Box Number!");

	}
	 
	 public function getCSVPickingReport() {

		
		///Check Permissions
	 
 
			$filter_so_no = Input::get('filter_so_no', NULL);
			$filter_store_name = Input::get('filter_store_name', NULL);
			$filter_created_at = Input::get('filter_created_at', NULL);
			$filter_doc_no = Input::get('filter_doc_no', NULL);

			//for back
			$sort_back  = Input::get('sort_back', 'so_no');
			$order_back = Input::get('order_back', 'ASC');
			$page_back  = Input::get('page_back', 1);

			// Details
			$sort_detail  = Input::get('sort', 'sku');
			$order_detail = Input::get('order', 'ASC');
			$page_detail  = Input::get('page', 1);

			//Data
			$so_id = Input::get('id', NULL);
			$so_no = Input::get('so_no', NULL);


			$this->data = Lang::get('store_return'); 

			$arrParams = array(
					'id'             	=> $so_id,
					'sort'              => $sort_detail,
					'order'             => $order_detail,
					'page'              => $page_detail,
					'so_no'             => $so_no,
					'filter_so_no'      => $filter_so_no,
				 	'filter_doc_no'		=> $filter_doc_no,
					'filter_created_at' => $filter_created_at,
		 
					'limit' => NULL
				);
 
			$results = StoreReturnPickinglist::getStocktransferPickReport($arrParams);
			$this->data['results'] = $results;

			 
			$pdf = App::make('dompdf');
			$pdf->loadView('store_return.report_detail', $this->data)->setPaper('a4')->setOrientation('landscape');
			// return $pdf->stream();
			return $pdf->download('stock_transfer_picking_report_' . date('Ymd') . '.pdf');
		 

		 
	}
	public function printBoxLabelstock($doc_num)
	{
		// Search Filters
		$this->data 		=	lang::get('store_return');
		$filter_type = Input::get('filter_type', NULL);
		$filter_doc_no = Input::get('filter_doc_no', NULL);
		$filter_status = Input::get('filter_status', NULL);
		$filter_store = Input::get('filter_store', NULL);
		$filter_stock_piler = Input::get('filter_stock_piler', NULL);
		$filter_box_code 	= Input::get('filter_box_code', null);

		$sort = Input::get('sort', 'doc_no');
		$order = Input::get('order', 'ASC');
		$page = Input::get('page', 1);

		$this->data['filter_type'] = $filter_type;
		$this->data['filter_doc_no'] = $filter_doc_no;
		$this->data['filter_status'] = $filter_status;
		$this->data['filter_store'] = $filter_store;
		$this->data['filter_stock_piler'] = $filter_stock_piler;
		$this->data['filter_box_code']	= $filter_box_code;
		$this->data['sort'] = $sort;
		$this->data['order'] = $order;
		$this->data['page'] = $page;

		$this->data['url_back'] = URL::previous();

			$this->data['doc_num'] = $doc_num;
			$this->data['records'] = StoreReturnPickinglist::getPicklistBoxes($doc_num);
			$this->data['storelocation'] = StoreReturnPickinglist::getStorelocation($doc_num);
			$this->data['permissions'] = unserialize(Session::get('permissions'));

			$this->layout = View::make('layouts.print');
			$this->layout->content = View::make('store_return.box_list_details', $this->data);
	}
	public function PickAndPackStore ()
	{

		$this->data 							=Lang::get('store_return');
	 	$this->data['text_empty_results']     = Lang::get('general.text_empty_results');
		$this->data['text_total']             = Lang::get('general.text_total');
		$this->data['text_select']            = Lang::get('general.text_select');
		$this->data['button_search']          = Lang::get('general.button_search');
		$this->data['button_clear']           = Lang::get('general.button_clear');
		$this->data['button_export']          = Lang::get('general.button_export');
	
		$this->data['url_back']             = $this->setURL();
		$this->data['url_detail']         = URL::to('stocktransfer/MTSpickdetails' . $this->setURL(true));
		$this->data['url_assign'] = URL::to('stock_transfer/assignpicking');
		$this->data['url_export'] =URL::to('stock_transfer/discrepansypick');
		/*$this->data['url_export']	=URL::to('stock_transfer/exportCSVpickingreport');*/
		
		$this->data['stores']                 = Store::lists( 'store_name', 'store_code');
 		$this->data['po_info']                 = Store::lists( 'store_name','store_name');
 		
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
	 
		$this->data['stock_piler_list'] = $this->getStockPilers();

		// Search Filters
		$filter_type = Input::get('filter_type', NULL);
		$filter_doc_no = Input::get('filter_doc_no', NULL);
		$filter_status = Input::get('filter_status', NULL);
		$filter_store = Input::get('filter_store', NULL);
		$filter_store_name = Input::get('filter_store_name', NULL);
		$filter_stock_piler = Input::get('filter_stock_piler', NULL);

        $filter_transfer_no = Input::get('filter_transfer_no', NULL);
        $filter_action_date = Input::get('filter_action_date', NULL);

		$sort = Input::get('sort');
		$order = Input::get('order', 'ASC');
		$page = Input::get('page', 1);

		//Data
		$arrParams = array(
						'filter_type' 			=> $filter_type,
						'filter_doc_no' 		=> $filter_doc_no,
						'filter_status' 		=> $filter_status,
						'filter_store' 			=> $filter_store,
						'filter_store_name' 			=> $filter_store_name,
						'filter_stock_piler' 	=> $filter_stock_piler,
                        'filter_transfer_no' 	=> $filter_transfer_no,
                        'filter_action_date' 	=> $filter_action_date,
						'sort'					=> $sort,
						'order'					=> $order,
						'page'					=> $page,
						'limit'					=> 30
					);

		$results 		= StoreReturnPickinglist::getStocktransferPickingListv2($arrParams)->toArray();
		$results_total 	= StoreReturnPickinglist::getStocktransferPickingListv2($arrParams, TRUE);

		// Pagination
		$this->data['arrFilters'] = array(
									'filter_type' 			=> $filter_type,
									'filter_doc_no' 		=> $filter_doc_no,
									'filter_status' 		=> $filter_status,
									'filter_store' 			=> $filter_store,
									'filter_store_name' 	=> $filter_store_name,
									'filter_stock_piler' 	=> $filter_stock_piler,
                                    'filter_transfer_no' 	=> $filter_transfer_no,
                                    'filter_action_date' 	=> $filter_action_date,
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
		$this->data['filter_store_name'] = $filter_store_name;
		$this->data['filter_stock_piler'] = $filter_stock_piler;
        $this->data['filter_transfer_no'] = $filter_transfer_no;
        $this->data['filter_action_date'] = $filter_action_date;

		$this->data['sort'] = $sort;
		$this->data['order'] = $order;
		$this->data['page'] = $page;

		$url = '?filter_type=' . $filter_type . '&filter_doc_no=' . $filter_doc_no;
		$url .= '&filter_status=' . $filter_status . '&filter_store=' . $filter_store;
		$url .= '&filter_store_name='.$filter_store_name;
		$url .= '&filter_stock_piler=' . $filter_stock_piler;
        $url .= '&filter_transfer_no=' . $filter_transfer_no;
        $url .= '&filter_action_date=' . $filter_action_date;
		$url .= '&page=' . $page;

		$order_doc_no = ($sort=='doc_no' && $order=='ASC') ? 'DESC' : 'ASC';
		

		$this->layout->content = View::make('store_return/picking_packing_store', $this->data);
	}



 
public function closePickliststock()
	{
		$docNo        = Input::get("doc_no");
		$boxcode 		=Input::get('boxcode');
		$status       = 'posted'; // closed
		$date_updated = date('Y-m-d H:i:s');

		$status_options = Dataset::where("data_code", "=", "PICKLIST_STATUS_TYPE")->get()->lists("id", "data_value");
		$picklist = Picklist::updateStatus($docNo, $status_options['closed']);
		Picklist::getpostedtoStore($docNo,$boxcode);
		
		/*Pic klist::getpos tedtoBo xOrder($ doc No);*/
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

		// jda syncing
		$picklistParams = array(
			'module' 		=> Config::get('transactions.module_picklist'),
			'jda_action'	=> Config::get('transactions.jda_action_picklist'),
			'reference'		=> $docNo
		);
		//create jda transaction for picklist closing
		$isSuccess = JdaTransaction::insert($picklistParams);
		Log::info(__METHOD__ .' dump: '.print_r($docNo,true));

		// run daemon command: php app/cron/jda/classes/picklist.php
		if( $isSuccess )
		{
			$daemon = "classes/picklist.php {$docNo}";
			CommonHelper::execInBackground($daemon,'picklist');
		}

		return Redirect::to('picking/list' . $this->setURL())->with('message', Lang::get('picking.text_success_posted'));
	}
		public function getMTSpickpackdetails ()
	{

		$picklistDoc = Input::get('picklist_doc', NULL);
		$this->data                       = Lang::get('store_return');
		$this->data['text_empty_results'] = Lang::get('general.text_empty_results');
		$this->data['text_total']         = Lang::get('general.text_total');
		$this->data['text_select']        = Lang::get('general.text_select');
		$this->data['button_back']        = Lang::get('general.button_back');
		$this->data['button_search']      = Lang::get('general.button_search');
		$this->data['button_clear']       = Lang::get('general.button_clear');
		$this->data['url_back']           = URL::to('picking/list' . $this->setURL(false, true));
			$this->data['url_detail']         = URL::to('stocktransfer/MTSpickdetails' . $this->setURL(true));
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
		$filter_store     = Input::get('filter_store', NULL);
		$filter_stock_piler     = Input::get('filter_stock_piler', NULL);
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

		$results 		= StoreReturnPickingdetail::getFilteredPicklistDetailStock($arrParams);
		$results_total 	= StoreReturnPickingdetail::getFilteredPicklistDetailStock($arrParams, true);
		// echo "<pre>"; print_r($results);die();

		// Pagination
		$this->data['arrFilters'] = array(
									// 'filter_to_slot'		=> $filter_to_slot,
									// 'filter_status_detail'	=> $filter_status_detail,
									'filter_type'			=> $filter_type,
									'filter_doc_no'			=> $filter_doc_no,
									'filter_status'			=> $filter_status,
									'filter_store'			=> $filter_store,
									'filter_stock_piler'	=> $filter_stock_piler,
									'sort_back'				=> $sort_back,
									'order_back'			=> $order_back,
									'page_back'				=> $page_back,
									'picklist_doc'			=> $picklistDoc,
									'filter_sku'			=> $filter_sku,
									'filter_upc'			=> $filter_upc,
									'filter_so'				=> $filter_so,
									'filter_from_slot'		=> $filter_from_slot,
									'sort'					=> $sort_detail,
									'order'					=> $order_detail
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
		$this->data['filter_store']      = $filter_store;
		$this->data['filter_stock_piler']      = $filter_stock_piler;
		// $this->data['filter_status_detail']  = $filter_status_detail;
		$this->data['sort_back']             = $sort_back;
		$this->data['order_back']            = $order_back;
		$this->data['page_back']             = $page_back;

		// Details
		$this->data['sort']  = $sort_detail;
		$this->data['order'] = $order_detail;
		$this->data['page']  = $page_detail;

		$url = '?filter_sku=' . $filter_sku . '&filter_upc=' . $filter_upc . '&filter_so=' . $filter_so;
		$url .= '&filter_from_slot=' . $filter_from_slot . '&picklist_doc=' . $picklistDoc;
		$url .= '&page=' . $page_detail;

		$this->data['url_export_detail'] =  URL::to('picking/export_detail' . $url);

		$order_sku = ($sort_detail=='sku' && $order_detail=='ASC') ? 'DESC' : 'ASC';
		$order_upc = ($sort_detail=='upc' && $order_detail=='ASC') ? 'DESC' : 'ASC';
		$order_so_no = ($sort_detail=='so_no' && $order_detail=='ASC') ? 'DESC' : 'ASC';
		$order_from_slot_code = ($sort_detail=='from_slot_code' && $order_detail=='ASC') ? 'DESC' : 'ASC';
		// $order_to_slot_code = ($sort_detail=='to_slot_code' && $order_detail=='ASC') ? 'DESC' : 'ASC';




	 	$this->data['filter_doc_no']		=$filter_doc_no;
	 	$this->data['url_back'] 			=URL::to('stocktransfer/PickAndPackStore');
		$this->layout->content = View::make('store_return/pick_pack_detail', $this->data);
	}

	public function getstocktranferload ()
	{
		 
	 	
		$this->data['stock_piler_list'] = $this->getStockPilers();

		$this->data                       = Lang::get('loads');
		//$filter_stock_piler 	= Input::get('filter_stock_piler', NULL);
		//$sort 	= Input::get('sort', 'load_code');
		//$order 	= Input::get('order', 'ASC');
		//$page 	= Input::get('page', 1);

		$this->data['filter_load_code']		= Input::get('filter_load_code', NULL);
		$this->data['filter_stock_piler']	= Input::get('filter_stock_piler', NULL);
		$this->data['filter_entry_date']  = Input::get('filter_entry_date', NULL);

		$this->data['sort'] = Input::get('sort', 'load_code');
		$this->data['order'] = Input::get('order', 'DESC');
		$this->data['page'] = Input::get('page', 1);

		$this->data['url_export'] = URL::to('load/export');
		
		$arrparam=$arrayName = array(
			'filter_load_code' 			=> $this->data['filter_load_code'],
			'filter_assigned_to_user_id'=> $this->data['filter_stock_piler'],
			'filter_ship_at'			=> $this->data['filter_entry_date'],
			'sort' 						=> $this->data['sort'],
			'order' 					=> $this->data['order'],
			'page' 						=> $this->data['page']
			 );
		$results = load::getlist($arrparam);
		$results_total = load::getlist($arrparam,True);

		$this->data['load_list']       = Paginator::make($results, $results_total, 30);
		$this->data['list_count']      = $results_total;
		$this->data['arrparam']        = $arrparam;
		$this->data['counter']         = $this->data['load_list']->getFrom();
	

		$this->data['permissions']     = unserialize(Session::get('permissions'));

		$url                         = '?filter_load_code=' . $this->data['filter_load_code'];
		$url                        .= '&filter_assigned_to_user_id=' . $this->data['filter_stock_piler'];
		$url                        .= '&page=' .$this->data['page'];

		$order_load_code = ($this->data['sort']=='load_code' && $this->data['order']=='ASC') ? 'DESC' : 'ASC';
		$order_date_created = ($this->data['sort']=='load.created_at'&& $this->data['order']=='ASC') ? 'DESC' : 'ASC';
		$order_ship_at = ($this->data['sort']=='ship_at'&& $this->data['order']=='ASC') ? 'DESC' : 'ASC';

		$this->data['sort_load_code']       = URL::to('shipping/list' . $url .'&sort=load_code&order=' . $order_load_code, NULL, FALSE);
		$this->data['sort_date_created']	= URL::to('shipping/list' . $url . '&sort=load.created_at&order=' . $order_date_created, NULL, FALSE);
		$this->data['sort_ship_at']			= URL::to('shipping/list' . $url . '&sort=ship_at&order=' . $order_ship_at, NULL, FALSE);
		//$this->data['sort_entry_date']       = URL::to('purchase_order' . $url . '&sort=entry_date&order=' . $order_entry_date, NULL, FALSE);

		$this->data['url_generate_load_code']	= URL::to('box/new/load');
		$this->data['url_closepicklist']	= URL::to('picking/close');
		$this->data['url_shipped'] = 		URL::to('load/shipLoad');
		$this->layout->content = View::make('store_return.stocktransfer_load', $this->data);
	}
	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		//
	}


	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		//
	}


	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		//
	}


	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		//
	}


	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		//
	}


	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		//
	}


	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}
protected function setURL($forDetail = false, $forBackToList = false) {
		// Search Filters
		$url = '?filter_so_no=' . Input::get('filter_so_no', NULL);
		$url .= '&filter_receiver_no=' . Input::get('filter_receiver_no', NULL);
		// $url .= '&filter_supplier=' . Input::get('filter_supplier', NULL);
		$url .= '&filter_entry_date=' . Input::get('filter_entry_date', NULL);
		$url .= '&filter_stock_piler=' . Input::get('filter_stock_piler', NULL);
		$url .= '&filter_status=' . Input::get('filter_status', NULL);
		$url .= '&filter_back_order=' . Input::get('filter_back_order', NULL);
		$url .= '&filter_brand=' . Input::get('filter_brand', NULL);
		$url .= '&filter_division=' . Input::get('filter_division', NULL);
		$url .= '&filter_shipment_reference_no=' . Input::get('filter_shipment_reference_no', NULL);
		if($forDetail) {
			$url .= '&sort_back=' . Input::get('sort', 'so_no');
			$url .= '&order_back=' . Input::get('order', 'DESC');
			$url .= '&page_back=' . Input::get('page', 1);
		} else {
			if($forBackToList == true) {
				$url .= '&sort=' . Input::get('sort_back', 'so_no');
				$url .= '&order=' . Input::get('order_back', 'DESC');
				$url .= '&page=' . Input::get('page_back', 1);
			} else {
				$url .= '&sort=' . Input::get('sort', 'so_no');
				$url .= '&order=' . Input::get('order', 'DESC');
				$url .= '&page=' . Input::get('page', 1);
			}

		}


		return $url;
	}

}
