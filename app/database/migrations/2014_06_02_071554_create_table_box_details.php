<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableBoxDetails extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('box_details', function($table){
			$table->increments('id');
			$table->integer('picklist_detail_id')->default(0);
			$table->string('box_code', 10);
			$table->integer('moved_qty')->default(0);
			// $table->string('pallet_code', 9);
			// $table->string('load_code', 9);
			$table->tinyInteger('sync_status')->default(0);
			$table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
			$table->timestamp('updated_at')->default('0000-00-00 00:00:00');
			$table->timestamp('jda_sync_date')->default('0000-00-00 00:00:00');
			$table->timestamp('deleted_at')->default('0000-00-00 00:00:00');
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
		Schema::drop('box_details');
	}

	//TODO :: remove this when box is stable
/*	Schema::create('box_manifest_detail', function($table)
		{
			$table->increments('id');
			$table->integer('store_order_details_id')->default(0);
			$table->string('box_id', 50)->default("0");
			$table->string('box_no', 50)->default("0");
			$table->integer('packed_qty')->default(0);
			$table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
			$table->timestamp('updated_at')->default('0000-00-00 00:00:00');

		});*/

}
