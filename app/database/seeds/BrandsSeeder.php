<?php

class BrandsSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run() {

		Brands::truncate();

		$data = array(
					array(
					  	'brand_code' => 'family_mart',
					    'brand_name' => 'Family Mart'
					  ),
					array(
					  	'brand_code' => 'gap',
					    'brand_name' => 'GAP'
					  )
			);

		foreach ($data as $d) {
            Brands::create($d);
        }


	}

}