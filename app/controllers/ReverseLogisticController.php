<?php

class ReverseLogisticController extends BaseController {
private $data = array();
protected $layout = "layouts.main";



	public function __construct() {
    	date_default_timezone_set('Asia/Manila');
		$this->beforeFilter('csrf', array('on' => 'post'));
		$this->beforeFilter('auth', array('only'=> array('Dashboard')));
	}
	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function getdiscrepancy()
	{
		$this->data                       = Lang::get('picking');
		$this->data['text_empty_results'] = Lang::get('general.text_empty_results');
		$this->data['text_total']         = Lang::get('general.text_total');
		$this->data['text_select']        = Lang::get('general.text_select');
		$this->data['button_back']        = Lang::get('general.button_back');
		$this->data['button_search']      = Lang::get('general.button_search');
		$this->data['button_clear']       = Lang::get('general.button_clear');
		$this->data['url_back']           = URL::to('picking/list');

		$this->data['url_export']             = URL::to('reverse_logistic/exportCSV');
		$this->data['url_exportexcel']             = URL::to('reverse_logistic/exportCSVexcelfile');

	 
		//added this because there is not closed in the detail
	 

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
		$filter_entry_date      = Input::get('filter_entry_date', NULL);
		$filter_sku           = Input::get('filter_sku', NULL);
		$filter_upc           = Input::get('filter_upc', NULL);
		$filter_so            = Input::get('filter_so', NULL);
		$filter_from_slot     = Input::get('filter_from_slot', NULL);
		$filter_store     = Input::get('filter_store', NULL);
		$filter_stock_piler     = Input::get('filter_stock_piler', NULL);
 		$picklist_doc 			= Input::get('picklist_doc', null);

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
						'filter_entry_date'		=> $filter_entry_date,
				 		'filter_doc_no'			=> $filter_doc_no,
				 		'filter_stock_piler'	=> $filter_stock_piler,
						'sort'					=> $sort_detail,
						'order'					=> $order_detail,
						'page'					=> $page_detail,
					 
						'limit'					=> 30
					);
		$results 		= Reverselogistic::getRLVarianceReport($arrParams)->toArray();
		$results_total 	= Reverselogistic::getRLVarianceReport($arrParams, true);
		// echo "<pre>"; print_r($results);die();

		// Pagination
		$this->data['arrFilters'] = array(
								 
									'filter_type'			=> $filter_type,
									'filter_doc_no'			=> $filter_doc_no,
									'filter_entry_date'		=> $filter_entry_date,
									'filter_store'			=> $filter_store,
									'filter_stock_piler'	=> $filter_stock_piler,
									'sort_back'				=> $sort_back,
									'order_back'			=> $order_back,
									'page_back'				=> $page_back, 
									'filter_sku'			=> $filter_sku,
									'filter_upc'			=> $filter_upc,
									'filter_so'				=> $filter_so,
									'filter_from_slot'		=> $filter_from_slot,
									'sort'					=> $sort_detail,
									'order'					=> $order_detail
								);

	/*	print_r($results);
		exit();*/
		$this->data['reverse_discrepancy']  = Paginator::make($results, $results_total, 30);
		$this->data['picklist_detail_count'] = $results_total;
		$this->data['counter']               = $this->data['reverse_discrepancy']->getFrom();
 
		$this->data['filter_type']           = $filter_type;
		$this->data['filter_doc_no']         = $filter_doc_no;
		$this->data['filter_entry_date']         = $filter_entry_date;
		$this->data['filter_sku']            = $filter_sku;
		$this->data['filter_upc']            = $filter_upc;
		$this->data['filter_so']             = $filter_so;
		$this->data['filter_from_slot']      = $filter_from_slot;
		$this->data['filter_store']      = $filter_store;
		$this->data['filter_stock_piler']      = $filter_stock_piler; 
		$this->data['sort_back']             = $sort_back;
		$this->data['order_back']            = $order_back;
		$this->data['page_back']             = $page_back;

		// Details
		$this->data['sort']  = $sort_detail;
		$this->data['order'] = $order_detail;
		$this->data['page']  = $page_detail;

		$url = '?filter_doc_no=' . $filter_doc_no . '&filter_upc=' . $filter_upc . '&filter_so=' . $filter_so;
		$url .= '&filter_entry_date=' . $filter_entry_date;
		$url .= '&page=' . $page_detail;

 
 
	 

		$this->layout->content = View::make('reverse_logistic.reverse_discrepancy', $this->data);

	}
	 public function closeReverseStatus()
	{
		 $tl_number 		= Input::get('tl_number',null);
		$picklist = ReverseLogistic::getReverseTLnumbercclose($tl_number);
		 
		return Redirect::to('reverse_logistic/reverse_list')->with('message', Lang::get('reverselogistic.reverse_text_successfully'));
	}

	public function ReverseTLnumbersync()
	{
			ReverseLogistic::getReverseTLnumbersync();
		return Redirect::to('reverse_logistic/reverse_list')->with('message','Sync To Mobile Successfully');
	}
	public function exportDetailsCSV() {
		///Check Permissions
	
 
			$filter_so_no = Input::get('filter_so_no', NULL);
			$filter_store_name = Input::get('filter_store_name', NULL);
			$filter_created_at = Input::get('filter_created_at', NULL);
			$filter_status = Input::get('filter_status', NULL);
			$filter_doc_no 	= Input::get('filter_doc_no', null);
			$filter_entry_date = Input::get('filter_entry_date', null);
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
			$this->data['text_empty_results'] = Lang::get('general.text_empty_results');

			$arrParams = array(
					'id'             	=> $so_id,
					'sort'              => $sort_detail,
					'order'             => $order_detail,
					'page'              => $page_detail,
					'so_no'             => $so_no,
					'filter_so_no'      => $filter_so_no,
		 			'filter_doc_no'		=> $filter_doc_no,
		 			'filter_entry_date'	=> $filter_entry_date,
		 			'filter_created_at' => $filter_created_at,
					'filter_status'     => $filter_status,
					'limit' => NULL
				);

		 
			
		$results = Reverselogistic::getRLVarianceReport($arrParams);

			$this->data['results'] = $results;

			$pdf = App::make('dompdf');
			$pdf->loadView('reverse_logistic.report_list', $this->data)->setPaper('a4')->setOrientation('landscape');
			// return $pdf->stream();
			return $pdf->download('return_to_warehouse_' . date('Ymd') . '.pdf');
		 
	}

	public function exportCSVexcelfile()
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
		$results = reverselogistic::getRLVarianceReport($arrParams);

 
	 
		$output = Lang::get('picking.col_doc_no'). ',';
		$output .= Lang::get('picking.col_store_name'). ',';
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
			'Content-Disposition' => 'attachment; filename="return_to_warehouse_' . date('Ymd')  . '_' . time() . '.csv"',
		);

		return Response::make(rtrim($output, "\n"), 200, $headers);

	}
	public function exportReverseUnlisted() {
		///Check Permissions
	
 
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
			$this->data['so_status_type'] = Dataset::getTypeWithValue("SR_STATUS_TYPE");
			$this->data['text_empty_results'] = Lang::get('general.text_empty_results');

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

		 
			
		$results = Reverselogistic::getRLUnlistedReport($arrParams);

			$this->data['results'] = $results;

			$pdf = App::make('dompdf');
			$pdf->loadView('reverse_logistic.report_unlisted', $this->data)->setPaper('a4')->setOrientation('landscape');
			// return $pdf->stream();
			return $pdf->download('reverse_logistic_unlisted' . date('Ymd') . '.pdf');
		 
	}
	public function exportCSV() {
		// Check Permissions
	
	 
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
			$this->data['so_status_type'] = Dataset::getTypeWithValue("SR_STATUS_TYPE");
			$this->data['text_empty_results'] = Lang::get('general.text_empty_results');

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

		$this->data['results'] = $results;

			$pdf = App::make('dompdf');
			$pdf->loadView('reverse_logistic.report_list', $this->data)->setPaper('a4')->setOrientation('landscape');
			// return $pdf->stream();
			return $pdf->download('reverse_logistic_variance_' . date('Ymd') . '.pdf');
	
	}

	 
	

	public function getreverselist()
	{
		 
		$this->data = Lang::get('reverselogistic');
		$this->data['text_select']            = Lang::get('general.text_select');
 		$this->data['url_export'] 			= URL::to('reverse_logistic/exportCSV');
 		$this->data['url_export_unlisted']  = URL::to('reverse_logistic/exportCSVunlisted');
	 	$this->data['text_total']         = Lang::get('general.text_total');
		$this->data['url_detail']         = URL::to('reverse_logistic/detail');
		// Search filters
			$this->data['error'] = '';
		if (Session::has('error')) {
			$this->data['error'] = Session::get('error');
		}

		$this->data['success'] = '';
		if (Session::has('success')) {
			$this->data['success'] = Session::get('success');
		}
 		
		$this->data['stock_piler_list'] = $this->getStockPilers();

		$this->data['stores']                 = Store::lists( 'store_name', 'store_code');
		// Search Filters
		$filter_entry_date = Input::get('filter_entry_date', NULL);
		$filter_doc_no = Input::get('filter_doc_no', NULL);
		$filter_status = Input::get('filter_status', NULL);
		$filter_store = Input::get('filter_store', NULL);
		$filter_stock_piler = Input::get('filter_stock_piler', NULL);

        $filter_transfer_no = Input::get('filter_transfer_no', NULL);
        $filter_action_date = Input::get('filter_action_date', NULL);

		$sort = Input::get('sort', 'doc_no');
		$order = Input::get('order', 'ASC');
		$page = Input::get('page', 1);

		//Data
		$arrParams = array(
			
						'filter_entry_date' 	=> $filter_entry_date,
						'filter_doc_no' 		=> $filter_doc_no,
						'filter_status' 		=> $filter_status,
						'filter_store' 			=> $filter_store,
						'filter_stock_piler' 	=> $filter_stock_piler,
                        'filter_transfer_no' 	=> $filter_transfer_no,
                        'filter_action_date' 	=> $filter_action_date,
						'sort'					=> $sort,
						'order'					=> $order,
						'page'					=> $page,
						'limit'					=> 30
					);


 		  
 		$results 		= ReverseLogistic::getReverseLogisticList($arrParams)->toArray();
		$results_total 	= reverselogistic::getReverseLogisticList($arrParams, TRUE);
	 
	 

		$this->data['arrFilters'] = array(
									'filter_entry_date' 			=> $filter_entry_date,
									'filter_doc_no' 		=> $filter_doc_no,
									'filter_status' 		=> $filter_status,
									'filter_store' 			=> $filter_store,
									'filter_stock_piler' 	=> $filter_stock_piler,
                                    'filter_transfer_no' 	=> $filter_transfer_no,
                                    'filter_action_date' 	=> $filter_action_date,
									'sort'					=> $sort,
									'order'					=> $order
								);

		$this->data['reverselogisticlist'] = Paginator::make($results, $results_total, 30);
		$this->data['picklist_count'] = $results_total;
		$this->data['counter'] 	= $this->data['reverselogisticlist']->getFrom();
		$this->data['filter_entry_date'] = $filter_entry_date;
		$this->data['filter_doc_no'] = $filter_doc_no;
		$this->data['filter_status'] = $filter_status;
		$this->data['filter_store'] = $filter_store;
		$this->data['filter_stock_piler'] = $filter_stock_piler;
        $this->data['filter_transfer_no'] = $filter_transfer_no;
        $this->data['filter_action_date'] = $filter_action_date;
		$this->data['sort'] = $sort;
		$this->data['order'] = $order;
		$this->data['page'] = $page;

		$url = '?filter_entry_date=' . $filter_entry_date . '&filter_doc_no=' . $filter_doc_no;
		$url .= '&filter_status=' . $filter_status . '&filter_store=' . $filter_store;
		$url .= '&filter_stock_piler=' . $filter_stock_piler;
        $url .= '&filter_transfer_no=' . $filter_transfer_no;
        $url .= '&filter_action_date=' . $filter_action_date;
		$url .= '&page=' . $page;

		$this->layout->content = View::make('reverse_logistic/reverse_list', $this->data);
	}


	public function getSODetails() {
		// Check Permissions
	
		// URL
	

		// Message
		 
		$picklistDoc = Input::get('picklist_doc', NULL);

		$this->data                       = Lang::get('picking');
		$this->data['text_empty_results'] = Lang::get('general.text_empty_results');
		$this->data['text_total']         = Lang::get('general.text_total');
		$this->data['text_select']        = Lang::get('general.text_select');
		$this->data['button_back']        = Lang::get('general.button_back');
		$this->data['button_search']      = Lang::get('general.button_search');
		$this->data['button_clear']       = Lang::get('general.button_clear');
		$this->data['pick_status_type']   = Dataset::getTypeWithValue("PICKLIST_STATUS_TYPE");
		//added this because there is not closed in the detail

		$this->data['url_back']           = URL::to('reverse_logistic/reverse_list' );
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
						'sort'					=> $sort_detail,
						'order'					=> $order_detail,
						'page'					=> $page_detail,
						'picklist_doc'			=> $picklistDoc,
						'limit'					=> 30
					);
 

		$results 		= ReverselogisticDetails::getReversedetails($arrParams);
		$results_total 	= ReverselogisticDetails::getReversedetails($arrParams, true);
	
		
		// Pagination
		$this->data['arrFilters'] = array( 
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

		$this->data['reversecountdetail']       = Paginator::make($results->toArray(), $results_total, 30);
		$this->data['picklist_detail_count'] = $results_total;
		$this->data['counter']               = $this->data['reversecountdetail']->getFrom();
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
/*
		$order_sku = ($sort_detail=='sku' && $order_detail=='ASC') ? 'DESC' : 'ASC';
		$order_upc = ($sort_detail=='upc' && $order_detail=='ASC') ? 'DESC' : 'ASC';
		$order_short_name = ($sort_detail=='short_name' && $order_detail=='ASC') ? 'DESC' : 'ASC';
		$order_delivered_quantity = ($sort_detail=='delivered_quantity' && $order_detail=='ASC') ? 'DESC' : 'ASC';
		$order_allocated_quantity = ($sort_detail=='allocated_quantity' && $order_detail=='ASC') ? 'DESC' : 'ASC';
		$order_dispatched_quantity = ($sort_detail=='dispatched_quantity' && $order_detail=='ASC') ? 'DESC' : 'ASC';
*/

		//header table sort order

		// Permissions

	$this->layout->content = View::make('reverse_logistic.detail', $this->data);
	}
	public function assignPilerFormReverse() {
	
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
		$this->data['url_back']         = URL::to('reverse_logistic/reverse_list');
		$this->data['params']           = explode(',', Input::get('so_no'));
		$this->data['info']             = Reverselogistic::getInfoBySoNo($this->data['params']);

		$this->layout->content    = View::make('reverse_logistic.assign_piler_form', $this->data);
	}

	public function assignToStockPilerReversepost() {
		// Check Permissions
		$pilers = implode(',' , Input::get('stock_piler'));

		//get moved_to_reserve id
		$arrParams = array('data_code' => 'SR_STATUS_TYPE', 'data_value'=> 'assigned');
		$storeReturnStatus = Dataset::getType($arrParams)->toArray();

		$arrSoNo = explode(',', Input::get("so_no"));

		foreach ($arrSoNo as $soNo) {
			$arrParams = array(
								'assigned_by' 			=> Auth::user()->id,
								'assigned_to_user_id' 	=> $pilers, //Input::get('stock_piler'),
								'so_status' 			=> $storeReturnStatus['id'], //assigned
								 
							);
		ReverseLogistic::assignToStockPilerReverse($soNo, $arrParams);

			// AuditTrail
	 }


		return Redirect::to('reverse_logistic/reverse_list')->with('message', Lang::get('reverselogistic.text_success_assign'));

	}
	private function getStockPilers()
	{
		$stock_pilers = array();
		foreach (User::getStockPilerOptions() as $item) {
			$stock_pilers[$item->id] = $item->firstname . ' ' . $item->lastname;
		}
		return array('' => Lang::get('general.text_select')) + $stock_pilers;
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


}
