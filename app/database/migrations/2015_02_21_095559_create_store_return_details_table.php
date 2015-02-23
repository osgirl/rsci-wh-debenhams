<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStoreReturnDetailsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('store_return_detail', function($table)
		{
			$table->increments('id');
			$table->string('so_no', 50);
			$table->string('sku', 30);
			$table->integer('delivered_qty')->default(0);
			$table->integer('received_qty')->default(0);
			$table->tinyInteger('sync_status')->default(0);
			$table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
			$table->timestamp('updated_at')->default('0000-00-00 00:00:00');
			$table->timestamp('jda_sync_date')->default('0000-00-00 00:00:00');
			$table->unique(array('so_no', 'sku'));
			$table->index(array('so_no', 'sku'));
			$table->engine = 'InnoDB';
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('store_return_detail');
	}

}
