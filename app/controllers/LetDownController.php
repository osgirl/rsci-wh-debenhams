<?php

class LetDownController extends BaseController {
	private $data = array();
	protected $layout = "layouts.main";

	public function __construct() {
		$this->beforeFilter('csrf', array('on' => 'post'));
		$this->beforeFilter('auth', array('only'=> array('Dashboard')));
		$this->apiUrl = Config::get('constant.api_url');
		date_default_timezone_set('Asia/Manila');
	}

	public function showIndex() {
		// Check Permissions
		if (Session::has('permissions')) {
	    	if (!in_array('CanAccessLetDowns', unserialize(Session::get('permissions'))))  {
				return Redirect::to('purchase_order');
			}
    	} else {
			return Redirect::to('users/logout');
		}

		$this->getList();
	}

	public function closeLetdown() {
		// Check Permissions
		if (Session::has('permissions')) {
	    	if (!in_array('CanCloseLetDown', unserialize(Session::get('permissions'))) || !in_array('CanCloseLetDownDetails', unserialize(Session::get('permissions'))))  {
				return Redirect::to('letdown');
			}
    	} else {
			return Redirect::to('users/logout');
		}

		$docNo = Input::get("doc_no");
		$status = Config::get('letdown_statuses.closed');

		Letdown::updateMoveToPickingHeaderStatus($docNo, $status);

		// Add transaction for jda syncing
		$isSuccess = JdaTransaction::insert(array(
			'module' 		=> Config::get('transactions.module_letdown'),
			'jda_action'	=> Config::get('transactions.jda_action_letdown'),
			'reference'		=> $docNo
		));

		//if success run daemon command: php app/cron/jda/daemon_closing_letdown.php
		if( $isSuccess )
		{
			// $letdown = 'daemon_closing_letdown.php';
			$letdown  = "classes/letdown.php {$docNo}";
			CommonHelper::execInBackground($letdown);
		}

		return $this->redirectCloseLetdown( Input::get('module', NULL),  Input::get('page_back', 1),Input::get('sort_back', 'doc_no'),Input::get('order_back', 'ASC'), Input::get('filter_sku', NULL),Input::get('filter_store', NULL),  Input::get('filter_slot', NULL), Input::get('sort', 'doc_no'), Input::get('order', 'ASC'), Input::get('page', 1),Input::get('filter_doc_no', NULL),$docNo, Input::get("id"), Lang::get('letdown.text_success_close_letdown'));
	}

	/*public function exportCSV() {
		// Check Permissions
		if (Session::has('permissions')) {
	    	if (!in_array('CanExportLetDowns', unserialize(Session::get('permissions'))))  {
				return Redirect::to('letdown' . $this->setURL());
			}
    	} else {
			return Redirect::to('users/logout');
		}

		$arrParams = array(
							'filter_doc_no' 		=> Input::get('filter_doc_no', NULL),
							'sort'					=> Input::get('sort', 'doc_no'),
							'order'					=> Input::get('order', 'ASC'),
							'page'					=> NULL,
							'limit'					=> NULL
						);

		$results = Letdown::getLetDownList($arrParams);

		$output = Lang::get('letdown.col_id'). ',';
		$output .= Lang::get('letdown.col_doc_number'). ',';
		$output .= Lang::get('letdown.col_status'). "\n";

	    foreach ($results as $key => $value) {

	    	$exportData = array(
	    						'"' . $value->id . '"',
	    						'"' . $value->move_doc_number . '"',
	    						'"' . $value->lt_status . '"'
	    					);

	      	$output .= implode(",", $exportData);
	      	$output .= "\n";
	  	}

		$headers = array(
			'Content-Type' => 'text/csv',
			'Content-Disposition' => 'attachment; filename="letdown_' . date('Ymd')  . '_' . time() . '.csv"',
		);

		return Response::make(rtrim($output, "\n"), 200, $headers);
	}*/

	public function exportCSV() {
		// Check Permissions
		if (Session::has('permissions')) {
	    	if (!in_array('CanExportLetDowns', unserialize(Session::get('permissions'))))  {
				return Redirect::to('letdown' . $this->setURL());
			}
    	} else {
			return Redirect::to('users/logout');
		}
		$this->data['col_doc_number'] = Lang::get('letdown.col_doc_number');
		$this->data['col_action']     = Lang::get('letdown.col_action');

		$arrParams = array(
							'filter_doc_no' 		=> Input::get('filter_doc_no', NULL),
							'sort'					=> Input::get('sort', 'doc_no'),
							'order'					=> Input::get('order', 'ASC'),
							'page'					=> NULL,
							'limit'					=> NULL
						);

		$results = Letdown::getLetDownList($arrParams);
		$this->data['results'] = $results;

		$pdf = App::make('dompdf');
		$pdf->loadView('letdown.report_list', $this->data)->setPaper('a4')->setOrientation('landscape');
		// return $pdf->stream();
		return $pdf->download('letdown_' . date('Ymd') . '.pdf');
	}

	/*public function exportDetailsCSV() {
		//TODO
		///Check Permissions
		if (Session::has('permissions')) {
	    	if (!in_array('CanExportLetDownDetails', unserialize(Session::get('permissions'))))  {
				return Redirect::to('letdown' . $this->setURL());
			}
    	} else {
			return Redirect::to('users/logout');
		}

		if (Letdown::find(Input::get('id', NULL))!=NULL) {
			$ld_id = Input::get('id', NULL);

			$arrParams = array(
							'sort'			=> Input::get('sort', 'sku'),
							'order'			=> Input::get('order', 'ASC'),
							'filter_sku' 	=> NULL,
							'filter_store' 	=> NULL,
							'filter_slot' 	=> NULL,
							'filter_status' => NULL,
							'page'			=> NULL,
							'limit'			=> NULL
						);

			$ld_info = Letdown::getLetDownInfo($ld_id);
			$results = LetdownDetails::getLetdownDetails($ld_info->move_doc_number, $arrParams);

			$output = Lang::get('letdown.col_upc'). ',';
			$output .= Lang::get('letdown.col_store'). ',';
			$output .= Lang::get('letdown.col_slot'). ',';
			$output .= Lang::get('letdown.col_quantity_to_pick'). ',';
			$output .= Lang::get('letdown.col_picked_quantity'). ',';
			$output .= Lang::get('letdown.col_status'). "\n";


		    foreach ($results as $key => $value) {
		    	$lt_status = "";
		    	if($value->move_to_picking_area == 0) {
		    		$lt_status = Lang::get('letdown.status_not_in_picking');
		    	} else {
		    		$lt_status = Lang::get('letdown.status_in_picking');
		    	}
		    	$exportData = array(
		    						'"' . $value->sku . '"',
		    						'"' . $value->store_name . '"',
		    						'"' . $value->from_slot_code . '"',
		    						'"' . $value->quantity_to_letdown . '"',
		    						'"' . $value->moved_qty . '"',
		    						'"' . $lt_status . '"'
		    					);

		      	$output .= implode(",", $exportData);
		      	$output .= "\n";
		  	}


			$headers = array(
				'Content-Type' => 'text/csv',
				'Content-Disposition' => 'attachment; filename="letdown_details_' . $ld_info->move_doc_number . '_' . date('Ymd')  . '_' . time() . '.csv"',
			);

			return Response::make(rtrim($output, "\n"), 200, $headers);
		}

		return;
	}*/

	public function exportDetailsCSV() {
		//TODO
		///Check Permissions
		if (Session::has('permissions')) {
	    	if (!in_array('CanExportLetDownDetails', unserialize(Session::get('permissions'))))  {
				return Redirect::to('letdown' . $this->setURL());
			}
    	} else {
			return Redirect::to('users/logout');
		}

		if (Letdown::find(Input::get('id', NULL))!=NULL) {
			$ld_id = Input::get('id', NULL);

			$arrParams = array(
							'sort'			=> Input::get('sort', 'sku'),
							'order'			=> Input::get('order', 'ASC'),
							'filter_sku' 	=> NULL,
							'filter_store' 	=> NULL,
							'filter_slot' 	=> NULL,
							'filter_status' => NULL,
							'page'			=> NULL,
							'limit'			=> NULL
						);

			$ld_info = Letdown::getLetDownInfo($ld_id);
			$results = LetdownDetails::getLetdownDetails($ld_info->move_doc_number, $arrParams);

			$this->data['results'] = $results;

			$pdf = App::make('dompdf');
			$pdf->loadView('letdown.report_detail', $this->data)->setPaper('a4')->setOrientation('landscape');
			return $pdf->stream();
			// return $pdf->download('letdown_detail' . date('Ymd') . '.pdf');

		}

		return;
	}


	public function getLetDownDetails() {
		// Check Permissions
		if (Session::has('permissions')) {
	    	if (!in_array('CanAccessLetDownDetails', unserialize(Session::get('permissions'))))  {
				return Redirect::to('letdown');
			} elseif (Letdown::find(Input::get('id', NULL))==NULL) {
				return Redirect::to('letdown')->with('error', Lang::get('letdown.error_letdown_details'));
			}
    	} else {
			return Redirect::to('users/logout');
		}

		$this->data['heading_title_letdown_details'] = Lang::get('letdown.heading_title_letdown_details');
		$this->data['heading_title_letdown_contents'] = Lang::get('letdown.heading_title_letdown_contents');

		$this->data['text_empty_results'] = Lang::get('general.text_empty_results');
		$this->data['text_total'] = Lang::get('general.text_total');
		$this->data['text_closed_letdown'] = Lang::get('letdown.text_closed_letdown');
		$this->data['text_warning'] = Lang::get('letdown.text_warning');


		$this->data['label_upc'] = Lang::get('letdown.label_upc');
		$this->data['label_slot'] = Lang::get('letdown.label_slot');
		$this->data['label_store'] = Lang::get('letdown.label_store');
		$this->data['label_status'] = Lang::get('letdown.label_status');

		$this->data['col_id'] = Lang::get('letdown.col_id');
		$this->data['col_upc'] = Lang::get('letdown.col_upc');
		$this->data['col_store'] = Lang::get('letdown.col_store');
		$this->data['col_slot'] = Lang::get('letdown.col_slot');
		$this->data['col_quantity_to_pick'] = Lang::get('letdown.col_quantity_to_pick');
		$this->data['col_picked_quantity'] = Lang::get('letdown.col_picked_quantity');
		$this->data['col_status'] = Lang::get('letdown.col_status');

		//letdown detail statuses
		$this->data['status_in_picking'] =Lang::get('letdown.status_in_picking');
		$this->data['status_not_in_picking'] =Lang::get('letdown.status_not_in_picking');

		$this->data['button_back'] = Lang::get('general.button_back');
		$this->data['button_jda'] = Lang::get('general.button_jda');
		$this->data['button_export'] = Lang::get('general.button_export');
		$this->data['button_close_letdown'] = Lang::get('letdown.button_close_letdown');
		$this->data['button_cancel'] = Lang::get('general.button_cancel');
		$this->data['button_search'] = Lang::get('general.button_search');
		$this->data['button_clear'] = Lang::get('general.button_clear');

		$this->data['ld_status_type'] = Dataset::getTypeWithValue("LETDOWN_STATUS_TYPE");
		//added this because there is not closed in the detail
		unset($this->data['ld_status_type'][1]);
		// URL
		$this->data['url_export'] = URL::to('letdown/export_detail');
		$this->data['url_back'] = URL::to('letdown' . $this->setURL(false, true));

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
		$filter_slot = Input::get('filter_slot', NULL);

		$filter_doc_no = Input::get('filter_doc_no', NULL);
		$sort_back = Input::get('sort_back', 'sku');
		$order_back = Input::get('order_back', 'ASC');
		$page_back = Input::get('page_back', 1);

		// Details
		$sort_detail = Input::get('sort', 'sku');
		$order_detail = Input::get('order', 'ASC');
		$page_detail = Input::get('page', 1);

		//Data
		$letdown_id = Input::get('id', NULL);
		$this->data['letdown_info'] = Letdown::getLetDownInfo($letdown_id);
		$move_doc_number = Input::get('doc_no', NULL);

		$arrParams = array(
						'sort'		=> $sort_detail,
						'order'		=> $order_detail,
						'page'		=> $page_detail,
						'filter_sku' => $filter_sku,
						'filter_store' => $filter_store,
						'filter_slot'	=> $filter_slot,
						'limit'		=> 30
					);


		$results 		= LetdownDetails::getLetdownDetails($move_doc_number, $arrParams);
		$results_total 	= LetdownDetails::getLetdownDetails($move_doc_number, $arrParams, true);
		// Pagination
		$this->data['arrFilters'] = array(
									'filter_doc_no' => $filter_doc_no,
									'page_back'		=> $page_back,
									'sort_back'		=> $sort_back,
									'order_back'	=> $order_back,
									'filter_sku'	=> $filter_sku,
									'filter_store' 	=> $filter_store,
									'filter_slot'	=> $filter_slot,
									'sort'			=> $sort_detail,
									'order'			=> $order_detail,
									'doc_no'		=> $move_doc_number,
									'id'			=> $letdown_id
								);

		$this->data['letdowns'] = Paginator::make($results, $results_total, 30);
		$this->data['letdowns_count'] = $results_total;

		$this->data['counter'] 	= $this->data['letdowns']->getFrom();
		$this->data['letdown_id'] = $letdown_id;

		// Main
		$this->data['filter_sku'] = $filter_sku;
		$this->data['filter_store'] = $filter_store;
		$this->data['filter_slot'] = $filter_slot;

		$this->data['sort'] = $sort_detail;
		$this->data['order'] = $order_detail;
		$this->data['page'] = $page_detail;

		// Details
		$this->data['sort_back'] 	= $sort_back;
		$this->data['order_back'] 	= $order_back;
		$this->data['page_back'] 	= $page_back;
		$this->data['filter_doc_no']= $filter_doc_no;

		$url = '?filter_sku=' . $filter_sku . '&filter_store=' . $filter_store . '&filter_slot='. $filter_slot. '&filter_doc_no='. $filter_doc_no;
		$url .= '&page_back=' . $page_back . '&sort_back=' . $sort_back . '&order_back=' . $order_back .'&id=' . $letdown_id . '&doc_no= '.$move_doc_number ;


		$order_sku = ($sort_detail=='sku' && $order_detail=='ASC') ? 'DESC' : 'ASC';
		$order_store = ($sort_detail=='store' && $order_detail=='ASC') ? 'DESC' : 'ASC';
		$order_slot = ($sort_detail=='slot' && $order_detail=='ASC') ? 'DESC' : 'ASC';


		$this->data['sort_sku'] = URL::to('letdown/detail' . $url . '&sort=sku&order=' . $order_sku, NULL, FALSE);
		$this->data['sort_slot'] = URL::to('letdown/detail' . $url . '&sort=slot&order=' . $order_slot, NULL, FALSE);
		$this->data['sort_store'] = URL::to('letdown/detail' . $url . '&sort=store&order=' . $order_store, NULL, FALSE);

		// Permissions
		$this->data['permissions'] = unserialize(Session::get('permissions'));
		$this->data['url_detail'] = URL::to('letdown/detail');

		$this->layout->content = View::make('letdown.detail', $this->data);
	}

	protected function getList() {
		$this->data['heading_title'] = Lang::get('letdown.heading_title');

		$this->data['text_empty_results'] = Lang::get('general.text_empty_results');
		$this->data['text_total'] = Lang::get('general.text_total');
		$this->data['text_closed_letdown'] = Lang::get('letdown.text_closed_letdown');
		$this->data['text_warning'] = Lang::get('letdown.text_warning');


		$this->data['label_doc_no'] = Lang::get('letdown.label_doc_no');

		$this->data['col_id'] = Lang::get('letdown.col_id');
		$this->data['col_doc_number'] = Lang::get('letdown.col_doc_number');
		$this->data['col_action'] = Lang::get('letdown.col_action');

		$this->data['button_search'] = Lang::get('general.button_search');
		$this->data['button_clear'] = Lang::get('general.button_clear');
		$this->data['button_export'] = Lang::get('general.button_export');
		$this->data['button_close_letdown'] = Lang::get('letdown.button_close_letdown');
		$this->data['button_lock_tags'] = Lang::get('letdown.button_lock_tags');



		// URL
		$this->data['url_export']   = URL::to('letdown/export' . $this->setURL());
		$this->data['url_detail']   = URL::to('letdown/detail' . $this->setURL(true));
		$this->data['url_locktags'] = URL::to('letdown/locktags' . $this->setURL(true));

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
		$filter_doc_no = Input::get('filter_doc_no', NULL);

		$sort = Input::get('sort', 'doc_no');
		$order = Input::get('order', 'ASC');
		$page = Input::get('page', 1);

		//Data

		$arrParams = array(
						'filter_doc_no' 		=> $filter_doc_no,
						'sort'					=> $sort,
						'order'					=> $order,
						'page'					=> $page,
						'limit'					=> 30
					);

		$results 		= Letdown::getLetDownList($arrParams);
		$results_total 	= Letdown::getLetDownList($arrParams, true);

		// Pagination
		$this->data['arrFilters'] = array(
									'filter_doc_no' 		=> $filter_doc_no,
									'sort'					=> $sort,
									'order'					=> $order
								);

		$this->data['letdowns'] = Paginator::make($results, $results_total, 30);
		$this->data['letdowns_count'] = $results_total;

		$this->data['counter'] 	= $this->data['letdowns']->getFrom();


		$this->data['filter_doc_no'] = $filter_doc_no;

		$this->data['sort'] = $sort;
		$this->data['order'] = $order;
		$this->data['page'] = $page;

		$url = '?filter_doc_no=' . $filter_doc_no;
		$url .= '&page=' . $page;

		$order_sku = ($sort=='sku' && $order=='ASC') ? 'DESC' : 'ASC';
		$order_doc_no = ($sort=='doc_no' && $order=='ASC') ? 'DESC' : 'ASC';

		$this->data['sort_doc_no'] = URL::to('letdown' . $url . '&sort=doc_no&order=' . $order_doc_no, NULL, FALSE);

		// Permissions
		$this->data['permissions'] = unserialize(Session::get('permissions'));

		$this->layout->content = View::make('letdown.list', $this->data);
	}

	public function getLockTagList()
	{
		$this->checkPermissions('CanViewLetdownLockTags', false);

		$this->data['heading_title_letdown_lock_tags'] = Lang::get('letdown.heading_title_letdown_lock_tags');

		$this->data['text_empty_results'] = Lang::get('general.text_empty_results');
		$this->data['text_total'] = Lang::get('general.text_total');
		$this->data['text_select'] = Lang::get('general.text_select');
		$this->data['text_warning_unlock'] = Lang::get('letdown.text_warning_unlock');
		$this->data['text_warning_unlock_single'] = Lang::get('letdown.text_warning_unlock_single');


		$this->data['label_stock_piler'] = Lang::get('letdown.label_stock_piler');
		$this->data['label_doc_no'] = Lang::get('letdown.label_doc_no');
		$this->data['label_upc'] = Lang::get('letdown.label_upc');

		$this->data['col_time_locked'] = Lang::get('letdown.col_time_locked');
		$this->data['col_stock_piler'] = Lang::get('letdown.col_stock_piler');
		$this->data['col_action'] = Lang::get('letdown.col_action');

		$this->data['button_search'] = Lang::get('general.button_search');
		$this->data['button_clear'] = Lang::get('general.button_clear');
		$this->data['button_to_letdown'] = Lang::get('letdown.button_to_letdown');
		$this->data['button_unlock_tags'] = Lang::get('letdown.button_unlock_tags');
		$this->data['button_unlock_tag'] = Lang::get('letdown.button_unlock_tag');


		$this->data['url_to_letdown'] = URL::to('letdown');
		$this->data['url_lock_detail'] = URL::to('letdown/locktags_detail'. $this->setURLLock(true));
		$this->data['url_unlock']= 	URL::to('letdown/unlock');

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

		$this->data['error_no_lock_tag'] = Lang::get('letdown.error_no_lock_tag');

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

		$results 		= LetdownDetails::getLockTags($arrParams)->toArray();
		$results_total 	= LetdownDetails::getLockTags($arrParams, true);

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

		$this->data['sort_lock_tag'] = URL::to('letdown/locktags' . $url . '&sort=lock_tag&order=' . $order_lock_tag, NULL, FALSE);
		// Permissions
		$this->data['permissions'] = unserialize(Session::get('permissions'));

		$this->layout->content = View::make('letdown.locklist', $this->data);

	}

	public function getLockTagDetail()
	{
		$this->checkPermissions('CanViewLetdownLockTags', false);

		$this->data['heading_title_letdown_lock_tags'] = Lang::get('letdown.heading_title_letdown_lock_tags');

		$this->data['text_empty_results'] = Lang::get('general.text_empty_results');
		$this->data['text_total'] = Lang::get('general.text_total');
		$this->data['text_select'] = Lang::get('general.text_select');
		$this->data['text_warning_unlock_single'] = Lang::get('letdown.text_warning_unlock_single');

		$this->data['col_doc_number'] = Lang::get('letdown.col_doc_number');
		$this->data['col_upc'] = Lang::get('letdown.col_upc');
		$this->data['col_product_name'] = Lang::get('letdown.col_product_name');
		$this->data['col_store_code'] = Lang::get('letdown.col_store_code');
		$this->data['col_store'] = Lang::get('letdown.col_store');

		$this->data['button_back'] = Lang::get('letdown.button_back_lock_tags');
		$this->data['button_unlock_tag'] = Lang::get('letdown.button_unlock_tag');

		$this->data['url_back']= 	URL::to('letdown/locktags'. $this->setURLLock(false, true));
		$this->data['url_unlock']= 	URL::to('letdown/unlock');


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

		$results = LetdownDetails::getLockTagDetails($lockTag);
		$resultsTotal = count($results['details']); // since there is no pagination

		$this->data['lock_tag_details'] = $results['details'];
		$this->data['sum_moved']= $results['sum_moved'];
		$this->data['lock_tag_details_count'] = $resultsTotal;
		// Permissions
		$this->data['permissions'] = unserialize(Session::get('permissions'));

		$this->layout->content = View::make('letdown.locklist_details', $this->data);
	}

	public function unlockLetdownTag()
	{
		try {
			$data = Input::all();
			if(!isset($data['lock_tag'])) throw new Exception("Lock tag empty.");
			$lockTags = explode(',',$data['lock_tag']);
			if(empty($lockTags)) throw new Exception("Lock tag empty.");
			DB::beginTransaction();
			LetdownDetails::unlockTag($lockTags);
			self::unlockLetdownTagAuditTrail($lockTags);
			DB::commit();
			return Redirect::to('letdown/locktags'. $this->setURLLock())->with('message', Lang::get('letdown.text_success_unlock'));
		} catch (Exception $e) {
			DB::rollback();
			return Redirect::to('letdown/locktags'. $this->setURLLock())->withErrors(Lang::get('letdown.text_fail_unlock'));
		}

	}

	protected function setURL($forDetail = false, $forBackToList = false) {
		// Search Filters
		$url = '?filter_doc_no=' . Input::get('filter_doc_no', NULL);

		if($forDetail) {
			$url .= '&page_back=' . Input::get('page', 1);
			$url .= '&sort_back=' . Input::get('sort', 'doc_no');
			$url .= '&order_back=' . Input::get('order', 'ASC');
		} else {
			if($forBackToList == true) {
				$url .= '&page=' . Input::get('page_back', 1);
				$url .= '&sort=' . Input::get('sort_back', 'doc_no');
				$url .= '&order=' . Input::get('order_back', 'ASC');
			} else {
				$url .= '&page=' . Input::get('page', 1);
				$url .= '&sort=' . Input::get('sort', 'doc_no');
				$url .= '&order=' . Input::get('order', 'ASC');
			}
		}

		return $url;
	}

	protected function setURLLock($forDetail = false, $forBackToList = false) {
		// Search Filters
		$url = '?filter_stock_piler=' . Input::get('filter_stock_piler', NULL);
		$url .= '?filter_doc_no=' . Input::get('filter_doc_no', NULL);
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

	private function closeLetdownAuditTrail($docNo)
	{
		$user = User::find(Auth::user()->id);
		$data_before = '';
		$data_after = 'Letdown Document No: ' . $docNo . ' closed by ' . $user->username;

		$arrParams = array(
						'module'		=> Config::get('audit_trail_modules.letdown'),
						'action'		=> Config::get('audit_trail.post_letdown_close'),
						'reference'		=> $docNo,
						'data_before'	=> $data_before,
						'data_after'	=> $data_after,
						'user_id'		=> Auth::user()->id,
						'created_at'	=> date('Y-m-d H:i:s'),
						'updated_at'	=> date('Y-m-d H:i:s')
						);
		AuditTrail::addAuditTrail($arrParams);
	}

	/**
	* Letdown lock tag unlock audit trail
	*
	* @example  self::unlockLetdownTagAuditTrail()
	*
	* @param  lockTags      Where something interesting takes place
	* @return void
	*/
	private function unlockLetdownTagAuditTrail($lockTags)
	{

		$lockTags = implode(',', $lockTags);
		$data_after = 'Locktags# '.$lockTags . ' unlocked by' . Auth::user()->username;

		$arrParams = array(
						'module'		=> Config::get('audit_trail_modules.letdown'),
						'action'		=> Config::get('audit_trail.unlock_letdown_tag'),
						'reference'		=> 'Lock tags # '. $lockTags,
						'data_before'	=> '',
						'data_after'	=> $data_after,
						'user_id'		=> Auth::user()->id,
						'created_at'	=> date('Y-m-d H:i:s'),
						'updated_at'	=> date('Y-m-d H:i:s')
						);
		AuditTrail::addAuditTrail($arrParams);
	}

	private function redirectCloseLetdown($module,$pageBack, $sortBack, $orderBack,  $filter_sku, $filter_store, $filter_slot, $sort, $order, $page, $filter_doc_no, $docNo, $id, $successMessage)
	{
		if ($module == 'letdown_detail') {
			$url = '?page_back=' .$pageBack. '&order_back=' . $orderBack .'&sort_back=' . $sortBack . '&filter_sku=' . $filter_sku . '&filter_store=' . $filter_store . '&filter_slot='. $filter_slot . '&filter_doc_no='. $filter_doc_no;
			$url .= '&sort=' . $sort . '&order=' . $order . '&page=' . $page;
			$url .= '&id=' . $id . '&doc_no= '.$docNo;

			return Redirect::to('letdown/detail' . $url)->with('message', $successMessage);
		} else {
			return Redirect::to('letdown' . $this->setURL())->with('message', $successMessage);
		}
	}

	private function checkPermissions($permission, $withId = true)
	{
		// Check Permissions
		if (Session::has('permissions')) {
	    	if (!in_array($permission, unserialize(Session::get('permissions'))))  {
				return Redirect::to('letdown');
			} elseif (Letdown::find(Input::get('id', NULL))==NULL) {
				if($withId) {
					return Redirect::to('letdown')->with('error', Lang::get('letdown.error_letdown_details'));
				}

			}
    	} else {
			return Redirect::to('users/logout');
		}
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


}
