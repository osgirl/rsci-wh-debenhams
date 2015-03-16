<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPrintTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::table('load', function($table)
        {
            $table->tinyInteger('printMTS')->default(0);
            $table->tinyInteger('printPacking')->default(0);
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        Schema::table('load', function($table)
        {
            $table->dropColumn('printMTS');
            $table->dropColumn('printPacking');
        });
	}

}
