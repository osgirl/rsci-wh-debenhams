<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLetdownDetailsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('letdown_details', function($table){
			$table->increments('id');
			$table->string('sku', 30);
			$table->integer('assigned_user_id')->default(0);
			$table->string('store_code', 50);
			$table->integer('move_doc_number')->default(0);
			$table->string('from_slot_code',10);
			$table->string('to_slot_code',10);
			$table->integer('quantity_to_letdown')->default(0);
			$table->integer('moved_qty')->default(0);
			$table->tinyInteger('move_to_picking_area')->default(0);
			$table->integer('lock_tag')->default(0);
			$table->integer('sequence_no')->default(0);
			$table->string('group_name',10);
			$table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
			$table->timestamp('updated_at')->default('0000-00-00 00:00:00');
			$table->timestamp('deleted_at')->default('0000-00-00 00:00:00');
			$table->unique(array('move_doc_number', 'sku', 'from_slot_code', 'store_code'), 'my_uniques');
			$table->index(array('move_doc_number', 'sku', 'move_to_picking_area', 'assigned_user_id', 'from_slot_code'), 'my_index');
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
		Schema::drop('letdown_details');
	}

}
