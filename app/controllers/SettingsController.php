<?php

class SettingsController extends BaseController {
	private $data = array();

	protected $layout = "layouts.main";
	
	public function __construct()
    {
    	$this->beforeFilter('csrf', array('on' => 'post'));
    	$this->beforeFilter('auth', array('only' => array('Dashboard')));
    			
		// Check Permissions
		if (Session::has('permissions')) {
	    	if (!in_array('CanAccessSettings', unserialize(Session::get('permissions'))))  {
				return Redirect::to('/');
			}
    	} else {
			return Redirect::to('users/logout');
		}
    }

	public function showIndex() {
		// Check Permissions
		if (Session::has('permissions')) {
	    	if (!in_array('CanAccessSettings', unserialize(Session::get('permissions'))))  {
				return Redirect::to('/');
			}
    	} else {
			return Redirect::to('users/logout');
		}
		
		$this->getList();
	}
	
	public function insertDataForm() {
		// Check Permissions
		if (Session::has('permissions')) {
	    	if (!in_array('CanInsertSettings', unserialize(Session::get('permissions'))))  {
				return Redirect::to('settings' . $this->setURL());
			}
    	} else {
			return Redirect::to('users/logout');
		}
		
		$this->data['heading_title_insert'] = Lang::get('settings.heading_title_insert');
		
		$this->data['entry_brand'] = Lang::get('settings.entry_brand');
		$this->data['entry_product_identifier'] = Lang::get('settings.entry_product_identifier');
		$this->data['entry_product_action'] = Lang::get('settings.entry_product_action');
		
		$this->data['button_submit'] = Lang::get('general.button_submit');
		$this->data['button_cancel'] = Lang::get('general.button_cancel');
		
		// Options
		$this->data['brand_options'] = $this->brandOptions();
		$this->data['product_identifier_options'] = $this->productIdentifierOptions();
		$this->data['product_action_options'] = $this->productActionOptions();
			
		// URL
		$this->data['url_cancel'] = URL::to('settings' . $this->setURL());
				
		$this->data['sort'] = Input::get('sort', 'brand');
		$this->data['order'] = Input::get('order', 'ASC');
		
		$this->layout->content = View::make('settings.insert', $this->data);
	}
	
	public function insertData() {
		// Check Permissions
		if (Session::has('permissions')) {
	    	if (!in_array('CanInsertSettings', unserialize(Session::get('permissions'))))  {
				return Redirect::to('settings' . $this->setURL());
			}
    	} else {
			return Redirect::to('users/logout');
		}
		
		$rules = array(
						'brand'					=> 'required|unique:settings,brand,NULL,id,deleted_at,0000-00-00 00:00:00',
						'product_identifier'	=> 'required',
						'product_action'		=> 'required'
					);
		$validator = Validator::make(Input::all(), $rules);

		if ($validator->fails()) {
			return Redirect::to('settings/insert' . $this->setURL())
				->withErrors($validator)
				->withInput();
		} else {
			$brands = $this->brandOptions();
			$arrParams = array(
							'brand'					=> Input::get('brand'),
							'brand_name'			=> $brands[Input::get('brand')],
							'product_identifier'	=> Input::get('product_identifier'),
							'product_action'		=> Input::get('product_action'),
							'created_at'			=> date('Y-m-d H:i:s'),
							'updated_at'			=> date('Y-m-d H:i:s')
							);
			Settings::addSetting($arrParams);
			
			// AuditTrail
			$brand_options = $this->brandOptions();
			$product_identifier_options = $this->productIdentifierOptions();
			$product_action_options = $this->productActionOptionsDisplay();
			
			$data_before = '';
			$data_after = 'Brand: ' . $brand_options[Input::get('brand')] . '<br />' . 
						  'Product Identifier: ' . $product_identifier_options[Input::get('product_identifier')] . '<br />' . 
						  'Action: ' . $product_action_options[Input::get('product_action')];
			
			$arrParams = array(
							'module'		=> 'Settings',
							'action'		=> 'Added New Setting',
							'reference'		=> Input::get('brand'),
							'data_before'	=> $data_before,
							'data_after'	=> $data_after,
							'user_id'		=> Auth::user()->id,
							'created_at'	=> date('Y-m-d H:i:s'),
							'updated_at'	=> date('Y-m-d H:i:s')
							);
			AuditTrail::addAuditTrail($arrParams);
			// AuditTrail

			return Redirect::to('settings'. $this->setURL())->with('success', Lang::get('settings.text_success_insert'));
		}
	}
	
	public function updateDataForm() {
		// Check Permissions
		if (Session::has('permissions')) {
	    	if (!in_array('CanUpdateSettings', unserialize(Session::get('permissions'))) || Settings::find(Input::get('id'))==NULL) {
				return Redirect::to('settings' . $this->setURL());
			}
    	} else {
			return Redirect::to('users/logout');
		}
		
		$this->data['heading_title_update'] = Lang::get('settings.heading_title_update');
		
		$this->data['entry_brand'] = Lang::get('settings.entry_brand');
		$this->data['entry_product_identifier'] = Lang::get('settings.entry_product_identifier');
		$this->data['entry_product_action'] = Lang::get('settings.entry_product_action');
				
		$this->data['button_submit'] = Lang::get('general.button_submit');
		$this->data['button_cancel'] = Lang::get('general.button_cancel');
		
		// Options
		$this->data['brand_options'] = $this->brandOptions();
		$this->data['product_identifier_options'] = $this->productIdentifierOptions();
		$this->data['product_action_options'] = $this->productActionOptions();
		
		// URL
		$this->data['url_cancel'] = URL::to('settings' . $this->setURL());
				
		$this->data['sort'] = Input::get('sort', 'brand');
		$this->data['order'] = Input::get('order', 'ASC');
		$this->data['page'] = Input::get('page', 1);
		
		// Data
		$this->data['settings'] = Settings::find(Input::get('id'));
		
		$this->layout->content = View::make('settings.update', $this->data);
	}
	
	public function updateData() {
		// Check Permissions
		if (Session::has('permissions')) {
	    	if (!in_array('CanUpdateSettings', unserialize(Session::get('permissions'))) || Settings::find(Input::get('id'))==NULL) {
				return Redirect::to('settings' . $this->setURL());
			}
    	} else {
			return Redirect::to('users/logout');
		}
		
		$rules = array(
						'brand'					=> 'required|unique:settings,brand,' . Input::get('id') . ',id,deleted_at,0000-00-00 00:00:00',
						'product_identifier'	=> 'required',
						'product_action'		=> 'required'
					);
		$validator = Validator::make(Input::all(), $rules);

		if ($validator->fails()) {
			return Redirect::to('settings/update' . $this->setURL() .'&id=' . Input::get('id'))
				->withErrors($validator)
				->withInput();
		} else {
			// AuditTrail
			$brand_options = $this->brandOptions();
			$product_identifier_options = $this->productIdentifierOptions();
			$product_action_options = $this->productActionOptionsDisplay();
			
			$settings = Settings::find(Input::get('id'));
			
			$data_before = 'Brand: ' . $brand_options[$settings->brand] . '<br />' . 
						   'Product Identifier: ' . $product_identifier_options[$settings->product_identifier] . '<br />' . 
						   'Action: ' . $product_action_options[$settings->product_action];
			// AuditTrail
			
			// Update Settings
			$brands = $this->brandOptions();
			$arrParams = array(
							'brand'					=> Input::get('brand'),
							'brand_name'			=> $brands[Input::get('brand')],
							'product_identifier'	=> Input::get('product_identifier'),
							'product_action'		=> Input::get('product_action'),
							'updated_at'	=> date('Y-m-d H:i:s')
							);
			Settings::updateSetting(Input::get('id'), $arrParams);
			
			// AuditTrail
			$data_after = 'Brand: ' . $brand_options[Input::get('brand')] . '<br />' . 
						  'Product Identifier: ' . $product_identifier_options[Input::get('product_identifier')] . '<br />' . 
						  'Action: ' . $product_action_options[Input::get('product_action')];
			
			$arrParams = array(
							'module'		=> 'Settings',
							'action'		=> 'Modifed Settings',
							'reference'		=> Input::get('brand'),
							'data_before'	=> $data_before,
							'data_after'	=> $data_after,
							'user_id'		=> Auth::user()->id,
							'created_at'	=> date('Y-m-d H:i:s'),
							'updated_at'	=> date('Y-m-d H:i:s')
							);
			AuditTrail::addAuditTrail($arrParams);
			// AuditTrail

			return Redirect::to('settings' . $this->setURL())->with('success', Lang::get('settings.text_success_update'));
		}
	}
	
	public function deleteData() {
		// Check Permissions
		if (Session::has('permissions')) {
	    	if (!in_array('CanDeleteSettings', unserialize(Session::get('permissions'))))  {
				return Redirect::to('settings' . $this->setURL());
			}
    	} else {
			return Redirect::to('users/logout');
		}
		
		$rules = array(
						'selected'	=> 'required'
					);
		$validator = Validator::make(Input::all(), $rules);

		if ($validator->fails()) {
			return Redirect::to('settings');
		} else {
			Settings::deleteSetting(Input::get('selected'));
			
			// AuditTrail
			$arrDeleted = array();
			foreach (Input::get('selected') as $id) {
				$info = Settings::find($id);
				$arrDeleted[] = $info->brand;
			}
			
			$data_before = '';
			$data_after = 'Deleted: ' . implode(', ', $arrDeleted);
			
			$arrParams = array(
							'module'		=> 'Settings',
							'action'		=> 'Deleted Settings',
							'reference'		=> implode(', ', $arrDeleted),
							'data_before'	=> $data_before,
							'data_after'	=> $data_after,
							'user_id'		=> Auth::user()->id,
							'created_at'	=> date('Y-m-d H:i:s'),
							'updated_at'	=> date('Y-m-d H:i:s')
							);
			AuditTrail::addAuditTrail($arrParams);
			// AuditTrail
			
			return Redirect::to('settings' . $this->setURL())->with('success', Lang::get('settings.text_success_delete'));
		}
	}
	
	protected function getList() {
		$this->data['heading_title'] = Lang::get('settings.heading_title');
		
		$this->data['text_empty_results'] = Lang::get('general.text_empty_results');
		$this->data['text_total'] = Lang::get('general.text_total');
		$this->data['text_confirm'] = Lang::get('general.text_confirm');
	
		$this->data['col_id'] = Lang::get('settings.col_id');
		$this->data['col_brand'] = Lang::get('settings.col_brand');
		$this->data['col_product_identifier'] = Lang::get('settings.col_product_identifier');
		$this->data['col_product_action'] = Lang::get('settings.col_product_action');
		$this->data['col_action'] = Lang::get('settings.col_action');
		
		$this->data['button_insert'] = Lang::get('settings.button_insert');
		$this->data['button_delete'] = Lang::get('settings.button_delete');
		
		$this->data['link_edit'] = Lang::get('general.link_edit');
		
		$this->data['error_delete'] = Lang::get('general.error_delete');
		
		// Options
		$this->data['product_identifier_options'] = $this->productIdentifierOptions();
		$this->data['product_action_options'] = $this->productActionOptionsDisplay();
		
		// URL
		$url = $this->setURL();
		$this->data['url_insert'] = URL::to('settings/insert' . $url);
		$this->data['url_update'] = URL::to('settings/update' . $url);
		
		// Messages
		$this->data['error'] = '';
		
		$this->data['success'] = '';
		if (Session::has('success')) {
			$this->data['success'] = Session::get('success');
		}
						
		// Data		
		$page = Input::get('page', 1);
		$sort = Input::get('sort', 'brand');
		$order = Input::get('order', 'ASC');
		
		$arrParams = array(
							'sort'				=> $sort,
							'order'				=> $order,
							'page'				=> $page,
							'limit'				=> 30
						);		
		$results = Settings::getSettings($arrParams);
		$results_total = Settings::getCountSettings($arrParams);
		
		// Pagination
		$this->data['arrFilters'] = array(
										'sort'				=> $sort,
										'order'				=> $order
									);
		
		$this->data['settings'] = Paginator::make($results, $results_total, 30);
		$this->data['settings_count'] = $results_total;
		
		$this->data['counter'] 	= $this->data['settings']->getFrom();
				
		$this->data['sort'] = $sort;
		$this->data['order'] = $order;
		$this->data['page'] = $page;
		
		$url = '?page=' . $page;
		
		$order_id = ($sort=='id' && $order=='ASC') ? 'DESC' : 'ASC';
		$order_brand = ($sort=='brand' && $order=='ASC') ? 'DESC' : 'ASC';
		
		$this->data['sort_id'] = URL::to('settings' . $url . '&sort=id&order=' . $order_id, NULL, FALSE);
		$this->data['sort_brand'] = URL::to('settings' . $url . '&sort=brand&order=' . $order_brand, NULL, FALSE);
		
		// Permissions
		$this->data['permissions'] = unserialize(Session::get('permissions'));
		
		$this->layout->content = View::make('settings.list', $this->data);
	}
	
	protected function setURL() {
		// Search Filters
		$url = '?sort=' . Input::get('sort', 'brand');
		$url .= '&order=' . Input::get('order', 'ASC');
		$url .= '&page=' . Input::get('page', 1);
		
		return $url;
	}
	
	protected function brandOptions() {
		return array(
					'' 				=> Lang::get('general.text_select'),
					'family-mart'	=> 'Family Mart',
					'gap'			=> 'Gap'
				);
	}
	
	protected function productIdentifierOptions() {
		return array(
					'upc'	=> Lang::get('settings.option_upc'),
					'sku'	=> Lang::get('settings.option_sku')
				);		
	}
	
	protected function productActionOptions() {
		return array(
					'upc-detail-page'		=> Lang::get('settings.option_details'),
					'increment-quantity'	=> Lang::get('settings.option_increment')
				);
	}
	
	protected function productActionOptionsDisplay() {
		return array(
					'upc-detail-page'		=> Lang::get('settings.option_details_list'),
					'increment-quantity'	=> Lang::get('settings.option_increment_list')
				);
	}
}