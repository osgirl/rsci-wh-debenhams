<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AuditTrailTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('audit_trail', function($table) {
			// auto increment id (primary key)
			$table->bigIncrements('audit_id');
			
			$table->string('module');
			$table->string('reference');
			$table->string('action');
			$table->text('data_before');
			$table->text('data_after');
			$table->integer('user_id')->default(0);
						
			// created_at, updated_at DATETIME
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
		Schema::drop('audit_trail');
	}

}
