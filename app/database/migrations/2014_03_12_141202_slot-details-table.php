<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SlotDetailsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('slot_details', function($table)
		{
			$table->increments('id');
			$table->integer('put_away_id');
			$table->string('slot_id', 10);
			$table->integer('assigned_user_id');
			$table->string('sku', 30); //revert this to bigInteger()
			$table->integer('quantity');
			$table->timestamp('expiry_date');
			$table->tinyInteger('sync_status')->default(0);
			$table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
			$table->timestamp('updated_at')->default('0000-00-00 00:00:00');
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
		Schema::drop('slot_details');
	}

}
