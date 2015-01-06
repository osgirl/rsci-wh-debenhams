sudo <?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStoreOrderDetailTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('store_order_detail', function($table)
		{
			$table->increments('id');
			$table->string('so_no', 50);
			$table->string('sku', 30); 
			$table->integer('ordered_qty')->default(0);
			$table->integer('packed_qty')->default(0);
			$table->integer('delivered_qty')->default(0);
			$table->tinyInteger('sync_status')->default(0);
			$table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
			$table->timestamp('updated_at')->default('0000-00-00 00:00:00');
			$table->timestamp('jda_sync_date')->default('0000-00-00 00:00:00');
			$table->unique(array('so_no', 'sku'));
			$table->engine = 'InnoDB';
		});

		Schema::table('store_order_detail', function($table)
		{
		   if ((Schema::hasColumn('store_order_detail', 'so_no')) && (Schema::hasColumn('store_order_detail', 'sku')))
			{
			    $table->index(array('so_no', 'sku'));
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
		Schema::drop('store_order_detail');
	}

	/*
	Old migration Backup

	Schema::create('store_order_detail', function($table)
		{
			$table->increments('id');
			$table->string('so_no', 50);
			$table->string('sku', 30); 
			$table->integer('ordered_qty')->default(0);
			$table->integer('allocated_qty')->default(0);
			$table->integer('picked_qty')->default(0);
			$table->integer('packed_qty')->default(0);
			$table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
			$table->timestamp('updated_at')->default('0000-00-00 00:00:00');
			$table->timestamp('deleted_at')->default('0000-00-00 00:00:00');
			$table->unique(array('so_no', 'sku'));
		});
	*/

}
