<?php

class StoresSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run() {
	
		Store::truncate();

		Store::create(array(
		  	"store_name"   => 'Family Mart Buendia',
		    "store_code" 	=> 'ST1',
		));


		Store::create(array(
		  	"store_name"   => 'Family Mart Glorrieta',
		    "store_code" 	=> 'ST2',
		));

		Store::create(array(
		  	"store_name"   => 'Family Mart Calamba',
		    "store_code" 	=> 'ST3',
		));
		
	}

}