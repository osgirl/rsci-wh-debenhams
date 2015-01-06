<?php

class VendorSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run() {
	
		DB::table('vendors')->truncate();

		  Vendors::create(array(
		    "vendor_name"=>"DC ALL PRODUCT",
		    "vendor_code"=>20100
		  ));
		  Vendors::create(array(
		    "vendor_name"=>"PHILIPPINE FAMILYMART PRODUCT",
		    "vendor_code"=>20999
		  ));
	}

}