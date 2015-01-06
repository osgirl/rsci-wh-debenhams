<?php

class SlotDetailsSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run() {
	
		SlotDetails::truncate();
		  SlotDetails::create(array(
		  	"slot_id" => 541,
		    "sku" =>900026,
		    "quantity" => 2
		  ));
		  

	}

}