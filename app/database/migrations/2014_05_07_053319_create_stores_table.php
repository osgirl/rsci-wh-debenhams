<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStoresTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('stores', function($table){
			$table->increments('id');
			$table->string('store_name', 50);
			$table->string('store_code', 50)->unique();
			$table->string('address1', 150);
			$table->string('address2', 150);
			$table->string('address3', 150);
			$table->string('city', 50);
			$table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
			$table->timestamp('updated_at')->default('0000-00-00 00:00:00');
			$table->engine = 'InnoDB';
		});

		Schema::table('stores', function($table)
		{
		   if ((Schema::hasColumn('stores', 'store_name')) && (Schema::hasColumn('stores', 'store_code')))
		   {
			    $table->index(array('store_name', 'store_code'));
		   }
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('stores');
	}

}
