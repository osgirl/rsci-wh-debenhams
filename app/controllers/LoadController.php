<?php

class LoadController extends BaseController {
	private $data = array();

	public $layout = "layouts.main";

	public function __construct()
    {
    	date_default_timezone_set('Asia/Manila');
    	$this->beforeFilter('csrf', array('on' => 'post'));
    	$this->beforeFilter('auth', array('only' => array('Dashboard')));

    	// Check Permissions
    	if (Session::has('permissions')) {
	    	if (!in_array('CanAccessShipping', unserialize(Session::get('permissions'))))  {
				return Redirect::to('/');
			}
    	} else {
			return Redirect::to('users/logout');
		}
    }

	public function showIndex() {
		// Check Permissions

    	if (Session::has('permissions')) {
	    	if (!in_array('CanAccessShipping', unserialize(Session::get('permissions'))))  {
				return Redirect::to('/');
			}
    	} else {
			return Redirect::to('users/logout');
		}

		$this->getList();
	}
	public function getBarcode()
	{

/*
 *  Author	David S. Tufts
 *  Company	davidscotttufts.com
 *	  
 *  Date:	05/25/2003
 *  Usage:	<img src="/barcode.php?text=testing" alt="testing" />
 */
// For demonstration purposes, get pararameters that are passed in through $_GET or set to the default value
 
 
	}
	public function exportCSV() {
		// Check Permissions
	/*	if (Session::has('permissions')) {
	    	if (!in_array('CanExportShipping', unserialize(Session::get('permissions'))))  {
				return Redirect::to('load/list');
			}
    	} else {
			return Redirect::to('users/logout');
		}
	*/
		$this->data = Lang::get('loads');
		$loadnumber 		= Input::get('loadnumber', NULL);

		$filter_load_code 		= Input::get('filter_load_code', NULL);
		$arrParams = array(
							'filter_load_code'	=> Input::get('filter_load_code', NULL), 

							'sort'				=> Input::get('sort', 'load_code'),
							'order'				=> Input::get('order', 'ASC'),
							'page'				=> NULL,
							'limit'				=> NULL
						);

		$results = Load::getExportLoadList($loadnumber, $arrParams);
		print_r($results);
		exit();
		$this->data['results'] = $results;

		$this->data['filter_load_code'] 	= $filter_load_code;
		$this->data['loadnumber'] 		=$loadnumber;

		$pdf = App::make('dompdf');
		$pdf->loadView('loads.report_list', $this->data)->setPaper('a4')->setOrientation('landscape');
		// return $pdf->stream();
		return $pdf->download('loads_' . date('Ymd') . '.pdf');
	}
	public function exportCSVbarcode() {
		// Check Permissions
	 

	 

		$results = Load::all(["load_code"]);

echo '<img src="data:image/png;base64,' . DNS1D::getBarcodePNG("1", "C128",3,33) . '" alt="barcode"   />';
 
 
 exit();
		/*$this->layout->content = View::make('picking/barcode')->with('producto',$results);*/
	 /*	$pdf = App::make('dompdf');
		$pdf->loadView('picking.barcode', $this->data)->setPaper('a7')->setOrientation('landscape');
		// return $pdf->stream();
		return $pdf->download('loads_' . date('Ymd') . '.pdf'); */
	}

	protected function getList()
	{
		$this->data = Lang::get('loads');

		// URL
		$this->data['url_export'] = URL::to('load/export');
		$this->data['url_ship_load'] = URL::to('load/ship');
		$this->data['url_load_print'] = URL::to('load/print');



		// Search Filters
		$filter_load_code = Input::get('filter_load_code', NULL);
		$filter_status = Input::get('filter_status', NULL);

		$sort = Input::get('sort', 'load_code');
		$order = Input::get('order', 'ASC');
		$page = Input::get('page', 1);

		// Data
		$arrParams = array(
							'filter_load_code'	=> $filter_load_code,
							'filter_status' 	=> $filter_status,
							'sort'				=> $sort,
							'order'				=> $order,
							'page'				=> $page,
							'limit'				=> 30
						);
		$results = Load::getLoadList($arrParams)->toArray();
		// echo '<pre>'; dd($results);
		$results_total = Load::getLoadList($arrParams,true);

		// Pagination
		$this->data['arrFilters'] = array(
										'filter_load_code'	=> $filter_load_code,
										'filter_status' 	=> $filter_status,
										'sort'				=> $sort,
										'order'				=> $order
									);

		$this->data['load'] = Paginator::make($results, $results_total, 30);
		$this->data['load_count'] = $results_total;

		$this->data['counter'] 	= $this->data['load']->getFrom();

		$this->data['filter_load_code'] = $filter_load_code;
		$this->data['filter_status'] = $filter_status;

		$this->data['sort'] = $sort;
		$this->data['order'] = $order;
		$this->data['page'] = $page;

		$url = '?filter_load_code=' . $filter_load_code;
		$url .= '&filter_status=' . $filter_status;
		$url .= '&page=' . $page;

		$order_load_code = ($sort=='load_code' && $order=='ASC') ? 'DESC' : 'ASC';
		$order_status = ($sort=='status' && $order=='ASC') ? 'DESC' : 'ASC';

		$this->data['sort_load_code'] = URL::to('load/list' . $url . '&sort=load_code&order=' . $order_load_code, NULL, FALSE);
		$this->data['sort_status'] = URL::to('load/list' . $url . '&sort=status&order=' . $order_status, NULL, FALSE);

		// Permissions
		$this->data['url_back'] =$this->setURL();
		$this->data['permissions'] = unserialize(Session::get('permissions'));

		$this->layout->content = View::make('loads.load_details', $this->data);

	}

	public function shipLoad()
	{
		try {
			$data = Input::all();
			if(!isset($data['load_code'])) 
			throw new Exception("Load code empty.");
			DB::beginTransaction();
			$params = array('load_code' => $data['load_code']);
			$isSuccess = Load::shipLoad($params);

			if( $isSuccess )
			{
				$shippingParams = array(
					'module' 		=> Config::get('transactions.module_shipping'),
					'jda_action'	=> Config::get('transactions.jda_action_shipping'),
					'reference'		=> $data['load_code']
				);
				//create jda transaction for shipping
				$jda = JdaTransaction::insert($shippingParams);

				//if success run daemon command: php app/cron/jda/picklist.php
				if( $jda ) {
					$shipping	= "classes/palletizing_step1.php {$data['load_code']}";
					CommonHelper::execInBackground($shipping,'palletizing_step1');
				}
			}
			self::shipLoadToBoxAuditTrail($data['load_code']);
			DB::commit();

			return Redirect::to('load/list'. $this->setURL())->with('message', Lang::get('loads.text_success_shipped'));
		} catch (Exception $e) {
			DB::rollback();
			return Redirect::to('load/list'. $this->setURL())->withErrors(Lang::get('loads.text_fail_load'));
		}
		die();
	}

	public function printLoad($loadCode)
	{
		try {
			// Search Filters
			$filter_load_code = Input::get('filter_load_code', NULL);

			$sort = Input::get('sort', 'doc_no');
			$order = Input::get('order', 'ASC');
			$page = Input::get('page', 1);

			$this->data['filter_load_code'] = $filter_load_code;
			$this->data['sort'] = $sort;
			$this->data['order'] = $order;
			$this->data['page'] = $page;

			$this->data['url_back'] = $this->setURL();

			$this->data['loadCode'] = $loadCode;
			$this->data['records'] = Load::getLoadDetails($loadCode);
			$this->data['permissions'] = unserialize(Session::get('permissions'));
            $load=Load::select('printMTS')->where('load_code','=',$loadCode)->get();
            $this->data['print_status']=$load[0]['printMTS'];

            // get the comments to MTS reports
            // $this->data['comments']= Load::getCommentsByLoadCode($loadCode);

            $this->layout = View::make('layouts.print');
            $this->layout->content = View::make('loads.printmts', $this->data);

		} catch (Exception $e) {
			return Redirect::to('load/list'. $this->setURL())->withErrors(Lang::get('loads.text_fail_load'));
		}
	}

    public function printPackingList($loadCode)
    {
		// Search Filters
		$filter_load_code = Input::get('filter_load_code', NULL);

		$sort = Input::get('sort', 'doc_no');
		$order = Input::get('order', 'ASC');
		$page = Input::get('page', 1);

		$this->data['filter_load_code'] = $filter_load_code;
		$this->data['sort'] = $sort;
		$this->data['order'] = $order;
		$this->data['page'] = $page;

		$this->data['url_back'] = $this->setURL();

            $this->data['loadCode'] = $loadCode;
            $this->data['records'] = Load::getPackingDetails($loadCode);
			$this->data['storelocation'] = load::getStoreLocationwarehouse($loadCode);
            $this->data['permissions'] = unserialize(Session::get('permissions'));
            $load=Load::select('printPacking')->where('load_code','=',$loadCode)->get();
            $this->data['print_status']=$load[0]['printPacking'];
 
            $this->layout = View::make('layouts.print');
            $this->layout->content = View::make('loads.print_packing_list', $this->data);
    }
    public function printPackingListstock($loadCode)
    {
		// Search Filters
		$filter_load_code = Input::get('filter_load_code', NULL);

		$sort = Input::get('sort', 'doc_no');
		$order = Input::get('order', 'ASC');
		$page = Input::get('page', 1);

		$this->data['filter_load_code'] = $filter_load_code;
		$this->data['sort'] = $sort;
		$this->data['order'] = $order;
		$this->data['page'] = $page;

		$this->data['url_back'] = $this->setURL();

            $this->data['loadCode'] = $loadCode;
            $this->data['records'] = Load::getPackingDetailsstock($loadCode);
			$this->data['storelocation'] = load::getStoreLocation($loadCode);
            $this->data['permissions'] = unserialize(Session::get('permissions'));
            $load=Load::select('printPacking')->where('load_code','=',$loadCode)->get();
            $this->data['print_status']=$load[0]['printPacking'];
 
            $this->layout = View::make('layouts.print');
            $this->layout->content = View::make('store_return.print_packing_list', $this->data);
    }

	public function updatePrintLoad($loadCode)
	{
		Load::where('load_code', '=', $loadCode)
                ->update(array(
                    "printMTS" => 1
                ));
			// Search Filters
			$filter_load_code = Input::get('filter_load_code', NULL);

			$sort = Input::get('sort', 'doc_no');
			$order = Input::get('order', 'ASC');
			$page = Input::get('page', 1);

			$this->data['filter_load_code'] = $filter_load_code;
			$this->data['sort'] = $sort;
			$this->data['order'] = $order;
			$this->data['page'] = $page;

			$this->data['url_back'] = $this->setURL();

			$this->data['loadCode'] = $loadCode;
			$this->data['records'] = Load::getLoadDetails($loadCode);
			$this->data['permissions'] = unserialize(Session::get('permissions'));
            $load=Load::select('printMTS')->where('load_code','=',$loadCode)->get();
            $this->data['print_status']=$load[0]['printMTS'];

            // get the comments to MTS reports
            $this->data['comments']=Load::getCommentsByLoadCode($loadCode);

            $this->layout = View::make('layouts.print');
            $this->layout->content = View::make('loads.printmts', $this->data);
	}

	public function updatePrintPackingList($loadCode)
	{
		 
			// Search Filters
			$filter_load_code = Input::get('filter_load_code', NULL);

			$sort = Input::get('sort', 'doc_no');
			$order = Input::get('order', 'ASC');
			$page = Input::get('page', 1);

			$this->data['filter_load_code'] = $filter_load_code;
			$this->data['sort'] = $sort;
			$this->data['order'] = $order;
			$this->data['page'] = $page;

			$this->data['url_back'] = $this->setURL();

			$this->data['loadCode'] = $loadCode;
            $this->data['records'] = Load::getPackingDetails($loadCode);
			$this->data['permissions'] = unserialize(Session::get('permissions'));
           

            $this->layout = View::make('layouts.print');
            $this->layout->content = View::make('loads.print_packing_list', $this->data);
	}

    public function printLoadingSheet($loadCode)
    {
        // try {
		// Search Filters
		$filter_load_code = Input::get('filter_load_code', NULL);

		$sort = Input::get('sort', 'doc_no');
		$order = Input::get('order', 'ASC');
		$page = Input::get('page', 1);

		/*$asdf 			= Input::get('load_code', null);
		$this->data['po_info'] 		=Load::getStoreLocation($asdf);*/

		$this->data['filter_load_code'] = $filter_load_code;
		$this->data['sort'] = $sort;
		$this->data['order'] = $order;
		$this->data['page'] = $page;

		$this->data['url_back'] = URL::to('load/shipping' . $this->setURL());

            $this->data['loadCode'] = $loadCode;
            $this->data['records'] = Load::getLoadingDetails($loadCode);
            $this->data['permissions'] = unserialize(Session::get('permissions'));

            $this->layout = View::make('layouts.print');
            $this->layout->content = View::make('loads.print_loading_sheet', $this->data);

        // } catch (Exception $e) {
        //     return Redirect::to('load/list'. $this->setURL())->withErrors(Lang::get('loads.text_fail_load'));
        // }
    }
    public function printLoadingSheetstock($loadCode)
    {
        // try {
		// Search Filters
		$filter_load_code = Input::get('filter_load_code', NULL);

		$sort = Input::get('sort', 'doc_no');
		$order = Input::get('order', 'ASC');
		$page = Input::get('page', 1);

	 

		$this->data['filter_load_code'] = $filter_load_code;
		$this->data['sort'] = $sort;
		$this->data['order'] = $order;
		$this->data['page'] = $page;

		$this->data['url_back'] = URL::to('stocktransfer/stocktranferload' . $this->setURL());

            $this->data['loadCode'] = $loadCode;
            $this->data['records'] = Load::getLoadingDetailsstock($loadCode);
			$this->data['storelocation'] = load::getStoreLocation($loadCode);

            $this->data['permissions'] = unserialize(Session::get('permissions'));

            $this->layout = View::make('layouts.print');
            $this->layout->content = View::make('store_return.print_loading_sheet', $this->data);

        // } catch (Exception $e) {
        //     return Redirect::to('load/list'. $this->setURL())->withErrors(Lang::get('loads.text_fail_load'));
        // }
    }

	protected function setURL($forDetail = false, $forBackToList = false) {
		// Search Filters
		$url = '?filter_type=' . Input::get('filter_type', NULL);
		$url .= '&filter_load_code=' . Input::get('filter_load_code', NULL);
		$url .= '&filter_status=' . Input::get('filter_status', NULL);
		if($forDetail) {
			$url .= '&sort_back=' . Input::get('sort', 'load_code');
			$url .= '&order_back=' . Input::get('order', 'ASC');
			$url .= '&page_back=' . Input::get('page', 1);
		} else {
			if($forBackToList == true) {
				$url .= '&sort=' . Input::get('sort_back', 'load_code');
				$url .= '&order=' . Input::get('order_back', 'ASC');
				$url .= '&page=' . Input::get('page_back', 1);
			} else {
				$url .= '&sort=' . Input::get('sort', 'load_code');
				$url .= '&order=' . Input::get('order', 'ASC');
				$url .= '&page=' . Input::get('page', 1);
			}
		}
		return $url;
	}

	/**
	* post audit trail when load is ship
	*
	* @example  self::shipLoadToBoxAuditTrail();
	*
	* @param  loadNo   		load number
	* @return void
	*/
	public static function shipLoadToBoxAuditTrail($loadNo)
	{
		$dataAfter = "Load code # {$loadNo} just shipped by ".Auth::user()->username;
		$arrParams = array(
			'module'		=> Config::get("audit_trail_modules.shipping"),
			'action'		=> Config::get("audit_trail.ship_load"),
			'reference'		=> "Load code #: " .$loadNo,
			'data_before'	=> '',
			'data_after'	=> $dataAfter,
			'user_id'		=> Auth::user()->id,
			'created_at'	=> date('Y-m-d H:i:s'),
			'updated_at'	=> date('Y-m-d H:i:s')
		);

		AuditTrail::addAuditTrail($arrParams);
	}

	protected function plNumberGeneration() {
		$pl             = Dataset::firstOrNew(array('data_code'=>'PL_NUM_FORMAT'));
		$plNo           = sprintf("%07s", (int)$pl->data_value + 1);
		$pl->data_value = $plNo;
		$pl->updated_at = date('Y-m-d H:i:s');
		$pl->save();
		$pl_code        = Dataset::find($pl->id);

		return $plNo;
		
	}

	public function showdivision() {
// Check Permissions
	if (Session::has('permissions')) {
		if (!in_array('CanAccessPurchaseOrders', unserialize(Session::get('permissions'))))  {
			return Redirect::to('user/profile');
		}
	} else {
		return Redirect::to('users/logout');
	}

	$this->getListdivision();
}

protected function getListdivision() {

		
		// URL

		//$this->data['url_export']                   = URL::to('purchase_order/export' . $this->setURL());
		//$this->data['url_export_backorder']         = URL::to('purchase_order/export_backorder' . $this->setURL());
		//$this->data['url_reopen']                   = URL::to('purchase_order/reopen');
		//$this->data['url_assign']                   = URL::to('purchase_order/assign' . $this->setURL());
		//$this->data['url_detail']                   = URL::to('purchase_order/detail' . $this->setURL(true));


		

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
	//	$this->data['po_status_type']   = Dataset::getTypeInList("PO_STATUS_TYPE");
		//$this->data['stock_piler_list'] = $this->getStockPilers();
	//	$this->data['brands_list'] = $this->getBrands();
		//$this->data['divisions_list'] = $this->getDivisions();

		// Search Filters
		$load_code   = Input::get('load_code', NULL);
		$filer = Input::get('filer', NULL);
		$date_at = Input::get('date_at', NULL);
		
		
		$filter_po_no = Input::get('filter_receiver_no', NULL);
		$filter_receiver_no = Input::get('filter_receiver_no', NULL);
		$filter_entry_date  = Input::get('filter_entry_date', NULL);
		$filter_stock_piler = Input::get('filter_stock_piler', NULL);
		$filter_status      = Input::get('filter_status', NULL);
		$filter_back_order  = Input::get('filter_back_order', NULL);
		$filter_brand       = Input::get('filter_brand', NULL);
		$filter_division    = Input::get('filter_division', NULL);
		$filter_shipment_reference_no = Input::get('filter_shipment_reference_no', NULL);

		$sort               = Input::get('sort', 'po_no');
		$order              = Input::get('order', 'DESC');
		$page               = Input::get('page', 1);

		$receiver_no = Input::get('receiver_no', NULL);
		//$this->data['po_info'] = load::getPOInfodiv($receiver_no);
		//Data
		$arrParams = array(
						'filter_po_no'       				=> $filter_po_no,
						'filter_receiver_no' 				=> $filter_receiver_no,
						'filter_entry_date'  				=> $filter_entry_date,
						'filter_stock_piler' 				=> $filter_stock_piler,
						'filter_back_order' 				=> $filter_back_order,
						'filter_status'      				=> $filter_status,
						'filter_brand'       				=> $filter_brand,
						'filter_division'	 				=> $filter_division,
						'filter_shipment_reference_no' 		=> $filter_shipment_reference_no,
						'receiver_no'		 				=> $receiver_no,
						'sort'               				=> $sort,
						'order'              				=> $order,
						'page'              				=> $page,
						'limit'              				=> 30
					);

		$results 		= PurchaseOrder::getPoListsdivision($arrParams);
		$results_total	= PurchaseOrder::getPoListsdiv($arrParams, TRUE);
		// echo "<pre>"; print_r($results); die();
		// $results_total 	= PurchaseOrder::getPOQuery($arrParams, TRUE); //count($results);//
		// print_r($results_total); die();
		DebugHelper::log(__METHOD__, $results_total);

		// Pagination
		$this->data['arrFilters'] = array(
									'filter_po_no'       => $filter_po_no,
									'filter_receiver_no' => $filter_receiver_no,
									'filter_shipment_reference_no'	=> $filter_shipment_reference_no,
									'filter_entry_date'  => $filter_entry_date,
									'filter_stock_piler' => $filter_stock_piler,
									'filter_back_order'  => $filter_back_order,
									'filter_status'      => $filter_status,
									'filter_brand'       => $filter_brand,
									'filter_division'	 => $filter_division,
									'sort'               => $sort,
									'order'              => $order
								);

		$this->data['purchase_orders']       = Paginator::make($results, $results_total, 30);
		$this->data['load_code']       =$load_code;
		$this->data['filer']       =$filer;
		$this->data['date_at']       =$date_at;
		
	/**	$this->data['purchase_orders_count'] = $results_total;
		$this->data['counter']               = $this->data['purchase_orders']->getFrom();
		$this->data['filter_po_no']          = $filter_po_no;
		$this->data['filter_receiver_no']    = $filter_receiver_no;
		$this->data['filter_shipment_reference_no']    = $filter_shipment_reference_no;
		$this->data['filter_entry_date']     = $filter_entry_date;
		$this->data['filter_stock_piler']    = $filter_stock_piler;
		$this->data['filter_status']         = $filter_status;
		$this->data['filter_back_order']     = $filter_back_order;
		$this->data['filter_brand']          = $filter_brand;
		$this->data['filter_division']       = $filter_division;
		$this->data['sort']                  = $sort;
		$this->data['order']                 = $order;
		$this->data['page']                  = $page; **/

		$url                                 = '?filter_po_no=' . $filter_po_no;
		$url                                 .= '&filter_entry_date=' . $filter_entry_date;
		$url                                 .= '&filter_status=' . $filter_status;
		$url                                 .= '&page=' . $page;

		$order_po_no                         = ($sort=='po_no' && $order=='ASC') ? 'DESC' : 'ASC';
		$order_receiver_no                   = ($sort=='receiver_no' && $order=='ASC') ? 'DESC' : 'ASC';
		$order_entry_date                    = ($sort=='entry_date' && $order=='ASC') ? 'DESC' : 'ASC';

		$this->data['url_back']              = URL::to('purchase_order' . $this->setURL(false, true));
		$this->data['sort_po_no']            = URL::to('purchase_order/division' . $url .'&receiver_no='.$receiver_no. '&sort=po_no&order=' . $order_po_no, NULL, FALSE);
		$this->data['sort_receiver_no']      = URL::to('purchase_order' . $url . '&sort=receiver_no&order=' . $order_receiver_no, NULL, FALSE);
		$this->data['sort_entry_date']       = URL::to('purchase_order' . $url . '&sort=entry_date&order=' . $order_entry_date, NULL, FALSE);

		// Permissions
		$this->data['permissions']           = unserialize(Session::get('permissions'));

		$this->layout->content = View::make('loads.load_details', $this->data);
	}
}

