<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class PurchaseOrderListsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('purchase_order_lists', function($table)
		{
			$table->increments('id');
			// $table->integer('user_id');
			$table->integer('assigned_by');
			$table->string('assigned_to_user_id', 30)->default(0);
			$table->integer('vendor_id');
			$table->integer('receiver_no');
			$table->integer('purchase_order_no');
			$table->string('destination', 10);
			$table->string('carton_id', 30)->default("0");
			$table->integer('total_qty');
			$table->string('back_order', 30)->default("0");
			$table->string('shipment_reference_no', 20)->default("0");
			$table->string('container_id', 20)->default("0");
			$table->tinyInteger('po_status')->default(1);
			$table->string('invoice_no', 30)->default("0");
			$table->string('invoice_amount')->default(0);
			$table->string('slot_code', 10)->default(0);
			$table->timestamp('delivery_date');
			$table->timestamp('datetime_done');
			$table->timestamp('latest_mobile_sync_date');
			$table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
			$table->timestamp('updated_at')->default('0000-00-00 00:00:00');
			$table->timestamp('deleted_at')->default('0000-00-00 00:00:00');
			$table->unique(array('purchase_order_no','receiver_no'));
			$table->engine = 'InnoDB';
		});

		Schema::table('purchase_order_lists', function($table)
		{
		    if ((Schema::hasColumn('purchase_order_lists', 'assigned_to_user_id')) && (Schema::hasColumn('purchase_order_lists', 'po_status')))
			{
			    $table->index(array('assigned_to_user_id', 'po_status'));
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
		Schema::drop('purchase_order_lists');
	}

}
