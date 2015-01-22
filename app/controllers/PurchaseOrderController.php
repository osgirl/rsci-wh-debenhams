<?php

class PurchaseOrderController extends BaseController {
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

	/**
	* Shows List of Purchase Orders
	*
	* @example  www.example.com/purchase_order
	*
	* @return View of Purchase order list
	*/
	public function showIndex() {
		// Check Permissions
		if (Session::has('permissions')) {
	    	if (!in_array('CanAccessPurchaseOrders', unserialize(Session::get('permissions'))))  {
	    		if (in_array('CanAccessBoxCreation', unserialize(Session::get('permissions')))) {
	    			return Redirect::to('box/list');
	    		} else {
	    			return Redirect::to('user/profile');
	    		}
			}
    	} else {
			return Redirect::to('users/logout');
		}

		$this->getList();
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
		if (Session::has('permissions')) {
	    	if (!in_array('CanAssignPurchaseOrders', unserialize(Session::get('permissions'))) || !in_array('CanAssignPurchaseOrderDetails', unserialize(Session::get('permissions'))))  {
				return Redirect::to('user/profile');
			}
    	} else {
			return Redirect::to('users/logout');
		}

		$pilers = implode(',' , Input::get('stock_piler'));


		//get moved_to_reserve id
		$arrParams = array('data_code' => 'PO_STATUS_TYPE', 'data_value'=> 'in_process');
		$po_status = Dataset::getType($arrParams)->toArray();

		$arrPO = explode(',', Input::get("po_no"));

		foreach ($arrPO as $purchase_order_no) {
			$arrParams = array(
								'assigned_by' 			=> Auth::user()->id,
								'assigned_to_user_id' 	=> $pilers, //Input::get('stock_piler'),
								'po_status' 			=> $po_status['id'], //in_process
								'updated_at' 			=> date('Y-m-d H:i:s')
							);
			PurchaseOrder::assignToStockPiler($purchase_order_no, $arrParams);

			// AuditTrail
			$users = User::getUsersFullname(Input::get('stock_piler'));

			$fullname = implode(', ', array_map(function ($entry) { return $entry['name']; }, $users));
			// $user = User::find(Input::get('stock_piler'));

			$data_before = '';
			$data_after = 'PO No: ' . $purchase_order_no . ' assigned to ' . $fullname;

			$arrParams = array(
							'module'		=> 'Purchase Order',
							'action'		=> 'Assigned PO',
							'reference'		=> $purchase_order_no,
							'data_before'	=> $data_before,
							'data_after'	=> $data_after,
							'user_id'		=> Auth::user()->id,
							'created_at'	=> date('Y-m-d H:i:s'),
							'updated_at'	=> date('Y-m-d H:i:s')
							);
			AuditTrail::addAuditTrail($arrParams);
			// AuditTrail
		}

		if (Input::get('module') == 'purchase_order_detail') {
			$url = $this->setURL();
			$url .= '&id=' . Input::get('id');
			$url .= '&sort_back=' . Input::get('sort_back').'&order_back=' . Input::get('order_back') . '&page_back=' . Input::get('page_back');

			return Redirect::to('purchase_order/detail' . $url)->with('message', Lang::get('purchase_order.text_success_assign'));
		} else {
			return Redirect::to('purchase_order' . $this->setURL())->with('message', Lang::get('purchase_order.text_success_assign'));
		}
	}

	public function closePO()
	{
		// Check Permissions
		if (Session::has('permissions')) {
	    	if (!in_array('CanClosePurchaseOrders', unserialize(Session::get('permissions'))) || !in_array('CanClosePurchaseOrderDetails', unserialize(Session::get('permissions'))))  {
				return Redirect::to('user/profile');
			}
    	} else {
			return Redirect::to('users/logout');
		}

		$po_id = Input::get("id");
		$purchase_order_no = Input::get("po_no");
		$invoice_no = Input::get("invoice_no");
		$invoice_amount = Input::get("invoice_amount");
		$status = 'closed'; // closed
		$date_updated = date('Y-m-d H:i:s');

		PurchaseOrder::updatePOStatus($purchase_order_no, $status, $date_updated, $invoice_no, $invoice_amount);

		/*$skus = PurchaseOrderDetail::getScannedPODetails($po_id);

		foreach($skus as $sku){
			$data = array(
				'sku' => $sku->upc,
				'quantity_delivered' => $sku->quantity_delivered,
				'quantity_remaining' => $sku->quantity_delivered
			);
			SkuOnDock::insertData($data);
		}*/

		// AuditTrail
		$user = User::find(Auth::user()->id);

		$data_before = '';
		$data_after = 'PO No: ' . $purchase_order_no . ' closed by ' . $user->username;

		$arrParams = array(
						'module'		=> 'Purchase Order',
						'action'		=> 'Closed PO',
						'reference'		=> $purchase_order_no,
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
			'reference'		=> $purchase_order_no
		));

		// run daemon command: php app/cron/jda/classes/receive_po.php
		if( $isSuccess )
		{
			$daemonReceivingClosingPo = "classes/receive_po.php {$purchase_order_no}";
			CommonHelper::execInBackground($daemonReceivingClosingPo);
		}

		if (Input::get('module') == 'purchase_order_detail') {
			$url = $this->setURL();
			$url .= '&id=' . Input::get('id');
			$url .= '&sort_back=' . Input::get('sort_back').'&order_back=' . Input::get('order_back') . '&page_back=' . Input::get('page_back');

			return Redirect::to('purchase_order/detail' . $url)->with('message', Lang::get('purchase_order.text_success_close_po'));
		} else {
			return Redirect::to('purchase_order' . $this->setURL())->with('message', Lang::get('purchase_order.text_success_close_po'));
		}
	}


	public function exportCSV() {
		// Check Permissions
		if (Session::has('permissions')) {
	    	if (!in_array('CanExportPurchaseOrders', unserialize(Session::get('permissions'))))  {
				return Redirect::to('purchase_order' . $this->setURL());
			}
    	} else {
			return Redirect::to('users/logout');
		}

		$arrParams = array(
							'filter_po_no' 			=> Input::get('filter_po_no', NULL),
							'filter_receiver_no' 	=> Input::get('filter_receiver_no', NULL),
							// 'filter_supplier' 		=> Input::get('filter_supplier', NULL),
							'filter_entry_date' 	=> Input::get('filter_entry_date',NULL),
							'filter_stock_piler' 	=> Input::get('filter_stock_piler', NULL),
							'filter_status' 		=> Input::get('filter_status', NULL),
							'sort'					=> Input::get('sort', 'po_no'),
							'order'					=> Input::get('order', 'ASC'),
							'page'					=> NULL,
							'limit'					=> NULL
						);

		$results = PurchaseOrder::getPoLists($arrParams);

		$output = Lang::get('purchase_order.col_po_no'). ',';
		$output .= Lang::get('purchase_order.col_receiver_no'). ',';
		// $output .= Lang::get('purchase_order.col_supplier'). ',';
		$output .= Lang::get('purchase_order.col_receiving_stock_piler'). ',';
		$output .= Lang::get('purchase_order.col_invoice_number'). ',';
		$output .= Lang::get('purchase_order.col_invoice_amount'). ',';
		$output .= Lang::get('purchase_order.col_entry_date'). ',';
		$output .= Lang::get('purchase_order.col_status'). "\n";

	    foreach ($results as $key => $value) {
	    	$exportData = array(
	    						'"' . $value->purchase_order_no . '"',
	    						'"' . $value->receiver_no . '"',
	    						'"' . $value->vendor_name . '"',
	    						'"' . $value->firstname . ' ' . $value->lastname . '"',
	    						'"' . $value->invoice_no . '"',
	    						'"' . $value->invoice_amount . '"',
	    						'"' . date("M d, Y", strtotime($value->created_at)) . '"',
	    						'"' . $value->data_display . '"'
	    					);

	      	$output .= implode(",", $exportData);
	      	$output .= "\n";
	  	}

		$headers = array(
			'Content-Type' => 'text/csv',
			'Content-Disposition' => 'attachment; filename="purchase_order_' . date('Ymd')  . '_' . time() . '.csv"',
		);

		return Response::make(rtrim($output, "\n"), 200, $headers);
	}

	public function exportDetailsCSV() {
		///Check Permissions
		if (Session::has('permissions')) {
	    	if (!in_array('CanExportPurchaseOrderDetails', unserialize(Session::get('permissions'))))  {
				return Redirect::to('purchase_order' . $this->setURL());
			}
    	} else {
			return Redirect::to('users/logout');
		}

		if (PurchaseOrder::find(Input::get('id', NULL))!=NULL) {
			$po_id = Input::get('id', NULL);

			$arrParams = array(
							'sort'		=> Input::get('sort', 'sku'),
							'order'		=> Input::get('order', 'ASC'),
							'page'		=> NULL,
							'limit'		=> NULL
						);

			$po_info = PurchaseOrder::getPOInfo($po_id);
			$results = PurchaseOrderDetail::getPODetails($po_id, $arrParams);

			$output = Lang::get('purchase_order.col_sku'). ',';
			$output .= Lang::get('purchase_order.col_upc'). ',';
			$output .= Lang::get('purchase_order.col_short_name'). ',';
			$output .= Lang::get('purchase_order.col_expected_quantity'). ',';
			$output .= Lang::get('purchase_order.col_received_quantity'). "\n";

		    foreach ($results as $key => $value) {
		    	$exportData = array(
		    						'"' . $value->sku . '"',
		    						'"' . $value->upc . '"',
		    						'"' . $value->short_description . '"',
		    						'"' . $value->quantity_ordered . '"',
		    						'"' . $value->quantity_delivered . '"'
		    					);

		      	$output .= implode(",", $exportData);
		      	$output .= "\n";
		  	}

			$headers = array(
				'Content-Type' => 'text/csv',
				'Content-Disposition' => 'attachment; filename="purchase_order_details_' . $po_info->purchase_order_no . '_' . date('Ymd')  . '_' . time() . '.csv"',
			);

			return Response::make(rtrim($output, "\n"), 200, $headers);
		}
	}

	public function getPODetails() {
		// Check Permissions
		if (Session::has('permissions')) {
	    	if (!in_array('CanAccessPurchaseOrderDetails', unserialize(Session::get('permissions'))))  {
				return Redirect::to('purchase_order');
			} elseif (PurchaseOrder::find(Input::get('id', NULL))==NULL) {
				return Redirect::to('purchase_order')->with('error', Lang::get('purchase_order.error_po_details'));
			}
    	} else {
			return Redirect::to('users/logout');
		}

		$this->data['heading_title_po_details'] = Lang::get('purchase_order.heading_title_po_details');
		$this->data['heading_title_po_contents'] = Lang::get('purchase_order.heading_title_po_contents');
		$this->data['heading_title_assign_po'] = Lang::get('purchase_order.heading_title_assign_po');

		$this->data['text_empty_results'] = Lang::get('general.text_empty_results');
		$this->data['text_total'] = Lang::get('general.text_total');
		$this->data['text_select'] = Lang::get('general.text_select');
		$this->data['text_assigned'] = Lang::get('purchase_order.text_assigned');
		$this->data['text_closed_po'] = Lang::get('purchase_order.text_closed_po');
		$this->data['text_warning'] = Lang::get('purchase_order.text_warning');

		$this->data['label_purchase_no'] = Lang::get('purchase_order.label_purchase_no');
		$this->data['label_receiver_no'] = Lang::get('purchase_order.label_receiver_no');
		$this->data['label_supplier'] = Lang::get('purchase_order.label_supplier');
		$this->data['label_entry_date'] = Lang::get('purchase_order.label_entry_date');
		$this->data['label_status'] = Lang::get('purchase_order.label_status');
		$this->data['label_stock_piler'] = Lang::get('purchase_order.label_stock_piler');
		$this->data['label_jda_sync'] = Lang::get('purchase_order.label_jda_sync');
		$this->data['label_app_sync'] = Lang::get('purchase_order.label_app_sync');
		$this->data['label_invoice_amount'] = Lang::get('purchase_order.label_invoice_amount');
		$this->data['label_invoice_number'] = Lang::get('purchase_order.label_invoice_number');

		$this->data['entry_purchase_no'] = Lang::get('purchase_order.entry_purchase_no');
		$this->data['entry_stock_piler'] = Lang::get('purchase_order.entry_stock_piler');
		$this->data['entry_invoice'] = Lang::get('purchase_order.entry_invoice');

		$this->data['col_id'] = Lang::get('purchase_order.col_id');
		$this->data['col_sku'] = Lang::get('purchase_order.col_sku');
		$this->data['col_upc'] = Lang::get('purchase_order.col_upc');
		$this->data['col_short_name'] = Lang::get('purchase_order.col_short_name');
		$this->data['col_expected_quantity'] = Lang::get('purchase_order.col_expected_quantity');
		$this->data['col_received_quantity'] = Lang::get('purchase_order.col_received_quantity');

		$this->data['button_back'] = Lang::get('general.button_back');
		$this->data['button_jda'] = Lang::get('general.button_jda');
		$this->data['button_export'] = Lang::get('general.button_export');
		$this->data['button_close_po'] = Lang::get('purchase_order.button_close_po');
		$this->data['button_assign_to_stock_piler'] = Lang::get('purchase_order.button_assign_to_stock_piler');
		$this->data['button_assign'] = Lang::get('general.button_assign');
		$this->data['button_cancel'] = Lang::get('general.button_cancel');

		$this->data['error_assign_po'] = Lang::get('purchase_order.error_assign_po');
		$this->data['col_expiry_date'] = Lang::get('purchase_order.col_expiry_date');

		// URL
		$this->data['url_export'] = URL::to('purchase_order/export_detail');
		$this->data['url_back'] = URL::to('purchase_order' . $this->setURL(false, true));

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


		// Search Filters
		// Main
		$filter_po_no = Input::get('filter_po_no', NULL);
		$filter_receiver_no = Input::get('filter_receiver_no', NULL);
		// $filter_supplier = Input::get('filter_supplier', NULL);
		$filter_entry_date = Input::get('filter_entry_date', NULL);
		$filter_stock_piler = Input::get('filter_stock_piler', NULL);
		$filter_status = Input::get('filter_status', NULL);

		$sort_back = Input::get('sort_back', 'po_no');
		$order_back = Input::get('order_back', 'ASC');
		$page_back = Input::get('page_back', 1);
		$receiver_no = Input::get('receiver_no', 1);

		// Details
		$sort_detail = Input::get('sort', 'sku');
		$order_detail = Input::get('order', 'ASC');
		$page_detail = Input::get('page', 1);

		//Data
		$po_id = Input::get('id', NULL);
		$this->data['po_info'] = PurchaseOrder::getPOInfo($po_id);

		$arrParams = array(
						'sort'		=> $sort_detail,
						'order'		=> $order_detail,
						'page'		=> $page_detail,
						'limit'		=> 30
					);

		$results 		= PurchaseOrderDetail::getPODetails($po_id, $arrParams);
		$results_total 	= PurchaseOrderDetail::getCountPODetails($po_id, $arrParams);

		// Pagination
		$this->data['arrFilters'] = array(
									'sort'					=> $sort_detail,
									'order'					=> $order_detail,
									'page_back'				=> $page_back,
									'sort_back'				=> $sort_back,
									'order_back'			=> $order_back,
									'id'					=> $po_id,
									'filter_po_no'			=> $filter_po_no,
									'filter_receiver_no'	=> $filter_receiver_no,
									// 'filter_supplier'		=> $filter_supplier,
									'filter_entry_date'		=> $filter_entry_date,
									'filter_stock_piler'	=> $filter_stock_piler,
									'filter_status'			=> $filter_status,
									'receiver_no'			=> $receiver_no,
								);

		$this->data['purchase_orders'] = Paginator::make($results, $results_total, 30);
		$this->data['purchase_orders_count'] = $results_total;

		$this->data['counter'] 	= $this->data['purchase_orders']->getFrom();

		// Main
		$this->data['filter_po_no'] = $filter_po_no;
		$this->data['filter_receiver_no'] = $filter_receiver_no;
		// $this->data['filter_supplier'] = $filter_supplier;
		$this->data['filter_entry_date'] = $filter_entry_date;
		$this->data['filter_stock_piler'] = $filter_stock_piler;
		$this->data['filter_status'] = $filter_status;

		$this->data['sort_back'] = $sort_back;
		$this->data['order_back'] = $order_back;
		$this->data['page_back'] = $page_back;

		// Details
		$this->data['sort_detail'] = $sort_detail;
		$this->data['order_detail'] = $order_detail;
		$this->data['page_detail'] = $page_detail;


		$url = '?filter_po_no=' . $filter_po_no . '&filter_receiver_no=' . $filter_receiver_no;
		// $url .= '&filter_supplier=' . $filter_supplier . '&filter_entry_date=' . $filter_entry_date;
		$url .= '&filter_stock_piler=' . $filter_stock_piler . '&filter_status=' . $filter_status;
		$url .= '&page_back=' . $page_back . '&order_back=' . $order_back . '&sort_back=' . $sort_back. '&id=' . $po_id;
		$url .= '&receiver_no=' . $receiver_no . '&page=' . $page_detail;

		$order_sku = ($sort_detail=='sku' && $order_detail=='ASC') ? 'DESC' : 'ASC';
		$order_upc = ($sort_detail=='upc' && $order_detail=='ASC') ? 'DESC' : 'ASC';
		$order_short_name = ($sort_detail=='short_name' && $order_detail=='ASC') ? 'DESC' : 'ASC';
		$order_expected_quantity = ($sort_detail=='expected_quantity' && $order_detail=='ASC') ? 'DESC' : 'ASC';
		$order_received_quantity = ($sort_detail=='received_quantity' && $order_detail=='ASC') ? 'DESC' : 'ASC';

		$this->data['sort_sku'] = URL::to('purchase_order/detail' . $url . '&sort=sku&order=' . $order_sku, NULL, FALSE);
		$this->data['sort_upc'] = URL::to('purchase_order/detail' . $url . '&sort=upc&order=' . $order_upc, NULL, FALSE);
		$this->data['sort_short_name'] = URL::to('purchase_order/detail' . $url . '&sort=short_name&order=' . $order_short_name, NULL, FALSE);
		$this->data['sort_expected_quantity'] = URL::to('purchase_order/detail' . $url . '&sort=expected_quantity&order=' . $order_expected_quantity, NULL, FALSE);
		$this->data['sort_received_quantity'] = URL::to('purchase_order/detail' . $url . '&sort=received_quantity&order=' . $order_expected_quantity, NULL, FALSE);

		// Permissions
		$this->data['permissions'] = unserialize(Session::get('permissions'));

		$this->layout->content = View::make('purchase_order.detail', $this->data);
	}

	/**
	* Get Purchase order list view
	*
	* @example  $this->getList();
	*
	* @return Purchase order list view
	*/
	protected function getList() {
		$this->data['heading_title'] = Lang::get('purchase_order.heading_title');
		$this->data['heading_title_assign_po'] = Lang::get('purchase_order.heading_title_assign_po');

		$this->data['text_empty_results'] = Lang::get('general.text_empty_results');
		$this->data['text_total'] = Lang::get('general.text_total');
		$this->data['text_select'] = Lang::get('general.text_select');
		$this->data['text_assigned'] = Lang::get('purchase_order.text_assigned');
		$this->data['text_closed_po'] = Lang::get('purchase_order.text_closed_po');
		$this->data['text_warning'] = Lang::get('purchase_order.text_warning');
		$this->data['text_confirm_assign'] = Lang::get('purchase_order.text_confirm_assign');
		$this->data['text_confirm_reopen'] = Lang::get('purchase_order.text_confirm_reopen');
		$this->data['text_posted_po'] = Lang::get('purchase_order.text_posted_po');

		$this->data['label_purchase_no'] = Lang::get('purchase_order.label_purchase_no');
		$this->data['label_receiver_no'] = Lang::get('purchase_order.label_receiver_no');
		$this->data['label_supplier'] = Lang::get('purchase_order.label_supplier');
		$this->data['label_entry_date'] = Lang::get('purchase_order.label_entry_date');
		$this->data['label_receiver'] = Lang::get('purchase_order.label_receiver');
		$this->data['label_status'] = Lang::get('purchase_order.label_status');
		$this->data['label_invoice_amount'] = Lang::get('purchase_order.label_invoice_amount');
		$this->data['label_invoice_number'] = Lang::get('purchase_order.label_invoice_number');

		$this->data['entry_purchase_no'] = Lang::get('purchase_order.entry_purchase_no');
		$this->data['entry_stock_piler'] = Lang::get('purchase_order.entry_stock_piler');
		$this->data['entry_invoice'] = Lang::get('purchase_order.entry_invoice');

		$this->data['col_id'] = Lang::get('purchase_order.col_id');
		$this->data['col_po_no'] = Lang::get('purchase_order.col_po_no');
		$this->data['col_receiver_no'] = Lang::get('purchase_order.col_receiver_no');
		$this->data['col_supplier'] = Lang::get('purchase_order.col_supplier');
		$this->data['col_receiving_stock_piler'] = Lang::get('purchase_order.col_receiving_stock_piler');
		$this->data['col_invoice_number'] = Lang::get('purchase_order.col_invoice_number');
		$this->data['col_invoice_amount'] = Lang::get('purchase_order.col_invoice_amount');
		$this->data['col_entry_date'] = Lang::get('purchase_order.col_entry_date');
		$this->data['col_status'] = Lang::get('purchase_order.col_status');
		$this->data['col_action'] = Lang::get('purchase_order.col_action');
		$this->data['col_back_order'] = Lang::get('purchase_order.col_back_order');
		$this->data['col_carton_id'] = Lang::get('purchase_order.col_carton_id');
		$this->data['col_total_qty'] = Lang::get('purchase_order.col_total_qty');

		$this->data['button_search'] = Lang::get('general.button_search');
		$this->data['button_clear'] = Lang::get('general.button_clear');
		$this->data['button_export'] = Lang::get('general.button_export');
		$this->data['button_jda'] = Lang::get('general.button_jda');
		$this->data['button_close_po'] = Lang::get('purchase_order.button_close_po');
		$this->data['button_assign_to_stock_piler'] = Lang::get('purchase_order.button_assign_to_stock_piler');
		$this->data['button_assign'] = Lang::get('general.button_assign');
		$this->data['button_cancel'] = Lang::get('general.button_cancel');

		$this->data['error_assign'] = Lang::get('purchase_order.error_assign');
		$this->data['error_assign_po'] = Lang::get('purchase_order.error_assign_po');

		// URL
		$this->data['url_export'] = URL::to('purchase_order/export');
		$this->data['url_reopen'] = URL::to('purchase_order/reopen');
		$this->data['url_detail'] = URL::to('purchase_order/detail' . $this->setURL(true));

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
		$this->data['po_status_type'] = Dataset::getTypeInList("PO_STATUS_TYPE");

		$this->data['stock_piler_list'] = $this->getStockPilers();

		// Search Filters
		$filter_po_no = Input::get('filter_po_no', NULL);
		$filter_receiver_no = Input::get('filter_receiver_no', NULL);
		// $filter_supplier = Input::get('filter_supplier', NULL);
		$filter_entry_date = Input::get('filter_entry_date', NULL);
		$filter_stock_piler = Input::get('filter_stock_piler', NULL);
		$filter_status = Input::get('filter_status', NULL);

		$sort = Input::get('sort', 'purchase_order_lists.created_at');
		$order = Input::get('order', 'DESC');
		$page = Input::get('page', 1);

		//Data
		$arrParams = array(
						'filter_po_no' 			=> $filter_po_no,
						'filter_receiver_no' 	=> $filter_receiver_no,
						// 'filter_supplier' 		=> $filter_supplier,
						'filter_entry_date' 	=> $filter_entry_date,
						'filter_stock_piler' 	=> $filter_stock_piler,
						'filter_status' 		=> $filter_status,
						'sort'					=> $sort,
						'order'					=> $order,
						'page'					=> $page,
						'limit'					=> 30
					);

		$results 		= PurchaseOrder::getPoLists($arrParams);
		$results_total 	= PurchaseOrder::getCount($arrParams);

		// Pagination
		$this->data['arrFilters'] = array(
									'filter_po_no' 			=> $filter_po_no,
									'filter_receiver_no' 	=> $filter_receiver_no,
									// 'filter_supplier' 		=> $filter_supplier,
									'filter_entry_date' 	=> $filter_entry_date,
									'filter_stock_piler' 	=> $filter_stock_piler,
									'filter_status' 		=> $filter_status,
									'sort'					=> $sort,
									'order'					=> $order
								);

		$this->data['purchase_orders'] = Paginator::make($results, $results_total, 30);
		$this->data['purchase_orders_count'] = $results_total;

		$this->data['counter'] 	= $this->data['purchase_orders']->getFrom();

		$this->data['filter_po_no'] = $filter_po_no;
		$this->data['filter_receiver_no'] = $filter_receiver_no;
		// $this->data['filter_supplier'] = $filter_supplier;
		$this->data['filter_entry_date'] = $filter_entry_date;
		$this->data['filter_stock_piler'] = $filter_stock_piler;
		$this->data['filter_status'] = $filter_status;

		$this->data['sort'] = $sort;
		$this->data['order'] = $order;
		$this->data['page'] = $page;

		$url = '?filter_po_no=' . $filter_po_no . '&filter_receiver_no=' . $filter_receiver_no;
		$url .= '&filter_entry_date=' . $filter_entry_date;
		$url .= '&filter_stock_piler=' . $filter_stock_piler . '&filter_status=' . $filter_status;
		$url .= '&page=' . $page;

		$order_po_no = ($sort=='po_no' && $order=='ASC') ? 'DESC' : 'ASC';
		$order_receiver_no = ($sort=='receiver_no' && $order=='ASC') ? 'DESC' : 'ASC';
		$order_entry_date = ($sort=='entry_date' && $order=='ASC') ? 'DESC' : 'ASC';

		$this->data['sort_po_no'] = URL::to('purchase_order' . $url . '&sort=po_no&order=' . $order_po_no, NULL, FALSE);
		$this->data['sort_receiver_no'] = URL::to('purchase_order' . $url . '&sort=receiver_no&order=' . $order_receiver_no, NULL, FALSE);
		$this->data['sort_entry_date'] = URL::to('purchase_order' . $url . '&sort=entry_date&order=' . $order_entry_date, NULL, FALSE);

		// Permissions
		$this->data['permissions'] = unserialize(Session::get('permissions'));

		$this->layout->content = View::make('purchase_order.list', $this->data);
	}

	protected function setURL($forDetail = false, $forBackToList = false) {
		// Search Filters
		$url = '?filter_po_no=' . Input::get('filter_po_no', NULL);
		$url .= '&filter_receiver_no=' . Input::get('filter_receiver_no', NULL);
		// $url .= '&filter_supplier=' . Input::get('filter_supplier', NULL);
		$url .= '&filter_entry_date=' . Input::get('filter_entry_date', NULL);
		$url .= '&filter_stock_piler=' . Input::get('filter_stock_piler', NULL);
		$url .= '&filter_status=' . Input::get('filter_status', NULL);
		if($forDetail) {
			$url .= '&sort_back=' . Input::get('sort', 'po_no');
			$url .= '&order_back=' . Input::get('order', 'ASC');
			$url .= '&page_back=' . Input::get('page', 1);
		} else {
			if($forBackToList == true) {
				$url .= '&sort=' . Input::get('sort_back', 'po_no');
				$url .= '&order=' . Input::get('order_back', 'ASC');
				$url .= '&page=' . Input::get('page_back', 1);
			} else {
				$url .= '&sort=' . Input::get('sort', 'po_no');
				$url .= '&order=' . Input::get('order', 'ASC');
				$url .= '&page=' . Input::get('page', 1);
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
	 * Clear/Reopen a purchase order
	 * @return
	 */
	public function reopen()
	{
		CommonHelper::setRequiredFields(array('purchase_order_no'));

		$purchase_order_no = Input::get('purchase_order_no');

		PurchaseOrder::reopenPO(array('po_order_no'=>$purchase_order_no));
		// AuditTrail
		$user = User::find(Auth::user()->id);

		$data_before = '';
		$data_after = 'PO No: ' . $purchase_order_no . ' is reopened by ' . $user->username;

		$arrParams = array(
						'module'		=> 'Purchase Order',
						'action'		=> 'Reopen PO',
						'reference'		=> $purchase_order_no,
						'data_before'	=> $data_before,
						'data_after'	=> $data_after,
						'user_id'		=> Auth::user()->id,
						'created_at'	=> date('Y-m-d H:i:s'),
						'updated_at'	=> date('Y-m-d H:i:s')
						);
		AuditTrail::addAuditTrail($arrParams);
		// AuditTrail

		return Redirect::to('purchase_order' . $this->setURL())
			->with('message', Lang::get('purchase_order.text_success_reopen', array('purchaseOrderNo'=> $purchase_order_no)));
	}


}
