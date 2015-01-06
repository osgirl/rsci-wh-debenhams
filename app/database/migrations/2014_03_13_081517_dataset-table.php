<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DatasetTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('dataset', function($table)
		{
			$table->increments('id');
			$table->string('data_code', 20);
			$table->string('data_value', 20);
			$table->string('data_display', 100);
			$table->string('description', 100);
			$table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
			$table->timestamp('updated_at')->default('0000-00-00 00:00:00');
			$table->timestamp('deleted_at')->default('0000-00-00 00:00:00');
			$table->engine = 'InnoDB';
		});

		Schema::table('dataset', function($table)
		{
		    if ((Schema::hasColumn('dataset', 'data_code')) )
			{
			    $table->index('data_code');
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
		Schema::drop('dataset');
	}

}
