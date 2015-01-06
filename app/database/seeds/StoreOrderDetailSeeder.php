<?php

class StoreOrderDetailSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run() {
	
		DB::table('store_order_detail')->truncate();

		  StoreOrderDetail::create(array(
		    "so_no"			=> "1",
		    "sku"			=> "NGM0024",
		    "allocated_qty" 	=> "100",
		    "ordered_qty" 	=> "100"
		  ));

		  StoreOrderDetail::create(array(
		    "so_no"			=> "1",
		    "sku"			=> "NGM0023",
		    "allocated_qty" 	=> "100",
		    "ordered_qty" 	=> "100"
		  ));

		  StoreOrderDetail::create(array(
		    "so_no"			=> "1",
		    "sku"			=> "NGM0022",
		    "allocated_qty" 	=> "90",
		    "ordered_qty" 	=> "90"
		  ));

		  StoreOrderDetail::create(array(
		    "so_no"			=> "2",
		    "sku"			=> "NGM0022",
		    "allocated_qty" 	=> "100",
		    "ordered_qty" 	=> "100"
		  ));

		  StoreOrderDetail::create(array(
		    "so_no"			=> "2",
		    "sku"			=> "NGM0023",
		    "ordered_qty" 	=> "90",
		    "allocated_qty" 	=> "90"
		  ));

		  StoreOrderDetail::create(array(
		    "so_no"			=> "3",
		    "sku"			=> "NGM0022",
		    "ordered_qty" 	=> "20",
		    "allocated_qty" 	=> "20"
		  ));

		  StoreOrderDetail::create(array(
		    "so_no"			=> "3",
		    "sku"			=> "NGM0023",
		    "ordered_qty" 	=> "20",
		    "allocated_qty" 	=> "20"
		  ));


		  StoreOrderDetail::create(array(
		    "so_no"			=> "4",
		    "sku"			=> "NGM0022",
		    "ordered_qty" 	=> "20",
		    "allocated_qty" 	=> "20"
		  ));

		  StoreOrderDetail::create(array(
		    "so_no"			=> "4",
		    "sku"			=> "NGM0023",
		    "ordered_qty" 	=> "20",
		    "allocated_qty" 	=> "20"
		  ));


		  StoreOrderDetail::create(array(
		    "so_no"			=> "5",
		    "sku"			=> "NGM0023",
		    "ordered_qty" 	=> "100",
		    "allocated_qty" 	=> "100"
		  ));

		  StoreOrderDetail::create(array(
		    "so_no"			=> "6",
		    "sku"			=> "NGM0022",
		    "ordered_qty" 	=> "200",
		    "allocated_qty" 	=> "200"
		  ));

		  StoreOrderDetail::create(array(
		    "so_no"			=> "7",
		    "sku"			=> "NGM0022",
		    "ordered_qty" 	=> "100",
		    "allocated_qty" 	=> "100"
		  ));


	}

}