<?php

class StoreOrderSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run() {
	
		DB::table('store_order')->truncate();

		  StoreOrder::create(array(
		    "so_no"			=> "1",
		    "so_status"		=> 1,
		    "store_code" 		=> "ST1",
		    "order_date"	=> "2014-04-23 00:00:00",
		  ));

		  StoreOrder::create(array(
		    "so_no"			=> "2",
		    "so_status"		=> 1,
		    "store_code" 		=> "ST2",
		    "order_date"	=> "2014-04-20 00:00:00",
		  ));

		  StoreOrder::create(array(
		    "so_no"			=> "3",
		    "so_status"		=> 1,
		    "store_code" 		=> "ST3",
		    "order_date"	=> "2014-04-28 00:00:00",
		  ));

		  StoreOrder::create(array(
		    "so_no"			=> "4",
		    "so_status"		=> 1,
		    "store_code" 		=> "ST3",
		    "order_date"	=> "2014-04-28 00:00:00",
		  ));

		  StoreOrder::create(array(
		    "so_no"			=> "5",
		    "so_status"		=> 1,
		    "store_code" 		=> "ST3",
		    "order_date"	=> "2014-04-28 00:00:00",
		  ));

		   StoreOrder::create(array(
		    "so_no"			=> "6",
		    "so_status"		=> 1,
		    "store_code" 		=> "ST1",
		    "order_date"	=> "2014-04-28 00:00:00",
		  ));

		   StoreOrder::create(array(
		    "so_no"			=> "7",
		    "so_status"		=> 1,
		    "store_code" 		=> "ST2",
		    "order_date"	=> "2014-04-28 00:00:00",
		  ));

	}

}