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

	public function exportCSV() {
		// Check Permissions
		if (Session::has('permissions')) {
	    	if (!in_array('CanExportShipping', unserialize(Session::get('permissions'))))  {
				return Redirect::to('load/list');
			}
    	} else {
			return Redirect::to('users/logout');
		}
		$this->data = Lang::get('loads');
		$arrParams = array(
							'filter_load_code'	=> Input::get('filter_load_code', NULL),
							'filter_status' 	=> Input::get('filter_status', NULL),
							'sort'				=> Input::get('sort', 'load_code'),
							'order'				=> Input::get('order', 'ASC'),
							'page'				=> NULL,
							'limit'				=> NULL
						);

		$results = Load::getLoadList($arrParams);
		$this->data['results'] = $results;

		$pdf = App::make('dompdf');
		$pdf->loadView('loads.report_list', $this->data)->setPaper('a4')->setOrientation('landscape');
		// return $pdf->stream();
		return $pdf->download('loads_' . date('Ymd') . '.pdf');
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
		$results_total = count($results);

		// Pagination
		$this->data['arrFilters'] = array(
										'filter_load_code'	=> $filter_load_code,
										'filter_status' 	=> $filter_status,
										'sort'				=> $sort,
										'order'				=> $order
									);

		$this->data['loads'] = Paginator::make($results, $results_total, 30);
		$this->data['load_count'] = $results_total;

		$this->data['counter'] 	= $this->data['loads']->getFrom();

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
		$this->data['permissions'] = unserialize(Session::get('permissions'));

		$this->layout->content = View::make('loads.list', $this->data);
	}

	public function shipLoad()
	{
		try {
			$data = Input::all();
			if(!isset($data['load_code'])) throw new Exception("Load code empty.");
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
					$picklistUpToShipping	= "classes/picklist.php {$data['load_code']}";
					CommonHelper::execInBackground($picklistUpToShipping);
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

	public function printBoxLabel($loadCode)
	{
		try {
			$this->data['loadCode'] = $loadCode;
			$this->data['records'] = Load::getPackingDetails($loadCode);
			$this->data['permissions'] = unserialize(Session::get('permissions'));

			$this->layout = View::make('layouts.print');
			$this->layout->content = View::make('loads.print_box_label', $this->data);

		} catch (Exception $e) {
			return Redirect::to('load/list'. $this->setURL())->withErrors(Lang::get('loads.text_fail_load'));
		}
	}

	public function printLoad($loadCode)
	{
		try {
			$this->data['loadCode'] = $loadCode;
			$this->data['records'] = Load::getLoadDetails($loadCode);
			$this->data['permissions'] = unserialize(Session::get('permissions'));

			$this->layout = View::make('layouts.print');
			$this->layout->content = View::make('loads.printmts', $this->data);

		} catch (Exception $e) {
			return Redirect::to('load/list'. $this->setURL())->withErrors(Lang::get('loads.text_fail_load'));
		}
	}

    public function printPackingList($loadCode)
    {
        try {
            $this->data['loadCode'] = $loadCode;
            $this->data['records'] = Load::getPackingDetails($loadCode);
            $this->data['permissions'] = unserialize(Session::get('permissions'));

            $this->layout = View::make('layouts.print');
            $this->layout->content = View::make('loads.print_packing_list', $this->data);

        } catch (Exception $e) {
            return Redirect::to('load/list'. $this->setURL())->withErrors(Lang::get('loads.text_fail_load'));
        }
    }

    public function printLoadingSheet($loadCode)
    {
        try {
            $this->data['loadCode'] = $loadCode;
            $this->data['records'] = Load::getPackingDetails($loadCode);
            $this->data['permissions'] = unserialize(Session::get('permissions'));

            $this->layout = View::make('layouts.print');
            $this->layout->content = View::make('loads.print_loading_sheet', $this->data);

        } catch (Exception $e) {
            return Redirect::to('load/list'. $this->setURL())->withErrors(Lang::get('loads.text_fail_load'));
        }
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
}
