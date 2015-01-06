<?php

class BoxManifestSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run() {
	
		DB::table('box_manifest')->truncate();
		DB::table('box_manifest_detail')->truncate();


	}

}