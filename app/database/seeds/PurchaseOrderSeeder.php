<?php

class PurchaseOrderSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run() {
	
		DB::table('purchase_order_lists')->truncate();

		  PurchaseOrder::create(array(
		  	"assigned_by"	=> 2,
		  	"assigned_to_user_id"	=> 3,
		  	"vendor_id"=>2,
		    "receiver_no"=>20204,
		    "purchase_order_no"=>10459,
		    "destination"=>'W',
		    "po_status" => 2,
		    "delivery_date" => date("Y-m-d")
		  ));

		  PurchaseOrder::create(array(
		  	"assigned_by"	=> 0,
		  	"assigned_to_user_id"	=> 0,
		  	"vendor_id"=>2,
		    "receiver_no"=>20205,
		    "purchase_order_no"=>10460,
		    "destination"=>'W',
		    "po_status" => 1,
		    "delivery_date" => date("Y-m-d")
		  ));

		  PurchaseOrder::create(array(
		  	"assigned_by"	=> 0,
		  	"assigned_to_user_id"	=> 0,
		  	"vendor_id"=>2,
		    "receiver_no"=>20206,
		    "purchase_order_no"=>10461,
		    "destination"=>'W',
		    "po_status" => 1,
		    "delivery_date" => date("Y-m-d")
		  ));

		  PurchaseOrder::create(array(
		  	"assigned_by"	=> 2,
		  	"assigned_to_user_id"	=> 3,
		  	"vendor_id"=>2,
		    "receiver_no"=>20209,
		    "purchase_order_no"=>10462,
		    "destination"=>'W',
		    "po_status" => 2,
		    "delivery_date" => date("Y-m-d")
		  ));

		  PurchaseOrder::create(array(
		  	"assigned_by"	=> 0,
		  	"assigned_to_user_id"	=> 0,
		  	"vendor_id"=>2,
		    "receiver_no"=>20208,
		    "purchase_order_no"=>10463,
		    "destination"=>'W',
		    "po_status" => 1,
		    "delivery_date" => date("Y-m-d")
		  ));

		  PurchaseOrder::create(array(
		  	"assigned_by"	=> 0,
		  	"assigned_to_user_id"	=> 0,
		  	"vendor_id"=>2,
		    "receiver_no"=>20207,
		    "purchase_order_no"=>10464,
		    "destination"=>'W',
		    "po_status" => 1,
		    "delivery_date" => date("Y-m-d")
		  ));

		  PurchaseOrder::create(array(
		  	"assigned_by"	=> 2,
		  	"assigned_to_user_id"	=> 3,
		  	"vendor_id"=>2,
		    "receiver_no"=>20208,
		    "purchase_order_no"=>10465,
		    "destination"=>'W',
		    "po_status" => 2,
		    "delivery_date" => date("Y-m-d")
		  ));

		  PurchaseOrder::create(array(
		  	"assigned_by"	=> 0,
		  	"assigned_to_user_id"	=> 0,
		  	"vendor_id"=>2,
		    "receiver_no"=>20209,
		    "purchase_order_no"=>10466,
		    "destination"=>'W',
		    "po_status" => 1,
		    "delivery_date" => date("Y-m-d")
		  ));

		  PurchaseOrder::create(array(
		  	"assigned_by"	=> 0,
		  	"assigned_to_user_id"	=> 0,
		  	"vendor_id"=>2,
		    "receiver_no"=>20210,
		    "purchase_order_no"=>10467,
		    "destination"=>'W',
		    "po_status" => 1,
		    "delivery_date" => date("Y-m-d")
		  ));

		  PurchaseOrder::create(array(
		  	"assigned_by"	=> 2,
		  	"assigned_to_user_id"	=> 3,
		  	"vendor_id"=>2,
		    "receiver_no"=>20211,
		    "purchase_order_no"=>10468,
		    "destination"=>'W',
		    "po_status" => 2,
		    "delivery_date" => date("Y-m-d")
		  ));

		  PurchaseOrder::create(array(
		  	"assigned_by"	=> 0,
		  	"assigned_to_user_id"	=> 0,
		  	"vendor_id"=>2,
		    "receiver_no"=>20212,
		    "purchase_order_no"=>10469,
		    "destination"=>'W',
		    "po_status" => 1,
		    "delivery_date" => date("Y-m-d")
		  ));

		  PurchaseOrder::create(array(
		  	"assigned_by"	=> 0,
		  	"assigned_to_user_id"	=> 0,
		  	"vendor_id"=>2,
		    "receiver_no"=>20213,
		    "purchase_order_no"=>10470,
		    "destination"=>'W',
		    "po_status" => 1,
		    "delivery_date" => date("Y-m-d")
		  ));


		  PurchaseOrder::create(array(
		  	"assigned_by"	=> 2,
		  	"assigned_to_user_id"	=> 3,
		  	"vendor_id"=>2,
		    "receiver_no"=>20214,
		    "purchase_order_no"=>10471,
		    "destination"=>'W',
		    "po_status" => 2,
		    "delivery_date" => date("Y-m-d")
		  ));

		  PurchaseOrder::create(array(
		  	"assigned_by"	=> 0,
		  	"assigned_to_user_id"	=> 0,
		  	"vendor_id"=>2,
		    "receiver_no"=>20215,
		    "purchase_order_no"=>10472,
		    "destination"=>'W',
		    "po_status" => 1,
		    "delivery_date" => date("Y-m-d")
		  ));

		  PurchaseOrder::create(array(
		  	"assigned_by"	=> 0,
		  	"assigned_to_user_id"	=> 0,
		  	"vendor_id"=>2,
		    "receiver_no"=>20216,
		    "purchase_order_no"=>10473,
		    "destination"=>'W',
		    "po_status" => 1,
		    "delivery_date" => date("Y-m-d")
		  ));

		  PurchaseOrder::create(array(
		  	"assigned_by"	=> 2,
		  	"assigned_to_user_id"	=> 3,
		  	"vendor_id"=>2,
		    "receiver_no"=>20217,
		    "purchase_order_no"=>10474,
		    "destination"=>'W',
		    "po_status" => 2,
		    "delivery_date" => date("Y-m-d")
		  ));

		  PurchaseOrder::create(array(
		  	"assigned_by"	=> 0,
		  	"assigned_to_user_id"	=> 0,
		  	"vendor_id"=>2,
		    "receiver_no"=>20218,
		    "purchase_order_no"=>10475,
		    "destination"=>'W',
		    "po_status" => 1,
		    "delivery_date" => date("Y-m-d")
		  ));

		  PurchaseOrder::create(array(
		  	"assigned_by"	=> 0,
		  	"assigned_to_user_id"	=> 0,
		  	"vendor_id"=>2,
		    "receiver_no"=>20219,
		    "purchase_order_no"=>10476,
		    "destination"=>'W',
		    "po_status" => 1,
		    "delivery_date" => date("Y-m-d")
		  ));

		  PurchaseOrder::create(array(
		  	"assigned_by"	=> 2,
		  	"assigned_to_user_id"	=> 3,
		  	"vendor_id"=>2,
		    "receiver_no"=>20220,
		    "purchase_order_no"=>10477,
		    "destination"=>'W',
		    "po_status" => 2,
		    "delivery_date" => date("Y-m-d")
		  ));

		  PurchaseOrder::create(array(
		  	"assigned_by"	=> 0,
		  	"assigned_to_user_id"	=> 0,
		  	"vendor_id"=>2,
		    "receiver_no"=>20221,
		    "purchase_order_no"=>10478,
		    "destination"=>'W',
		    "po_status" => 1,
		    "delivery_date" => date("Y-m-d")
		  ));

		  PurchaseOrder::create(array(
		  	"assigned_by"	=> 0,
		  	"assigned_to_user_id"	=> 0,
		  	"vendor_id"=>2,
		    "receiver_no"=>20222,
		    "purchase_order_no"=>10479,
		    "destination"=>'W',
		    "po_status" => 1,
		    "delivery_date" => date("Y-m-d")
		  ));

		  PurchaseOrder::create(array(
		  	"assigned_by"	=> 2,
		  	"assigned_to_user_id"	=> 3,
		  	"vendor_id"=>2,
		    "receiver_no"=>20223,
		    "purchase_order_no"=>10480,
		    "destination"=>'W',
		    "po_status" => 2,
		    "delivery_date" => date("Y-m-d")
		  ));

		  PurchaseOrder::create(array(
		  	"assigned_by"	=> 0,
		  	"assigned_to_user_id"	=> 0,
		  	"vendor_id"=>2,
		    "receiver_no"=>20224,
		    "purchase_order_no"=>10481,
		    "destination"=>'W',
		    "po_status" => 1,
		    "delivery_date" => date("Y-m-d")
		  ));

		  PurchaseOrder::create(array(
		  	"assigned_by"	=> 0,
		  	"assigned_to_user_id"	=> 0,
		  	"vendor_id"=>2,
		    "receiver_no"=>20225,
		    "purchase_order_no"=>10482,
		    "destination"=>'W',
		    "po_status" => 1,
		    "delivery_date" => date("Y-m-d")
		  ));

		  
		  PurchaseOrder::create(array(
		  	"assigned_by"	=> 0,
		  	"assigned_to_user_id"	=> 0,
		  	"vendor_id"=>2,
		    "receiver_no"=>20226,
		    "purchase_order_no"=>10483,
		    "destination"=>'W',
		    "po_status" => 1,
		    "delivery_date" => date("Y-m-d")
		  ));

		  PurchaseOrder::create(array(
		  	"assigned_by"	=> 2,
		  	"assigned_to_user_id"	=> 3,
		  	"vendor_id"=>2,
		    "receiver_no"=>20227,
		    "purchase_order_no"=>10484,
		    "destination"=>'W',
		    "po_status" => 2,
		    "delivery_date" => date("Y-m-d")
		  ));

		  PurchaseOrder::create(array(
		  	"assigned_by"	=> 0,
		  	"assigned_to_user_id"	=> 0,
		  	"vendor_id"=>2,
		    "receiver_no"=>20228,
		    "purchase_order_no"=>10485,
		    "destination"=>'W',
		    "po_status" => 1,
		    "delivery_date" => date("Y-m-d")
		  ));

		  PurchaseOrder::create(array(
		  	"assigned_by"	=> 0,
		  	"assigned_to_user_id"	=> 0,
		  	"vendor_id"=>2,
		    "receiver_no"=>20229,
		    "purchase_order_no"=>10486,
		    "destination"=>'W',
		    "po_status" => 1,
		    "delivery_date" => date("Y-m-d")
		  ));

		  PurchaseOrder::create(array(
		  	"assigned_by"	=> 2,
		  	"assigned_to_user_id"	=> 3,
		  	"vendor_id"=>2,
		    "receiver_no"=>20230,
		    "purchase_order_no"=>10487,
		    "destination"=>'W',
		    "po_status" => 2,
		    "delivery_date" => date("Y-m-d")
		  ));

		  PurchaseOrder::create(array(
		  	"assigned_by"	=> 0,
		  	"assigned_to_user_id"	=> 0,
		  	"vendor_id"=>2,
		    "receiver_no"=>20231,
		    "purchase_order_no"=>10488,
		    "destination"=>'W',
		    "po_status" => 1,
		    "delivery_date" => date("Y-m-d")
		  ));

		  PurchaseOrder::create(array(
		  	"assigned_by"	=> 0,
		  	"assigned_to_user_id"	=> 0,
		  	"vendor_id"=>2,
		    "receiver_no"=>20232,
		    "purchase_order_no"=>10489,
		    "destination"=>'W',
		    "po_status" => 1,
		    "delivery_date" => date("Y-m-d")
		  ));
	}

}