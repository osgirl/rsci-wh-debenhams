<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStoreOrderTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('store_order', function($table)
		{
			$table->increments('id');
			$table->string('so_no', 50);
			$table->string('store_code')->default(0);
			$table->string('load_code', 9)->default(0);;
			$table->tinyInteger('so_status')->default(1); //set this again to 1 after testing
			$table->integer('assigned_user_id')->default(0);
			$table->tinyInteger('sync_status')->default(0);
			$table->timestamp('order_date')->default('0000-00-00 00:00:00');
			$table->timestamp('delivery_date')->default('0000-00-00 00:00:00');
			$table->timestamp('jda_sync_date')->default('0000-00-00 00:00:00');
			$table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
			$table->timestamp('updated_at')->default('0000-00-00 00:00:00');
			$table->unique(array('so_no', 'store_code'));
			$table->engine = 'InnoDB';
		});


		Schema::table('store_order', function($table)
		{
		   if ((Schema::hasColumn('store_order', 'so_status')) && (Schema::hasColumn('store_order', 'order_date')))
			{
			    $table->index(array('so_status', 'order_date'));
			}

			if ((Schema::hasColumn('store_order', 'so_no')) && (Schema::hasColumn('store_order', 'so_status')))
			{
			    $table->index(array('so_no', 'so_status'));
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
		Schema::drop('store_order');
	}

	/*
	This is the old migration

	Schema::create('store_order', function($table)
		{
			$table->increments('id');
			$table->string('so_no', 50);
			$table->string('store_code')->default('0');
			$table->tinyInteger('so_status')->default(1);
			$table->integer('assigned_user_id')->default(0);
			$table->tinyInteger('lt_status_closed')->default(0);
			$table->timestamp('order_date')->default('0000-00-00 00:00:00');
			$table->timestamp('dispatch_date')->default('0000-00-00 00:00:00');
			$table->timestamp('datetime_done')->default('0000-00-00 00:00:00');
			$table->timestamp('latest_mobile_sync_date')->default('0000-00-00 00:00:00');
			$table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
			$table->timestamp('updated_at')->default('0000-00-00 00:00:00');
			$table->timestamp('deleted_at')->default('0000-00-00 00:00:00');
			$table->unique(array('so_no', 'store_code'));
		});
	*/

}
