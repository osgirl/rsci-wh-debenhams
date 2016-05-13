<?php

class shippingController extends \BaseController {
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
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		//
		$this->getlist();
	
	}

	public function getlist()
	{

		$this->data['stock_piler_list'] = $this->getStockPilers();

		
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




		$this->layout->content=view::make('loads.shipping',$this->data);
	}

    public function assignPilerForm() {

	    if (Session::has('permissions')) {
	        if (!in_array('CanAssignPacking', unserialize(Session::get('permissions'))))  {
	            return Redirect::to('purchase_order');
	        }
	    } else {
	        return Redirect::to('users/logout');
	    }

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

	    $this->data['url_back']         = URL::to('shipping/list'). $this->setURL();
	    $this->data['filter_assigned_to_user_id'] = $filter_assigned_to_user_id;
	    $this->data['sort'] = $sort;
	    $this->data['order'] = $order;
	    $this->data['page'] = $page;
	/*
	    $this->data['filter_type'] = $filter_type;
	    $this->data['filter_doc_no'] = $filter_doc_no;
	    $this->data['filter_status'] = $filter_status;
	    $this->data['filter_store'] = $filter_store;

	    $this->data['url_back']         = URL::to('picking/list'). $this->setURL();
	   
	*/
	    $this->layout->content  = View::make('loads.shipping_assign_piler', $this->data);
	}

	protected function setURL($forDetail = false, $forBackToList = false) {
		// Search Filters
		$url = '?filter_load_code=' . Input::get('filter_load_code', NULL);
		$url .= '&filter_assigned_to_user_id=' . Input::get('filter_assigned_to_user_id', NULL);
		$url .= '&filter_entry_date=' . Input::get('filter_entry_date', NULL);
		if($forDetail) {
			$url .= '&sort_back=' . Input::get('sort', 'load_code');
			$url .= '&order_back=' . Input::get('order', 'DESC');
			$url .= '&page_back=' . Input::get('page', 1);
		} else {
			if($forBackToList == true) {
				$url .= '&sort=' . Input::get('sort_back', 'load_code');
				$url .= '&order=' . Input::get('order_back', 'DESC');
				$url .= '&page=' . Input::get('page_back', 1);
			} else {
				$url .= '&sort=' . Input::get('sort', 'load_code');
				$url .= '&order=' . Input::get('order', 'DESC');
				$url .= '&page=' . Input::get('page', 1);
			}

		}
		return $url;
	}


	public function assignToPiler() {
        // Check Permissions
        
        $pilers = implode(',' , Input::get('stock_piler'));
        //get moved_to_reserve id
       //arrParams = array('data_code' => 'BOX_STATUS_TYPE', 'data_value'=> 'assigned');
        //oxStatus = Dataset::getType($arrParams)->toArray();

        $arrBoxCode = explode(',', Input::get("load_code"));

		
        foreach ($arrBoxCode as $codes) {
            $arrParams = array(
                'assigned_by' 			=> Auth::user()->id,
				'assigned_to_user_id' 	=> $pilers, //Input::get('stock_piler'),
				'updated_at' 			=> date('Y-m-d H:i:s')
            );
            
            load::assignToStockPiler($codes, $arrParams);

            // AuditTrail
            /**
            $users = User::getUsersFullname(Input::get('stock_piler'));

            $fullname = implode(', ', array_map(function ($entry) { return $entry['name']; }, $users));

            $data_before = '';
            $data_after = 'Box Code: ' . $box_codes . ' assigned to ' . $fullname;

            $arrParams = array(
                'module'		=> Config::get('audit_trail_modules.boxing'),
                'action'		=> Config::get('audit_trail.update_box'),
                'reference'		=> $box_codes,
                'data_before'	=> $data_before,
                'data_after'	=> $data_after,
                'user_id'		=> Auth::user()->id,
                'created_at'	=> date('Y-m-d H:i:s'),
                'updated_at'	=> date('Y-m-d H:i:s')
               
            );
            //AuditTrail::addAuditTrail($arrParams);
            // AuditTrail
             **/
        }

		

        return Redirect::to('shipping/list' . $this->setURL())->with('message','Successfully assigned the Load!');
    }

 	private function getStockPilers()
    {
        $stock_pilers = array();
        foreach (User::getStockPilerOptions() as $item) {
            $stock_pilers[$item->id] = $item->firstname . ' ' . $item->lastname;
        }
        return array('' => 'Please Select') + $stock_pilers;
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
