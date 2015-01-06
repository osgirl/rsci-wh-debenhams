<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLoadTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('load', function($table)
		{
			$table->increments('id');
			$table->string('load_code', 9);
			$table->tinyInteger('is_shipped')->default(0);
			$table->tinyInteger('sync_status')->default(0);
			$table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
			$table->timestamp('updated_at')->default('0000-00-00 00:00:00');
			$table->timestamp('jda_sync_date')->default('0000-00-00 00:00:00');
			$table->unique(array('load_code'));
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
		Schema::drop('load');
	}

}
