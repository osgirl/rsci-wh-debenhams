<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class InsertErrorFieldManualMove extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('manual_move', function(Blueprint $table)
        {
            $table->string('error_message');
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('manual_move', function(Blueprint $table)
        {
            $table->dropColumn('error_message');
        });
	}

}
