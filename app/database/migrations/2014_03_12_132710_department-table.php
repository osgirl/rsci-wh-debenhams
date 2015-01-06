<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DepartmentTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('department', function($table) 
		{
			$table->increments('id');
			$table->integer('dept_code');
			$table->integer('sub_dept');
			$table->integer('class');
			$table->integer('sub_class');
			$table->string('description', 30);
			$table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
			$table->timestamp('updated_at')->default('0000-00-00 00:00:00');
			$table->timestamp('deleted_at')->default('0000-00-00 00:00:00');
			$table->unique(array('dept_code', 'sub_dept', 'class', 'sub_class'));
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
		Schema::drop('department');
	}

}
