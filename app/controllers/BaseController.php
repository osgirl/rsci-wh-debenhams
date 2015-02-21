<?php

class BaseController extends Controller {
	private $data = array();

	/**
	 * Setup the layout used by the controller.
	 *
	 * @return void
	 */
	protected function setupLayout()
	{
		if ( ! is_null($this->layout))
		{
			$this->data['menu_wh_receiving'] = Lang::get('general.menu_wh_receiving');
			$this->data['menu_purchase_orders'] = Lang::get('general.menu_purchase_orders');
			$this->data['menu_inventory'] = Lang::get('general.menu_inventory');

			$this->data['menu_transfers'] = Lang::get('general.menu_transfers');
			$this->data['menu_letdown'] = Lang::get('general.menu_letdown');
			$this->data['menu_picking'] = Lang::get('general.menu_picking');
			$this->data['menu_shipping'] = Lang::get('general.menu_shipping');
			$this->data['menu_carton'] = Lang::get('general.menu_carton');
			$this->data['menu_load'] = Lang::get('general.menu_load');

			$this->data['menu_str_receiving'] = Lang::get('general.menu_str_receiving');
			$this->data['menu_store_order'] = Lang::get('general.menu_store_order');
			$this->data['menu_store_return'] = Lang::get('general.menu_store_return');


			$this->data['menu_reports'] = Lang::get('general.menu_reports');
			$this->data['menu_product_master_list'] = Lang::get('general.menu_product_master_list');
			$this->data['menu_slot_master_list'] = Lang::get('general.menu_slot_master_list');
			$this->data['menu_vendor_master_list'] = Lang::get('general.menu_vendor_master_list');
			$this->data['menu_store_master_list'] = Lang::get('general.menu_store_master_list');
			$this->data['menu_audit_trail'] = Lang::get('general.menu_audit_trail');
			$this->data['menu_unlisted_list'] = Lang::get('general.menu_unlisted_list');
			$this->data['menu_expiry_items'] = Lang::get('general.menu_expiry_items');

			$this->data['menu_system'] = Lang::get('general.menu_system');
			$this->data['menu_settings'] = Lang::get('general.menu_settings');
			$this->data['menu_users'] = Lang::get('general.menu_users');
			$this->data['menu_user_roles'] = Lang::get('general.menu_user_roles');

			$this->data['menu_profile'] = Lang::get('general.menu_profile');
			$this->data['menu_change_password'] = Lang::get('general.menu_change_password');
			$this->data['menu_logout'] = Lang::get('general.menu_logout');

			$this->data['title_brand'] = Lang::get('general.title_brand');


			// Permissions
			$this->data['permissions'] = unserialize(Session::get('permissions'));

			$this->layout = View::make($this->layout, $this->data);
		}
	}

}