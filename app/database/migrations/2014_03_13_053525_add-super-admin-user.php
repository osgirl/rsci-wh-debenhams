<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSuperAdminUser extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		DB::table('users')->insert(array(
			'username' => 'superadmin',
			'password' => Hash::make('superadmin'),
			'firstname' => 'Dean',
			'lastname' => 'Casili',
			'role_id' => 1,
			'created_at' => date('Y-m-d H:i:s')
		));

		DB::table('users')->insert(array(
			'username' => 'admin',
			'password' => Hash::make('password'),
			'firstname' => 'Admin',
			'lastname' => 'Admin',
			'role_id' => 2,
			'brand_id' => 2,
			'created_at' => date('Y-m-d H:i:s')
		));

		DB::table('users')->insert(array(
			'username' => 'stock.piler',
			'password' => Hash::make('password'),
			'firstname' => 'Stock',
			'lastname' => 'Piler',
			'role_id' => 3,
			'brand_id' => 2,
			'created_at' => date('Y-m-d H:i:s'),
			'barcode' 	=> 'NGM0001'
		));

		DB::table('users')->insert(array(
			'username' => 'stock.pilar',
			'password' => Hash::make('password'),
			'firstname' => 'Stock',
			'lastname' => 'Pilar',
			'role_id' => 3,
			'brand_id' => 2,
			'created_at' => date('Y-m-d H:i:s'),
			'barcode' 	=> 'NGM0002'
		));

		DB::table('users')->insert(array(
			'username' => 'box.creator',
			'password' => Hash::make('password'),
			'firstname' => 'Box',
			'lastname' => 'Creator',
			'role_id' => 5,
			'brand_id' => 2,
			'created_at' => date('Y-m-d H:i:s')
		));

		DB::table('users')->insert(array(
			'username' => 'store.owner',
			'password' => Hash::make('password'),
			'firstname' => 'Store',
			'lastname' => 'Owner',
			'role_id' => 4,
			'brand_id' => 2,
			'store_code'	=> 20,
			'created_at' => date('Y-m-d H:i:s')
		));

		DB::table('users')->insert(array(
			'username' => 'store.owner2',
			'password' => Hash::make('password'),
			'firstname' => 'Store2',
			'lastname' => 'Owner2',
			'role_id' => 4,
			'brand_id' => 2,
			'store_code'	=> 26,
			'created_at' => date('Y-m-d H:i:s')
		));
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		DB::table('users')->where('username', '=', 'superadmin')->delete();
	}

}
