<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInterTransferTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('inter_transfer', function($table)
		{
			$table->increments('id');
			$table->string('box_code', 10);
			$table->string('mts_number', 30)->default(0);
			$table->tinyInteger('no_of_boxes')->default(0);
			$table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
			$table->timestamp('updated_at')->default('0000-00-00 00:00:00');
			$table->unique(array('box_code', 'mts_number'));
			$table->index(array('box_code', 'mts_number'));
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
		Schema::drop('inter_transfer');
	}

}
