<?php

class DatabaseSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		Eloquent::unguard();

		#$this->call('ProductListsSeeder');
		#$this->call('PurchaseOrderSeeder');
		#$this->call('PurchaseOrderDetailSeeder');
		$this->call('SettingsSeeder');
		#$this->call('SlotSeeder');
		#$this->call('SlotDetailsSeeder');
		#$this->call('VendorSeeder');
		#$this->call('LetdownSeeder');
		#$this->call('StoreOrderSeeder');
		#$this->call('StoreOrderDetailSeeder');
		#$this->call('StoresSeeder');
		#$this->call('BoxManifestSeeder');
	}

}
