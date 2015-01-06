<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ProductListsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('product_lists', function($table) 
		{
			$table->increments('id');
			$table->string('sku', 30); //revert this to bigInteger()
			$table->string('upc', 30); //revert this to bigInteger()
			$table->string('short_description', 10);
			$table->string('description', 30);
			$table->integer('vendor');
			$table->integer('dept_code');
			$table->integer('sub_dept');
			$table->integer('class');
			$table->integer('sub_class');
			$table->tinyInteger('set_code');
			$table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
			$table->timestamp('updated_at')->default('0000-00-00 00:00:00');
			$table->timestamp('deleted_at')->default('0000-00-00 00:00:00');
			$table->unique(array('upc', 'sku'));
			$table->engine = 'InnoDB';
		});

		Schema::table('product_lists', function($table)
		{
			if (Schema::hasColumn('product_lists', 'sku'))
			{
			    $table->index('sku');
			}

			if ((Schema::hasColumn('product_lists', 'dept_code')) && (Schema::hasColumn('product_lists', 'sub_dept')))
		    {
			    $table->index(array('dept_code', 'sub_dept'));
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
		Schema::drop('product_lists');
	}

}
