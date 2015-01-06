<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLetdownTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('letdown', function($table){
			$table->increments('id');
			$table->integer('move_doc_number')->default(0);
			$table->tinyInteger('lt_status')->default(0);
			$table->timestamp('date_completed')->default('0000-00-00 00:00:00');
			$table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
			$table->timestamp('updated_at')->default('0000-00-00 00:00:00');
			// $table->unique(array('move_doc_number', 'created_at'));
			$table->unique(array('move_doc_number'));
			$table->index('lt_status');
			$table->engine = 'InnoDB';
		});

		/*Schema::table('letdown', function($table)
		{
		   if (Schema::hasColumn('letdown', 'lt_status'))
		   {
			    $table->index('lt_status');
		   }
		});*/
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('letdown');
	}

}
