<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStoreReturnTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('store_return', function($table)
		{
			$table->increments('id');
			$table->integer('assigned_by');
			$table->string('assigned_to_user_id', 30)->default(0);
			$table->string('so_no', 50);
			$table->string('store_code')->default(0);
			// $table->string('load_code', 9)->default(0);;
			$table->tinyInteger('so_status')->default(0);
			$table->integer('assigned_user_id')->default(0);
			$table->tinyInteger('sync_status')->default(0);
			$table->timestamp('delivered_date')->default('0000-00-00 00:00:00');
			$table->timestamp('jda_sync_date')->default('0000-00-00 00:00:00');
			$table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
			$table->timestamp('updated_at')->default('0000-00-00 00:00:00');
			$table->unique(array('so_no', 'store_code'));
			$table->index(array('so_no','so_status', 'delivered_date'));
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
		Schema::drop('store_return');
	}

}
