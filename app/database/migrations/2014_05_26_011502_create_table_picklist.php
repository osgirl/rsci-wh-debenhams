<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTablePicklist extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('picklist', function($table){
			$table->increments('id');
			$table->string('type', 20)->default('upc');
			$table->integer('move_doc_number')->default(0);
			$table->tinyInteger('pl_status')->default(0);
			$table->timestamp('date_completed')->default('0000-00-00 00:00:00');
			$table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
			$table->timestamp('updated_at')->default('0000-00-00 00:00:00');
			$table->unique(array('move_doc_number'));
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
		Schema::drop('picklist');
	}

}
