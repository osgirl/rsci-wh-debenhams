<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUnlistedTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('unlisted', function($table)
		{
			$table->increments('id');
			$table->string('sku', 30); //revert this to bigInteger()
			$table->integer('reference_no');
			$table->integer('quantity_received');
			$table->longText('description');
			$table->string('style_no', 30);
			$table->integer('brand')->default(0);
			$table->integer('division')->default(0);
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
		Schema::drop('unlisted');
	}

}
