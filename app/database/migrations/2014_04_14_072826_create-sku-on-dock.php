<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSkuOnDock extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('sku_on_dock', function($table)
		{
			$table->increments('id');
			$table->string('sku', 30); //revert this to bigInteger()
			$table->integer('total_qty_delivered');
			$table->integer('total_qty_remaining');
			$table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
			$table->timestamp('updated_at')->default('0000-00-00 00:00:00');
			$table->engine = 'InnoDB';
		});

		Schema::table('sku_on_dock', function($table)
		{
		    if (Schema::hasColumn('sku_on_dock', 'sku'))
			{
			    $table->index(array('sku'));
			}

			if (Schema::hasColumn('sku_on_dock', 'created_at') && Schema::hasColumn('sku_on_dock', 'total_qty_delivered') && Schema::hasColumn('sku_on_dock', 'total_qty_remaining'))
			{
			    DB::statement('ALTER TABLE `wms_sku_on_dock` ADD INDEX( `created_at`, `total_qty_delivered`, `total_qty_remaining`)');
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
		Schema::drop('sku_on_dock');
	}

}
