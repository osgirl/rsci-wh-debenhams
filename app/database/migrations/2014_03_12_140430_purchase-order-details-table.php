<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class PurchaseOrderDetailsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('purchase_order_details', function($table)
		{
			$table->increments('id');
			$table->integer('po_id');
			// $table->integer('sku');
			$table->string('sku', 30); //revert this to bigInteger()
			$table->integer('receiver_no');
			$table->integer('quantity_ordered');
			$table->float('unit_price')->default(0);
			$table->integer('quantity_delivered');
			// $table->softDeletes();
			$table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
			$table->timestamp('updated_at')->default('0000-00-00 00:00:00');
			$table->timestamp('deleted_at')->default('0000-00-00 00:00:00');
			$table->unique(array('sku', 'receiver_no'));
			$table->engine = 'InnoDB';
		});

		
		Schema::table('purchase_order_details', function($table)
		{
		    if ((Schema::hasColumn('purchase_order_details', 'po_id')) && (Schema::hasColumn('purchase_order_details', 'sku')))
			{
			    $table->index(array('po_id', 'sku'));
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
		Schema::drop('purchase_order_details');
	}

}
