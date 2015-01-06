<?php

class SettingsSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run() {
	
		Settings::truncate();

		$data = array(
					array(
					  	'brand' => 'family-mart',
					    'brand_name' => 'Family Mart',
					    'product_identifier' => 'upc',
					    'product_action' => 'upc-detail-page'
					  ),
					array(
					  	"brand" => 'gap',
					    "brand_name" => 'Gap',
					    "product_identifier" => 'sku',
					    'product_action' => 'increment-quantity'
					  )
			);

		foreach ($data as $d) {
            Settings::create($d);
        }
		  
		  

	}

}